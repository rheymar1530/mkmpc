<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\CredentialModel;
use App\CDVModel;
use PDF;

class ChangePayableController extends Controller
{
    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }  
        $data['changes'] = DB::table('change_payable as cp')
                            ->select(DB::raw("cp.id_change_payable,DATE_FORMAT(cp.date,'%m/%d/%Y') as date,RepaymentDescription(r.payment_for,r.id_repayment) as change_for,cp.total_amount,cp.id_repayment,DATE_FORMAT(cp.date_created,'%m/%d/%Y') as date_created,
                                CASE WHEN cp.status = 0 THEN 'Posted'
                                     WHEN cp.status = 10 THEN 'Cancelled'
                                     ELSE ''
                                     END as status_description,if(cp.status=0,'success2','danger2') as status_badge"))
                            ->leftJoin('repayment as r','r.id_repayment','cp.id_repayment')
                            ->orderBy('cp.id_change_payable','DESC')
                            ->get();
 
        return view('change-payable.index',$data);
    }

    public function create(){
        $data = array();

        $data = $data+$this->formData();
        $data['ChangeList'] = $this->ChangePayableList();
        $data['opcode'] = 0;
 
        return view('change-payable.form',$data);
        dd($data);
    }

    public function ChangePayableList(){
        $changeList = DB::select("SELECT DATE_FORMAT(r.date,'%m/%d/%Y')  as transaction_date,r.id_repayment,DATE_FORMAT(rp.check_date,'%m/%d/%Y') as check_date,
        rp.check_bank,rp.check_no,rp.amount,r.change_payable-getRepaymentBulkChange(r.id_repayment,0) as change_balance,
        if(deposit_status=0,'For deposit',if(deposit_status=1,'Partially Deposited','Deposited')) as deposit_status_description ,
        r.deposit_status,if(r.deposit_status=0,'info',if(r.deposit_status=1,'primary','success')) as deposit_badge,
        RepaymentDescription(r.payment_for,r.id_repayment) as change_for
        FROM repayment as r
        LEFT JOIN repayment_payment as  rp on rp.id_repayment = r.id_repayment
        WHERE r.id_paymode = 4 AND r.change_payable > 0 AND r.change_status = 0 AND r.status <> 10;");


        $g = new GroupArrayController();

        return $g->array_group_by($changeList,['id_repayment']);
    }

    public function edit($id_change_payable,Request $request){
        $data['change_details'] = DB::table('change_payable')
        ->where('id_change_payable',$id_change_payable)
        ->first();

        $data['opcode'] = 1;

        $r = new Request(['id_repayment'=>$data['change_details']->id_repayment,'id_change_payable'=>$id_change_payable]);


        $rpData = $this->ParseRepaymentDetails($r);



        $data = $data + $rpData + $this->formData();

        $change_application = DB::table('change_payable_details')
                                     ->select(DB::raw("if(id_member > 0 ,1,2) as type,id_member,id_chart_account,amount"))
                                     ->where('id_change_payable',$id_change_payable)
                                     ->get();

  
        $g = new GroupArrayController();
        $data['ChangeApplication'] = $g->array_group_by($change_application,['type']);

        // dd($data);
        return view('change-payable.form',$data);

        dd($data);
    }

    public function view($id_change_payable,Request $request){
        $data = $this->viewData($id_change_payable);

        return view('change-payable.view',$data);
    }
    public function print($id_change_payable){
        $data = $this->viewData($id_change_payable);
        $data['file_name'] = "Change Payable ID# {$id_change_payable}";
        $html = view('change-payable.print',$data);
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '0.33in');
        $pdf->setOption('margin-top', '0.33in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.42in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
        // $pdf->setOption('header-right', 'No.: '.$data['details']->month_year.'-'.$data['details']->id_repayment_statement);
    
        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        return $pdf->stream("{$data['file_name']}.pdf",array('Attachment'=>1));
    }
    public function viewData($id_change_payable){
        $data['details'] = DB::table('change_payable as cp')
                            ->select(DB::raw("cp.id_change_payable,DATE_FORMAT(cp.date,'%m/%d/%Y') as date,cp.total_amount,cp.id_repayment,cp.remarks,if(cp.status=0,'Posted','Cancelled') as status_description,if(cp.status=0,'success2','danger2') as status_badge,cp.status,cp.reason,DATE_FORMAT(cp.status_date,'%m/%d/%Y %h:%i %p') as status_date,cp.id_cash_disbursement,RepaymentDescription(r.payment_for,r.id_repayment) as change_for"))
                            ->leftJoin('repayment as r','r.id_repayment','cp.id_repayment')
                            ->where('id_change_payable',$id_change_payable)
                            ->first();

        $applications = DB::table('change_payable_details as cdd')
                                ->select(DB::raw("if(cdd.id_member > 0,1,2) as type,
                                    if(cdd.id_member > 0,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix),ca.description) as reference,cdd.amount,cdd.id_cash_disbursement"))
                                ->leftJoin('member as m','m.id_member','cdd.id_member')
                                ->leftJoin('chart_account as ca','ca.id_chart_account','cdd.id_chart_account')
                                ->where('cdd.id_change_payable',$id_change_payable)
                                ->where('cdd.amount','>',0)
                                ->get();

        $g = new GroupArrayController();

        $data['Applications'] = $g->array_group_by($applications,['type']);  
        
        return $data;     
    }

    public function ParseRepaymentDetails(Request $request){
        $id_repayment = $request->id_repayment;
        $id_change_payable  = $request->id_change_payable ?? 0;

        $data['details'] = DB::table('repayment')
                                    ->select(DB::raw("id_repayment,total_amount,total_amount+change_payable as total_payment,@released:=getRepaymentBulkChange(id_repayment,$id_change_payable) as released_change,change_payable-@released as change_payable,RepaymentDescription(payment_for,id_repayment) as change_for"))
                                    ->where('id_repayment',$id_repayment)
                                    ->first();

        $data['RESPONSE_CODE'] = "SUCCESS";

        // $data['memberList'] = DB::select()


        if($id_change_payable > 0){
            $data['memberList'] = DB::select("SELECT m.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,if(rt.id_repayment is null,1,0) as data_add
            FROM change_payable_details as cpd
            LEFT JOIN repayment_transaction as rt on rt.id_repayment = ? AND rt.id_member = cpd.id_member
            LEFT JOIN member as m on m.id_member = cpd.id_member
            WHERE cpd.id_change_payable = ?;",[$id_repayment,$id_change_payable]);
        }else{
            $data['memberList'] = DB::table('repayment_transaction as rt')
                                  ->select(DB::raw("m.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,0 as data_add"))
                                  ->leftJoin('member as m','m.id_member','rt.id_member')
                                  ->where('rt.id_repayment',$id_repayment)
                                  ->groupBy('rt.id_member')
                                  ->orDerby('member_name')
                                  ->get();
        }


        return $data;
    }

    public function formData(){
        $data['chartAccounts'] = DB::select("SELECT id_chart_account,description 
        FROM chart_account 
        WHERE id_chart_account_type=4
        ORDER BY description;");


        return $data;
    }
    public function post(Request $request){
        // dd($request->all());
        $id_repayment = $request->id_repayment;

        $id_change_payable = $request->id_change_payable ?? 0;

        $member_change = $request->member_change ?? [];
        $other_income  = $request->other_income ?? [];
        $total = 0;

        $opcode = $request->opcode  ?? 0;

        $change_date = $request->date ?? MySession::current_date();
        $remarks = $request->remarks ?? '';




        if($opcode == 1){
            $id_repayment = DB::table('change_payable')
            ->select(DB::raw('id_repayment'))
            ->where('id_change_payable',$id_change_payable)
            ->first()->id_repayment;

        }



        $details = DB::table('repayment')
                  ->select(DB::raw("id_repayment,change_payable-getRepaymentBulkChange(id_repayment,$id_change_payable) as change_payable"))
                  ->where('id_repayment',$id_repayment)
                  ->first();


        if($details->change_payable <= 0){
            $data['RESPONSE_CODE'] = "SUCCESS";
            $data['message'] = "Invalid Request";

            return response($data);
        }

        $ChangeObj = array();
        foreach($member_change as $mc){
            $ChangeObj[] = [
                'id_change_payable' => 0,
                'id_member'=>$mc['id_member'],
                'id_chart_account'=>0,
                'amount'=>$mc['amount']
            ];
            $total += $mc['amount'];
        }

        foreach($other_income as $oth){
            $ChangeObj[] = [
                'id_change_payable' => 0,
                'id_member'=>0,
                'id_chart_account'=>$oth['id_account'],
                'amount'=>$oth['amount']
            ];         
            $total += $oth['amount'];   
        }

        if($total > $details->change_payable){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Request";

            return response($data);
        }

        $ChangeData = [
            'date'=>$change_date,
            'id_repayment' => $id_repayment,
            'total_amount' => $total,
            'remarks'=>$remarks
        ];

       

        if($opcode == 0){
            $ChangeData['id_user'] = MySession::myId();
            DB::table('change_payable')
            ->insert($ChangeData);


            $id_change_payable = DB::table('change_payable')->where('id_user',MySession::myId())->max('id_change_payable');
        }else{
            $this->UpdateRepaymentChangeStatus($id_repayment,$id_change_payable);
            DB::table('change_payable')
            ->where('id_change_payable',$id_change_payable)
            ->update($ChangeData);
        }


        foreach($ChangeObj as $c=>$ch){
            $ChangeObj[$c]['id_change_payable'] = $id_change_payable;
        }


        DB::table('change_payable_details')
        ->where('id_change_payable',$id_change_payable)
        ->delete();

        DB::table('change_payable_details')
        ->insert($ChangeObj);



        $this->UpdateRepaymentChangeStatus($id_repayment,0);

        $this->GenerateMemberCDV($id_repayment,$id_change_payable);



        $this->GenerateChangeCDV($id_change_payable);


        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['message'] = "Change Payable Successfully Posted";
        $data['ID_CHANGE_PAYABLE'] = $id_change_payable;

        return response($data);

        dd($ChangeObj,$total);

        dd($member_change);

        //
    }

    public function GenerateChangeCDV($id_change_payable){
        $id_cash_disbursement = DB::table('change_payable')->select(DB::raw('ifnull(id_cash_disbursement,0) as id_cash_disbursement'))->where('id_change_payable',$id_change_payable)->first()->id_cash_disbursement ?? 0;
        $totalOthers = DB::table('change_payable_details')
                     ->select(DB::raw("ifnull(SUM(amount),0) as amount"))
                     ->where('id_change_payable',$id_change_payable)
                     ->where('id_chart_account','>',0)
                     ->first()->amount ?? 0;


        if($totalOthers > 0){
            CDVModel::ChangePayableIncomeCDV($id_change_payable,$id_cash_disbursement);
        }elseif($totalOthers == 0 && $id_cash_disbursement > 0){
            DB::table('cash_disbursement')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->update(['status'=>10]);

        }
        // dd($id_cash_disbursement,$totalOthers);

    }

    public function UpdateRepaymentChangeStatus($id_repayment,$id_change_payable){
        DB::table('repayment')
        ->where('id_repayment',$id_repayment)
        ->update([
            'change_status'=>DB::raw("if(change_payable-getRepaymentBulkChange(id_repayment,$id_change_payable) <= 0,1,0 )")
        ]);
    }

    public function GenerateMemberCDV($id_repayment,$id_change_payable){
        // $memberCDV = DB::select("SELECT cp.id_change_payable,k.id_repayment,
        // k.id_member,ifnull(cpd.amount,0) as amount,ifnull(cpd.id_cash_disbursement,0) as id_cash_disbursement
        // FROM (
        // SELECT rt.id_repayment,rt.id_member
        // FROM repayment_transaction as rt
        // WHERE rt.id_repayment = ? AND rt.status <> 10
        // GROUP BY rt.id_member) as k
        // LEFT JOIN change_payable as cp on cp.id_repayment = k.id_repayment AND cp.id_change_payable = ?
        // LEFT JOIN change_payable_details as cpd on cpd.id_change_payable = cp.id_change_payable AND cpd.id_member = k.id_member AND cpd.id_member > 0;",[$id_repayment,$id_change_payable]);

        $memberCDV = DB::select("SELECT cp.id_change_payable,
        cpd.id_member,ifnull(cpd.amount,0) as amount,ifnull(cpd.id_cash_disbursement,0) as id_cash_disbursement
        FROM change_payable as cp
        LEFT JOIN change_payable_details as cpd on cpd.id_change_payable = cp.id_change_payable
        WHERE cp.id_change_payable = ? AND  cpd.id_member > 0",[$id_change_payable]);

        //post CDV if (amount >0 && id_cash_disbursement =0) || ($id_cash_disbursement > 0)

        foreach($memberCDV as $cdv){
            $id_cash_disbursement = DB::table('cash_disbursement')->where('type',12)->where('reference',$id_change_payable)->where('id_member',$cdv->id_member)->max('id_cash_disbursement');
            if(($cdv->amount > 0 && $id_cash_disbursement ==0) || $id_cash_disbursement > 0){
                CDVModel::ChangePayableCDV($cdv->id_change_payable,$cdv->id_member,$id_cash_disbursement);
            }
        }
    }

    public function postStatus(Request $request){
        $id_change_payable = $request->id_change_payable;
        $status = 10;
        $reason = $request->reason;

        $details = DB::table('change_payable')->where('id_change_payable',$id_change_payable)->first();

        if($details->status == 10){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Request";
            return response($data);
        }

        DB::table('change_payable')
        ->where('id_change_payable',$id_change_payable)
        ->update(['status'=>$status,'reason'=>$reason,'status_id_user'=>MySession::myId(),'status_date'=>DB::raw("now()")]);

        $this->UpdateRepaymentChangeStatus($details->id_repayment,0);

        $ChangeCDV = DB::table('change_payable_details')
                     ->select('id_cash_disbursement')
                     ->where('id_change_payable',$id_change_payable)
                     ->where('id_cash_disbursement','>',0)
                     ->get();

        foreach($ChangeCDV as $cdv){
            DB::table('cash_disbursement')
            ->where('id_cash_disbursement',$cdv->id_cash_disbursement)
            ->update(['status'=>10,'description'=>DB::raw("concat(description,' [CANCELLED]')"),'date_cancelled'=>DB::raw("now()"),'cancellation_reason'=>$reason]);            
        }



        $data['RESPONSE_CODE'] = "SUCCESS";
        return response($data);
    }
}
