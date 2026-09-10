<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Edit User</title><link rel="stylesheet" href="https://cdn.metroui.org.ua/v4.3.2/css/metro-all.min.css"></head>
<body style="background-color: #f3f3f3;">
    <div data-role="appbar" class="bg-indigo fg-white"><a href="<?= base_url('usermanajemen') ?>" class="brand no-hover"><span class="mif-arrow-left"></span> Batal</a></div>
    <div class="container" style="margin-top: 80px;">
        <div class="row flex-justify-center"><div class="cell-md-6 card p-6">
            <h4>Edit Data Pengguna</h4><hr class="thin">
            <form action="<?= base_url('usermanajemen/update') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" value="<?= $user_edit['user_id']; ?>">
                <div class="form-group mb-4"><label>Username</label><input type="text" name="username" data-role="input" value="<?= esc($user_edit['username']); ?>" required></div>
                <div class="form-group mb-4"><label>Password Baru <small class="fg-red">(Kosongkan jika tidak ingin mengubah password)</small></label>
                    <input type="password" name="password" data-role="input" placeholder="Ketik password baru...">
                </div>
                <div class="form-group mb-4"><label>Jabatan (Role)</label>
                    <select name="role" data-role="select">
                        <option value="Kasir" <?= $user_edit['role'] == 'Kasir' ? 'selected' : ''; ?>>Kasir</option>
                        <option value="Manager" <?= $user_edit['role'] == 'Manager' ? 'selected' : ''; ?>>Manager</option>
                        <option value="Owner" <?= $user_edit['role'] == 'Owner' ? 'selected' : ''; ?>>Owner</option>
                    </select>
                </div>
                <button type="submit" class="button warning block fg-white"><span class="mif-floppy-disk"></span> Perbarui Data</button>
            </form>
        </div></div>
    </div>
    <script src="https://cdn.metroui.org.ua/v4.3.2/js/metro.min.js"></script>
</body>
</html>