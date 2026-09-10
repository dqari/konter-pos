<?php
namespace App\Controllers;
use App\Models\InventoryModel;
use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class Kasir extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('auth');

        $keranjang = $session->get('keranjang') ?? [];

        $data = [
            'username'  => $session->get('username'),
            'keranjang' => $keranjang,
            'toko'      => \Config\Database::connect()->table('store_profile')->get()->getRowArray(),
        ];

        return view('kasir/index', $data);
    }

    public function pelanggan()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('auth');
        }

        $db = \Config\Database::connect();
        $keranjang = $session->get('keranjang') ?? [];

        return view('kasir/pelanggan', [
            'keranjang' => $keranjang,
            'toko'      => $db->table('store_profile')->get()->getRowArray(),
        ]);
    }

    public function scan()
    {
        $session = session();
        $kode_scan = $this->request->getPost('imei'); // Menangkap hasil ketikan/scan barcode

        $db = \Config\Database::connect();
        $keranjang = $session->get('keranjang') ?? [];

        // ==========================================
        // SKENARIO 1: CEK APAKAH INI IMEI HANDPHONE?
        // ==========================================
        $builderHP = $db->table('inventory_imei');
        $builderHP->join('products', 'products.product_id = inventory_imei.product_id');
        $builderHP->where('inventory_imei.imei', $kode_scan);
        $builderHP->where('inventory_imei.status', 'Tersedia');
        $dataHP = $builderHP->get()->getRowArray();

        if ($dataHP) {
            // Jika ada di keranjang, tolak (karena HP tidak bisa dibeli 2x dengan IMEI sama)
            if (isset($keranjang[$kode_scan])) {
                return redirect()->to('kasir')->with('error', 'IMEI ini sudah ada di keranjang!');
            }

            // Masukkan HP ke keranjang
            $keranjang[$kode_scan] = [
                'tipe_barang' => 'Handphone',
                'product_id'  => $dataHP['product_id'],
                'nama'        => $dataHP['brand'] . ' ' . $dataHP['model_name'],
                'kode_unik'   => $dataHP['imei'], // IMEI
                'harga_awal'  => $dataHP['base_selling_price'],
                'harga_akhir' => $dataHP['base_selling_price'],
                'diskon'      => 0,
                'qty'         => 1 // HP selalu 1
            ];
            $session->set('keranjang', $keranjang);
            return redirect()->to('kasir');
        }

        // ==========================================
        // SKENARIO 2: CEK APAKAH INI BARCODE AKSESORIS?
        // ==========================================
        $builderAks = $db->table('inventory_aksesoris');
        $builderAks->join('products', 'products.product_id = inventory_aksesoris.product_id');
        $builderAks->where('products.barcode', $kode_scan);
        $builderAks->where('products.type', 'Aksesoris');
        $dataAks = $builderAks->get()->getRowArray();

        if ($dataAks) {
            // Cek ketersediaan stok
            $qty_di_keranjang = isset($keranjang[$kode_scan]) ? $keranjang[$kode_scan]['qty'] : 0;
            
            if ($dataAks['qty'] <= $qty_di_keranjang) {
                return redirect()->to('kasir')->with('error', 'Stok Aksesoris ini tidak mencukupi/habis!');
            }

            // Jika sudah ada di keranjang, TAMBAH QTY-nya (Tidak bikin baris baru)
            if (isset($keranjang[$kode_scan])) {
                $keranjang[$kode_scan]['qty'] += 1;
                $keranjang[$kode_scan]['harga_akhir'] = ($keranjang[$kode_scan]['harga_awal'] - $keranjang[$kode_scan]['diskon']) * $keranjang[$kode_scan]['qty'];
            } else {
                // Jika belum ada, masukkan Aksesoris ke keranjang
                $keranjang[$kode_scan] = [
                    'tipe_barang' => 'Aksesoris',
                    'product_id'  => $dataAks['product_id'],
                    'nama'        => $dataAks['brand'] . ' ' . $dataAks['model_name'],
                    'kode_unik'   => $dataAks['barcode'], // Barcode Umum
                    'harga_awal'  => $dataAks['base_selling_price'],
                    'harga_akhir' => $dataAks['base_selling_price'],
                    'diskon'      => 0,
                    'qty'         => 1 // Mulai dari 1
                ];
            }
            $session->set('keranjang', $keranjang);
            return redirect()->to('kasir');
        }

        // JIKA TIDAK DITEMUKAN DI KEDUANYA
        return redirect()->to('kasir')->with('error', 'Barcode/IMEI tidak ditemukan atau stok habis!');
    }

    // FUNGSI BARU UNTUK MENERAPKAN DISKON
    public function update_diskon()
    {
        $session = session();
        $imei = $this->request->getPost('imei');
        $diskon = $this->request->getPost('diskon');

        $keranjang = $session->get('keranjang');

        if (isset($keranjang[$imei]) && is_numeric($diskon) && $diskon >= 0) {
            $diskon = min((float) $diskon, (float) $keranjang[$imei]['harga_awal']);
            $keranjang[$imei]['diskon'] = $diskon;
            $keranjang[$imei]['harga_akhir'] = $keranjang[$imei]['harga_awal'] - $diskon;
            $session->set('keranjang', $keranjang);
        }

        return redirect()->to('kasir');
    }

    public function hapus_keranjang($imei)
    {
        $session = session();
        $keranjang = $session->get('keranjang');
        unset($keranjang[urldecode($imei)]);
        $session->set('keranjang', $keranjang);
        return redirect()->to('kasir');
    }

    
    // ==========================================
    // FUNGSI CHECKOUT V2 (Mendukung QTY & FIFO)
    // ==========================================
    public function checkout()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('auth');

        $keranjang = $session->get('keranjang');
        if (empty($keranjang)) return redirect()->to('kasir')->with('error', 'Keranjang masih kosong!');

        $paymentMethod = $this->request->getPost('payment_method');
        if (! in_array($paymentMethod, ['Cash', 'Transfer', 'Debit', 'Qris'], true)) {
            return redirect()->to('kasir')->with('error', 'Metode pembayaran tidak valid.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        $user = $db->table('users')->where('username', $session->get('username'))->get()->getRowArray();
        if (! $user) {
            $db->transRollback();
            return redirect()->to('auth')->with('error', 'Sesi user tidak valid.');
        }

        $total_amount = 0;
        $detailTransaksi = [];

        foreach ($keranjang as $item) {
            $product = $db->table('products')
                ->where('product_id', (int) $item['product_id'])
                ->get()->getRowArray();
            $quantity = (int) ($item['qty'] ?? 0);
            $discount = max(0, (float) ($item['diskon'] ?? 0));

            if (! $product || $quantity < 1 || $quantity > 100000) {
                $db->transRollback();
                return redirect()->to('kasir')->with('error', 'Data barang di keranjang tidak valid.');
            }

            $unitPrice = max(0, (float) $product['base_selling_price'] - $discount);
            $lineTotal = $unitPrice * $quantity;
            $purchaseCost = 0;

            if ($product['type'] === 'Handphone') {
                $imei = (string) ($item['kode_unik'] ?? '');
                $stock = $db->table('inventory_imei')
                    ->where('imei', $imei)
                    ->where('product_id', (int) $product['product_id'])
                    ->where('status', 'Tersedia')
                    ->get()->getRowArray();

                if ($quantity !== 1 || ! $stock) {
                    $db->transRollback();
                    return redirect()->to('kasir')->with('error', 'IMEI sudah terjual atau tidak tersedia.');
                }

                $purchaseCost = (float) $stock['purchase_price'];
            } else {
                $stockRows = $db->table('inventory_aksesoris')
                    ->where('product_id', (int) $product['product_id'])
                    ->where('qty >', 0)
                    ->orderBy('date_received', 'ASC')
                    ->orderBy('id', 'ASC')
                    ->get()->getResultArray();
                $available = array_sum(array_column($stockRows, 'qty'));

                if ($available < $quantity) {
                    $db->transRollback();
                    return redirect()->to('kasir')->with('error', 'Stok aksesori tidak mencukupi.');
                }

                $remaining = $quantity;
                foreach ($stockRows as $stock) {
                    if ($remaining <= 0) break;

                    $taken = min($remaining, (int) $stock['qty']);
                    $purchaseCost += $taken * (float) $stock['purchase_price'];
                    $remaining -= $taken;
                }
            }

            $total_amount += $lineTotal;
            $detailTransaksi[] = [
                'item'          => $item,
                'product'       => $product,
                'quantity'      => $quantity,
                'line_total'    => $lineTotal,
                'purchase_cost' => $purchaseCost,
            ];
        }

        $invoice_number = 'INV-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $db->table('transactions')->insert([
            'invoice_number'  => $invoice_number,
            'user_id'         => $user['user_id'],
            'customer_name'   => trim((string) $this->request->getPost('customer_name')) ?: null,
            'customer_phone'  => trim((string) $this->request->getPost('customer_phone')) ?: null,
            'total_amount'    => $total_amount,
            'payment_method'  => $paymentMethod,
            'status'          => 'completed',
        ]);

        $transaction_id = $db->insertID();

        foreach ($detailTransaksi as $detail) {
            $item = $detail['item'];
            $product = $detail['product'];

            $db->table('transaction_details')->insert([
                'transaction_id' => $transaction_id,
                'product_id'     => $product['product_id'],
                'imei'           => ($product['type'] === 'Handphone') ? $item['kode_unik'] : null,
                'qty'            => $detail['quantity'],
                'price_sold'     => $detail['line_total'],
                'purchase_cost'  => $detail['purchase_cost'],
            ]);

            if ($product['type'] === 'Handphone') {
                $updated = $db->table('inventory_imei')
                    ->where('imei', $item['kode_unik'])
                    ->where('status', 'Tersedia')
                    ->update(['status' => 'Terjual']);
                if ($updated !== true || $db->affectedRows() !== 1) {
                    $db->transRollback();
                    return redirect()->to('kasir')->with('error', 'IMEI berubah saat transaksi. Silakan ulangi.');
                }
            } else {
                $stok_tersedia = $db->table('inventory_aksesoris')
                                    ->where('product_id', $product['product_id'])
                                    ->where('qty >', 0)
                                    ->orderBy('date_received', 'ASC')
                                    ->orderBy('id', 'ASC')
                                    ->get()->getResultArray();
                $sisa_dibeli = $detail['quantity'];
                foreach ($stok_tersedia as $stok) {
                    if ($sisa_dibeli <= 0) break; 

                    $taken = min($sisa_dibeli, (int) $stok['qty']);
                    $updated = $db->table('inventory_aksesoris')
                        ->where('id', $stok['id'])
                        ->where('qty >=', $taken)
                        ->set('qty', 'qty - ' . $taken, false)
                        ->update();
                    if (! $updated || $db->affectedRows() !== 1) {
                        $db->transRollback();
                        return redirect()->to('kasir')->with('error', 'Stok berubah saat transaksi. Silakan ulangi.');
                    }
                    $sisa_dibeli -= $taken;
                }

                if ($sisa_dibeli > 0) {
                    $db->transRollback();
                    return redirect()->to('kasir')->with('error', 'Stok tidak mencukupi.');
                }
            }
        }

        $db->table('audit_logs')->insert([
            'user_id'        => $user['user_id'],
            'action'         => 'sale.completed',
            'reference_type' => 'transaction',
            'reference_id'   => (string) $transaction_id,
            'details'        => json_encode(['invoice_number' => $invoice_number, 'total_amount' => $total_amount]),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        if (! $db->transStatus()) {
            $db->transRollback();
            return redirect()->to('kasir')->with('error', 'Database gagal memproses transaksi!');
        }
        $db->transCommit();

        $session->remove('keranjang');
        session()->setFlashdata('cetak_nota', $invoice_number);
        return redirect()->to('kasir')->with('sukses', 'Transaksi berhasil! Menyiapkan nota...');
    }

    // ==========================================
    // FUNGSI CETAK NOTA V2
    // ==========================================
    public function cetak($invoice, $type = 'asli') 
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('auth');

        $db = \Config\Database::connect();
        $toko = $db->table('store_profile')->get()->getRowArray();
        $transaksi = $db->table('transactions')->where('invoice_number', $invoice)->get()->getRowArray();
        
        if(!$transaksi) return redirect()->to('kasir')->with('error', 'Nota tidak ditemukan!');

        // QUERY V2: Ambil data dari tabel detail dan langsung gabung ke tabel master produk
        $detail = $db->table('transaction_details')
                     ->select('products.brand, products.model_name, products.type, transaction_details.imei, transaction_details.qty, transaction_details.price_sold')
                     ->join('products', 'products.product_id = transaction_details.product_id')
                     ->where('transaction_details.transaction_id', $transaksi['transaction_id'])
                     ->get()->getResultArray();

        $data = [
            'toko'       => $toko,
            'transaksi'  => $transaksi,
            'detail'     => $detail,
            'kasir'      => $session->get('username'),
            'is_reprint' => ($type == 'copy') ? true : false
        ];

        return view('kasir/cetak', $data);
    }
 
 
}