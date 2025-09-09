<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
// use App\Listeners\UpdateTableAfterEmailSentOverdue;
use Illuminate\Support\Facades\Event;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Events\MessageSent;


class OverdueNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $data_param;
    
    public function __construct($data_param)
    {
        $this->data_param = $data_param;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build(){
        $od_data = $this->data_param;

        $data['member_name'] = $od_data['member_name'];

        $subject = "Loan Overdue";

        // if(env('DEBUG_EMAIL')){
        //     $emails_to = [env('DEBUGING_EMAIL_ACC')];
        // }else{
            $emails_to = [$od_data['email']];
        // }

    
        // $emails_to = [env('DEBUGING_EMAIL_ACC'),'caluzarheymar@gmail.com'];
        

       return $this->to($emails_to)
                ->view('emails.overdue',$od_data)
                ->subject($subject)
                ->from(config('variables.coop_email'),config('variables.coop_abbr'));
                // ->withSwiftMessage(function ($message) use ($od_data) {
                //     $message->getHeaders()->addTextHeader('X-Message-ID', $od_data['id_member']);
                // })
                // ->with(['id_member' => $od_data->id_member]);
    }
    /**
     * Serialize the object to a string.
     *
     * @return string
     */
 
    public function send(Mailer $mailer)
    {
        parent::send($mailer);

        $identifier = $this->data_param;

        // Update your table based on the identifier
        DB::table('overdue_email')
            ->where('id_member', $identifier['id_member'])
            ->where('status',2)
            ->whereIn('id_loan',$identifier['id_loans'])
            ->where('month_due',$identifier['month_date'])
            ->update(['status' => 3]);

        // Dispatch the event to handle further processing if needed
        // Event::dispatch(new MessageSent($this->data_param));
    }
}
