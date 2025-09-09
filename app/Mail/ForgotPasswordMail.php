<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use DB;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $id_password_reset;
    
    public function __construct($id_password_reset)
    {
    
        $this->id_password_reset = $id_password_reset;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(Request $request)
    {
        


        $data['details'] = DB::table('tbl_password_reset as tpr')
                            ->select('tpr.email','tpr.token','tpr.id_tbl_password_reset','tpr.id_user','cm.name')
                           ->leftJoin('cms_users as cm','cm.id','tpr.id_user')
                           ->where('id_tbl_password_reset',$this->id_password_reset)
                           ->first();

        $query_string = array(
            'id_user'=>$data['details']->id_user,
            'token'=>$data['details']->token,
            'email'=>$data['details']->email,
            'id_password_reset'=>$data['details']->id_tbl_password_reset
        );

        $data['queryString'] = http_build_query($query_string);
        $data['currentDomain'] = $request->getSchemeAndHttpHost();

        $subject = env('APP_NAME')." Portal Account Password Reset (".$data['details']->name.")";
        $emails_to = env('DEBUG_EMAIL') ? env('DEBUGING_EMAIL_ACC'):$data['details']->email;



        return $this->to($emails_to)->view('emails.forgot-password',$data)->subject($subject)->from(config('variables.coop_email'),config('variables.coop_abbr'));

        // return $this->view('emails.forgot_password_mail',$data);
    }
}
