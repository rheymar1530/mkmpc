<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use DB;
use App\Member;
use App\Loan;
use App\MySession;
use Dompdf\Dompdf;
use App\CredentialModel;
use App\WebHelper;
use PDF;
use Dompdf\Options;

use App\Mail\LoanConfirmationMail;


class LoanApprovalController extends Controller
{
    public function test_priv(){
        return 8;
    }

    public function test_mail(){
        $id_loan = 969;
        Mail::send(new LoanConfirmationMail($id_loan)); 

        $details = $data['details'] = DB::table('loan')
        ->select(DB::raw("loan.id_loan,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,loan.principal_amount,loanStatus(loan.status) as loan_status,loan.interest_rate,loan.status,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) loan_service,m.email"))
        ->leftJoin('member as m','m.id_member','loan.id_member')
        ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
        ->where('loan.id_loan',$id_loan)
        ->first();

        return view('emails.loan_confirmation',$data);
    }
    
    public function getLoanDueDate($date_released,$terms){
        $date=date_create($date_released);
        $day = date_format($date,"d");
        $month = date_format($date,"m");
        $due_year = $year = date_format($date,"Y");
        // if($day <= 15){
        //     $due_month = $month;
        //     $due_year = $year;
        // }else{
        //     $due_month = $month+1;

        //     if($due_month > 12){
        //         $due_month = $due_month-12;
        //         $due_year++;
        //     }
        // }
        $due_month = $month+1;
        if($due_month > 12){
            $due_month = $due_month-12;
            $due_year++;
        }
        $output = array();
        for($i=1;$i<=$terms;$i++){
            if($due_month == 2){
                $due_date = date("Y-m-t", strtotime("$due_year-$due_month"));
            }else{
                $due_month = (int)$due_month;
                $due_month = ($due_month < 10)?"0$due_month":$due_month;
                $due_date ="$due_year-$due_month-30";
            }
            $output["P".$i] = $due_date;

            $due_month++;
            if($due_month > 12){
                $due_month = $due_month - 12;
                $due_year++;
            }
        }

        return $output;
    }
    
    public function add_loan_due_date($id_loan,$due_dates){
        $loan_table = DB::table('loan_table')
        ->select(DB::raw("id_loan,count,term_code,repayment_amount,interest_amount,fees,total_due,is_paid"))
        ->where('id_loan',$id_loan)
        ->orderBy('count')
        ->get();
        $loan_table_update = array();
        $mat_date = null;

        foreach($loan_table as $lt){
            $temp = array();
            foreach($lt as $key=>$val){
                $temp[$key] = $val;
            }
            $temp['due_date'] = $due_dates[$lt->term_code];

            $mat_date =$temp['due_date'];
            array_push($loan_table_update,$temp);
        }

        DB::table('loan_table')
        ->where('id_loan',$id_loan)
        ->delete();

        DB::table('loan_table')
        ->insert($loan_table_update);

        //UPDATE MATURITY DATE
        DB::table('loan')
        ->where('id_loan',$id_loan)
        ->update(['maturity_date'=>$mat_date]);

        return "success";
    }

