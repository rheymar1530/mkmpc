<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SchedulerController;

class EntrySchedulerCommand extends Command


{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entry-scheduler:push {--date=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will execute and push all automated entries';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('max_execution_time', -1);
        ini_set("memory_limit", "-1");
        $date = $this->option('date')[0] ?? null;
        $con = new SchedulerController();
        $result = $con->execute_task($date);
        $this->info($result);
    }
}
