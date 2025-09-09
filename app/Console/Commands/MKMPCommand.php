<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MKMPCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example usage:
     * php artisan db:import dump.sql
     */
    protected $signature = 'mkmpc:import_db';

    /**
     * The console command description.
     */
    protected $description = 'Import an SQL dump file into the database';

    public function handle()
    {
        // ✅ SQL dump path inside database/dumps/
        $file = database_path('dumps/MKMPC_DUMPS.sql');

        if (!file_exists($file)) {
            $this->error("❌ File not found: $file");
            return Command::FAILURE;
        }

        // DB connection info from Laravel config
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host');

        // Step 1: Ensure schema exists
        $createCommand = sprintf(
            'mysql -h %s -u %s %s -e "CREATE DATABASE IF NOT EXISTS %s CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"',
            escapeshellarg($host),
            escapeshellarg($username),
            $password ? '-p' . escapeshellarg($password) : '',
            escapeshellarg($database)
        );

        $this->info("Ensuring database `$database` exists...");
        system($createCommand, $createReturn);

        if ($createReturn !== 0) {
            $this->error("❌ Failed to create database `$database`.");
            return Command::FAILURE;
        }

        // Step 2: Import dump
        $importCommand = sprintf(
            'mysql -h %s -u %s %s %s < %s',
            escapeshellarg($host),
            escapeshellarg($username),
            $password ? '-p' . escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($file)
        );

        $this->info("Importing SQL dump from: $file ...");
        system($importCommand, $importReturn);

        if ($importReturn === 0) {
            $this->info("✅ SQL dump imported successfully!");
            return Command::SUCCESS;
        } else {
            $this->error("❌ Failed to import SQL dump.");
            return Command::FAILURE;
        }
    }
}