<?php
namespace App\Controllers;
use App\Models\ProductModel;

class Inventori extends BaseController
{
    private const MAX_REASONABLE_PRICE = 50000000;
    // ==========================================
    // 1. HALAMAN UTAMA INVENTORI
    // ==========================================
    public function index()
    {
        $session = session();
        if ($session->get('role') == 'Kasir' || !$session->get('isLoggedIn')) return redirect()->to('dashboard');

        $db = \Config\Database::connect();
        
        // Query Cerdas V2: Menghitung stok HP (dari tabel IMEI) dan stok Aksesoris (dari tabel QTY)
        $query = $db->query("
            SELECT p.*, 
            (CASE 
                WHEN p.type = 'Handphone' THEN (SELECT COUNT(*) FROM inventory_imei WHERE product_id = p.product_id AND status = 'Tersedia')
                ELSE IFNULL((SELECT SUM(qty) FROM inventory_aksesoris WHERE product_id = p.product_id), 0)
            END) as stok_tersedia
            FROM products p
            ORDER BY p.product_id DESC
        ");

        $data = [
            'produk'   => $query->getResultArray(),
            'username' => $session->get('username'),
            'toko'     => $db->table('store_profile')->get()->getRowArray(),
        ];

        return view('inventori/index', $data);
    }

    // ==========================================
    // 2. SIMPAN PRODUK BARU
    // ==========================================
    public function simpan()
    {
        $db = \Config\Database::connect();
        $type = $this->request->getPost('type');
        $barcode = $this->request->getPost('barcode');
        $price = $this->request->getPost('base_selling_price');

        if (! in_array($type, ['Handphone', 'Aksesoris'], true)
            || trim((string) $this->request->getPost('brand')) === ''
            || trim((string) $this->request->getPost('model_name')) === ''
            || ! is_numeric($price) || (float) $price < 0 || (float) $price > self::MAX_REASONABLE_PRICE) {
            return redirect()->to('inventori')->with('error', 'Harga jual tidak valid. Maksimal Rp 50.000.000.');
        }
        
        if ($type == 'Handphone') $barcode = null; // HP tidak pakai barcode umum

        // Validasi Barcode Kembar (Khusus Aksesoris)
        if ($barcode) {
            $cek = $db->table('products')->where('barcode', $barcode)->get()->getRow();
            if ($cek) return redirect()->to('inventori')->with('error', 'Gagal! Barcode tersebut sudah dipakai produk lain.');
        }

        $db->table('products')->insert([
            'type'               => $type,
            'barcode'            => empty($barcode) ? null : $barcode,
            'brand'              => $this->request->getPost('brand'),
            'model_name'         => $this->request->getPost('model_name'),
            'base_selling_price' => (float) $price
        ]);
        $this->auditLog('product.created', 'product', (string) $db->insertID(), ['type' => $type]);
        
        return redirect()->to('inventori')->with('pesan', 'Master Produk berhasil ditambahkan!');
    }

    // ==========================================
    // 3. EDIT PRODUK
    // ==========================================
    public function update()
    {
        $db = \Config\Database::connect();
        $id = $this->request->getPost('product_id');
        $price = $this->request->getPost('base_selling_price');

        if (! ctype_digit((string) $id)
            || trim((string) $this->request->getPost('brand')) === ''
            || trim((string) $this->request->getPost('model_name')) === ''
            || ! is_numeric($price) || (float) $price < 0 || (float) $price > self::MAX_REASONABLE_PRICE) {
            return redirect()->to('inventori')->with('error', 'Harga jual tidak valid. Maksimal Rp 50.000.000.');
        }
        
        $db->table('products')->where('product_id', $id)->update([
            'brand'              => $this->request->getPost('brand'),
            'model_name'         => $this->request->getPost('model_name'),
            'base_selling_price' => (float) $price
        ]);
        $this->auditLog('product.updated', 'product', (string) $id);
        return redirect()->to('inventori')->with('pesan', 'Data Master Produk berhasil diperbarui!');
    }

    // ==========================================
    // 4. HALAMAN KELOLA STOK (Bisa HP / Aksesoris)
    // ==========================================
    public function stok($product_id)
    {
        $session = session();
        if ($session->get('role') == 'Kasir' || !$session->get('isLoggedIn')) return redirect()->to('dashboard');

        $db = \Config\Database::connect();
        
        // Cari informasi produk
        $produk = $db->table('products')->where('product_id', $product_id)->get()->getRowArray();
        if (!$produk) return redirect()->to('inventori')->with('error', 'Produk tidak ditemukan!');

        // Ambil riwayat stok sesuai tipe barang
        if ($produk['type'] == 'Handphone') {
            $stok_detail = $db->table('inventory_imei')->where('product_id', $product_id)->orderBy('date_received', 'DESC')->get()->getResultArray();
        } else {
            $stok_detail = $db->table('inventory_aksesoris')->where('product_id', $product_id)->orderBy('date_received', 'DESC')->get()->getResultArray();
        }

        $data = [
            'username'    => $session->get('username'),
            'produk'      => $produk,
            'stok_detail' => $stok_detail,
            'toko'        => $db->table('store_profile')->get()->getRowArray(),
        ];

        return view('inventori/stok', $data);
    }

    // ==========================================
    // 5. SIMPAN TAMBAHAN STOK
    // ==========================================
    public function simpan_stok()
    {
        $db = \Config\Database::connect();
        $product_id = $this->request->getPost('product_id');
        $type       = $this->request->getPost('type'); // Menangkap tipe dari form
        $modal      = $this->request->getPost('purchase_price');

        if (! ctype_digit((string) $product_id) || ! is_numeric($modal) || (float) $modal < 0 || (float) $modal > self::MAX_REASONABLE_PRICE) {
            return redirect()->to('inventori')->with('error', 'Harga modal tidak valid. Maksimal Rp 50.000.000.');
        }

        $produk = $db->table('products')->where('product_id', (int) $product_id)->get()->getRowArray();
        if (! $produk || $produk['type'] !== $type) {
            return redirect()->to('inventori')->with('error', 'Produk tidak ditemukan atau tipenya tidak sesuai.');
        }

        if ($type == 'Handphone') {
            // LOGIKA STOK HANDPHONE (SIMPAN IMEI)
            $imei = $this->request->getPost('imei');

            if (! is_string($imei) || trim($imei) === '' || strlen($imei) > 32) {
                return redirect()->to('inventori/stok/' . $product_id)->with('error', 'IMEI tidak valid.');
            }
            
            // Cegah duplikat IMEI
            $cek = $db->table('inventory_imei')->where('imei', $imei)->get()->getRowArray();
            if ($cek) return redirect()->to('inventori/stok/'.$product_id)->with('error', 'Gagal! IMEI '.$imei.' sudah pernah diinput.');

            $db->table('inventory_imei')->insert([
                'imei'           => $imei,
                'product_id'     => $product_id,
                'purchase_price' => (float) $modal,
                'status'         => 'Tersedia'
            ]);
            $this->auditLog('stock.received', 'product', (string) $product_id, ['type' => 'Handphone', 'imei' => $imei]);
            $pesan = 'Stok IMEI berhasil ditambahkan!';

        } else {
            // LOGIKA STOK AKSESORIS (SIMPAN QTY)
            $qty = $this->request->getPost('qty');

            if (! ctype_digit((string) $qty) || (int) $qty < 1) {
                return redirect()->to('inventori/stok/' . $product_id)->with('error', 'Jumlah stok harus berupa bilangan positif.');
            }
            
            $db->table('inventory_aksesoris')->insert([
                'product_id'     => $product_id,
                'qty'            => (int) $qty,
                'purchase_price' => (float) $modal
            ]);
            $this->auditLog('stock.received', 'product', (string) $product_id, ['type' => 'Aksesoris', 'qty' => (int) $qty]);
            $pesan = 'Stok Aksesoris sebanyak '.$qty.' unit berhasil ditambahkan!';
        }

        return redirect()->to('inventori/stok/' . $product_id)->with('pesan', $pesan);
    }

    public function hapus_stok()
    {
        $session = session();
        if (! in_array($session->get('role'), ['Manager', 'Owner'], true)) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $productId = $this->request->getPost('product_id');
        $imei = trim((string) $this->request->getPost('imei'));

        if (! ctype_digit((string) $productId) || $imei === '') {
            return redirect()->to('inventori')->with('error', 'Data stok tidak valid.');
        }

        $db = \Config\Database::connect();
        $stok = $db->table('inventory_imei')
            ->where('product_id', (int) $productId)
            ->where('imei', $imei)
            ->get()->getRowArray();

        if (! $stok) {
            return redirect()->to('inventori/stok/' . $productId)->with('error', 'Stok IMEI tidak ditemukan.');
        }

        if ($stok['status'] !== 'Tersedia') {
            return redirect()->to('inventori/stok/' . $productId)->with('error', 'IMEI yang sudah terjual tidak boleh dihapus.');
        }

        $db->transBegin();
        $deleted = $db->table('inventory_imei')
            ->where('product_id', (int) $productId)
            ->where('imei', $imei)
            ->where('status', 'Tersedia')
            ->delete();

        if (! $deleted || $db->affectedRows() !== 1) {
            $db->transRollback();
            return redirect()->to('inventori/stok/' . $productId)->with('error', 'Stok gagal dihapus atau sudah berubah.');
        }

        $db->table('audit_logs')->insert([
            'user_id'        => $session->get('user_id'),
            'action'         => 'stock.deleted',
            'reference_type' => 'product',
            'reference_id'   => (string) $productId,
            'details'        => json_encode([
                'type' => 'Handphone',
                'imei' => $imei,
                'purchase_price' => $stok['purchase_price'],
            ]),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        if (! $db->transStatus()) {
            $db->transRollback();
            return redirect()->to('inventori/stok/' . $productId)->with('error', 'Audit penghapusan gagal disimpan.');
        }

        $db->transCommit();
        return redirect()->to('inventori/stok/' . $productId)->with('pesan', 'Stok IMEI berhasil dihapus.');
    }
}