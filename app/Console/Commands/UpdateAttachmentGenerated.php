<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateAttachmentGenerated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soa:update_generated {--token=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will update the SOA attachment status to "generated"';

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
        $token = $this->option('token');
        DB::table('lse.tbl_statement_control')
        ->where('access_token',$token[0])
        ->update(['attachment_status' => 2]);
    }
}
