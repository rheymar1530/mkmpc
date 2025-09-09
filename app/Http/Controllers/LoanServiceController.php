<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\CredentialModel;
use App\MySession as MySession;

class LoanServiceController extends Controller
{
    public function index(){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Loan Services";

        $data['loan_service_list'] = DB::table('loan_service as loan')
        ->select(DB::raw("loan.id_loan_service,loan.name,dt.description as disbursement_type,if(loan.min_amount = loan.max_amount,FORMAT(loan.min_amount,2),concat(FORMAT(loan.min_amount,2),' - ',FORMAT(loan.max_amount,2))) as amount_range,FORMAT(loan.default_amount,2) as default_amount,im.description as interest_method,lp.description as loan_payment_method,ifnull(concat(getMonth(loan.start_month_period),' - ',getMonth(end_month_period)),'') as period,if(loan.status=1,'Active','Inactive') as status"))
        ->leftJoin("disbursement_type as dt","dt.id_disbursement_type","loan.id_disbursement_type")
        ->leftJoin("loan_payment_type as lp","lp.id_loan_payment_type","loan.id_loan_payment_type")
        ->leftJoin("interest_method as im",'im.id_interest_method','loan.id_interest_method')
        ->orDerby('loan.id_loan_service','DESC')
        ->get();

        // return $data;
        return view('loan_service.index',$data);
    }
    public function create(){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/loan_service');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['head_title'] = "Create Loan Service";
        $data['opcode'] = 0;
        $data['membership_types'] = DB::table('membership_type')->get();

        $data['disbursement_type'] = DB::table('disbursement_type')->get();
        $data['interest_method'] = DB::table('interest_method')->get();
        $data['loan_payment_type'] = DB::table('loan_payment_type')->get();
        $data['period'] = DB::table('period')->get();
        $data['repayment_period'] = DB::table('repayment_period')->get();

        $data['approvers'] = DB::table('cms_privileges')->select('id','name')->where('is_approver',1)->get();
        $data['charges_group'] = DB::table('charges_group')
        ->select(DB::raw("name,id_charges_group"))
        ->where('active',1)
        ->get();
        $data['terms_condition'] = DB::table('terms_condition')->get();
                                   
        $data['one_time_type'] = DB::table('one_time_type')->get();
        return view('loan_service.loan_service_form',$data);

        return $data;
    }
    public function post(Request $request){
        // if($request->ajax()){
            $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/loan_service');

            $loan_service = $request->loan_service;
            $terms = $request->terms;
            $net_pay = $request->net_pay;
            $loan_approvers = $request->loan_approvers;
            $post_save_as = $request->post_save_as;


            if(!isset($loan_service['id_charges_group'])){
                $loan_service['id_charges_group'] = 0;
            }

        

            // if($loan_service['id_loan_payment_type'] == 1){
            //     $loan_service['deduct_interest'] = 0;
            // }

            $opcode = $request->opcode;
            $id_loan_service = $request->id_loan_service ?? 0 ;

            $requirements = $request->requirements;

            // return $this->push_requirements(123,$requirements);

            if(($loan_service['id_terms_condition'] ?? 0)  > 0){
                $tc = $this->TCDetails($loan_service['id_terms_condition'],($post_save_as == 1)?0:$id_loan_service,$loan_service['ls_interest_rate'],$loan_service['ls_loan_protection']);

                $terms = $tc['terms'];
                $request->merge(['terms' => $terms]);
            }elseif($loan_service['id_loan_payment_type'] == 2 && $loan_service['id_one_time_type'] == 2){
                $loan_service['start_month_period'] =0;
                $terms = array([
                    "period" =>$loan_service['end_month_period'],
                    "terms_token" => null,
                    "interest_rate" => $loan_service['ls_interest_rate'],
                    "loan_protection_rate" =>$loan_service['ls_loan_protection']
                ]);


                $request->merge(['terms' => $terms]);                
               

        
            }

            $validation = $this->validate_inputs($request);
            // return $validation;
            if($validation['is_invalid_input']){
                $data['RESPONSE_CODE'] = "INVALID_INPUT";
                $data['INVALID'] = $validation;

                return response($data);
            }
       
            // return $net_pay;

            if($opcode == 0 || $post_save_as == 1){ // insert

                // return $loan_service['name'];

                if(!$data['credential']->is_create){
                    $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                    $data['message'] = "You dont have a privilege to save this";
                    return response($data);
                }


                $name_count = DB::table('loan_service')->where('name',$loan_service['name'])->count();
                if($name_count > 0){
                    $data['RESPONSE_CODE'] = "DUPLICATE_NAME";
                    $data['message'] = "Duplicate Loan Service Name Found";

                    return response($data);
                }


                DB::table('loan_service')
                ->insert($loan_service);

                $id_loan_service = DB::table('loan_service')->max('id_loan_service');

                //update loan token
                 DB::table('loan_service')->where('id_loan_service',$id_loan_service)->update(['loan_token' => DB::raw("concat(id_loan_service,LEFT(MD5(NOW()), 15))")]);
            }else{
                if(!$data['credential']->is_edit){
                    $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                    $data['message'] = "You dont have a privilege to save this";
                    return response($data);
                }

                $name_count = DB::table('loan_service')->where('name',$loan_service['name'])->where('id_loan_service','<>',$id_loan_service)->count();
                if($name_count > 0){
                    $data['RESPONSE_CODE'] = "DUPLICATE_NAME";
                    $data['message'] = "Duplicate Loan Service Name Found";

                    return response($data);
                }
          
                // return $loan_service;

                DB::table('loan_service')
                ->where('id_loan_service',$id_loan_service)
                ->update($loan_service);

                //DELETE TERMS AND NET PAY

                DB::select("DELETE terms,net_pay FROM terms
                    LEFT JOIN net_pay on terms.id_loan_service = net_pay.id_loan_service
                    WHERE terms.id_loan_service = ?",[$id_loan_service]);

                DB::table('loan_service_approvers')
                ->where('id_loan_service',$id_loan_service)
                ->delete();

                DB::table('loan_service_requirements')
                ->where('id_loan_service',$id_loan_service)
                ->delete();
            }
            //Insert Loan Approver
            $approver_array = array();
            for($i=0;$i<count($loan_approvers);$i++){
                $approver_array[]=[
                    'id_cms_privileges'=>$loan_approvers[$i],
                    'id_loan_service' => $id_loan_service
                ];
            }

            if(count($approver_array) > 0){
                DB::table('loan_service_approvers')
                ->insert($approver_array);
            }

            for($i=0;$i<count($terms);$i++){
                $term_insert =[];
                foreach($terms[$i] as $key=>$val){
                    $term_insert[$key] = $val;
                    if($post_save_as == 1){
                        if($key == "terms_token"){
                            $term_insert[$key] = null;
                        }
                    }
                }
                $term_insert['id_loan_service'] = $id_loan_service;

                DB::table('terms')->insert($term_insert);
                $id_terms = DB::table("terms")->max("id_terms");
                // update term token if insert function
                // if($opcode == 0){
                    DB::table('terms')->where('id_terms',$id_terms)->whereNull('terms_token')->update(['terms_token' => DB::raw("concat(id_terms,LEFT(MD5(NOW()), 15))")]);
                // }
            }
            if(isset($net_pay)){
                $this->push_net_pay($id_loan_service,$net_pay);
            }

            if(isset($requirements)){
                $this->push_requirements($id_loan_service,$requirements);
            }    

            // return 123;
            $data["RESPONSE_CODE"] = "success";
            $data['id_loan_service'] = $id_loan_service;
            return $data;
            return response($loan_service);
        // }
    }
    public function TCDetails($id_terms_condition,$id_loan_service,$interest_rate,$loan_protection_rate){
        $data['details'] = DB::table('terms_conditions_details')->select(DB::raw("MAX(up_to_terms) as terms_max"))->where('id_terms_condition',$id_terms_condition)->first();

        $terms = array();


        for($i=1;$i<=$data['details']->terms_max;$i++){
            $terms[]=[
                'period'=>null,
                'terms_token'=>null,
                'terms'=>$i,
                'interest_rate'=>$interest_rate,
                'loan_protection_rate'=>$loan_protection_rate
            ];
        }

        if($id_loan_service > 0){
            $ls_terms = DB::table('terms')->where('id_loan_service',$id_loan_service)->get();
            $g = new GroupArrayController();

            $ls_terms = $g->array_group_by($ls_terms,['terms']);
            foreach($terms as $c=>$t){
                if(isset($ls_terms[$t['terms']])){
                    $terms[$c]['terms_token'] = $ls_terms[$t['terms']][0]->terms_token;
                    // $terms[$c]['interest_rate'] = $ls_terms[$t['terms']][0]->interest_rate;
                    // $terms[$c]['loan_protection_rate'] = $ls_terms[$t['terms']][0]->loan_protection_rate;
                }
            }
        }
        $data['terms'] = $terms;

        return $data;
        dd($terms);
    }
    public function push_net_pay($id_loan_service,$net_pay){
        $net_pay_push = array();
        if($net_pay == "") return;
        for($i=0;$i<count($net_pay);$i++){
            $temp_push = [];
            foreach($net_pay[$i] as $key=>$val){
                $temp_push[$key] = $val;
            }
            $temp_push['id_loan_service'] = $id_loan_service;
            array_push($net_pay_push,$temp_push);
        }
        DB::table('net_pay')->insert($net_pay_push);
        return $net_pay_push;
    }

    public function push_requirements($id_loan_service,$requirements){
        $requirements_push = array();
        if($requirements == "") return;
        for($i=0;$i<count($requirements);$i++){
            $temp_push = [];
            foreach($requirements[$i] as $key=>$val){
                $temp_push[$key] = $val;
            }
            $temp_push['id_loan_service'] = $id_loan_service;
            array_push($requirements_push,$temp_push);
        }
        DB::table('loan_service_requirements')->insert($requirements_push);
        return $requirements_push;
    }
    public function view($id_loan_service){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/loan_service');

        if(!$data['credential']->is_view && !$data['credential']->is_edit){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['opcode'] = 1;
        $data['membership_types'] = DB::table('membership_type')->get();


        $data['disbursement_type'] = DB::table('disbursement_type')->get();
        $data['interest_method'] = DB::table('interest_method')->get();
        $data['loan_payment_type'] = DB::table('loan_payment_type')->get();
        $data['period'] = DB::table('period')->get();
        $data['repayment_period'] = DB::table('repayment_period')->get();

        $data['requirements'] = DB::table('loan_service_requirements')->orderBy('id_loan_service_requirements')->where('id_loan_service',$id_loan_service)->get();

        $data['loan_approvers'] = collect(DB::table('loan_service_approvers')->where('id_loan_service',$id_loan_service)->get())->pluck('id_cms_privileges');


        $data['details'] = DB::table('loan_service')
        ->select('loan_service.*','charges_group.name as charge_name',DB::raw("GETCHARGESGROUP(loan_service.id_charges_group) as charges_list"))
        ->LeftJOin('charges_group','charges_group.id_charges_group','loan_service.id_charges_group')
        ->where('loan_service.id_loan_service',$id_loan_service)
        ->first();

        $data['head_title'] = "#$id_loan_service - ".$data['details']->name;

        // return $data;
        $data['terms'] = DB::table('terms')
        ->select("terms.*")
        ->where('id_loan_service',$id_loan_service)
        ->orDerby('id_terms')
        ->get();
        $data['net_pay'] = DB::table('net_pay')
        ->select("net_pay.*")

        ->where('id_loan_service',$id_loan_service)
        ->orDerby('id_net_pay')
        ->get();
        $data['charges_group'] = DB::table('charges_group')
        ->select(DB::raw("name,id_charges_group"))
        ->where('active',1)
        ->get();

        $data['approvers'] = DB::table('cms_privileges')->select('id','name')->where('is_approver',1)->get();

        $data['terms_condition'] = DB::table('terms_condition')
                                   ->get();

        $data['terms_condition_details'] = DB::table('terms_conditions_details')->select(DB::raw("MAX(up_to_terms) as terms_max"))->where('id_terms_condition',$data['details']->id_terms_condition)->first();
        $data['one_time_type'] = DB::table('one_time_type')->get();
        // dd($data);
        // $g = new GroupArrayController();
        // $data['net_pay'] = $g->array_group_by($net_pay,['id_terms']);
        // return $data['net_pay'];
        // return $data['net_pay'][39][0]->prin_min;

        return view('loan_service.loan_service_form',$data);
        // return $data;
    }
    public function parseTermsCondition(Request $request){
        $id_terms_condition = $request->id_terms_condition;
        $data['details'] = DB::table('terms_conditions_details')->select(DB::raw("MAX(up_to_terms) as terms_max"))->where('id_terms_condition',$id_terms_condition)->first();

        return response($data);
    }
    public function parseCharges(Request $request){
        if($request->ajax()){
            $id_charges_group = $request->id_charges_group;
            $data['charges'] = DB::table('charges_group')->select(DB::raw("GETCHARGESGROUP(charges_group.id_charges_group) as charges"))->where('id_charges_group',$id_charges_group)->where('active',1)->first();

            return response($data);

        }
    }

    public function validate_inputs($request){
        $output = array();
        $is_invalid_input = false;
        $invalid_input = array();

        $loan_service = $request->loan_service;

        $input_term_period_not_zero = ['terms'];
        $input_term_period_not_empty = ['interest_rate'];

        $input_requirement_not_empty = ['req_description'];
        
            // $number = ['min_amount','max_amount','default_amount','cbu_amount','no_comakers'];
        // ,'no_comakers'
        $number = ['min_amount','max_amount','default_amount'];
        // $inputs = ['name','id_disbursement_type','id_interest_method','id_loan_payment_type','id_charges_group','avail_age'];

        $inputs = ['name','id_disbursement_type','id_interest_method','id_loan_payment_type','avail_age'];

        if($loan_service['id_loan_payment_type'] == 2){
            array_push($inputs,'start_month_period');
            array_push($inputs,'end_month_period');
            array_push($inputs,'repayment_schedule');
        }else{
            array_push($inputs,'id_term_period');
            array_push($inputs,'id_interest_period');
            array_push($inputs,'id_repayment_period');            
        }

        if($loan_service['is_renew_no_pay'] == 1){
            array_push($number,'renew_payments');
        }

        for($i=0;$i<count($inputs);$i++){
            if($loan_service[$inputs[$i]] == ""){
                array_push($invalid_input,$inputs[$i]);
                $is_invalid_input = true;
            }            
        }

        for($i=0;$i<count($number);$i++){
            if($loan_service[$number[$i]] == 0 || $loan_service[$number[$i]] == ""){
                array_push($invalid_input,$number[$i]);
                $is_invalid_input = true;
            }
        }

        //NET PAY VALIDATION
        $net_pay_invalid = array();
        $net_pay = $request->net_pay;
        // $input_net_pay = ['prin_min','prin_max','net_min','net_max'];
        $input_net_pay = ['prin_max','net_min'];
        // if(isset($net_pay)){
        //     for($i=0;$i<count($net_pay);$i++){
        //         $temp = array();
        //         foreach($input_net_pay as $k){
        //             $val = $net_pay[$i][$k];
        //             if($val == 0 || $val == ""){
        //                 $is_invalid_input = true;
        //                 array_push($temp,$k);
        //             }
        //             if(count($temp) > 0){
        //                 $net_pay_invalid[$i] = $temp;
        //             }
        //         }
        //     }  
        // }

        //PERIOD/TERMS VALIDATION
        $terms = $request->terms;
        $terms_invalid = array();
        for($i=0;$i<count($terms);$i++){
            $temp = array();
            foreach($terms[$i] as $key=>$val){
                //FOR EMPTY FIELDS
                if(in_array($key,$input_term_period_not_empty)){
                    if($val == ""){
                        $is_invalid_input = true;
                        array_push($temp,$key);
                    }
                } 

                //GREATER THAN ZERO
                if(in_array($key,$input_term_period_not_zero)){
                    if($val == 0 || $val == ""){
                        $is_invalid_input = true;
                        array_push($temp,$key);
                    }
                }              
            }

            if(count($temp) > 0){
                $terms_invalid[$i] = $temp;
            }
        }

        $requirements = $request->requirements;
        $requirements_invalid = array();

        if(isset($requirements)){    
            for($i=0;$i<count($requirements);$i++){
                $temp = array();
                foreach($requirements[$i] as $key=>$val){
                    //FOR EMPTY FIELDS
                    if(in_array($key,$input_requirement_not_empty)){
                        if($val == ""){
                            $is_invalid_input = true;
                            array_push($temp,$key);
                        }
                    } 
                 
                }

                if(count($temp) > 0){
                    $requirements_invalid[$i] = $temp;
                }
            }
        }

        $output['is_invalid_input'] = $is_invalid_input;
        $output['invalid_input'] = $invalid_input;
        $output['net_pay_invalid'] = $net_pay_invalid;
        $output['terms_invalid'] = $terms_invalid;
        $output['requirements_invalid'] = $requirements_invalid;


        return $output;
    }
    public function search_loan_service(Request $request){
        if($request->ajax()){
            $search = $request->term;   
            if(strlen($search) < 3){
                $data['loan_services'] = array();
                return response($data);
            }
            $data['loan_services'] = DB::table('loan_service')
            ->select(DB::raw("name as tag_value,id_loan_service as tag_id"))
            ->where(function ($query) use ($search){
                $query->where(DB::raw("name"), 'like', "%$search%");
            })
            ->get();
            return response($data);
        }
    }

}

