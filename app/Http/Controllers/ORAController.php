<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\WebHelper;
use App\CredentialModel;
use Excel;
use PDF;
use App\Exports\ORAExport;


class ORAController extends Controller
{
    public function index(Request $request){

        // dd($data);
        $data = $this->parseData($request);



        return view('ora.index',$data);
    }

    public function parseData($request){
        $end = MySession::current_date();
        $start = date('Y-m-01', strtotime($end));
        // $end = MySession::current_date();

        $data['exportMode'] = 1;
        $start = $data['selected_start'] = $request->start ?? $start;
        $end = $data['selected_end'] = $request->end ?? $end;
        $data['date'] =  WebHelper::ReportDateFormatter($start,$end);
        $data['date2'] = date("m_d_Y", strtotime($start))." - ".date("m_d_Y", strtotime($end));

        $ors = collect($this->getORS($start,$end))->pluck('or_number')->toArray();


        $output = array();

        $groupedOR = $this->groupOrByStub($ors);
        $g = new GroupArrayController();
        foreach($groupedOR as $gr){
            $temp = array();
            $series = range($gr['start'],$gr['end']);
            $stubData = $this->QueryStub($series,$data['selected_start'],$data['selected_end']);

            $groupedStub = $g->array_group_by($stubData,['or_number']);

            $temp['series'] = $gr;

            $temp['content'] = array();

            foreach($series as $ser){
                $ob = (object)[
                    'date'=>'',
                    'or_number'=>$ser,
                    'description'=>'Missing',
                    'total_amount'=> 0,
                    'status' => '',
                    'missing'=>1,
                    'within_date' =>0,
                    'reference'=>''
                ];
                $temp['content'][$ser] = $groupedStub[$ser] ?? [$ob];
            }
            array_push($output,$temp);

        }

        $data['or_output'] = $output;
        $data['head_title'] = "ORA";

        return $data;
    }

    public function export($type,Request $request){
        $data = $this->parseData($request);


        if($type == "pdf"){
            $data['exportMode'] = 2;
            $html =  view('ora.pdf',$data);

            // return $html;
            $pdf = PDF::loadHtml($html);
            $pdf->setOption("encoding","UTF-8");
            $pdf->setOption('margin-bottom', '5mm');
            $pdf->setOption('margin-top', '7mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('header-left', 'Page [page] of [toPage]');

            $pdf->setOption('header-font-size', 8);
            $pdf->setOption('header-font-name', 'Calibri');
            // $pdf->setOrientation('landscape');

            return $pdf->stream();
        }elseif($type == "excel"){
            $data['exportMode'] = 3;
            $d = $data['date2'];
            $data['date'] = $data['date2'];



            return Excel::download(new ORAExport($data), "{$data['head_title']} {$d}.xlsx");
        }

    }


    public function getORS($start_date,$end_date){
        $p = [
            'start1' => $start_date,
            'end1' => $end_date,
            'start2' => $start_date,
            'end2' => $end_date,
        ];

        $ors = DB::select("SELECT * FROM (
        SELECT r.date,r.or_number,RepaymentDescription(r.payment_for,r.id_repayment) as description,r.total_amount,if(r.status = 10, 'Cancelled','') as status, 1 as `source`
        FROM repayment as r
        WHERE r.date >= :start1 AND r.date <= :end1
        UNION ALL
        SELECT cr.date_received,cr.or_no,concat('CR# ',cr.id_cash_receipt) as description,total_payment,if(cr.status = 10, 'Cancelled','') as status, 2 as `source`
        FROM cash_receipt as cr
        WHERE cr.date_received >= :start2 AND cr.date_received <= :end2 AND type = 1) as ors
        ORDER BY CAST(or_number AS UNSIGNED);",$p);

        return $ors;
    }

    function groupOrByStub(array $orNumbers, int $stubSize = 50): array  {

        if (empty($orNumbers)) {
            return [];
        }

        // Ensure integers and remove duplicates
        $orNumbers = array_unique(array_map('intval', $orNumbers));
        sort($orNumbers);

        $groups = [];

        foreach ($orNumbers as $number) {

            // Compute stub start
            $start = floor(($number - 1) / $stubSize) * $stubSize + 1;
            $end   = $start + $stubSize - 1;

            $key = $start . '-' . $end;

            $groups[$key] = [
                'start' => (int)$start,
                'end'   => (int)$end
            ];
        }

        return array_values($groups);
    }

    public function QueryStub($series,$start,$end){

        $repaymentQuery = DB::table('repayment as r')
        ->select([
            DB::raw("DATE_FORMAT(r.date,'%m/%d/%Y') as date"),
            'r.or_number',
            DB::raw("concat(RepaymentDescription(r.payment_for, r.id_repayment)) as description"),
            DB::raw("concat('Repayment # <a href=\"/repayment-bulk/view/',r.id_repayment,'\" target=\"_blank\">',r.id_repayment,'</a>') as reference"),
            'r.total_amount',
            DB::raw("IF(r.status = 10, 'Cancelled', '') as status"),
            DB::raw('1 as source, 0 as missing'),
            DB::raw("if(r.date >= '$start'  AND r.date <= '$end',1,0) as within_date")
        ])
        ->whereIn('r.or_number', $series);

        $cashReceiptQuery = DB::table('cash_receipt as cr')
            ->select([
                DB::raw("DATE_FORMAT(cr.date_received,'%m/%d/%Y') as date"),
                'cr.or_no as or_number',
                DB::raw("concat(if(cr.payee_type=1,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix),payee_text)) as payee"),
                DB::raw("concat('CR# <a href=\"/cash_receipt/view/',cr.id_cash_receipt,'\" target=\"_blank\">',cr.id_cash_receipt,'</a>') as reference"),
                // DB::raw("CONCAT('CR# ', cr.id_cash_receipt) as description"),
                'cr.total_payment as total_amount',
                DB::raw("IF(cr.status = 10, 'Cancelled', '') as status"),
                DB::raw('2 as source,0 as missing'),
                DB::raw("if(cr.date_received >= '$start' AND cr.date_received <= '$end',1,0) as within_date")
            ])
            ->leftJoin('member as m','m.id_member','cr.id_member')
            ->where('cr.type',1)
            ->whereIn('cr.or_no', $series);


        $ors = $repaymentQuery
            ->unionAll($cashReceiptQuery);

        $result = DB::query()
            ->fromSub($ors, 'ors')
            ->orderByRaw('CAST(or_number AS UNSIGNED)')
            ->get();

        return $result;
    }
}
