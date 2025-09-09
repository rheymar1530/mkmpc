<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use DB;
use Illuminate\Support\Facades\Hash;
class TestController extends Controller
{

    public function test_gg(){
        // $gg = DB::table('cash_disbursement')
    }


    public function test(){
        ini_set('memory_limit', '-1');
        set_time_limit(-1);

        // return;

        // $this->update_ledger_id_act_loan();
        // return $this->generate_member_account();


        return $this->update_id_parent_loan();
        // $jsonString=file_get_contents(public_path("/ledger2.json")) ;

        $jsonString=file_get_contents(public_path("/loan_ledger.json")) ;

        $loan = json_decode($jsonString,true);

        // dd($loan);
        // $this->active_loan($loan);


        // return ;

        // return;
        // return $loan;


        // return $loan[0];

        // return $this->update_id_parent_loan();

        // return 123;
        // return $this->active_loan($loan);
   


        // foreach($loan as $l){
        //     foreach($l as $k=>$row){
        //         array_push($key,$k);
        //     }

        //     return $key;
        // }

        // return $key;
        // return $loan;


        $l_key = ['MembersName','typeofloan','date','reference','Particular','Amountpaid','Overdue','LoanDebit','LoanPaypenalty','LoanPayinterest','LoanPayprincipal','BalPenalty','BalInt','BalPrincipal','LoanBalance','status','LineNo','recno'];


        $dt_key = ['date'];

        $j = array_chunk($loan,1000,true);

        foreach($j as $ins){
            $insert_obj = array();
            foreach($ins as $i){
                $temp = array();
                foreach($l_key as $k){
                    if(in_array($k,$dt_key)){
                         $temp[$k] = date('Y-m-d', strtotime($i[$k]));
                    }else{
                        $temp[$k] = $i[$k] ?? 0;
                    }
                }
                array_push($insert_obj,$temp);

                
            }   

           DB::table('dummy_loan_ledger2')
           ->insert($insert_obj);
        }

        return "SUCCESS";


        // foreach($j as $)
        DB::table('dummy_loan_ledger')
        ->insert($loan);

        return "SUCCESS";
        foreach($loan as $l){
            foreach($l as $k=>$row){
                array_push($key,$k);
            }
        }

        return $key;
    }

    public function active_loan($json){

        $keys = ['NAME','TYPEOFLOAN','TERM','INTERESTRATE','AMOUNTGRANTED','LOANRELEASE','DATEGRANTED','MATURITYDATE','AMORTSCHED','SCHEDINT','SCHEDPRIN','GAMOUNTDUE','AMOUNTPAID','PAYSURPEN','ACCUMINTEREST','PAYINTEREST','PAYPRINCIPAL','PRINCIPALBALANCE','RECNO'];

        $dt_field = ['DATEGRANTED','MATURITYDATE'];

        $insert_obj = array();
        foreach($json as $loans){
            $temp = array();
            foreach($keys as $k){
                if(in_array($k,$dt_field)){
                    $temp[$k] = date('Y-m-d', strtotime($loans[$k]));
                }else{
                    $temp[$k]= $loans[$k] ?? '';
                }
                
            }
            array_push($insert_obj,$temp);
        }

        // dd($insert_obj);

        // dd($insert_obj);


        // foreach($json as $loans){
        //     $temp = array();
        //     foreach($loans as $key=>$val){
        //         $temp[str_replace(" ","", $key)] = $val;
        //     }
        //     array_push($insert_obj,$temp);
        // }

        DB::table('dummy_act_loan')->insert($insert_obj);

        dd("SUCCESS");
        return "SUCCESS";
    }

    public function update_id_parent_loan(){
        $data = DB::table('dummy_loan_ledger')
                ->select(DB::raw("id_dummy_loan_ledger,Particular,id_dummy_loan_service,id_dummy_act_loan"))
                ->orDerby('ordering')
                ->get();

        $current_id_loan_service = 0;
        $c_id_dummy_loan = 0;
        foreach($data as $d){
            if($d->Particular == "release" || $d->Particular == "Disbursement" || $d->Particular == ""){
                if($d->id_dummy_act_loan > 0){
                    $c_id_dummy_loan = $d->id_dummy_act_loan;
                }else{
                    $c_id_dummy_loan = 0;
                } 
                $current_id_loan_service = $d->id_dummy_loan_service;
            }elseif($d->Particular != "release" && $current_id_loan_service == $d->id_dummy_loan_service){
                DB::table('dummy_loan_ledger')
                ->where('id_dummy_loan_ledger',$d->id_dummy_loan_ledger)
                ->update(['id_parent_dummy_loan'=>$c_id_dummy_loan]);
                $current_id_loan_service = $d->id_dummy_loan_service;
            }else{
                $current_id_loan_service = 0;
            }
            
        }
        return "success";
    }

    public function update_ledger_id_act_loan(){
        $ledger = DB::table('dummy_act_loan')
                  ->get();

        foreach($ledger as $led){
            DB::table('dummy_loan_ledger')
            ->where('MembersName',$led->NAME)
            ->where('id_dummy_loan_service',$led->id_dummy_loan_service)
            ->where('date',$led->DATEGRANTED)
            ->where('particular',"release")
            ->update(['id_dummy_act_loan'=>$led->id_dummy_act_loan]);
        }
        dd($ledger);
    }
    public function generate_member_account(){
        $member = DB::table('member')
                  ->select('id_member',DB::raw("concat(replace(lower(last_name),'ñ','n'),id_member) as credential,id_member"))
                  ->get();

        foreach($member as $m){
            $hashed_password = Hash::make($m->credential, ['rounds' => 12]);

            DB::select("INSERT into cms_users (id_member,name,photo,email,password,id_cms_privileges,id_branch,settings)
                        SELECT id_member,concat(first_name,' ',last_name,' ',suffix) as name,image_path,email,'$hashed_password' as password,
                        7 as id_cms_privileges,id_branch,''
                        FROM member
                        WHERE id_member =".$m->id_member);
        }
        return $member;
    }
}

/****
 *  
 *  if the particular is "release" 
 *      if the id_dummy_act_loan is equal to 0 then c_id_dummy_loan = 0
 *      else c_id_dummy_loan = id_dummy_act_loan
 * 
 * and id_dummy_act_loan != 0 then id_dummy_act_loan will store to variable (c_id_dummy_loan)
 *  
 *  if the particular is not release and current id loan service = row id laan service then set id_parent_dummy_loan = c_id_dummy_loan
 * 
 * 
 * 
 *  set current id loan service
 * ****/ 