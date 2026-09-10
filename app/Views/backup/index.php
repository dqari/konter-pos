<?php
$tokoNama = $toko['store_name'] ?? 'WM Cellular';
$tokoLogo = $toko['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Database - <?= esc($tokoNama); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f5f7fb; color: #172033; }
        .navbar { background: #111a2d !important; }
        .card { border: 1px solid #e6ebf2; border-radius: 18px; box-shadow: 0 12px 32px rgba(23,32,51,.06); }
        .store-logo { width: 38px; height: 38px; padding: 4px; object-fit: contain; border-radius: 11px; background: #f4b740; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark shadow-sm"><div class="container-fluid px-4">
    <a class="navbar-brand fw-bold" href="<?= base_url('dashboard'); ?>"><i class="bi bi-arrow-left-circle me-1"></i> Dashboard</a>
    <span class="navbar-text text-white fw-bold d-none d-md-block"><?php if ($tokoLogo): ?><img class="store-logo me-2 align-middle" src="<?= base_url('uploads/logo/' . $tokoLogo); ?>" alt="Logo toko"><?php endif; ?><i class="bi bi-database-check me-1"></i><?= esc($tokoNama); ?> / BACKUP</span>
    <span class="text-white"><i class="bi bi-person-circle me-1"></i><?= esc($username); ?></span>
</div></nav>
<main class="container py-4" style="max-width:1100px;">
    <?php if (session()->getFlashdata('pesan')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('pesan')); ?></div><?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div><?php endif; ?>
    <div class="card p-4 mb-4"><div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center"><div><div class="text-secondary small fw-bold text-uppercase" style="letter-spacing:.14em;">Perlindungan data</div><h2 class="fw-bold mb-1">Backup database</h2><p class="text-secondary mb-0">Simpan salinan database sebelum perubahan besar atau secara harian.</p></div><form action="<?= base_url('backup/create'); ?>" method="POST"><?= csrf_field(); ?><button class="btn btn-dark fw-bold"><i class="bi bi-cloud-arrow-down me-1"></i>Buat backup sekarang</button></form></div></div>
    <div class="card p-4"><h5 class="fw-bold mb-3"><i class="bi bi-clock-history text-primary me-2"></i>File backup tersimpan</h5><div class="table-responsive"><table class="table align-middle"><thead class="table-light"><tr><th>Nama file</th><th>Dibuat</th><th>Ukuran</th><th class="text-end">Aksi</th></tr></thead><tbody><?php if ($backups): ?><?php foreach ($backups as $backup): ?><tr><td class="fw-semibold"><i class="bi bi-file-earmark-code text-secondary me-2"></i><?= esc($backup['name']); ?></td><td><?= date('d M Y H:i', $backup['date']); ?></td><td><?= number_format($backup['size'] / 1024, 1, ',', '.'); ?> KB</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= base_url('backup/download/' . rawurlencode($backup['name'])); ?>"><i class="bi bi-download me-1"></i>Unduh</a></td></tr><?php endforeach; ?><?php else: ?><tr><td colspan="4" class="text-center text-secondary py-5">Belum ada backup. Buat backup pertama sekarang.</td></tr><?php endif; ?></tbody></table></div></div>
</main>
</body>
</html>
