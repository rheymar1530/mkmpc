<?php

namespace App\Http\Controllers;

use App\MySession;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Illuminate\Database\QueryException;

class PatronageRefundController extends Controller
{

    private $DefAllocation = [
        ["description" => "Cash" ,"key" =>"w_cash"],
        ["description" => "CBU" ,"key" =>"w_cbu"]
    ];

    public function create(Request $request){
        $data['sidebar']="sidebar-collapse";

        $data['sel_year'] = $year = $request->year ?? MySession::current_year();
        $data['icsp'] = $ICP = $this->cleanNumber($request->icsp ?? $this->parseInterestCapitalSharePayables($year,0));
        $data['prp'] = $PR = $this->cleanNumber($request->prp ?? $this->parsePatronageRefundPayables($year,0));

        $data['DefAllocation'] = $this->DefAllocation;


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

        // $ISCRate =  ($TotalCBU > 0) ? ($ICP/($TotalCBU/$MonthCount)) : 0;
        // $PRRate = ($TotalInterest > 0) ? ($PR/$TotalInterest) : 0;



        // $MemberFinalData = array();
        // foreach($MemberLists as $group=>$members){
        //     $MemberListsTable = array();
        //     foreach($members as $m){
        //         $CBUAmt_ = isset($cbuData[$m->id_member]) ? $cbuData[$m->id_member][0]->Total : 0;
        //         $IntAmt_ = isset($interestData[$m->id_member]) ? $interestData[$m->id_member][0]->Total : 0;
        //         $TotalAmt_ = $CBUAmt_ + $IntAmt_;

        //         if($TotalAmt_ > 0) {

        //             $AverageCBU = $CBUAmt_/$MonthCount;
        //             $MemberICS = ROUND($AverageCBU*$ISCRate,2);

        //             $MemberPR = ROUND($PRRate*$IntAmt_,2);
        //             $MemberListsTable[]= [
        //                 'id_member'=>$m->id_member,
        //                 'member' => $m->Name,
        //                 'InterestTotal'=>$IntAmt_,
        //                 'CBUTotal'=>$CBUAmt_,
        //                 'Total'=>$TotalAmt_,
        //                 'AverageCBU'=>$AverageCBU,
        //                 'ICS'=>$MemberICS,
        //                 'PR'=>$MemberPR,
        //                 'TotalPayables'=>$MemberICS+$MemberPR
        //             ];
        //         }
        //     }
        //     $MemberFinalData[$group] = $MemberListsTable;
        // }

        $ISCRate = ($TotalCBU > 0) ? ($ICP / ($TotalCBU / $MonthCount)) : 0;
        $PRRate  = ($TotalInterest > 0) ? ($PR / $TotalInterest) : 0;

        $ICP_total = $ICP;
        $PR_total  = $PR;

        $AllMembers = [];
        $MemberFinalData = [];

        /*
        |--------------------------------------------------------------------------
        | STEP 1 — COMPUTE RAW VALUES FOR ALL MEMBERS
        |--------------------------------------------------------------------------
        */

        foreach ($MemberLists as $group => $members) {

            foreach ($members as $m) {

                $CBUAmt_ = isset($cbuData[$m->id_member]) ? $cbuData[$m->id_member][0]->Total : 0;
                $IntAmt_ = isset($interestData[$m->id_member]) ? $interestData[$m->id_member][0]->Total : 0;
                $TotalAmt_ = $CBUAmt_ + $IntAmt_;

                if ($TotalAmt_ > 0) {

                    $AverageCBU = $CBUAmt_ / $MonthCount;

                    $ICS_raw = $AverageCBU * $ISCRate;
                    $PR_raw  = $PRRate * $IntAmt_;

                    $ICS_member = round($ICS_raw, 2);
                    $PR_member  = round($PR_raw, 2);

                    $AllMembers[] = [
                        'group' => $group,
                        'id_member' => $m->id_member,
                        'member' => $m->Name,
                        'InterestTotal' => $IntAmt_,
                        'CBUTotal' => $CBUAmt_,
                        'Total' => $TotalAmt_,
                        'AverageCBU' => $AverageCBU,
                        'ICS_raw' => $ICS_raw,
                        'PR_raw' => $PR_raw,
                        'ICS' => $ICS_member,
                        'PR' => $PR_member
                    ];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2 — COMPUTE TOTALS
        |--------------------------------------------------------------------------
        */

        $totalICS = 0;
        $totalPR  = 0;

        foreach ($AllMembers as $m) {
            $totalICS += $m['ICS'];
            $totalPR  += $m['PR'];
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 3 — CALCULATE ROUNDING DIFFERENCE
        |--------------------------------------------------------------------------
        */

        $ICSdiffCents = round(($ICP_total - $totalICS) * 100);
        $PRdiffCents  = round(($PR_total - $totalPR) * 100);

        $memberCount = count($AllMembers);

        /*
        |--------------------------------------------------------------------------
        | STEP 4 — DISTRIBUTE ICS ROUNDING
        |--------------------------------------------------------------------------
        */

        $i = 0;

        while ($ICSdiffCents != 0 && $memberCount > 0) {

            if ($ICSdiffCents > 0) {
                $AllMembers[$i]['ICS'] += 0.01;
                $ICSdiffCents--;
            } else {
                $AllMembers[$i]['ICS'] -= 0.01;
                $ICSdiffCents++;
            }

            $i++;
            if ($i >= $memberCount) $i = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 5 — DISTRIBUTE PR ROUNDING
        |--------------------------------------------------------------------------
        */

        $i = 0;

        while ($PRdiffCents != 0 && $memberCount > 0) {

            if ($PRdiffCents > 0) {
                $AllMembers[$i]['PR'] += 0.01;
                $PRdiffCents--;
            } else {
                $AllMembers[$i]['PR'] -= 0.01;
                $PRdiffCents++;
            }

            $i++;
            if ($i >= $memberCount) $i = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 6 — REBUILD GROUP STRUCTURE
        |--------------------------------------------------------------------------
        */

        foreach ($AllMembers as $m) {

            $group = $m['group'];

            $m['TotalPayables'] = $m['ICS'] + $m['PR'];

            $MemberFinalData[$group][] = [
                'id_member' => $m['id_member'],
                'member' => $m['member'],
                'InterestTotal' => $m['InterestTotal'],
                'CBUTotal' => $m['CBUTotal'],
                'Total' => $m['Total'],
                'AverageCBU' => $m['AverageCBU'],
                'ICS' => $m['ICS'],
                'PR' => $m['PR'],
                'TotalPayables' => $m['TotalPayables']
            ];
        }

        $output = array();
        $output['MemberFinalData'] = $MemberFinalData;
        $output['ISCRate']= $ISCRate;
        $output['PRRate'] = $PRRate;

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

    public function post(Request $request){


        $year = $request->year ?? MySession::current_year();

        $year = 2025;
        $ICP = $this->cleanNumber($request->icsp ?? $this->parseInterestCapitalSharePayables($year,0));
        $PR = $this->cleanNumber($request->prp ?? $this->parsePatronageRefundPayables($year,0));
        $remarks = $request->remarks ?? '';
        $default_allocation = $request->default_allocation ?? 0;
        // dd($request->all())
        $def_key = $this->DefAllocation[$default_allocation]['key'];



        $m = $this->CompileAllocationData($year,$ICP,$PR);

        $opcode = $request->opcode  ?? 0;
        $id_patronage_capital_allocation = $request->id_patronage_capital_allocation ?? 0;

        try{
            DB::beginTransaction();
            $allocationParent = [
                'year'=>$year,
                'capital_share_p'=>$ICP,
                'patronage_refund_p'=>$PR,
                'capital_share_rate'=>$m['ISCRate'],
                'patronage_refund_rate'=>$m['PRRate'],
                'remarks'=>$remarks
            ];

            $mem = $m['MemberFinalData'];
            $allocationContent = array();

            foreach($mem as $bgy=>$contents){
                foreach($contents as $c){
                    $allocationContent[] = [
                        'id_patronage_capital_allocation'=>0,
                        'id_member'=>$c['id_member'],
                        'capital_share'=>$c['CBUTotal'],
                        'ave_monthly_cbu'=>$c['AverageCBU'],
                        'interest_capital_share'=>$c['ICS'],
                        'loan_interest'=>$c['InterestTotal'],
                        'patronage_refund'=>$c['PR'],
                        'total'=>$c['TotalPayables'],
                         $def_key=>$c['TotalPayables']
                    ];
                }
            }

            if($opcode == 0){
                $id_patronage_capital_allocation = DB::table('patronage_capital_allocation')
                ->insertGetId($allocationParent);
            }

            foreach($allocationContent as $i=>$al){
                $allocationContent[$i]['id_patronage_capital_allocation'] = $id_patronage_capital_allocation;
            }

            DB::table('patronage_capital_allocation_details')
            ->insert($allocationContent);

            DB::commit();

            return response(['RESPONSE_CODE'=>'SUCCESS','id_patronage_capital_allocation' => $id_patronage_capital_allocation, 'message'=>"Allocation successfully save!"]);


        }catch(QueryException $e){
            \Log::error($e->getMessage());
            DB::rollback();

            dd('HASDHASD');
        }catch(\Exception $e){
            \Log::error($e->getMessage());
            DB::rollback();
        }
        // ALLOCATION PARENT



        dd($allocationParent,$allocationContent);



        // dd($mem_allocation);
    }

    public function postAllocation(Request $request){
        if($request->ajax()){
            $allocations = $request->allocations ?? [];
            $id_patronage_capital_allocation = $request->id_patronage_capital_allocation;

            $idMembers = collect($allocations)->pluck('id_member')->toArray();

            try{
                // FETCH AMOUNTS FOR VALIDATION
                $allocationsValidation = DB::table('patronage_capital_allocation_details as pca')
                                        ->select('id_member','interest_capital_share','patronage_refund','total')
                                        ->where('id_patronage_capital_allocation',$id_patronage_capital_allocation)
                                        ->whereIn('id_member',$idMembers)
                                        ->get()->keyBy('id_member');

                $invalidAmount = [];

                $allocationOBJ = [];
                foreach($allocations as $al){
                    $cash = ($al['cash'] == "") ? 0 : $al['cash'];

                    $cbu = ($al['cbu'] == "") ? 0 : $al['cbu'];
                    $totalAlloc = (float)$cash + (float)$cbu;
                    $validationAmount = (float)$allocationsValidation[$al['id_member']]->total;

                    if($totalAlloc !== $validationAmount){
                        $invalidAmount[]=$al['id_member'];
                    }

                    $allocationOBJ[$al['id_member']]= [
                        'w_cash'=>$cash,
                        'w_cbu'=>$cbu
                    ];
                }

                if(count($invalidAmount) > 0){
                    $data['RESPONSE_CODE'] = "ERROR";
                    $data['message'] = "Invalid Amount";
                    $data['invalidRows'] = $invalidAmount;

                    return response($data);
                }

                foreach($allocationOBJ as $id_member=>$al){
                    DB::table('patronage_capital_allocation_details')
                    ->where('id_patronage_capital_allocation',$id_patronage_capital_allocation)
                    ->where('id_member',$id_member)
                    ->update($al);
                }

                DB::commit();

                return response(['RESPONSE_CODE'=>"SUCCESS","message"=>"Allocation Successfully Posted"]);



            }catch(QueryException $e){
                \Log::error($e->getMessage());
                DB::rollback();

                dd('HASDHASD');
            }catch(\Exception $e){
                \Log::error($e->getMessage());
                DB::rollback();
            }



        }
    }

    public function allocationPage($id_patronage_capital_allocation,Request $request){
        $data['sidebar']="sidebar-collapse";
        $data['details'] = DB::table('patronage_capital_allocation')
                            ->where('id_patronage_capital_allocation',$id_patronage_capital_allocation)
                            ->first();

        $groupings = $data['Groups'] = DB::table('patronage_capital_allocation_details as pra')
                        ->select(DB::raw("if(m.id_baranggay_lgu is null,'Regular',concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name)) as groupings,if(m.id_baranggay_lgu is null,0,m.id_baranggay_lgu) as group_ref"))
                        ->leftJoin('member as m','m.id_member','pra.id_member')
                        ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','m.id_baranggay_lgu')
                        ->orderBy(DB::raw("if(m.id_baranggay_lgu is null,3,bl.type) "))
                        ->orderBy(DB::raw("concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name) "))
                        ->groupBy(DB::raw("if(m.id_baranggay_lgu is null,'Regular',concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name))"))
                        ->get();

        $defGroup = $groupings[0]->group_ref;

        $data['AllocationTable'] = $this->FetchGroupAllocation($id_patronage_capital_allocation,$defGroup);


        return view('patronage_refunds.allocation-form',$data);


    }

    public function FetchGroupAllocation($id_patronage_capital_allocation,$id_brgy_lgu){
        $output = DB::table('patronage_capital_allocation_details as pc')
                    ->select('m.id_member',DB::raw("FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as Name,
                    pc.capital_share,pc.ave_monthly_cbu,pc.interest_capital_share,pc.loan_interest,pc.patronage_refund,pc.total,pc.w_cash,pc.w_cbu,if(pc.w_cash + pc.w_cbu =0,pc.total,pc.w_cash) as def_val"))
                   ->leftJoin('member as m','m.id_member','pc.id_member')
                   ->where('pc.id_patronage_capital_allocation',$id_patronage_capital_allocation)
                   ->where(function($query) use($id_brgy_lgu){
                        if($id_brgy_lgu > 0){
                            $query->where('m.id_baranggay_lgu',$id_brgy_lgu);
                        }else{
                             $query->whereNull('m.id_baranggay_lgu');
                        }
                   })
                   ->orderBy('Name')
                   ->get();
        return $output;
    }


    public function fetchMemberAllocation(Request $request){
        $id_patronage_capital_allocation = $request->id_patronage_capital_allocation;
        $type = $request->type;

        $data['allocations'] = $this->FetchGroupAllocation($id_patronage_capital_allocation,$type);

        return response($data);
    }


}
