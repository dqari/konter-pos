<?php
$tokoNama = $toko['store_name'] ?? 'WM Cellular';
$tokoLogo = $toko['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Konter POS</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --ink: #172033; --muted: #718096; --paper: #f5f7fb; --amber: #f4b740; --cyan: #39b8c8; --navy: #111a2d; }
        body { background: var(--paper); color: var(--ink); font-family: 'DM Sans', sans-serif; }
        .navbar { background: var(--navy) !important; }
        .navbar-brand, h1, h2, h3, h4, h5 { font-family: 'Space Grotesk', sans-serif; }
        .dashboard-shell { max-width: 1320px; }
        .hero-panel { background: linear-gradient(135deg, #111a2d 0%, #253552 100%); border-radius: 22px; position: relative; overflow: hidden; }
        .hero-panel:after { content: ''; position: absolute; width: 240px; height: 240px; right: -70px; bottom: -120px; border: 34px solid rgba(244,183,64,.18); border-radius: 50%; }
        .hero-label { color: var(--amber); font-size: .72rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
        .brand-logo { width: 50px; height: 50px; padding: 7px; object-fit: contain; border-radius: 15px; background: var(--amber); }
        .hero-brand { display: flex; align-items: center; gap: 18px; }
        .hero-title { margin: 2px 0 0; font-size: clamp(2rem, 4vw, 3.35rem); line-height: 1; letter-spacing: -.04em; }
        .hero-copy { margin-left: 68px; }
        .menu-card { transition: transform .2s ease, box-shadow .2s ease; cursor: pointer; border: 0; border-radius: 18px; overflow: hidden; }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 18px 32px rgba(23,32,51,.14) !important; }
        .menu-card .card-body { min-height: 190px; display: flex; flex-direction: column; justify-content: space-between; }
        .menu-card.bg-info { background: #e5f7f8 !important; color: var(--ink) !important; }
        .menu-card.bg-success { background: #e8f6ef !important; color: var(--ink) !important; }
        .menu-card.bg-danger { background: #fff0ec !important; color: var(--ink) !important; }
        .menu-card.bg-dark { background: #e9edf5 !important; color: var(--ink) !important; }
        .menu-card.bg-warning { background: #fff7df !important; color: var(--ink) !important; }
        .menu-card .icon-box { color: var(--ink); font-size: 2.8rem; margin-bottom: 18px; }
        .menu-card .card-text { color: var(--muted) !important; }
        .module-arrow { color: var(--muted); font-size: 1.2rem; }
        .menu-section-label { margin-top: 12px; padding-top: 22px; border-top: 1px solid #dfe6ef; }
        .menu-section-label span { color: var(--muted); font-size: .72rem; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; }
        @media (max-width: 575px) { .hero-brand { align-items: flex-start; } .hero-copy { margin-left: 0; } .hero-panel .lead { margin-left: 0 !important; padding-left: 0 !important; border-left: 0 !important; } }
    </style>
</head>
<body>

    <!-- Navbar Atas -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">

            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-phone-fill"></i> KONTER POS
            </a>
            <!-- Tombol Hamburger (Muncul saat di layar HP) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5"></i> 
                            <span class="ms-1"><?= esc($username); ?> (<span class="fw-bold"><?= esc($role); ?></span>)</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li>
                                <form action="<?= base_url('logout') ?>" method="POST" class="m-0">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item text-danger fw-bold">
                                        <i class="bi bi-box-arrow-right"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="container dashboard-shell py-5">
        <!-- ==========================================
             BRANDING HERO BANNER - WM CELULLAR
             ========================================== -->
        <div class="hero-panel shadow-sm mb-5">
            <div class="card-body p-4 p-md-5 text-white d-flex align-items-center justify-content-between position-relative">
                <!-- Ornamen Latar Belakang (Watermark) -->
                <i class="bi bi-shop position-absolute text-white" style="font-size: 15rem; right: -20px; top: -50px; opacity: 0.05; transform: rotate(-10deg);"></i>
                
                <div class="position-relative z-1">
                    <div class="hero-brand">
                        <?php if ($tokoLogo): ?><img class="brand-logo me-3 shadow" src="<?= base_url('uploads/logo/' . $tokoLogo); ?>" alt="Logo <?= esc($tokoNama); ?>"><?php else: ?><div class="bg-warning text-dark rounded-circle d-flex justify-content-center align-items-center me-3 shadow" style="width: 50px; height: 50px;"><i class="bi bi-phone-vibrate fs-3"></i></div><?php endif; ?>
                        <div><div class="hero-label mb-1">Point of sale & inventory</div><h1 class="hero-title fw-bolder"><?= esc($tokoNama); ?></h1></div>
                    </div>
                    <p class="hero-copy lead mb-0 text-white-50 ps-3 border-start border-warning border-3">
                        <i class="bi bi-geo-alt-fill me-1"></i> ITC Roxy Mas | Sistem Manajemen POS Terpadu
                    </p>
                </div>
            </div>
        </div>
        <!-- ================= END BRANDING ================= -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="hero-label mb-2">Workspace hari ini</div>
                <h2 class="fw-bold text-dark mb-1">Halo, <?= esc($username); ?></h2>
                <p class="text-secondary fs-5">Pilih alur kerja yang ingin dibuka.</p>
            </div>
        </div>

        <!-- Grid Menu -->
        <div class="row g-4">
            
            <!-- Modul POS (Tampil untuk Semua: Kasir, Manager, Owner) -->
            <?php if($role == 'Kasir' || $role == 'Manager' || $role == 'Owner'): ?>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card menu-card bg-info text-white shadow h-100" onclick="window.location.href='<?= base_url('kasir') ?>'">
                    <div class="card-body text-center p-4">
                        <div class="icon-box"><i class="bi bi-cart4"></i></div>
                        <h5 class="card-title fw-bold">Modul POS</h5>
                        <p class="card-text small">Sistem kasir & penjualan</p><i class="bi bi-arrow-up-right module-arrow"></i>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Master Inventori (Tampil untuk Manager & Owner) -->
            <?php if($role == 'Manager' || $role == 'Owner'): ?>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card menu-card bg-success text-white shadow h-100" onclick="window.location.href='<?= base_url('inventori') ?>'">
                    <div class="card-body text-center p-4">
                        <div class="icon-box"><i class="bi bi-box-seam"></i></div>
                        <h5 class="card-title fw-bold">Master Inventori</h5>
                        <p class="card-text small">Stok handphone & harga</p><i class="bi bi-arrow-up-right module-arrow"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card menu-card bg-warning text-dark shadow h-100" onclick="window.location.href='<?= base_url('retur') ?>'">
                    <div class="card-body p-4"><div><div class="icon-box"><i class="bi bi-arrow-return-left"></i></div><h5 class="card-title fw-bold">Retur Transaksi</h5><p class="card-text small">Kembalikan stok dan histori tetap aman</p></div><i class="bi bi-arrow-up-right module-arrow"></i></div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card menu-card bg-success text-white shadow h-100" onclick="window.location.href='<?= base_url('opname') ?>'">
                    <div class="card-body p-4"><div><div class="icon-box"><i class="bi bi-clipboard-check"></i></div><h5 class="card-title fw-bold">Stock Opname</h5><p class="card-text small">Cocokkan stok fisik dan sistem</p></div><i class="bi bi-arrow-up-right module-arrow"></i></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Laporan Keuangan (Tampil HANYA untuk Owner) -->
            <?php if($role == 'Owner'): ?>
            <div class="col-12 menu-section-label"><span>Owner workspace</span></div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card menu-card bg-danger text-white shadow h-100" onclick="window.location.href='<?= base_url('laporan/keuangan') ?>'">
                    <div class="card-body text-center p-4">
                        <div class="icon-box"><i class="bi bi-cash-coin"></i></div>
                        <h5 class="card-title fw-bold">Laporan Keuangan</h5>
                        <p class="card-text small">Omzet, modal & laba bersih</p><i class="bi bi-arrow-up-right module-arrow"></i>
                    </div>
                </div>
            </div>

            <!-- Manajemen User (Tampil HANYA untuk Owner) -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card menu-card bg-dark text-white shadow h-100" onclick="window.location.href='<?= base_url('usermanajemen') ?>'">
                    <div class="card-body text-center p-4">
                        <div class="icon-box"><i class="bi bi-people-fill"></i></div>
                        <h5 class="card-title fw-bold">Manajemen User</h5>
                        <p class="card-text small">Atur akun karyawan</p><i class="bi bi-arrow-up-right module-arrow"></i>
                    </div>
                </div>
            </div>
			<div class="col-12 col-md-6 col-lg-3">
             <div class="card menu-card bg-warning text-dark shadow h-100" onclick="window.location.href='<?= base_url('toko') ?>'">
                 <div class="card-body text-center p-4">
                     <div class="icon-box"><i class="bi bi-shop"></i></div>
                     <h5 class="card-title fw-bold">Profil Toko</h5>
                     <p class="card-text small">Identitas & kontak konter</p><i class="bi bi-arrow-up-right module-arrow"></i>
                 </div>
             </div>
         </div>
         <div class="col-12 col-md-6 col-lg-3">
             <div class="card menu-card bg-dark text-white shadow h-100" onclick="window.location.href='<?= base_url('backup') ?>'">
                 <div class="card-body p-4"><div><div class="icon-box"><i class="bi bi-database-check"></i></div><h5 class="card-title fw-bold">Backup Data</h5><p class="card-text small">Lindungi database operasional toko</p></div><i class="bi bi-arrow-up-right module-arrow"></i></div>
             </div>
         </div>
         <div class="col-12 col-md-6 col-lg-3">
             <div class="card menu-card bg-info text-white shadow h-100" onclick="window.location.href='<?= base_url('audit') ?>'">
                 <div class="card-body p-4"><div><div class="icon-box"><i class="bi bi-shield-check"></i></div><h5 class="card-title fw-bold">Audit Aktivitas</h5><p class="card-text small">Lihat histori perubahan sistem</p></div><i class="bi bi-arrow-up-right module-arrow"></i></div>
             </div>
         </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Script Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>