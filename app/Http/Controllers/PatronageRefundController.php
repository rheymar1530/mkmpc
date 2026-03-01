<?php

namespace App\Http\Controllers;

use App\MySession;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class PatronageRefundController extends Controller
{
    public function create(Request $request){
        $data['sidebar']="sidebar-collapse";

        $data['sel_year'] = $year = $request->year ?? MySession::current_year();
        $data['icsp'] = $ICP = $this->cleanNumber($request->icsp ?? $this->parseInterestCapitalSharePayables($year,0));
        $data['prp'] = $PR = $this->cleanNumber($request->prp ?? $this->parsePatronageRefundPayables($year,0));



        $mem_allocation = $this->CompileAllocationData($year,$ICP,$PR);

        $data = [...$data,...$mem_allocation ];


        // foreach($mem_allocation as $m){}

        return view('patronage_refunds.create',$data);

    }
    public function CompileAllocationData(int $year,$ICP,$PR){

        // transactions months
        $transactionDates = $this->getMonthlyDateRanges($year);

        $MonthCount = count($transactionDates);




        $StartDate = $transactionDates[0]['start'];
        $EndDate = $transactionDates[$MonthCount-1]['end'];


        $tempArray = array();

        foreach($transactionDates as $tr){
            array_push($tempArray,"SUM(CASE WHEN transaction_date >= '{$tr['start']}' AND transaction_date <= '{$tr['end']}' THEN amount else 0 END) as '{$tr['month']}'");
        }

        $TransactionDateString = implode(', ',$tempArray).", SUM(amount) as Total";


        $g = new GroupArrayController();

        $cbuData = $this->MembersCBU($StartDate,$EndDate,$TransactionDateString);
        $TotalCBU = collect($cbuData)->sum('Total');



        $cbuData = $g->array_group_by($cbuData,['id_member']);


        $interestData = $this->MemberInterests((int) date("m", strtotime($StartDate)),(int) date("m", strtotime($EndDate)),$year);
        $TotalInterest = collect($interestData)->sum('Total');
        $interestData = $g->array_group_by($interestData,['id_member']);


        $MemberLists  = DB::table('member as m')
                        ->select('m.id_member',DB::raw("FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as Name,
                        if(m.id_baranggay_lgu is null,'Regular',concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name)) as dataGroupings"))
                        ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','m.id_baranggay_lgu')
                        ->orderBy(DB::raw("if(m.id_baranggay_lgu is null,3,bl.type) "))
                        ->orderBy(DB::raw("concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name) "))
                        ->orderBy('Name')
                        ->get();
        $MemberLists = $g->array_group_by($MemberLists,['dataGroupings']);







        $ISCRate =  ($TotalCBU > 0) ? ($ICP/($TotalCBU/$MonthCount)) : 0;
        $PRRate = ($TotalInterest > 0) ? ($PR/$TotalInterest) : 0;



        $MemberFinalData = array();
        foreach($MemberLists as $group=>$members){
            $MemberListsTable = array();
            foreach($members as $m){
                $CBUAmt_ = isset($cbuData[$m->id_member]) ? $cbuData[$m->id_member][0]->Total : 0;
                $IntAmt_ = isset($interestData[$m->id_member]) ? $interestData[$m->id_member][0]->Total : 0;
                $TotalAmt_ = $CBUAmt_ + $IntAmt_;

                if($TotalAmt_ > 0) {

                    $AverageCBU = $CBUAmt_/$MonthCount;
                    $MemberICS = ROUND($AverageCBU*$ISCRate,2);

                    $MemberPR = ROUND($PRRate*$IntAmt_,2);
                    $MemberListsTable[]= [
                        'member' => $m->Name,
                        'InterestTotal'=>$IntAmt_,
                        'CBUTotal'=>$CBUAmt_,
                        'Total'=>$TotalAmt_,
                        'AverageCBU'=>$AverageCBU,
                        'ICS'=>$MemberICS,
                        'PR'=>$MemberPR,
                        'TotalPayables'=>$MemberICS+$MemberPR
                    ];
                }
            }
            $MemberFinalData[$group] = $MemberListsTable;
        }

        $output = array();
        $output['MemberFinalData'] = $MemberFinalData;

        return $output;


    }
    public function MemberInterests($start_month, $end_month,$year){
        $intCon = new PaidInterestSummaryController();

        $interestData = $intCon->parseData($start_month, $end_month,$year,1)['interest_table'];

        return $interestData;


    }

    public function MembersCBU(string $StartDate,string $EndDate,string $TransactionDateString){
        $cbuCon = new CBUController();
        $CBUSQL = $cbuCon->CBUMonthlyQueryBase();

        $sql =" SELECT FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as Name,m.id_member,$TransactionDateString FROM (
            $CBUSQL
        ) as cbu
        LEFT JOIN member as m on m.id_member = cbu.id_member
        WHERE cbu.transaction_date >= ?
        GROUP BY cbu.id_member
        HAVING SUM(amount) > 0
        ORDER BY Name;";

        $param = array_fill(0,6,$EndDate);
        $cbuData = DB::select($sql,[...$param,$StartDate]);

        return $cbuData;
    }


    public function parsePatronageRefundPayables($year,$control_number){

        return 169497.53;
    }

    public function parseInterestCapitalSharePayables($year,$control_number){
         return 254246.29;
    }




    function getMonthlyDateRanges(int $year): array
    {
        $ranges = [];

        $currentYear  = MySession::current_year();
        $currentMonth = MySession::current_month();

        $lastMonth = ($year == $currentYear) ? $currentMonth : 12;

        for ($month = 1; $month <= $lastMonth; $month++) {

            $date  = Carbon::create($year, $month, 1);
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();

            $ranges[] = [
                'month' => $date->format('M'),
                'start' => $start->toDateString(),
                'end'   => $end->toDateString(),
            ];
        }

        return $ranges;
    }

    function cleanNumber($formattedNumber) {
        return (float)str_replace(',', '', $formattedNumber);
    }
}
