<?php
$tokoNama = $toko['store_name'] ?? 'WM Cellular';
$tokoLogo = $toko['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Konter POS (Bootstrap)</title>
    
    <!-- Link CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --ink: #172033; --amber: #f4b740; --cyan: #39b8c8; --navy: #111a2d; }
        body { background: #f5f7fb; min-height: 100vh; font-family: 'DM Sans', sans-serif; overflow: hidden; }
        .login-stage { width: min(1080px, 94vw); min-height: 640px; display: grid; grid-template-columns: 1.05fr .95fr; background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 24px 70px rgba(17, 26, 45, .16); }
        .brand-side { background: linear-gradient(145deg, #111a2d, #263653); color: #fff; padding: clamp(2rem, 5vw, 4.5rem); position: relative; overflow: hidden; }
        .brand-side:after { content: ''; position: absolute; width: 330px; height: 330px; right: -120px; bottom: -150px; border: 42px solid rgba(244,183,64,.2); border-radius: 50%; }
        .brand-side:before { content: ''; position: absolute; width: 180px; height: 180px; left: -90px; top: -90px; border: 24px solid rgba(57,184,200,.16); border-radius: 50%; }
        .brand-content { position: relative; z-index: 1; }
        .brand-mark { width: 58px; height: 58px; display: grid; place-items: center; border-radius: 16px; background: var(--amber); color: var(--ink); font-size: 1.8rem; }
        .brand-mark img { width: 100%; height: 100%; padding: 6px; object-fit: contain; border-radius: 12px; }
        .brand-side h1, .login-card h2 { font-family: 'Space Grotesk', sans-serif; }
        .brand-eyebrow { color: var(--amber); font-size: .72rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
        .login-card { padding: clamp(2rem, 5vw, 4rem); display: flex; flex-direction: column; justify-content: center; }
        .login-card .form-control { border: 1px solid #dce4ef; border-radius: 10px; padding: .8rem 1rem; }
        .login-card .form-control:focus { border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(57,184,200,.14); }
        .login-button { background: var(--ink); border: 0; border-radius: 10px; padding: .85rem; }
        .login-button:hover { background: #263653; }
        @media (max-width: 767px) { body { overflow: auto; } .login-stage { display: block; min-height: auto; } .brand-side { padding: 2rem; min-height: 250px; } .login-card { padding: 2rem; } }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center">

    <div class="login-stage">
        <section class="brand-side">
            <div class="brand-content">
                <div class="brand-mark mb-5"><?php if ($tokoLogo): ?><img src="<?= base_url('uploads/logo/' . $tokoLogo); ?>" alt="Logo <?= esc($tokoNama); ?>"><?php else: ?><i class="bi bi-phone-vibrate"></i><?php endif; ?></div>
                <div class="brand-eyebrow mb-3"><?= esc($tokoNama); ?> / Point of sale</div>
                <h1 class="display-4 fw-bold mb-3">Jual lebih cepat.<br>Catat lebih rapi.</h1>
                <p class="text-white-50 fs-5 mb-0">Satu ruang kerja untuk kasir, stok IMEI, aksesori, dan laporan toko.</p>
            </div>
        </section>
        <section class="login-card">
            <div class="mb-4">
                <div class="brand-eyebrow mb-2" style="color: var(--cyan);">Akses sistem</div>
                <h2 class="fw-bold mb-2">Selamat datang kembali</h2>
                <p class="text-muted mb-0">Masuk untuk membuka meja kasir dan operasional toko.</p>
            </div>
            
            <?php if(session()->getFlashdata('msg')):?>
                <div class="alert alert-danger text-center" role="alert">
                    <?= session()->getFlashdata('msg') ?>
                </div>
            <?php endif;?>

            <form action="<?= base_url('auth/process') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" name="username" class="form-control form-control-lg bg-light" placeholder="Masukkan Username" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg bg-light" placeholder="Masukkan Password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn login-button btn-lg text-white fw-bold"><i class="bi bi-arrow-right-circle me-2"></i>Masuk ke workspace</button>
                </div>
            </form>
            <p class="small text-muted mt-4 mb-0"><i class="bi bi-shield-check text-success me-1"></i>Akses terlindungi untuk tim toko.</p>
        </section>
    </div>

    <!-- Script JS Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>