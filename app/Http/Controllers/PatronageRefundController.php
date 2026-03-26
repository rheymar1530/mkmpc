<?php

namespace App\Http\Controllers;

use App\MySession;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Illuminate\Database\QueryException;
use App\CredentialModel;
use PDF;
use App\JVModel;
use App\Exports\PatronageRefundExport;
use Excel;

class PatronageRefundController extends Controller
{

    private $DefAllocation = [
        ["description" => "Cash" ,"key" =>"w_cash"],
        ["description" => "CBU" ,"key" =>"w_cbu"]
    ];
    function getRetentionRate(float $amount): float
    {
        $ranges = config('patronage-allocation-net.ranges');


        foreach ($ranges as $range) {
            if (
                $amount >= $range['min'] &&
                (is_null($range['max']) || $amount <= $range['max'])
            ) {
                return $range['rate']/100;
            }
        }

        return 0;
    }
    public function index(){
        // $this->recurssion();
        // return;

        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        $data['head_title'] = "Patronage Allocation";

        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['patronage_refunds'] = DB::table('patronage_capital_allocation as pca')
                               ->select(DB::raw("pca.id_patronage_capital_allocation,pca.year,capital_share_p,patronage_refund_p,pca.remarks,
                                        CASE WHEN pca.status = 0 THEN 'For Release'
                                        WHEN pca.status = 1 THEN 'Approved'
                                        WHEN pca.status = 2 THEN 'Confirmed'
                                        ELSE 'Cancelled' END as status_description
                                        ,DATE_FORMAT(pca.date_created,'%m/%d/%Y') as date_created,pca.status as status_code"))
                               ->orDerby('pca.id_patronage_capital_allocation','DESC')
                               ->get();

        $data['current_date'] = MySession::current_date();

        return view('patronage_refunds.index',$data);

        return $data;
    }

    public function create(Request $request){
        $export = $request->export ?? 0;

        $data['sidebar']="sidebar-collapse";

        $data['sel_year'] = $year = $request->year ?? MySession::current_year();
        $data['icsp'] = $ICP = $this->cleanNumber($request->icsp ?? $this->parseInterestCapitalSharePayables($year,0));
        $data['prp'] = $PR = $this->cleanNumber($request->prp ?? $this->parsePatronageRefundPayables($year,0));

        $data['DefAllocation'] = $this->DefAllocation;

        $mem_allocation = $this->CompileAllocationData($year,$ICP,$PR);
        $data = [...$data,...$mem_allocation ];

        $totalKeys = ['CBUTotal','AverageCBU','ICS','InterestTotal','PR','TotalPayables','Net','CBU_Retention'];
        $t = [];
        foreach($data['MemberFinalData'] as $k){
            foreach($totalKeys as $key){
                $am = collect($k)->sum($key);
                $t[$key] = ($t[$key] ?? 0) + $am;
            }
        }

        $data['totals'] = $t;

        // dd($data);
        $d = $data['file_name'] = "ISC AND PR YEAR {$data['sel_year']}";
        if($export == 1){

            $data['isExcel'] = false;


            $html = view('patronage_refunds.export-create',$data);

            $pdf = PDF::loadHtml($html);
            $pdf->setOption("encoding","UTF-8");
            $pdf->setOption('margin-bottom', '0.33in');
            $pdf->setOption('margin-top', '0.33in');
            $pdf->setOption('margin-right', '0.33in');
            $pdf->setOption('margin-left', '0.42in');
            $pdf->setOption('header-left', 'Page [page] of [toPage]');
            $pdf->setOrientation('landscape');
            // $pdf->setOption('header-right', 'No.: '.$data['details']->month_year.'-'.$data['details']->id_repayment_statement);

            $pdf->setOption('header-font-size', 8);
            $pdf->setOption('header-font-name', 'Calibri');

            return $pdf->stream("{$data['file_name']}.pdf",array('Attachment'=>1));
        }elseif($export == 2){
            $data['isExcel'] = true;
             return Excel::download(new PatronageRefundExport($data), "{$d}.xlsx");
        }

        return view('patronage_refunds.create',$data);

    }

    public function CompileAllocationData(int $year,$ICP,$PR){

        // transactions months
        $transactionDates = $this->getMonthlyDateRanges($year);

        $MonthCount = count($transactionDates);


        $output = array();

        $StartDate = $transactionDates[0]['start'];
        $EndDate = $transactionDates[$MonthCount-1]['end'];


        $tempArray = array();
        $monthAr = [];
        foreach($transactionDates as $tr){
            // array_push($tempArray,"SUM(CASE WHEN transaction_date >= '{$tr['start']}' AND transaction_date <= '{$tr['end']}' THEN amount else 0 END) as '{$tr['month']}'");
            array_push($tempArray,"SUM(CASE WHEN transaction_date <= '{$tr['end']}' THEN amount else 0 END) as '{$tr['month']}'");
            $monthAr[]=$tr['month'];
        }

        $monthSum = "`".implode("`+`",$monthAr)."`";





        $TransactionDateString = implode(', ',$tempArray)."";


        $g = new GroupArrayController();

        $cbuData = $this->MembersCBU($StartDate,$EndDate,$TransactionDateString,$monthSum);
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
        | STEP 5 — DISTRIBUTE PR ROUNDING (NO REORDER)
        |--------------------------------------------------------------------------
        */
        if ($memberCount > 0 && $PRdiffCents != 0) {

            // Build index list
            $indexes = array_keys($AllMembers);

            // Sort indexes based on remainder (NOT the actual array)
            usort($indexes, function ($i, $j) use ($AllMembers, $PRdiffCents) {

                $ra = $AllMembers[$i]['PR_raw'] - floor($AllMembers[$i]['PR_raw'] * 100) / 100;
                $rb = $AllMembers[$j]['PR_raw'] - floor($AllMembers[$j]['PR_raw'] * 100) / 100;

                return ($PRdiffCents > 0)
                    ? $rb <=> $ra   // highest remainder first
                    : $ra <=> $rb;  // lowest remainder first
            });

            $remaining = abs($PRdiffCents);

            foreach ($indexes as $idx) {

                if ($remaining <= 0) break;

                if ($PRdiffCents > 0) {
                    $AllMembers[$idx]['PR'] += 0.01;
                    $remaining--;

                } else {
                    if ($AllMembers[$idx]['PR'] >= 0.01) {
                        $AllMembers[$idx]['PR'] -= 0.01;
                        $remaining--;
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 6 — REBUILD GROUP STRUCTURE
        |--------------------------------------------------------------------------
        */
        $output['ave_CBU'] = 0;
        $output['total_Interest'] = 0;
        foreach ($AllMembers as $m) {

            $group = $m['group'];

            $m['TotalPayables'] = $m['ICS'] + $m['PR'];

            $rate = $this->getRetentionRate($m['TotalPayables']);

            $net = round($m['TotalPayables']*$rate,2);

            $cbu_retention = $m['TotalPayables']-$net;


            $MemberFinalData[$group][] = [
                'id_member' => $m['id_member'],
                'member' => $m['member'],
                'InterestTotal' => $m['InterestTotal'],
                'CBUTotal' => $m['CBUTotal'],
                'Total' => $m['Total'],
                'AverageCBU' => $m['AverageCBU'],
                'ICS' => $m['ICS'],
                'PR' => $m['PR'],
                'TotalPayables' => $m['TotalPayables'],
                'NetRate' => $rate*100,
                'Net'=>$net,
                'CBU_Retention'=>$cbu_retention
            ];

            $output['ave_CBU'] += $m['AverageCBU'];
            $output['total_Interest'] += $m['InterestTotal'];
        }

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

    public function MembersCBU(string $StartDate,string $EndDate,string $TransactionDateString,string $monthSum){
        $cbuCon = new CBUController();
        $CBUSQL = $cbuCon->CBUMonthlyQueryBase(1);

        $sql =" with cbu as (
                SELECT FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as Name,m.id_member,$TransactionDateString FROM (
            $CBUSQL
        ) as cbu
        LEFT JOIN member as m on m.id_member = cbu.id_member

        GROUP BY cbu.id_member
        HAVING SUM(amount) > 0
        ORDER BY Name
        ) SELECT cbu.*,$monthSum as Total FROM cbu;";
        // WHERE cbu.transaction_date >= ?
        $param = array_fill(0,6,$EndDate);

        //,$StartDate
        $cbuData = DB::select($sql,[...$param]);

        // $cbuData = DB::select($sql,[$StartDate,$EndDate,$StartDate,$EndDate,$StartDate,$EndDate,$StartDate,$EndDate,$StartDate,$EndDate,$StartDate,$EndDate,$StartDate]);

        return $cbuData;
    }


    public function parsePatronageRefundPayables($year,$control_number){
        return $this->parseChart($year,$control_number,20);
        return 169497.53;
    }

    public function parseInterestCapitalSharePayables($year,$control_number){
        // DB::table('')
        return $this->parseChart($year,$control_number,19);
        return 254246.29;
    }
    public function parseChart($year,$control_number,$id_chart_account){
        $param = array_fill(0,4,$id_chart_account);


        return DB::select("SELECT SUM(amount) as amount FROM (
        SELECT ifnull(SUM(credit-debit),0) as amount
        FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        WHERE jv.date <= '$year-12-31' AND jv.status <> 10 AND jvd.id_chart_account = ?
        UNION ALL
        SELECT ifnull(SUM(credit-debit),0)
        FROM cash_disbursement as cv
        LEFT JOIN cash_disbursement_details as cvd on cvd.id_cash_disbursement = cv.id_cash_disbursement
        WHERE cv.date <= '$year-12-31' AND cv.status <> 10 AND cvd.id_chart_account = ?
        UNION ALL
        SELECT ifnull(SUM(credit-debit),0)
        FROM cash_receipt_voucher as crb
        LEFT JOIN cash_receipt_voucher_details as crbd on crbd.id_cash_receipt_voucher = crb.id_cash_receipt_voucher
        WHERE crb.date <= '$year-12-31' AND crb.status <> 10 AND crbd.id_chart_account = ?
        UNION ALL
        SELECT ifnull(SUM(credit-debit),0)
        FROM chart_beginning
        WHERE status <> 10 and id_chart_account = ?) as k;",$param)[0]->amount;
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
                        'w_cbu'=>$c['CBU_Retention'],
                        'w_cash'=>$c['Net']
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

            // POST ALLOCATION PER BARANGGAY
            DB::select("INSERT INTO patronage_capital_allocation_group (id_patronage_capital_allocation,id_baranggay_lgu,isc,pr)
            SELECT pcad.id_patronage_capital_allocation ,ifnull(m.id_baranggay_lgu,0) AS id_baranggay_lgu,SUM(interest_capital_share) as ics,SUM(patronage_refund) as pr
            FROM patronage_capital_allocation_details as pcad
            LEFT JOIN member as m on m.id_member = pcad.id_member
            LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = m.id_baranggay_lgu
            WHERE pcad.id_patronage_capital_allocation = ?
            GROUP BY ifnull(m.id_baranggay_lgu,0)
            ORDER BY if(m.id_baranggay_lgu is null,3,bl.type),concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name);",[$id_patronage_capital_allocation]);

            DB::select("UPDATE patronage_capital_allocation_details as pcad
            LEFT JOIN member as m on m.id_member = pcad.id_member
            SET pcad.id_baranggay_lgu = ifnull(m.id_baranggay_lgu,0)
            WHERE pcad.id_patronage_capital_allocation = ?",[$id_patronage_capital_allocation]);

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
            $id_baranggay_lgu = $request->id_baranggay_lgu;
            $id_patronage_capital_allocation = $request->id_patronage_capital_allocation;
            $release_remarks = $request->release_remarks ?? '';
            $date_released = $request->date_released;

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
                    $totalAlloc = ROUND((float)$cash + (float)$cbu,2);
                    $validationAmount = ROUND((float)$allocationsValidation[$al['id_member']]->total,2);

                    if($totalAlloc !== $validationAmount){
                        $invalidAmount[]=$al['id_member'];

                        dd($totalAlloc,$validationAmount);
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

                DB::table('patronage_capital_allocation_group')
                ->where('id_patronage_capital_allocation',$id_patronage_capital_allocation)
                ->where('id_baranggay_lgu',$id_baranggay_lgu)
                ->update([
                    'date_released'=> $date_released,
                    'release_remarks'=>$release_remarks,
                    'status'=>1
                ]);

                $t = DB::table('patronage_capital_allocation_details')
                ->select('id_member')
                ->where('id_patronage_capital_allocation',$id_patronage_capital_allocation)
                ->where('id_baranggay_lgu',$id_baranggay_lgu)
                ->get();


                foreach($t as $d){
                    JVModel::PRAllocation($id_patronage_capital_allocation,$d->id_member);
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


        $groupings = $data['Groups'] = DB::table('patronage_capital_allocation_group as pcg')
                                        ->select(DB::raw("if(pcg.id_baranggay_lgu=0,'Regular',concat(if(bl.type=1,'Brgy - ','LGU - '),bl.name)) as groupings,
                                        pcg.id_baranggay_lgu as group_ref"))
                                        ->where('id_patronage_capital_allocation',$id_patronage_capital_allocation)
                                        ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','pcg.id_baranggay_lgu')
                                        ->orderBy(DB::raw("if(pcg.id_baranggay_lgu=0,3,bl.type)"))
                                        ->orderBy(DB::raw("bl.name"))
                                        ->get();

        // $groupings = $data['Groups'] = DB::table('patronage_capital_allocation_details as pra')
        //                 ->select(DB::raw("if(m.id_baranggay_lgu is null,'Regular',concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name)) as groupings,if(m.id_baranggay_lgu is null,0,m.id_baranggay_lgu) as group_ref"))
        //                 // ->leftJoin('member as m','m.id_member','pra.id_member')
        //                 ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','pra.id_baranggay_lgu')
        //                 ->where('id_patronage_capital_allocation',$id_patronage_capital_allocation)
        //                 ->orderBy(DB::raw("if(m.id_baranggay_lgu is null,3,bl.type) "))
        //                 ->orderBy(DB::raw("concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name) "))
        //                 ->groupBy(DB::raw("if(m.id_baranggay_lgu is null,'Regular',concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name))"))
        //                 ->get();
        $defGroup = $groupings[0]->group_ref;

        $data['AllocationTable'] = $this->FetchGroupAllocation($id_patronage_capital_allocation,$defGroup,false);
        $data['GroupDetails'] = $this->FetchGroupDetails($id_patronage_capital_allocation,$defGroup);

        return view('patronage_refunds.allocation-form',$data);
    }

    public function FetchGroupAllocation($id_patronage_capital_allocation,$id_brgy_lgu,$all){
        $output = DB::table('patronage_capital_allocation_details as pc')
                    ->select('m.id_member',DB::raw("FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as Name,if(m.id_baranggay_lgu is null,'Regular',concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name)) as groupings,if(m.id_baranggay_lgu is null,0,m.id_baranggay_lgu) as group_ref,
                    pc.capital_share,pc.ave_monthly_cbu,pc.interest_capital_share,pc.loan_interest,pc.patronage_refund,pc.total,pc.w_cash,pc.w_cbu,pc.id_journal_voucher"))
                   ->leftJoin('member as m','m.id_member','pc.id_member')
                   ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','pc.id_baranggay_lgu')
                   ->where('pc.id_patronage_capital_allocation',$id_patronage_capital_allocation);
        if(!$all){
            $output->where('pc.id_baranggay_lgu',$id_brgy_lgu);
        }else{
            $output->orderBy(DB::raw("if(pc.id_baranggay_lgu = 0,3,bl.type) "))
                    ->orderBy("bl.name");
        }
        return $output->orderBy('Name')->get();
    }

    public function FetchGroupDetails($id_patronage_capital_allocation,$type){
        return DB::table('patronage_capital_allocation_group')
                          ->select(DB::raw("date_released,release_remarks,if(status = 0,'Draft','Released') as status,status as status_code"))
                          ->where('id_patronage_capital_allocation',$id_patronage_capital_allocation)
                          ->where('id_baranggay_lgu',$type)
                          ->first();
    }

    public function fetchMemberAllocation(Request $request){
        $id_patronage_capital_allocation = $request->id_patronage_capital_allocation;
        $type = $request->type;

        $data['allocations'] = $this->FetchGroupAllocation($id_patronage_capital_allocation,$type,false);
        $data['details'] = $this->FetchGroupDetails($id_patronage_capital_allocation,$type);

        return response($data);
    }

    public function allocation_summary(Request $request){
        $ID_PATRONAGE_CAPITAL_ALLOCATION  = $request->ID_PATRONAGE_CAPITAL_ALLOCATION;
        $data['details'] = DB::table('patronage_capital_allocation_details as pcad')
                           ->select(DB::raw("SUM(interest_capital_share) as ics,SUM(patronage_refund) as pr,SUM(w_cash) as wcash,SUM(w_cbu)  as wcbu"))
                           ->where('pcad.id_patronage_capital_allocation',$ID_PATRONAGE_CAPITAL_ALLOCATION)
                           ->first();

        return response($data);
    }

    public function PrintPatronageRefund($id_patronage_capital_allocation){
        $out = $this->FetchGroupAllocation($id_patronage_capital_allocation,0,true);

        $g = new GroupArrayController();

        $MemberLists = $g->array_group_by($out,['groupings']);

        $data['MemberFinalData'] = json_decode(json_encode($MemberLists),true);
        // dd($data);
        $data['file_name'] = "Patronage Refund Allocation";
        $data['details'] = DB::table('patronage_capital_allocation')->where('id_patronage_capital_allocation',$id_patronage_capital_allocation)->first();

        $html = view('patronage_refunds.print-allocation-all',$data);


        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '0.33in');
        $pdf->setOption('margin-top', '0.33in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.42in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
        $pdf->setOrientation('landscape');
        // $pdf->setOption('header-right', 'No.: '.$data['details']->month_year.'-'.$data['details']->id_repayment_statement);

        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        return $pdf->stream("{$data['file_name']}.pdf",array('Attachment'=>1));
    }

    public function GroupStatus(Request $request){
        $id_patronage_capital_allocation = $request->id_patronage_capital_allocation;

        $groups = DB::select("SELECT pcg.id_baranggay_lgu,CASE WHEN pcg.id_baranggay_lgu = 0 THEN 'Regular'
        ELSE concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name) END as groupings,
        CASE WHEN pcg.status = 0 THEN 'DRAFT'
        ELSE  'RELEASED' END as status_description,pcg.status as status_code
        FROM patronage_capital_allocation_group as pcg
        LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = pcg.id_baranggay_lgu
        WHERE pcg.id_patronage_capital_allocation = ?
        ORDER BY pcg.id_patronage_capital_allocation_group;",[$id_patronage_capital_allocation]);

        return response($groups);
    }


}
