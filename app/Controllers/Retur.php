<?php

namespace App\Controllers;

class Retur extends BaseController
{
    public function index()
    {
        $session = session();
        if (! in_array($session->get('role'), ['Manager', 'Owner'], true)) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $db = \Config\Database::connect();
        $transactions = $db->table('transactions')
            ->select('transactions.transaction_id, transactions.invoice_number, transactions.transaction_date, transactions.total_amount, transactions.status, users.username')
            ->join('users', 'users.user_id = transactions.user_id', 'left')
            ->whereIn('transactions.status', ['completed', 'partially_returned'])
            ->orderBy('transactions.transaction_date', 'DESC')
            ->get(50)->getResultArray();

        return view('retur/index', [
            'username'    => $session->get('username'),
            'transactions' => $transactions,
            'toko'        => $db->table('store_profile')->get()->getRowArray(),
        ]);
    }

    public function proses()
    {
        $session = session();
        if (! in_array($session->get('role'), ['Manager', 'Owner'], true)) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $transactionId = $this->request->getPost('transaction_id');
        $reason = trim((string) $this->request->getPost('reason'));
        $finalStatus = $this->request->getPost('final_status');
        if (! ctype_digit((string) $transactionId) || $reason === '' || strlen($reason) > 255
            || ! in_array($finalStatus, ['returned', 'cancelled'], true)) {
            return redirect()->to('retur')->with('error', 'Transaksi dan alasan retur wajib diisi.');
        }

        $db = \Config\Database::connect();
        $transaction = $db->table('transactions')
            ->where('transaction_id', (int) $transactionId)
            ->whereIn('status', ['completed', 'partially_returned'])
            ->get()->getRowArray();
        if (! $transaction) {
            return redirect()->to('retur')->with('error', 'Transaksi tidak ditemukan atau sudah selesai diretur.');
        }

        $details = $db->table('transaction_details')
            ->select('transaction_details.*, products.type')
            ->join('products', 'products.product_id = transaction_details.product_id')
            ->where('transaction_id', (int) $transactionId)
            ->get()->getResultArray();
        if ($details === []) {
            return redirect()->to('retur')->with('error', 'Detail transaksi tidak ditemukan.');
        }

        $db->transBegin();
        foreach ($details as $detail) {
            $returnableQty = (int) $detail['qty'] - (int) ($detail['return_qty'] ?? 0);
            if ($returnableQty < 1) {
                continue;
            }

            if ($detail['type'] === 'Handphone') {
                $updated = $db->table('inventory_imei')
                    ->where('imei', $detail['imei'])
                    ->where('status', 'Terjual')
                    ->update(['status' => 'Tersedia']);
                if (! $updated || $db->affectedRows() !== 1) {
                    $db->transRollback();
                    return redirect()->to('retur')->with('error', 'IMEI ' . $detail['imei'] . ' tidak dapat dikembalikan.');
                }
            } else {
                $unitCost = $detail['qty'] > 0 ? ((float) $detail['purchase_cost'] / (int) $detail['qty']) : 0;
                $db->table('inventory_aksesoris')->insert([
                    'product_id'     => $detail['product_id'],
                    'qty'            => $returnableQty,
                    'purchase_price' => $unitCost,
                ]);
            }

            $db->table('transaction_details')
                ->where('detail_id', $detail['detail_id'])
                ->update([
                    'return_qty'    => (int) $detail['qty'],
                    'return_reason' => $reason,
                    'returned_at'   => date('Y-m-d H:i:s'),
                ]);
        }

        $db->table('transactions')
            ->where('transaction_id', (int) $transactionId)
            ->update(['status' => $finalStatus]);

        $db->table('audit_logs')->insert([
            'user_id'        => $session->get('user_id'),
            'action'         => $finalStatus === 'cancelled' ? 'sale.cancelled' : 'sale.returned',
            'reference_type' => 'transaction',
            'reference_id'   => (string) $transactionId,
            'details'        => json_encode(['invoice_number' => $transaction['invoice_number'], 'reason' => $reason, 'status' => $finalStatus]),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        if (! $db->transStatus()) {
            $db->transRollback();
            return redirect()->to('retur')->with('error', 'Retur gagal disimpan.');
        }
        $db->transCommit();

        return redirect()->to('retur')->with('pesan', 'Transaksi ' . $transaction['invoice_number'] . ' berhasil diproses.');
    }
}
