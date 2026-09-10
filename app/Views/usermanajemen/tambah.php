<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User - Konter POS</title>
    <link rel="stylesheet" href="https://cdn.metroui.org.ua/v4.3.2/css/metro-all.min.css">
</head>
<body style="background-color: #f3f3f3;">
    <div data-role="appbar" class="bg-indigo fg-white">
        <a href="<?= base_url('dashboard') ?>" class="brand no-hover"><span class="mif-arrow-left"></span> Dashboard</a>
        <span class="brand text-bold mx-auto">👥 MANAJEMEN USER (OWNER)</span>
        <ul class="app-bar-menu ml-auto"><li><a href="#"><span class="mif-user"></span> <?= esc($username); ?></a></li></ul>
    </div>

    <div class="container" style="margin-top: 80px;">
        <div class="d-flex flex-justify-between flex-align-center mb-4">
            <h2>Daftar Pengguna Sistem</h2>
            <button class="button primary" onclick="window.location.href='<?= base_url('usermanajemen/tambah') ?>'"><span class="mif-user-plus"></span> Tambah User</button>
        </div>

        <?php if(session()->getFlashdata('sukses')): ?> <div class="remark success mb-4"><?= session()->getFlashdata('sukses') ?></div> <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?> <div class="remark alert mb-4"><?= esc(session()->getFlashdata('error')) ?></div> <?php endif; ?>

        <div class="card p-4">
            <table class="table striped table-border" data-role="table">
                <thead><tr><th>ID</th><th>Username</th><th>Role (Akses)</th><th>Dibuat Pada</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                        <tr>
                            <td><?= $u['user_id']; ?></td>
                            <td class="text-bold"><?= $u['username']; ?></td>
                            <td>
                                <?php if($u['role'] == 'Owner') echo "<span class='badge bg-red fg-white'>Owner</span>"; ?>
                                <?php if($u['role'] == 'Manager') echo "<span class='badge bg-green fg-white'>Manager</span>"; ?>
                                <?php if($u['role'] == 'Kasir') echo "<span class='badge bg-cyan fg-white'>Kasir</span>"; ?>
                            </td>
                            <td><?= date('d-m-Y', strtotime($u['created_at'])); ?></td>
                            <td>
                                <a href="<?= base_url('usermanajemen/edit/'.$u['user_id']) ?>" class="button small warning outline"><span class="mif-pencil"></span> Edit</a>
                                <?php if($u['user_id'] != session()->get('user_id')): ?>
                                    <a href="<?= base_url('usermanajemen/hapus/'.$u['user_id']) ?>" class="button small alert outline" onclick="return confirm('Yakin hapus user ini?')"><span class="mif-bin"></span> Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.metroui.org.ua/v4.3.2/js/metro.min.js"></script>
</body>
</html>