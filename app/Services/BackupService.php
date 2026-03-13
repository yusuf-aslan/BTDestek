<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class BackupService
{
    /**
     * List of tables to include in backup.
     * Order is important for foreign key constraints during restore.
     */
    protected array $tables = [
        'users',
        'departments',
        'categories',
        'locations',
        'device_templates',
        'assets',
        'announcement_templates',
        'announcements',
        'canned_responses',
        'articles',
        'menus',
        'general_settings',
        'tickets',
        'ticket_attachments',
        'ticket_notes',
        'ticket_activities',
        'notifications',
    ];

    /**
     * Export all data to a JSON string.
     */
    public function export(): string
    {
        $backup = [
            'version' => '1.0.0',
            'exported_at' => now()->toDateTimeString(),
            'tables' => [],
        ];

        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                $backup['tables'][$table] = DB::table($table)->get()->toArray();
            }
        }

        return json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Restore data from a JSON file.
     * WARNING: This will truncate existing tables!
     */
    public function import(array $data): bool
    {
        if (!isset($data['tables'])) {
            return false;
        }

        // Disable foreign key checks for the import
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($this->tables as $table) {
                if (isset($data['tables'][$table]) && Schema::hasTable($table)) {
                    // Truncate current table
                    DB::table($table)->truncate();

                    // Insert backup data
                    $rows = $data['tables'][$table];
                    if (!empty($rows)) {
                        // Cast stdClass to array for DB::table()->insert()
                        $insertData = array_map(function ($item) {
                            return (array) $item;
                        }, $rows);
                        
                        // Insert in chunks to avoid memory/SQL issues
                        foreach (array_chunk($insertData, 100) as $chunk) {
                            DB::table($table)->insert($chunk);
                        }
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return true;
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            \Illuminate\Support\Facades\Log::error('Backup Restore Failed: ' . $e->getMessage());
            return false;
        }
    }
}
