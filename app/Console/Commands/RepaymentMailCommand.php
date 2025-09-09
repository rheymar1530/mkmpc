<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


use App\Mail\RepaymentMail;

class RepaymentMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repayment:pushmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will push the repayment email notification';

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

        $repayment_push = DB::table('repayment_transaction as rt')
                          ->select('rt.id_repayment_transaction','m.email')
                          ->leftJoin('member as m','m.id_member','rt.id_member')
                          ->where('rt.email_sent','<=',1)
                          ->whereNotNull('rt.transaction_type')
                          ->where('rt.status','<>',10)
                          ->get();

        info("-------START EMAIL PUSHER------");
        foreach($repayment_push as $rep){
            // $email = $rep->email;
            // $email = 'caluzarheymar@gmail.com';


            $email = env('DEBUG_EMAIL') ? env('DEBUGING_EMAIL_ACC'):$rep->email;


            $valid_mail = filter_var($email, FILTER_VALIDATE_EMAIL);

            if ($valid_mail) {
                Mail::send(new RepaymentMail($rep->id_repayment_transaction));
            }
            

            DB::table('repayment_transaction')
            ->where('id_repayment_transaction',$rep->id_repayment_transaction)
            ->update(['email_sent'=>2]);
        }
        info("-------END EMAIL PUSHER------");

        echo "success";
    }
}
