<?php

namespace App\Libraries;

use RuntimeException;

class DatabaseBackup
{
    public function create(): string
    {
        $backupDirectory = WRITEPATH . 'backups';
        if (! is_dir($backupDirectory) && ! mkdir($backupDirectory, 0750, true) && ! is_dir($backupDirectory)) {
            throw new RuntimeException('Folder backup tidak dapat dibuat.');
        }

        $filename = 'konter-pos-' . date('Y-m-d-His') . '.sql';
        $path = $backupDirectory . DIRECTORY_SEPARATOR . $filename;
        $db = \Config\Database::connect();
        $handle = fopen($path, 'wb');
        if ($handle === false) {
            throw new RuntimeException('File backup tidak dapat dibuat.');
        }

        try {
            fwrite($handle, "-- Konter POS database backup\n-- Created: " . date('Y-m-d H:i:s') . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n");
            $tables = $db->query('SHOW FULL TABLES WHERE Table_type = \'BASE TABLE\'')->getResultArray();

            foreach ($tables as $tableRow) {
                $table = (string) array_values($tableRow)[0];
                $quotedTable = '`' . str_replace('`', '``', $table) . '`';
                $create = $db->query('SHOW CREATE TABLE ' . $quotedTable)->getRowArray();
                $createSql = (string) ($create['Create Table'] ?? array_values($create)[1] ?? '');

                fwrite($handle, "DROP TABLE IF EXISTS {$quotedTable};\n{$createSql};\n\n");
                $rows = $db->table($table)->get()->getResultArray();
                foreach ($rows as $row) {
                    $columns = array_map(static fn (string $column): string => '`' . str_replace('`', '``', $column) . '`', array_keys($row));
                    $values = array_map(static function ($value) use ($db): string {
                        return $value === null ? 'NULL' : $db->escape((string) $value);
                    }, array_values($row));
                    fwrite($handle, 'INSERT INTO ' . $quotedTable . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ");\n");
                }
                fwrite($handle, "\n");
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        } catch (\Throwable $exception) {
            fclose($handle);
            @unlink($path);
            throw new RuntimeException('Backup database gagal: ' . $exception->getMessage(), 0, $exception);
        }

        fclose($handle);
        if (! is_file($path) || filesize($path) === 0) {
            @unlink($path);
            throw new RuntimeException('Backup database menghasilkan file kosong.');
        }

        return $filename;
    }
}
