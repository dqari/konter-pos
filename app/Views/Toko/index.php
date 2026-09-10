<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Toko - Konter POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; border: none; }
        .logo-preview { max-width: 150px; max-height: 150px; object-fit: contain; background: white; padding: 10px; border-radius: 10px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="<?= base_url('dashboard') ?>">
                <i class="bi bi-arrow-left-circle me-1"></i> Dashboard
            </a>
            <span class="navbar-text text-dark fw-bold mx-auto d-none d-md-block">
                <i class="bi bi-shop me-1"></i> IDENTITAS & PROFIL TOKO
            </span>
        </div>
    </nav>

    <div class="container py-5">
        <?php if(session()->getFlashdata('sukses')): ?>
            <div class="alert alert-success shadow-sm border-0 border-start border-5 border-success">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('sukses') ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
           <!-- Kolom Kiri: Preview Identitas -->
         <div class="col-12 col-md-4">
             <div class="card shadow-sm h-100 bg-primary text-white text-center p-4">

                 <!-- LOGIKA MENAMPILKAN LOGO ATAU IKON -->
                 <div class="mb-3">
                     <?php if(!empty($toko['logo'])): ?>
                         <!-- Menampilkan Gambar Logo -->
                         <img src="<?= base_url('uploads/logo/' . $toko['logo']) ?>" class="shadow-sm bg-white p-2 rounded-3" style="max-width: 150px; max-height: 150px; object-fit: contain;" alt="Logo Toko">
                     <?php else: ?>
                         <!-- Menampilkan Ikon Rumah Toko jika Logo belum ada -->
                         <i class="bi bi-shop" style="font-size: 5rem;"></i>
                     <?php endif; ?>
                 </div>

                 <h3 class="fw-bold"><?= esc($toko['store_name']); ?></h3>
                    <hr class="border-white opacity-50">
                    <p class="mb-1"><i class="bi bi-geo-alt-fill me-2"></i><?= esc($toko['address']); ?></p>
                    <p class="mb-1"><i class="bi bi-telephone-fill me-2"></i><?= esc($toko['phone']); ?></p>
                    <p class="mb-1"><i class="bi bi-envelope-fill me-2"></i><?= esc($toko['email']); ?></p>
                    <hr class="border-white opacity-50">
                    <small class="opacity-75">Teks Bawah Nota:<br><i>"<?= esc($toko['footer_nota']); ?>"</i></small>
                </div>
            </div>

            <!-- Kolom Kanan: Form Edit -->
            <div class="col-12 col-md-8">
                <div class="card shadow-sm h-100 p-4">
                    <h4 class="fw-bold mb-4"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Informasi Toko</h4>
                    
                    <!-- WAJIB ADA enctype="multipart/form-data" AGAR BISA UPLOAD GAMBAR -->
                    <form action="<?= base_url('toko/update') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $toko['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Nama Toko / Perusahaan</label>
                                <input type="text" name="store_name" class="form-control bg-light" value="<?= esc($toko['store_name']); ?>" required>
                            </div>
                            
                            <!-- INPUT UNTUK LOGO -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-primary">Upload Logo Baru</label>
                                <input type="file" name="logo" class="form-control border-primary bg-light" accept="image/png, image/jpeg, image/jpg">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah logo.</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">No. Telp / WhatsApp</label>
                                <input type="text" name="phone" class="form-control bg-light" value="<?= esc($toko['phone']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Toko</label>
                                <input type="email" name="email" class="form-control bg-light" value="<?= esc($toko['email']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat Lengkap</label>
                            <textarea name="address" class="form-control bg-light" rows="3" required><?= esc($toko['address']); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pesan di Bawah Nota (Footer)</label>
                            <input type="text" name="footer_nota" class="form-control bg-light" value="<?= esc($toko['footer_nota']); ?>">
                        </div>
                        
                        <button type="submit" class="btn btn-warning btn-lg fw-bold w-100 shadow-sm text-dark">
                            <i class="bi bi-floppy me-2"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>