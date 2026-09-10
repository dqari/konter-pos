<?php
$tokoNama = $toko['store_name'] ?? 'WM Cellular';
$tokoLogo = $toko['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retur Transaksi - <?= esc($tokoNama); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f5f7fb; color: #172033; }
        .navbar { background: #111a2d !important; }
        .card { border: 1px solid #e6ebf2; border-radius: 18px; box-shadow: 0 12px 32px rgba(23,32,51,.06); }
        .store-logo { width: 38px; height: 38px; padding: 4px; object-fit: contain; border-radius: 11px; background: #f4b740; }
        .table thead th { font-size: .72rem; letter-spacing: .08em; text-transform: uppercase; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="<?= base_url('dashboard'); ?>"><i class="bi bi-arrow-left-circle me-1"></i> Dashboard</a>
        <span class="navbar-text text-white fw-bold d-none d-md-block">
            <?php if ($tokoLogo): ?><img class="store-logo me-2 align-middle" src="<?= base_url('uploads/logo/' . $tokoLogo); ?>" alt="Logo <?= esc($tokoNama); ?>"><?php endif; ?>
            <i class="bi bi-arrow-return-left me-1"></i> <?= esc($tokoNama); ?> / RETUR TRANSAKSI
        </span>
        <span class="text-white"><i class="bi bi-person-circle me-1"></i><?= esc($username); ?></span>
    </div>
</nav>
<main class="container-fluid px-4 py-4" style="max-width: 1450px;">
    <?php if (session()->getFlashdata('pesan')): ?><div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('pesan')); ?></div><?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= esc(session()->getFlashdata('error')); ?></div><?php endif; ?>

    <div class="card p-4 mb-4">
        <div class="text-uppercase text-secondary small fw-bold" style="letter-spacing:.14em;">Kontrol transaksi</div>
        <h2 class="fw-bold mb-1">Retur & pembatalan</h2>
        <p class="text-secondary mb-0">Kembalikan stok tanpa menghapus histori penjualan. IMEI dikembalikan ke stok tersedia dan aksesori masuk sebagai batch baru.</p>
    </div>
    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Invoice</th><th>Tanggal</th><th>Kasir</th><th class="text-end">Total</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                <?php if ($transactions): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td class="fw-bold"><?= esc($transaction['invoice_number']); ?></td>
                            <td><?= date('d M Y H:i', strtotime($transaction['transaction_date'])); ?></td>
                            <td><?= esc($transaction['username'] ?? '-'); ?></td>
                            <td class="text-end">Rp <?= number_format($transaction['total_amount'], 0, ',', '.'); ?></td>
                            <td><span class="badge text-bg-success">Selesai</span></td>
                            <td class="text-end">
                                <form action="<?= base_url('retur/proses'); ?>" method="POST" class="d-flex justify-content-end gap-2">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="transaction_id" value="<?= esc($transaction['transaction_id'], 'attr'); ?>">
                                    <select name="final_status" class="form-select form-select-sm" style="max-width:135px" required><option value="returned">Diretur</option><option value="cancelled">Dibatalkan</option></select>
                                    <input type="text" name="reason" class="form-control form-control-sm" style="max-width:180px" placeholder="Alasan" required>
                                    <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Kembalikan stok dan ubah status transaksi ini?')"><i class="bi bi-arrow-return-left me-1"></i>Proses</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-secondary py-5">Tidak ada transaksi yang dapat diretur.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
