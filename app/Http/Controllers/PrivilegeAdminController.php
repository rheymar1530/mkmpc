<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\MySession;

use DB;

class PrivilegeAdminController extends Controller
{
    public function index(){
        if(!MySession::isSuperAdmin()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
    	$data['title'] = "Privilege List";
    	$data['privilege_lists'] = DB::table('cms_privileges')->get();

    	return view('privilege.privilege_list',$data);
    }
    public function add(){	
        if(!MySession::isSuperAdmin()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
    	$data['title'] = "Add Privilege";
    	$data['opcode'] = 0;
    	// $data['menus'] = DB::table('cms_menus')->where('is_active',1)->get();
        $data['menus'] = DB::select("SELECT * FROM (
                                    SELECT menu.id,concat(if(p_menu.name is not null,concat(p_menu.name,' - '),''),menu.name) as name,p_menu.name as 'parent_name',menu_child(menu.id) as child_count,
                                    ifnull(p_menu.sorting,menu.sorting) as parent_sort,menu.sorting as child_sort,menu.parent_id
                                    FROM cms_menus as menu
                                    LEFT JOIN cms_menus as p_menu on p_menu.id = menu.parent_id
                                    WHERE menu.is_active = 1 and menu.is_sub_module = 0) as men
                                    WHERE men.child_count = 0
                                    ORDER BY parent_sort ASC,child_sort ASC;");

    	return view('privilege.privilege_form',$data);
    }
    public function edit(Request $request){
        if(!MySession::isSuperAdmin()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
    	$data['opcode'] = 1;
    	if(isset($request->id_privilege)){
    		$id_privilege = $request->id_privilege;
    		$data['details'] = DB::table('cms_privileges')->where('id',$id_privilege)->first();
            $data['menus'] = DB::select("SELECT * FROM (
                                SELECT menu.id,concat(if(p_menu.name is not null,concat(p_menu.name,' - '),''),menu.name) as name,ifnull(is_create,0) as is_create,ifnull(is_read,0) as is_read,
                                ifnull(is_edit,0) as is_edit,ifnull(is_delete,0) as is_delete,ifnull(is_print,0) as is_print,
                                ifnull(is_view,0) as is_view,ifnull(is_confirm,0) as is_confirm,ifnull(is_confirm2,0) as is_confirm2,ifnull(is_cancel,0) as is_cancel,menu_child(menu.id) as child_count,p_menu.name as 'parent_name',ifnull(p_menu.sorting,menu.sorting) as parent_sort,menu.sorting as child_sort,menu.parent_id
                                FROM cms_menus as menu
                                LEFT JOIN credentials as cred on cred.id_menu = menu.id and cred.id_cms_privileges = $id_privilege
                                LEFT JOIN cms_menus as p_menu on p_menu.id = menu.parent_id
                                            WHERE  menu.is_sub_module = 0
                    ) as cred
                                WHERe cred.child_count = 0
                                ORDER BY parent_sort ASC,child_sort ASC;");
    		// $data['menus'] = DB::select("SELECT menu.id,menu.name,ifnull(is_create,0) as is_create,ifnull(is_read,0) as is_read,
						// 				ifnull(is_edit,0) as is_edit,ifnull(is_delete,0) as is_delete,ifnull(is_print,0) as is_print,
						// 				ifnull(is_view,0) as is_view,ifnull(is_confirm,0) as is_confirm
						// 				FROM cms_menus as menu
						// 				LEFT JOIN credentials as cred on cred.id_menu = menu.id and cred.id_cms_privileges = $id_privilege
						// 				ORDER BY menu.id;");
    	}
    	$data['title'] = "Edit Privilege";
    	return view('privilege.privilege_form',$data);
    }

    public function post(Request $request){
    	if($request->ajax()){
    		$object = array();
    		$prev_name = $request->prev_name;
    		$id_prev = $request->id_prev;
    		$opcode = $request->opcode;

    		if($opcode == 0){
	    		DB::table('cms_privileges')
	    		->insert([
	    			'name'=>$prev_name,
	    			'is_superadmin'=>0
	    		]);
	    		$id_privilege = DB::table('cms_privileges')
	    		->max('id');
			}else{
				$id_privilege = $id_prev;
				DB::table('cms_privileges')
				->where('id',$id_privilege)
				->update(['name'=>$prev_name]);

                DB::table("cms_menus_privileges")
                ->where('id_cms_privileges',$id_privilege)
                ->delete();

				DB::table('credentials')
				->where('id_cms_privileges',$id_privilege)
				->delete();
			}

            $menu_credential = array();
            $id_menu = array();
    		for($i=0;$i<count($request->id_menu);$i++){
    			$object[] = [
    				'id_menu' => $request->id_menu[$i],
    				'is_create' => $request->is_create[$i],
    				'is_read' => $request->is_read[$i],
    				'is_edit' =>$request->is_update[$i],
    				'is_delete' => $request->is_delete[$i],
                    'is_cancel'=>$request->is_cancel[$i],
    				'is_print' => $request->is_print[$i],
    				'is_view' => $request->is_view[$i],
    				'is_confirm' => $request->is_confirm[$i],
                    'is_confirm2' => $request->is_confirm2[$i],
    				'id_cms_privileges'=>$id_privilege
     			];
                if($request->is_view[$i] == 1){
                    array_push($id_menu,$request->id_menu[$i]);
                    $menu_credential[] =[
                        'id_cms_menus' =>  $request->id_menu[$i],
                        'id_cms_privileges' => $id_privilege
                    ];
                }
    		}
            
    		DB::table('credentials')
    		->insert($object);

            if(count($menu_credential) > 0){
                DB::table('cms_menus_privileges')
                ->insert($menu_credential);
            }
            DB::select("INSERT INTO credentials (is_view,is_create,is_read,is_edit,is_delete,is_print,is_confirm,is_confirm2,id_cms_privileges,id_menu)
                        SELECT if(view_child(id,$id_privilege) > 0,1,0),0,0,0,0,0,0,0,$id_privilege,id FROM cms_menus where menu_child(id) > 0;");
            if(count($id_menu) > 0){
                DB::select("INSERT INTO cms_menus_privileges (id_cms_menus,id_cms_privileges)
                            SELECT distinct parent_id,$id_privilege  FROM cms_menus where id in (".implode(",",$id_menu).");");                
            }
    		return response(array(
    			'message' => 'success'
    		));
    	}
    }
    public function switch(Request $request){
        if($request->ajax()){
            $switch_id = $request->switch_id;

            //Validate if switch ID is  valid
            $count = DB::table('privileges_switch')
                     ->where('id_cms_privileges',MySession::myParentPrivilegeID())
                     ->where('id_cms_privilege_s',$switch_id)
                     ->count();

            if($count == 0){
                $response['RESPONSE_CODE'] = "ERROR";
                $response['message'] = "Invalid Request";

                return response($response);
            }

            $priv =$d['qw'] = DB::table("cms_privileges")->where("id", $switch_id)->first();


            Session::put('admin_is_superadmin', $priv->is_superadmin);
            Session::put("admin_privileges", $priv->id); 
            Session::put('user_admin', $priv->is_admin);
            Session::put('admin_privileges_name', $priv->name);
            $response['RESPONSE_CODE'] = "SUCCESS";

            return response($response);
        }
    }
}
                    // SELECT 0,0,0,0,0,0,0,$id_privilege,id FROM cms_menus where menu_child(id) > 0;