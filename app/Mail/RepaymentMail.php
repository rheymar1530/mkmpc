<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;

use DB;

class RepaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id_repayment_transaction)
    {
        $this->id_repayment_transaction = $id_repayment_transaction;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(Request $request)
    {
        // ,m.email
        $data['details'] = DB::table('repayment_transaction as rt')
                            ->select(DB::raw("rt.id_repayment_transaction,DATE_FORMAT(rt.transaction_date,'%M %d, %Y') as transaction_dt,rt.swiping_amount,rt.total_payment,rt.change,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,if(rt.transaction_type =1,'Cash','ATM Swipe') as paymode,rt.transaction_type,m.email,rt.or_no,rt.email_sent"))
                           ->leftJoin('member as m','m.id_member','rt.id_member')
                           ->where('rt.id_repayment_transaction',$this->id_repayment_transaction)
                           ->first();

        $emails_to = env('DEBUG_EMAIL') ? env('DEBUGING_EMAIL_ACC'):$data['details']->email;
        $updated = ($data['details']->email_sent == 1)?" (Updated)":"";


        $subject = "Loan Payment Notification ".$data['details']->transaction_dt.$updated;
        // $data['currentDomain'] = Request::getSchemeAndHttpHost();
        $data['currentDomain'] = $request->getSchemeAndHttpHost();;
        
        // $subject = $subject." ".$data['currentDomain'];


        return $this->to($emails_to)->view('emails.repayment',$data)->subject($subject)->from(config('variables.coop_email'),config('variables.coop_abbr'));


 
        // return $this->view('emails.no_mail');
    }
}
