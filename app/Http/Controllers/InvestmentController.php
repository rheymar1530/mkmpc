<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\InvestmentModel;
use App\MySession;
use Carbon\Carbon;
Use App\CRVModel;
Use App\JVModel;
use App\CredentialModel;

class InvestmentController extends Controller
{
    private $post_keys = ['id_investment_product','id_investment_product_terms','id_interest_type','id_interest_period','interest_rate','terms','id_withdrawable_part','withdraw_before_maturity','member_only','withdrawal_fee','one_time'];
    // 'monthly_fee','yearly_fee',

    public function index(Request $request){
        // $this->GenerateOR(26);

        // return env('DB_DATABASE');
        /******
         * FILTERING:
         *  STATUS
         *  DATE
         * 
         * ******/
        // CRVModel::InvestmentCRV(27);
        // return;
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());

        $data['isAdmin'] = MySession::isAdmin();
        $data['current_date'] =$cur_date= MySession::current_date();
        $data['head_title'] = "Investments";
        $data['investment_products'] = DB::table('investment_product')->select(DB::raw('id_investment_product,product_name'))->where('status','<>',10)->get();

        $data['fill_date_start'] = $request->filter_start_date ?? date('Y-m-d', strtotime('-6 months'));
        $data['fill_date_end'] = $request->filter_end_date ?? MySession::current_date();

        $data['sel_filter_date_type'] = isset($request->filter_date_type)?$request->filter_date_type:1;

        $data['date_check'] = isset($request->date_check)?$request->date_check:0;

        $default = 2;
        $data['current_tab'] = $request->status ?? $default;
        $data['current_tab'] = is_numeric($data['current_tab'])?intval($data['current_tab']):$data['current_tab'];





