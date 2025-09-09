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
use Illuminate\Http\Request;


class RegistrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data_param;
    
    public function __construct($id_registration)
    {
        $this->id_registration = $id_registration;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build(Request $request)
    {
        $id_registration= $this->id_registration;
        $data['details'] = DB::table('registration as r')
                               ->select(DB::raw("r.token,r.id_member,r.email,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name"))
                               ->leftJoin('member as m','m.id_member','r.id_member')
                               ->where('r.id_registration',$id_registration)
                               ->first();
        $query_string = array(
            'id_member'=>$data['details']->id_member,
            'token'=>$data['details']->token,
            'email'=>$data['details']->email
        );

        $data['queryString'] = http_build_query($query_string);
        $data['currentDomain'] = $request->getSchemeAndHttpHost();


        $emails_to = env('DEBUG_EMAIL') ? env('DEBUGING_EMAIL_ACC'):$data['details']->email;
        // $subject = env('APP_NAME')." Portal Member's Registration (".$data['details']->member_name.")";
        $subject = env('APP_NAME')." Member Registration Portal (".$data['details']->member_name.")";

        $this->to($emails_to)->view('emails.registration',$data)->subject($subject)->from(config('variables.coop_email'),config('variables.coop_abbr'));


    }
}
