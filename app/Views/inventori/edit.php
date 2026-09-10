<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Model - Konter POS</title>
    <link rel="stylesheet" href="https://cdn.metroui.org.ua/v4.3.2/css/metro-all.min.css">
</head>
<body style="background-color: #f3f3f3;">
    <div data-role="appbar" class="bg-orange fg-white">
        <a href="<?= base_url('inventori') ?>" class="brand no-hover">
            <span class="mif-arrow-left"></span> Batal & Kembali
        </a>
        <span class="brand text-bold mx-auto">✏️ EDIT MODEL HP</span>
    </div>

    <div class="container" style="margin-top: 80px;">
        <div class="grid">
            <div class="row flex-justify-center">
                <div class="cell-md-8 cell-lg-6">
                    <div class="card p-6 mt-4 border-top bd-orange border-size-4">
                        <h4 class="mb-4">Perbarui Data Spesifikasi</h4>
                        <hr class="thin">

                        <form action="<?= base_url('inventori/update') ?>" method="POST">
                            <?= csrf_field() ?>
                            <!-- Input tersembunyi untuk membawa ID -->
                            <input type="hidden" name="product_id" value="<?= $produk['product_id']; ?>">

                            <div class="form-group mb-4">
                                <label>Merek Handphone</label>
                                <input type="text" name="brand" data-role="input" value="<?= esc($produk['brand']); ?>" required>
                            </div>

                            <div class="form-group mb-4">
                                <label>Nama Model & Spesifikasi</label>
                                <input type="text" name="model_name" data-role="input" value="<?= esc($produk['model_name']); ?>" required>
                            </div>

                            <div class="form-group mb-4">
                                <label>Harga Jual Standar (Rp)</label>
                                <input type="number" name="base_selling_price" data-role="input" value="<?= esc($produk['base_selling_price']); ?>" min="0" max="50000000" required>
                            </div>

                            <div class="form-group mt-6">
                                <button type="submit" class="button warning block fg-white"><span class="mif-floppy-disk"></span> Perbarui Data</button>
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