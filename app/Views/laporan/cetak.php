<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Penjualan</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        
        /* =========================================
           CSS HEADER V2 (DENGAN LOGO)
           ========================================= */
        .header-wrapper { 
            display: flex; 
            align-items: center; 
            border-bottom: 2px solid #000; 
            padding-bottom: 15px; 
            margin-bottom: 20px; 
        }
        .header-logo { 
            width: 120px; 
            text-align: left; 
        }
        .header-logo img { 
            max-height: 90px; 
            max-width: 100%; 
            object-fit: contain; 
        }
        .header-content { 
            flex-grow: 1; 
            text-align: center; 
        }
        .header-content h2 { 
            margin: 0; 
            font-size: 24px; 
            text-transform: uppercase; 
        }
        .header-content p { 
            margin: 5px 0 0 0; 
        }
        .header-spacer { 
            width: 120px; /* Penyeimbang agar teks tetap di tengah sempurna */
        }
        
        /* CSS TABEL TETAP SAMA */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* CSS FOOTER TANDA TANGAN */
        .footer-ttd { width: 100%; margin-top: 50px; display: table; }
        .ttd-box { display: table-cell; text-align: center; width: 50%; }
        .ttd-name { display: block; margin-top: 70px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body onload="window.print()">

    <!-- AREA HEADER BERLOGO -->
    <div class="header-wrapper">
        <div class="header-logo">
            <?php if(!empty($toko['logo'])): ?>
                <img src="<?= base_url('uploads/logo/' . $toko['logo']) ?>" alt="Logo Toko">
            <?php endif; ?>
        </div>
        <div class="header-content">
            <h2><?= esc($toko['store_name']); ?></h2>
            <p><?= esc($toko['address']); ?> | Telp: <?= esc($toko['phone']); ?></p>
            <h3 style="margin-top: 15px; margin-bottom: 5px;">LAPORAN KEUANGAN PENJUALAN</h3>
            <p>
                Periode: 
                <b>
                    <?= (!empty($start_date) && !empty($end_date)) ? date('d-m-Y', strtotime($start_date)) . ' s/d ' . date('d-m-Y', strtotime($end_date)) : 'Semua Waktu'; ?>
                </b>
            </p>
        </div>
        <div class="header-spacer"></div> <!-- Spacer penyeimbang -->
    </div>

    <!-- AREA TABEL LAPORAN (ROWSPAN AKTIF) -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="20%">No. Nota</th>
                <th width="30%">Barang & Keterangan</th>
                <th width="15%">Total Modal</th>
                <th width="15%">Total Terjual</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // LOGIKA MENGHITUNG ROWSPAN
            $rowspans = [];
            foreach($laporan as $row) {
                $inv = $row['invoice_number'];
                if(!isset($rowspans[$inv])) {
                    $rowspans[$inv] = 0;
                }
                $rowspans[$inv]++;
            }

            $no = 1; 
            $grand_modal = 0; 
            $grand_jual = 0;
            $current_invoice = ''; 

            foreach($laporan as $row): 
                $grand_modal += $row['modal'];
                $grand_jual += $row['price_sold'];
                $inv = $row['invoice_number'];
            ?>
            <tr>
                <!-- CETAK KOLOM NO, TANGGAL, & NOTA -->
                <?php if($inv !== $current_invoice): ?>
                    <td class="text-center" rowspan="<?= $rowspans[$inv]; ?>" style="vertical-align: top;"><?= $no++; ?></td>
                    <td rowspan="<?= $rowspans[$inv]; ?>" style="vertical-align: top;"><?= date('d/m/Y H:i', strtotime($row['transaction_date'])); ?></td>
                    <td rowspan="<?= $rowspans[$inv]; ?>" style="vertical-align: top;"><?= $inv; ?></td>
                    <?php $current_invoice = $inv; ?>
                <?php endif; ?>

                <!-- KOLOM BARANG & HARGA -->
                <td>
                    <?= $row['brand'] . ' ' . $row['model_name']; ?><br>
                    <?php if($row['type'] == 'Handphone'): ?>
                        <small>IMEI: <?= $row['imei']; ?></small>
                    <?php else: ?>
                        <small>QTY: <?= $row['qty']; ?> Unit</small>
                    <?php endif; ?>
                </td>
                <td class="text-right">Rp <?= number_format($row['modal'], 0, ',', '.'); ?></td>
                <td class="text-right">Rp <?= number_format($row['price_sold'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">TOTAL KESELURUHAN</th>
                <th class="text-right">Rp <?= number_format($grand_modal, 0, ',', '.'); ?></th>
                <th class="text-right">Rp <?= number_format($grand_jual, 0, ',', '.'); ?></th>
            </tr>
            <tr>
                <th colspan="4" class="text-right">TOTAL LABA BERSIH (KEUNTUNGAN)</th>
                <th colspan="2" class="text-center" style="font-size: 16px;">
                    Rp <?= number_format($grand_jual - $grand_modal, 0, ',', '.'); ?>
                </th>
            </tr>
        </tfoot>
    </table>

    <div class="footer-ttd">
        <div class="ttd-box"></div>
        <div class="ttd-box">
            Mengetahui, Owner<br>
            <span class="ttd-name"><?= strtoupper(esc($username)); ?></span>
        </div>
    </div>

</body>
</html>