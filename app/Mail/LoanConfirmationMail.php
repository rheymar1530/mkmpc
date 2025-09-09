<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;
use DB;
use App\Http\Controllers\LoanApprovalController;

class LoanConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id_loan,$comaker_mail=array())
    {
        $this->id_loan = $id_loan;
        $this->comaker_mail = $comaker_mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        // $loan_status_email = [1,2,3,5]; //allowed loan status to trigger email

        $loan_status_email = [0,2,5];
        $details = $data['details'] = DB::table('loan')
                   ->select(DB::raw("loan.id_loan,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,loan.principal_amount,if(loan.status=3,'Released',loanStatus(loan.status)) as loan_status,loan.interest_rate,loan.status,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) loan_service,m.email,loan.loan_token,loan.cancellation_reason,ls.name as service_name,if(loan.id_loan_payment_type=2,'',concat(loan.terms,' Month(s)')) as terms,DATE_FORMAT(loan.date_created,'%m/%d/%Y') as date_submitted"))
                   ->leftJoin('member as m','m.id_member','loan.id_member')
                   ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
                   ->where('loan.id_loan',$this->id_loan)
                   ->first();

        if($details->status==0 && count($this->comaker_mail) > 0){
            // if approved and for comaker email
            $data['maker_name'] = $this->comaker_mail['name'];
            $emails_to = $this->comaker_mail['email'];
            $subject = "Co-Maker Notification - Loan Application ".$details->service_name;
            $data['show_complete'] = false;

            return $this->to($emails_to)->view('emails.comaker',$data)->subject($subject)->from(config('variables.coop_email'),config('variables.coop_abbr'));
        }

        if(!in_array(intval($details->status),$loan_status_email)){
            return $this->view('emails.no_mail');
        }

        switch(intval($details->status)){
            case 0:
                $subject = "Loan Application ";
                break;
            case 2:
                $subject = "Loan Application Status ";
                break;
            case 5:
                $subject = "Loan Application Status ";
                break;
            default:
                $subject = "Loan Application ";
                break;
        }

        $subject = $subject." ".$details->service_name;
        $emails_to = env('DEBUG_EMAIL') ? env('DEBUGING_EMAIL_ACC'):$details->email;

        $data['currentDomain'] = Request::getSchemeAndHttpHost();
        $data['show_complete'] = true;

        

        if($details->status == 2){
            // Email with waiver attachment on loan approval
            $l_app = new LoanApprovalController();
            $pdf = $l_app->print_application_waiver($details->loan_token,true);
            $file_name = $details->id_loan." - ".$details->member_name." Loan Application Waiver";

            return $this->to($emails_to)->view('emails.loan_confirmation',$data)->subject($subject)->from(config('variables.coop_email'),config('variables.coop_abbr'))->attachData($pdf, $file_name.'.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $this->to($emails_to)->view('emails.loan_confirmation',$data)->subject($subject)->from(config('variables.coop_email'),config('variables.coop_abbr'));
    }
}




/*******************
 * LOAN EMAIL PARAMETERS:

 * loan id
 * member name
 * status [code and description]
 * loan service
 * loan amount
 * interest rate
 * 
 * ALLOWED LOAN STATUS FOR EMAIL
 * 
 * 1 (PROCESSING)
 * 2 (APPROVED/FOR RELEASING)
 * 3 (Released)
 * 5 (DISAPPROVED)
 * 
 * 
 * ****************/
