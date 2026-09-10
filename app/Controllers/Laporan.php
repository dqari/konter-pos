<?php
namespace App\Controllers;

class Laporan extends BaseController
{
    // ==========================================
    // FUNGSI TAMPILAN WEB LAPORAN KEUANGAN
    // ==========================================
    public function keuangan()
    {
        $session = session();
        if ($session->get('role') != 'Owner' || !$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Akses Ditolak!');
            return redirect()->to('dashboard');
        }

        $start_date = $this->request->getGet('start_date');
        $end_date   = $this->request->getGet('end_date');

        $db = \Config\Database::connect();
        $builder = $db->table('transaction_details');
        
        // QUERY V2: Membawa Tipe Produk dan QTY dari database
        $builder->select('transactions.transaction_date, transactions.invoice_number, products.brand, products.model_name, products.type, transaction_details.imei, transaction_details.qty, transaction_details.price_sold, transaction_details.purchase_cost, transaction_details.product_id');
        $builder->join('transactions', 'transactions.transaction_id = transaction_details.transaction_id');
        $builder->join('products', 'products.product_id = transaction_details.product_id');
        
        if (!empty($start_date) && !empty($end_date)) {
            $builder->where('DATE(transactions.transaction_date) >=', $start_date);
            $builder->where('DATE(transactions.transaction_date) <=', $end_date);
        }
        $builder->orderBy('transactions.transaction_date', 'DESC');
        $dataLaporan = $builder->get()->getResultArray();

        $total_pendapatan = 0; $total_modal = 0; $total_unit = 0;
        $rekap_harian = [];
        $laporan_final = [];

        foreach ($dataLaporan as $row) {
            $total_modal_baris = (float) $row['purchase_cost'];
            $laba_baris = $row['price_sold'] - $total_modal_baris;

            $total_pendapatan += $row['price_sold'];
            $total_modal += $total_modal_baris;
            $total_unit += $row['qty']; // Menjumlahkan QTY terjual

            // Menyimpan variabel baru untuk dikirim ke View
            $row['modal'] = $total_modal_baris;
            $row['laba'] = $laba_baris;
            $laporan_final[] = $row;

            $tgl = date('Y-m-d', strtotime($row['transaction_date']));
            if(!isset($rekap_harian[$tgl])) $rekap_harian[$tgl] = ['pendapatan' => 0, 'laba' => 0];
            $rekap_harian[$tgl]['pendapatan'] += $row['price_sold'];
            $rekap_harian[$tgl]['laba'] += $laba_baris;
        }
        
        ksort($rekap_harian);
        $grafik_tanggal = []; $grafik_pendapatan = []; $grafik_laba = [];
        foreach($rekap_harian as $tgl => $nilai) {
            $grafik_tanggal[] = date('d M Y', strtotime($tgl));
            $grafik_pendapatan[] = $nilai['pendapatan'];
            $grafik_laba[] = $nilai['laba'];
        }

        $data = [
            'username'         => $session->get('username'),
            'toko'             => $db->table('store_profile')->get()->getRowArray(),
            'laporan'          => $laporan_final, // Gunakan array yang sudah matang
            'start_date'       => $start_date,
            'end_date'         => $end_date,
            'total_pendapatan' => $total_pendapatan,
            'total_modal'      => $total_modal,
            'laba_bersih'      => $total_pendapatan - $total_modal,
            'total_unit'       => $total_unit,
            'grafik_tanggal'   => json_encode($grafik_tanggal),
            'grafik_pendapatan'=> json_encode($grafik_pendapatan),
            'grafik_laba'      => json_encode($grafik_laba)
        ];

        return view('laporan/keuangan', $data);
    }

    // ==========================================
    // FUNGSI CETAK LAPORAN (KERTAS A4)
    // ==========================================
    public function cetak()
    {
        $session = session();
        if ($session->get('role') != 'Owner') return redirect()->to('dashboard');

        $start_date = $this->request->getGet('start_date');
        $end_date   = $this->request->getGet('end_date');

        $db = \Config\Database::connect();
        $builder = $db->table('transaction_details');
        $builder->select('transactions.transaction_date, transactions.invoice_number, products.brand, products.model_name, products.type, transaction_details.imei, transaction_details.qty, transaction_details.price_sold, transaction_details.purchase_cost, transaction_details.product_id');
        $builder->join('transactions', 'transactions.transaction_id = transaction_details.transaction_id');
        $builder->join('products', 'products.product_id = transaction_details.product_id');
        
        if (!empty($start_date) && !empty($end_date)) {
            $builder->where('DATE(transactions.transaction_date) >=', $start_date);
            $builder->where('DATE(transactions.transaction_date) <=', $end_date);
        }
        $builder->orderBy('transactions.transaction_date', 'ASC'); 
        $dataLaporan = $builder->get()->getResultArray();

        $laporan_final = [];
        foreach ($dataLaporan as $row) {
            $row['modal'] = (float) $row['purchase_cost'];
            $laporan_final[] = $row;
        }

        $toko = $db->table('store_profile')->get()->getRowArray();

        $data = [
            'toko'       => $toko,
            'laporan'    => $laporan_final,
            'start_date' => $start_date,
            'end_date'   => $end_date,
            'username'   => $session->get('username')
        ];

        return view('laporan/cetak', $data);
    }
}