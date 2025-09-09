<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SchedulerCommands extends Model
{
    public static function JV($date,$reference){
        DB::select("INSERT INTO journal_voucher (jv_type,id_journal_voucher_reference,date,type,payee_type,payee,address,description,id_branch,id_member,id_supplier,id_employee,reference,id_bank,total_amount,status,id_cdv,scheduler)
        SELECT jv_type,id_journal_voucher_reference,? as date,type,jv.payee_type,jv.payee,jv.address,jv.description,jv.id_branch,jv.id_member,jv.id_supplier,jv.id_employee,jv.reference,jv.id_bank
        ,jv.total_amount,0 as status,jv.id_cdv ,1 as scheduler
        FROM journal_voucher as jv
        where id_journal_voucher = ?;",[$date,$reference]);


        $id_journal_voucher = DB::table('journal_voucher')->where('scheduler',1)->max('id_journal_voucher');


        DB::select("INSERT INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,subsi_code,details,reference)
                    SELECT ?,id_chart_account,account_code,description,debit,credit,subsi_code,details,reference 
                    FROM journal_voucher_details as jvd
                    WHERE jvd.id_journal_voucher =?",[$id_journal_voucher,$reference]);        

        return $id_journal_voucher;
    }
    public static function CDV($date,$reference){
        DB::select("INSERT INTO cash_disbursement (date,paymode,paymode_account,type,total,description,payee_type,id_member,id_supplier,id_employee,address,payee,reference,status,id_branch,check_no,check_date,scheduler)
        SELECT ? as date,paymode,paymode_account,type,total,description,payee_type,id_member,id_supplier,id_employee,address,payee,reference,status,id_branch,check_no,check_date,1 as scheduler
        FROM cash_disbursement
        WHERE id_cash_disbursement=?;",[$date,$reference]);


        $id_cash_disbursement = DB::table('cash_disbursement')->where('scheduler',1)->max('id_cash_disbursement');


        DB::select("INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,remarks,details,reference,id_account_code_maintenance )
        SELECT ? as id_cash_disbursement,id_chart_account,account_code,description,debit,credit,remarks,details,reference,id_account_code_maintenance 
        FROM cash_disbursement_details
        WHERE id_cash_disbursement=?",[$id_cash_disbursement,$reference]);   

        return $id_cash_disbursement;       
    }

    public static function Depreciation($date){
        // $date = '2020-01-01';
        $date = date("Y-m-t",strtotime($date));
        $p = array_fill(0,4,$date);

        $dep_entry = DB::select("SELECT 0 as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,debit,credit FROM (
        SELECT 1 as type,ca.depreciation_account as id_chart_account,SUM(adm.depreciation_amount*getAssetQuantityAsOf(ai.asset_code,0,?)) as debit,0 as credit, ai.id_chart_account as dep
        FROM asset_depreciation_month  as adm
        LEFT JOIN asset_item as ai on ai.id_asset_item = adm.id_asset_item
        LEFT JOIN chart_account as ca on ca.id_chart_account = ai.id_chart_account
        where adm.id_journal_voucher=0 and adm.depreciation_date=?
        GROUP BY ai.id_chart_account
        UNION ALL
        SELECT 2 as type,ca.ac_depreciation_account,0 as debit,SUM(adm.depreciation_amount*getAssetQuantityAsOf(ai.asset_code,0,?)) as credit ,ai.id_chart_account as dep
        FROM asset_depreciation_month  as adm
        LEFT JOIN asset_item as ai on ai.id_asset_item = adm.id_asset_item
        LEFT JOIN chart_account as ca on ca.id_chart_account = ai.id_chart_account
        where adm.id_journal_voucher=0 and adm.depreciation_date=?
        GROUP BY ai.id_chart_account) as dep
        LEFT JOIN chart_account as ca on ca.id_chart_account = dep.id_chart_account
        ORDER BY dep,type ASC;",$p);

        if(count($dep_entry) == 0){
            return "NO DATA";
        }

        $sum = collect($dep_entry)->sum('credit');
        
        $entry = json_decode(json_encode($dep_entry),true) ;

        $jv_obj=[
            'jv_type'=>1,
            'date'=>$date,
            'type'=>1,
            'description'=> 'To record the depreciation as of '.date("m/t/Y",strtotime($date)),
            'payee_type'=>4,
            'payee'=>config('variables.coop_abbr'),
            'address'=>config('variables.coop_address'),
            'id_branch'=>1,
            'reference'=>0,
            'total_amount'=>$sum,
            'cash'=>0,
            'scheduler'=>1
        ];

        DB::table('journal_voucher')
        ->insert($jv_obj);

        $id_journal_voucher = DB::table('journal_voucher')->where('date',$date)->where('type',1)->max('id_journal_voucher');

        for($i=0;$i<count($entry);$i++){
            $entry[$i]['id_journal_voucher'] = $id_journal_voucher;
        }

        DB::table('journal_voucher_details')
        ->insert($entry);

        DB::table('asset_depreciation_month')
        ->where('id_journal_voucher',0)
        ->where('depreciation_date',$date)
        ->update(['id_journal_voucher'=>$id_journal_voucher]);

        return $id_journal_voucher;

        // DB::table('journal_voucher')
        // ->
    }

    public static function NetSurplus($date){
        $net = DB::select("SELECT ca.id_chart_account,ifnull(SUM(if(g.normal=1,debit-credit,credit-debit)*if(g.id_chart_account_subtype=2,-1,1) * if(g.id_chart_account_type=4,1,-1)),0) as amount
        FROM (
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,jv.date,credit,debit,YEAR(jv.date) as year,MONTH(jv.date) as month,ca.id_chart_account
        FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE jv.status <> 10 and cat.report_type = 2 AND jv.date <= ?
        UNION ALL
        /*************CV***************/
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,cv.date,credit,debit,YEAR(cv.date) as year,MONTH(cv.date) as month,ca.id_chart_account
        FROM cash_disbursement as cv
        LEFT JOIN cash_disbursement_details as cvd on cvd.id_cash_disbursement = cv.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE cv.status <> 10 and cat.report_type = 2 AND cv.date <= ?
        UNION ALL
        /*************CRV***************/
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,crv.date,credit,debit,YEAR(crv.date) as year,MONTH(crv.date) as month,ca.id_chart_account
        FROM cash_receipt_voucher as crv
        LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE crv.status <> 10 and cat.report_type = 2 AND crv.date <= ?
        UNION ALL
        /*************BEGINNING***************/
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,date,credit,debit,YEAR(date) as year,MONTH(date) as month,ca.id_chart_account
        FROM chart_beginning 
        LEFT JOIN chart_account as ca on ca.id_chart_account = chart_beginning.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE cat.report_type = 2 AND date <= ? and chart_beginning.status <> 10) as g
        LEFT JOIN chart_account as ca on ca.id_chart_account = 34
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type",array_fill(0,4,$date))[0];


        $entry_net = DB::select("SELECT ifnull(SUM(debit),0) as amount
                                FROM journal_voucher_details as jvd
                                LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = jvd.id_journal_voucher
                                WHERE jv.date <= ? AND jv.status <> 10 AND jvd.id_chart_account=34",[$date])[0]->amount;



        $id_c = $net->id_chart_account;
        $sum = $amount = $net->amount-$entry_net;

        if($sum <= 0){
            return 0;
        }



        // dd($id_c);
        //$net [debit]
        $entry = DB::select("SELECT id_chart_account,account_code,description,$amount as debit,0 as credit FROM chart_account where id_chart_account=$id_c
                        UNION ALL
                        SELECT id_chart_account,account_code,description,0 as debit,ROUND($amount*(percentage/100),2) as credit FROM chart_account where percentage > 0;");


        $entry = json_decode(json_encode($entry),true) ;

        $jv_obj=[
            'jv_type'=>1,
            'date'=>$date,
            'type'=>1,
            'description'=> "Net Surplus",
            'payee_type'=>4,
            'payee'=>config('variables.coop_abbr'),
            'address'=>config('variables.coop_address'),
            'id_branch'=>1,
            'reference'=>0,
            'total_amount'=>$sum,
            'cash'=>0,
            'scheduler'=>1
        ];

        DB::table('journal_voucher')
        ->insert($jv_obj);

        $id_journal_voucher = DB::table('journal_voucher')->where('date',$date)->where('type',1)->max('id_journal_voucher');

        for($i=0;$i<count($entry);$i++){
            $entry[$i]['id_journal_voucher'] = $id_journal_voucher;
        }

        DB::table('journal_voucher_details')
        ->insert($entry);



        return $id_journal_voucher;
    }
}
