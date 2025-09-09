<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MySession as MySession;
use DB;
use App\CredentialModel;
class SOAMonitorController extends Controller
{
    public function client_soa_index(Request $request){
        $data['is_soa_create'] = CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/admin/generate_soa');

        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());

        // return $data;
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['is_read'] =$data['credential']->is_read;
        $data['sel_branch'] = (isset($request->sel_branch))?$request->sel_branch:MySession::myBranchID();
        $data['sel_branch'] = ($data['is_read'] ==1)?$data['sel_branch']:MySession::myBranchID();
        $data['txt_date'] = (isset($request->txt_date))?$request->txt_date:MySession::current_date();
        $data['txt_date_to'] = (isset($request->txt_date_to))?$request->txt_date_to:MySession::current_date();

        // return $data['txt_date_to'];
        $data['branches'] = DB::table('lse.tbl_branch')
                            ->select('id_branch','branch_name')
                            ->where(function($query) use ($data){
                                if($data['is_read'] == 0){
                                    $query->where('id_branch', '=', MySession::myBranchID());
                                }
                            })
                            ->orderby('branch_name','ASC')
                            ->get();
        // $data['client_soa'] = DB::connection('cloud_db')
        //                     ->select("SELECT *,if(control_number is null,'No Transaction',if(no_soa_count >0,'Incomplete','Completed')) as status,ROUND((w_soa_count/(no_soa_count+w_soa_count)) * 100,2) as percentage FROM (
        //                                 SELECT tp.account_no as 'account_no',tp.name as 'account_name' ,
        //                                 SUM(CASE WHEN control_number = 0 THEN tc.debit ELSE 0 END) as 'wo_soa_amount',
        //                                 SUM(CASE WHEN control_number =0 THEN 1 ELSE 0 END) as 'no_soa_count',
        //                                 SUM(CASE WHEN control_number >0 THEN 1 ELSE 0 END) as 'w_soa_count',
        //                                 control_number
        //                                 FROM lse.tbl_client_profile tp
        //                                 LEFT JOIN lse.tbl_client_account tc on tc.id_client_profile = tp.id_client_profile AND tc.transaction_date <= '".$data['txt_date']."'
        //                                 WHERE tp.id_branch = ".$data['sel_branch']."
        //                                 GROUP BY tp.id_client_profile) as t");
        $data['client_soa'] = DB::connection('cloud_db')
                              ->select("SELECT *,if(balance is null,'No Transaction',if(no_soa_count >0,'Incomplete','Completed')) as status,if(deb=0,'No Transaction','') as remarks,
                                        ROUND((w_soa_count/(no_soa_count+w_soa_count)) * 100,2) as percentage
                                        FROM (
                                        SELECT tp.id_client_profile,tp.account_no as 'account_no',tp.name as 'account_name' ,
                                        (sum(tc.debit) - sum(tc.credit)) as balance,
                                        SUM(CASE WHEN control_number = 0 THEN tc.debit ELSE 0 END) as 'wo_soa_amount',
                                        SUM(CASE WHEN control_number =0 THEN 1 ELSE 0 END) as 'no_soa_count',
                                        SUM(CASE WHEN control_number >0 THEN 1 ELSE 0 END) as 'w_soa_count',
                                        (sum( if(tc.transaction_date >= '".$data['txt_date']."',tc.debit,0))) as deb 
                                        FROM lse.tbl_client_profile tp
                                        LEFT JOIN lse.tbl_client_account tc on tc.id_client_profile = tp.id_client_profile AND tc.transaction_date <= '".$data['txt_date_to']."'
                                        WHERE tp.id_branch = ".$data['sel_branch']."
                                        GROUP BY tp.id_client_profile) as t");

        return view('soa_monitor.soa_monitor_index',$data);
    }
}
