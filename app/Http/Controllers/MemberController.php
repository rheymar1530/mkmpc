<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DateTime;
use Storage;
use App\CredentialModel;
use App\MySession as MySession;
use App\WebHelper;
use App\Member;

use Illuminate\Support\Facades\Hash;
class MemberController extends Controller
{
    private $member_files_path = '/uploads/member_files/';
    public function current_date(){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        return $dt->format('Y-m-d');
    }
    public function date_stamp(){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        return $dt->format('YmdHis');
    }
    public function index(){
        $data['credential'] = CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['lists'] = DB::table('member as m')
        ->select(DB::raw("m.id_member,m.membership_id as member_code,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as full_name,br.branch_name,email,m.date_created,m.status,mt.description as member_type,bl.name as brgy_lgu"))
        ->leftJoin('tbl_branch as br','br.id_branch','m.id_branch')
        ->leftJoin('membership_type as mt','m.memb_type','mt.id_membership_type')
        ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','m.id_baranggay_lgu')
        ->orDerby('id_member','DESC')
        ->get();

        // dd($data);
        $data['head_title'] = "Members";
        // return $data;
        return view("member.index",$data);
    }

    public function add_member(){

        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/member/list');
        $data['head_title'] = "Add Member";
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $g = new GroupArrayController();

        $data['positions'] = DB::table('position')->orderBy('description')->get();
        $data['coop_positions'] = DB::table('coop_position')->orderBy('description')->get();
        $data['civil_status'] = DB::table('civil_status')->get();
        $data['educational_attainment'] = DB::table('educational_attainment')->get();
        $data['branches'] = DB::table('tbl_branch')->get();
        $data['income_source'] = DB::table('tbl_income_source')->get();
        $data['current_date'] = $this->current_date();
        $data['attachments'] = array();
        $data['membership_types'] = DB::table('membership_type')->get();
        $bg_lgu = DB::table('baranggay_lgu')->get();
        $data['bg_lgu'] = $g->array_group_by($bg_lgu,['type']);

        $data['positions'] = DB::table('tbl_position')->get();
        
        $data['opcode'] = 0;

        // $this->RandMemType();
        return view("member.new_form",$data);

        return view("member.member_form",$data);
        return $data;
    }

    public function RandMemType(){
        $mem_type = [1,2,3];
        $g = new GroupArrayController();
        $bg_lgu = DB::table('baranggay_lgu')->get();
        $d = $g->array_group_by($bg_lgu,['type']);   


        $members = DB::table('member')->select('id_member')->get();

        $brgy =collect($d[1])->pluck('id_baranggay_lgu')->toArray();
        $lgu =collect($d[2])->pluck('id_baranggay_lgu')->toArray();



        foreach($members as $m){
            //type
            $mtype = $mem_type[rand(0,2)];
            if($mtype == 1){
                $brgy_lgu = null;
            }else{
                $x = ($mtype == 2)?$brgy:$lgu;
                $brgy_lgu = $x[rand(0,count($x)-1)];
            }
    
            DB::table('member')
            ->where('id_member',$m->id_member)
            ->update(['memb_type'=>$mtype,'id_baranggay_lgu'=>$brgy_lgu]);

        }

        dd("DONE");

        // dd($d[1]);


        dd($brgy);


        dd($data);
    }
    public function view_member($member_code){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/member/list');
        // return MySession::MemberCode();
        if(MySession::isAdmin()){
            if((!$data['credential']->is_view && !$data['credential']->is_edit) ){
                return redirect('/redirect/error')->with('message', "privilege_access_invalid");
            }
        }else{
            if($member_code != MySession::MemberCode()){
                return redirect('/redirect/error')->with('message', "privilege_access_invalid");
            }
        }

        // dd(1);
        // || ($member_code != MySession::MemberCode() && !MySession::isAdmin())

        // if(!$data['credential']->is_view && !$data['credential']->is_edit){
        //     return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        // }
        $g = new GroupArrayController();
        $data['positions'] = DB::table('position')->orderBy('description')->get();
        $data['coop_positions'] = DB::table('coop_position')->orderBy('description')->get();
        $data['civil_status'] = DB::table('civil_status')->get();
        $data['educational_attainment'] = DB::table('educational_attainment')->get();
        $data['branches'] = DB::table('tbl_branch')->get();
        $data['income_source'] = DB::table('tbl_income_source')->get();
        $data['current_date'] = $this->current_date();
        $data['opcode'] = 1;
        $storage_path = $this->member_files_path;

        $data['membership_types'] = DB::table('membership_type')->get();
        $bg_lgu = DB::table('baranggay_lgu')->get();
        $data['bg_lgu'] = $g->array_group_by($bg_lgu,['type']);
        

        // $data['details'] = DB::table('member')->where('member_code',$member_code)->first();
        $data['details'] = DB::table('member')->select(DB::raw("*,ifnull(amount_per_shares,100) as amount_per_shares,FormatName(first_name,middle_name,last_name,suffix) as full_name,member.id_baranggay_lgu as brgy_lgu"))->where('membership_id',$member_code)->first();
        $data['attachments'] = $g->array_group_by(DB::table('member_attachments')->where('id_member',$data['details']->id_member)->get(),['type']);
        
        $data['completed_form'] = MySession::isAdmin()?1:$data['details']->info_completed;
       // return $data['attachments']["1"][0]->description;
        // $data['details'] = json_encode($data['details']);
        if($data['details']->image_path != ""){
            $data['image_path'] = "/storage".$storage_path.$data['details']->member_code."/account_image/".$data['details']->image_path;
        }else{
            $data['image_path'] = "/storage/uploads/account_image/no_img.png";
        }
        $data['head_title'] = "Member - ".$data['details']->full_name;
        $data['positions'] = DB::table('tbl_position')->get();
        // return $data['image_path'];
        return view("member.new_form",$data);
        return view("member.member_form",$data);

        return $data;
    }

