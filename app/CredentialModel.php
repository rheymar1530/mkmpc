<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use DB;

class CredentialModel extends Model
{
    public static function GetCredential($privilege){
        /******GET MENU ID BY ROUTE********/
        $route  = '/'.Route::getFacadeRoot()->current()->uri();

        // dd($route);
       
        $menu_id = DB::table('cms_menus')
                ->select('id')
                ->where('path',$route)
                ->first()->id ?? 0;



        $controls = DB::table('credentials')
                    ->select('credentials.*','cms_privileges.is_superadmin')
                    ->leftJoin('cms_privileges','cms_privileges.id','credentials.id_cms_privileges')
                    ->where('id_menu',$menu_id)
                    ->where('id_cms_privileges',$privilege)
                    ->first();
        if(!isset($controls)){
            return (object)[
                'is_create'=>0,
                'is_read'=>0,
                'is_edit'=>0,
                'is_delete'=>0,
                'is_print'=>0,
                'is_confirm'=>0,
                'is_view'=>0
            ];
        }

            return $controls;
    }
    public static function GetCredentialFrame($privilege,$route){
        /******GET MENU ID BY ROUTE********/
        // $route  = '/'.Route::getFacadeRoot()->current()->uri();
        $menu_id = DB::table('cms_menus')
                ->select('id')
                ->where('path',$route)
                ->first()->id ?? 0;


        $controls = DB::table('credentials')
                    ->where('id_menu',$menu_id)
                    ->where('id_cms_privileges',$privilege)
                    ->first();

        if(!isset($controls)){
            return (object)[
                'is_create'=>0,
                'is_read'=>0,
                'is_edit'=>0,
                'is_delete'=>0,
                'is_print'=>0,
                'is_confirm'=>0,
                'is_view'=>0
            ];
        }



        return $controls;
    }
}
