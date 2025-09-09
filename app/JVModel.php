<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class JVModel extends Model
{
    public static function RepaymentJV($id_repayment_transaction,$edited,$cancel){
        $id_journal_voucher = DB::table('journal_voucher')->where('reference',$id_repayment_transaction)->where('type',2)->max('id_journal_voucher');
        $d = DB::table('repayment_transaction')->select("cancel_reason","date_cancelled","transaction_type")->where('id_repayment_transaction',$id_repayment_transaction)->first();




        
        if(!isset($id_journal_voucher)){
            DB::select("INSERT INTO journal_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type,id_employee)
                        SELECT transaction_date,2 as type,concat('(ID#',id_repayment_Transaction,') REPAYMENT FOR LOAN ID(S) ',getRepaymentTransactionIDLoans(id_repayment_transaction),if(rt.transaction_type = 3,' (PAYROLL)','')) as description,
                        rt.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)  as payee,rt.id_repayment_transaction as reference,0 as status,if(rt.transaction_type=1,total_payment,swiping_amount) as total,
                        m.id_branch,m.address,2 as payee_type,if(rt.transaction_type = 3,m.id_employee,0)
                        FROM repayment_transaction as rt
                        LEFT JOIN member as m on m.id_member = rt.id_member
                        where id_repayment_Transaction = ?;",[$id_repayment_transaction]);     
            $id_journal_voucher = DB::table('journal_voucher')->where('reference',$id_repayment_transaction)->where('type',2)->max('id_journal_voucher');      
        }else{
            if($cancel){

                 DB::table('journal_voucher')
                 ->where('id_journal_voucher',$id_journal_voucher)
                 ->update(['status'=>10,'description'=>DB::raw("concat(REPLACE(description,' [CANCELLED]',''),' [CANCELLED]')"),'date_cancelled'=>$d->date_cancelled,'cancellation_reason'=>$d->cancel_reason]);    

                return;            
            }
            if($edited){
                 DB::table('journal_voucher')
                 ->where('id_journal_voucher',$id_journal_voucher)
                // ->update(['description'=>DB::raw("concat(REPLACE(description,' [EDITED]',''),' [EDITED]')")])  ;
                  ->update(['description'=>DB::raw("concat('(ID#',reference,') REPAYMENT FOR LOAN ID(S) ',getRepaymentTransactionIDLoans(reference))")])  ;

            }

            DB::table('journal_voucher_details')
            ->where('id_journal_voucher',$id_journal_voucher)
            ->delete();
        }
        $param = [];
        for($i=0;$i<9;$i++){
            array_push($param,$id_repayment_transaction);
        }

        DB::select("insert INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
                    /*************SWIPING AMOUNT**********************/
                    select $id_journal_voucher id_journal_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,if(rt.transaction_type=1,total_payment,swiping_amount) as debit,0 as credit,
                    '' as remarks,id_repayment_transaction as reference
                    FROM repayment_transaction as rt
                     LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = if(rt.transaction_type=1,12,8)
                     LEFT JOIN tbl_bank as tb on tb.id_bank = rt.id_bank
                     LEFT JOIN chart_account as ca on ca.id_chart_account = if(rt.transaction_type=1,1,tb.id_chart_account)
                    where id_repayment_transaction = ?
                    UNION ALL
                    /*********************REBATES*****************/
                    SELECT $id_journal_voucher id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,amount as debit,0 as credit,concat('ID#',rb.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as remarks,rb.id_loan as reference
                    FROM repayment_rebates as rb
                    LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 3
                    LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
                    LEFT JOIN loan on loan.id_loan = rb.id_loan
                    LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                    WHERE rb.id_repayment_transaction = ?

                    UNION ALL
                   SELECT id_journal_voucher,id_chart_account,account_code,rep_loan.description,debit,SUM(credit) as credit,concat('ID#',reference,' ',getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms)) as remarks,reference FROM (
                   /*************LOAN PRINCIPAL AMOUNT**********************/
                    select $id_journal_voucher id_journal_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,paid_principal as credit ,
                    '' as remarks,rl.id_loan as reference
                    FROM repayment_loans as rl
                    LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 9
                    LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
                    WHERE id_repayment_transaction = ?
        
                    UNION ALL
                    /*************LOAN FEES**********************/
                    select $id_journal_voucher id_journal_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,paid_fees as credit ,
                    '' as remarks,rl.id_loan as reference
                    FROM repayment_loans as rl
                    LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 10
                    LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account 
                    WHERE id_repayment_transaction = ?
                    
                    UNION ALL
                    /*************LOAN INTEREST**********************/
                    select $id_journal_voucher id_journal_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,paid_interest as credit ,
                    '' as remarks,rl.id_loan as reference
                    FROM repayment_loans as rl
                    LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance =6
                    LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account 
                    WHERE id_repayment_transaction = ?) 
                    as rep_loan
                    LEFT JOIN loan as l on rep_loan.reference = l.id_loan
                    LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
                    WHERE credit > 0
                    GROUP BY id_chart_account,reference
                    UNION ALL
                    /*************OTHER FEES**********************/
                    select $id_journal_voucher id_journal_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,rf.amount as credit ,
                    pt.description as remarks,id_repayment_transaction as reference FROM repayment_fees as rf
                    LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
                    LEFT JOIN chart_account as ca on ca.id_chart_account = pt.id_chart_account 
                    WHERE id_Repayment_transaction =? and amount > 0
                    UNION ALL
                    /*************PENALTY**********************/
                    select $id_journal_voucher id_journal_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,rp.amount as credit ,
                    pt.description as remarks,id_repayment_transaction as reference FROM repayment_penalty as rp
                    LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rp.id_payment_type
                    LEFT JOIN chart_account as ca on ca.id_chart_account = pt.id_chart_account 
                    WHERE id_Repayment_transaction =? AND rp.amount > 0
                    UNION ALL
                    /*************SURCHARGES**********************/

                    /***********penalty=>38 || interest=>35******************************/
                    SELECT $id_journal_voucher id_journal_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,rls.amount as credit ,
                    concat('ID#',rls.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as remarks,rls.id_loan as reference FROM repayment_loan_surcharges as rls
                    LEFT JOIN chart_account as ca on ca.id_chart_account= 38
                    LEFT JOIN loan on loan.id_loan = rls.id_loan
                    LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                    WHERE rls.id_repayment_transaction = ?
                    UNION ALL
                    /*****CHANGE*****/ 
                    select $id_journal_voucher id_journal_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,`change` as credit,
                    '' as remarks,id_repayment_transaction as reference
                    FROM repayment_transaction as rt
                    LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 11
                    LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
                    where id_repayment_transaction = ? and `change` > 0;",$param);

        $output['status'] = "SUCCESS";
        $output['id_journal_voucher'] = $id_journal_voucher;


        self::setIsCash($id_journal_voucher);


        return $output;


        return "success";

    }
    public static function ChangeJV($id_repayment_change,$edited,$cancel){
        $id_journal_voucher = DB::table('journal_voucher')->where('reference',$id_repayment_change)->where('type',3)->max('id_journal_voucher');

        if(!isset($id_journal_voucher)){
            DB::select("INSERT INTO journal_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type)
                        SELECT date,3 as type,concat('(ID#',id_repayment_change,') CHANGE FOR REPAYMENT ID# ',id_repayment_transaction) as description,
                        rc.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)  as payee,rc.id_repayment_change as reference,0 as status,amount as total,
                        m.id_branch,m.address,2 as payee_type
                        FROM repayment_change as rc
                        LEFT JOIN member as m on m.id_member = rc.id_member
                         where id_repayment_change = $id_repayment_change;");
            $id_journal_voucher = DB::table('journal_voucher')->where('reference',$id_repayment_change)->where('type',3)->max('id_journal_voucher');
        }else{
            if($cancel){
               DB::table('journal_voucher')
               ->where('id_journal_voucher',$id_journal_voucher)
               ->update(['description'=>DB::raw("concat(REPLACE(description,' [CANCELLED]',''),' [CANCELLED]')"),'status'=>10]);    
               return;            
            }
            if($edited){
                DB::select("UPDATE repayment_change as rc
                            LEFT JOIN member as m on m.id_member = rc.id_member
                            LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = rc.id_journal_voucher
                            SET jv.date = rc.date,jv.description=concat('(ID#',id_repayment_change,') CHANGE FOR REPAYMENT ID# ',id_repayment_transaction,' ',REPLACE(description,' [EDITED]',''),' [EDITED]'),jv.total_amount = rc.amount
                            WHERE rc.id_repayment_change = ?",[$id_repayment_change]);
            }

            DB::table('journal_voucher_details')
            ->where('id_journal_voucher',$id_journal_voucher)
            ->delete();
        }

        DB::select("insert INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
                    SELECT 
                    $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,amount as debit,0 as credit,
                    'Change from repayment' as remarks,id_repayment_change as reference
                    FROm repayment_change
                    LEFT JOIN chart_account as ca on ca.id_chart_account = 23
                    WHERE id_repayment_change = ?
                    UNION ALL
                    SELECT 
                    $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,amount as credit,
                    'Change release' as remarks,id_repayment_change as reference
                    FROm repayment_change
                    LEFT JOIN chart_account as ca on ca.id_chart_account = 1
                    WHERE id_repayment_change = ?;",[$id_repayment_change,$id_repayment_change]);

        self::setIsCash($id_journal_voucher);

        $output['status'] = "SUCCESS";
        $output['id_journal_voucher'] = $id_journal_voucher;

        return $output;
    }

    public static function AssetDisposalJV($id_asset_disposal){
        $id_journal_voucher = DB::table('asset_disposal')->select('id_journal_voucher')->where('id_asset_disposal',$id_asset_disposal)->first()->id_journal_voucher;


        if($id_journal_voucher == 0){
            DB::select("INSERT INTO journal_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type)
                        SELECT date,6 as type,concat('ASSET DISPOSAL ID# ',id_asset_disposal) as description,0 as id_member,'SMESTCCO' as payee,id_asset_disposal as reference,0 as status,amount_received as total,
                        1 as id_branch,'' as address,4 as payee_type
                        FROM asset_disposal
                        WHERE asset_disposal.id_asset_disposal = ?;",[$id_asset_disposal]);
            $id_journal_voucher = DB::table('journal_voucher')->where('type',6)->max('id_journal_voucher');
        }else{
            DB::select("UPDATE asset_disposal as ad
                        LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = ad.id_journal_voucher
                        SET jv.date =ad.date,jv.description = concat('ASSET DISPOSAL ID# ', ad.id_asset_disposal),jv.payee='SMESTCCO',jv.total_amount =ad.amount_received,jv.id_branch = 1
                        WHERE ad.id_asset_disposal = $id_asset_disposal;");

            DB::table('journal_voucher_details')
            ->where('id_journal_voucher',$id_journal_voucher)
            ->delete();
        }

        // SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,SUM(accumulated_depreciation*adi.quantity) as debit,0 credit,'' as details,id_asset_disposal as reference
        // FROM asset_disposal_item as adi
        // LEFT JOIN asset_item as ai on ai.asset_code = adi.asset_code 
        // LEFT JOIN chart_account as ca on ca.id_chart_account =if(ai.id_chart_account=9,10,11)
        // WHERE adi.id_asset_disposal = $id_asset_disposal
        //Insert CHILD
        DB::select("INSERT INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
                    select $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,amount_received as debit,0 credit,'' as details,id_asset_disposal as reference
                    FROM asset_disposal 
                    LEFT JOIN chart_account as ca on ca.id_chart_account =40
                    where id_asset_disposal =$id_asset_disposal and amount_received > 0
                    UNION ALL
                    select $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,abs(loss_gain_amount) as debit,0 credit,'' as details,id_asset_disposal as reference
                    FROM asset_disposal 
                    LEFT JOIN chart_account as ca on ca.id_chart_account =75
                    where id_asset_disposal =$id_asset_disposal and loss_gain_amount < 0
                    UNION ALL
                    SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,SUM(accumulated_depreciation*adi.quantity) as debit,0 credit,'' as details,id_asset_disposal as reference
                    FROM asset_disposal_item as adi
                    LEFT JOIN asset_item as ai on ai.asset_code = adi.asset_code 
                    LEFT JOIN chart_account as cd on cd.id_chart_account = ai.id_chart_account
                    LEFT JOIN chart_account as ca on ca.id_chart_account = cd.ac_depreciation_account
                    WHERE adi.id_asset_disposal = $id_asset_disposal
                    UNION ALL
                    SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,(ai.unit_cost*adi.quantity) credit,'' as details,id_asset_disposal as reference
                    FROM asset_disposal_item as adi
                    LEFT JOIN asset_item as ai on ai.asset_code = adi.asset_code
                    LEFT JOIN chart_account as ca on ca.id_chart_account =ai.id_chart_account
                    WHERE adi.id_asset_disposal =$id_asset_disposal
                    UNION ALL
                    select $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,abs(loss_gain_amount) as credit,'' as details,id_asset_disposal as reference
                    FROM asset_disposal 
                    LEFT JOIN chart_account as ca on ca.id_chart_account =41
                    where id_asset_disposal =$id_asset_disposal and loss_gain_amount > 0;");

        DB::table('asset_disposal')
        ->where('id_asset_disposal',$id_asset_disposal)
        ->update([
            'id_journal_voucher'=>$id_journal_voucher
        ]);

        self::setIsCash($id_journal_voucher);
        return $id_journal_voucher;
    }

    public static function BankTransactionJV($id_bank_transaction){
        $id_journal_voucher = DB::table('bank_transaction')->select('id_journal_voucher')->where('id_bank_transaction',$id_bank_transaction)->first()->id_journal_voucher;

        if($id_journal_voucher == 0){
            DB::select("INSERT INTO journal_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type)
            SELECT date,4 as type,concat('Bank Transaction ID# ',id_bank_transaction,' ',if(bt.type=1,concat('Deposit to ', tb.bank_name),if(bt.type=2,concat('Withdrawal from ',tb.bank_name),concat('Transfer from ',tb.bank_name,' to ',tb_trans.bank_name)))) as description,bt.id_member,
            if(bt.type=3,tb_trans.bank_name,bt.name) as payee,id_bank_transaction,0 as status,bt.amount,m.id_branch,m.address,4 as payee_type
            FROM bank_transaction as bt
            LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
            LEFT JOIN tbl_bank as tb_trans on tb_trans.id_bank = bt.id_bank_transfer_to
            LEFT JOIN member as m on m.id_member = bt.id_member
            WHERE bt.id_bank_transaction = ?;",[$id_bank_transaction]);

            $id_journal_voucher = DB::table('journal_voucher')->where('type',4)->max('id_journal_voucher');
        }else{
            DB::select("UPDATE bank_transaction as bt
                        LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
                        LEFT JOIN tbl_bank as tb_trans on tb_trans.id_bank = bt.id_bank_transfer_to
                        LEFT JOIN member as m on m.id_member = bt.id_member
                        LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = bt.id_journal_voucher
                        SET jv.date =bt.date,jv.description=concat('Bank Transaction ID# ',id_bank_transaction,' ',if(bt.type=1,concat('Deposit to ', tb.bank_name),if(bt.type=2,concat('Withdrawal from ',tb.bank_name),concat('Transfer from ',tb.bank_name,' to ',tb_trans.bank_name)))),
                        jv.id_member = bt.id_member,jv.payee =if(bt.type=3,tb_trans.bank_name,bt.name),jv.total_amount = bt.amount
                        WHERE bt.id_bank_transaction = ?;",[$id_bank_transaction]);

            DB::table('journal_voucher_details')->where('id_journal_voucher',$id_journal_voucher)->delete();
        }

        // DB::select("
        //     INSERT INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
        //     SELECT $id_journal_voucher as id_journal_voucher,tb.id_chart_account,ca.account_code,ca.description,amount as debit,0 as credit,'',id_bank_transaction from bank_transaction as bt
        // LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
        // LEFT JOIN chart_account as ca on ca.id_chart_account = tb.id_chart_account
        // where id_bank_transaction = ?
        // UNION ALL
        // SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,amount as credit,'',id_bank_transaction from bank_transaction as bt
        // LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank_transfer_to
        // LEFT JOIN chart_account as ca on ca.id_chart_account = if(bt.type=3,tb.id_chart_account,1)
        // where id_bank_transaction = ?;",[$id_bank_transaction,$id_bank_transaction]);
        DB::select("
            INSERT INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
            SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,amount as debit,0 as credit,'',id_bank_transaction from bank_transaction as bt
            LEFT JOIN tbl_bank as tb on tb.id_bank = if(bt.type=2 or bt.type=1,bt.id_bank,bt.id_bank_transfer_to)
            LEFT JOIN chart_account as ca on ca.id_chart_account = if(bt.type=2,1,tb.id_chart_account)
            where id_bank_transaction = ?
            UNION ALL
            SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,amount as credit,'',id_bank_transaction from bank_transaction as bt
            LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
            LEFT JOIN chart_account as ca on ca.id_chart_account = if(bt.type=3 OR bt.type=2,tb.id_chart_account,1)
            where id_bank_transaction = ?;",[$id_bank_transaction,$id_bank_transaction]);

        DB::table('bank_transaction')
        ->where('id_bank_transaction',$id_bank_transaction)
        ->update(['id_journal_voucher'=>$id_journal_voucher]);



        self::setIsCash($id_journal_voucher);
    }

    public static function PayrollJV($id_payroll){
            $id_journal_voucher = DB::table('payroll')->select('id_journal_voucher')->where('id_payroll',$id_payroll)->first()->id_journal_voucher;

            if($id_journal_voucher == 0){
                DB::select("INSERT INTO journal_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type)
                SELECT p.period_start as date,7 as type,concat('PAYROLL ID# ',p.id_payroll,' FOR PERIOD OF ',DATE_FORMAT(p.period_start,'%m/%d/%Y'),' - ',DATE_FORMAT(p.period_end,'%m/%d/%Y'),if(p.id_payroll_mode=3,concat(' (',p.no_days,' Days)'),''))  as description,0 as id_member,'SMESTCCO' as payee,p.id_payroll as reference,0 as status,
                SUM(net_income) as total_amount,1 as branch,'' as address, 4 as payee_type
                FROM payroll as p 
                LEFT JOIN payroll_employee as pe on pe.id_payroll = p.id_payroll
                where p.id_payroll = ?;",[$id_payroll]);

                $id_journal_voucher = DB::table('journal_voucher')->where('type',7)->where('reference',$id_payroll)->max('id_journal_voucher');

                DB::table('payroll')->where('id_payroll',$id_payroll)->update(['id_journal_voucher'=>$id_journal_voucher]);
            }else{
                DB::select("UPDATE payroll as p
                            LEFT JOIN journal_voucher as jv on jv.id_journal_voucher =p.id_journal_voucher
                            INNER JOIN 
                            (SELECT pe.id_payroll,SUM(net_income) as net_income
                            FROM payroll_employee as pe
                            WHERE pe.id_payroll=$id_payroll) t
                            ON t.id_payroll = p.id_payroll
                            SET jv.date = p.period_start,jv.description = concat('PAYROLL ID# ',p.id_payroll,' FOR PERIOD OF ',DATE_FORMAT(p.period_start,'%m/%d/%Y'),' - ',DATE_FORMAT(p.period_end,'%m/%d/%Y'),if(p.id_payroll_mode=3,concat(' (',p.no_days,' Days)'),'')),
                            jv.payee = 'SMESTCCO',jv.address='',total_amount =  t.net_income
                            WHERE p.id_journal_voucher = $id_journal_voucher;");
                DB::table('journal_voucher_details')
                ->where('id_journal_voucher',$id_journal_voucher)
                ->delete();
            }
            DB::select("
            INSERT INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)   
            SELECT  $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,
            SUM(basic_pay+ot+night_shift_dif+holiday+paid_leaves+salary_adjustment+13th_month+others) as debit,0 as credit,'' as details,pe.id_payroll as reference
            FROM payroll_employee as pe 
            LEFT JOIN employee as em on em.id_employee = pe.id_employee
            LEFT JOIN chart_account as ca on ca.id_chart_account = if(em.id_employee_type = 1,45,44)
            where pe.id_payroll =  $id_payroll
            GROUP BY em.id_employee_type
            UNION ALL
            SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,
            SUM(total_allowance) as debit,0 as credit,'' as details,pe.id_payroll
            FROM payroll_employee as pe 
            LEFT JOIN chart_account as ca on ca.id_chart_account = 46
            where pe.id_payroll = $id_payroll
            HAVING SUM(total_allowance) > 0
            UNION ALL
            SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,
            0 as debit,SUM(sss+philhealth+hdmf+insurance+sss_loan+hdmf_loan) as credit,'' as details,pe.id_payroll
            FROM payroll_employee as pe 
            LEFT JOIN chart_account as ca on ca.id_chart_account = 27
            where pe.id_payroll = $id_payroll 
            HAVING SUM(sss+philhealth+hdmf+insurance+sss_loan+hdmf_loan) > 0
            UNION ALL
            SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,
            0 as debit,SUM(ca) as credit,'' as details,pe.id_payroll
            FROM payroll_employee as pe 
            LEFT JOIN chart_account as ca on ca.id_chart_account = 7
            where pe.id_payroll = $id_payroll 
            HAVING SUM(ca) > 0
            UNION ALL
            SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,
            0 as debit,SUM(absences+late) as credit,'' as details,pe.id_payroll
            FROM payroll_employee as pe 
            LEFT JOIN chart_account as ca on ca.id_chart_account = 45
            where pe.id_payroll = $id_payroll
            HAVING SUM(absences+late) > 0
            UNION ALL
            SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,
            0 as debit,SUM(wt) as credit,'' as details,pe.id_payroll
            FROM payroll_employee as pe 
            LEFT JOIN chart_account as ca on ca.id_chart_account = 26
            where pe.id_payroll = $id_payroll
            HAVING SUM(wt) > 0
            UNION ALL
            SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,
            0 as debit,SUM(net_income) as credit,'' as details,pe.id_payroll
            FROM payroll_employee as pe 
            LEFT JOIN payroll as p on p.id_payroll = pe.id_payroll
            LEFT JOIN tbl_bank as tb on tb.id_bank = p.id_bank
            LEFT JOIN chart_account as ca on ca.id_chart_account = if(p.id_bank=0,1,tb.id_chart_account)
            where pe.id_payroll = $id_payroll;");



            self::setIsCash($id_journal_voucher);
    }
    public static function  ATMSwipeJV($id_atm_swipe){
        $id_journal_voucher = DB::table('atm_swipe')->select('id_journal_voucher')->where('id_atm_swipe',$id_atm_swipe)->first()->id_journal_voucher;

        if($id_journal_voucher == 0){
            DB::select("INSERT INTO journal_voucher (date,type,description,id_member,id_employee,payee,reference,status,total_amount,id_branch,address,payee_type)
            SELECT  atm.date,8 as type,concat('ATM SWIPE ID# ',atm.id_atm_swipe) as description,atm.id_member,atm.id_employee,
            CASE
            WHEN client_type =2 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
            WHEN client_type = 3 THEN FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
            ELSE client END as payee,atm.id_atm_swipe as reference,0 as status,atm.amount as total,
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

            $id_journal_voucher = DB::table('journal_voucher')->where('type',8)->max('id_journal_voucher');
            DB::table('atm_swipe')->where('id_atm_swipe',$id_atm_swipe)->update(['id_journal_voucher'=>$id_journal_voucher]);

        }else{

            DB::select("UPDATE atm_swipe as atm     
                        LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = atm.id_journal_voucher
                        LEFT JOIN member as m on m.id_member = if(atm.client_type=2,atm.id_member,0)
                        LEFT JOIN employee as e on e.id_employee = if(atm.client_type=3,atm.id_employee,0)
                        set jv.date = atm.date,jv.id_member = atm.id_member,jv.id_employee = atm.id_employee,
                        jv.payee =  CASE
                                    WHEN client_type =2 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
                                    WHEN client_type = 3 THEN FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
                                    ELSE client END,
                        jv.total_amount = atm.amount,
                        jv.id_branch = CASE
                                    WHEN client_type =2 THEN m.id_branch
                                    WHEN client_type = 3 THEN e.id_branch
                                    ELSE 0 END,
                        jv.address =CASE
                                    WHEN client_type =2 THEN m.address
                                    WHEN client_type = 3 THEN e.address
                                    ELSE '' END,
                        jv.payee_type = atm.client_type
                        where id_atm_swipe = ?;",[$id_atm_swipe]);


            DB::table('journal_voucher_details')
            ->where('id_journal_voucher',$id_journal_voucher)
            ->delete();
        }

        DB::select("INSERT INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
        SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,(amount) as debit,0 as credit,'' as remarks,atm.id_atm_swipe
        FROM atm_swipe as atm
        left join tbl_bank as tb on tb.id_bank = atm.id_bank
        LEFT JOIN chart_account as ca on ca.id_chart_account = tb.id_chart_account
        WHERE id_atm_swipe = ?
        UNION ALL
        SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,change_payable as credit,'' as remarks,atm.id_atm_swipe
        FROM atm_swipe as atm
        LEFT JOIN account_code_maintenance as acm on acm.id_account_code_maintenance = 11
        LEFT JOIN chart_account as ca on ca.id_chart_account = acm.id_chart_account
        WHERE id_atm_swipe = ?
        UNION ALL
        SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,transaction_charge as credit,'' as remarks,atm.id_atm_swipe
        FROM atm_swipe as atm
        LEFT JOIN account_code_maintenance as acm on acm.id_account_code_maintenance = 14
        LEFT JOIN chart_account as ca on ca.id_chart_account = acm.id_chart_account
        WHERE id_atm_swipe = ?;",[$id_atm_swipe,$id_atm_swipe,$id_atm_swipe]);


        self::setIsCash($id_journal_voucher);
        return $id_journal_voucher;
    }
    public static function InvestmentRenewalJV($id_investment){
        $id_journal_voucher = DB::table('investment')->select('id_journal_voucher')->where('id_investment',$id_investment)->first()->id_journal_voucher;

        if($id_journal_voucher == 0){
            DB::select("INSERT INTO journal_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type)
            SELECT investment_date,10 as type,concat('Investment - Renewal of ',ip.product_name, ' (ID#',id_prev_investment,') new investment ID#',i.id_investment) as description,i.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)  as payee,
            i.id_investment as reference,0 as status,i.amount as total,m.id_branch,m.address,2 as payee_type
            FROM investment as i
            LEFT JOIN member as m on m.id_member= i.id_member
            LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
            WHERE i.id_investment=?;",[$id_investment]);  

            $id_journal_voucher = DB::table('journal_voucher')->where('type',10)->where('reference',$id_investment)->max('id_journal_voucher');
            DB::table('investment')->where('id_investment',$id_investment)->update(['id_journal_voucher'=>$id_journal_voucher]);
        }else{
            DB::select("UPDATE investment as i
                        LEFT JOIN member as m on m.id_member= i.id_member
                        LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
                        LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = i.id_journal_voucher
                        SET jv.date = i.investment_date,jv.description = concat('Investment - Renewal of ',ip.product_name, ' (ID#',id_prev_investment,') new investment ID#',i.id_investment),jv.payee=FormatName(m.first_name,m.middle_name,m.last_name,m.suffix),
                        jv.total_amount = i.amount,jv.id_branch = m.id_branch,jv.address = m.address
                        WHERE i.id_investment = ? and i.status = 2;",[$id_investment]); 

            DB::table('journal_voucher_details')
            ->where('id_journal_voucher',$id_journal_voucher)
            ->delete();
        }
        DB::select("INSERT INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
        SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,ir.principal as debit,0 as credit,concat('INVESTMENT ID #',ir.id_investment_prev,' Principal [Renewal]') as details,ir.id_investment_prev 
        FROM investment_renewal as ir
        LEFT JOIN investment as i on i.id_investment = ir.id_investment_new
        LEFT JOIn investment_product as ip on ip.id_investment_product = i.id_investment_product
        LEFT JOIN chart_account as ca on ca.id_chart_account = ip.id_chart_account
        where ir.id_investment_new =? and ir.principal > 0
        UNION ALL
        SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,ir.interest as debit,0 as credit,concat('INVESTMENT ID #',ir.id_investment_prev,' Interest [Renewal]') as details,ir.id_investment_prev 
        FROM investment_renewal as ir
        LEFT JOIN investment as i on i.id_investment = ir.id_investment_new
        LEFT JOIn investment_product as ip on ip.id_investment_product = i.id_investment_product
        LEFT JOIN chart_account as ca on ca.id_chart_account = ip.interest_chart_account
        where ir.id_investment_new =? and ir.interest > 0
        UNION ALL
        SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,total_amount as credit,concat('INVESTMENT ID #',ir.id_investment_new,' Principal') as details,ir.id_investment_new
        FROM investment_renewal as ir
        LEFT JOIN investment as i on i.id_investment = ir.id_investment_new
        LEFT JOIn investment_product as ip on ip.id_investment_product = i.id_investment_product
        LEFT JOIN chart_account as ca on ca.id_chart_account = ip.id_chart_account
        where ir.id_investment_new =? AND total_amount > 0;
        ;",[$id_investment,$id_investment,$id_investment]);

        self::setIsCash($id_journal_voucher);






    }
    public static function setIsCash($id_journal_voucher){
        DB::table('journal_voucher')
        ->where('id_journal_voucher',$id_journal_voucher)
        ->update(['cash'=>DB::raw("isCash(id_journal_voucher)")]);
    }
}
// SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,
//             0 as debit,SUM(net_income) as credit,'' as details,pe.id_payroll
//             FROM payroll_employee as pe 
//             LEFT JOIN employee as em on em.id_employee = pe.id_employee
//             LEFT JOIN tbl_bank as tb on tb.id_bank = em.id_bank
//             LEFT JOIN chart_account as ca on ca.id_chart_account = if(em.id_bank=0,1,tb.id_chart_account)
//             where pe.id_payroll = $id_payroll
//             GROUP BY em.id_bank;


