<?php
$tokoNama = $toko['store_name'] ?? 'WM Cellular';
$tokoLogo = $toko['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan & Analisis - Konter POS</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root { --ink: #172033; --muted: #718096; --line: #e6ebf2; --paper: #f5f7fb; --amber: #f4b740; --cyan: #39b8c8; --navy: #111a2d; --green: #169b78; }
        body { background: var(--paper); color: var(--ink); font-family: 'DM Sans', sans-serif; }
        .navbar { background: var(--navy) !important; }
        .navbar-brand, h1, h2, h3, h4, h5 { font-family: 'Space Grotesk', sans-serif; }
        .report-shell { max-width: 1540px; }
        .store-logo { width: 40px; height: 40px; padding: 5px; object-fit: contain; border-radius: 11px; background: var(--amber); }
        .page-kicker { color: var(--muted); font-size: .72rem; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; }
        .filter-panel, .chart-panel, .table-panel { border: 1px solid var(--line); border-radius: 18px; box-shadow: 0 12px 32px rgba(23,32,51,.06) !important; }
        .filter-panel { background: #fff; }
        .filter-panel label { color: var(--muted); font-size: .78rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .form-control { border-color: #dce4ef; border-radius: 10px; }
        .form-control:focus { border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(57,184,200,.12); }
        .btn-primary { background: var(--ink); border-color: var(--ink); }
        .btn-primary:hover { background: #263653; border-color: #263653; }
        .btn-report { background: var(--amber); border-color: var(--amber); color: var(--ink); }
        .btn-report:hover { background: #dda32e; border-color: #dda32e; color: var(--ink); }
        .summary-card { min-height: 148px; border: 0; border-radius: 18px; overflow: hidden; position: relative; box-shadow: 0 12px 26px rgba(23,32,51,.09) !important; }
        .summary-card:after { content: ''; position: absolute; width: 110px; height: 110px; right: -35px; bottom: -50px; border: 18px solid rgba(255,255,255,.16); border-radius: 50%; }
        .summary-card .card-body { position: relative; z-index: 1; }
        .summary-card .metric-label { font-size: .72rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; opacity: .75; }
        .summary-card h3 { font-size: clamp(1.25rem, 2vw, 1.8rem); }
        .summary-card .icon-box-summary { font-size: 2.5rem; opacity: .52; }
        .summary-sales { background: #1e72e8; color: #fff; }
        .summary-cost { background: #556274; color: #fff; }
        .summary-profit { background: var(--green); color: #fff; }
        .summary-units { background: #23b8cf; color: var(--ink); }
        .panel-title { color: var(--ink); font-weight: 700; }
        .chart-panel .card-body { min-height: 330px; }
        .table thead th { font-size: .7rem; letter-spacing: .08em; text-transform: uppercase; }
        .table tbody tr { border-color: var(--line); }
        .table tbody tr:hover { background: #fbfcfe; }
        .dataTables_wrapper .dataTables_filter input, .dataTables_wrapper .dataTables_length select { border: 1px solid var(--line); border-radius: 8px; padding: .4rem .6rem; }
        @media (max-width: 767px) { .container-fluid { padding-left: 14px !important; padding-right: 14px !important; } .navbar-text { display: none !important; } }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="<?= base_url('dashboard') ?>">
                <i class="bi bi-arrow-left-circle me-1"></i> Dashboard
            </a>
            <span class="navbar-text text-white fw-bold mx-auto d-none d-md-block">
                <?php if ($tokoLogo): ?><img class="store-logo me-2 align-middle" src="<?= base_url('uploads/logo/' . $tokoLogo); ?>" alt="Logo <?= esc($tokoNama); ?>"><?php endif; ?><i class="bi bi-graph-up-arrow me-1"></i> <?= esc($tokoNama); ?> / ANALISIS KEUANGAN
            </span>
            <div class="d-flex text-white align-items-center">
                <i class="bi bi-person-circle fs-5 me-2"></i> Owner: <?= esc($username); ?>
            </div>
        </div>
    </nav>

    <div class="container-fluid report-shell px-4 py-4">
        <!-- FORM FILTER TANGGAL & TOMBOL CETAK -->
     <div class="filter-panel card mb-4">
         <div class="card-body p-4">
             <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
                 <div><div class="page-kicker mb-1">Owner workspace</div><h3 class="fw-bold mb-1">Laporan keuangan</h3><p class="text-secondary mb-0">Pantau omzet, modal, laba, dan detail transaksi berdasarkan periode.</p></div>
             <form action="<?= base_url('laporan/keuangan') ?>" method="GET" class="row g-3 align-items-center">
                 <div class="col-auto">
                     <label class="d-block mb-1">Mulai</label><input type="date" name="start_date" class="form-control fw-bold" value="<?= isset($start_date) ? esc($start_date) : ''; ?>" required>
                 </div>
                 <div class="col-auto d-none d-sm-block pb-2 text-secondary">s/d</div>
                 <div class="col-auto">
                     <label class="d-block mb-1">Sampai</label><input type="date" name="end_date" class="form-control fw-bold" value="<?= isset($end_date) ? esc($end_date) : ''; ?>" required>
                 </div>
                 <div class="col-auto">
                     <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-funnel me-1"></i> Terapkan</button>
                     <a href="<?= base_url('laporan/keuangan') ?>" class="btn btn-light shadow-sm"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                 </div>
                 <div class="col-auto">
                     <!-- Tombol Cetak Membuka Tab Baru -->
                     <button type="button" onclick="cetakLaporan()" class="btn btn-report fw-bold shadow-sm">
                         <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                     </button>
                 </div>
             </form>
             </div>
         </div>
     </div>

     <script>
         // Fungsi JS untuk mem-passing tanggal ke halaman cetak
         function cetakLaporan() {
             let start = document.querySelector('input[name="start_date"]').value;
             let end = document.querySelector('input[name="end_date"]').value;
             let url = '<?= base_url('laporan/cetak') ?>';
             if (start && end) {
                 url += '?start_date=' + start + '&end_date=' + end;
             }
             window.open(url, '_blank', 'width=900,height=600');
         }
     </script>
        <!-- 4 Kotak Ringkasan (KPI) -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card summary-card summary-sales h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="metric-label mb-1">Total omzet</p>
                            <h3 class="mb-0 fw-bold">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                        </div>
                        <i class="bi bi-wallet2 icon-box-summary"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card summary-card summary-cost h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="metric-label mb-1">Total modal</p>
                            <h3 class="mb-0 fw-bold">Rp <?= number_format($total_modal, 0, ',', '.'); ?></h3>
                        </div>
                        <i class="bi bi-box-arrow-in-down icon-box-summary"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card summary-card summary-profit h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="metric-label mb-1">Laba bersih</p>
                            <h3 class="mb-0 fw-bold">Rp <?= number_format($laba_bersih, 0, ',', '.'); ?></h3>
                        </div>
                        <i class="bi bi-cash-coin icon-box-summary"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card summary-card summary-units h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="metric-label mb-1">Unit terjual</p>
                            <h3 class="mb-0 fw-bold"><?= $total_unit; ?> Unit</h3>
                        </div>
                        <i class="bi bi-phone icon-box-summary text-dark"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRAFIK CHART.JS -->
        <div class="card chart-panel mb-4">
            <div class="card-header bg-white pt-3 pb-2 border-0">
                <h5 class="panel-title"><i class="bi bi-bar-chart-line-fill me-2" style="color: var(--cyan);"></i>Tren penjualan harian</h5>
            </div>
            <div class="card-body">
                <canvas id="grafikPenjualan" height="80"></canvas>
            </div>
        </div>

        <!-- TABEL RINCIAN -->
        <div class="card table-panel">
            <div class="card-header bg-white pt-3 pb-2 border-0 d-flex justify-content-between align-items-center">
                <h5 class="panel-title mb-0"><i class="bi bi-receipt-cutoff me-2" style="color: var(--green);"></i>Rincian transaksi per unit</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelLaporan" class="table table-striped table-hover table-bordered align-middle mb-0" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal Transaksi</th>
                                <th>No. Nota</th>
                                <th>Barang & IMEI/QTY</th>
                                <th>Harga Modal</th>
                                <th>Harga Terjual</th>
                                <th>Laba (Untung)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($laporan)): ?>
                                <?php foreach($laporan as $row): ?>
                                    <tr>
                                        <td>
                                            <span class="d-none"><?= date('YmdHi', strtotime($row['transaction_date'])); ?></span> <!-- Untuk sorting -->
                                            <?= date('d-m-Y H:i', strtotime($row['transaction_date'])); ?>
                                        </td>
                                            <!-- Ubah Baris Nomor Nota menjadi Link -->
                                        <td>
                                            <a href="<?= base_url('kasir/cetak/'.$row['invoice_number'].'/copy') ?>" target="_blank" class="fw-bold text-decoration-none" title="Cetak Ulang Nota Ini">
                                            <i class="bi bi-printer-fill me-1"></i> <?= $row['invoice_number']; ?>
                                            </a>
                                        </td>
                                        <td>  
                                            <span class="fw-bold"><?= $row['brand'] . ' ' . $row['model_name']; ?></span><br>
                                            
                                            <!-- LOGIKA V2 TAMPILAN BARANG -->
                                            <?php if($row['type'] == 'Handphone'): ?>
                                                <small class="text-muted"><i class="bi bi-upc"></i> IMEI: <?= $row['imei']; ?></small>
                                            <?php else: ?>
                                                <small class="text-muted"><i class="bi bi-boxes"></i> QTY: <?= $row['qty']; ?> Unit</small>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- MEMANGGIL VARIABEL V2 -->
                                        <td>Rp <?= number_format($row['modal'], 0, ',', '.'); ?></td>
                                        <td>Rp <?= number_format($row['price_sold'], 0, ',', '.'); ?></td>
                                        <td class="fw-bold <?= $row['laba'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                            Rp <?= number_format($row['laba'], 0, ',', '.'); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pustaka JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#tabelLaporan').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
                order: [[0, 'desc']] // Urutkan dari transaksi terbaru
            });
        });

        // ==========================================
        // RENDER GRAFIK CHART.JS
        // ==========================================
        const ctx = document.getElementById('grafikPenjualan').getContext('2d');
        
        // Mengambil data JSON dari Controller PHP
        const labelTanggal = <?= $grafik_tanggal; ?>;
        const dataPendapatan = <?= $grafik_pendapatan; ?>;
        const dataLaba = <?= $grafik_laba; ?>;

        new Chart(ctx, {
            type: 'line', // Jenis grafik garis
            data: {
                labels: labelTanggal,
                datasets: [
                    {
                        label: 'Laba Bersih (Rp)',
                        data: dataLaba,
                        borderColor: '#169b78',
                        backgroundColor: 'rgba(22, 155, 120, 0.12)',
                        borderWidth: 3,
                        pointRadius: 5,
                        fill: true,
                        tension: 0.3 // Garis agak melengkung
                    },
                    {
                        label: 'Total Omzet (Rp)',
                        data: dataPendapatan,
                        borderColor: '#39b8c8',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 4,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        labels: { usePointStyle: true, boxWidth: 8, color: '#718096', font: { family: 'DM Sans' } }
                    },
                    tooltip: {
                        callbacks: {
                            // Format angka tooltip jadi mata uang Rupiah
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e6ebf2' },
                        ticks: {
                            // Ringkas angka panjang di sumbu Y (misal 1000000 jadi 1 Jt)
                            callback: function(value, index, values) {
                                if(value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                if(value >= 1000) return 'Rp ' + (value / 1000) + ' Rb';
                                return 'Rp ' + value;
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>