    public function view_approval($loan_token){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/loan');

  
        // Loan::GenerateCDV(2843);

        if(!$data['credential']->is_confirm && !$data['credential']->is_confirm2){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $loan = DB::table('loan')
        ->select("loan.id_loan","loan.status","loan.created_by","loan.id_loan_service","loan.year_due")
        ->where('loan.loan_token',$loan_token)
        ->first();
        $data['loan_status'] = [];
        $approvers = $this->getApproversLoanService($loan->id_loan_service,$loan->status);


        $for_release= false;
        foreach($approvers as $ind=>$allowed){
            if($allowed){
                $data['loan_status'][$ind]= ($ind==2)?'Approved, for Releasing':'Released';
                $for_release = ($ind == 3)?true:false;
            }
        }


        $loan_status= $data['loan_status'];

        // return $this->GenerateCashReceipt($loan->id_loan,55511122);
        $data['privilegeID'] = MySession::myPrivilegeId();
        $data['for_releasing'] = false;

        $data['SHOW_EDIT_BUTTON'] = false;

        if($loan->status == 0){ // if loan status is submitted or processing
            if(MySession::isAdmin()){
                $data['SHOW_EDIT_BUTTON'] = true;
            }else{
                $data['SHOW_EDIT_BUTTON'] = ($loan->created_by == MySession::mySystemUserId())?true:false;
            }
            $ls = DB::table('loan_service')
            ->select('id_loan_payment_type','id_one_time_type')
            ->where('id_loan_service',$loan->id_loan_service)
            ->first();
            $interest_multiplier = 1;
            if($ls->id_loan_payment_type == 2 && $ls->id_one_time_type == 2){
                $one = Loan::OneTimeOpen(['id_loan_service'=>$loan->id_loan_service,'due_year'=>$loan->year_due]);
                
                $interest_multiplier = $one['duration'];
            }     

            $data['for_approval'] = true;
            $loan_app = new LoanApplicationController();

            // "ls.name"
            $service_details = $data['service_details'] = DB::table('loan as ap_loan')
            ->select(DB::raw("getLoanServiceName(ap_loan.id_loan_payment_type,ls.name,ap_loan.terms) as name"),"ap_loan.id_loan","ls.id_loan_service","terms.interest_rate","terms.terms","terms.period","ls.cbu_amount","ls.is_deduct_cbu",DB::raw("ap_loan.principal_amount,terms.loan_protection_rate,ls.id_term_period,ls.id_interest_period,ls.id_interest_method,if(ls.id_loan_payment_type =1,concat(ap_loan.terms,' ',p.description),getMonth(terms.period)) as terms_desc,terms.terms_token,ls.min_amount,ls.max_amount,ls.id_loan_payment_type,concat(getMonth(ls.end_month_period),' ',ls.repayment_schedule,', ',YEAR(curdate())) as due_date,ap_loan.id_member,terms.loan_protection_rate,ls.id_charges_group,FormatName(member.first_name,member.middle_name,member.last_name,member.suffix) as member_name,ap_loan.status,ap_loan.loan_token,ap_loan.loan_remarks,LoanStatus(ap_loan.status) as loan_status,ap_loan.status as status_code,ls.deduct_interest,$interest_multiplier as month_duration,terms.interest_rate*$interest_multiplier as interest_rate,terms.interest_rate as interest_show,ls.with_maker_cbu,ls.maker_min_cbu"))
            ->leftJoin('loan_service as ls','ls.id_loan_service','ap_loan.id_loan_service')
            ->leftJoin('member','member.id_member','ap_loan.id_member')
            ->leftJoin('terms','ap_loan.terms_token','terms.terms_token')
            ->where('ap_loan.loan_token',$loan_token)
            ->leftJoin('period as p','p.id_period','ls.id_term_period')
            ->first();

            $data['head_title'] = "Loan #".$service_details->id_loan." (".$service_details->loan_status.")";

            $data['loan_status'][5] = "Disapproved";

            // dd($approvers,$service_details->status_code);
            $priv =  MySession::myPrivilegeId();
            // $priv = $this->test_priv();
            // return 
            // if($service_details->status_code == 0){ // if submitted
            //     if(count($approvers) == 1){ // if approver count is 1 only
            //         if($approvers[0]->id_cms_privileges == $priv){
            //             $loan_status[2] = "Approved, for Releasing";
            //         }
            //     }else{
            //         if($approvers[0]->id_cms_privileges == $priv){
            //             $loan_status[1] = "Processing";
            //         }
            //     }
            //     $loan_status[5] = "Disapproved";
            // }

            // $data['loan_status'] = $loan_status;

            $charges = $loan_app->parseChargesLoan($service_details->id_loan);

            // return $charges;
            $cbu = Member::CheckCBU($data['service_details']->cbu_amount,$service_details->id_member);


   

            $previous_loan = Member::CheckPreviousLoan($service_details->id_loan_service,$service_details->id_member,$service_details->terms_token);
            $loan_parameter = [
                'id_member' =>$service_details->id_member,
                'cbu_deficient' => $cbu['difference'],
                'charges' => $charges,
                'principal_amount' =>$service_details->principal_amount,
                'interest_rate'=> $service_details->interest_rate,
                'terms' => $service_details->terms,
                'term_period' => $service_details->id_term_period,
                'interest_pediod'=>$service_details->id_interest_period,
                'interest_method' => $service_details->id_interest_method,
                'is_cbu_deduct' => $service_details->is_deduct_cbu,
                'loan_protection_rate' => $service_details->loan_protection_rate,
                'payment_type' => $service_details->id_loan_payment_type,
                'previous_loan' => $previous_loan,
                'deduct_interest'=>$service_details->deduct_interest,
                'id_loan_service'=>$service_details->id_loan_service
            ];
            $other_deductions = DB::table('loan_manual_deduction')
                                ->select('id_loan_fees','amount','remarks')
                                ->where('id_loan',$service_details->id_loan)
                                ->get();
            $other_deductions = json_decode(json_encode($other_deductions),true);
            $loan_parameter['other_deductions'] = $other_deductions;
            $loan_offset = DB::table('loan_offset as lo')
            ->leftJoin('loan','loan.id_loan','lo.id_loan_to_pay')
            ->where('lo.id_loan',$service_details->id_loan)
            ->get();

            $loan_paid = array();
            foreach($loan_offset as $lo){
                $temp = array();
                $temp['loan_token'] = $lo->loan_token;
                $temp['amount'] = $lo->amount;

                array_push($loan_paid,$temp);
            }
            if(count($loan_paid) > 0){
                $loan_parameter['loan_payment'] =Loan::parseExistingLoanBalance($service_details->id_loan_service,$service_details->terms_token,$service_details->id_member,MySession::current_date(),$loan_paid,2);
            }
            $data['loan'] = Loan::ComputeLoan($loan_parameter);
            $id_loan = $service_details->id_loan;
  
        }else{
     
            $priv =  MySession::myPrivilegeId();


            $id_loan = $loan->id_loan;
            $data = Loan::LoanDetails($id_loan);

            $data['loan_status'] =$loan_status;

            $data['banks'] = DB::table('tbl_bank')->get();

            // dd($data)

            // return $data;

            $data['service_details'] = $data['service_details'];
            $data['head_title'] = "Loan #".$data['service_details']->id_loan." (".$data['service_details']->loan_status.")";

            $data['for_approval'] = $for_release;
            $data['for_releasing'] = $for_release;
            $data['loan'] = $data;
            $loan_status[5] = "Disapproved";
            $data['loan_status'] = $loan_status;



            // if($loan->status == 1){ // if loan is processing
            //     $data['for_approval'] = true;
            //     if($priv == 9){ // Privilege is General Manager
            //         $loan_status[2] = "Approved, for Releasing";
            //     }
            //     $loan_status[5] = "Disapproved";
            //     $data['loan_status'] = $loan_status;
            //     // return "waht";
            // }elseif($loan->status == 2){ // if loan is approved/ for releasing
            //     if($priv == 8 || $priv == 9){
            //         $data['for_approval'] = true;
            //         $loan_status[3] = "Released";
            //         $data['loan_status'] = $loan_status;
            //         $data['for_releasing'] = true;
            //     } 
            // }

            if($data['service_details']->lstatus > 0){
                $data['show_repayment'] = true;
            }            
        }
        // return $data;
        $data['INVALID_APPLICATION'] = false;
        $data['ERROR_MESSAGE'] = array();
        $repayment_condition = Member::CheckRepaymentCondition($data['service_details']->id_loan_service,$data['service_details']->id_member);
        if(!$repayment_condition['isValid']){
            $data['INVALID_APPLICATION'] = true;
            array_push($data['ERROR_MESSAGE'],$repayment_condition["ERROR_MESSAGE"]);
        }
        $outsanding_overdue = Member::CheckOutstandingOverdue($data['service_details']->id_member);
        if(!$outsanding_overdue['isValid']){
            $data['INVALID_APPLICATION'] = true;
            array_push($data['ERROR_MESSAGE'],$outsanding_overdue["ERROR_MESSAGE"]);
        }
        $age_condition = Member::CheckAgeCondition($data['service_details']->id_loan_service,$data['service_details']->id_member);
        if(!$age_condition['isValid']){
            $data['INVALID_APPLICATION'] = true;
            array_push($data['ERROR_MESSAGE'],$age_condition["ERROR_MESSAGE"]);
        }

        //CBU CHECK
        $data['CBU'] = Member::CheckCBU($data['service_details']->cbu_amount,$data['service_details']->id_member);
        $data['ACCOUNT_CBU'] = Member::getCBU($data['service_details']->id_member);
        if(!$data['CBU']['is_capital_buildup_valid']){
            $data['INVALID_APPLICATION'] = true;
            array_push($data['ERROR_MESSAGE'],"CBU Required Amount: ".number_format($data['service_details']->cbu_amount,2)." || Current CBU: ".number_format($data['ACCOUNT_CBU'],2)." || CBU deficient: ".number_format($data['CBU']['difference'],2).".");
        }

        // return $data['service_details']->status;

        // return $data['ERROR_MESSAGE'];
        $data['net_pays'] = DB::table('loan_net_pay')
        ->select(DB::raw("DATE_FORMAT(period_start,'%m/%d/%Y') as period_start,DATE_FORMAT(period_end,'%m/%d/%Y') as period_end,FORMAT(amount,2) as amount"))
        ->where('id_loan',$id_loan)
        ->where('amount','>',0)
        ->orderBy('id_loan_net_pay')
        ->get();


        $data['comakers'] = DB::table('loan_comakers as lc')
        ->select(DB::raw("lc.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as name"))
        ->leftJoin('member as m','m.id_member','lc.id_member')
        ->where('id_loan',$id_loan)
        ->orDerby("id_loan_co_makers")
        ->get();


   

        $data['maker_cbu'] = array();

        if($loan->status == 0){
            if($data['service_details']->with_maker_cbu == 1){
                foreach($data['comakers'] as $count=>$m){  
                    $mCBU = Member::getCBU($m->id_member);

                    if($mCBU < $service_details->maker_min_cbu){
                        $data['maker_cbu'][$m->id_member] = $mCBU;
                    }
                    
                    // array_push($data['maker_cbu'],$mCBU);
                    // $data['maker_cbu'][$count] = $mCBU;

                }                
            }           
        }


        $data['other_lendings'] = DB::table('other_lenders')
        ->select(DB::raw("name,DATE_FORMAT(date_started,'%m/%d/%Y') as date_started,DATE_FORMAT(date_ended,'%m/%d/%Y') as date_ended,FORMAT(amount,2) as amount"))
        ->where('id_loan',$id_loan)
        ->orDerby('id_other_lenders')
        ->get();
        $data['current_date'] = MySession::current_date();

        $data['LOAN_BALANCE'] = Loan::LoanOverallBalance([$id_loan],0);

        $data['DISCOUNT'] = DB::table('repayment_loan_discount as rld')
                            ->leftJoin('repayment_transaction as rt','rt.id_repayment_transaction','rld.id_repayment_transaction')
                            ->Where('rld.id_loan',$id_loan)
                            ->SUM('rld.amount');

        // dd($data);



       
        // dd($data['CURRENT_DUE']);

        return view("loan.loan_form_display",$data);
    }
    public function loan_approval(Request $request){
        if($request->ajax()){
            $loan_token = $request->loan_token;
            $status = $request->status;
            $cancellation_reason = $request->cancellation_reason;
            $priv =  MySession::myPrivilegeId();
            // $priv = $this->test_priv();
            // return $status+1;
            $data['RESPONSE_CODE'] = "SUCCESS";
            $data['SHOW_PRINT'] = 0;
            $data['SHOW_PRINT_VOURCHER'] = 0;

            //validate loan
            $loan_details = DB::table('loan')->select('loan.status','loan.id_loan','loan.id_loan_service','loan.id_loan_payment_type','loan.terms','loan.id_member','loan.terms_token','m.email')
            ->leftJoin('member as m','m.id_member','loan.id_member')
            ->where('loan.loan_token',$loan_token)
            ->first();


            $current_loan_status = $loan_details->status;


            $date_rel = ($status == 3)?$request->date_released:null;

            $loan = Loan::LoanDetails($loan_details->id_loan,$date_rel);


            if($current_loan_status == 2 && $status == 3){ //if status is to be update to "Released"
            // $loan = Loan::LoanDetails($loan_details->id_loan);

            // PUSH TO ACTIVE LOANS TO WAIVER
            DB::select("INSERT INTO waiver_active_loan (id_loan,id_loan_p,balance,mo_dues)
                SELECT ?,loan.id_loan as id_loan_p,
                GetLoanBalance(loan.id_loan) as balance,(lt.repayment_amount+lt.interest_amount+lt.fees) as amortization
                FROM loan 
                LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
                where loan.id_member=? and loan.loan_status =1 
                GROUP BY loan.id_loan;",[$loan_details->id_loan,$loan_details->id_member]);

            DB::table('loan_charges')
            ->where('id_loan',$loan_details->id_loan)
            ->where('is_loan_charges',0)
            ->delete();

            if(count($loan['OTHER_DEDUCTIONS'])){
                DB::table('loan_charges')
                ->insert($loan['OTHER_DEDUCTIONS']);
            }
            
            if($loan['PREVIOUS_LOAN_ID'] > 0){
                $this->insert_previous_loan_paid($loan_details->id_loan,$loan['PREVIOUS_LOAN_ID'],$loan_details->terms_token,$date_rel);
            }

            DB::table('loan')
            ->where('loan_token',$loan_token)
            ->update(['total_deductions' => $loan['TOTAL_DEDUCTED_CHARGES'],
             'cbu_deficient' => $loan['CBU_DEFICIENT_AMOUNT'],
             'loan_amount'=>$loan['TOTAL_AMOUNT_DUE'],
             'total_loan_proceeds' => $loan['TOTAL_LOAN_PROCEED'],
             'prev_loan_balance' => $loan['LOAN_BALANCE']+($loan['SURCHARGE_BALANCE_RENEW'] ?? 0),
             'prev_loan_rebates' => $loan['REBATES'],
             'previous_loan_id' => $loan['PREVIOUS_LOAN_ID'],
             'status' => 3,
             'date_released'=>$request->date_released,
             'loan_status'=>1,
             'released_by' => MySession::mySystemUserId(),
             'release_timestamp'=>DB::raw("now()"),
             'disburse_mode'=>$request->disburse_mode ?? 0]);

                if($loan_details->id_loan_payment_type == 1){ // if installment
                    $due_dates = $this->getLoanDueDate($request->date_released,$loan_details->terms);
                    $this->add_loan_due_date($loan_details->id_loan,$due_dates);
                }

                if($loan['LOAN_BALANCE'] > 0){
                    Loan::PayPreviousLoan($loan_details->id_loan_service,$loan_details->id_member,$request->date_released,$loan_details->terms_token,$loan_details->id_loan);
                }

                // function that offset loan 
                if(count($loan['PREV_LOAN_OFFSET']) > 0){
                    DB::table('loan_offset')
                    ->where('id_loan',$loan_details->id_loan)
                    ->delete();
                    $l_off = array();
                    foreach($loan['PREV_LOAN_OFFSET'] as $l){
                        $temp = array();
                        $temp['id_loan'] = $loan_details->id_loan;
                        $temp['id_loan_to_pay'] = $l->id_loan;
                        $temp['amount'] = $l->payment;
                        $temp['rebates'] = $l->rebates;

                        array_push($l_off,$temp);
                    }

                    DB::table('loan_offset')
                    ->insert($l_off);

                    Loan::GenerateOffsetRepayment($loan_details->id_loan,$date_rel);
                }
                // end function that offset loan

                //LOAN MATURITY DATE

                $mat_date = DB::table('loan_table')->select(DB::raw("MAX(due_date) as maturity_date"))->where('id_loan',$loan_details->id_loan)->first()->maturity_date;
                
                DB::table('loan')
                ->where('id_loan',$loan_details->id_loan)
                ->update(['maturity_date'=>$mat_date,'member_cbu'=>Member::getCBU($loan_details->id_member)]);

                $data['id_cash_disbursement'] = Loan::GenerateCDV($loan_details->id_loan);
                $data['SHOW_PRINT_VOURCHER'] = 1;

                // Mail::send(new LoanConfirmationMail($loan_details->id_loan));
                $this->EmailPusher($loan_details,$status); 
                return response($data);
            }

            if($current_loan_status > 2){
                $data['RESPONSE_CODE'] = "INVALID_STATUS";

                return response($data);
            }

            if($current_loan_status == 0){
                $this->insert_loan_approver($loan_details->id_loan,$loan_details->id_loan_service);
            }
            if($status <=2){

                DB::table('loan_approvers')
                ->where('id_loan',$loan_details->id_loan)
                ->where('id_cms_privileges',$priv)
                ->update([
                    'id_user'=>MySession::mySystemUserId(),
                    'date_updated'=>DB::raw("now()")
                ]);
            }

            if(($current_loan_status ==0) && ($status == 1 || $status == 2 || $status == 5)){
                /****************
                 * LOAN FINAL DATA CAN BE POSTED IF
                 *  -   CURRENT STATUS IS SUBMITED AND STATUS UPDATE IS PROCESSING => 1 OUT OF FIRST APPROVER
                 *  -   CURRENT STATUS IS SUBMITTED AND STATUS UPDATE IS APPROVED => 1 OUT OF 1 APPROVER
                 *  -   CURRENT STATUS IS SUBMITTED AND STATUS UPDATE IS CANCELLED
                 * *******************/


                //Execute Loan Approval
                $this->postLoanApproved($loan_token,$status,$cancellation_reason);    
                if($status ==1 || $status == 2){
                    $data['SHOW_PRINT'] = 1;
                }
                // if($status == 2){
                //     // OFFSET LOAN ON APPROVAL
                //     if(count($loan['PREV_LOAN_OFFSET']) > 0){
                //         DB::table('loan_offset')
                //         ->where('id_loan',$loan_details->id_loan)
                //         ->delete();
                //         $l_off = array();
                //         foreach($loan['PREV_LOAN_OFFSET'] as $l){
                //             $temp = array();
                //             $temp['id_loan'] = $loan_details->id_loan;
                //             $temp['id_loan_to_pay'] = $l->id_loan;
                //             $temp['amount'] = $l->payment;
                //             $temp['rebates'] = $l->rebates;

                //             array_push($l_off,$temp);
                //         }

                //         DB::table('loan_offset')
                //         ->insert($l_off);

                //         Loan::GenerateOffsetRepayment($loan_details->id_loan,$date_rel);
                //     }
                // }
            }elseif($status == 5){
                DB::table('loan')->where('loan_token',$loan_token)
                ->update(['status'=>$status,          
                    'cancellation_reason'=>$cancellation_reason]);
            }elseif(($current_loan_status > 0) && ($status == 1 || $status == 2 || $status == 5)){
             DB::table('loan')->where('loan_token',$loan_token)
             ->update(['status'=>$status,          
                'cancellation_reason'=>$cancellation_reason]);               
         }

           // Mail::send(new LoanConfirmationMail($loan_details->id_loan)); 
         $this->EmailPusher($loan_details,$status);

         return response($data);
     }
 }

 public function getApproversLoanService($id_loan_service,$status){
        $credential = CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/loan');
        if($status == 0){
            return [
                2=> $credential->is_confirm
            ];
        }elseif($status == 2){
            return [
                3=> $credential->is_confirm2
            ];            
        }



        return [
            // 1 => false, //Status for PROCESSING
            2=> true , //Status for Approved,
            3=>true   //Status for release
        ];
    // $approvers = DB::table('loan_service_approvers as la')
    // ->select('la.id_cms_privileges',DB::raw("null as id_user"))
    // ->leftJoin('cms_privileges as cp','cp.id','la.id_cms_privileges')
    // ->where('la.id_loan_service',$id_loan_service)
    // ->orderby('cp.order')
    // ->get();

    return $approvers;
}

public function EmailPusher($loan_details,$new_status){
    if(!env('LOAN_EMAIL_NOTIF')){
        return;
    }

    // $loan_status_email = [1,2,3,5];
    $loan_status_email = [0,2,5];

    if(in_array(intval($new_status),$loan_status_email)){
        // $email = null;
        // $email = $loan_details->email;
        $email = env('DEBUG_EMAIL') ? env('DEBUGING_EMAIL_ACC'):$loan_details->email;
        $valid_mail = filter_var($email, FILTER_VALIDATE_EMAIL);

        if ($valid_mail) {
            Mail::send(new LoanConfirmationMail($loan_details->id_loan)); 
        }
    }

    if($new_status == 0){
        $comakers = DB::table('loan_comakers as lc')
        ->select(DB::raw("FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as comaker_name,m.email"))
        ->leftJoin('member as m','m.id_member','lc.id_member')
        ->where('lc.id_loan',$loan_details->id_loan)
        ->get();

        

        foreach($comakers as $com){
            $c_email = env('DEBUG_EMAIL') ? env('DEBUGING_EMAIL_ACC'):$com->email;

            $valid_mail = filter_var($c_email, FILTER_VALIDATE_EMAIL);
            // $com_obj = array();

            if ($valid_mail) {
                $c_name = $com->comaker_name;
                $com_obj = [
                    'email'=>$c_email,
                    'name'=>$c_name
                ];

                Mail::send(new LoanConfirmationMail($loan_details->id_loan,$com_obj)); 
            }
        }


    }
}
public function getApproversLoan($id_loan){

}
public function getLoanStatusList($status,$approvers){
        // if(count($approvers) == 1 && $status == 0){
        //     $status[2] = "Approved, for Releasing";
        // }elseif()
}
public function postLoanApproved($loan_token,$status,$cancellation_reason){
    $loan_app = new LoanApplicationController();
    $loan = DB::table('loan')->select("id_loan_service","year_due")->where('loan_token',$loan_token)->first();
    $id_ls = $loan->id_loan_service;

    $one = Loan::OneTimeOpen(['id_loan_service'=>$id_ls,'due_year'=>$loan->year_due]);

    $interest_multiplier = $one['duration'];
    $DATE = MySession::current_date();

    // concat(YEAR('$DATE')+if(MONTH('$DATE') >= ls.start_month_period AND MONTH('$DATE') <= ls.end_month_period,0,1),'-',ls.end_month_period,'-',ls.repayment_schedule)
    $service_details = DB::table('loan as ap_loan')
    ->select(DB::raw("getLoanServiceName(ap_loan.id_loan_payment_type,ls.name,ap_loan.terms) as name"),"ls.id_loan_service","terms.terms","terms.period","ls.cbu_amount","ls.is_deduct_cbu",DB::raw("ap_loan.principal_amount,terms.loan_protection_rate,ls.id_term_period,ls.id_interest_period,ls.id_interest_method,concat(terms.terms,' ',p.description) as terms_desc,terms.terms_token,ls.min_amount,ls.max_amount,ls.id_loan_payment_type,concat(getMonth(ls.end_month_period),' ',ls.repayment_schedule,', ',YEAR('$DATE')) as due_date,
    if(ap_loan.year_due is  null,
    CASE WHEN ls.end_month_period >= ls.start_month_period
    THEN 
    concat(YEAR('$DATE')+IF(MONTH('$DATE') > ls.end_month_period,1,0),'-',ls.end_month_period,'-',ls.repayment_schedule)
    ELSE
    concat(YEAR('$DATE')+IF(MONTH('$DATE') <= ls.end_month_period,0,1),'-',ls.end_month_period,'-',ls.repayment_schedule)
    END,concat(ap_loan.year_due,'-',ap_loan.period,'-',ap_loan.repayment_schedule)) as form_due_date,ap_loan.id_member,terms.loan_protection_rate,ls.id_charges_group,ap_loan.id_loan,ls.deduct_interest,$interest_multiplier as month_duration,terms.interest_rate*$interest_multiplier as interest_rate,terms.interest_rate as interest_show,ls.id_one_time_type"))
    ->leftJoin('loan_service as ls','ls.id_loan_service','ap_loan.id_loan_service')
    ->leftJoin('terms','ap_loan.terms_token','terms.terms_token')
    ->where('ap_loan.loan_token',$loan_token)
    ->leftJoin('period as p','p.id_period','ls.id_term_period')
    ->first();

    $id_loan = $service_details->id_loan;

        $first_loan = Member::CheckFirstLoan($service_details->id_member,$service_details->id_loan_service); //revalidate if there is no active loan (FIRST LOAN)

        //UPDATE THE LOAN DETAILS BASED ON LATEST DATA FROM LOAN SERVICE AND TERMS
        DB::select("UPDATE loan as ap_loan
            JOIN loan_service as ls on ls.id_loan_service = ap_loan.id_loan_service
            JOIN terms on terms.id_loan_service = ls.id_loan_service and terms.terms_token = ap_loan.terms_token
            SET ap_loan.id_loan_service = ls.id_loan_service, ap_loan.id_disbursement_type = ls.id_disbursement_type, 
            ap_loan.id_interest_method = ls.id_interest_method, ap_loan.id_loan_payment_type = ls.id_loan_payment_type, 
            ap_loan.id_term_period = ls.id_term_period, ap_loan.id_interest_period = ls.id_interest_period, 
            ap_loan.id_repayment_period = ls.id_repayment_period, ap_loan.start_month_period = ls.start_month_period, 
            ap_loan.end_month_period = ls.end_month_period, ap_loan.repayment_schedule = ls.repayment_schedule, 
            ap_loan.id_charges_group = ls.id_charges_group, ap_loan.interest_rate = terms.interest_rate*$interest_multiplier, 
            ap_loan.terms = terms.terms, ap_loan.period = terms.period,ap_loan.terms_token = terms.terms_token,
            ap_loan.loan_protection_rate = terms.loan_protection_rate, ap_loan.is_deduct_cbu = ls.is_deduct_cbu,
            ap_loan.deduct_interest = ls.deduct_interest,ap_loan.month_duration = $interest_multiplier,ap_loan.interest_show=terms.interest_rate,ap_loan.id_one_time_type=ls.id_one_time_type
            WHERE id_loan = $id_loan;");
        //END UPDATE LOAN DETAILS

        //PUSH LOAN CHARGES

        //REMOVE CURRENT LOAN CHARGES
        DB::table('loan_charges')->where('id_loan',$id_loan)->delete();

        $c_type = [1]; //Default fee type to all
        $id_charges_group = $service_details->id_charges_group;
        $interest = $service_details->interest_rate;

        $principal_amount = $service_details->principal_amount;
        array_push($c_type,($first_loan)?2:3);

        // DB::select("INSERT INTO loan_charges (id_loan,id_loan_fees,id_fee_calculation,value,id_calculated_fee_base,is_deduct,application_fee_type,calculated_charge,non_deduct_option)
        //     SELECT $id_loan,id_loan_fees,id_fee_calculation,value,id_calculated_fee_base,is_deduct,application_fee_type,if(c.id_fee_calculation=1,calculateChargeAmountPer($principal_amount,$interest,c.value,c.id_calculated_fee_base),c.value) as calculated_charge,c.non_deduct_option
        //     FROM charges as c
        //     WHERE id_charges_group =$id_charges_group and application_fee_type in (".implode(",",$c_type).")");


        DB::select("INSERT INTO loan_charges (id_loan,id_loan_fees,id_fee_calculation,value,id_calculated_fee_base,is_deduct,application_fee_type,calculated_charge,non_deduct_option)
            SELECT * FROM (
                SELECT $id_loan,id_loan_fees,id_fee_calculation,@val:= if(c.with_range=0,c.value,ChargeRange(c.id_charges,$principal_amount,c.value)) as value,id_calculated_fee_base,is_deduct,application_fee_type,if(c.id_fee_calculation=1,calculateChargeAmountPer($principal_amount,$interest,@val,c.id_calculated_fee_base),@val) as calculated_charge,c.non_deduct_option
                FROM charges as c
                WHERE id_charges_group =$id_charges_group and application_fee_type in (".implode(",",$c_type).")) as charges
            WHERE charges.value > 0 ");

           //END PUSH LOAN CHARGES

        $cbu = Member::CheckCBU($service_details->cbu_amount,$service_details->id_member);


        $cbu_deficient = ($service_details->is_deduct_cbu == 1)?$cbu['difference']:0;

        $charges = $loan_app->parseChargesLoan($id_loan);

        $previous_loan = Member::CheckPreviousLoan($service_details->id_loan_service,$service_details->id_member,$service_details->terms_token);
        $other_deductions = DB::table('loan_manual_deduction')
                                ->select('id_loan_fees','amount','remarks')
                                ->where('id_loan',$id_loan)
                                ->get();
        $other_deductions = json_decode(json_encode($other_deductions),true);

        $loan_parameter = [
            'id_member' => $service_details->id_member,
            'cbu_deficient' => $cbu_deficient,
            'charges' => $charges,
            'principal_amount' =>$service_details->principal_amount,
            'interest_rate'=> $service_details->interest_rate,
            'terms' => $service_details->terms,
            'term_period' => $service_details->id_term_period,
            'interest_pediod'=>$service_details->id_interest_period,
            'interest_method' => $service_details->id_interest_method,
            'is_cbu_deduct' => $service_details->is_deduct_cbu,
            'loan_protection_rate' => $service_details->loan_protection_rate,
            'payment_type' => $service_details->id_loan_payment_type,
            'previous_loan' => $previous_loan,
            'deduct_interest'=>$service_details->deduct_interest,
            'id_loan_service'=>$service_details->id_loan_service,
            'other_deductions'=>$other_deductions
        ];
            // $loan_offset = DB::table('loan_offset as lo')
            // ->leftJoin('loan','loan.id_loan','lo.id_loan_to_pay')
            // ->where('lo.id_loan',$id_loan)
            // ->get();

            // $loan_paid = array();
            // foreach($loan_offset as $lo){
            //     $temp = array();
            //     $temp['loan_token'] = $lo->loan_token;
            //     $temp['amount'] = $lo->amount;

            //     array_push($loan_paid,$temp);
            // }
            // if(count($loan_paid) > 0){
            //     $loan_parameter['loan_payment'] =Loan::parseExistingLoanBalance($output['service_details']->id_member,MySession::current_date(),$loan_paid,2);
            //     // return $loan_parameter;
            // }
        $loan_output = Loan::ComputeLoan($loan_parameter);

        //INSERT TO LOAN TABLE
        DB::table('loan_table')->where('id_loan',$id_loan)->delete();

        $loan_table_param = array();
        foreach($loan_output['LOAN_TABLE'] as $tb){
            $loan_table_param[]=[
                'id_loan'=>$id_loan,
                'count' => $tb['count'],
                'term_code'=>$tb['term_code'],
                'repayment_amount'=>$tb['repayment_amount'],
                'interest_amount'=>$tb['interest_amount'],
                'fees' => $tb['fees'],
                'total_due' => $tb['total_due'],
                'due_date' => ($service_details->id_loan_payment_type == 2)?$service_details->form_due_date:null
            ];
        }

        DB::table('loan_table')
        ->insert($loan_table_param);
        //END INSERT LOAN TABLE

        //Insert OTHER DEDUCTIONS/CHARGES
        $other_charges_param = array();



        foreach($loan_output['OTHER_DEDUCTIONS'] as $oth){
            $temp = array();
            foreach($oth as $key=>$val){
                $temp[$key]=$val;
            }
            $temp['id_loan'] = $id_loan;
            array_push($other_charges_param,$temp);

        }

        DB::table('loan_charges')
        ->insert($other_charges_param);
        //END INSERT OTHER DEDUCTIONS/CHARGES


        //UPDATE OTHER LOAN DETAILS
        DB::table('loan')
        ->where('id_loan',$id_loan)
        ->update([
            'total_deductions' => $loan_output['TOTAL_DEDUCTED_CHARGES'],
            'cbu_deficient' => $cbu_deficient,
            'total_loan_proceeds' => $loan_output['TOTAL_LOAN_PROCEED'],
            'loan_protection_amount' => $loan_output['LOAN_PROTECTION_AMOUNT'],
            'not_deducted_charges' => $loan_output['TOTAL_NOT_DEDUCTED_CHARGES'],
            'loan_amount'=>$loan_output['TOTAL_AMOUNT_DUE'],
            'status'=>$status,
            'cancellation_reason'=>$cancellation_reason,
            'prev_loan_balance' => $loan_output['LOAN_BALANCE']+($loan_output['SURCHARGE_BALANCE_RENEW'] ?? 0),
            'prev_loan_rebates' => $loan_output['REBATES']
        ]);
        //END



        if($loan_output['offset_cbu']){
            DB::table('loan_charges')
            ->where('id_loan',$id_loan)
            ->where('id_loan_fees',2)
            ->whereNotNull('id_calculated_fee_base')
            ->delete();
        }
        return "success";


        // DB::table('loan')
        return $loan_output;
    }

    public function print_application_waiver($loan_token,$for_mail=false){
        $data['loan_details'] = DB::table('loan as ap_loan')
        ->select(DB::raw("UPPER(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as name,getLoanServiceName(ap_loan.id_loan_payment_type,ls.name,ap_loan.terms) as loan_service,concat(terms,' ',tp.description) as duration,FORMAT(ap_loan.total_loan_proceeds,2) as total_loan,interest_rate,ap_loan.principal_amount as principal_num,FORMAT(ap_loan.principal_amount,2) as principal_amount,ap_loan.id_loan,lt.total_due as amtz,FORMAT(lt.total_due,2) as total_due,FORMAT(ap_loan.loan_amount,2) as loan_amount,m.address,m.mobile_no,m.id_member,ap_loan.loan_remarks,ap_loan.loan_status as status_code,bl.name as brgy_lgu,DATE_FORMAT(ap_loan.date_released,'%m/%d/%Y') as date_released,DATE_FORMAT(ap_loan.maturity_date,'%m/%d/%Y') as maturity_date,terms,ap_loan.loan_remarks as purpose,m.address,m.mobile_no,cs.description as civil_status,DATE_FORMAT(m.date_of_birth,'%m/%d/%Y') as birthday,m.spouse,m.spouse_occupation,ap_loan.member_cbu,m.memb_type,bl.treasurer,bl.chairman"))
        ->leftJoin('loan_service as ls','ls.id_loan_service','ap_loan.id_loan_service')
        ->leftJoin('member as m','m.id_member','ap_loan.id_member')
        ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','m.id_baranggay_lgu')

        // ->leftJoin('position as pos','pos.id_position','m.id_position')
        ->leftJoin('period as tp','tp.id_period','ap_loan.id_term_period')
        ->leftJoin('loan_table as lt','lt.id_loan','ap_loan.id_loan')
        ->leftJoin('loan_net_pay as lnp','lnp.id_loan','ap_loan.id_loan')
        ->leftJoin('civil_status as cs','cs.id_civil_status','m.id_civil_status')
        ->where('ap_loan.loan_token',$loan_token)
        ->first();



        $data['net_pay'] = DB::table('loan_net_pay')
        ->select(DB::raw("ifnull(SUM(amount),0) as net_pay"))
        ->where('id_loan',$data['loan_details']->id_loan)
        ->first()->net_pay;

        $data['comakers'] = DB::table('loan_comakers as c')
        ->select(DB::raw("UPPER(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as comaker_name,m.address,m.mobile_no,cs.description as civil_status,DATE_FORMAT(m.date_of_birth,'%m/%d/%Y') as birthday,m.spouse,m.spouse_occupation"))
        ->leftJoin('member as m','m.id_member','c.id_member')
        ->leftJoin('civil_status as cs','cs.id_civil_status','m.id_civil_status')
        ->where('c.id_loan',$data['loan_details']->id_loan)
        ->first();

        // dd($data);
        $date= WebHelper::ConvertDatePeriod(MySession::current_date());

        if($data['loan_details']->status_code == 0){
            $data['active_loan'] = DB::select("
                SELECT *,GetLoanBalance(k.id_loan) as balance FROM (
                SELECT loan.loan_token,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as loan,0 as payment,loan.principal_amount,(lt.repayment_amount+lt.interest_amount+lt.fees) as amortization,DATE_FORMAT(loan.date_released,'%m/%d/%Y') as date_released,DATE_FORMAT(loan.maturity_date,'%m/%d/%Y') as maturity_date
                FROM loan 
                LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
                where loan.id_member=? and loan.loan_status =1 and loan.loan_token <> ? AND ls.id_loan_payment_type=1
                GROUP BY loan.id_loan) as k;",[$data['loan_details']->id_member,$loan_token]);

            // dd($data);

            $data['cbu'] = Member::getCBU($data['loan_details']->id_member);  
            $data['cbu_as_of'] =date("m/d/Y", strtotime(MySession::current_date()));          
        }else{
            $data['active_loan'] = DB::select("SELECT loan.loan_token,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as loan,wal.balance,loan.principal_amount,
                wal.mo_dues as amortization,DATE_FORMAT(loan.date_released,'%m/%d/%Y') as date_released,DATE_FORMAT(loan.maturity_date,'%m/%d/%Y') as maturity_date
                FROM waiver_active_loan as wal
                LEFT JOIN loan on loan.id_loan = wal.id_loan_p
                LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                WHERE  wal.id_loan = ? AND ls.id_loan_payment_type=1;",[$data['loan_details']->id_loan]);     
            $data['cbu'] = $data['loan_details']->member_cbu;  
            $data['cbu_as_of'] = $data['loan_details']->date_released;          
        }

        // dd($data);

        // dd($this->amountToWords(152351));
        // $f = new \NumberFormatter("en",\NumberFormatter::SPELLOUT);


    
        $data['loan'] = Loan::LoanDetails($data['loan_details']->id_loan);

        // dd($data['loan']);
        // $data['principal_worded'] = ucwords($f->format($data['loan_details']->principal_num));
        $data['principal_worded'] = $this->amountToWords($data['loan_details']->principal_num);
        // return $data['loan_details']->principal_num;
        // $html = view('loan.loan_waiver_lepsta1',$data);
        $html = view('loan.'.env('WAIVER_BLADE'),$data);

        // dd($data);

        // $pdf = PDF::loadHtml($html);
        // $pdf->setOption("encoding","UTF-8");
        // $pdf->setOption('margin-bottom', '5mm');
        // $pdf->setOption('margin-top', '7mm');
        // $pdf->setOption('margin-right', '5mm');
        // $pdf->setOption('margin-left', '5mm');
        // $pdf->setOption('header-left', 'Page [page] of [toPage]');

        // $pdf->setOption('header-font-size', 8);
        // $pdf->setOption('header-font-name', 'Calibri');
        // // $pdf->setOrientation('landscape');

        // return $pdf->stream();
        // exit;
        // return $html;

        // $html = view('loan.waiver',$data);
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true); // Enable HTML5 parsing
        $options->set('isFontSubsettingEnabled', true); // Enable font subsetting
        $options->set('isRemoteEnabled', true); // Enable remote file access
        $options->set('unicode', true); // Enable Unicode support
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->render();
        $font = $dompdf->getFontMetrics()->get_font("serif");

        if($for_mail){
            return $dompdf->output();
        }
            // $dompdf->set_paper("A4", 'landscape');
            // $dompdf->getCanvas()->page_text(530, 5, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 9, array(0,0,0));      
        $dompdf->stream("PL.pdf", array("Attachment" => false));     

        exit;  

    }

    public function amountToWords($amount) {
        $number = number_format($amount, 2, '.', '');
        $decimal_part = intval(substr($number, -2));
        $integer_part = intval(substr($number, 0, -3));
        $words = '';
        
        $ones = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine');
        $tens = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety');
        $teens = array('eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');
        $hundreds = array('', 'one hundred', 'two hundred', 'three hundred', 'four hundred', 'five hundred', 'six hundred', 'seven hundred', 'eight hundred', 'nine hundred');
        $thousands = array('', 'thousand', 'million', 'billion', 'trillion');
        
        if ($integer_part == 0) {
            $words = 'zero';
        }
        
        $thousands_index = 0;
        
        while ($integer_part > 0) {
            if ($integer_part % 1000 > 0) {
                $hundreds_index = floor(($integer_part % 1000) / 100);
                $tens_index = floor(($integer_part % 100) / 10);
                $ones_index = $integer_part % 10;
                $thousands_word = $thousands[$thousands_index];
                $hundreds_word = $hundreds[$hundreds_index];
                $tens_word = '';
                $ones_word = '';
                
                if ($tens_index == 1 && $ones_index > 0) {
                    $tens_word = $teens[$ones_index - 1];
                    $ones_word = '';
                } else {
                    $tens_word = $tens[$tens_index];
                    $ones_word = $ones[$ones_index];
                }
                
                $words = $hundreds_word . ' ' . $tens_word . ' ' . $ones_word . ' ' . $thousands_word . ' ' . $words;
            }
            
            $integer_part = floor($integer_part / 1000);
            $thousands_index++;
        }
        
        
        if ($decimal_part > 0) {
            $words .= ' and ';
            
            if($decimal_part >= 11 && $decimal_part <= 19) {
                $words .= $teens[$decimal_part - 11];
            }elseif ($decimal_part >= 20 || $decimal_part == 10) {
                $tens_digit = floor($decimal_part / 10);
                $words .= $tens[$tens_digit];
                $decimal_part -= $tens_digit * 10;
            }
            
            if ($decimal_part >= 1 && $decimal_part <= 9) {
                $words .= ' ' . $ones[$decimal_part];
            }
        
            $words .= ' cents';
        }
        
        return ucwords(trim($words));
    }
    public function parseLoanStatus($status){
        $status=[
            1=>[8],
            2=>[9]
        ];
    }
    public function insert_loan_approver($id_loan,$id_loan_service){
        DB::table('loan_approvers')->where('id_loan',$id_loan)->delete();
        // DB::select("INSERT INTO loan_approvers (id_loan,id_cms_privileges)
        //     SELECT $id_loan,id_cms_privileges FROM loan_service_approvers
        //     WHERE id_loan_service = $id_loan_service;");

    }
    public function repayment_transactions_frame($loan_token){
        $id_loan = DB::table('loan')->select('id_loan',DB::raw("getLoanTotalPaymentType(id_loan,1) as paid_principal,getLoanTotalPaymentType(id_loan,2) as paid_interest,getLoanTotalPaymentType(id_loan,3) as paid_fees"))->where('loan_token',$loan_token)->first();
        if(isset($id_loan)){
            $data['details'] = $id_loan;
            $id_loan = $id_loan->id_loan;



            $repayment_transaction = DB::select("CALL RepaymentRunningBalance($id_loan)");
            $g = new GroupArrayController();
            $data['repayment_transactions'] = $g->array_group_by($repayment_transaction,['due_date']);
            $data['LOAN_ID'] = $id_loan;

            return view("loan.repayment_transaction_frame",$data);

            return $data;
        }
    }
    public function GenerateCashReceipt($id_loan,$or_no){
        $total_payment = 0;
        $loan  =$d['q']= DB::table('loan')
        ->select("prev_loan_balance","prev_loan_rebates","date_released",
            DB::raw("getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_service_name"),"loan.id_member")
        ->leftJoin("loan_service as ls","ls.id_loan_service","loan.id_loan_service")
        ->where('id_loan',$id_loan)
        ->first();
            //Charges
        $charges = DB::select("select id_payment_type,calculated_charge 
            From loan_charges as lc
            LEFT JOIN tbl_payment_type as tp on lc.id_loan_fees = tp.reference and tp.type = 2
            where lc.id_loan = ? and lc.non_deduct_option is null;",[$id_loan]);
        $cash_receipt_details = array();

        foreach($charges as $ch){
            $temp = array();
            $temp['id_payment_type'] = $ch->id_payment_type;
            $temp['amount'] = $ch->calculated_charge;
            $temp['description'] = null;
            array_push($cash_receipt_details,$temp);
            $total_payment += $ch->calculated_charge;
        }

        if($loan->prev_loan_balance > 0){
            $temp = array();
            $temp['id_payment_type'] = config('variables.previous_loan_payment');
            $temp['description'] = "Payment for ".$loan->loan_service_name." (ID# $id_loan) -".number_format($loan->prev_loan_rebates,2); 
            $temp['amount'] = $loan->prev_loan_balance-$loan->prev_loan_rebates; 

            $total_payment += $temp['amount'];



            array_push($cash_receipt_details,$temp);
        }




            // if($loan->prev_loan_balance > 0){
            //     $temp = array();
            //     $temp['id_payment_type'] = config('variables.previous_loan_rebates');
            //     $temp['amount'] = $loan->prev_loan_rebates; 

            //     array_push($cash_receipt_details,$temp);
            // }

        $cash_receipt = [
            'date_received'=> $loan->date_released,
            'id_paymode' => 1,
            'payee_type' => 1,
            'id_member'=> $loan->id_member,
            'or_no' => $or_no,
            'reference_no' => $id_loan,
            'type' => 2,
            'total_payment' => $total_payment

        ];

        DB::table('cash_receipt')
        ->insert($cash_receipt);

        $id_cash_receipt = DB::table('cash_receipt')->where('type',2)->where('reference_no',$id_loan)->max('id_cash_receipt');

        for($i=0;$i<count($cash_receipt_details);$i++){
            $cash_receipt_details[$i]['id_cash_receipt'] = $id_cash_receipt;
        }

        DB::table('cash_receipt_details')
        ->insert($cash_receipt_details);

        return $id_cash_receipt;



        return $cash_receipt_details;
        return $charges;
    }
    public function insert_previous_loan_paid($id_loan,$id_loan_previous,$terms_token,$date){
        DB::table('paid_previous_balance')->where('id_loan_current',$id_loan)->delete();
            // DB::select("INSERT INTO paid_previous_balance (id_loan_current,id_loan_previous,previous_principal,previous_interest,previous_fees)
            //             SELECT  id_loan,$id_loan_previous as prev,getLoanOverallBalance($id_loan_previous,1) as principal,getLoanOverallBalance($id_loan_previous,2) as interest,getLoanOverallBalance($id_loan_previous,3) as fees
            //             FROM loan where id_loan = $id_loan and terms_token = $terms_token");

        $mat_date = $date = WebHelper::ConvertDatePeriod2($date);

        if(env("RENEWAL_INTEREST_FULL_CONTRACT")){
            $mat_date = DB::table('loan')->select('maturity_date')->where('id_loan',$id_loan_previous)->first()->maturity_date;
            $mat_date = ($date > $mat_date)?$date:$mat_date;   
        }

        $int_q = env('REPAYMENT_INTEREST_FULL_CONTRACT')?"loan.maturity_date":"'$date'";
        DB::select("INSERT INTO paid_previous_balance (id_loan_current,id_loan_previous,previous_principal,previous_interest,previous_fees,previous_surcharge)
            SELECT  id_loan,$id_loan_previous as prev,getLoanOverallBalance($id_loan_previous,1) as principal,getInterestBalanceAsOf($id_loan_previous,'$mat_date') as interest,getFeesBalanceAsOf($id_loan_previous,'$mat_date')  as fees,getSurchargeBalanceAsOf($id_loan_previous,'$mat_date')  as surcharge
            FROM loan where id_loan = $id_loan and terms_token = '$terms_token'");
    }

    function convert_number_to_words($number) {

      $decimal_part = round(($number - floor($number)), 2) * 100;
      $whole_part = (int) $number;

      $words = '';

      $ones = array(
        0 => '',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen'
    );

      $tens = array(
        0 => '',
        1 => '',
        2 => 'Twenty',
        3 => 'Thirty',
        4 => 'Forty',
        5 => 'Fifty',
        6 => 'Sixty',
        7 => 'Seventy',
        8 => 'Eighty',
        9 => 'Ninety'
    );

      if ($whole_part == 0) {
        $words = 'Zero';
    }

    if ($whole_part >= 1000) {
        $thousands = (int) ($whole_part / 1000);
        $words .= $ones[$thousands] . ' thousand ';
        $whole_part %= 1000;
    }

    if ($whole_part >= 100) {
        $hundreds = (int) ($whole_part / 100);
        $words .= $ones[$hundreds] . ' hundred ';
        $whole_part %= 100;
    }

    if ($whole_part >= 20) {
        $tens_digit = (int) ($whole_part / 10);
        $words .= $tens[$tens_digit] . ' ';
        $whole_part %= 10;
    }

    if ($whole_part >= 1) {
        $words .= $ones[$whole_part] . ' ';
    }

    $words .= 'and ' . $decimal_part . '/100';

    return ucwords(trim($words));
}
        // public function print_cdv()
}


// SELECT ls.name as 'loan_service_name',loan.principal_amount,loan.interest_rate,concat(terms,' ',period.description) as terms,
// ROUND((loan.principal_amount*(loan.interest_rate/100)*ifnull(terms,1))+loan.principal_amount,2) as loan_amount
// FROM loan 
// LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
// LEFT JOIN period on period.id_period = loan.id_term_period
// where id_member = 20 and loan_status = 1;




//     SELECT 
// crd.id_cash_receipt_details,ifnull(crd.description,tp.description) as 'payment_description',crd.amount as amount
// FROM cash_receipt as cr
// LEFT JOIN cash_receipt_details as crd on crd.id_cash_receipt = cr.id_cash_receipt
// LEFT JOIN tbl_payment_type as tp on tp.id_payment_type = crd.id_payment_Type
// WHERe cr.id_Cash_receipt = 61;