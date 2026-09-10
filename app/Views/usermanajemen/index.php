<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Konter POS</title>
    
    <!-- Bootstrap 5 & DataTables CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; border: none; }
        /* Warna Indigo kustom ala Dashboard sebelumnya */
        .bg-indigo { background-color: #6610f2 !important; color: white; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-indigo shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('dashboard') ?>">
                <i class="bi bi-arrow-left-circle me-1"></i> Dashboard
            </a>
            <span class="navbar-text text-white fw-bold mx-auto d-none d-md-block">
                <i class="bi bi-people-fill me-1"></i> MANAJEMEN USER (OWNER)
            </span>
            <div class="d-flex text-white align-items-center">
                <i class="bi bi-person-circle fs-5 me-2"></i> <?= esc($username); ?>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="container py-4">
        
        <!-- Header & Tombol Tambah Modal -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <h3 class="fw-bold mb-0 text-dark">Daftar Pengguna Sistem</h3>
            <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah User Baru
            </button>
        </div>

        <!-- Notifikasi -->
        <?php if(session()->getFlashdata('sukses')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-5 border-success" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('sukses') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-5 border-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Tabel User -->
        <div class="card shadow-sm p-4 border-top border-5 border-primary">
            <div class="table-responsive">
                <table id="tabelUser" class="table table-striped table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-dark">
                        <tr>
                            <th width="10%">ID</th>
                            <th>Username</th>
                            <th>Role (Akses)</th>
                            <th>Tgl Terdaftar</th>
                            <th class="text-center" width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                            <tr>
                                <td><?= $u['user_id']; ?></td>
                                <td class="fw-bold text-primary">
                                    <i class="bi bi-person-badge me-1 text-muted"></i> <?= esc($u['username']); ?>
                                </td>
                                <td>
                                    <?php if($u['role'] == 'Owner'): ?>
                                        <span class="badge bg-danger rounded-pill px-3"><i class="bi bi-key-fill me-1"></i>Owner</span>
                                    <?php elseif($u['role'] == 'Manager'): ?>
                                        <span class="badge bg-success rounded-pill px-3">Manager</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark rounded-pill px-3">Kasir</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d-m-Y', strtotime($u['created_at'])); ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <!-- Tombol Panggil Modal Edit -->
                                        <button type="button" class="btn btn-sm btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#editModal<?= $u['user_id']; ?>" title="Edit User">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        
                                        <!-- Tombol Hapus (Kecuali Akun Sendiri) -->
                                        <?php if($u['user_id'] != session()->get('user_id')): ?>
                                            <form action="<?= base_url('usermanajemen/hapus/'.$u['user_id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user <?= esc($u['username']); ?>? Akun yang dihapus tidak bisa dikembalikan.')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold" title="Hapus User">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled title="Anda tidak bisa menghapus akun Anda sendiri"><i class="bi bi-shield-lock-fill"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==========================================
         AREA MODAL (POP-UP)
         ========================================== -->

    <!-- 1. MODAL TAMBAH USER -->
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold" id="tambahModalLabel"><i class="bi bi-person-plus-fill me-2"></i>Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('usermanajemen/simpan') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" name="username" class="form-control bg-light" placeholder="Ketik username baru..." required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control bg-light" placeholder="Ketik password..." required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jabatan (Role)</label>
                            <select name="role" class="form-select bg-light" required>
                                <option value="" disabled selected>-- Pilih Hak Akses --</option>
                                <option value="Kasir">Kasir (Hanya akses Modul POS)</option>
                                <option value="Manager">Manager (Bisa input Stok & Kasir)</option>
                                <option value="Owner">Owner (Akses Penuh)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-floppy me-1"></i> Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. MODAL EDIT USER (Di-looping sebanyak jumlah user) -->
    <?php foreach($users as $u): ?>
        <div class="modal fade" id="editModal<?= $u['user_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $u['user_id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning text-dark border-0">
                        <h5 class="modal-title fw-bold" id="editModalLabel<?= $u['user_id']; ?>"><i class="bi bi-pencil-square me-2"></i>Edit Pengguna</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= base_url('usermanajemen/update') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="modal-body p-4">
                            <input type="hidden" name="user_id" value="<?= $u['user_id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" name="username" class="form-control bg-light" value="<?= esc($u['username']); ?>" required autocomplete="off">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Password Baru</label>
                                <input type="password" name="password" class="form-control bg-light border-warning" placeholder="Ketik jika ingin mengubah password..." autocomplete="off">
                                <div class="form-text text-danger"><i class="bi bi-info-circle me-1"></i>Kosongkan jika tidak ingin mengubah password lama!</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jabatan (Role)</label>
                                <!-- Keamanan: Jika yang diedit adalah dirinya sendiri (Owner), maka disable ubah role agar tidak sengaja turun jabatan -->
                                <?php if($u['user_id'] == session()->get('user_id')): ?>
                                    <input type="hidden" name="role" value="Owner">
                                    <input type="text" class="form-control bg-light text-muted" value="Owner (Akun Anda Sendiri)" readonly>
                                <?php else: ?>
                                    <select name="role" class="form-select bg-light" required>
                                        <option value="Kasir" <?= $u['role'] == 'Kasir' ? 'selected' : ''; ?>>Kasir</option>
                                        <option value="Manager" <?= $u['role'] == 'Manager' ? 'selected' : ''; ?>>Manager</option>
                                        <option value="Owner" <?= $u['role'] == 'Owner' ? 'selected' : ''; ?>>Owner</option>
                                    </select>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning fw-bold text-dark"><i class="bi bi-floppy me-1"></i> Perbarui Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <!-- AKHIR AREA MODAL -->

    <!-- Pustaka JavaScript Wajib -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#tabelUser').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
                order: [[0, 'asc']] // Urutkan berdasarkan ID
            });
        });
    </script>
</body>
</html>