<?php

namespace App\Controllers;

class StockOpname extends BaseController
{
    public function index()
    {
        $session = session();
        if (! in_array($session->get('role'), ['Manager', 'Owner'], true)) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $db = \Config\Database::connect();
        $products = $db->query("SELECT p.product_id, p.brand, p.model_name, p.type,
            CASE WHEN p.type = 'Handphone' THEN
                (SELECT COUNT(*) FROM inventory_imei i WHERE i.product_id = p.product_id AND i.status = 'Tersedia')
            ELSE IFNULL((SELECT SUM(a.qty) FROM inventory_aksesoris a WHERE a.product_id = p.product_id), 0) END AS system_qty
            FROM products p ORDER BY p.type, p.brand, p.model_name")->getResultArray();

        return view('opname/index', [
            'username' => $session->get('username'),
            'products' => $products,
            'toko'     => $db->table('store_profile')->get()->getRowArray(),
        ]);
    }

    public function simpan()
    {
        $session = session();
        if (! in_array($session->get('role'), ['Manager', 'Owner'], true)) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $physical = $this->request->getPost('physical_qty') ?? [];
        $notes = trim((string) $this->request->getPost('notes'));
        if (! is_array($physical) || $physical === []) {
            return redirect()->to('opname')->with('error', 'Isi stok fisik minimal satu produk.');
        }

        $db = \Config\Database::connect();
        $productIds = array_keys($physical);
        $products = $db->table('products')->whereIn('product_id', $productIds)->get()->getResultArray();
        $productMap = [];
        foreach ($products as $product) {
            $productMap[(string) $product['product_id']] = $product;
        }

        $db->transBegin();
        $items = [];
        foreach ($physical as $productId => $physicalQty) {
            if (! isset($productMap[(string) $productId]) || ! ctype_digit((string) $physicalQty) || (int) $physicalQty < 0) {
                $db->transRollback();
                return redirect()->to('opname')->with('error', 'Jumlah stok fisik tidak valid.');
            }

            $product = $productMap[(string) $productId];
            $systemQty = $this->systemQuantity($db, $product);
            $physicalQty = (int) $physicalQty;
            $difference = $physicalQty - $systemQty;

            if ($difference !== 0 && $product['type'] === 'Aksesoris') {
                if (! $this->adjustAccessoryStock($db, (int) $productId, $difference)) {
                    $db->transRollback();
                    return redirect()->to('opname')->with('error', 'Stok aksesori tidak dapat disesuaikan untuk ' . $product['brand'] . ' ' . $product['model_name'] . '.');
                }
            }

            $items[] = [
                'product_id'   => (int) $productId,
                'system_qty'   => $systemQty,
                'physical_qty' => $physicalQty,
                'difference'   => $difference,
                'notes'        => $difference !== 0 ? ($notes ?: 'Selisih hasil stock opname') : null,
            ];
        }

        $db->table('stock_opnames')->insert([
            'user_id'     => $session->get('user_id'),
            'opname_date' => date('Y-m-d'),
            'status'      => 'completed',
            'notes'       => $notes ?: null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
        $opnameId = $db->insertID();
        foreach ($items as $item) {
            $item['opname_id'] = $opnameId;
            $db->table('stock_opname_items')->insert($item);
        }

        $db->table('audit_logs')->insert([
            'user_id'        => $session->get('user_id'),
            'action'         => 'stock.opname_completed',
            'reference_type' => 'stock_opname',
            'reference_id'   => (string) $opnameId,
            'details'        => json_encode(['items' => count($items), 'notes' => $notes]),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        if (! $db->transStatus()) {
            $db->transRollback();
            return redirect()->to('opname')->with('error', 'Stock opname gagal disimpan.');
        }
        $db->transCommit();
        return redirect()->to('opname')->with('pesan', 'Stock opname berhasil disimpan dan selisih aksesori disesuaikan.');
    }

    private function systemQuantity($db, array $product): int
    {
        if ($product['type'] === 'Handphone') {
            return (int) $db->table('inventory_imei')->where('product_id', $product['product_id'])->where('status', 'Tersedia')->countAllResults();
        }

        return (int) ($db->table('inventory_aksesoris')->selectSum('qty')->where('product_id', $product['product_id'])->get()->getRow('qty') ?? 0);
    }

    private function adjustAccessoryStock($db, int $productId, int $difference): bool
    {
        if ($difference > 0) {
            return (bool) $db->table('inventory_aksesoris')->insert([
                'product_id'     => $productId,
                'qty'            => $difference,
                'purchase_price' => 0,
            ]);
        }

        $remaining = abs($difference);
        $rows = $db->table('inventory_aksesoris')->where('product_id', $productId)->where('qty >', 0)->orderBy('date_received', 'ASC')->orderBy('id', 'ASC')->get()->getResultArray();
        foreach ($rows as $row) {
            if ($remaining <= 0) break;
            $taken = min($remaining, (int) $row['qty']);
            if (! $db->table('inventory_aksesoris')->where('id', $row['id'])->where('qty >=', $taken)->set('qty', 'qty - ' . $taken, false)->update()) {
                return false;
            }
            $remaining -= $taken;
        }

        return $remaining === 0;
    }
}
