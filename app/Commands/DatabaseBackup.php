<?php

namespace App\Commands;

use App\Libraries\DatabaseBackup as DatabaseBackupService;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\Commands;
use RuntimeException;

class DatabaseBackup extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'backup:database';
    protected $description = 'Membuat backup database MySQL ke writable/backups.';
    protected $usage = 'backup:database';

    public function run(array $params)
    {
        try {
            $filename = (new DatabaseBackupService())->create();
            CLI::write('Backup berhasil: ' . $filename, 'green');
        } catch (RuntimeException $exception) {
            CLI::error($exception->getMessage());
            CLI::error('Pastikan mysqldump tersedia dan koneksi database benar.');
        }
    }
}
