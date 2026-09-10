<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota - <?= $transaksi['invoice_number']; ?></title>
    <style>
        /* SETELAN KERTAS CONTINUOUS FORM (A4 HALF / 9.5 x 5.5 inch) */
        @page {
            size: 20cm 14cm; /* Lebar 20cm, Tinggi 14cm agar muat banyak barang */
            margin: 0; 
        }
        
        body {
            margin: 0;
            padding: 3mm 5mm; /* Jarak aman tepi kertas ditipiskan */
            font-family: 'Courier New', Courier, monospace; 
            font-size: 11px; /* Ukuran font standar diperkecil sedikit */
            color: #000;
            background: #fff;
            line-height: 1.2; /* Merapatkan jarak antar baris */
        }

        /* HEADER DIPERPADAT */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 1px dashed #000; 
            padding-bottom: 4px; /* Hemat ruang vertikal */
            margin-bottom: 4px;
        }
        .logo {
            width: 50px; /* Logo dikecilkan */
            height: 50px;
            object-fit: contain;
            margin-right: 12px;
            filter: grayscale(100%) contrast(1.2) brightness(0.9);
        }
        .toko-info { flex-grow: 1; }
        .toko-info h2 { margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .toko-info p { margin: 2px 0 0 0; font-size: 10px; }
        
        /* INFO NOTA */
        .meta-transaksi {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 10px;
        }
        
        /* TABEL BARANG LEBIH KOMPAK */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        th, td {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 3px 2px; /* Jarak atas-bawah teks di tabel ditipiskan */
            text-align: left;
            font-size: 10px;
        }
        th { font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .imei-text {
            font-size: 9px; /* Font IMEI lebih kecil agar tidak makan tempat */
            display: block;
            margin-top: 1px;
            padding-left: 8px; 
        }

        .total-row td {
            border-top: 1px solid #000; 
            border-bottom: none;
            font-weight: bold;
            font-size: 12px;
            padding-top: 5px;
        }

        /* AREA TTD DINAIKKAN */
        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 8px; /* Lebih mepet ke tabel total */
            padding: 0 15px; 
            font-size: 10px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-name {
            display: block;
            margin-top: 30px; /* Jarak untuk coretan pulpen dikurangi sedikit */
        }

        /* FOOTER LEBIH TIPIS */
        .footer {
            text-align: center;
            font-size: 9px;
            border-top: 1px dashed #000;
            padding-top: 4px;
            margin-top: 8px;
        }
        
        @media print {
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <?php if(!empty($toko['logo'])): ?>
            <img src="<?= base_url('uploads/logo/' . $toko['logo']) ?>" class="logo" alt="Logo Toko">
        <?php endif; ?>
        <div class="toko-info">
            <h2><?= esc($toko['store_name']); ?></h2>
            <p>
                <?= esc($toko['address']); ?><br>
                Telp/WA: <?= esc($toko['phone']); ?> | Email: <?= esc($toko['email']); ?>
            </p>
        </div>
    </div>

    <div class="meta-transaksi">
        <!-- TAMBAHKAN BLOK INI UNTUK PENANDA CETAK ULANG -->
     <?php if(isset($is_reprint) && $is_reprint == true): ?>
     <div style="width: 100%; text-align: center; border: 2px dashed #000; padding: 4px; margin-bottom: 8px; font-weight: bold; font-size: 14px;">
         *** CETAK ULANG (SALINAN) ***
     </div>
     <?php endif; ?>
        <div>
            NO. NOTA  : <?= $transaksi['invoice_number']; ?><br>
            TANGGAL   : <?= date('d-m-Y H:i', strtotime($transaksi['transaction_date'])); ?><br>
            PELANGGAN : <b><?= !empty($transaksi['customer_name']) ? esc($transaksi['customer_name']) : 'UMUM'; ?></b>
            <?= !empty($transaksi['customer_phone']) ? ' (' . esc($transaksi['customer_phone']) . ')' : ''; ?>
        </div>
        <div class="text-right">
            KASIR : <?= strtoupper(esc($kasir)); ?><br>
            BAYAR : <?= strtoupper($transaksi['payment_method']); ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">QTY</th>
                <th width="55%">DESKRIPSI BARANG</th>
                <th width="40%" class="text-right">HARGA (Rp)</th>
            </tr>
        </thead>
       <tbody>
            <?php foreach($detail as $d): ?>
            <tr>
                <!-- 1. Menampilkan QTY yang sebenarnya (Bukan angka 1 mati) -->
                <td class="text-center" style="vertical-align: top;"><?= $d['qty']; ?></td>
                
                <td>
                    <b><?= strtoupper($d['brand'] . ' ' . $d['model_name']); ?></b>
                    
                    <!-- 2. Logika Cerdas: Tampilkan IMEI HANYA jika tipenya Handphone -->
                    <?php if($d['type'] == 'Handphone'): ?>
                        <span class="imei-text">SN/IMEI: <?= $d['imei']; ?></span>
                    <?php endif; ?>
                </td>
                
                <td class="text-right" style="vertical-align: top;">
                    <?= number_format($d['price_sold'], 0, ',', '.'); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-right">TOTAL KESELURUHAN :</td>
                <td class="text-right">Rp <?= number_format($transaksi['total_amount'], 0, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            Pelanggan,<br>
            <span class="signature-name">
                ( <?= !empty($transaksi['customer_name']) ? strtoupper(esc($transaksi['customer_name'])) : '........................'; ?> )
            </span>
        </div>
        <div class="signature-box">
            Hormat Kami,<br>
            <span class="signature-name">
                ( ........................ )
            </span>
        </div>
    </div>

    <div class="footer">
        <?= esc($toko['footer_nota']); ?><br>
        *** TERIMA KASIH ATAS KUNJUNGAN ANDA ***
    </div>

</body>
</html>