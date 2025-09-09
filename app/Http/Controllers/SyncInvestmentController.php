<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;

class SyncInvestmentController extends Controller
{
    public function sync_data(){
        return DB::select("SELECT * FROM (
SELECT 49 as id_member,1 as id_investment_product,'2023-01-13' as investment_date,50000 as amount,0.75 as interest_rate,12 as terms,21875 as total_interest_with UNION ALL
SELECT 53 as id_member,2 as id_investment_product,'2023-02-03' as investment_date,100000 as amount,9 as interest_rate,1 as terms,9000 as total_interest_with UNION ALL
SELECT 86 as id_member,1 as id_investment_product,'2023-03-24' as investment_date,300000 as amount,0.75 as interest_rate,12 as terms,15750 as total_interest_with UNION ALL
SELECT 35 as id_member,1 as id_investment_product,'2023-03-24' as investment_date,50000 as amount,0.75 as interest_rate,12 as terms,2625 as total_interest_with UNION ALL
SELECT 49 as id_member,1 as id_investment_product,'2023-03-24' as investment_date,100000 as amount,0.75 as interest_rate,12 as terms,5250 as total_interest_with UNION ALL
SELECT 62 as id_member,1 as id_investment_product,'2023-04-12' as investment_date,300000 as amount,0.75 as interest_rate,12 as terms,15750 as total_interest_with UNION ALL
SELECT 187 as id_member,1 as id_investment_product,'2023-04-28' as investment_date,400000 as amount,0.75 as interest_rate,12 as terms,21000 as total_interest_with UNION ALL
SELECT 187 as id_member,1 as id_investment_product,'2023-05-21' as investment_date,100000 as amount,0.75 as interest_rate,12 as terms,5250 as total_interest_with UNION ALL
SELECT 74 as id_member,1 as id_investment_product,'2023-06-04' as investment_date,200000 as amount,0.75 as interest_rate,12 as terms,10500 as total_interest_with UNION ALL
SELECT 187 as id_member,1 as id_investment_product,'2023-06-29' as investment_date,100000 as amount,0.75 as interest_rate,12 as terms,5250 as total_interest_with UNION ALL
SELECT 49 as id_member,1 as id_investment_product,'2023-07-05' as investment_date,100000 as amount,0.75 as interest_rate,12 as terms,5250 as total_interest_with UNION ALL
SELECT 62 as id_member,1 as id_investment_product,'2022-08-10' as investment_date,100000 as amount,0.75 as interest_rate,12 as terms,5250 as total_interest_with UNION ALL
SELECT 86 as id_member,1 as id_investment_product,'2022-08-10' as investment_date,200000 as amount,0.75 as interest_rate,12 as terms,10500 as total_interest_with UNION ALL
SELECT 49 as id_member,1 as id_investment_product,'2022-10-11' as investment_date,100000 as amount,0.75 as interest_rate,12 as terms,5250 as total_interest_with UNION ALL
SELECT 62 as id_member,1 as id_investment_product,'2022-10-25' as investment_date,500000 as amount,0.75 as interest_rate,12 as terms,26250 as total_interest_with UNION ALL
SELECT 27 as id_member,1 as id_investment_product,'2022-11-27' as investment_date,50000 as amount,0.75 as interest_rate,12 as terms,2625 as total_interest_with UNION ALL
SELECT 49 as id_member,1 as id_investment_product,'2022-12-06' as investment_date,100000 as amount,0.75 as interest_rate,12 as terms,5250 as total_interest_with UNION ALL
SELECT 187 as id_member,1 as id_investment_product,'2022-12-28' as investment_date,100000 as amount,0.75 as interest_rate,12 as terms,0 as total_interest_with UNION ALL
SELECT 122 as id_member,3 as id_investment_product,'2023-01-14' as investment_date,80000 as amount,9 as interest_rate,1 as terms,7200 as total_interest_with UNION ALL
SELECT 186 as id_member,3 as id_investment_product,'2023-02-16' as investment_date,100000 as amount,9 as interest_rate,1 as terms,9000 as total_interest_with UNION ALL
SELECT 186 as id_member,3 as id_investment_product,'2023-03-14' as investment_date,120000 as amount,9 as interest_rate,1 as terms,10800 as total_interest_with UNION ALL
SELECT 185 as id_member,3 as id_investment_product,'2023-05-02' as investment_date,300000 as amount,9 as interest_rate,1 as terms,27000 as total_interest_with UNION ALL
SELECT 63 as id_member,3 as id_investment_product,'2023-05-26' as investment_date,200000 as amount,9 as interest_rate,1 as terms,20000 as total_interest_with UNION ALL
SELECT 69 as id_member,3 as id_investment_product,'2022-08-23' as investment_date,200000 as amount,10 as interest_rate,1 as terms,0 as total_interest_with 
) as inv
ORDER BY investment_date ASC;;");
    }
    public function sync(){
        // $sync_data = [
        //     ['id_member'=>49,'id_investment_product'=>3,'investment_date'=>'2023-01-13','amount'=>100000,'interest_rate'=>0.75,'terms'=>12,'total_interest_with'=>1500],
        //     ['id_member'=>86,'id_investment_product'=>3,'investment_date'=>'2023-03-24','amount'=>300000,'interest_rate'=>0.75,'terms'=>12,'total_interest_with'=>27000],
        //     ['id_member'=>62,'id_investment_product'=>3,'investment_date'=>'2023-04-12','amount'=>300000,'interest_rate'=>0.75,'terms'=>12,'total_interest_with'=>27000],

        // ];


        $this->sync_withdrawal();
        return;
        $sync_data = json_decode(json_encode($this->sync_data()),true);

        $withdrawal_obj = array();
        $invController = new InvestmentController();

        foreach($sync_data as $sd){
            $id_investment_product_terms = DB::table('investment_product_terms')
                     ->where('id_investment_product',$sd['id_investment_product'])
                     ->where('terms',$sd['terms'])
                     ->where('interest_rate',$sd['interest_rate'])
                     ->first()->id_investment_product_terms;

            $request_param = array(
                'investor'=>$sd['id_member'],
                'investment_product'=>$sd['id_investment_product'],
                'id_investment_product_terms'=>$id_investment_product_terms,
                'amount'=>$sd['amount'],
                'opcode'=>0,
                'id_investment'=>0,
                'from_renewal'=> true
            );

            // dd($request_param);
            $post = $invController->post(new Request($request_param));

            $id_investment = $post['INVESTMENT_ID'];
            $update_status_param = array(
                'id_investment'=>$id_investment,
                'status'=>2,
                'status_mode'=>2,
                'investment_date'=>$sd['investment_date'],
                'beg_confirmation'=>true
            );

            $invController->update_investment_status(new Request($update_status_param));


            // //Withdrawals


            // DB::table('investment_withdrawal_batch')
            // ->insert($batch_obj);

            // $id_investment_withdrawal_batch = DB::table('investment_withdrawal_batch')->where('type',3)->where('ref',$id_investment_new)->max('id_investment_withdrawal_batch');

            $withdrawal_obj[] = [ 
                'id_investment_withdrawal_batch' =>0,
                'id_investment'=>$id_investment,
                'id_member'=>$sd['id_member'],
                'principal'=> 0,
                'interest'=> $sd['total_interest_with'],
                'total_amount'=> $sd['total_interest_with'],
                'status'=> 1,
                'id_user'=>MySession::myId()
            ];            
            
        }

        // //push withdrawals
        // $batch_obj = [
        //     'type'=>4,
        //     'date_released'=>'2023-05-20',
        //     'total_amount' => array_sum(array_column($sync_data,'total_interest_with')),
        //     'id_member'=>0,
        //     'id_user'=>MySession::myId(),
        //     'id_user_status'=>MySession::myId(),
        //     'close_request'=>0,
        //     'ref'=>0,
        //     'status'=>1
        // ];
        // DB::table('investment_withdrawal_batch')
        // ->insert($batch_obj);

        // $id_investment_withdrawal_batch = DB::table('investment_withdrawal_batch')->where('type',4)->max('id_investment_withdrawal_batch');

        // for($i=0;$i<count($withdrawal_obj);$i++){
        //     $withdrawal_obj[$i]['id_investment_withdrawal_batch'] = $id_investment_withdrawal_batch;
        // }

        // DB::table('investment_withdrawal')
        // ->insert($withdrawal_obj);

        // dd($withdrawal_obj);
        // // $sum = array_sum(array_column($sync_data,'total_interest_with'));
        // dd($sync_data);
    }
    public function sync_withdrawal(){
        $investments = DB::select("SELECT i.id_investment,SUM(interest_amount) as total_interest,i.id_member FROM investment as i 
        LEFT JOIN investment_table as it on it.id_investment = i.id_investment and it.date <= '2023-07-31'
        where i.id_investment_product = 1   
        GROUP BY i.id_investment
        HAVING SUM(interest_amount) > 0;");

        $withdrawal_obj = array();
        $sum = array_sum(array_column($investments,'total_interest'));


        $batch_obj = [
            'type'=>4,
            'date_released'=>'2023-07-31',
            'total_amount' => $sum,
            'id_member'=>0,
            'id_user'=>MySession::myId(),
            'id_user_status'=>MySession::myId(),
            'close_request'=>0,
            'ref'=>0,
            'status'=>1
        ];

        DB::table('investment_withdrawal_batch')
        ->insert($batch_obj);

        $id_investment_withdrawal_batch = DB::table('investment_withdrawal_batch')->where('type',4)->max('id_investment_withdrawal_batch');

        foreach($investments as $i){
             $withdrawal_obj[] = [ 
                'id_investment_withdrawal_batch' =>$id_investment_withdrawal_batch,
                'id_investment'=>$i->id_investment,
                'id_member'=>$i->id_member,
                'principal'=> 0,
                'interest'=> $i->total_interest,
                'total_amount'=> $i->total_interest,
                'status'=> 1,
                'id_user'=>MySession::myId()
            ];   
        }
        DB::table('investment_withdrawal')
        ->insert($withdrawal_obj);

        dd($withdrawal_obj);

    }
}

// DELETE FROM investment_withdrawal_batch;

// ALTER TABLE investment_withdrawal_batch  auto_increment=1;
// ALTER TABLE investment_withdrawal  auto_increment=1;