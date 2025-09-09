<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MySession;
use DB;
use App\CredentialModel;
use Dompdf\Dompdf;
use PDF;
use App\WebHelper;

class RepaymentCheckController extends Controller
{

    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }   


        $data['checks'] = DB::table('repayment_check as rc')
                          ->select(DB::raw("rc.id_repayment_check,DATE_FORMAT(rc.transaction_date,'%m/%d/%Y') as transaction_date,tb.branch_name,if(rc.id_check_type=1,'On-date','Post dated') as check_type,bank.bank_name,rc.check_no,DATE_FORMAT(rc.check_date,'%m/%d/%Y') as check_date,rc.total as amount,DATE_FORMAT(rc.date_created,'%m/%d/%Y') as date_created,rc.status,
                            CASE WHEN rc.status=0 THEN 'Draft'
                            WHEN rc.status=1 THEN 'Confirmed'
                            WHEN rc.status=10 THEN 'Cancelled' END as status_description"))
                          ->leftJoin('tbl_branch as tb','tb.id_branch','rc.id_branch')
                          ->leftJoin('tbl_bank as bank','bank.id_bank','rc.id_bank')
                          ->orderBy('rc.id_repayment_check','DESC')
                          ->get();
        return view('repayment-check.index',$data);
    }   
    public function create(Request $request){
        $data['opcode'] = 0;
        $data['selected_branch'] = $request->branch ?? 1;


        $data['branches'] = DB::table('tbl_branch')
                            ->select('id_branch','branch_name')
                            ->get();


        $data['banks'] = DB::table('tbl_bank')
                         ->select(DB::raw("id_bank,bank_name"))
                         ->get();

        $data['repayments'] =$this->parseRepayments($data['selected_branch'],0);
        // dd($data);
        return view('repayment-check.form',$data);
    }

    public function edit($id_repayment_check){
        $data['opcode'] = 1;
        $data['details'] = DB::table('repayment_check as rc')
                           ->select(DB::raw("rc.*,tb.branch_name"))
                           ->leftJoin('tbl_branch as tb','tb.id_branch','rc.id_branch')
                           ->where('id_repayment_check',$id_repayment_check)
                           ->first();

        if($data['details']->status != 0){
             return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['branches'] = DB::table('tbl_branch')
                            ->select('id_branch','branch_name')
                            ->get();


        $data['banks'] = DB::table('tbl_bank')
                         ->select(DB::raw("id_bank,bank_name"))
                         ->get();

        $data['selected_branch'] = $data['details']->id_branch;

        $data['repayments'] =$this->parseRepayments($data['selected_branch'],$id_repayment_check);

        return view('repayment-check.form',$data);

        dd($data);
        // $data['selecte']
    }
    public function view($id_repayment_check){
        $data['details'] = DB::table('repayment_check as rc')
                          ->select(DB::raw("rc.id_repayment_check,DATE_FORMAT(rc.transaction_date,'%m/%d/%Y') as transaction_date,tb.branch_name,if(rc.id_check_type=1,'On-date','Post dated') as check_type,bank.bank_name,rc.check_no,DATE_FORMAT(rc.check_date,'%m/%d/%Y') as check_date,rc.total as amount,DATE_FORMAT(rc.date_created,'%m/%d/%Y') as date_created,rc.status,rc.remarks,CASE WHEN rc.status=0 THEN 'Draft'
                            WHEN rc.status=1 THEN 'Confirmed'
                            WHEN rc.status=10 THEN 'Cancelled' END as status_description"))
                          ->leftJoin('tbl_branch as tb','tb.id_branch','rc.id_branch')
                          ->leftJoin('tbl_bank as bank','bank.id_bank','rc.id_bank')
                          ->where('rc.id_repayment_check',$id_repayment_check)
                          ->first();
        $data['repayments'] = DB::select("SELECT rt.id_repayment_transaction,rt.repayment_token,transaction_date,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as date,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,total_payment,0 as checked
                                         FROM repayment_check_transaction as rct
                                         LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rct.id_Repayment_transaction
                                         LEFT JOIN member as m on m.id_member = rt.id_member
                                         WHERE rct.id_repayment_check = ?
                                         ORDER BY rt.id_repayment_transaction,rt.transaction_date",[$id_repayment_check]);

        return view('repayment-check.view',$data);
    }


    public function parseRepayments($id_branch,$id_repayment_check){
        $param = [
            'id_branch'=>$id_branch,
            'id_repayment_check'=>$id_repayment_check
        ];

        $data = DB::select("SELECT * FROM (
        SELECT rt.id_repayment_transaction,rt.repayment_token,transaction_date,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as date,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,total_payment,0 as checked
        FROM repayment_transaction as rt
        LEFT JOIN member as m on m.id_member = rt.id_member
        WHERE rt.transaction_type = 4 AND rt.status <> 10 AND rt.id_branch_rt = :id_branch AND rt.id_repayment_check is  null
        UNION
        SELECT rt.id_repayment_transaction,transaction_date,rt.repayment_token,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as date,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,total_payment, 1 as checked
        FROM repayment_transaction as rt
        LEFT JOIN member as m on m.id_member = rt.id_member
        WHERE rt.transaction_type = 4 AND rt.status <> 10 AND  rt.id_repayment_check=:id_repayment_check) as k
        ORDER BY k.id_repayment_transaction DESC,k.transaction_date DESC;",$param);

        return $data;
    }


    public function post(Request $request){

        $opcode = $request->opcode  ?? 0;
        $repayments = $request->repayments ?? [];
        $id_repayment_check = $request->id_repayment_check ?? 0;


        if($opcode == 1){
            $details = DB::table('repayment_check')->where('id_repayment_check',$id_repayment_check)->first();
            if($details->status != 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Invalid Request";
                return response($data);
            }


        }


        if(count($repayments) == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please select at least 1 repayment";

            return response($data);
        }

        $field = array(
            'transaction_date'=>['key'=>'transaction_date','required'=>true],
            'remarks'=>['key'=>'remarks','required'=>false],
            'check_type'=>['key'=>'id_check_type','required'=>true],
            'bank'=>['key'=>'id_bank','required'=>true],
            'check_date'=>['key'=>'check_date','required'=>true],
            'check_no'=>['key'=>'check_no','required'=>true]
        );


        $postOBJ = array();
        $details = $request->details;
        $invalid_field = array();
        foreach($field as $key=>$f){
            if($f['required'] && (!isset($details[$key]) || $details[$key] == "" )){
                array_push($invalid_field,$key);
            }
            $postOBJ[$f['key']] = $details[$key];
        }

        if(count($invalid_field) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please fill required fields";
            $data['invalid_fields'] = $invalid_field;
            return response($data);
        }

        $repayment_data = $this->RepaymentsValidation($repayments,$id_repayment_check);
        $repayment_transaction_ids = $repayment_data['repayments']->pluck('id_repayment_transaction')->toArray();


        if($repayment_data['total'] <= 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Selected Loan Payment";
            return response($data);
        }

        $postOBJ['total'] = $repayment_data['total'];
        if($opcode == 0){
            // Insert
            $postOBJ['id_user'] = MySession::myId();
            $postOBJ['id_branch'] = $request->id_branch;

            DB::table('repayment_check')
            ->insert($postOBJ);
            
            $id_repayment_check = DB::table('repayment_check')->max("id_repayment_check");
        }else{
            //update
            DB::table('repayment_check')
            ->where('id_repayment_check',$id_repayment_check)
            ->update($postOBJ);

            DB::table('repayment_check_transaction')
            ->where('id_repayment_check',$id_repayment_check)
            ->delete();

            DB::table('repayment_transaction')
            ->where('id_repayment_check',$id_repayment_check)
            ->update(['id_repayment_check'=>null]);

        }




        $repayment_item = array();
        foreach($repayment_data['repayments'] as $rp){
            $repayment_item[]=[
                'id_repayment_check'=>$id_repayment_check,
                'id_repayment_transaction'=>$rp->id_repayment_transaction,
                'amount'=>$rp->total_payment
            ];
        }

        DB::table('repayment_check_transaction')
        ->insert($repayment_item);


        DB::table('repayment_transaction')
        ->whereIn('id_repayment_transaction',$repayment_transaction_ids)
        ->update(['id_repayment_check'=>$id_repayment_check]);

        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['ID_REPAYMENT_CHECK'] = $id_repayment_check;

        return response($data);

        dd($id_repayment_check);

    }

    public function RepaymentsValidation($repayments,$id_repayment_check){
        $repayments = DB::table('repayment_transaction')
                     ->select(DB::raw('id_repayment_transaction,total_payment'))
                     ->whereIn('id_repayment_transaction',$repayments)
                     ->where('status','<>',10)
                     ->get();

        $data['repayments'] = $repayments;
        $data['total'] = $repayments->sum('total_payment');

        return $data;
    }

    public function post_status(Request $request){
        if($request->ajax()){
            $id_repayment_check = $request->id_repayment_check;
            $reason = $request->reason;
            $status = $request->status ?? 1;

            $status = (in_array(intval($status),[1,10]))?$status:1;

            //validation
            $details = DB::table('repayment_check')->where('id_repayment_check',$id_repayment_check)->first();

            if($details->status == 10){
                //if cancelled
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Loan Payment Check is already cancelled";
                return response($data);
            }



            $StatusOBJ = [
                'status'=>$status,
                'status_user'=>MySession::myId(),
                'status_date'=>DB::raw("now()")
            ];


            if($status == 10){
                $StatusOBJ['reason'] = $reason;
            }

            DB::table('repayment_check')
            ->where('id_repayment_check',$id_repayment_check)
            ->update($StatusOBJ);


            if($status == 10){     
                DB::table('repayment_transaction')
                ->where('id_repayment_check',$id_repayment_check)
                ->update(['id_repayment_check'=>null]);
            }


            $data['RESPONSE_CODE'] = "SUCCESS";

            return response($data);

            dd($StatusOBJ);

            dd($status);

        }
    }
}
