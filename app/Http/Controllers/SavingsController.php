<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SavingsController extends Controller
{
    public function test(){
        $amount = 150000;
        $interest_rate = 3.5;

        $this->compute($amount,$interest_rate);
    }
    public function compute($amount,$interest_rate){
        $interest_amount = $amount*($interest_rate/100);

        $interest_amount_per_annum = ROUND($interest_amount/365,2);
        $output = array(
            'beginning_balance'=>$amount,
            'interest_rate'=>$interest_rate,
            'gained_interest'=>$interest_amount_per_annum,
            'ending_balance'=>$amount+$interest_amount_per_annum
        );

        dd($output);


        dd($interest_amount_per_annum);
    }
}
