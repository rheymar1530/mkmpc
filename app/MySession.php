<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;
use DateTime;
use DB;
class MySession extends Model
{
    public static function current_date(){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        // return '2025-04-30';
        // return '2024-12-15';
        // return '2023-12-20';
        // return '2024-02-16';
        // return '2022-09-02';
        // return '2022-10-16';

        // return '2025-07-30';


        // return '2025-10-01';
        // return '2025-06-07';
        // return '2025-10-01';
        return $dt->format('Y-m-d');
    }

    public static function current_year(){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        return $dt->format('Y');      
    }

    public static function current_month(){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        // return '2022-09-02';
        // return '2022-10-16';
        return $dt->format('m');
    }

    public static function mySystemUserId(){
       return Session::get('system_user_id');  
    }

    public static function isSuperAdmin(){
        return Session::get('admin_is_superadmin');
    }

    public static function myId(){
        return Session::get('admin_id');
    }

    public static function myName(){
    	return Session::get('admin_name');
    }

    public static function myPrivilegeName()
    {
    	return Session::get('admin_privileges_name');
    }
    public static function myPrivilegeID(){
        // return 8; //CC
        // return 9; //GM
        return Session::get('admin_privileges');
    }
    public static function myParentPrivilegeID(){
        return Session::get('parent_privilege');
    }

    public static function myPhoto(){
        return Session::get('admin_photo');
    }
    
    public static function WebSettings()
    {
    	return Session::get('web_view_settings');
    }
    public static function me(){
        return DB::table('cms_users')->where('id',Session::get('system_user_id'))->first();
    }

    public static function myBranchID(){
        return Session::get('id_branch');
    }

    public static function isAdmin(){
        return Session::get('user_admin');
    }

    public static function MemberCode(){
        return Session::get('member_code');
    }

    public static function PrintNote(){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();

        $date = $dt->format('m/d/Y h:i A');
        return self::myName()." $date";
    }
}
