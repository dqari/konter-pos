<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Model - Konter POS</title>
    <link rel="stylesheet" href="https://cdn.metroui.org.ua/v4.3.2/css/metro-all.min.css">
</head>
<body style="background-color: #f3f3f3;">

    <!-- Navbar -->
    <div data-role="appbar" class="bg-green fg-white">
        <a href="<?= base_url('inventori') ?>" class="brand no-hover">
            <span class="mif-arrow-left"></span> Batal & Kembali
        </a>
        <span class="brand text-bold mx-auto">➕ TAMBAH MODEL HP</span>
    </div>

    <!-- Konten Form -->
    <div class="container" style="margin-top: 80px;">
        <div class="grid">
            <div class="row flex-justify-center">
                <div class="cell-md-8 cell-lg-6">
                    <div class="card p-6 mt-4">
                        <h4 class="mb-4">Input Data Model Handphone</h4>
                        <hr class="thin">
                        
                        <form action="<?= base_url('inventori/simpan') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="form-group mb-4">
                                <label>Merek Handphone</label>
                                <select name="brand" data-role="select" required>
                                    <option value="" disabled selected>-- Pilih Merek --</option>
                                    <option value="Samsung">Samsung</option>
                                    <option value="Apple">Apple (iPhone)</option>
                                    <option value="Xiaomi">Xiaomi</option>
                                    <option value="Oppo">Oppo</option>
                                    <option value="Vivo">Vivo</option>
                                    <option value="Realme">Realme</option>
									<option value="Infinix">Infinix</option> 
									<option value="Infinix">Iqoo</option>
									<option value="Infinix">Itel</option>
	 								<option value="Lainnya">Lainnya...</option>
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label>Nama Model & Spesifikasi</label>
                                <input type="text" name="model_name" data-role="input" placeholder="Contoh: Galaxy S24 Ultra 256GB" required>
                            </div>

                            <div class="form-group mb-4">
                                <label>Harga Jual Standar (Rp)</label>
                                <input type="number" name="base_selling_price" data-role="input" placeholder="Contoh: 15000000" min="0" max="50000000" required>
                                <small class="text-muted">Isi angka saja tanpa titik (Contoh: 2500000).</small>
                            </div>

                            <div class="form-group mt-6">
                                <button type="submit" class="button success block"><span class="mif-floppy-disk"></span> Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.metroui.org.ua/v4.3.2/js/metro.min.js"></script>
</body>
</html>