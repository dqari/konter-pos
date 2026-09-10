<?php

namespace App\Controllers;

class AuditLog extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $logs = $db->table('audit_logs')
            ->select('audit_logs.*, users.username')
            ->join('users', 'users.user_id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.created_at', 'DESC')
            ->get(200)->getResultArray();

        return view('audit/index', [
            'username' => session()->get('username'),
            'logs'     => $logs,
            'toko'     => $db->table('store_profile')->get()->getRowArray(),
        ]);
    }
}
