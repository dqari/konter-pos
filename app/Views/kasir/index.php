<?php
$tokoNama = $toko['store_name'] ?? 'WM Cellular';
$tokoLogo = $toko['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir V2 - Konter POS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --ink: #172033; --muted: #718096; --line: #e6ebf2; --paper: #f5f7fb; --amber: #f4b740; --cyan: #39b8c8; --navy: #111a2d; }
        body { background: var(--paper); color: var(--ink); font-family: 'DM Sans', sans-serif; }
        .navbar { background: var(--navy) !important; border-bottom: 0 !important; min-height: 74px; }
        .navbar-brand, h1, h2, h3, h4, h5, .angka-total { font-family: 'Space Grotesk', sans-serif; }
        .store-logo { width: 42px; height: 42px; padding: 5px; object-fit: contain; border-radius: 12px; background: var(--amber); }
        .card { border-radius: 18px; border: 1px solid var(--line); box-shadow: 0 12px 32px rgba(23, 32, 51, .06) !important; }
        .cashier-shell { max-width: 1540px; }
        .page-kicker { color: var(--muted); font-size: .75rem; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; }
        .scanner-card { border-top: 4px solid var(--amber); background: #fff; }
        .scanner-input { border: 2px solid #dce4ef; border-radius: 12px; color: var(--ink); }
        .scanner-input:focus { border-color: var(--cyan); box-shadow: 0 0 0 4px rgba(57, 184, 200, .14); }
        .scanner-button { background: var(--ink); border: 0; border-radius: 12px; }
        .scanner-button:hover { background: #263653; }
        .scan-hint { background: #f3fbfc; border: 1px solid #d6f0f3; border-radius: 14px; }
        .cart-card { background: #fff; }
        .cart-heading { border-bottom: 1px solid var(--line); }
        .layar-total { background: var(--navy) !important; border: 0 !important; border-radius: 16px; padding: 20px 24px; box-shadow: 0 10px 24px rgba(17, 26, 45, .18); }
        .angka-total { font-size: clamp(2rem, 4vw, 3.5rem); color: var(--amber); text-shadow: none; }
        .table-wrap { border: 1px solid var(--line) !important; border-radius: 14px !important; }
        .table thead th { color: var(--muted); font-size: .7rem; letter-spacing: .08em; text-transform: uppercase; border-bottom-width: 1px; }
        .table tbody tr { border-color: var(--line); }
        .checkout-panel { background: #f8fbfb !important; border-color: #bfe8eb !important; border-radius: 14px !important; }
        .btn-success { background: #169b78; border-color: #169b78; }
        .btn-success:hover { background: #117c61; border-color: #117c61; }
        .form-control, .form-select { border-color: #dce4ef; border-radius: 10px; }
        .form-control:focus, .form-select:focus { border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(57, 184, 200, .12); }
        @media (max-width: 767px) { .container-fluid { padding-left: 14px !important; padding-right: 14px !important; } .angka-total { font-size: 2.25rem; } .navbar-text { display: none !important; } }
    </style>
</head>
<body>

    <!-- NAVBAR BRANDING PREMIUM -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top" style="background-color: #0f172a; border-bottom: 3px solid #ffc107;">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= base_url('dashboard') ?>">
                <?php if ($tokoLogo): ?><img class="store-logo me-2" src="<?= base_url('uploads/logo/' . $tokoLogo); ?>" alt="Logo <?= esc($tokoNama); ?>"><?php else: ?><i class="bi bi-shop text-warning fs-3 me-2"></i><?php endif; ?>
                <div>
                    <span class="d-block" style="font-size: 1.2rem; line-height: 1; letter-spacing: 1px;"><?= esc($tokoNama); ?></span>
                    <span class="text-white-50" style="font-size: 0.7rem; letter-spacing: 2px;">POINT OF SALE</span>
                </div>
            </a>
            <span class="navbar-text text-white fw-bold mx-auto d-none d-md-block fs-5">
                <i class="bi bi-cart4 me-1"></i> MEJA KASIR
            </span>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-warning fw-bold" onclick="openCustomerDisplay()" title="Buka layar pelanggan">
                    <i class="bi bi-display me-1"></i> Layar Pelanggan
                </button>
                <div class="d-flex text-white align-items-center bg-white bg-opacity-10 px-3 py-1 rounded-pill border border-light border-opacity-25 shadow-sm">
                <i class="bi bi-person-circle fs-5 me-2 text-warning"></i> Kasir: <?= esc($username ?? 'Admin'); ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid cashier-shell px-4 py-4">

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-5 border-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('sukses')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-5 border-success text-center fs-5" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <strong><?= session()->getFlashdata('sukses') ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- SCRIPT AUTO-PRINT NOTA -->
        <?php if(session()->getFlashdata('cetak_nota')): ?>
            <script>
                window.open('<?= base_url('kasir/cetak/'.session()->getFlashdata('cetak_nota')) ?>', '_blank', 'width=800,height=500');
            </script>
        <?php endif; ?>

        <div class="row g-4">
            <!-- KIRI: SCANNER -->
            <div class="col-12 col-lg-4">
                <div class="card scanner-card h-100">
                    <div class="card-body p-4">
                        <div class="page-kicker mb-2">Meja kasir</div>
                        <h4 class="fw-bold mb-2"><i class="bi bi-upc-scan me-2" style="color: var(--cyan);"></i>Scan produk</h4>
                        <p class="text-secondary small mb-4">Gunakan scanner untuk IMEI handphone atau barcode aksesori.</p>

                        <!-- FORM ACTION DIPERBAIKI MENJADI kasir/scan -->
                        <form action="<?= base_url('kasir/scan') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <input type="text" name="imei" class="form-control form-control-lg scanner-input text-center fw-bold" placeholder="Arahkan scanner ke sini" required autofocus autocomplete="off" style="font-size: 1.35rem; height: 64px;">
                            </div>
                            <button type="submit" class="btn scanner-button btn-lg w-100 text-white fw-bold shadow-sm">
                                <i class="bi bi-cart-plus-fill me-1"></i> Tambah ke Keranjang
                            </button>
                        </form>

                        <div class="mt-5 p-3 scan-hint text-center">
                            <i class="bi bi-lightning-charge-fill fs-4 d-block mb-2" style="color: var(--amber);"></i>
                            <small class="text-secondary">Sistem otomatis mengenali jenis produk dan memeriksa stok tersedia.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KANAN: KERANJANG -->
            <div class="col-12 col-lg-8">
                <div class="card cart-card h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="cart-heading d-flex justify-content-between align-items-center mb-4 pb-3">
                            <div><div class="page-kicker">Transaksi aktif</div><h4 class="mb-0 fw-bold">Keranjang belanja</h4></div>
                            <span class="badge rounded-pill text-bg-light border px-3 py-2"><i class="bi bi-shield-check me-1 text-success"></i>Siap diproses</span>
                        </div>

                        <?php 
                            $total = 0; 
                            $keranjang = session()->get('keranjang') ?? [];
                            foreach($keranjang as $k) { $total += $k['harga_akhir']; } 
                        ?>

                        <!-- LAYAR TOTAL BRANDING PREMIUM -->
                        <div class="layar-total mb-4 position-relative overflow-hidden" style="background: linear-gradient(to right, #111827, #000000); border: 2px solid #333;">
                            <div class="position-absolute" style="right: 10px; top: -15px; opacity: 0.05;">
                                <i class="bi bi-basket2-fill" style="font-size: 8rem; color: #fff;"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1 position-relative">
                                <p class="text-white-50 fw-bold mb-0" style="letter-spacing: 2px; font-size: 0.8rem;">TOTAL BELANJA</p>
                                <span class="badge bg-warning text-dark rounded-pill fw-bold" style="font-size: 0.7rem;"><?= esc($tokoNama); ?></span>
                            </div>
                            <p class="angka-total position-relative">Rp <?= number_format($total, 0, ',', '.'); ?></p>
                        </div>

                        <div class="table-responsive flex-grow-1 table-wrap mb-4">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">Produk & Kode</th>
                                        <th width="15%" class="text-center">QTY</th>
                                        <th width="20%" class="text-end">Harga Satuan</th>
                                        <th width="30%" class="text-center">Diskon & Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($keranjang)): ?>
                                        <?php foreach($keranjang as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold fs-6 text-dark"><?= esc($item['nama']); ?></div>
                                                        <small class="text-muted"><i class="bi bi-upc me-1"></i><?= esc($item['kode_unik']); ?></small><br>
                                                    <!-- Lable Tipe Barang -->
                                                    <?php if($item['tipe_barang'] == 'Handphone'): ?>
                                                        <span class="badge bg-primary rounded-pill" style="font-size: 0.65rem;">Handphone</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;">Aksesoris</span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- KOLOM QTY BARU -->
                                                <td class="text-center">
                                                    <span class="fs-5 fw-bold text-primary"><?= esc($item['qty']); ?></span>
                                                </td>

                                                <td class="text-end fw-bold">
                                                    Rp <?= number_format($item['harga_awal'], 0, ',', '.'); ?>
                                                </td>

                                                <td>
                                                    <form action="<?= base_url('kasir/update_diskon') ?>" method="POST" class="m-0">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="imei" value="<?= esc($item['kode_unik'], 'attr'); ?>">
                                                        <div class="input-group input-group-sm mb-1">
                                                            <span class="input-group-text bg-light">Rp</span>
                                                            <input type="number" name="diskon" class="form-control" value="<?= esc($item['diskon'], 'attr'); ?>" min="0" placeholder="Diskon">
                                                            <button type="submit" class="btn btn-info text-white" title="Terapkan"><i class="bi bi-check-lg"></i></button>
                                                        </div>
                                                    </form>
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <small class="fw-bold text-success">Sub: Rp <?= number_format($item['harga_akhir'], 0, ',', '.'); ?></small>
                                                        <form action="<?= base_url('kasir/hapus_keranjang/'.rawurlencode($item['kode_unik'])) ?>" method="POST" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0" title="Hapus"><i class="bi bi-trash3-fill"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <i class="bi bi-cart-x text-muted opacity-25" style="font-size: 5rem;"></i>
                                                <h5 class="text-muted mt-3 fw-bold">Keranjang Kosong</h5>
                                                <p class="text-muted small">Scan Barcode / IMEI untuk memulai.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if(!empty($keranjang)): ?>
                            <form action="<?= base_url('kasir/checkout') ?>" method="POST" class="checkout-panel p-3 shadow-sm border border-2">
                                <?= csrf_field() ?>
                                <div class="row align-items-center border-bottom pb-3 mb-3">
                                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                                        <label class="form-label fw-bold text-primary"><i class="bi bi-person-lines-fill me-1"></i>Nama Pelanggan (Opsional)</label>
                                        <input type="text" name="customer_name" class="form-control" placeholder="Contoh: Bpk. Budi">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold text-primary"><i class="bi bi-whatsapp me-1"></i>No. HP / WA (Opsional)</label>
                                        <input type="text" name="customer_phone" class="form-control" placeholder="Contoh: 0812...">
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-5 mb-3 mb-md-0">
                                        <label class="form-label fw-bold text-secondary">Metode Pembayaran:</label>
                                        <select name="payment_method" class="form-select form-select-lg fw-bold border-success" required>
                                            <option value="Cash">💵 Cash (Tunai)</option>
                                            <option value="Transfer">🏦 Transfer Bank</option>
                                            <option value="Debit">💳 Kartu Debit</option>
                                            <option value="Qris">📱 QRIS</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-7">
                                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow" onclick="return confirm('Selesaikan transaksi dan cetak nota?')">
                                            <i class="bi bi-printer-fill me-2"></i> PROSES TRANSAKSI
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openCustomerDisplay() {
            const display = window.open('<?= base_url('kasir/pelanggan') ?>', 'wmCustomerDisplay', 'popup=yes,width=1280,height=800,left=0,top=0');
            if (display) {
                display.focus();
            }
        }
    </script>
</body>
</html>