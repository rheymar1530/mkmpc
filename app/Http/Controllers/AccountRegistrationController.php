<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\RegistrationEmail;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AccountRegistrationController extends Controller
{
    public function registration_form(Request $request){
        // dd($this->generateRandomString(60));

        // // dd("SUCCESS"); 
        // $data['details'] = DB::table('registration as r')
        //                        ->select(DB::raw("r.token,r.id_member,r.email,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name"))
        //                        ->leftJoin('member as m','m.id_member','r.id_member')
        //                        ->where('r.id_registration',1)
        //                        ->first();

        // $query_string = array(
        //     'id_member'=>$data['details']->id_member,
        //     'token'=>$data['details']->token,
        //     'email'=>$data['details']->email
        // );

        // $data['queryString'] = http_build_query($query_string);
        // $data['currentDomain'] = $request->getSchemeAndHttpHost();

        // return view('emails.registration',$data);
        // dd($data);

        return view('registration.registration');
    }
    public function search_member(Request $request){
        if($request->ajax()){
            $search = $request->term;   
            if(strlen($search) < 3){
                $data['accounts'] = array();
                return response($data);
            }
            $data['accounts'] = DB::table('member as m')
            ->select(DB::raw("concat(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as tag_value,id_member as tag_id"))
            ->where(function ($query) use ($search){
                $query->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%$search%");
            })
            ->where('m.status',1)
            ->get();
            return response($data);
        }
    }
    public function parseMember(Request $request){
        if($request->ajax()){
            $id_member = $request->id_member;

            $data['details'] = DB::table('member as m')
            ->select(DB::raw('id_member,address,email'))
            ->where('id_member',$id_member)
            ->first();
            return response($data);
        }
    }
    public function post(Request $request){
        if($request->ajax()){
            $data['RESPONSE_CODE'] = "SUCCESS";
            $messages = [
                'g-recaptcha-response.required' => 'You must check the reCAPTCHA.',
                'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
            ];
            $validator = Validator::make($request->all(), [
                'g-recaptcha-response' => 'required|captcha'
            ],$messages);
      
            //Google Captcha Validator
            if ($validator->fails()) {
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Please check google CAPTCHA";
                return response($data);
            }

            //Validations Scripts

            $count_member = DB::table('registration')->where('id_member',$request->id_member)->count();
            if($count_member > 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Member is already registered";
                return response($data);
            }


            $count_cms_users = DB::table('cms_users')->where('id_member',$request->id_member)->count();
            if($count_cms_users > 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Member is already registered";
                return response($data);
            }


            //count email
            $count_member = DB::table('registration')->where('email',$request->email)->count();
            if($count_member > 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Email is already used";
                return response($data);
            }

            //Post Script
            $postObj = [
                'id_member'=>$request->id_member,
                'email' => $request->email,
                'token'=> $this->generateRandomString()
            ];

            DB::table('registration')
            ->insert($postObj);

            $id_registration = DB::table('registration')->where('id_member',$request->id_member)->max('id_registration');
            
            Mail::send(new RegistrationEmail($id_registration));

            return response($data);

            dd("SUCCESS");
            // return response($request->all());
        }
    }
    public function SetPasswordView(Request $request){
        $data['details'] = DB::table('registration as r')
                               ->select(DB::raw("r.id_registration,r.token,r.id_member,r.email,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name"))
                               ->leftJoin('member as m','m.id_member','r.id_member')
                               ->where('token',$request->token)
                               ->where('r.id_member',$request->id_member)
                               ->where('r.email',$request->email)
                               ->where('r.status',0)
                               ->first();
        if(!isset($data['details'])){
            abort(404);
        }
        return view('registration.set_password',$data);
    }

    public function PostPasswordRegister(Request $request){
       $password = $request->password;
       $re_password = $request->re_password;
       $reg_token = $request->reg_token;
       $reg_id = $request->reg_id;

       $hashed_password = Hash::make($password, ['rounds' => 12]);

       $registration_details = DB::table('registration')->where('id_registration',$reg_id)->where('token',$reg_token)->first();

       $member_details = DB::table('member')
                         ->select(DB::raw("concat(first_name,' ',last_name) as member_name,id_member"))
                         ->where('id_member',$registration_details->id_member)
                         ->first();

        $settings['dark_mode'] = 0;
        DB::table('cms_users')
        ->insert([
            'name'=>$member_details->member_name,
            'photo'=>'',
            'email'=>$registration_details->email,
            'password'=>$hashed_password,
            'id_cms_privileges'=>7,
            'id_branch' => 0,
            'id_member' => $member_details->id_member,
            'settings'=>json_encode($settings)
        ]);

        DB::table('registration')
        ->where('id_registration',$registration_details->id_registration)
        ->where('id_member',$member_details->id_member)
        ->update(['status'=>1]);


        DB::table('member')
        ->where('id_member',$member_details->id_member)
        ->update([
            'email'=>$registration_details->email
        ]);

        $users = DB::table('cms_users')
        ->where("id_member", $member_details->id_member)
        ->orDerby('id','DESC')
        ->first();

        return $this->post_on_session($users,1);

    }

    public function post_on_session($users,$type){
        //type 1 (registration/redirect to membership info); 2 password reset (redirect to dashboard)

        $priv = DB::table("cms_privileges")->where("id", $users->id_cms_privileges)->first();
 
        if(isset($users->id_member)){
            $member_details = DB::table('member')
                             ->select('member_code')
                             ->where('id_member',$users->id_member)
                             ->first();
        }

        if($priv->is_superadmin){ // if account is super admin
            $photo_path = (substr($users->photo, 0, 8) == "/storage")?substr($users->photo, 9):$users->photo;
            $photo = ($users->photo) ? asset("/storage/".$photo_path) : "/storage/uploads/account_image/no_img.png";
            
            }else{
                if($users->photo != ""){
                    $photo_path = (substr($users->photo, 0, 8) == "/storage")?substr($users->photo, 9):$users->photo;
                    $photo = "/storage/$photo_path";
                }else{
                    $photo ="/storage/uploads/account_image/no_img.png";
                }
            }
            

            try{
                $view_settings =  json_decode($users->settings,true);
                foreach($view_settings as $key=>$val){
                    $view_settings[$key] = ($val == "1")?true:false;
                }
                 Session::put('web_view_settings',$view_settings);
            }catch (\Exception $e) {
                Session::put('web_view_settings','');
            }

            
            Session::put('admin_id', $users->id_member);
            Session::put('system_user_id',$users->id);
            Session::put('admin_is_superadmin', $priv->is_superadmin);
            // 
            Session::put('user_admin', $priv->is_admin);
            // 
            Session::put('admin_name', $users->name);
            Session::put('admin_photo', $photo);
            Session::put('parent_privilege',$users->id_cms_privileges);
            Session::put("admin_privileges", $users->id_cms_privileges);
            Session::put('admin_privileges_name', $priv->name);
            Session::put('member_code',$member_details->member_code ?? 0);
           
        
            Session::put('id_branch',$users->id_branch);
            if($type == 1){
                 return redirect('/member/view/'.$member_details->member_code);
            }else{
                return redirect('/admin');
            }
    }

    public function ForgotPasswordForm(Request $request){

        // $id_password_reset = 4;

        // Mail::send(new ForgotPasswordMail($id_password_reset));
        
        // dd("success");    

        // $data['details'] = DB::table('tbl_password_reset as tpr')
        //                     ->select('tpr.email','tpr.token','tpr.id_tbl_password_reset','tpr.id_user','cm.name')
        //                    ->leftJoin('cms_users as cm','cm.id','tpr.id_user')
        //                    ->where('id_tbl_password_reset',$id_password_reset)
        //                    ->first();
        // $query_string = array(
        //     'id_user'=>$data['details']->id_user,
        //     'token'=>$data['details']->token,
        //     'email'=>$data['details']->email
        // );

        // $data['queryString'] = http_build_query($query_string);
        // $data['currentDomain'] = $request->getSchemeAndHttpHost();

        // return view('emails.forgot-password',$data);

        // dd($data);

        return view('forgot_password.forgot_password');
    }


    public function post_request(Request $request){

        $data['RESPONSE_CODE'] = "SUCCESS";
        $messages = [
            'g-recaptcha-response.required' => 'You must check the reCAPTCHA.',
            'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
        ];
        $validator = Validator::make($request->all(), [
            'g-recaptcha-response' => 'required|captcha'
        ],$messages);
  
        //Google Captcha Validator
        if ($validator->fails()) {
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please check google CAPTCHA";
            return response($data);
        }

        //validate if there are existing requests
        $count = DB::table('tbl_password_reset')
                 ->where('email',$request->email)
                 ->where('status',0)
                 ->count();

        if($count > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "There are pending forgot password request on this email.";
            return response($data);            
        }


        $account = DB::table('cms_users')
                   ->select('email','name','id')
                   ->where('email',$request->email)
                   ->first();


        if($account == null){
            $data['RESPONSE_CODE'] = "INVALID_ACCOUNT";
            return $data;
        }

        DB::table('tbl_password_reset')
        ->insert([
            'token' => $this->generateRandomString(60),
            'id_user' => $account->id,
            'email' => $account->email,
            'status'=>0
        ]);

        $id_password_reset = DB::table('tbl_password_reset')
                ->where('id_user',$account->id)
                ->max('id_tbl_password_reset');

        Mail::send(new ForgotPasswordMail($id_password_reset));
        // Mail::send(new ForgotPasswordMail($account,$max));
        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['RESPONSE_EMAIL'] = $account->email;

        return response($data);
    }

    public function ForgotPasswordInputs(Request $request){

        //validate if request exists
        $data['details'] = DB::table('tbl_password_reset')
                           ->where('id_tbl_password_reset',$request->id_password_reset)
                           ->where('id_user',$request->id_user)
                           ->where('token',$request->token)
                           ->where('email',$request->email)
                           ->where('status',0)
                           ->first();
        if(!isset($data['details'])){
            abort(404);
        }
        return view('forgot_password.reset_form',$data);
        dd($data);
    }
// Qweqwe11
    public function PostPasswordReset(Request $request){
        $password = $request->password;
        $re_password = $request->re_password;
        $request_token = $request->request_token;
        $request_id = $request->request_id;
        $request_id_user = $request->request_id_user;



        $hashed_password = Hash::make($password, ['rounds' => 12]);

        $details = DB::table('tbl_password_reset')
                           ->where('id_tbl_password_reset',$request_id)
                           ->where('id_user',$request_id_user)
                           ->where('token',$request_token)
                           ->first();

    
       //update password of cms user
       DB::table('cms_users')
       ->where('id',$details->id_user)
       ->where('email',$details->email)
       ->update(['password'=>$hashed_password]);

       //update password request
       DB::table('tbl_password_reset')
       ->where('token',$request_token)
       ->where('id_tbl_password_reset',$request_id)
       ->update(['status'=>1,'date_password_change'=>DB::raw("now()")]);

        $users = DB::table('cms_users')
        ->where("id", $details->id_user)
        ->first();

        return $this->post_on_session($users,2);
    }

    public function generateRandomString($length = 45) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
