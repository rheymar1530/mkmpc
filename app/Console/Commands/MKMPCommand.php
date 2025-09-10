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

        $sqlContent = file_get_contents($file);


        try{
                    DB::unprepared($sqlContent);
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollback();
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Something went wrong";
            $data['error'] = $ex->getMessage();

            dd($data);
            return response($data);
        }




        dd("SUCCESS");

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