<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use DateTime;

class ChartBudgetController extends Controller
{
    public function index(Request $request){
        $data['sidebar'] ='sidebar-collapse';
        $data['selected_type'] = $request->type ?? 5;
        $data['selected_year'] = $request->year ?? MySession::current_year();
        $data['types'] = DB::table('chart_account_type')
                        ->where('id_chart_account_type','>',0)
                        ->get();

        $data['chart_accounts'] = DB::table('chart_account')
                                  ->select(DB::raw("id_chart_account,concat(description) as description"))
                                  ->where('id_chart_account_type',$data['selected_type'])
                                  ->get();

        $chart_budget = DB::table('chart_account_budget as cab')
                        ->select(DB::raw("cab.id_chart_account,cab.month,cab.amount"))
                        ->leftJoin('chart_account as ca','ca.id_chart_account','cab.id_chart_account')
                        ->where('year',$data['selected_year'])
                        ->where('ca.id_chart_account_type',$data['selected_type'])
                        ->get();

        $g = new GroupArrayController();

        $chart_budget = $g->array_group_by($chart_budget,['id_chart_account']);

        // return $chart_budget;
        $data['chart_budget'] = array();
        foreach($chart_budget as $id=>$cb){
            $data['chart_budget'][$id] = array();
            $t = array();
            foreach($cb as $c){
                $t[$c->month] = $c->amount;
            }

            $data['chart_budget'][$id] = $t;
        }

        return view('chart_budget.index',$data);

    }

    public function post(Request $request){
        if($request->ajax()){
            $year = $request->year;
            $chart_budget = $request->chart_budget;

            $post_data = array();

            foreach($chart_budget as $chart){
                foreach($chart['monthly_amount'] as $m=>$cm){
                    $temp = array();
                    $temp['year'] = $year;
                    $temp['id_chart_account'] = $chart['id_chart_account'];
                    $temp['month'] = $m;
                    $temp['amount'] = $cm;

                    array_push($post_data,$temp);
                }
            }

            //post data
            //remove existing
            DB::table('chart_account_budget')
            ->where('year',$year)
            ->delete();


            DB::table('chart_account_budget')
            ->insert($post_data);

            return response("SUCCESS");

            return $post_data;
            return response($request);
        }
    }   
}
