<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('auth');
        }

        $data = [
            'username' => $session->get('username'),
            'role'     => $session->get('role'),
            'toko'     => \Config\Database::connect()->table('store_profile')->get()->getRowArray(),
        ];

        return view('dashboard', $data);
    }
}