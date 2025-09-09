<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use DB;
use DNS2D;
use DNS1D;


class SendSOAMail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $control_number; 

    public function __construct($control_number){
        $this->control_number = $control_number;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data['test'] = $this->control_number;

        $data['statement_details'] =   DB::connection('cloud_db_lse')
        ->table('lse.tbl_statement_control as ts')
        ->select(DB::raw("tp.id_client_profile,if(ts.control_number > 1000000,ts.control_number,LPAD(ts.control_number, 7, 0)) as control_number,
            if(statement_start_date = statement_end_date,DATE_FORMAT(statement_start_date,'%m/%d/%Y'),CONCAT(DATE_FORMAT(statement_start_date,'%m/%d/%Y'),'-',DATE_FORMAT(statement_end_date,'%m/%d/%Y'))) as billing_period,
            DATE_FORMAT(statement_date,'%m/%d/%Y') as statement_date,
            DATE_FORMAT(DATE_ADD(statement_date, INTERVAL 30 DAY),'%m/%d/%Y') as due_date,ts.access_token,GETCostCenters(control_number) as cost_center,
            CONCAT(DATE_FORMAT(statement_start_date,'%m/%d/%Y'),' - ',DATE_FORMAT(statement_end_date,'%m/%d/%Y')) as bill_period,ts.status as status_code,tp.name as account_name
            "))
        ->leftJoin('tbl_client_profile as tp','tp.account_no','ts.account_no')
        ->where('control_number',$this->control_number)
        ->orderby('ts.control_number')
        ->first();
        $control_number  = $data['statement_details']->control_number;

        \Storage::disk('client')->put($control_number.".png",base64_decode(DNS2D::getBarcodePNG("$control_number", "QRCODE")));
        // $emails_to = ['mconel2018@gmail.com'];
        $emails_to = ['caluzarheymar@gmail.com'];
        $subject = "LIBCAP Super Express E-Statement of Account";

        return $this->to($emails_to)->view('emails.soa_send',$data)->subject($subject)->from('r3s1d3nt3v1l2018@gmail.com');
    }
}
