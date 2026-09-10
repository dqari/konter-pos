<?php

namespace App\Controllers;

use App\Libraries\DatabaseBackup;
use RuntimeException;

class Backup extends BaseController
{
    public function index()
    {
        $files = glob(WRITEPATH . 'backups' . DIRECTORY_SEPARATOR . 'konter-pos-*.sql') ?: [];
        rsort($files);
        $backups = array_map(static function (string $path): array {
            return [
                'name' => basename($path),
                'size' => filesize($path),
                'date' => filemtime($path),
            ];
        }, $files);

        return view('backup/index', [
            'username' => session()->get('username'),
            'backups'  => $backups,
            'toko'     => \Config\Database::connect()->table('store_profile')->get()->getRowArray(),
        ]);
    }

    public function create()
    {
        try {
            $filename = (new DatabaseBackup())->create();
            $this->auditLog('backup.created', 'backup', $filename);
            return redirect()->to('backup')->with('pesan', 'Backup berhasil dibuat: ' . $filename);
        } catch (RuntimeException $exception) {
            return redirect()->to('backup')->with('error', $exception->getMessage());
        }
    }

    public function download(string $filename)
    {
        if (! preg_match('/\Akonter-pos-[0-9-]+\.sql\z/', $filename)) {
            return redirect()->to('backup')->with('error', 'Nama file backup tidak valid.');
        }

        $path = WRITEPATH . 'backups' . DIRECTORY_SEPARATOR . $filename;
        if (! is_file($path)) {
            return redirect()->to('backup')->with('error', 'File backup tidak ditemukan.');
        }

        return $this->response->download($path, null);
    }
}
