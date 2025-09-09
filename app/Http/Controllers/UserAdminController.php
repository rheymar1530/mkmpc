<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use Session;
use App\MySession as MySession;
use Storage;
class UserAdminController extends Controller
{
    public function index(){
        if(!MySession::isSuperadmin()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['title'] = "User List";
        $data['users'] = DB::table('cms_users')
        ->select('cms_users.id','cms_users.name','cms_users.email','cms_privileges.name as priv_name')
        ->leftJoin('cms_privileges','cms_privileges.id','cms_users.id_cms_privileges')
        ->get();
        return view('user_account.account_list',$data);
    }
    public function add(){
        if(!MySession::isSuperadmin()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['opcode'] = 0;
        $data['privileges'] = DB::table('cms_privileges')->get();
        $data['branches'] = DB::table('tbl_branch')
                            ->select('id_branch','branch_name')
                            ->get();
   
        return view('user_account.account_form',$data);
    }
    public function edit(Request  $request){
        if(!MySession::isSuperadmin()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $id_user = DB::table('cms_users')->max('id');

        $id= $request->id;
        $data['opcode'] = 1;
        $data['branches'] = DB::table('tbl_branch')
                    ->select('id_branch','branch_name')
                    ->get();
        $data['privileges'] = DB::table('cms_privileges')->get();
        $data['user_details'] = DB::table('cms_users')
                               ->select('id','name','email','photo','id_cms_privileges','id_branch','id_member')
                               ->where('id',$id)
                               ->first();

        if(isset($data['user_details']->id_member)){
            $data['selected_member'] =DB::table('member')
                ->select(DB::raw("concat(membership_id,' || ',first_name,' ',last_name) as member_name,id_member"))
                ->where('id_member',$data['user_details']->id_member)
                ->first();
        }
        // return $data;
        return view('user_account.account_form',$data);
    }
    public function post(Request  $request){


        if(!MySession::isSuperadmin()){
            return "ERROR";
        }
        $opcode = $request->opcode;
        $photo = $request->photo;
        $id_member = $request->id_member;

        // return $id_member;
        $path = '';

        if($photo != 'undefined'){
            $file_name = $photo->getClientOriginalName();
            $path = $photo->storeAs('/public/uploads',$file_name);
            $path = substr($path, 7);
        }
        $settings['dark_mode'] =0;
        // return $path;
        $hashed_password = Hash::make($request->password, ['rounds' => 12]);
        if($opcode == 0){ // insert   

              
            DB::table('cms_users')
            ->insert([
                'name'=>$request->name,
                'photo'=>$path,
                'email'=>$request->username,
                'password'=>$hashed_password,
                'id_cms_privileges'=>$request->id_cms_privileges,
                'id_branch' => $request->id_branch,
                'id_member' => $id_member,
                'settings'=>json_encode($settings)
            ]);
            $id_user = DB::table('cms_users')->max('id');
        }else{ //Update
            DB::table('cms_users')
            ->where('id',$request->id_user)
            ->update([
                'name' =>$request->name,
                'email'=>$request->username,
                'password'=>DB::raw("if('$request->password' = '',password,'$hashed_password')"),
                'photo'=>DB::raw("if('$photo' = 'undefined',photo,'$path')"),
                'id_cms_privileges' => $request->id_cms_privileges,
                'id_branch' => $request->id_branch,
                'settings'=>json_encode($settings)
            ]);
            $id_user = $request->id_user;
        }
        $data['message'] = "success";
        $data['id_user'] = $id_user;
        // Session::put("admin_privileges",$request->id_cms_privileges);
        return response($data);
    }
    public function post_settings(Request $request){
        if($request->ajax()){
            $settings = $request->settings;
            DB::table('cms_users')
           ->where('id',MySession::mySystemUserId())
           ->update([
             'settings'=>json_encode($settings)
           ]);

           $view_settings = $settings;
           foreach($view_settings as $key=>$val){
                $view_settings[$key] = ($val == "1")?true:false;
           }
           Session::put('web_view_settings',$view_settings);
            // foreach ($settings as $key => $value) {

            // }
        }
        return response(json_encode($settings));
    }
    public function set_settings(){

    }
    public function profile(){

        $no_img = config('global.no_image');
        $data['user_details'] = DB::table('cms_users as u')
                          ->select('u.id as id_user','u.name','u.email','priv.name as priv_name','branch.branch_name','u.id_member',DB::raw("if(u.photo is null or u.photo = '','$no_img',u.photo) as photo,member.member_code"))
                          ->LeftJoin('cms_privileges as priv','priv.id','u.id_cms_privileges')
                          ->LeftJOin('tbl_branch as branch','branch.id_branch','u.id_branch')
                          ->leftJoin('member','member.id_member','u.id_member')
                          ->where('u.id',MySession::mySystemUserId())
                          ->first();
                          // return $data;
                          

        return view('user_account.profile',$data);
        return $data;
    }
    public function post_user_update(Request $request){
        try{
            if($request->ajax()){
                $opcode =$request->opcode;

                // return 123;
                // return $opcode;
                if($opcode == 1){ //PHOTO
                    $path = '';
                    $photo = $request->image;
                    if($photo != 'undefined'){
                        $file_name = $photo->getClientOriginalName();
                        $upload_path = "/uploads/user_files/profile_pic";
                        Storage::disk('local')->putFileAs($upload_path,$photo,$file_name);
                         // $path = $photo->storeAs('/public/uploads/account_image/'.$member_code,$file_name);
                         $path = $upload_path."/".$file_name;
                    }

                    $update_data = array();
                    if($request->remove_img){
                         $update_data['photo'] = "";
                    }else{
                        if($path != ''){
                            $update_data['photo'] = $path;
                            Session::put('admin_photo',asset("/storage/".$path));
                        }
                    }
                    try{
                        DB::table('cms_users')
                        ->where('id',MySession::mySystemUserId())
                        ->update($update_data);
                        $data['message'] = 'success';
                    }catch(\Illuminate\Database\QueryException $ex){ 
                       $error_message = $ex->getMessage();
                       $data['message'] = 'failed';
                    }

                    
                }elseif($opcode == 2){ // Password
                    $current_password = DB::table('cms_users')->select('password')->where('id',MySession::mySystemUserId())->first();
                    // return $request->password;
                    if(!Hash::check($request->password, $current_password->password)){
                        $data['message'] = "invalid_password";
                        return response($data);
                    }
                    $update_data['password'] = Hash::make($request->new_password, ['rounds' => 12]);
                    try{
                        DB::table('cms_users')
                        ->where('id',MySession::mySystemUserId())
                        ->update($update_data);
                        $data['message'] = 'success';
                    }catch(\Illuminate\Database\QueryException $ex){ 
                       $error_message = $ex->getMessage();
                       $data['message'] = 'failed';
                    }   
                    // return $data['message'] = "YESZ";
                }
                return response($data);
            }
        }catch (\Exception $e) {
                    $data['message'] = 'failed';
                        $data['text'] = $e->getMessage();
                        return $data;
        }
    }
    public function get_member_details(Request $request){
        if($request->ajax()){
            $id_member = $request->id_member;
            $data['details'] = DB::table('member')
                               ->select(DB::raw("concat(first_name,' ',last_name,' ',suffix) as name,id_branch,email"))
                               ->where('id_member',$id_member)
                               ->first();
            return response($data);
        }
    }

}
