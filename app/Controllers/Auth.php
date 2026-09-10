<?php

namespace App\Controllers;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        return view('auth/login', [
            'toko' => $db->table('store_profile')->get()->getRowArray(),
        ]);
    }

    public function process()
    {
        $session = session();
        $userModel = new UserModel();

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        if (! is_string($username) || ! is_string($password) || $username === '' || $password === '') {
            $session->setFlashdata('msg', 'Username dan password wajib diisi.');
            return redirect()->to('auth');
        }

        $dataUser = $userModel->where('username', $username)->first();

        if ($dataUser) {
            $pass_db = $dataUser['password_hash'];
            if (password_verify($password, $pass_db)) {
                $session->regenerate(true);
                $ses_data = [
                    'user_id'    => $dataUser['user_id'],
                    'username'   => $dataUser['username'],
                    'role'       => $dataUser['role'],
                    'isLoggedIn' => TRUE
                ];
                $session->set($ses_data);
                return redirect()->to('dashboard'); // Tanpa garis miring di awal
            } else {
                $session->setFlashdata('msg', 'Password salah!');
                return redirect()->to('auth');
            }
        } else {
            $session->setFlashdata('msg', 'Username tidak ditemukan!');
            return redirect()->to('auth');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth');
    }
}