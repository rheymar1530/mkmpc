<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\WebHelper;


class MyDuesController extends Controller
{
    public function index(){
        $rp = new RepaymentController();

        $dt = WebHelper::ConvertDatePeriod(MySession::current_date());
        $data['loans'] = $rp->ActiveLoans(MySession::myId(),$dt,0);

        $data['head_title'] = "My Dues";
        return view('my_due.index',$data);
    }
}
