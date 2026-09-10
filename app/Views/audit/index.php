<?php
$tokoNama = $toko['store_name'] ?? 'WM Cellular';
$tokoLogo = $toko['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log - <?= esc($tokoNama); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>body{background:#f5f7fb;color:#172033}.navbar{background:#111a2d!important}.card{border:1px solid #e6ebf2;border-radius:18px;box-shadow:0 12px 32px rgba(23,32,51,.06)}.store-logo{width:38px;height:38px;padding:4px;object-fit:contain;border-radius:11px;background:#f4b740}.table thead th{font-size:.72rem;letter-spacing:.08em;text-transform:uppercase}.action-badge{font-size:.72rem;letter-spacing:.03em}</style>
</head>
<body>
<nav class="navbar navbar-dark shadow-sm"><div class="container-fluid px-4"><a class="navbar-brand fw-bold" href="<?= base_url('dashboard'); ?>"><i class="bi bi-arrow-left-circle me-1"></i> Dashboard</a><span class="navbar-text text-white fw-bold d-none d-md-block"><?php if($tokoLogo): ?><img class="store-logo me-2 align-middle" src="<?= base_url('uploads/logo/' . $tokoLogo); ?>" alt="Logo toko"><?php endif; ?><i class="bi bi-shield-check me-1"></i><?= esc($tokoNama); ?> / AUDIT LOG</span><span class="text-white"><i class="bi bi-person-circle me-1"></i><?= esc($username); ?></span></div></nav>
<main class="container-fluid px-4 py-4" style="max-width:1500px"><div class="card p-4 mb-4"><div class="text-secondary small fw-bold text-uppercase" style="letter-spacing:.14em">Jejak perubahan sistem</div><h2 class="fw-bold mb-1">Audit log</h2><p class="text-secondary mb-0">Riwayat aktivitas penting yang dilakukan pengguna di aplikasi.</p></div><div class="card p-4"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Referensi</th><th>Detail</th></tr></thead><tbody><?php if($logs): ?><?php foreach($logs as $log): ?><tr><td class="text-nowrap"><?= date('d M Y H:i:s', strtotime($log['created_at'])); ?></td><td class="fw-semibold"><?= esc($log['username'] ?? 'Sistem'); ?></td><td><span class="badge rounded-pill text-bg-light border action-badge"><?= esc($log['action']); ?></span></td><td><?= esc($log['reference_type'] ?? '-'); ?> #<?= esc($log['reference_id'] ?? '-'); ?></td><td><code class="small text-wrap"><?= esc($log['details'] ?? '-'); ?></code></td></tr><?php endforeach; ?><?php else: ?><tr><td colspan="5" class="text-center text-secondary py-5">Belum ada aktivitas tercatat.</td></tr><?php endif; ?></tbody></table></div></div></main>
</body>
</html>
