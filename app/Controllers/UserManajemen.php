<?php
namespace App\Controllers;
use App\Models\UserModel;

class UserManajemen extends BaseController
{
    public function index()
    {
        $session = session();
        if ($session->get('role') != 'Owner' || !$session->get('isLoggedIn')) {
            return redirect()->to('dashboard');
        }

        $userModel = new UserModel();
        $data = [
            'username' => $session->get('username'),
            'users'    => $userModel->findAll()
        ];

        return view('usermanajemen/index', $data);
    }

    public function tambah()
    {
        $session = session();
        if ($session->get('role') != 'Owner') return redirect()->to('dashboard');

        return view('usermanajemen/tambah', ['username' => $session->get('username')]);
    }

    public function simpan()
    {
        $userModel = new UserModel();
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $role = $this->request->getPost('role');

        if ($username === '' || strlen($username) > 100 || strlen($password) < 8
            || ! in_array($role, ['Kasir', 'Manager', 'Owner'], true)) {
            session()->setFlashdata('error', 'Username, password, atau role tidak valid. Password minimal 8 karakter.');
            return redirect()->to('usermanajemen/tambah');
        }
        
        // Cek apakah username sudah ada
        $cekUsername = $userModel->where('username', $username)->first();
        if ($cekUsername) {
            session()->setFlashdata('error', 'Username sudah dipakai! Pilih username lain.');
            return redirect()->to('usermanajemen/tambah');
        }

        $data = [
            'username'      => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => $role
        ];

        $userModel->insert($data);
        $this->auditLog('user.created', 'user', (string) $userModel->getInsertID(), ['role' => $role]);
        session()->setFlashdata('sukses', 'User baru berhasil ditambahkan!');
        return redirect()->to('usermanajemen');
    }

    public function edit($id)
    {
        $session = session();
        if ($session->get('role') != 'Owner') return redirect()->to('dashboard');

        $userModel = new UserModel();
        $data = [
            'username'  => $session->get('username'),
            'user_edit' => $userModel->find($id)
        ];

        return view('usermanajemen/edit', $data);
    }

    public function update()
    {
        $userModel = new UserModel();
        $id = $this->request->getPost('user_id');
        $username = trim((string) $this->request->getPost('username'));
        $role = $this->request->getPost('role');

        if (! ctype_digit((string) $id) || $username === '' || strlen($username) > 100
            || ! in_array($role, ['Kasir', 'Manager', 'Owner'], true)) {
            return redirect()->to('usermanajemen')->with('error', 'Data user tidak valid.');
        }

        $data = [
            'username' => $username,
            'role'     => $role
        ];

        // Jika password diisi, maka update passwordnya. Jika kosong, biarkan password lama.
        $password_baru = $this->request->getPost('password');
        if (!empty($password_baru)) {
            if (strlen($password_baru) < 8) {
                return redirect()->to('usermanajemen')->with('error', 'Password minimal 8 karakter.');
            }
            $data['password_hash'] = password_hash($password_baru, PASSWORD_DEFAULT);
        }

        $userModel->update($id, $data);
        $this->auditLog('user.updated', 'user', (string) $id);
        session()->setFlashdata('sukses', 'Data user berhasil diperbarui!');
        return redirect()->to('usermanajemen');
    }

    public function hapus($id)
    {
        $session = session();
        if ($session->get('user_id') == $id) {
            session()->setFlashdata('error', 'Anda tidak bisa menghapus akun Anda sendiri saat sedang login!');
        } else {
            $userModel = new UserModel();
            $userModel->delete($id);
            $this->auditLog('user.deleted', 'user', (string) $id);
            session()->setFlashdata('sukses', 'User berhasil dihapus!');
        }
        return redirect()->to('usermanajemen');
    }
}