    public function post(Request $request){
        if($request->ajax()){

            $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/member/list');
            $opcode = $request->opcode;

            $credential = true;
            $id_member = $request->id_member;
            $data['opcopde'] = $request->opcode;
            $data['save_and_add'] = ($request->save_and_add == "btn_submit_member_add")?true:false;
            $image_capture_via =  $request->image_capture_via;
            $base64_image = $request->base_64_img;
            $membership_id = $request->membership_id;



            // if(isset($membership_id)){
            //     return "SET";
            // }else{
            //     return "NOT SET";
            // }
            // return $id_member;

            // $opcode = 1;
            // $id_member = 9;

            // ,"initial_paidup"
            $input_keys = ['membership_date', 'first_name', 'middle_name', 'last_name','suffix', 'date_of_birth','place_of_birth', 'gender', 'id_civil_status', 'address', 'mobile_no','tin', 'email', 'religion', 'id_educational_attainment',"id_branch","memb_type","bod_resolution","num_share","amount_per_shares","income_source","annual_income","spouse","spouse_occupation","no_dependents","spouse_annual_income","initial_paidup","member_position"];

            $sql_object = array();
            foreach($input_keys as $in){
                $sql_object[$in] = $request->{$in} ?? '';
            }
            $sql_object['id_baranggay_lgu'] = null;
            if($sql_object['memb_type'] >= 2){
                $sql_object['id_baranggay_lgu'] = $request->brgy_lgu;
            }
            // dd($sql_object);
            // return $sql_object;

            if($opcode == 0){ // Insert
                if(!$data['credential']->is_create){
                    $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                    $data['message'] = "You dont have a privilege to save this";
                    return response($data);
                }


                $duplicate_member_id_count = isset($membership_id)?$this->validate_membership_id($id_member,$membership_id,$opcode):0;
                if($duplicate_member_id_count > 0){
                    $data['RESPONSE_CODE'] = "DUPLICATE_MEMBER_ID_FOUND";
                    $data['message'] = "Please check the membership ID under member information";
                    return response($data);                    
                }
                DB::table("member")->insert($sql_object);
                $id = DB::table('member')->max('id_member');
                $id_member = $id;
                $member_code = isset($membership_id)?$membership_id:$id.$this->date_stamp();

                if($image_capture_via == 0){
                    $path = $this->upload_image($request->image,$member_code);
                }else{
                    $path = $this->upload_base_64($base64_image,$member_code);
                }
                

                DB::table("member")
                ->where("id_member",$id_member)
                ->update([
                    'image_path' => $path,
                    'member_code' => $member_code,
                    'membership_id' => isset($membership_id)?$membership_id:$member_code

                ]);
                //Push/Upload attachments
                $this->upload_attachment($request->gov_id,$request->gov_id_remarks,1,$member_code,$id_member);
                $this->upload_attachment($request->bod_mem,$request->bod_mem_remarks,2,$member_code,$id_member);
            }else{ // update
                // if(!$data['credential']->is_edit){
                //     $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                //     $data['message'] = "You dont have a privilege to save this";
                //     return response($data);
                // }

                $duplicate_member_id_count = isset($membership_id)?$this->validate_membership_id($id_member,$membership_id,$opcode):0;
                if($duplicate_member_id_count > 0){
                    $data['RESPONSE_CODE'] = "DUPLICATE_MEMBER_ID_FOUND";
                    $data['message'] = "Please check the membership ID under member information";
                    return response($data);                    
                }


                $member_code = DB::table('member')->select('member_code')->where('id_member',$id_member)->first()->member_code;
                // return $request->is_remove_image;
                if($request->is_remove_image == 1){ // If image is removed
                    $sql_object['image_path'] = "";
                }else{

                    if($image_capture_via == 0){ // if uploaded via file
                        if($request->image != "undefined" && $request->image != ""){ // If image is changed
                            $sql_object['image_path'] = $this->upload_image($request->image,$member_code);
                        }
                    }else{
                        $sql_object['image_path'] = $this->upload_base_64($base64_image,$member_code);
                    }
                    
                }



                $sql_object['membership_id'] = (isset($membership_id))?$membership_id:$id_member.$this->date_stamp();
                DB::table("member")
                ->where('id_member',$id_member)
                ->update($sql_object);

                // return response($sql_object['image_path']);

                $type_delete = array();
                if($request->remove_gov_id == 1){
                    array_push($type_delete,1);
                }
                if($request->remove_bod_id == 1){
                    array_push($type_delete,2);
                }

                if(count($type_delete) > 0){
                    DB::table('member_attachments')
                    ->where('id_member',$id_member)
                    ->wherein('type',$type_delete)
                    ->delete();
                }

                //Push/Upload attachments
                $this->upload_attachment($request->gov_id,$request->gov_id_remarks,1,$member_code,$id_member);
                $this->upload_attachment($request->bod_mem,$request->bod_mem_remarks,2,$member_code,$id_member);        
            }
            $data['member_code'] = $member_code;

            // $this->create_system_account($id_member);

            // form_data.append("remove_gov_id",remove_gov_id);
            // form_data.append("remove_bod_id",remove_bod_id);
            // form_data.append("",is_remove_image);


            $data['RESPONSE_CODE'] = "success";
            
            return response($data);
            return $request->attachment_file;


            return "screen0";

        }
    }
    public function upload_attachment($file,$remarks,$type,$member_code,$id_member){
        $date_stamp = $this->date_stamp();
        $storage_path = $this->member_files_path;
        //Type = 1 GOV ID; 2 BOD MEM
        if($type == 1){
            $f_type = "GOV_ID";
        }else{
            $f_type = "BOD_MEM";
        }
        if($file != 'undefined' && $file != ""){
            $file_name = $f_type."_".$date_stamp."_".$file->getClientOriginalName();
            Storage::disk('local')->putFileAs($storage_path.$member_code."/attachments",$file,$file_name); 

            DB::table('member_attachments')
            ->insert([
                'id_member' => $id_member,
                'type' => $type,
                'description' => $remarks,
                'file_name' => $file_name
            ]);
        }


    }
    public function upload_image($photo,$member_code){
        $path = '';
        $storage_path = $this->member_files_path;
        $date_stamp = $this->date_stamp();
        if($photo != 'undefined'){
            $file_name = $date_stamp."_".$photo->getClientOriginalName();
            $upload_path = $storage_path.$member_code."/account_image";
            Storage::disk('local')->putFileAs($upload_path,$photo,$file_name);
            // $path = $photo->storeAs('/public/uploads/account_image/'.$member_code,$file_name);
            $path = $file_name;
        }

        return $path;
    }
    public function upload_base_64($base64,$member_code){
        $path = '';
        $storage_path = $this->member_files_path;
        $date_stamp = $this->date_stamp();
        if($base64 != ''){
            $extension = explode('/', explode(':', substr($base64, 0, strpos($base64, ';')))[1])[1];
            $replace = substr($base64, 0, strpos($base64, ',')+1); 
            $image = str_replace($replace, '', $base64); 
            $image = str_replace(' ', '+', $image); 
            $file_name = $date_stamp.'.'.$extension;

            $upload_path = $storage_path.$member_code."/account_image";


            Storage::disk('local')->put($upload_path."/".$file_name, base64_decode($image));    
            $path = $file_name;
        }

        return $path;
    }
    public function validate_membership_id($id_member,$membership_id,$opcode){
        $duplicate_id_count = DB::table('member')
        ->where('membership_id',$membership_id)
        ->where(function($query) use($opcode,$id_member){
                                if($opcode == 1){ //if edit
                                    $query->where('id_member','<>',$id_member);
                                }
                            })->count();
        return $duplicate_id_count;
    }
    public function hash($input){
        $hashed_password = Hash::make($input, ['rounds' => 12]);
        return $hashed_password;
    }

