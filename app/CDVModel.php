<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class CDVModel extends Model
{
    public static function Payroll($id_payroll){
        $id_cash_disbursement = DB::table('payroll')->select('id_cash_disbursement')->where('id_payroll',$id_payroll)->first()->id_cash_disbursement;

        $payee = config('variables.coop_abbr');
        if($id_cash_disbursement == 0){
            // DB::select("INSERT INTO cash_disbursement (date,type,description,id_member,payee,reference,status,total,id_branch,address,payee_type)
            //     SELECT p.date_released as date,6 as type,concat('PAYROLL ID# ',p.id_payroll,' FOR PERIOD OF ',DATE_FORMAT(p.period_start,'%m/%d/%Y'),' - ',DATE_FORMAT(p.period_end,'%m/%d/%Y'),if(p.id_payroll_mode=3,concat(' (',p.no_days,' Days)'),''))  as description,0 as id_member,'$payee' as payee,p.id_payroll as reference,0 as status,
            //     SUM(net_income) as total,1 as branch,'' as address, 4 as payee_type
            //     FROM payroll as p 
            //     LEFT JOIN payroll_employee as pe on pe.id_payroll = p.id_payroll
            //     where p.id_payroll = ?;",[$id_payroll]);

            DB::select("INSERT INTO cash_disbursement (date,type,description,id_member,payee,reference,status,total,id_branch,address,payee_type)
                SELECT p.date_released as date,6 as type,concat('PAYROLL ID# ',p.id_payroll,' FOR PERIOD OF ',DATE_FORMAT(p.period_start,'%m/%d/%Y'),' - ',DATE_FORMAT(p.period_end,'%m/%d/%Y'),if(p.id_payroll_mode=3,concat(' (',p.no_days,' Days)'),''))  as description,0 as id_member,FormatName(e.first_name,e.middle_name,e.last_name,e.suffix) as payee,p.id_payroll as reference,0 as status,
                SUM(net_income) as total,e.id_branch as branch,e.address as address, 4 as payee_type
                FROM payroll as p 
                LEFT JOIN payroll_employee as pe on pe.id_payroll = p.id_payroll
                LEFT JOIN employee as e on e.id_employee = p.disbursed_by
                where p.id_payroll = ?;",[$id_payroll]);

            $id_cash_disbursement = DB::table('cash_disbursement')->where('type',6)->where('reference',$id_payroll)->max('id_cash_disbursement');

            DB::table('payroll')->where('id_payroll',$id_payroll)->update(['id_cash_disbursement'=>$id_cash_disbursement]);
        }else{
            // DB::select("UPDATE payroll as p
            //     LEFT JOIN cash_disbursement as jv on jv.id_cash_disbursement =p.id_cash_disbursement
            //     INNER JOIN 
            //     (SELECT pe.id_payroll,SUM(net_income) as net_income
            //         FROM payroll_employee as pe
            //         WHERE pe.id_payroll=$id_payroll) t
            //     ON t.id_payroll = p.id_payroll
            //     SET jv.date = p.date_released,jv.description = concat('PAYROLL ID# ',p.id_payroll,' FOR PERIOD OF ',DATE_FORMAT(p.period_start,'%m/%d/%Y'),' - ',DATE_FORMAT(p.period_end,'%m/%d/%Y'),if(p.id_payroll_mode=3,concat(' (',p.no_days,' Days)'),'')),
            //     jv.payee = '$payee',jv.address='',total =  t.net_income
            //     WHERE p.id_cash_disbursement = $id_cash_disbursement;");

            DB::select("UPDATE payroll as p
                LEFT JOIN cash_disbursement as jv on jv.id_cash_disbursement =p.id_cash_disbursement
                LEFT JOIN employee as e on e.id_employee = p.disbursed_by
                INNER JOIN 
                (SELECT pe.id_payroll,SUM(net_income) as net_income
                    FROM payroll_employee as pe
                    WHERE pe.id_payroll=$id_payroll) t
                ON t.id_payroll = p.id_payroll
                SET jv.date = p.date_released,jv.description = concat('PAYROLL ID# ',p.id_payroll,' FOR PERIOD OF ',DATE_FORMAT(p.period_start,'%m/%d/%Y'),' - ',DATE_FORMAT(p.period_end,'%m/%d/%Y'),if(p.id_payroll_mode=3,concat(' (',p.no_days,' Days)'),'')),
                jv.payee = FormatName(e.first_name,e.middle_name,e.last_name,e.suffix),jv.address=e.address,total =  t.net_income
                WHERE p.id_cash_disbursement = $id_cash_disbursement;");
            DB::table('cash_disbursement_details')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->delete();
        }

        // SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
        // SUM(total_allowance) as debit,0 as credit,'' as details,pe.id_payroll
        // FROM payroll_employee as pe 
        // LEFT JOIN chart_account as ca on ca.id_chart_account = 46
        // where pe.id_payroll = $id_payroll
        // HAVING SUM(total_allowance) > 0
        DB::select("
            INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference)   
            SELECT  $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
            SUM(basic_pay+ot+night_shift_dif+holiday+paid_leaves+salary_adjustment+13th_month+others) as debit,0 as credit,'' as details,pe.id_payroll as reference
            FROM payroll_employee as pe 
            LEFT JOIN employee as em on em.id_employee = pe.id_employee
            LEFT JOIN chart_account as ca on ca.id_chart_account = if(em.id_employee_type = 1,45,44)
            where pe.id_payroll =  $id_payroll
            GROUP BY em.id_employee_type
            UNION ALL

            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
            SUM(pea.amount) as debit,0 as credit,'' as details,pe.id_payroll
            FROM payroll as pe 
            LEFT JOIN payroll_employee_allowances as pea on pea.id_payroll = pe.id_payroll
            LEFT JOIN allowance_name as an on an.id_allowance_name = pea.id_allowance_name
            LEFT JOIN chart_account as ca on ca.id_chart_account = an.id_chart_account
            where pe.id_payroll = $id_payroll
            GROUP BY ca.id_chart_account
            HAVING SUM(pea.amount) > 0
           
            UNION ALL
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
            0 as debit,SUM(insurance+sss_loan+hdmf_loan+sss+philhealth+hdmf) as credit,'' as details,pe.id_payroll
            FROM payroll_employee as pe 
            LEFT JOIN chart_account as ca on ca.id_chart_account = 27
            where pe.id_payroll = $id_payroll 
            HAVING SUM(insurance+sss_loan+hdmf_loan+sss+philhealth+hdmf) > 0

            -- -- SSS -- 
            -- UNION ALL
            -- SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
            -- 0 as debit,SUM(sss) as credit,'' as details,pe.id_payroll
            -- FROM payroll_employee as pe 
            -- LEFT JOIN chart_account as ca on ca.id_chart_account = 83
            -- where pe.id_payroll = $id_payroll 
            -- HAVING SUM(sss) > 0
            -- -- PHILHEALTH -- 
            -- UNION ALL
            -- SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
            -- 0 as debit,SUM(philhealth) as credit,'' as details,pe.id_payroll
            -- FROM payroll_employee as pe 
            -- LEFT JOIN chart_account as ca on ca.id_chart_account = 84
            -- where pe.id_payroll = $id_payroll 
            -- HAVING SUM(philhealth) > 0

            -- -- PAG-IBIG -- 
            -- UNION ALL
            -- SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
            -- 0 as debit,SUM(hdmf) as credit,'' as details,pe.id_payroll
            -- FROM payroll_employee as pe 
            -- LEFT JOIN chart_account as ca on ca.id_chart_account = 85
            -- where pe.id_payroll = $id_payroll 
            -- HAVING SUM(hdmf) > 0
            UNION ALL
            -- SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
            -- 0 as debit,SUM(ca) as credit,'' as details,pe.id_payroll
            -- FROM payroll_employee as pe 
            -- LEFT JOIN chart_account as ca on ca.id_chart_account = 7
            -- where pe.id_payroll = $id_payroll 
            -- HAVING SUM(ca) > 0
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
                        0 as debit,SUM(amount) as credit,'' as details,p.id_payroll FROM (
            SELECT if(pca.id_cash_disbursement =0,jdv.id_chart_account,cdv.id_chart_account) as id_chart_account,pca.amount,pca.id_payroll FROM payroll_ca as pca
            LEFT JOIN journal_voucher_details as jdv on jdv.id_journal_voucher = pca.id_journal_voucher
            LEFT JOIN cash_disbursement_details as cdv on cdv.id_cash_disbursement = pca.id_cash_disbursement
            where pca.id_payroll = $id_payroll) as p
            LEFT JOIN chart_account as ca on ca.id_chart_account = p.id_chart_account
            WHERE ca.is_cash_advance = 1
            GROUP BY id_chart_account
            HAVING SUM(amount) > 0
            UNION ALL
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
            0 as debit,SUM(absences+late) as credit,'' as details,pe.id_payroll
            FROM payroll_employee as pe 
            LEFT JOIN chart_account as ca on ca.id_chart_account = 45
            where pe.id_payroll = $id_payroll
            HAVING SUM(absences+late) > 0
            UNION ALL
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
            0 as debit,SUM(wt) as credit,'' as details,pe.id_payroll
            FROM payroll_employee as pe 
            LEFT JOIN chart_account as ca on ca.id_chart_account = 26
            where pe.id_payroll = $id_payroll
            HAVING SUM(wt) > 0
            UNION ALL
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,
            0 as debit,SUM(net_income) as credit,'' as details,pe.id_payroll
            FROM payroll_employee as pe 
            LEFT JOIN payroll as p on p.id_payroll = pe.id_payroll
            LEFT JOIN tbl_bank as tb on tb.id_bank = p.id_bank
            LEFT JOIN chart_account as ca on ca.id_chart_account = if(p.id_bank=0,1,tb.id_chart_account)
            where pe.id_payroll = $id_payroll;");
    }
    public static function ChangeCDV($id_repayment_change,$edited,$cancel){
        $id_cash_disbursement = DB::table('cash_disbursement')->where('reference',$id_repayment_change)->where('type',7)->max('id_cash_disbursement');

        if(!isset($id_cash_disbursement)){
            DB::select("INSERT INTO cash_disbursement (date,type,description,id_member,payee,reference,status,total,id_branch,address,payee_type)
                        SELECT date,7 as type,concat('(ID#',id_repayment_change,') CHANGE FOR REPAYMENT ID# ',id_repayment_transaction) as description,
                        rc.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)  as payee,rc.id_repayment_change as reference,0 as status,amount as total,
                        m.id_branch,m.address,2 as payee_type
                        FROM repayment_change as rc
                        LEFT JOIN member as m on m.id_member = rc.id_member
                         where id_repayment_change = $id_repayment_change;");
            $id_cash_disbursement = DB::table('cash_disbursement')->where('reference',$id_repayment_change)->where('type',7)->max('id_cash_disbursement');
        }else{
            if($cancel){

               $d = DB::table('repayment_change')->select(DB::raw("cancellation_reason,cancelled_at"))->where('id_repayment_change',$id_repayment_change)->first();
               DB::table('cash_disbursement')
               ->where('id_cash_disbursement',$id_cash_disbursement)
               ->update(['description'=>DB::raw("concat(REPLACE(description,' [CANCELLED]',''),' [CANCELLED]')"),'status'=>10,'cancellation_reason'=>$d->cancellation_reason,'date_cancelled'=>$d->cancelled_at]);    
               return;            
            }
            if($edited){
                // DB::select("UPDATE repayment_change as rc
                //             LEFT JOIN member as m on m.id_member = rc.id_member
                //             LEFT JOIN cash_disbursement as jv on jv.id_cash_disbursement = rc.id_cash_disbursement
                //             SET jv.date = rc.date,jv.description=concat('(ID#',id_repayment_change,') CHANGE FOR REPAYMENT ID# ',id_repayment_transaction,' ',REPLACE(description,' [EDITED]',''),' [EDITED]'),jv.total = rc.amount
                //             WHERE rc.id_repayment_change = ?",[$id_repayment_change]);
                // DB::select("UPDATE repayment_change as rc
                //             LEFT JOIN member as m on m.id_member = rc.id_member
                //             LEFT JOIN cash_disbursement as jv on jv.id_cash_disbursement = rc.id_cash_disbursement
                //             SET jv.date = rc.date,jv.description=concat('(ID#',id_repayment_change,') CHANGE FOR REPAYMENT ID# ',id_repayment_transaction,' ',REPLACE(description,' [EDITED]',''),' [EDITED]'),jv.total = rc.amount
                //             WHERE rc.id_repayment_change = ?",[$id_repayment_change]);
                //  DB::select("UPDATE repayment_change as rc
                //             LEFT JOIN member as m on m.id_member = rc.id_member
                //             LEFT JOIN cash_disbursement as jv on jv.id_cash_disbursement = rc.id_cash_disbursement
                //             SET jv.date = rc.date,jv.description=concat('(ID#',id_repayment_change,') CHANGE FOR REPAYMENT ID# ',id_repayment_transaction),jv.total = rc.amount
                //             WHERE rc.id_repayment_change = ?",[$id_repayment_change]);
                DB::select("UPDATE repayment_change as rc
                LEFT JOIN member as m on m.id_member = rc.id_member
                LEFT JOIN cash_disbursement as jv on jv.id_cash_disbursement = rc.id_cash_disbursement
                SET jv.date = rc.date,jv.description=concat('(ID#',id_repayment_change,') CHANGE FOR REPAYMENT ID# ',id_repayment_transaction),jv.total = rc.amount,
                jv.id_member = rc.id_member,jv.payee= FormatName(m.first_name,m.middle_name,m.last_name,m.suffix),jv.id_branch = m.id_branch,jv.address =m.address,jv.payee_type=2
                WHERE rc.id_repayment_change = ?",[$id_repayment_change]);


            }

            DB::table('cash_disbursement_details')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->delete();
        }

        DB::select("insert INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference)
                    SELECT 
                    $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code as account_code,ca.description,amount as debit,0 as credit,
                    'Change from repayment' as remarks,id_repayment_change as reference
                    FROm repayment_change
                    LEFT JOIN chart_account as ca on ca.id_chart_account = 23
                    WHERE id_repayment_change = ?
                    UNION ALL
                    SELECT 
                    $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,amount as credit,
                    'Change release' as remarks,id_repayment_change as reference
                    FROm repayment_change
                    LEFT JOIN chart_account as ca on ca.id_chart_account = 1
                    WHERE id_repayment_change = ?;",[$id_repayment_change,$id_repayment_change]);

        $output['status'] = "SUCCESS";
        $output['id_cash_disbursement'] = $id_cash_disbursement;

        return $output;
    }

    public static function  ATMSwipeCDV($id_atm_swipe){
        $id_cash_disbursement = DB::table('atm_swipe')->select('id_cash_disbursement')->where('id_atm_swipe',$id_atm_swipe)->first()->id_cash_disbursement;

        if($id_cash_disbursement == 0){
            DB::select("INSERT INTO cash_disbursement (date,paymode,paymode_account,type,description,id_member,id_employee,payee,reference,status,total,id_branch,address,payee_type)
            SELECT  atm.date,1 as paymode,1 as paymode_account,8 as type,concat('ATM SWIPE ID# ',atm.id_atm_swipe) as description,atm.id_member,atm.id_employee,
            CASE
            WHEN client_type =2 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
            WHEN client_type = 3 THEN FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
            ELSE client END as payee,atm.id_atm_swipe as reference,0 as status,atm.change_payable as total,
            CASE
            WHEN client_type =2 THEN m.id_branch
            WHEN client_type = 3 THEN e.id_branch
            ELSE 0 END as id_branch,
            CASE
            WHEN client_type =2 THEN m.address
            WHEN client_type = 3 THEN e.address
            ELSE '' END as address,
            client_type as payee_type
            FROM atm_swipe as atm
            LEFT JOIN member as m on m.id_member = if(atm.client_type=2,atm.id_member,0)
            LEFT JOIN employee as e on e.id_employee = if(atm.client_type=3,atm.id_employee,0)
            WHERE id_atm_swipe = ?;",[$id_atm_swipe]);

            $id_cash_disbursement = DB::table('cash_disbursement')->where('type',8)->max('id_cash_disbursement');
            DB::table('atm_swipe')->where('id_atm_swipe',$id_atm_swipe)->update(['id_cash_disbursement'=>$id_cash_disbursement]);

        }else{

            DB::select("UPDATE atm_swipe as atm     
                        LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = atm.id_cash_disbursement
                        LEFT JOIN member as m on m.id_member = if(atm.client_type=2,atm.id_member,0)
                        LEFT JOIN employee as e on e.id_employee = if(atm.client_type=3,atm.id_employee,0)
                        set cd.date = atm.date,cd.id_member = atm.id_member,cd.id_employee = atm.id_employee,
                        cd.payee =  CASE
                                    WHEN client_type =2 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
                                    WHEN client_type = 3 THEN FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
                                    ELSE client END,
                        cd.total = atm.change_payable,
                        cd.id_branch = CASE
                                    WHEN client_type =2 THEN m.id_branch
                                    WHEN client_type = 3 THEN e.id_branch
                                    ELSE 0 END,
                        cd.address =CASE
                                    WHEN client_type =2 THEN m.address
                                    WHEN client_type = 3 THEN e.address
                                    ELSE '' END,
                        cd.payee_type = atm.client_type
                        where id_atm_swipe = ?;",[$id_atm_swipe]);


            DB::table('cash_disbursement_details')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->delete();
        }


        // DB::select("INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference)
        // SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,(amount) as debit,0 as credit,'' as remarks,atm.id_atm_swipe
        // FROM atm_swipe as atm
        // left join tbl_bank as tb on tb.id_bank = atm.id_bank
        // LEFT JOIN chart_account as ca on ca.id_chart_account = tb.id_chart_account
        // WHERE id_atm_swipe = ?
        // UNION ALL
        // SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,change_payable as credit,'' as remarks,atm.id_atm_swipe
        // FROM atm_swipe as atm
        // LEFT JOIN account_code_maintenance as acm on acm.id_account_code_maintenance = 12
        // LEFT JOIN chart_account as ca on ca.id_chart_account = acm.id_chart_account
        // WHERE id_atm_swipe = ?
        // UNION ALL
        // SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,transaction_charge as credit,'' as remarks,atm.id_atm_swipe
        // FROM atm_swipe as atm
        // LEFT JOIN account_code_maintenance as acm on acm.id_account_code_maintenance = 14
        // LEFT JOIN chart_account as ca on ca.id_chart_account = acm.id_chart_account
        // WHERE id_atm_swipe = ?;",[$id_atm_swipe,$id_atm_swipe,$id_atm_swipe]);

        DB::select("INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference)
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,change_payable as debit,0 as credit,'' as remarks,atm.id_atm_swipe
        FROM atm_swipe as atm
        LEFT JOIN account_code_maintenance as acm on acm.id_account_code_maintenance = 11
        LEFT JOIN chart_account as ca on ca.id_chart_account = acm.id_chart_account
        WHERE id_atm_swipe = ?
        UNION ALL
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,change_payable as credit,'' as remarks,atm.id_atm_swipe
        FROM atm_swipe as atm
        LEFT JOIN account_code_maintenance as acm on acm.id_account_code_maintenance = 12
        LEFT JOIN chart_account as ca on ca.id_chart_account = acm.id_chart_account
        WHERE id_atm_swipe = ?;",[$id_atm_swipe,$id_atm_swipe,$id_atm_swipe]);

        return $id_cash_disbursement;
    }

    public static function BankTransactionCDV($id_bank_transaction){
        $id_cash_disbursement = DB::table('bank_transaction')->select('id_cash_disbursement')->where('id_bank_transaction',$id_bank_transaction)->first()->id_cash_disbursement;

        if($id_cash_disbursement == 0){
            DB::select("INSERT INTO cash_disbursement (date,type,description,id_member,payee,reference,status,total,id_branch,address,payee_type)
            SELECT date,8 as type,concat('Bank Transaction ID# ',id_bank_transaction,' ',if(bt.type=1,concat('Deposit to ', tb.bank_name),if(bt.type=2,concat('Withdrawal from ',tb.bank_name),concat('Transfer from ',tb.bank_name,' to ',tb_trans.bank_name)))) as description,bt.id_member,
            if(bt.type=3,tb_trans.bank_name,bt.name) as payee,id_bank_transaction,0 as status,bt.amount,ifnull(m.id_branch,1) as id_branch,m.address,4 as payee_type
            FROM bank_transaction as bt
            LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
            LEFT JOIN tbl_bank as tb_trans on tb_trans.id_bank = bt.id_bank_transfer_to
            LEFT JOIN member as m on m.id_member = bt.id_member
            WHERE bt.id_bank_transaction = ?;",[$id_bank_transaction]);

            $id_cash_disbursement = DB::table('cash_disbursement')->where('type',8)->max('id_cash_disbursement');
        }else{
            DB::select("UPDATE bank_transaction as bt
                        LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
                        LEFT JOIN tbl_bank as tb_trans on tb_trans.id_bank = bt.id_bank_transfer_to
                        LEFT JOIN member as m on m.id_member = bt.id_member
                        LEFT JOIN cash_disbursement as cdv on cdv.id_cash_disbursement = bt.id_cash_disbursement
                        SET cdv.date =bt.date,cdv.description=concat('Bank Transaction ID# ',id_bank_transaction,' ',if(bt.type=1,concat('Deposit to ', tb.bank_name),if(bt.type=2,concat('Withdrawal from ',tb.bank_name),concat('Transfer from ',tb.bank_name,' to ',tb_trans.bank_name)))),
                        cdv.id_member = bt.id_member,cdv.payee =if(bt.type=3,tb_trans.bank_name,bt.name),cdv.total = bt.amount,cdv.id_branch=ifnull(m.id_branch,1)
                        WHERE bt.id_bank_transaction = ?;",[$id_bank_transaction]);

            DB::table('cash_disbursement_details')->where('id_cash_disbursement',$id_cash_disbursement)->delete();
        }

        DB::select("
            INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference)
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,amount as debit,0 as credit,'',id_bank_transaction from bank_transaction as bt
            LEFT JOIN tbl_bank as tb on tb.id_bank = if(bt.type=2 or bt.type=1,bt.id_bank,bt.id_bank_transfer_to)
            LEFT JOIN chart_account as ca on ca.id_chart_account = if(bt.type=2,1,tb.id_chart_account)
            where id_bank_transaction = ?
            UNION ALL
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,amount as credit,'',id_bank_transaction from bank_transaction as bt
            LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
            LEFT JOIN chart_account as ca on ca.id_chart_account = if(bt.type=3 OR bt.type=2,tb.id_chart_account,1)
            where id_bank_transaction = ?;",[$id_bank_transaction,$id_bank_transaction]);

        DB::table('bank_transaction')
        ->where('id_bank_transaction',$id_bank_transaction)
        ->update(['id_cash_disbursement'=>$id_cash_disbursement]);
    }

    public static function CBUWithdrawalCDV($id_cbu_withdrawal){
        $id_cash_disbursement = DB::table('cbu_withdrawal')->select('id_cash_disbursement')->where('id_cbu_withdrawal',$id_cbu_withdrawal)->first()->id_cash_disbursement;

        if($id_cash_disbursement == 0){
            DB::select("INSERT INTO cash_disbursement (date,paymode,paymode_account,type,description,id_member,payee,reference,status,total,id_branch,address,payee_type)
            SELECT cbu.date_released,1 as paymode,1 as paymode_account,9 as type,concat('CBU WITHDRAWAL ID# ',cbu.id_cbu_withdrawal) as description,cbu.id_member,
            FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)as payee,cbu.id_cbu_withdrawal as reference,0 as status,cbu.amount as total,
            m.id_branch,m.address,2 as payee_type
            FROM cbu_withdrawal as cbu
            LEFT JOIN member as m on m.id_member = cbu.id_member
            WHERE cbu.id_cbu_withdrawal = ?;",[$id_cbu_withdrawal]);

            $id_cash_disbursement = DB::table('cash_disbursement')->where('type',9)->max('id_cash_disbursement');

            DB::table('cbu_withdrawal')->where('id_cbu_withdrawal',$id_cbu_withdrawal)->update(['id_cash_disbursement'=>$id_cash_disbursement]);


        }else{

            // DB::select("UPDATE atm_swipe as atm     
            //             LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = atm.id_cash_disbursement
            //             LEFT JOIN member as m on m.id_member = if(atm.client_type=2,atm.id_member,0)
            //             LEFT JOIN employee as e on e.id_employee = if(atm.client_type=3,atm.id_employee,0)
            //             set cd.date = atm.date,cd.id_member = atm.id_member,cd.id_employee = atm.id_employee,
            //             cd.payee =  CASE
            //                         WHEN client_type =2 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
            //                         WHEN client_type = 3 THEN FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
            //                         ELSE client END,
            //             cd.total = atm.change_payable,
            //             cd.id_branch = CASE
            //                         WHEN client_type =2 THEN m.id_branch
            //                         WHEN client_type = 3 THEN e.id_branch
            //                         ELSE 0 END,
            //             cd.address =CASE
            //                         WHEN client_type =2 THEN m.address
            //                         WHEN client_type = 3 THEN e.address
            //                         ELSE '' END,
            //             cd.payee_type = atm.client_type
            //             where id_atm_swipe = ?;",[$id_atm_swipe]);


            DB::table('cash_disbursement_details')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->delete();
        }

        DB::select("INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference)
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,cbu.amount as debit,0 as credit,'' as remarks,cbu.id_cbu_withdrawal
        FROM cbu_withdrawal as cbu
        LEFT JOIN chart_account as ca on ca.id_chart_account = 28
        WHERE id_cbu_withdrawal = ?
        UNION ALL
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,cbu.amount  as credit,'' as remarks,cbu.id_cbu_withdrawal
        FROM cbu_withdrawal as cbu
        LEFT JOIN tbl_bank as tb on tb.id_bank = cbu.id_bank
        LEFT JOIN chart_account as ca on ca.id_chart_account = if(cbu.id_bank = 0,1,tb.id_chart_account)
        WHERE id_cbu_withdrawal = ?;",[$id_cbu_withdrawal,$id_cbu_withdrawal]);
    }

    public static function InvestmentWithdrawalCDV($id_investment_withdrawal){
        $id_cash_disbursement = DB::table('investment_withdrawal')->select('id_cash_disbursement')->where('id_investment_withdrawal',$id_investment_withdrawal)->first()->id_cash_disbursement;

        if($id_cash_disbursement == 0){
            DB::select("INSERT INTO cash_disbursement (date,paymode,paymode_account,type,description,id_member,payee,reference,status,total,id_branch,address,payee_type)
            SELECT iwb.date_released,1 as paymode,1 as paymode_account,10 as type,concat('Withdrawal for Investment ID#',iw.id_investment,' (',ip.product_name,') [Ref# ',iw.id_investment_withdrawal,' || Batch#',iw.id_investment_withdrawal_batch,']') as description,
            iw.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as payee,iw.id_investment_withdrawal,0 as status,iw.total_amount as total,m.id_branch,m.address,2 as payee_type
            FROM investment_withdrawal as iw
            LEFT JOIN investment_withdrawal_batch as iwb on iwb.id_investment_withdrawal_batch = iw.id_investment_withdrawal_batch
            LEFT JOIN investment as i on i.id_investment = iw.id_investment
            LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
            LEFT JOIN member as m on m.id_member = iw.id_member
            WHERE iw.status = 1 AND iw.id_investment_withdrawal = ?;",[$id_investment_withdrawal]);

            $id_cash_disbursement = DB::table('cash_disbursement')->where('type',10)->max('id_cash_disbursement');

            DB::table('investment_withdrawal')
            ->where('id_investment_withdrawal',$id_investment_withdrawal)
            ->update([
                'id_cash_disbursement'=>$id_cash_disbursement
            ]);
        }else{
            DB::select("UPDATE investment_withdrawal as iw
            LEFT JOIN investment_withdrawal_batch as iwb on iwb.id_investment_withdrawal_batch = iw.id_investment_withdrawal_batch
            LEFT JOIN cash_disbursement as cdv on cdv.id_cash_disbursement = iw.id_cash_disbursement
            LEFT JOIN investment as i on i.id_investment = iw.id_investment
            LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
            LEFT JOIN member as m on m.id_member = iw.id_member
            SET cdv.date=iwb.date_released,cdv.description = concat('Withdrawal for Investment ID#',iw.id_investment,' (',ip.product_name,') [Ref# ',iw.id_investment_withdrawal,' || Batch#',iw.id_investment_withdrawal_batch,']'),
            cdv.payee = FormatName(m.first_name,m.middle_name,m.last_name,m.suffix),cdv.total= iw.total_amount,cdv.id_branch = m.id_branch
            WHERE iw.status=1 and iw.id_investment_withdrawal = ?;",[$id_cash_disbursement]);

            DB::table('cash_disbursement_details')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->delete();
        }


        // DB::select("INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference)
        //             SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,iw.amount as debit,0 as credit,'' as details,iw.id_investment_withdrawal
        //             FROM investment_withdrawal as iw 
        //             LEFT JOIN investment as i on i.id_investment = iw.id_investment
        //             LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
        //             LEFT JOIN chart_account as ca on ca.id_chart_account = ip.id_chart_account
        //             WHERE iw.id_investment_withdrawal=?
        //             UNION ALL
        //             SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,amount as credit,'' as details,iw.id_investment_withdrawal
        //             FROM investment_withdrawal as iw 
        //             LEFT JOIN chart_account as ca on ca.id_chart_account = 1
        //             WHERE iw.id_investment_withdrawal=?;",[$id_investment_withdrawal,$id_investment_withdrawal]);

        DB::select("INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference)
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,iw.principal as debit,0 as credit,'' as details,iw.id_investment_withdrawal
        FROM investment_withdrawal as iw 
        LEFT JOIN investment as i on i.id_investment = iw.id_investment
        LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
        LEFT JOIN chart_account as ca on ca.id_chart_account = ip.id_chart_account
        WHERE iw.id_investment_withdrawal=? AND iw.principal > 0
        UNION ALL
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,iw.interest as debit,0 as credit,'' as details,iw.id_investment_withdrawal
        FROM investment_withdrawal as iw 
        LEFT JOIN investment as i on i.id_investment = iw.id_investment
        LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
        LEFT JOIN chart_account as ca on ca.id_chart_account = ip.interest_chart_account
        WHERE iw.id_investment_withdrawal=? AND iw.interest > 0
        UNION ALL
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,total_amount as credit,'' as details,iw.id_investment_withdrawal
        FROM investment_withdrawal as iw 
        LEFT JOIN chart_account as ca on ca.id_chart_account = 1
        WHERE iw.id_investment_withdrawal=?;",[$id_investment_withdrawal,$id_investment_withdrawal,$id_investment_withdrawal]);




        // SELECT 0 as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,iw.principal as debit,0 as credit,'' as details,iw.id_investment_withdrawal
        // FROM investment_withdrawal as iw 
        // LEFT JOIN investment as i on i.id_investment = iw.id_investment
        // LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
        // LEFT JOIN chart_account as ca on ca.id_chart_account = ip.id_chart_account
        // WHERE iw.id_investment_withdrawal=23 AND iw.principal > 0
        // UNION ALL
        // SELECT 0 as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,iw.interest as debit,0 as credit,'' as details,iw.id_investment_withdrawal
        // FROM investment_withdrawal as iw 
        // LEFT JOIN investment as i on i.id_investment = iw.id_investment
        // LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
        // LEFT JOIN chart_account as ca on ca.id_chart_account = ip.interest_chart_account
        // WHERE iw.id_investment_withdrawal=23 AND iw.interest > 0
        // UNION ALL
        // SELECT 0 as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,ifnull(in_f.amount,0) as credit,'Withdrawal Fee',iw.id_investment_withdrawal
        // FROM investment_withdrawal as iw
        // LEFT JOIN investment_fee as in_f on in_f.id_investment = iw.id_investment and in_f.id_fee_type=1
        // LEFT JOIN chart_account as ca on ca.id_chart_account = 5
        // WHERE iw.id_investment_withdrawal=23 and in_f.amount > 0
        // UNION ALL
        // SELECT 0 as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,(total_amount-ifnull(in_f.amount,0) ) as credit,'' as details,iw.id_investment_withdrawal
        // FROM investment_withdrawal as iw 
        // LEFT JOIN chart_account as ca on ca.id_chart_account = 1
        // LEFT JOIN investment_fee as in_f on in_f.id_investment = iw.id_investment AND in_f.id_fee_type =1
        // WHERE iw.id_investment_withdrawal=23;
           

        return $id_cash_disbursement;

    }
    public static function PrimeWithdrawalCDV($id_prime_withdrawal){
        $id_cash_disbursement = DB::table('prime_withdrawal')->select('id_cash_disbursement')->where('id_prime_withdrawal',$id_prime_withdrawal)->first()->id_cash_disbursement;

        if($id_cash_disbursement == 0){
            DB::select("INSERT INTO cash_disbursement (date,paymode,paymode_account,type,description,id_member,payee,reference,status,total,id_branch,address,payee_type)
            SELECT pb.date_released,1 as paymode,1 as paymode_account,11 as type,concat('PRIME WITHDRAWAL ID# ',cbu.id_prime_withdrawal,' [BATCH #',pb.id_prime_withdrawal_batch,']') as description,cbu.id_member,
            FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)as payee,cbu.id_prime_withdrawal as reference,0 as status,cbu.amount as total,
            m.id_branch,m.address,2 as payee_type
            FROM prime_withdrawal as cbu
            LEFT JOIN member as m on m.id_member = cbu.id_member
            LEFT JOIN prime_withdrawal_batch as pb on pb.id_prime_withdrawal_batch =cbu.id_prime_withdrawal_batch
            WHERE cbu.id_prime_withdrawal = ?;",[$id_prime_withdrawal]);

            $id_cash_disbursement = DB::table('cash_disbursement')->where('type',11)->max('id_cash_disbursement');

            DB::table('prime_withdrawal')->where('id_prime_withdrawal',$id_prime_withdrawal)->update(['id_cash_disbursement'=>$id_cash_disbursement]);
        }else{

            // DB::select("UPDATE atm_swipe as atm     
            //             LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = atm.id_cash_disbursement
            //             LEFT JOIN member as m on m.id_member = if(atm.client_type=2,atm.id_member,0)
            //             LEFT JOIN employee as e on e.id_employee = if(atm.client_type=3,atm.id_employee,0)
            //             set cd.date = atm.date,cd.id_member = atm.id_member,cd.id_employee = atm.id_employee,
            //             cd.payee =  CASE
            //                         WHEN client_type =2 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
            //                         WHEN client_type = 3 THEN FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
            //                         ELSE client END,
            //             cd.total = atm.change_payable,
            //             cd.id_branch = CASE
            //                         WHEN client_type =2 THEN m.id_branch
            //                         WHEN client_type = 3 THEN e.id_branch
            //                         ELSE 0 END,
            //             cd.address =CASE
            //                         WHEN client_type =2 THEN m.address
            //                         WHEN client_type = 3 THEN e.address
            //                         ELSE '' END,
            //             cd.payee_type = atm.client_type
            //             where id_atm_swipe = ?;",[$id_atm_swipe]);


            DB::table('cash_disbursement_details')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->delete();
        }

        DB::select("INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference)
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,cbu.amount as debit,0 as credit,'' as remarks,cbu.id_prime_withdrawal
        FROM prime_withdrawal as cbu
        LEFT JOIN chart_account as ca on ca.id_chart_account = 77
        WHERE id_prime_withdrawal = ?
        UNION ALL
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,cbu.amount  as credit,'' as remarks,cbu.id_prime_withdrawal
        FROM prime_withdrawal as cbu
        LEFT JOIN chart_account as ca on ca.id_chart_account = 1
        WHERE id_prime_withdrawal = ?;",[$id_prime_withdrawal,$id_prime_withdrawal]);
    }

    public static function ChangePayableCDV($id_change_payable,$id_member,$id_cash_disbursement){
        $type = 12;

        if($id_cash_disbursement == 0){
            DB::select("INSERT INTO cash_disbursement (date,paymode,paymode_account,type,description,id_member,payee,reference,status,total,id_branch,address,payee_type)
            SELECT date,1 as paymode,1 as paymode_account,$type as type,concat('TO RELEASE THE CHANGE UNDER REPAYMENT ID#', cp.id_repayment) as description,
            cpd.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as payee,cp.id_change_payable as reference,0 as status,cpd.amount,m.id_branch,m.address,2 as payee_type
            FROM change_payable_details as cpd 
            LEFT JOIN change_payable as cp on cp.id_change_payable = cpd.id_change_payable
            LEFT JOIN member as m on m.id_member = cpd.id_member
            where cpd.id_member =?  AND cp.id_change_payable =?",[$id_member,$id_change_payable]);

            $id_cash_disbursement = DB::table('cash_disbursement')->where('type',$type)->where('reference',$id_change_payable)->max('id_cash_disbursement');       

        }else{
            // dd("WOW");
            DB::select("UPDATE change_payable_details as cpd 
            LEFT JOIN change_payable as cp on cp.id_change_payable = cpd.id_change_payable
            LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = $id_cash_disbursement
            SET cd.date = cp.date,cd.description=concat('TO RELEASE THE CHANGE UNDER REPAYMENT ID#', cp.id_repayment),cd.total = cpd.amount
            where cpd.id_member =? AND cp.id_change_payable =?",[$id_member,$id_change_payable]);

            DB::table('cash_disbursement_details')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->delete();
        }

        DB::select("INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference)
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,cdd.amount as debit,0 as credit,'' as remarks,cdd.id_change_payable_details
        FROM change_payable_details as cdd
        LEFT JOIN chart_account as ca on ca.id_chart_account =23
        WHERE cdd.id_change_payable = ? AND id_member = ?
        UNION ALL
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,cpd.amount as credit,'' as remarks,cpd.id_change_payable_details
        FROM change_payable_details as cpd
        LEFT JOIN change_payable as cp on cp.id_change_payable = cpd.id_change_payable
        LEFT JOIN check_deposit_details as cdd on cdd.id_repayment = cp.id_repayment 
        LEFT JOIN check_deposit as cd on cd.id_check_deposit = cdd.id_check_deposit
        LEFT JOIN tbl_bank as tb on tb.id_bank = cd.id_bank
        LEFT JOIN chart_account as ca on ca.id_chart_account = tb.id_chart_account
        WHERE cpd.id_change_payable = ? AND id_member =? AND cd.status <> 10
        GROUP BY cpd.id_change_payable_details;",[$id_change_payable,$id_member,$id_change_payable,$id_member]); 


        DB::table('change_payable_details')
        ->where('id_member',$id_member)
        ->where('id_change_payable',$id_change_payable)
        ->update(['id_cash_disbursement'=>$id_cash_disbursement]);    

    }
    public static function ChangePayableIncomeCDV($id_change_payable,$id_cash_disbursement){
        $type = 12;

        if($id_cash_disbursement == 0){
            DB::select("INSERT INTO cash_disbursement (date,paymode,paymode_account,type,description,id_member,payee,reference,status,total,id_branch,address,payee_type)
            SELECT date,1 as paymode,1 as paymode_account,12 as type,concat('TO RELEASE THE CHANGE UNDER REPAYMENT ID#', cp.id_repayment) as description,
            0 as id_member,'MKMPC',cp.id_change_payable as reference,0 as status,SUM(cpd.amount),1 as id_branch,'' as address,4 as payee_type
            FROM change_payable_details as cpd 
            LEFT JOIN change_payable as cp on cp.id_change_payable = cpd.id_change_payable
            WHERE cpd.id_change_payable = ? AND cpd.id_chart_account > 0;",[$id_change_payable]);

            $id_cash_disbursement = DB::table('cash_disbursement')->where('type',$type)->where('reference',$id_change_payable)->max('id_cash_disbursement');       

        }else{
            // dd("WOW");
            // DB::select("UPDATE change_payable_details as cpd 
            // LEFT JOIN change_payable as cp on cp.id_change_payable = cpd.id_change_payable
            // LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = $id_cash_disbursement
            // SET cd.date = cp.date,cd.description=concat('TO RELEASE THE CHANGE UNDER REPAYMENT ID#', cp.id_repayment),cd.total = cpd.amount
            // where cpd.id_member =? AND cp.id_change_payable =?",[$id_member,$id_change_payable]);

            DB::select("UPDATE (
            SELECT date,concat('TO RELEASE THE CHANGE UNDER REPAYMENT ID#', cp.id_repayment) as description,0 as status,SUM(cpd.amount) as amount,cp.id_cash_disbursement
            FROM change_payable_details as cpd 
            LEFT JOIN change_payable as cp on cp.id_change_payable = cpd.id_change_payable
            WHERE cpd.id_change_payable = ?  AND cpd.id_chart_account > 0) as c
            LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = c.id_cash_disbursement
            SET cd.date = c.date,cd.description = c.description,cd.status = c.status,cd.total = c.amount;",[$id_change_payable]);

            DB::table('cash_disbursement_details')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->delete();
        }

        DB::select("INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference)
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,SUM(cdd.amount) as debit,0 as credit,'' as remarks,cdd.id_change_payable_details
        FROM change_payable_details as cdd
        LEFT JOIN chart_account as ca on ca.id_chart_account =cdd.id_chart_account
        WHERE cdd.id_change_payable = ? AND cdd.id_chart_account > 0
        GROUP BY cdd.id_chart_account
        UNION ALL
        SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,SUM(cpd.amount) as credit,'' as remarks,cp.id_change_payable
        FROM change_payable_details as cpd
        LEFT JOIN change_payable as cp on cp.id_change_payable = cpd.id_change_payable
        LEFT JOIN check_deposit_details as cdd on cdd.id_repayment = cp.id_repayment 
        LEFT JOIN check_deposit as cd on cd.id_check_deposit = cdd.id_check_deposit
        LEFT JOIN tbl_bank as tb on tb.id_bank = cd.id_bank
        LEFT JOIN chart_account as ca on ca.id_chart_account = tb.id_chart_account
        WHERE cpd.id_change_payable =?  AND cd.status <> 10 AND cpd.id_chart_account > 0",[$id_change_payable,$id_change_payable]); 


        DB::table('change_payable')
        ->where('id_change_payable',$id_change_payable)
        ->update(['id_cash_disbursement'=>$id_cash_disbursement]);    

    }
}
