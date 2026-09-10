<?php
$tokoNama = $toko['store_name'] ?? 'WM Cellular';
$tokoLogo = $toko['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Inventori V2 - Konter POS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --ink: #172033; --muted: #718096; --line: #e6ebf2; --paper: #f5f7fb; --amber: #f4b740; --cyan: #39b8c8; --navy: #111a2d; }
        body { background: var(--paper); color: var(--ink); font-family: 'DM Sans', sans-serif; }
        .navbar { background: var(--navy) !important; }
        .navbar-brand, h1, h2, h3, h4, h5 { font-family: 'Space Grotesk', sans-serif; }
        .inventory-shell { max-width: 1460px; }
        .page-kicker { color: var(--muted); font-size: .72rem; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; }
        .store-logo { width: 38px; height: 38px; padding: 4px; object-fit: contain; border-radius: 11px; background: var(--amber); }
        .card { border-radius: 18px; border: 1px solid var(--line); box-shadow: 0 12px 32px rgba(23, 32, 51, .06) !important; }
        .inventory-head { background: #fff; border-left: 4px solid var(--cyan); border-radius: 16px; }
        .table thead th { font-size: .7rem; letter-spacing: .08em; text-transform: uppercase; }
        .table tbody tr { border-color: var(--line); }
        .table tbody tr:hover { background: #fbfcfe; }
        .dataTables_wrapper .dataTables_filter input, .dataTables_wrapper .dataTables_length select { border: 1px solid var(--line); border-radius: 8px; padding: .4rem .6rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('dashboard') ?>">
                <i class="bi bi-arrow-left-circle me-1"></i> Kembali
            </a>
            <span class="navbar-text text-white fw-bold mx-auto d-none d-md-block">
                <?php if ($tokoLogo): ?><img class="store-logo me-2 align-middle" src="<?= base_url('uploads/logo/' . $tokoLogo); ?>" alt="Logo <?= esc($tokoNama); ?>"><?php endif; ?><i class="bi bi-box-seam"></i> <?= esc($tokoNama); ?> / INVENTORI
            </span>
            <div class="d-flex text-white align-items-center">
                <i class="bi bi-person-circle fs-5 me-2"></i> <?= esc($username); ?>
            </div>
        </div>
    </nav>

    <div class="container inventory-shell py-4">
        
        <div class="inventory-head d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3 p-4">
            <div><div class="page-kicker mb-1">Kontrol persediaan</div><h3 class="fw-bold mb-1 text-dark">Produk & stok</h3><p class="text-secondary mb-0">Pantau harga jual, IMEI, barcode, dan ketersediaan barang.</p></div>
            
            <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahModelModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Produk Baru
            </button>
        </div>

        <!-- NOTIFIKASI -->
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

        <!-- TABEL DATA -->
        <div class="card p-4">
            <div class="table-responsive">
                <table id="tabelInventori" class="table table-striped table-hover table-bordered align-middle mb-0" style="width:100%">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" width="5%">ID</th>
                            <th width="10%">Tipe</th>
                            <th width="12%">Merek</th>
                            <th>Nama Produk</th>
                            <th width="15%">Barcode</th>
                            <th width="12%">Harga Jual</th>
                            <th class="text-center" width="10%">Stok</th>
                            <th class="text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($produk) && is_array($produk)): ?>
                            <?php foreach($produk as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $row['product_id']; ?></td>
                                    <td>
                                        <!-- Penanda Warna Tipe Barang -->
                                        <?php if($row['type'] == 'Handphone'): ?>
                                            <span class="badge bg-primary w-100">Handphone</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark w-100">Aksesoris</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-secondary text-uppercase"><?= esc($row['brand']); ?></span></td>
                                    <td class="fw-bold"><?= esc($row['model_name']); ?></td>
                                    <td><code class="text-dark fw-bold"><?= $row['barcode'] ? esc($row['barcode']) : '-'; ?></code></td>
                                    <td>Rp <?= number_format($row['base_selling_price'], 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <?php if($row['stok_tersedia'] > 0): ?>
                                            <span class="badge bg-success rounded-pill px-3"><?= $row['stok_tersedia']; ?> Unit</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger rounded-pill px-3">Habis</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <!-- Tombol Kelola Stok -->
                                            <button class="btn btn-sm btn-info text-white" title="Kelola Stok" onclick="window.location.href='<?= base_url('inventori/stok/'.$row['product_id']) ?>'">
                                                <i class="bi bi-box-arrow-in-right"></i> Stok
                                            </button>
                                            
                                            <!-- Tombol Edit -->
                                            <button type="button" class="btn btn-sm btn-warning text-dark" title="Edit Data" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['product_id']; ?>">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==========================================
         AREA MODAL (POP-UP)
         ========================================== -->

    <!-- 1. MODAL TAMBAH PRODUK BARU -->
    <div class="modal fade" id="tambahModelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Produk Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <form action="<?= base_url('inventori/simpan') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="modal-body p-4">
                        
                        <!-- PILIHAN TIPE PRODUK -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori Produk</label>
                            <select name="type" id="pilihTipe" class="form-select bg-light border-primary" required onchange="toggleBarcode()">
                                <option value="Handphone" selected>📱 Handphone (Sistem IMEI)</option>
                                <option value="Aksesoris">🎧 Aksesoris (Sistem Barcode / QTY)</option>
                            </select>
                        </div>

                        <!-- KOLOM BARCODE (Disembunyikan secara default menggunakan Javascript) -->
                        <div class="mb-3" id="formBarcode" style="display: none;">
                            <label class="form-label fw-bold text-danger">Scan Barcode Aksesoris</label>
                            <div class="input-group">
                                <span class="input-group-text bg-warning text-dark"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" name="barcode" id="inputBarcode" class="form-control border-warning bg-light" placeholder="Arahkan scanner ke kotak..." autocomplete="off">
                            </div>
                            <small class="text-muted">Wajib diisi jika memilih kategori Aksesoris.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Merek</label>
                            <input type="text" name="brand" class="form-control bg-light" placeholder="Contoh: Samsung, Robot, Vivan" required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Produk & Spesifikasi</label>
                            <input type="text" name="model_name" class="form-control bg-light" placeholder="Contoh: Kabel Data Type-C 2A" required autocomplete="off">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Harga Jual Standar (Rp)</label>
                            <input type="number" name="base_selling_price" class="form-control bg-light price-input" placeholder="Contoh: 35000" min="0" max="50000000" required>
                            <div class="form-text price-warning text-danger d-none">Periksa kembali nominal harga.</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-floppy me-1"></i> Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. MODAL EDIT (Looping) -->
    <?php if(!empty($produk) && is_array($produk)): ?>
        <?php foreach($produk as $row): ?>
            <div class="modal fade" id="editModal<?= $row['product_id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-warning text-dark border-0">
                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Master Produk</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="<?= base_url('inventori/update') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="modal-body p-4">
                                <input type="hidden" name="product_id" value="<?= $row['product_id']; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Merek</label>
                                    <input type="text" name="brand" class="form-control bg-light" value="<?= esc($row['brand']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Produk</label>
                                    <input type="text" name="model_name" class="form-control bg-light" value="<?= esc($row['model_name']); ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Harga Jual Standar (Rp)</label>
                                    <input type="number" name="base_selling_price" class="form-control bg-light price-input" value="<?= esc($row['base_selling_price']); ?>" min="0" max="50000000" required>
                                    <div class="form-text price-warning text-danger d-none">Periksa kembali nominal harga.</div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning fw-bold text-dark"><i class="bi bi-floppy me-1"></i> Perbarui</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#tabelInventori').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
            });
        });

        // JAVASCRIPT: Menampilkan/Menyembunyikan Kolom Barcode berdasarkan Pilihan Kategori
        function toggleBarcode() {
            var tipe = document.getElementById('pilihTipe').value;
            var formBarcode = document.getElementById('formBarcode');
            var inputBarcode = document.getElementById('inputBarcode');
            
            if (tipe === 'Aksesoris') {
                formBarcode.style.display = 'block'; // Tampilkan input barcode
                inputBarcode.required = true;        // Jadikan wajib diisi
                inputBarcode.focus();                // Pindahkan kursor ke dalam kotak
            } else {
                formBarcode.style.display = 'none';  // Sembunyikan
                inputBarcode.required = false;       // Jadikan opsional
                inputBarcode.value = '';             // Bersihkan isinya
            }
        }

        document.querySelectorAll('.price-input').forEach(function(input) {
            input.addEventListener('input', function() {
                const warning = input.parentElement.querySelector('.price-warning');
                if (warning) warning.classList.toggle('d-none', Number(input.value) <= 50000000);
            });
        });
    </script>
</body>
</html>