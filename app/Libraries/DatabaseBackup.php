<?php

namespace App\Libraries;

use RuntimeException;

class DatabaseBackup
{
    public function create(): string
    {
        $config = config('Database')->default;
        $database = (string) ($config['database'] ?? '');
        if ($database === '') {
            throw new RuntimeException('Nama database belum dikonfigurasi.');
        }

        $backupDirectory = WRITEPATH . 'backups';
        if (! is_dir($backupDirectory) && ! mkdir($backupDirectory, 0750, true) && ! is_dir($backupDirectory)) {
            throw new RuntimeException('Folder backup tidak dapat dibuat.');
        }

        $binary = getenv('MYSQLDUMP_PATH') ?: 'mysqldump';
        if ($binary === 'mysqldump' && is_file('C:\\xampp\\mysql\\bin\\mysqldump.exe')) {
            $binary = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        }

        $filename = 'konter-pos-' . date('Y-m-d-His') . '.sql';
        $path = $backupDirectory . DIRECTORY_SEPARATOR . $filename;
        $arguments = [
            '--single-transaction',
            '--routines',
            '--events',
            '--triggers',
            '--host=' . ($config['hostname'] ?? 'localhost'),
            '--port=' . ($config['port'] ?? 3306),
            '--user=' . ($config['username'] ?? ''),
        ];
        if (($config['password'] ?? '') !== '') {
            $arguments[] = '--password=' . $config['password'];
        }
        $arguments[] = $database;

        $command = self::quote($binary);
        foreach ($arguments as $argument) {
            $command .= ' ' . self::quote($argument);
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', $path, 'w'],
            2 => ['pipe', 'w'],
        ];
        $process = proc_open($command, $descriptors, $pipes);
        if (! is_resource($process)) {
            throw new RuntimeException('mysqldump tidak dapat dijalankan.');
        }
        fclose($pipes[0]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0 || ! is_file($path) || filesize($path) === 0) {
            @unlink($path);
            throw new RuntimeException(trim($error) ?: 'Backup database gagal dibuat.');
        }

        return $filename;
    }

    private static function quote(string $value): string
    {
        return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
    }
}
