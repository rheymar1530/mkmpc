<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;

class EntrySchedulerController extends Controller
{
    public function generate_dates($start_date,$end_date){

    }
    public function index(){
        $date = '2022-10-31';

        // MySession::current_date()
        $newDate = date("Y-m-d", strtotime("+1 month", strtotime($date)));

        return $newDate;
        return $this->execute_scheduler();
    }
    public function execute_scheduler(){
        $pending_sch = DB::table('scheduler as s')
                      ->select(DB::raw("se.date,se.reference,s.id_scheduler,s.books,se.id_scheduler_entry,s.type"))
                      ->leftJoin('scheduler_entry as se','se.id_scheduler','s.id_scheduler')
                      ->where('s.status',1)
                      ->where('se.date','<=',MySession::current_date())
                      ->where('se.done',0)
                      ->get();
        
        foreach($pending_sch as $ps){
            if($ps->books == "JV"){
                return $this->GenerateJV($ps->id_scheduler_entry,$ps->reference,$ps->date);
            }
        }

        return "SUCCESS";       
    }

    public function GenerateJV($id_scheduler_entry,$jv_reference,$date){

        DB::select("INSERT INTO journal_voucher (jv_type,id_journal_voucher_reference,date,type,payee_type,payee,address,description,id_branch,id_member,id_supplier,id_employee,reference,id_bank,total_amount,status,id_cdv,scheduler)
        SELECT 1 as jv_type,0 as id_journal_voucher_reference,? as date,1 as type,jv.payee_type,jv.payee,jv.address,jv.description,jv.id_branch,jv.id_member,jv.id_supplier,jv.id_employee,jv.reference,jv.id_bank
        ,jv.total_amount,0 as status,jv.id_cdv ,1 as scheduler
        FROM journal_voucher as jv
        where id_journal_voucher = ?;",[$date,$jv_reference]);


        $id_journal_voucher = DB::table('journal_voucher')->where('scheduler',1)->max('id_journal_voucher');


        DB::select("INSERT INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,subsi_code,details,reference)
                    SELECT ?,id_chart_account,account_code,description,debit,credit,subsi_code,details,reference 
                    FROM journal_voucher_details as jvd
                    WHERE jvd.id_journal_voucher =?",[$id_journal_voucher,$jv_reference]);

        DB::table('scheduler_entry')
        ->where('id_scheduler_entry',$id_scheduler_entry)
        ->update([
            'done'=>1,
            'date_executed'=>DB::raw("now()"),
            'entry_reference'=>$id_journal_voucher
        ]);
                    

        return "SUCCESS";
    }
}
