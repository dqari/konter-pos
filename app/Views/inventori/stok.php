<?php
$tokoNama = $toko['store_name'] ?? 'WM Cellular';
$tokoLogo = $toko['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Stok - <?= esc($produk['model_name']); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --ink: #172033; --muted: #718096; --line: #e6ebf2; --paper: #f5f7fb; --amber: #f4b740; --cyan: #39b8c8; --navy: #111a2d; }
        body { background: var(--paper); color: var(--ink); font-family: 'DM Sans', sans-serif; }
        .navbar { background: var(--navy) !important; }
        .navbar-brand, h1, h2, h3, h4, h5 { font-family: 'Space Grotesk', sans-serif; }
        .stock-shell { max-width: 1460px; }
        .card { border-radius: 18px; border: 1px solid var(--line); box-shadow: 0 12px 32px rgba(23, 32, 51, .06) !important; }
        .product-banner { background: #fff; border-left: 4px solid var(--cyan); }
        .section-kicker { color: var(--muted); font-size: .72rem; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; }
        .store-logo { width: 38px; height: 38px; padding: 4px; object-fit: contain; border-radius: 11px; background: var(--amber); }
        .stock-form-header { background: var(--navy) !important; }
        .table thead th { color: var(--muted); font-size: .7rem; letter-spacing: .08em; text-transform: uppercase; }
        .table tbody tr { border-color: var(--line); }
        .table tbody tr:hover { background: #fbfcfe; }
        .form-control { border-color: #dce4ef; border-radius: 10px; }
        .form-control:focus { border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(57,184,200,.12); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('inventori') ?>">
                <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Master Produk
            </a>
            <span class="navbar-text text-white fw-bold mx-auto d-none d-md-block">
                <?php if ($tokoLogo): ?><img class="store-logo me-2 align-middle" src="<?= base_url('uploads/logo/' . $tokoLogo); ?>" alt="Logo <?= esc($tokoNama); ?>"><?php endif; ?><i class="bi bi-box-seam me-1"></i> <?= esc($tokoNama); ?> / KELOLA STOK
            </span>
            <div class="d-flex text-white align-items-center">
                <i class="bi bi-person-circle fs-5 me-2"></i> <?= esc($username); ?>
            </div>
        </div>
    </nav>

    <div class="container stock-shell py-4">
        
        <!-- HEADER PRODUK INFO -->
        <div class="card product-banner shadow-sm mb-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <?php if($produk['type'] == 'Handphone'): ?>
                        <span class="badge bg-primary mb-2 px-3">📱 Handphone</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark mb-2 px-3">🎧 Aksesoris</span>
                    <?php endif; ?>
                    
                    <div class="section-kicker mb-1">Detail persediaan</div>
                    <h3 class="fw-bold mb-0"><?= esc($produk['brand']); ?> <?= esc($produk['model_name']); ?></h3>
                    <div class="text-muted mt-1">
                        Harga Jual Standar: <strong class="text-success">Rp <?= number_format($produk['base_selling_price'], 0, ',', '.'); ?></strong>
                        <?php if($produk['type'] == 'Aksesoris' && !empty($produk['barcode'])): ?>
                            | Barcode: <strong class="text-dark"><?= esc($produk['barcode']); ?></strong>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-none d-md-block text-end">
                    <i class="bi bi-box-seam text-muted opacity-25" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>

        <!-- NOTIFIKASI PESAN -->
        <?php if(session()->getFlashdata('pesan')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-5 border-success" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('pesan') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-5 border-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- ===================================== -->
            <!-- KOLOM KIRI: FORM TAMBAH STOK -->
            <!-- ===================================== -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header stock-form-header text-white p-3 border-0">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-plus-square-fill me-2"></i>Tambah Stok Baru</h5>
                    </div>
                    <div class="card-body p-4 bg-light">
                        <form action="<?= base_url('inventori/simpan_stok') ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= $produk['product_id']; ?>">
                            <input type="hidden" name="type" value="<?= $produk['type']; ?>">

                            <!-- JIKA PRODUK ADALAH HANDPHONE -->
                            <?php if($produk['type'] == 'Handphone'): ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Nomor IMEI</label>
                                    <input type="text" name="imei" class="form-control border-primary bg-white" placeholder="Scan Barcode IMEI..." required autofocus autocomplete="off">
                                    <small class="text-muted">Masukkan 15 digit angka IMEI.</small>
                                </div>
                            
                            <!-- JIKA PRODUK ADALAH AKSESORIS -->
                            <?php else: ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-warning">Jumlah (QTY) Masuk</label>
                                    <input type="number" name="qty" class="form-control border-warning bg-white" placeholder="Contoh: 10" min="1" required autofocus autocomplete="off">
                                    <small class="text-muted">Jumlah unit barang yang datang.</small>
                                </div>
                            <?php endif; ?>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Harga Modal / Beli (Per Unit)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary text-white fw-bold">Rp</span>
                                    <input type="number" name="purchase_price" class="form-control bg-white price-input" placeholder="Contoh: 2000000" min="0" max="50000000" required>
                                    <small class="text-danger price-warning d-none">Periksa kembali nominal modal.</small>
                                </div>
                                <small class="text-muted">Harga asli dari supplier (kulakan).</small>
                            </div>

                            <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold shadow-sm">
                                <i class="bi bi-box-arrow-in-down me-1"></i> Simpan Stok Masuk
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ===================================== -->
            <!-- KOLOM KANAN: TABEL RIWAYAT STOK -->
            <!-- ===================================== -->
            <div class="col-12 col-md-8">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="section-kicker mb-1">Audit stok</div>
                        <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2" style="color: var(--cyan);"></i>Riwayat stok masuk</h5>
                        
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th width="5%">No</th>
                                        <!-- Header Tabel Berubah Sesuai Tipe -->
                                        <?php if($produk['type'] == 'Handphone'): ?>
                                            <th>Nomor IMEI</th>
                                            <th>Status</th>
                                        <?php else: ?>
                                            <th>Jumlah (QTY) Masuk</th>
                                        <?php endif; ?>
                                        <th>Harga Modal</th>
                                        <th>Tanggal Masuk</th>
                                        <?php if($produk['type'] == 'Handphone'): ?>
                                            <th>Aksi</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($stok_detail)): ?>
                                        <?php $no = 1; foreach($stok_detail as $row): ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                
                                                <?php if($produk['type'] == 'Handphone'): ?>
                                                    <td class="fw-bold"><?= esc($row['imei']); ?></td>
                                                    <td class="text-center">
                                                        <?php if($row['status'] == 'Tersedia'): ?>
                                                            <span class="badge bg-success px-3">Tersedia</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary px-3">Terjual</span>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php else: ?>
                                                    <td class="text-center fw-bold fs-5 text-primary">+ <?= $row['qty']; ?> Unit</td>
                                                <?php endif; ?>

                                                <td class="text-end">Rp <?= number_format($row['purchase_price'], 0, ',', '.'); ?></td>
                                                <td class="text-center text-muted small"><?= date('d M Y - H:i', strtotime($row['date_received'])); ?></td>
                                                <?php if($produk['type'] == 'Handphone'): ?>
                                                    <td class="text-center">
                                                        <?php if($row['status'] == 'Tersedia'): ?>
                                                            <form action="<?= base_url('inventori/hapus_stok') ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus IMEI <?= esc($row['imei'], 'js'); ?> dari stok?')">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="product_id" value="<?= esc($produk['product_id'], 'attr'); ?>">
                                                                <input type="hidden" name="imei" value="<?= esc($row['imei'], 'attr'); ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus stok">
                                                                    <i class="bi bi-trash3-fill"></i>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="text-muted small">Terkunci</span>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="<?= $produk['type'] == 'Handphone' ? '6' : '5'; ?>" class="text-center py-4 text-muted">Belum ada riwayat stok yang ditambahkan.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.price-input').forEach(function(input) {
            input.addEventListener('input', function() {
                const warning = input.closest('.mb-4').querySelector('.price-warning');
                if (warning) warning.classList.toggle('d-none', Number(input.value) <= 50000000);
            });
        });
    </script>
</body>
</html>