        $with_filter = false;
        $data['investments'] = DB::table('investment as i')
        ->select(DB::raw("i.id_investment,@is_matured:=if(i.maturity_date <= '$cur_date',1,0) as is_matured,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as investor,concat(i.terms,' ',iip.unit,'(s)') as terms,concat(i.interest_rate,'%') as interest_rate,DATE_FORMAT(i.date_created,'%m/%d/%Y') as date_created,if(i.close_request in (1,2),8,if(i.renewal_status = 1,8,i.status)) as status_code,@q:=
            CASE WHEN i.status = 0 THEN 'Draft'
            WHEN i.status = 1 THEN 'Pre-approved'
            WHEN i.status = 2 THEN concat('Active',if(@is_matured=1,' (Matured)',''))
            WHEN i.status = 3 THEN 'Rejected'
            WHEN i.status = 4 THEN 'Cancelled'
            WHEN i.status = 5 THEN 'Closed'
            ELSE 'Closed' END as status,DATE_FORMAT(i.maturity_date,'%m/%d/%Y') as maturity_date,i.amount,DATE_FORMAT(i.investment_date,'%m/%d/%Y') as investment_date,ip.product_name,i.or_number,if(i.close_request in (1,2),'Withdrawal Processing',if(i.renewal_status = 1,'Renewal Processing',@q)) as status,
            CASE 
            WHEN i.id_withdrawable_part = 1 THEN (i.amount)
            WHEN i.id_withdrawable_part = 2 THEN (if('$cur_date' >= i.maturity_date,i.amount,0))
            ELSE i.amount
            END +
            CASE 
            WHEN i.id_withdrawable_part = 1 THEN (if('$cur_date' >= i.maturity_date,SUM(interest_amount),0))
            WHEN i.id_withdrawable_part = 2 THEN (SUM(interest_amount))
            ELSE SUM(interest_amount)
            END - getInvestmentTotalWithdraw(i.id_investment,0,1,1) AS withdrawable,    
            if((i.withdraw_before_maturity=1 OR (i.withdraw_before_maturity = 0 AND i.maturity_date <= '$cur_date' )),1,0) as is_withdrawable"))
        ->leftJoin('member as m','m.id_member','i.id_member')
        ->leftJoin('inv_interest_period as iip','iip.id_interest_period','i.id_interest_period')
        ->leftJoin('investment_product as ip','ip.id_investment_product','i.id_investment_product')
        ->leftJoin('investment_table as it',function($join){
            $join->on('it.id_investment','i.id_investment')
            ->where('it.date','<=',DB::raw("ifnull(i.date_closed,'".MySession::current_date()."')"));
        })
        ->where(function($query) use ($data,$request){
            if(!MySession::isAdmin()){
                $query->where('i.id_member',MySession::myId());
            }else{
                //other filtering options
                if(isset($request->id_member)){
                    $query->where('i.id_member',$request->id_member);
                }
                if($data['date_check'] == 1){
                    if($data['sel_filter_date_type'] == 1){ // date created
                        $query->whereRaw("DATE(i.date_created) >= ?",[$data['fill_date_start']])
                        ->whereRaw("DATE(i.date_created) <= ?",[$data['fill_date_end']]);
                        $with_filter = true;
                    }elseif($data['sel_filter_date_type'] == 2){
                        $query->whereRaw("DATE(i.investment_date) >= ?",[$data['fill_date_start']])
                        ->whereRaw("DATE(i.investment_date) <= ?",[$data['fill_date_end']]);   
                        $with_filter = true;                 
                    }
                }
                if(isset($request->filter_investment_product) && $request->filter_investment_product > 0){
                    $query->where('i.id_investment_product',$request->filter_investment_product);
                }
            }
        })
        ->where(function($query) use ($data){

            if($data['current_tab'] !== "All"){
                if($data['current_tab'] < 5){
                    $fil_status = $data['current_tab'] == 4 ? [4,5] : [$data['current_tab']];
                    $query->whereIn('i.status',$fil_status)->where('renewal_status',0)->where('close_request',0);
                    
                }elseif($data['current_tab'] == 5){
                    //Closed
                    $query->whereIn('i.status',[5]);
                }else{
                    //with withdrawal/renewal processing
                    $query->whereIn('i.close_request',[1,2])->orWhere('i.renewal_status',1)->where('i.status',2);
                }
            }
        })


        // ->where(function($query) use ($request,$with_filter,$data){ 

        //     if(isset($request->filter_status) && $request->filter_status != "ALL"){
        //         $query->where('i.status',$request->filter_status);
        //         $with_filter = true;
        //     }
        //     if(isset($request->id_member)){
        //         $query->where('i.id_member',$request->id_member);
        //         $with_filter = true;
        //     }
        //     if($data['date_check'] == 1){
        //         if($data['sel_filter_date_type'] == 1){ // date created
        //             $query->whereRaw("DATE(i.date_created) >= ?",[$data['fill_date_start']])
        //             ->whereRaw("DATE(i.date_created) <= ?",[$data['fill_date_end']]);
        //             $with_filter = true;
        //         }elseif($data['sel_filter_date_type'] == 2){
        //             $query->whereRaw("DATE(i.investment_date) >= ?",[$data['fill_date_start']])
        //             ->whereRaw("DATE(i.investment_date) <= ?",[$data['fill_date_end']]);   
        //             $with_filter = true;                 
        //         }
        //     }
        //     // if(isset($request->filter_date_type)){
        //     //     if($request->filter_date_type == 1){ // date created
        //     //         $query->whereRaw("DATE(i.date_created) >= ?",[$request->filter_start_date])
        //     //         ->whereRaw("DATE(i.date_created) <= ?",[$request->filter_end_date]);
        //     //         $with_filter = true;
        //     //     }elseif($request->filter_date_type == 2){
        //     //         $query->whereRaw("DATE(i.investment_date) >= ?",[$request->filter_start_date])
        //     //         ->whereRaw("DATE(i.investment_date) <= ?",[$request->filter_end_date]);   
        //     //         $with_filter = true;                 
        //     //     }
        //     // }
        //     if(isset($request->filter_investment_product) && $request->filter_investment_product > 0){
        //         $query->where("i.id_investment_product",$request->filter_investment_product);
        //         $with_filter = true;
        //     }
        //     // if(!$with_filter){
        //     //     if($request->filter_status != "ALL"){
        //     //         $query->where('i.status',0);
        //     //     }
        //     // }
        // })
        ->groupBy('i.id_investment')
        ->orDerby('i.id_investment','DESC')
        ->get();

        $data['selected_member'] =DB::table('member as m')
        ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,id_member"))
        ->where('id_member',$request->id_member)
        ->first();


        $counts = DB::table('investment as i')
        ->select(DB::raw("CASE 
            WHEN status in (3,4) THEN 3
            WHEN (close_request in (1,2) OR renewal_status =1) AND status = 2 THEN 6
            ELSE status END as status_in,COUNT(*) as count"))
        ->where(function($query) use ($data,$request){
            if(!MySession::isAdmin()){
                $query->where('i.id_member',MySession::myId());
            }else{
                //other filtering options
                if(isset($request->id_member)){
                    $query->where('i.id_member',$request->id_member);
                }
                if($data['date_check'] == 1){
                    if($data['sel_filter_date_type'] == 1){ // date created
                        $query->whereRaw("DATE(i.date_created) >= ?",[$data['fill_date_start']])
                        ->whereRaw("DATE(i.date_created) <= ?",[$data['fill_date_end']]);
                        $with_filter = true;
                    }elseif($data['sel_filter_date_type'] == 2){
                        $query->whereRaw("DATE(i.investment_date) >= ?",[$data['fill_date_start']])
                        ->whereRaw("DATE(i.investment_date) <= ?",[$data['fill_date_end']]);   
                        $with_filter = true;                 
                    }
                }
                if(isset($request->filter_investment_product) && $request->filter_investment_product > 0){
                    $query->where('i.id_investment_product',$request->filter_investment_product);
                }
            }
        })
        ->groupBy('status_in')
        ->get();

        // dd($data);
        $g = new GroupArrayController();
        $data['investment_counts'] = $g->array_group_by($counts,['status_in']);


        return view('investment.index-card',$data);
    }
    public function create(Request $request){
        $data['products'] = DB::table('investment_product')
        ->select("id_investment_product","product_name")
        ->where('status','<>',10)
        ->get();

        $data['head_title'] = "Create Investment";
        $data['opcode'] = 0;
        return view('investment.form',$data);
    }

    public function view($id_investment,Request $request){
        $data['opcode'] = 1;
        $data['head_title'] = "Investment ID# $id_investment";
        $data['investment_app_details'] = DB::table('investment as i')
        ->select(DB::raw("i.id_investment,i.id_investment_product,i.id_investment_product_terms,i.amount,i.id_member,concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,i.status,i.id_member,i.cancellation_remarks,i.or_number"))
        ->leftJoin('member as m','m.id_member','i.id_member')
        ->where('id_investment',$id_investment)
        ->first();

        if(!MySession::isAdmin() && $data['investment_app_details']->id_member != MySession::myId()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['STATUS_MODE'] = 1;

        $edit_mode = $request->edit ?? 0;
        /******
         * Viewing only if:
         * Account is Admin and edit parameter if false or null
         * Investment Status is not draft
         * 
         * 
         * 
         * *******/ 

        $data['benefactors'] = DB::table('investment_benefactor')
        ->where('id_investment',$id_investment)
        ->get();



        if($data['investment_app_details']->status > 0 || (MySession::isAdmin() && !$edit_mode)){
            return $this->parseView($id_investment,$data['benefactors']);
        }

        $data['products'] = DB::table('investment_product')
        ->select("id_investment_product","product_name")
        ->where('status','<>',10)
        ->get();        

        $r = new Request([
            'id_investment_product'=>$data['investment_app_details']->id_investment_product
        ]);

        $details_t = $this->parseTerms($r);
        $data['terms'] = $details_t['terms'];
        $data['limit'] = $details_t['limit'];

        // dd($data);



        $data['STATUS_LISTS'] = array('4'=>'Cancel');

        return view('investment.form',$data);
    }
    public function parseView($id_investment,$benefactors){
        /**************
         * INVESTMENT STATUS
         * 0 - Draft
         * 1 - 1st Confirmation (Optional)
         * 2 - Approved
         * 3 - Rejected
         * 4 - Cancelled
         * 5 - Closed
         * *********/





        $cur_date = MySession::current_date();
        $inv_details =  $data['investment_app_details'] = DB::table('investment as i')
        ->select(DB::raw("i.id_investment,@is_matured:=if(i.maturity_date <= '$cur_date',1,0) as is_matured,i.id_investment_product,i.amount,i.id_member,concat(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,i.status,DATE_FORMAT(i.date_created,'%m/%d/%Y') as date_created,i.status as status_code,@q:=
            CASE WHEN i.status = 0 THEN 'Draft'
            WHEN i.status = 1 THEN 'Pre-approved'
            WHEN i.status = 2 THEN  concat('Active',if(@is_matured=1,' (Matured)',''))
            WHEN i.status = 3 THEN 'Rejected'
            WHEN i.status = 4 THEN 'Cancelled'
            WHEN i.status = 5 THEN 'Closed'
            ELSE 'Closed' END as status,i.id_investment_product_terms,i.cancellation_remarks,DATE_FORMAT(i.date_cancelled,'%m/%d/%Y %r') as date_cancelled,getInvestmentTotalWithdraw(i.id_investment,0,1,2) as withdrawn_principal,getInvestmentTotalWithdraw(i.id_investment,0,1,3) as withdrawn_interest,
            CASE 
            WHEN i.id_withdrawable_part = 1 THEN (i.amount)
            WHEN i.id_withdrawable_part = 2 THEN (if('$cur_date' >= i.maturity_date,i.amount,0))
            ELSE i.amount
            END AS principal_avail,
            CASE 
            WHEN i.id_withdrawable_part = 1 THEN (if('$cur_date' >= i.maturity_date,SUM(interest_amount),0))
            WHEN i.id_withdrawable_part = 2 THEN (SUM(interest_amount))
            ELSE SUM(interest_amount)
            END AS interest_avail,    
            if((i.withdraw_before_maturity=1 OR (i.withdraw_before_maturity = 0 AND i.maturity_date <= '$cur_date' )),1,0) as is_withdrawable,i.id_cash_receipt_voucher,i.or_number,i.close_request,i.renewal_status,i.id_prev_investment,i.id_new_investment,i.id_journal_voucher,if(i.close_request in (1,2),'Withdrawal Processing',if(i.renewal_status = 1,'Renewal Processing',@q)) as status,if(i.close_request in (1,2),8,if(i.renewal_status = 1,8,i.status)) as status_code,if(i.maturity_date <= '$cur_date',1,0) as is_matured"))
        // ->leftJoin('investment_table as it','it.id_investment','i.id_investment')
        ->leftJoin('investment_table as it',function($join){
            $join->on('it.id_investment','i.id_investment')
            ->where('it.date','<=',MySession::current_date());
        })
        ->leftJoin('member as m','m.id_member','i.id_member')
        ->where('i.id_investment',$id_investment)
        ->first();

        // dd($inv_details);

        $data['benefactors'] = $benefactors;
        $data['head_title'] = "Investment ID# $id_investment";

        // dd($data);

        $data['show_inv_full_transaction'] = ($inv_details->status_code == 2 || $inv_details->status_code == 5)?true:false;

        $data['w_principal'] = $w_principal = $inv_details->principal_avail - $inv_details->withdrawn_principal;
        $data['w_interest'] = $w_interest =  $inv_details->interest_avail - $inv_details->withdrawn_interest;

        $data['w_full'] = (($data['w_principal'] == 0)?$inv_details->amount:$data['w_principal']) + $w_interest;

        if($inv_details->close_request >=  1 && $inv_details->close_request <= 2){
            $data['close_request_details'] = $inv_full = DB::table('investment_close_request as ic')->select('ic.total_amount','ic.id_investment_withdrawal_batch')->where('ic.id_investment',$id_investment)->whereIn('ic.status',[0,1,2])->orderBy('ic.status','DESC')->first();

            $data['full_w_request'] = $inv_full->total_amount;     
        }

        // dd($data);
        $data['withdrawables_details'] = array();

        $data['withdrawables'] = $w_principal+$w_interest;

        if($w_principal > 0){
            $data['withdrawables_details']['Principal'] = $w_principal;
        }
        if($w_interest > 0){
            $data['withdrawables_details']['Interest'] = $w_interest;
        }

        $id_inv = ($inv_details->status_code==0)?0:$id_investment;

        $data['STATUS_MODE'] = 2;
        $data['allow_status_update'] = ($inv_details->status_code <= 1)?true:false;

        $is_renewal = $inv_details->id_prev_investment == 0?false:true;

        $data['investment_data'] = InvestmentModel::ComputeInvestment($inv_details->id_member,$inv_details->id_investment_product,$inv_details->id_investment_product_terms,$id_inv,$inv_details->amount,true,$is_renewal);


        $condition_status = array();
        $approvers = DB::table('investment_product_approver')
        ->where('id_investment_product',$inv_details->id_investment_product)
        ->get();
        $approver_count = count($approvers);
        $data['SHOW_INVESTMENT_DATE'] = false;

        if($inv_details->status_code==0){
            // DRAFT
            if($approver_count == 2){
                // account privilege must be Credit Committee
                if(MySession::myPrivilegeId() == 8){
                    $condition_status = array(
                        '1'=>'Pre-approved'
                    );                   
                }
            }else{
                if(MySession::myPrivilegeId() == $approvers[0]->id_cms_privileges){
                    $condition_status = array(
                        '2'=>'Approved'
                    );                       
                }
                $data['SHOW_INVESTMENT_DATE'] = true;
            }
        }elseif($inv_details->status_code == 1){
            // APPROVED
            if(MySession::myPrivilegeId() == $approvers[1]->id_cms_privileges){
                $condition_status = array(
                    '2'=>'Approved'
                );
            }
            $data['SHOW_INVESTMENT_DATE'] = true;              
        }
        $def_status = array(
            '4' =>'Rejected',
            // '5' => 'Cancelled'
        );


        $data['STATUS_LISTS']=$status_list = $condition_status+(($inv_details->status_code <= 1)?$def_status:[]);
        $data['allow_edit'] =false;


        if(MySession::isAdmin() && $inv_details->status_code==0 && $inv_details->id_prev_investment == 0){
            $data['allow_edit'] = true;
        }

        // if($inv_details->status_code == 5){
            // $data['investment_data']['WITH_TABLE'] = false;
        $data['withdrawal_summary'] = DB::table('investment_withdrawal as iw')
        ->select(DB::raw("SUM(iw.principal) as principal,SUM(iw.interest) as interest,SUM(iw.principal+iw.interest) as total"))
        ->leftJoin('investment_withdrawal_batch as ib','ib.id_investment_withdrawal_batch','iw.id_investment_withdrawal_batch')
        ->where('iw.status','<>',10)
        ->whereIn('ib.type',[1,2,4])
        ->where('iw.id_investment',$id_investment)
        ->first();

        $data['renewed_amount'] = DB::table('investment_withdrawal as iw')
        ->select(DB::raw("ifnull(SUM(iw.principal+iw.interest),0) as total"))
        ->leftJoin('investment_withdrawal_batch as ib','ib.id_investment_withdrawal_batch','iw.id_investment_withdrawal_batch')
        ->where('iw.status','<>',10)
        ->where('ib.type','=',3)
        ->where('iw.id_investment',$id_investment)
        ->first()->total;

        $data['show_withdrawal_details'] = !isset($data['withdrawal_summary']->total)?false:true;

            // if(in_array($inv_details->status_code,[3,4])){
            //     $data['investment_data']['WITH_TABLE'] = false;
            // }


        // }

        $data['confirm_close_request'] = ($inv_details->close_request == 1 && MySession::isAdmin())?true:false;


        // dd($status_list);
        return view('investment.view',$data);
    }

    public function compute(Request $request){
        // $data['investment_data'] = InvestmentModel::ComputeInvestment($request->investment_product,0,$request->amount);
        return response($data);
    }

    public function compute_frame(Request $request){
        $with_table = $request->with_table ?? 1;

        if(MySession::isAdmin()){
            $id_member = $request->investor ?? MySession::myId();
        }else{
            $id_member = MySession::myId();
        }
        
        $data['investment_data'] = InvestmentModel::ComputeInvestment($id_member,$request->investment_product,$request->id_terms,0,$request->amount,$with_table);

        return view('investment.frame',$data);
    }

    public function post(Request $request){
        // return $this->update_investment_data(6);
        // $investment_info = InvestmentModel::ComputeInvestment(0,5,0);
        // return;

        $opcode = $request->opcode;
        $id_investment = $request->id_investment ?? 0;
        $benefactors = $request->benefactors ?? [];
        $from_renewal  = $request->from_renewal ?? false;


        if(MySession::isAdmin()){
            $id_member = $request->investor ?? MySession::myId();
        }else{
            $id_member = MySession::myId();
        }

        $id_investment_product = $request->investment_product;
        $id_investment_product_terms = $request->id_investment_product_terms;


        if(!$from_renewal){
            $valid_benefactor = count($benefactors) == 0?false:true;
            foreach($benefactors as $b){
                if($b['name'] == "" || !isset($benefactors)){
                  $valid_benefactor = false;
              }
          }

          if(!$valid_benefactor){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please Enter at least 1 benefactor";

            return response($data);
        }
    }




    $amount = $request->amount ?? 0;

    $post_keys = $this->post_keys;
    $investment_info = InvestmentModel::ComputeInvestment($id_member,$id_investment_product,$id_investment_product_terms,0,$amount,true,$from_renewal);


    




    $data['RESPONSE_CODE'] = "SUCCESS";
        // VALIDATIONS

    if($investment_info['STATUS'] == "ERROR" && !$from_renewal){
        $data['RESPONSE_CODE'] = "ERROR";
        $data['message'] = $investment_info['message'];

        return response($data);
    }

        // dd($investment_info);
        // END VALIDATION
    $investment_obj = array();
    $inv_product_details = $investment_info['INVESTMENT_PROD'];
    foreach($post_keys as $key){
        $investment_obj[$key] = $inv_product_details->{$key};
    }

    if($opcode == 0){
        $investment_obj['id_member'] = $id_member;
        $investment_obj['amount'] = $amount;
        $investment_obj['user_id'] = MySession::mySystemUserId();

        DB::table('investment')->insert($investment_obj);

        $id_investment = DB::table('investment')->where('user_id',MySession::mySystemUserId())->max('id_investment');
    }else{
        $investment_obj['amount'] = $amount;
        DB::table('investment')->where('id_investment',$id_investment)->update($investment_obj);

        DB::table('investment_benefactor')->where('id_investment',$id_investment)->delete();

    }

        //benefactors
    $benefactor_obj = array();
    foreach($benefactors as $b){
        if($b['name'] != ""){
            $benefactor_obj[]= [
                'id_investment'=>$id_investment,
                'name'=>$b['name'],
                'relationship'=>$b['relationship']
            ];                
        }
    }

    DB::table('investment_benefactor')
    ->insert($benefactor_obj);

        // PUSH FEES
    $this->push_fees($investment_info['FEES'],$id_investment);
        // PUSH INVESTMENT TABLE
    $this->set_investment_table($id_investment,$investment_info['INVESTMENT_TABLE']);

    $data['INVESTMENT_ID'] = $id_investment;

    if($from_renewal){
        return $data;
    }


    return response($data);
    dd($inv_product_details);
}


public function push_fees($fees,$id_investment){


    $fee_obj = array();

    $fees = json_decode(json_encode($fees),true);
    DB::table('investment_fee')->where('id_investment',$id_investment)->delete();
    foreach($fees as $f){
        $fee_obj[] = [
            'id_investment'=>$id_investment,
            'id_fee_type'=>$f['id_fee_type'],
            'value'=>$f['value'],
            'id_fee_calculation'=>$f['id_fee_calculation'],
            'id_calculated_fee_base'=>$f['id_calculated_fee_base'],
            'amount'=>$f['amount'],

        ];
    }

    DB::table('investment_fee')
    ->insert($fee_obj);
        // return;
        // DB::table('investment_fee')->where('id_investment',$id_investment)->delete();
        // DB::select(" INSERT INTO investment_fee (id_investment,id_fee_type,amount)
        //     SELECT ? as id_investment,id_fee_type,amount
        //     FROM investment_product_fees
        //     WHERE id_investment_product = ?",[$id_investment,$id_investment_product]);        
}

public function set_investment_table($id_investment,$investment_table){
    $out = array();
    foreach($investment_table as $inv){
        $out[]=[
            'id_investment'=>$id_investment,
            'date'=>$inv['date'],
            'interest_amount'=>$inv['interest_amount'],
            'end_interest'=>$inv['end_interest'],
            'end_amount'=>$inv['end_amount'],

        ];
    }
    DB::table('investment_table')->where('id_investment',$id_investment)->delete();
    DB::table('investment_table')
    ->insert($out);
}

public function update_investment_status(Request $request){


    $id_investment = $request->id_investment;
    $status = $request->status;
    $status_mode = $request->status_mode;
    $investment_date = $request->investment_date;
    $beg_confirmation = $request->beg_confirmation ?? false;

    $inv_details = DB::table('investment')->where('id_investment',$id_investment)->first();


    $data['message'] = "Investment Status Successfully Updated";

    if($inv_details->close_request == 1){
        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['message'] = "Withdrawal request status successfully updated";
        if($status == 1){
            $this->confirm_force_withdraw($id_investment);
        }else{
            $this->cancel_withdrawal_request($id_investment,$request->reason);
        }

        return response($data);
    }


    if(!MySession::isAdmin()){
        $status = 4;
    }else{
        $status = ($status == 4)?3:$status;
    }
    
    $reason = $request->reason;

    $data['RESPONSE_CODE'] = "SUCCESS";
    $data['ID_INVESTMENT'] = $id_investment;
    $data['is_released'] = false;

    $allow_update = false;
    


    if($inv_details->status > 1){
        $data['RESPONSE_CODE']="ERROR";
        $data['message'] = "Invalid Request";

        return response($data);
    }

    if($status == 3 || !MySession::isAdmin()){
            // cancellation
        if($inv_details->status == 0){
           $f= $this->update_investment_data($id_investment);
       }
       $this->cancel_investment($id_investment,$status,$reason);

       if($inv_details->id_prev_investment > 0){
            //Renewal
        $this->ConfirmRenewal($inv_details->id_prev_investment,$id_investment,$status);
    }
    return response($data);
}


if($inv_details->status == 0){
    $approvers_data = DB::table('investment_product_approver')->select(DB::raw($inv_details->id_investment." as id_investment,id_cms_privileges"))
    ->where('id_investment_product',$inv_details->id_investment_product)
    ->orDerby('id_investment_product_approver')
    ->get();

    $allowed_priv = (count($approvers_data) > 1)?8:$approvers_data[0]->id_cms_privileges;
    $allowed_status = (count($approvers_data) > 1)?[1,3]:[2,3];

    $id_cms_privileges = $allowed_priv;

    if(!in_array($status,$allowed_status)){
        $data['RESPONSE_CODE'] = "ERROR";
        $data['message'] = "Invalid Status";
        return response($data);                
    }

    DB::table('investment_approver')->where('id_investment',$id_investment)->delete();
    DB::table('investment_approver')->insert(json_decode(json_encode($approvers_data),true));

    if(MySession::myPrivilegeId() != $allowed_priv){
        $data['RESPONSE_CODE'] = "ERROR";
        $data['message'] = "Status Privilege Error";
        return response($data);
    }

    $allow_update = true;
    $out=$this->update_investment_data($id_investment);

    if($out['STATUS'] == "ERROR"){
        $data['RESPONSE_CODE'] = "ERROR";
        $data['message'] = $out['message'];
        $allow_update = false;
        return response($data);
    }
}elseif($inv_details->status == 1){
    $id_cms_privileges = 9;
    if(MySession::myPrivilegeId() != 9){
        $data['RESPONSE_CODE'] = "ERROR";
        $data['message'] = "Status Privilege Error";
        return response($data);                
    }

    if($status != 2){
        $data['RESPONSE_CODE'] = "ERROR";
        $data['message'] = "Invalid Status";
        return response($data);                  
    }
    $allow_update = true;
}

if($allow_update){
    DB::table('investment_approver')
    ->where('id_investment',$id_investment)
    ->where('id_cms_privileges',$id_cms_privileges)
    ->update([
        'id_user'=>MySession::mySystemUserId(),
        'date_updated'=>DB::raw("now()")
    ]);

    $investment_date = ($status == 2)?$investment_date:null;
    DB::table('investment')
    ->where('id_investment',$id_investment)
    ->update(['status'=>$status,
      'investment_date'=>$investment_date]);

    if($status == 2){
           // update investment table
     $mat = $this->update_investment_table($id_investment,$investment_date,$inv_details->id_interest_period);
     DB::table('investment')
     ->where('id_investment',$id_investment)
     ->update(['maturity_date'=>$mat]);

     $data['is_released'] = true;

     if($inv_details->id_prev_investment == 0 && !$beg_confirmation){
        CRVModel::InvestmentCRV($id_investment);
    }
}
if($inv_details->id_prev_investment > 0){
            //Renewal
    $data['is_released'] = false;
    $this->ConfirmRenewal($inv_details->id_prev_investment,$id_investment,$status);

    if($status == 2){
        JVModel::InvestmentRenewalJV($id_investment);
    }
}
}

return response($data);
}

public function update_investment_table($id_investment,$investment_date,$interest_period){
    switch ($interest_period){
            case 1: //Monthly
            $var = 1;
            break;
            case 2: //Quarterly
            $var = 3;
            break;
            default:
            $var = 12;
            break;
        }

        

        $table = DB::table('investment_table')
        ->select('id_investment_table','date')
        ->where('id_investment',$id_investment)
        ->orDerby('date')
        ->get();

        // $investment_date = Carbon::parse($investment_date)->addMonths(1)->format('Y-m-d');
        foreach($table as $tb){
            $investment_date = Carbon::parse($investment_date)->addMonths($var)->format('Y-m-d');
            DB::table('investment_table')
            ->where('id_investment',$id_investment)
            ->where('id_investment_table',$tb->id_investment_table)
            ->update(['date'=>$investment_date]);

        }

        return $investment_date;

        dd($table);
    }
    public function cancel_investment($id_investment,$status,$reason){
        DB::table('investment')
        ->where('id_investment',$id_investment)
        ->update(['cancellation_remarks'=>$reason,
          'status'=>$status,
          'cancelled_by'=>MySession::mySystemUserId(),
          'date_cancelled'=>DB::raw("now()")]);
    }
    public function update_investment_data($id_investment,$bypass_validation=false){
        // after status update
        $investment_product = DB::table('investment')->select(DB::raw("id_investment_product,amount,id_investment_product_terms,id_member,id_prev_investment"))->where('id_investment',$id_investment)->first();

        $is_renewal = ($investment_product->id_prev_investment > 0)?true:false;

        $investment_info = InvestmentModel::ComputeInvestment($investment_product->id_member,$investment_product->id_investment_product,$investment_product->id_investment_product_terms,0,$investment_product->amount,true,$is_renewal);

        if($investment_info['STATUS'] == "SUCCESS"){
            $investment_table = $investment_info['INVESTMENT_TABLE'];
            $inv_product_details = $investment_info['INVESTMENT_PROD'];

            $investment_obj = array();
            $post_keys = $this->post_keys;
            foreach($post_keys as $key){
                $investment_obj[$key] = $inv_product_details->{$key};
            }

            DB::table('investment')
            ->where('id_investment',$id_investment)
            ->update($investment_obj);

            // PUSH FEES
            $this->push_fees($investment_info['FEES'],$id_investment);

            // PUSH INVESTMENT TABLE
            $this->set_investment_table($id_investment,$investment_table);

            // dd($investment_obj);
        }

        return $investment_info;
    }

    public function parseTerms(Request $request){
        // if($request->ajax()){
        $id_investment_product = $request->id_investment_product;


        $details = DB::table('investment_product')
        ->select(DB::raw('min_amount,max_amount'))
        ->where('id_investment_product',$id_investment_product)
        ->first();

        $data['limit'] = "₱".number_format($details->min_amount,2)." - "."₱".number_format($details->max_amount,2);

        $data['terms'] = DB::table('investment_product_terms as ipt')
        ->select('ipt.id_investment_product_terms','ipt.terms','ipt.interest_rate','iip.unit')
        ->leftJoin('investment_product as ip','ip.id_investment_product','ipt.id_investment_product')
        ->leftJoin('inv_interest_period as iip','iip.id_interest_period','ip.id_interest_period')
        ->where('ipt.id_investment_product',$id_investment_product)
        ->get();
        return $data;
        return response($data);
        // }
    }

    public function parseWithdrawalSummary($id_investment){
        $data['withdrawals'] = DB::select("Select 
            iw.id_investment_withdrawal as reference,iw.principal,iw.interest,iw.total_amount,if(iw.status=0,'Draft',if(iw.status=1,'Released','Cancelled')) as status,ifnull(DATE_FORMAT(iwb.date_released,'%m/%d/%Y'),'-') as status_date,DATE_FORMAT(iw.date_created,'%m/%d/%Y') as date_created,iw.status as status_code,if(iwb.type=1,'Withdrawal',if(iwb.type=2,'Early Termination',if(iwb.type=3,concat('Renewal'),'Beginning Withdrawal'))) as type,if(iwb.type=3,i.id_new_investment,0) as id_new_investment
            FROM investment_withdrawal as iw
            LEFT JOIN investment_withdrawal_batch as iwb on iwb.id_investment_withdrawal_batch = iw.id_investment_withdrawal_batch
            LEFT JOIN investment as i on i.id_investment = iw.id_investment
            WHERE iw.id_investment = ? and iw.status <> 10;",[$id_investment]);
        // dd($data);
        return view('investment.withdrawal_history_frame',$data);

        dd($data);
    }

    public function GenerateOR($id_investment){

        $id_cash_receipt = DB::table('investment')
        ->where('id_investment',$id_investment)
        ->max('id_cash_receipt');

        // dd($id_cash_receipt);


        if($id_cash_receipt  == 0){
            DB::select("INSERT INTO cash_receipt (date_received,id_paymode,payee_type,id_member,or_no,reference_no,total_payment,type,payment_remarks)
                SELECT investment_date as date_received,1 as id_paymode,1 as payee_type,i.id_member,i.or_number as or_no,i.id_investment as reference_no,amount as total_payment,4 as type,concat(ip.product_name,' (ID# ',i.id_investment,')') as payment_remarks
                FROM investment as i
                LEFT JOIN investment_product as ip on ip.id_investment_product=i.id_investment_product
                WHERE i.id_investment= ? ;",[$id_investment]);    
            // AND i.or_number is not null

            $id_cash_receipt = DB::table('cash_receipt')
            ->select('id_cash_receipt')
            ->where('type',4)
            ->where('reference_no',$id_investment)
            ->where('status','<>',10)
            ->max('id_cash_receipt') ?? 0;

            DB::table('investment')->where('id_investment',$id_investment)->update(['id_cash_receipt'=>$id_cash_receipt]);

        }else{
            DB::table('cash_receipt_details')
            ->where('id_cash_receipt',$id_cash_receipt)
            ->delete();
        }


        $id_payment_type = config("variables.investment");


        DB::select(" INSERT INTO cash_receipt_details (id_cash_receipt,id_payment_type,amount,description,reference)
            SELECT $id_cash_receipt as id_cash_receipt,$id_payment_type as id_payment_type,i.amount,concat(ip.product_name,' (ID# ',i.id_investment,')') as description,i.id_investment as reference
            FROM investment as i
            LEFT JOIN investment_product as ip on ip.id_investment_product=i.id_investment_product
            WHERE i.id_investment =?;",[$id_investment]);

        return $id_cash_receipt;

    }

    public function check_or(Request $request){
        if($request->ajax()){
            $investment = DB::table('investment')->where('id_investment',$request->id_investment)->first();

            $or_number = ($investment->or_number ?? 0);
            
            if($or_number == 0){
                $data['RESPONSE_CODE'] = "SHOW_OR_ENTRY";


                return response($data);
            }else{
                $data['RESPONSE_CODE'] = "SHOW_PRINT";
                $data['id_cash_receipt'] = $investment->id_cash_receipt;

                return response($data);
            }

            return response($data);
        }
    }

    public function post_or(Request $request){
        if($request->ajax()){
            $id_investment = $request->id_investment;
            $or_no = $request->or_no;
            $or_opcode = $request->or_opcode;

            $data['RESPONSE_CODE'] = "SUCCESS";
            $id_cash_receipt = 0;

            if($or_opcode == 0){
                DB::table('investment')
                ->where('id_investment',$id_investment)
                ->update(['or_number'=>$or_no]);

                $id_cash_receipt = $this->GenerateOR($id_investment);
                CRVModel::InvestmentCRV($id_investment);
            }

            $data['id_cash_receipt'] = $id_cash_receipt;

            return response($data);
        }
    }

    public function force_withdraw(Request $request){
        //Status ; 0 no request; 1 - with request; 2 - approved
        $id_investment = $request->id_investment;
        $reason = $request->reason;

        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['message'] = "Investment withdrawal request successfully posted";

        $details = DB::table('investment')->select(DB::raw("close_request"))->where('id_investment',$id_investment)->first();

        if($details->close_request != 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Request";

            return response($data);
        }

        DB::table('investment')
        ->where('id_investment',$id_investment)
        ->update([
            'close_request'=>1,
        ]);

        $inv_details = $this->inv_details($id_investment);

        DB::table('investment_close_request')
        ->insert([
            'id_investment'=>$id_investment,
            'reason'=>$reason,
            'principal' =>($inv_details->principal-$inv_details->withdrawn_principal),
            'interest' => ($inv_details->interest-$inv_details->withdrawn_interest),
            'total_amount'=>($inv_details->principal-$inv_details->withdrawn_principal)+($inv_details->interest-$inv_details->withdrawn_interest),
        ]);
        if(MySession::isAdmin()){
            $this->confirm_force_withdraw($id_investment);
        }
        return response($data);


    }

    function confirm_force_withdraw($id_investment){
        $cur_date = MySession::current_date();
        $inv_details = $this->inv_details($id_investment);
        $data['RESPONSE_CODE'] = "SUCCESS";

        if($inv_details->close_request >= 2){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Request";

            return $data;
        }


        $w_request = DB::table('investment_close_request')->select('principal','interest','total_amount')->where('id_investment',$id_investment)->where('status',0)->first();



        // $total_amount = ($inv_details->principal+$inv_details->interest) - ($inv_details->withdrawn_principal+$inv_details->withdrawn_interest);

        // $total_principal =($inv_details->principal-$inv_details->withdrawn_principal);
        // $total_interest = ($inv_details->interest-$inv_details->withdrawn_interest);
        // $total_amount = $total_principal + $total_interest;

        $total_principal = $w_request->principal;
        $total_interest = $w_request->interest;
        $total_amount = $total_principal + $total_interest;

        $batch_obj = [
            'type'=>2,
            'total_amount' => $total_amount,
            'id_member'=>$inv_details->id_member,
            'id_user'=>MySession::myId(),
            'id_user_status'=>MySession::myId(),
            'close_request'=>1,
            'ref'=>$id_investment
        ];

        DB::table('investment_withdrawal_batch')
        ->insert($batch_obj);

        $id_investment_withdrawal_batch = DB::table('investment_withdrawal_batch')->where('close_request',1)->where('ref',$id_investment)->max('id_investment_withdrawal_batch');

        $withdrawal_obj = [ 
            'id_investment_withdrawal_batch' =>$id_investment_withdrawal_batch,
            'id_investment'=>$inv_details->id_investment,
            'id_member'=>$inv_details->id_member,
            'principal'=> $total_principal,
            'interest'=> $total_interest,
            'total_amount'=> $total_amount,
            'id_user'=>MySession::myId()
        ];

        DB::table('investment_withdrawal')
        ->insert($withdrawal_obj);


        DB::table('investment_close_request')
        ->where('id_investment',$id_investment)
        ->where('status',0)
        ->update(['status'=>1,'id_investment_withdrawal_batch'=>$id_investment_withdrawal_batch,'status_date'=>DB::raw("now()")]);

        DB::table('investment')
        ->where('id_investment',$id_investment)
        ->update(['close_request'=>2]);

        return $data;

    }

    public function inv_details($id_investment){
        $cur_date = MySession::current_date();
        $inv_details =  DB::table('investment as i')
        ->select(DB::raw("i.id_investment,i.amount as principal,i.id_member,i.status,getInvestmentTotalWithdraw(i.id_investment,0,1,2) as withdrawn_principal,getInvestmentTotalWithdraw(i.id_investment,0,1,3) as withdrawn_interest,
            (SUM(interest_amount)) as interest,close_request,renewal_status,i.id_member,i.id_investment_product,i.id_investment_product_terms,i.investment_date,if(i.maturity_date <= '$cur_date',1,0) as is_matured"))

        ->leftJoin('investment_table as it',function($join){
            $join->on('it.id_investment','i.id_investment')
            ->where('it.date','<=',MySession::current_date());
        })
        ->leftJoin('member as m','m.id_member','i.id_member')
        ->where('i.id_investment',$id_investment)
        ->first();   
        return $inv_details; 
    }
    public function cancel_withdrawal_request($id_investment,$reason){
        DB::table('investment_close_request')
        ->where('id_investment',$id_investment)
        ->where('status','<>',10)
        ->update([
            'status'=>10,
            'status_date'=>DB::raw("now()"),
            'rejection_remarks'=>$reason
        ]);

        DB::table('investment')
        ->where('id_investment',$id_investment)
        ->where('status','<>',10)
        ->update([
            'close_request'=>0
        ]);
    }

    public function RenewInvestment(Request $request){
        $id_investment = $request->id_investment;
        $current_date = MySession::current_date();
        $inv_details = $this->inv_details($id_investment);



        // check if investment is matured
        if($inv_details->is_matured == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Investment is not yet matured";

            return response($data);
        }

        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['message'] = "Investment successfully renewed";
        
        if($inv_details->status != 2){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Investment Status is not active";
            return response($data);
        }

        if($inv_details->renewal_status != 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Investment has renewal request";
            return response($data);            
        }

        $withdrawal_count = DB::table('investment_withdrawal')
        ->where('id_investment',$id_investment)
        ->where('status',0)
        ->count();

        if($withdrawal_count > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Investment has pending withdrawal request";

            return response($data);              
        }

        $total_amount = ($inv_details->principal+$inv_details->interest) - ($inv_details->withdrawn_principal+$inv_details->withdrawn_interest);

        // dd($total_amount);

        $investment_prod = DB::table('investment_product')
        ->select(DB::raw("min_amount,max_amount"))
        ->where('id_investment_product',$inv_details->id_investment_product)
        ->first();

        if($total_amount < $investment_prod->min_amount || $total_amount > $investment_prod->max_amount){
            $out_range ="₱".number_format($investment_prod->min_amount,2)." - ₱".number_format($investment_prod->max_amount,2);

            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invesment amount within $out_range";

            return response($data);
        }

        
        $request_param = array(
            'investor'=>$inv_details->id_member,
            'investment_product'=>$inv_details->id_investment_product,
            'id_investment_product_terms'=>$inv_details->id_investment_product_terms,
            'amount'=>$total_amount,
            'opcode'=>0,
            'id_investment'=>0,
            'from_renewal'=> true
        );



        $post = $this->post(new Request($request_param));



        $id_new_investment = $post['INVESTMENT_ID'];
        // $id_new_investment = 10;

        //update the new investment link to prev investment id
        DB::table('investment')
        ->where('id_investment',$id_new_investment)
        ->update(['id_prev_investment'=>$id_investment]);

        //push benefactor
        DB::select(
            "INSERT INTO investment_benefactor (id_investment,name,relationship)
            SELECT $id_new_investment,name,relationship
            FROM investment_benefactor
            WHERE id_investment=$id_investment"
        );

        //update previous investment new investment id link
        DB::table('investment')
        ->where('id_investment',$id_investment)
        ->update(['id_new_investment'=>$id_new_investment,'renewal_status'=>1]);

        //Generate invenstment renewal data
        DB::table('investment_renewal')
        ->insert([
            'id_investment_new'=>$id_new_investment,
            'id_investment_prev'=>$id_investment,
            'principal'=>$inv_details->principal-$inv_details->withdrawn_principal,
            'interest'=>$inv_details->interest-$inv_details->withdrawn_interest,
            'total_amount'=>$total_amount,
        ]);


        $data['message2'] = "Please wait until the renewed investment has been approved";
        $data['NEW_ID_INVESTMENT'] = $id_new_investment;

        return response($data);
    }

    public function ConfirmRenewal($id_investment_prev,$id_investment_new,$status){

        $inv_details = $this->inv_details($id_investment_prev);
        if($status == 2){
            // Approved

            //Withdrawal Data
            $w_request = DB::table('investment_renewal')
            ->select('principal','interest','total_amount',DB::raw("DATE(date_created) as date"))
            ->where('id_investment_new',$id_investment_new)  
            ->where('id_investment_prev',$id_investment_prev)
            ->where('status',0)
            ->first();
            $total_principal = $w_request->principal;
            $total_interest = $w_request->interest;
            $total_amount = $total_principal + $total_interest;

            $batch_obj = [
                'type'=>3,
                'date_released'=>$inv_details->investment_date,
                'total_amount' => $total_amount,
                'id_member'=>$inv_details->id_member,
                'id_user'=>MySession::myId(),
                'id_user_status'=>MySession::myId(),
                'close_request'=>0,
                'ref'=>$id_investment_new,
                'status'=>1
            ];

            DB::table('investment_withdrawal_batch')
            ->insert($batch_obj);

            $id_investment_withdrawal_batch = DB::table('investment_withdrawal_batch')->where('type',3)->where('ref',$id_investment_new)->max('id_investment_withdrawal_batch');

            $withdrawal_obj = [ 
                'id_investment_withdrawal_batch' =>$id_investment_withdrawal_batch ?? 0,
                'id_investment'=>$inv_details->id_investment,
                'id_member'=>$inv_details->id_member,
                'principal'=> $total_principal,
                'interest'=> $total_interest,
                'total_amount'=> $total_amount,
                'status'=> 1,
                'id_user'=>MySession::myId()
            ];            
            
            DB::table('investment_withdrawal')
            ->insert($withdrawal_obj);

            DB::table('investment')
            ->where('id_investment',$id_investment_prev)
            ->update([
                'status'=>5,
                'renewal_status'=>2,
                'date_closed'=>$w_request->date
            ]);

            DB::table('investment_renewal')
            ->where('id_investment_new',$id_investment_new)  
            ->where('id_investment_prev',$id_investment_prev)
            ->update(['status'=>1]);           


        }elseif($status == 3 || $status == 4){
            DB::table('investment_renewal')
            ->where('id_investment_new',$id_investment_new)  
            ->where('id_investment_prev',$id_investment_prev)
            ->update(['status'=>10]);   

            DB::table('investment')
            ->where('id_investment',$id_investment_prev)
            ->update([
                'renewal_status'=>0,
                'id_new_investment'=>0
            ]);        
        }
        return "SUCCESS";

    }
}