    public function post_status(Request $request){
        if($request->ajax()){
            $id_member = $request->id_member;
            $status =  $request->status;
            $data['RESPONSE_CODE'] = "SUCCESS";

            // $data['RESPONSE_CODE'] = "ERROR";
            // $data['message'] = "THIS IS THE ERROR MESSAGE";

            DB::table('member')
            ->where('id_member',$id_member)
            ->update(['status'=>$status]);


            return response($data);
        }
    }

    public function create_system_account($id_member){
        $c = DB::table('cms_users')->where('id_member',$id_member)->count();
        if($c == 0){
            // $last_name = DB::table('member')->select(DB::raw("concat(replace(lower(last_name),'ñ','n'),id_member) as pass"))->where('id_member',$id_member)->first()->pass;
            // $hashed_password = Hash::make($last_name, ['rounds' => 12]);


            // // concat(replace(lower(last_name),'ñ','n'),id_member)
            // DB::select("INSERT into cms_users (id_member,name,photo,email,password,id_cms_privileges,id_branch,settings)
            //     SELECT id_member,concat(first_name,' ',last_name,' ',suffix) as name,concat('/storage/uploads/member_files/',member_code,'/account_image/',image_path),email,'$hashed_password' as password,
            //     7 as id_cms_privileges,id_branch,''
            //     FROM member
            //     WHERE id_member =?",[$id_member]);
        }else{
            // concat(replace(lower(m.last_name),'ñ','n'),m.id_member)
            DB::select("UPDATE cms_users as cu
            LEFT JOIN member as m on m.id_member = cu.id_member
            SET cu.email = m.email,
            cu.id_branch = m.id_branch,cu.name = concat(m.first_name,' ',m.last_name,' ',m.suffix),
            photo = concat('/storage/uploads/member_files/',m.member_code,'/account_image/',m.image_path)
            WHERE cu.id_member = ?;",[$id_member]);
        }
    }
    public function getMemberCBU(Request $request){
        $ls = DB::table('loan_service')
              ->select(DB::raw("with_maker_cbu,maker_min_cbu"))
              ->where('id_loan_service',$request->id_loan_service)
              ->first();

        if($ls->with_maker_cbu == 0){
            $data['WITH_CBU'] = false;
            return response($data);
        }


        $data['cbu'] = Member::getCBU($request->id_member);
        $data['WITH_CBU'] = true;
        $data['valid'] = ($data['cbu'] >= $ls->maker_min_cbu)?true:false;
        $data['minimum_cbu_amount'] = $ls->maker_min_cbu;

        return response($data);
    }
}
