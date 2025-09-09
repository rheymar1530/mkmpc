<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;


class InvestmentProductController extends Controller
{
    public function fee_types(){
        return DB::table('inv_fee_type')->get();
    }
    public function index(){
        $data['head_title'] = "Investment Product";
        $data['investment_products'] = DB::select("SELECT ip.id_investment_product,ip.product_name,concat(FORMAT(min_amount,2),' - ',FORMAT(max_amount,2)) as amount,iit.description as interest_type,
                                                    iip.description as interest_period,iwp.description as withdrawable_part,if(ip.status=10,'Inactive','') as status,if(ip.member_only =1,'Yes','') as member_only,iip.unit
                                                    FROM investment_product as ip
                                                    LEFT JOIN inv_interest_type as iit on iit.id_interest_type = ip.id_interest_type
                                                    LEFT JOIN inv_interest_period as iip on iip.id_interest_period = ip.id_interest_period
                                                    LEFT JOIN inv_withdrawable_part as iwp on iwp.id_withdrawable_part = ip.id_withdrawable_part
                                                    ORDER BY ip.id_investment_product DESC;");
        return view('investment_product.index',$data);
    }
    public function create(){

        $data = $this->selections();
        $data['head_title'] = "Create Investment Product";

        $data['opcode'] = 0;



        return view('investment_product.form',$data);
    }

    public function view($id_investment_product){
        $data = $this->selections();
        $data['opcode'] = 1; 
        $data['head_title'] = "Investment Product #$id_investment_product";
        $data['details'] = DB::table('investment_product')->where('id_investment_product',$id_investment_product)->first();

        $data['head_title'] = $data['details']->product_name." [#$id_investment_product]";
        // dd($data['details']);


        $data['selected_approver'] = DB::table('investment_product_approver')->select('id_cms_privileges')->where('id_investment_product',$id_investment_product)->get()->pluck('id_cms_privileges')->toArray();

        // $data['fee_types'] = DB::select("SELECT ift.id_fee_type,ift.description,ifnull(ipf.amount,0) as amount FROM inv_fee_type as ift
        //                                  LEFT JOIN investment_product_fees as ipf on ipf.id_investment_product = ? AND ipf.id_fee_type = ift.id_fee_type",[$id_investment_product]);

        $data['fees'] = DB::table('investment_product_fees')
                        ->where('id_investment_product',$id_investment_product)
                        ->get();

        $data['terms'] = DB::table('investment_product_terms')
                         ->where('id_investment_product',$id_investment_product)
                         ->get();


        return view('investment_product.form',$data);
    }

    function selections(){
        $data['interest_types'] = DB::table('inv_interest_type')->get();
        $data['interest_periods'] = DB::table('inv_interest_period')->get();
        $data['withdraw_parts'] = DB::table('inv_withdrawable_part')->get();
        $data['fee_calculations'] = DB::table('fee_calculation')->get();
        $data['calculated_fee_base'] = DB::table('calculated_fee_base')->get();
        $data['fee_types'] = $this->fee_types();

        // dd($data['fee_types']);

        // dd($data['fee_types']);
        // $data['fee_types'] = DB::table('inv_fee_type')->get();
        $data['approvers'] = DB::table('cms_privileges')->select('id','name')->where('is_approver',1)->get();

        $data['chart_accounts'] = DB::table('chart_account')->select(DB::raw("concat(account_code,' - ',description) as account,id_chart_account"))->get();



        return $data;        
    }

    public function post(Request $request){

        $data['RESPONSE_CODE'] = "SUCCESS";
        $opcode = $request->opcode;
        $id_investment_product = $request->id_investment_product ?? 0;
        $product_details = $request->product_details;
        $approvers = $request->approvers ?? [];
        $save_mode = $request->save_mode ?? 0;
        $terms = $request->terms ?? [];
        $deleted = $request->deleted_terms ?? [];
   
        $opcode = ($save_mode == 1)?0:$opcode;
        $fees_obj = $request->fees ?? [];




        if(count($approvers) == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please Select at least 1 Approver";

            return response($data);
        }

        if(count($terms) == 0){
            $data['RESPONSE_CODE']  = "ERROR";
            $data['message'] = "No Terms Selected";

            return response($data);
        }

        //validate terms
        $term_field = ['terms','interest_rate'];

        $post_terms_obj = array();
        $update_terms_obj = array();
        $invalid_terms = array();
        for($i=0;$i<count($terms);$i++){
            $t = array();
            foreach($term_field as $tf){
                $t[$tf] = $terms[$i][$tf];
                if(!isset($terms[$i][$tf]) || $terms[$i][$tf] <= 0){
                    if(!isset($invalid_terms[$i])){
                        $invalid_terms[$i] = array();
                    }
                    array_push($invalid_terms[$i],$tf);
                }
            }
            $id_investment_product_terms = $terms[$i]['id'] ?? 0;

            if($id_investment_product_terms == 0 || $save_mode == 1){
                 array_push($post_terms_obj,$t);
            }else{
                $update_terms_obj[$id_investment_product_terms] = $t;
            }           
        }

        if(count($invalid_terms) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Values";
            $data['term_field'] = $invalid_terms;

            return response($data);
        }
        $product_details['id_withdrawable_part'] = 2; //interest only

        // dd($product_details);
        
        $product_details_validator = array(
            "product_name" => ['col_name'=>'product_name','required'=>true,'number'=>false],
            "min_amount" => ['col_name'=>'min_amount','required'=>true,'number'=>true],
            "max_amount" => ['col_name'=>'max_amount','required'=>true,'number'=>true],
            "interest_type" => ['col_name'=>'id_interest_type','required'=>true,'number'=>false],
            "interest_period" => ['col_name'=>'id_interest_period','required'=>true,'number'=>false],
            // "interest_rate" => ['col_name'=>'interest_rate','required'=>true,'number'=>true],
            // "duration" => ['col_name'=>'duration','required'=>true,'number'=>true],
            "id_withdrawable_part" => ['col_name'=>'id_withdrawable_part','required'=>true,'number'=>false],
            "can_be_with_maturity" => ['col_name'=>'withdraw_before_maturity','required'=>false,'number'=>false],
            "member_only" => ['col_name'=>'member_only','required'=>false,'number'=>false],
            // "withdrawal_fee" => ['col_name'=>'withdrawal_fee','required'=>false,'number'=>true],
            // "monthly_fee" => ['col_name'=>'monthly_fee','required'=>false,'number'=>true],
            // "yearly_fee" => ['col_name'=>'yearly_fee','required'=>false,'number'=>true],
            // "one_time" => ['col_name'=>'one_time','required'=>false,'number'=>true],
            "id_chart_account" => ['col_name'=>'id_chart_account','required'=>true,'number'=>true],
            'interest_chart_account'=>['col_name'=>'interest_chart_account','required'=>true,'number'=>true],
        );

        $product_obj = array();
        $invalid = array();

        foreach($product_details_validator as $post_key=>$v){
            $required = $v['required'];
            $is_number = $v['number'];
            $valid = true;

            if($required){

                if(!isset($product_details[$post_key])){
                    $valid = false;
                }else{
                    $value =$product_details[$post_key];
                    if($is_number){
                        $valid = ($value <=0)?false:true;
                    }else{
                        $valid = ($value =="")?false:true;
                    }                    
                }
                if(!$valid){
                    array_push($invalid,$post_key);
                }
            }

            $product_obj[$v['col_name']] = $product_details[$post_key];
        }

        if(count($invalid) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please fill required fields";
            $data['fields'] = $invalid;

            return response($data);
        }

        if($product_obj['min_amount'] > $product_obj['max_amount']){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Amount Range";
            $data['fields'] =['min_amount','max_amount'];

            return response($data);
        }

        if($opcode == 0){ //ADD
            DB::table('investment_product')
            ->insert($product_obj);

            $id_investment_product = DB::table('investment_product')->max('id_investment_product');
        }else{
            $product_obj['status'] = $request->status;
            DB::table('investment_product')
            ->where('id_investment_product',$id_investment_product)
            ->update($product_obj);

            DB::table('investment_product_approver')
            ->where('id_investment_product',$id_investment_product)
            ->delete();

            DB::table('investment_product_fees')
            ->where('id_investment_product',$id_investment_product)
            ->delete();
        }

        // Terms
        for($i=0;$i<count($post_terms_obj);$i++){
            $post_terms_obj[$i]['id_investment_product'] = $id_investment_product;
        }
        DB::table('investment_product_terms')
        ->insert($post_terms_obj);

        if(count($update_terms_obj) > 0){
            foreach($update_terms_obj as $id=>$up_data){
                DB::table('investment_product_terms')
                ->where('id_investment_product_terms',$id)
                ->where('id_investment_product',$id_investment_product)
                ->update($up_data);
            }
        }
        // // Fees
        for($i=0;$i<count($fees_obj);$i++){
            $fees_obj[$i]['id_investment_product'] = $id_investment_product;
        }
        DB::table('investment_product_fees')
        ->insert($fees_obj);

        // Approvers
        $approver_obj = array();
        foreach($approvers as $ap){
            $approver_obj[]=[
                'id_cms_privileges'=>$ap,
                'id_investment_product'=>$id_investment_product
            ];
        }
        DB::table('investment_product_approver')
        ->insert($approver_obj);


        DB::table('investment_product_terms')
        ->where('id_investment_product',$id_investment_product)
        ->whereIn('id_investment_product_terms',$deleted)
        ->delete();

        $data['id_investment_product'] = $id_investment_product;

        return response($data);



        // return $id_investment_product;

        return "SUCCESS";

    }
}
