<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class CRVModel extends Model
{
    public static function RepaymentCRV($id_repayment_transaction,$edited,$cancel){
        $id_cash_receipt_voucher = DB::table('cash_receipt_voucher')->where('reference',$id_repayment_transaction)->where('type',2)->max('id_cash_receipt_voucher');

        if(!isset($id_cash_receipt_voucher)){
            DB::select("INSERT INTO cash_receipt_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type,paymode,or_no)
                        SELECT transaction_date,2 as type,concat('(ID#',id_repayment_Transaction,') REPAYMENT FOR LOAN ID(S) ',getRepaymentTransactionIDLoans(id_repayment_transaction)) as description,
                        rt.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)  as payee,rt.id_repayment_transaction as reference,0 as status,if(rt.transaction_type in (1,4),total_payment,swiping_amount) as total,
                        m.id_branch,m.address,2 as payee_type,if(rt.transaction_type=1,1,if(rt.transaction_type=3,3,2)) as paymode,rt.or_no
                        FROM repayment_transaction as rt
                        LEFT JOIN member as m on m.id_member = rt.id_member
                        where id_repayment_Transaction = ?;",[$id_repayment_transaction]);   
            $id_cash_receipt_voucher = DB::table('cash_receipt_voucher')->where('reference',$id_repayment_transaction)->where('type',2)->max('id_cash_receipt_voucher');      
        }else{
            if($cancel){

                $d = DB::table('repayment_transaction')->select("cancel_reason","date_cancelled")->where('id_repayment_transaction',$id_repayment_transaction)->first();

                 DB::table('cash_receipt_voucher')
                 ->where('id_cash_receipt_voucher',$id_cash_receipt_voucher)
                 ->update(['status'=>10,'description'=>DB::raw("concat(REPLACE(description,' [CANCELLED]',''),' [CANCELLED]')"),'date_cancelled'=>$d->date_cancelled,'cancellation_reason'=>$d->cancel_reason]);    

                return;            
            }
            if($edited){
                $d = DB::table('repayment_transaction')->where('id_repayment_transaction',$id_repayment_transaction)->first();
                DB::select("UPDATE repayment_transaction as rt
                LEFT JOIN cash_receipt_voucher as crv on crv.id_cash_receipt_voucher = rt.id_cash_receipt_voucher
                SET crv.date=rt.transaction_date,crv.or_no = rt.or_no,crv.description =concat('(ID#',reference,') REPAYMENT FOR LOAN ID(S) ',getRepaymentTransactionIDLoans(reference)),
                crv.paymode =if(rt.transaction_type=1,1,if(rt.transaction_type=3,3,2)),crv.total_amount = if(rt.transaction_type in (1,4),total_payment,swiping_amount)
                WHERE rt.id_repayment_transaction = ?",[$id_repayment_transaction]);

                 DB::table('cash_receipt_voucher')
                 ->where('id_cash_receipt_voucher',$id_cash_receipt_voucher)
                // ->update(['description'=>DB::raw("concat(REPLACE(description,' [EDITED]',''),' [EDITED]')")])  ;
                 ->update(['or_no'=>$d->or_no,'description'=>DB::raw("concat('(ID#',reference,') REPAYMENT FOR LOAN ID(S) ',getRepaymentTransactionIDLoans(reference))")])  ;
            }

            DB::table('cash_receipt_voucher_details')
            ->where('id_cash_receipt_voucher',$id_cash_receipt_voucher)
            ->delete();
        }
        $param = [];
        for($i=0;$i<10;$i++){
            array_push($param,$id_repayment_transaction);
        }

        // LEFT JOIN chart_account as ca on ca.id_chart_account = if(rt.transaction_type=1,1,tb.id_chart_account)

        $id_check_on_hand = config('variables.check_on_hand_account');
        // INSERT CRV CHILD
        DB::select("insert INTO cash_receipt_voucher_details (id_cash_receipt_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
                    /*************SWIPING AMOUNT**********************/
                    select $id_cash_receipt_voucher id_cash_receipt_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,if(rt.transaction_type in (1,4),total_payment,swiping_amount) as debit,0 as credit,
                    '' as remarks,id_repayment_transaction as reference
                    FROM repayment_transaction as rt
                     LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = if(rt.transaction_type=1,12,8)
                     LEFT JOIN tbl_bank as tb on tb.id_bank = rt.id_bank
                     LEFT JOIN chart_account as ca on ca.id_chart_account = if(rt.transaction_type=4,$id_check_on_hand,if(rt.transaction_type=1,1,tb.id_chart_account))
                     
                    where id_repayment_transaction = ?
                    UNION ALL
                    /*********************REBATES*****************/
                    SELECT $id_cash_receipt_voucher id_cash_receipt_voucher,ca.id_chart_account,ca.account_code,ca.description,amount as debit,0 as credit,concat('ID#',rb.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as remarks,rb.id_loan as reference
                    FROM repayment_rebates as rb
                    LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 3
                    LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
                    LEFT JOIN loan on loan.id_loan = rb.id_loan
                    LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                    WHERE rb.id_repayment_transaction = ?
                    UNION ALL
                   SELECT id_cash_receipt_voucher,id_chart_account,account_code,rep_loan.description,debit,SUM(credit) as credit,concat('ID#',reference,' ',getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms)) as remarks,reference FROM (
                   /*************LOAN PRINCIPAL AMOUNT**********************/
                    select $id_cash_receipt_voucher id_cash_receipt_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,paid_principal as credit ,
                    '' as remarks,rl.id_loan as reference
                    FROM repayment_loans as rl
                    LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 9
                    LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
                    WHERE id_repayment_transaction = ?
        
                    UNION ALL
                    /*************LOAN FEES**********************/
                    select $id_cash_receipt_voucher id_cash_receipt_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,paid_fees as credit ,
                    '' as remarks,rl.id_loan as reference
                    FROM repayment_loans as rl
                    LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 10
                    LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account 
                    WHERE id_repayment_transaction = ?
                    UNION ALL
                    /*************LOAN INTEREST**********************/
                    select $id_cash_receipt_voucher id_cash_receipt_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,paid_interest as credit ,
                    '' as remarks,rl.id_loan as reference
                    FROM repayment_loans as rl
                    LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance =6
                    LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account 
                    WHERE id_repayment_transaction = ?
                     UNION ALL
                    /*************LOAN SURCHARGE**********************/
                    select $id_cash_receipt_voucher id_cash_receipt_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,paid_surcharge as credit ,
                    '' as remarks,rl.id_loan as reference
                    FROM repayment_loans as rl
                  
                    LEFT JOIN chart_account as ca on ca.id_chart_account = 38
                    WHERE id_repayment_transaction = ?

                    ) as rep_loan
                    LEFT JOIN loan as l on rep_loan.reference = l.id_loan
                    LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
                    WHERE credit > 0
                    GROUP BY id_chart_account,reference
                   

                    UNION ALL
                    /*************OTHER FEES**********************/
                    select $id_cash_receipt_voucher id_cash_receipt_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,rf.amount as credit ,
                    pt.description as remarks,id_repayment_transaction as reference FROM repayment_fees as rf
                    LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
                    LEFT JOIN chart_account as ca on ca.id_chart_account = pt.id_chart_account 
                    WHERE id_Repayment_transaction =? and amount > 0
                    UNION ALL
                    /*************PENALTY**********************/
                    select $id_cash_receipt_voucher id_cash_receipt_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,rp.amount as credit ,
                    pt.description as remarks,id_repayment_transaction as reference FROM repayment_penalty as rp
                    LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rp.id_payment_type
                    LEFT JOIN chart_account as ca on ca.id_chart_account = pt.id_chart_account 
                    WHERE id_Repayment_transaction =? AND rp.amount > 0
                    UNION ALL
                    /***************LOAN PENALTY AND SURCHARGES*********************************/
                    SELECT $id_cash_receipt_voucher id_cash_receipt_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,rls.amount as credit ,
                    concat('ID#',rls.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as remarks,rls.id_loan as reference FROM repayment_loan_surcharges as rls
                    LEFT JOIN chart_account as ca on ca.id_chart_account= 38
                    LEFT JOIN loan on loan.id_loan = rls.id_loan
                    LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                    WHERE rls.id_repayment_transaction = ?
                    UNION ALL
                    /*****CHANGE*****/ 
                    select $id_cash_receipt_voucher id_cash_receipt_voucher,ca.id_chart_account,ca.account_code as account_code,ca.description,0 as debit,`change` as credit,
                    '' as remarks,id_repayment_transaction as reference
                    FROM repayment_transaction as rt
                    LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 11
                    LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
                    where id_repayment_transaction = ? and `change` > 0;",$param);

        $output['status'] = "SUCCESS";
        $output['id_cash_receipt_voucher'] = $id_cash_receipt_voucher;

        return $output;


        return "success";

    }
  public static function BankTransactionCRV($id_bank_transaction){
        $id_cash_receipt_voucher = DB::table('bank_transaction')->select('id_cash_receipt_voucher')->where('id_bank_transaction',$id_bank_transaction)->first()->id_cash_receipt_voucher;

        if($id_cash_receipt_voucher == 0){
            DB::select("INSERT INTO cash_receipt_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type)
            SELECT date,3 as type,concat('Bank Transaction ID# ',id_bank_transaction,' ',if(bt.type=1,concat('Deposit to ', tb.bank_name),if(bt.type=2,concat('Withdrawal from ',tb.bank_name),concat('Transfer from ',tb.bank_name,' to ',tb_trans.bank_name)))) as description,bt.id_member,
            if(bt.type=3,tb_trans.bank_name,bt.name) as payee,id_bank_transaction,0 as status,bt.amount,ifnull(m.id_branch,1) as id_branch,m.address,4 as payee_type
            FROM bank_transaction as bt
            LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
            LEFT JOIN tbl_bank as tb_trans on tb_trans.id_bank = bt.id_bank_transfer_to
            LEFT JOIN member as m on m.id_member = bt.id_member
            WHERE bt.id_bank_transaction = ?;",[$id_bank_transaction]);

            $id_cash_receipt_voucher = DB::table('cash_receipt_voucher')->where('type',3)->max('id_cash_receipt_voucher');
        }else{
            DB::select("UPDATE bank_transaction as bt
                        LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
                        LEFT JOIN tbl_bank as tb_trans on tb_trans.id_bank = bt.id_bank_transfer_to
                        LEFT JOIN member as m on m.id_member = bt.id_member
                        LEFT JOIN cash_receipt_voucher as crv on crv.id_cash_receipt_voucher = bt.id_cash_receipt_voucher
                        SET crv.date =bt.date,crv.description=concat('Bank Transaction ID# ',id_bank_transaction,' ',if(bt.type=1,concat('Deposit to ', tb.bank_name),if(bt.type=2,concat('Withdrawal from ',tb.bank_name),concat('Transfer from ',tb.bank_name,' to ',tb_trans.bank_name)))),
                        crv.id_member = bt.id_member,crv.payee =if(bt.type=3,tb_trans.bank_name,bt.name),crv.total_amount = bt.amount,crv.id_branch=ifnull(m.id_branch,1)
                        WHERE bt.id_bank_transaction = ?;",[$id_bank_transaction]);

            DB::table('cash_receipt_voucher_details')->where('id_cash_receipt_voucher',$id_cash_receipt_voucher)->delete();
        }

        DB::select("
            INSERT INTO cash_receipt_voucher_details (id_cash_receipt_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
            SELECT $id_cash_receipt_voucher as id_cash_receipt_voucher,ca.id_chart_account,ca.account_code,ca.description,amount as debit,0 as credit,'',id_bank_transaction from bank_transaction as bt
            LEFT JOIN tbl_bank as tb on tb.id_bank = if(bt.type=2 or bt.type=1,bt.id_bank,bt.id_bank_transfer_to)
            LEFT JOIN chart_account as ca on ca.id_chart_account = if(bt.type=2,1,tb.id_chart_account)
            where id_bank_transaction = ?
            UNION ALL
            SELECT $id_cash_receipt_voucher as id_cash_receipt_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,amount as credit,'',id_bank_transaction from bank_transaction as bt
            LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
            LEFT JOIN chart_account as ca on ca.id_chart_account = if(bt.type=3 OR bt.type=2,tb.id_chart_account,1)
            where id_bank_transaction = ?;",[$id_bank_transaction,$id_bank_transaction]);

        DB::table('bank_transaction')
        ->where('id_bank_transaction',$id_bank_transaction)
        ->update(['id_cash_receipt_voucher'=>$id_cash_receipt_voucher]);
    }
    public static function InvestmentCRV($id_investment){
        $id_cash_receipt_voucher = DB::table('investment')->select('id_cash_receipt_voucher')->where('id_investment',$id_investment)->first()->id_cash_receipt_voucher;

        if($id_cash_receipt_voucher == 0){
            DB::select("INSERT INTO cash_receipt_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type,paymode,or_no)
            SELECT investment_date,4 as type,concat('Investment - (ID#',id_investment,') ',ip.product_name,' ',if(i.or_number is not null,concat(' [OR# ',i.or_number,']'),'')) as description,i.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)  as payee,
            i.id_investment as reference,0 as status,i.amount as total,m.id_branch,m.address,2 as payee_type,1 as paymode,i.or_number as or_no
            FROM investment as i
            LEFT JOIN member as m on m.id_member= i.id_member
            LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
            WHERE i.status = 2 and i.id_investment=?;",[$id_investment]);  

            $id_cash_receipt_voucher=$max_id = DB::table('cash_receipt_voucher')
                      ->where('type',4)
                      ->where('reference',$id_investment)
                      ->max('id_cash_receipt_voucher');

            DB::table('investment')
            ->where('id_investment',$id_investment)
            ->update([
                'id_cash_receipt_voucher'=>$max_id
            ]);
        }else{
            DB::select("UPDATE investment as i
                        LEFT JOIN member as m on m.id_member= i.id_member
                        LEFT JOIN cash_receipt_voucher as crv on crv.id_cash_receipt_voucher = i.id_cash_receipt_voucher
                        LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
                        SET crv.date = i.investment_date,crv.description = concat('Investment - (ID#',id_investment,') ',ip.product_name,' ',if(i.or_number is not null,concat(' [OR# ',i.or_number,']'),'')),crv.payee=FormatName(m.first_name,m.middle_name,m.last_name,m.suffix),
                        crv.total_amount = i.amount,crv.id_branch = m.id_branch,crv.address = m.address,crv.or_no = i.or_number
                        WHERE i.id_investment = ? and i.status = 2;",[$id_investment]); 
 
            DB::table('cash_receipt_voucher_details')
            ->where('id_cash_receipt_voucher',$id_cash_receipt_voucher)
            ->delete();         
        }

        DB::select("INSERT INTO cash_receipt_voucher_details (id_cash_receipt_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
                    SELECT $id_cash_receipt_voucher as id_cash_receipt_voucher,ca.id_chart_account,ca.account_code,ca.description,amount as debit,0 as credit,'',i.id_investment
                    FROM investment as i 
                    LEFT JOIN chart_account as ca on ca.id_chart_account = 1
                    WHERE i.id_investment=?
                    UNION ALL
                    SELECT $id_cash_receipt_voucher as id_cash_receipt_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,amount as credit,'',i.id_investment
                    FROM investment as i 
                    LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
                    LEFT JOIN chart_account as ca on ca.id_chart_account = ip.id_chart_account
                    WHERE i.id_investment=?;",[$id_investment,$id_investment]);


        return "SUCCESS";

    }

    public static function CheckDepositCRV($id_check_deposit){
        $id_cash_receipt_voucher = DB::table('check_deposit')
                                  ->select("id_cash_receipt_voucher")
                                  ->where('id_check_deposit',$id_check_deposit)
                                  ->first()->id_cash_receipt_voucher;

        if($id_cash_receipt_voucher == 0){
            DB::select("INSERT INTO cash_receipt_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type,paymode,or_no)
            SELECT cd.date_deposited as transaction_date,5 as type,UPPER(concat('TO DEPOSIT THE CHECK No(s) ',group_concat(r.check_no),' (',tb.bank_name,')')) as description,null as id_member,'MKMPC' as payee,cd.id_check_deposit as reference ,
            0 as status,cd.amount as total,1 as id_branch,null as address,4 as payee_type,2 as paymode,null as or_no
            FROM check_deposit as cd
            LEFT JOIN check_deposit_details as cdd on cdd.id_check_deposit = cd.id_check_deposit
            LEFT JOIN repayment_payment as r on r.id_repayment_payment= cdd.id_repayment_payment
            LEFT JOIN tbl_bank as tb on tb.id_bank = cd.id_bank
            WHERE cd.id_check_deposit = ?
            GROUP BY cd.id_check_deposit;",[$id_check_deposit]);

            $id_cash_receipt_voucher = DB::table('cash_receipt_voucher')
                                       ->where('type',5)
                                       ->max('id_cash_receipt_voucher');



            DB::table('check_deposit')
            ->where('id_check_deposit',$id_check_deposit)
            ->update(['id_cash_receipt_voucher'=>$id_cash_receipt_voucher]);

        }else{
            DB::select("UPDATE  (
            SELECT cd.date_deposited as transaction_date,UPPER(concat('TO DEPOSIT THE CHECK No(s) ',group_concat(r.check_no),' (',tb.bank_name,')')) as description,'MKMPC' as payee,cd.id_check_deposit as reference 
            ,cd.amount as total,null as address,null as or_no,cd.id_cash_receipt_voucher
            FROM check_deposit as cd
            LEFT JOIN check_deposit_details as cdd on cdd.id_check_deposit = cd.id_check_deposit
            LEFT JOIN repayment_payment as r on r.id_repayment_payment= cdd.id_repayment_payment
            LEFT JOIN tbl_bank as tb on tb.id_bank = cd.id_bank
            WHERE cd.id_check_deposit = ?
            GROUP BY cd.id_check_deposit) as cr
            LEFT JOIN cash_receipt_voucher as crv on crv.id_cash_receipt_voucher = cr.id_cash_receipt_voucher
            SET crv.date = cr.transaction_date,crv.description = cr.description,crv.payee = cr.payee,crv.total_amount = cr.total,crv.address = cr.address,crv.or_no = cr.or_no
            ;",[$id_check_deposit]);

            DB::table('cash_receipt_voucher_details')
            ->where('id_cash_receipt_voucher',$id_cash_receipt_voucher)
            ->delete();
        }
        $id_check_on_hand = config('variables.check_on_hand_account');
        DB::select("INSERT INTO cash_receipt_voucher_details (id_cash_receipt_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
        SELECT $id_cash_receipt_voucher as id_cash_receipt_voucher,ca.id_chart_account,ca.account_code,ca.description,amount as debit,0 as credit,'' as details,cd.id_check_deposit
        FROM check_deposit as cd
        LEFT JOIN tbl_bank as tb on tb.id_bank = cd.id_bank
        LEFT JOIN chart_account as ca on ca.id_chart_account = tb.id_chart_account
        WHERE cd.id_check_deposit = ?
        UNION ALL
        SELECT $id_cash_receipt_voucher as id_cash_receipt_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,amount as credit,'' as details,cd.id_check_deposit
        FROM check_deposit as cd
        LEFT JOIN chart_account as ca on ca.id_chart_account = $id_check_on_hand
        WHERE cd.id_check_deposit = ?;",[$id_check_deposit,$id_check_deposit]);

    }
    public static function ChangePayableCRV($id_repayment,$id_cash_receipt_voucher){
        $id_check_on_hand = config('variables.check_on_hand_account');
        if($id_cash_receipt_voucher == 0){
            DB::select("INSERT INTO cash_receipt_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type,paymode,or_no)
            SELECT date,6 as type,concat('CHANGE PAYABLES FOR REPAYMENT ID# ',r.id_repayment) as description,null as id_member,
            concat('REPAYMENT ID# ',r.id_repayment) as payee,r.id_repayment as reference,0 as status, change_payable as total_amount,1 as id_branch,'' as address
            ,4 as payee_type, 2 as paymode,r.or_number as or_no
            FROM repayment as r
            WHERE r.id_repayment = ?;",[$id_repayment]);

            $id_cash_receipt_voucher = DB::table('cash_receipt_voucher')->where('type',6)->max('id_cash_receipt_voucher');
        }else{
            DB::select("UPDATE repayment as r
            LEFT JOIN cash_receipt_voucher as crv on crv.id_cash_receipt_voucher = r.id_cash_receipt_voucher
            set crv.date =r.date,crv.description =concat('CHANGE PAYABLES FOR REPAYMENT ID# ',r.id_repayment),
            crv.payee = concat('REPAYMENT ID# ',r.id_repayment),crv.status = 0,
            crv.total_amount = change_payable,crv.or_no = r.or_number
            WHERE r.id_repayment = ?;",[$id_repayment]);
        }

        DB::table('cash_receipt_voucher_details')
        ->where('id_cash_receipt_voucher',$id_cash_receipt_voucher)
        ->delete();


        DB::select("INSERT INTO cash_receipt_voucher_details (id_cash_receipt_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
        SELECT $id_cash_receipt_voucher as id_cash_receipt_voucher,ca.id_chart_account,ca.account_code,ca.description,change_payable as debit,0 as credit
        ,'' as details,r.id_repayment as reference
        FROM repayment as r
        LEFT JOIN chart_account as ca on ca.id_chart_account = $id_check_on_hand
        WHERE r.id_repayment = ?
        UNION ALL
        SELECT $id_cash_receipt_voucher as id_cash_receipt_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,change_payable as credit
        ,'' as details,r.id_repayment as reference
        FROM repayment as r
        LEFT JOIN chart_account as ca on ca.id_chart_account = 23
        WHERE r.id_repayment = ?; ",[$id_repayment,$id_repayment]);

        DB::table('repayment')
        ->where('id_repayment',$id_repayment)
        ->update(['id_cash_receipt_voucher'=>$id_cash_receipt_voucher]);
           

    }
}