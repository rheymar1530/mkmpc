<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use Illuminate\Support\Facades\DB;
use App\Mail\OverdueNotificationMail;
use App\Http\Controllers\GroupArrayController;
use Illuminate\Support\Facades\Mail;

class NotificationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $dt;
    public function __construct($dt)
    {
        $this->dt = $dt;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        $dt = $this->dt;
        $loans = DB::table('overdue_email as oe')
                 ->select(DB::raw("oe.id_member,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_name',month_total_due,current_payment,total_due,month_due,UPPER(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,loan.loan_token,m.email"))
                 ->leftJoin('loan','loan.id_loan','oe.id_loan')
                 ->leftJoin('member as m','m.id_member','oe.id_member')
                 ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
                 ->where('oe.month_due',$dt)
                 ->where('oe.status','<=',1)
                 ->orderBy('oe.id_overdue_email')
                 ->get();

        $g = new GroupArrayController();

        $loans = $g->array_group_by($loans,['id_member']);


        foreach($loans as $loan){
            $data['member_name'] = $loan[0]->member_name;
            $data['email'] = $loan[0]->email;
            $data['id_member'] = $loan[0]->id_member;  

            $data['loans'] = $loan;
            $data['id_loans'] = collect($loan)->pluck('id_loan');
            $data['month_date'] = $dt;

            DB::table('overdue_email')
            ->whereIn('id_loan',$data['id_loans'])
            ->where('status','<=',1)
            ->where('month_due',$data['month_date'])
            ->update(['status'=>2]);
            
            Mail::send(new OverdueNotificationMail($data));


        }


        // $data['member_name'] = $loan[0]->member_name;
        // $data['email'] = $loan[0]->email;
        // $data['id_member'] = $loan[0]->id_member;



        // //validation scripts ............
        // $loan_valid = array();
        // // $loan_valid = DB::table('overdue_email')
        // //      ->whereIn('id_loan',collect($loan)->pluck('id_loan'))
        // //      ->where('month_due',$dt)
        // //      ->where('status',1)
        // //      ->get();
        // foreach($loan as $l){
        //     $c = DB::table('overdue_email')
        //          ->where('id_loan',$l->id_loan)
        //          ->where('month_due',$dt)
        //          ->where('status',1)
        //          ->count();
        //     if($c > 0){
        //         array_push($loan_valid,$l);
        //     }
        // }

        // if(count($loan_valid) == 0){
        //     return;
        // }

        // $data['loans'] = $loan_valid;
        // $data['id_loans'] = collect($loan_valid)->pluck('id_loan');


        // $data['month_date'] = $dt;
       

        // //status sending
        // DB::table('overdue_email')
        // ->whereIn('id_loan',$data['id_loans'])
        // ->where('status',1)
        // ->where('month_due',$data['month_date'])
        // ->update(['status'=>2]);

        // //validation script for sending mail
        // Mail::send(new OverdueNotificationMail($data));

    }
}
