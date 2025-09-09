<?php

namespace App\Http\Controllers;
use Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\MySession as MySession;
use Redirect;

class LoginController extends Controller
{
    public function login(Request $request){
        // $data = DB::table('cms_users')->get();
        // return $data;
        if(MySession::myId()){
            return redirect('/admin');
           // return redirect()->route('dashboard');
        }
        return view('adminLTE.'.env('LOGIN_BLADE'));
        // return view('adminLTE.login3');
    }
    public function postLogin(){
        $messages = [
            'email.exists'=> 'Invalid Username',
            'email.required'=> 'Please Provide Username',
            'g-recaptcha-response.required' => 'You must check the reCAPTCHA.',
            'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
        ];
        $v = [
            'email' => 'required|exists:cms_users',
            'password' => 'required',
            // 'g-recaptcha-response' => 'required|captcha'
        ];
        
        if(env('NOCAPTCHA_CONFIG')){
            $v['g-recaptcha-response'] = 'required|captcha';
        }

        $validator = Validator::make(Request::all(), $v,$messages);
        // return redirect()->route('getLogin')->with('message', Request::input("email"));
        if ($validator->fails()) {
            $message = $validator->errors()->all();
            return redirect()->back()->with(['with_error'=>true,'message' => $message, 'message_type' => 'danger']);
        }
        $email = Request::input("email");
        $password = Request::input("password");
        $users = DB::table('cms_users')->where("email", $email)->first();
        if (\Hash::check($password, $users->password)) {
            $priv = DB::table("cms_privileges")->where("id", $users->id_cms_privileges)->first();

            if(isset($users->id_member)){
                $member_details = DB::table('member')
                                ->select('member_code','info_completed')
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


            $info_completed = (MySession::isAdmin())?1:($member_details->info_completed ?? 1);
            // if($info_completed == 0){
            //     return redirect('/member/view/'.$member_details->member_code);
            // }
            
            if(Request::input('redirect_url') != ''){
                return Redirect::to(urldecode(Request::input('redirect_url')));
            }else{
                return redirect('/admin');
            }
        } else {
            return redirect()->route('login',['redirect_url'=>Request::input('redirect_url')])->with(['with_error'=>true,'message'=> ["Wrong Password"]]);
            // return redirect()->route('login',['redirect_url'=>Request::input('redirect_url')])->with('message', "Wrong Password");
        }
    }
    public function dashboard(){
        $data['title'] = "THIS IS TITLE";
        $data['dark_mode'] = true;
        // return MySession::myName();
        // return array(
        //     'time' => time(),
        //     'session' =>Session::get('lastActivityTime'),
        //     'difference' =>time()-Session::get('lastActivityTime')
        // );
        return view('Layout_test.index3',$data);
    }
    public function test_index(){
        // return MySession::myId();
        // return Session::get('admin_id');
        return view('Layout_test.index2');
        // return time();
        // return MySession::myId();
    }
    public function getLogout(){
        Session::flush();
        return redirect('login')->with('message', 'Thank You, See You Later !');
    }
}
