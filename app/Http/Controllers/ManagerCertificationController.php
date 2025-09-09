<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MySession;
use DB;
use App\CredentialModel;
use Dompdf\Dompdf;
use PDF;
use App\WebHelper;

class ManagerCertificationController extends Controller
{   
    private $cdv_cat = "1,2";
    private $cdv_cat_ar = [1,2];
    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }   

        $data['lists'] = DB::table('manager_certification as mc')
                         ->select(DB::raw("id_manager_certification,@st:=DATE_FORMAT(date_start,'%m/%d/%Y') as date_start,@end:=DATE_FORMAT(date_end,'%m/%d/%Y') as date_end,if(date_start=date_end,@st,concat(@st,' - ',@end)) as date_form,total,DATE_FORMAT(mc.date_created,'%m/%d/%Y') as date_created"))
                         ->orderBy('mc.id_manager_certification','DESC')
                         ->get();



        return view('manager_certification.index',$data);
    }
    public function create(Request $request){
        $dt = MySession::current_date();

        $start = date('Y-m-d', strtotime('-1 Month'));
        $end = MySession::current_date();



        $data['start_date'] = $request->start_date ?? $start;
        $data['end_date'] = $request->end_date ?? $end;

        $data['allow_post'] = true;
        $data['opcode'] = 0;


        $data['cdvs'] = $this->GenerateCDV($data['start_date'],$data['end_date'],0);

        // dd($data);
        return view('manager_certification.form',$data);
    }

    public function edit(Request $request,$id_manager_certification){
        $data['details'] = DB::table('manager_certification as mc')
                         ->select(DB::raw("*,YEAR(date_start) as mc_year,concat(MONTH(date_start),' - ',if(mc.id_manager_certification > 100,mc.id_manager_certification,LPAD(mc.id_manager_certification, 3, 0))) as formatted_reference"))
                         ->where('id_manager_certification',$id_manager_certification)
                         ->first();

        $data['start_date'] = $request->start_date ?? $data['details']->date_start;
        $data['end_date'] = $request->end_date ?? $data['details']->date_end;
        $data['allow_post'] = true;
        $data['opcode'] = 1;
        $data['cdvs'] = $this->GenerateCDV($data['start_date'],$data['end_date'],$id_manager_certification);

        return view('manager_certification.form',$data);
        dd($data);
    }

    public function view($id_manager_certification){
        $data=$this->parseViewData($id_manager_certification);
        $data['allow_post'] = false;

        return view('manager_certification.form',$data);
    }
    public function print($id_manager_certification){
        $data=$this->parseViewData($id_manager_certification);
        $data['file_name'] = $data['details']->formatted_reference;
        $html =  view('manager_certification.print',$data);

        // return $html;
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '0.33in');
        $pdf->setOption('margin-top', '0.33in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.42in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
    
        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');


       
        // $pdf->setOrientation('landscape');

        return $pdf->stream("Manager Certification {$data['file_name']}.pdf",array('Attachment'=>1));


    }

    public function parseViewData($id_manager_certification){
        $out['details'] =  $data['lists'] = DB::table('manager_certification as mc')
                          ->select(DB::raw("id_manager_certification,@st:=DATE_FORMAT(date_start,'%m/%d/%Y') as date_start,@end:=DATE_FORMAT(date_end,'%m/%d/%Y') as date_end,if(date_start=date_end,@st,concat(@st,' - ',@end)) as date_form,total,DATE_FORMAT(mc.date_created,'%m/%d/%Y') as date_created,YEAR(date_start) as mc_year,concat(MONTH(date_start),' - ',if(mc.id_manager_certification > 100,mc.id_manager_certification,LPAD(mc.id_manager_certification, 3, 0))) as formatted_reference,date_start as ds,date_end as de"))
                         ->where('id_manager_certification',$id_manager_certification)
                         ->first();

        $out['cdvs']= DB::select("SELECT cd.id_cash_disbursement,DATE_FORMAT(cd.date,'%m/%d/%Y') as date,cd.payee,cd.total as amount,cd.description  as purpose,0 as checked,cd.date as adate
                        FROM cash_disbursement as cd 
                        LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
                        LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
                        WHERE cd.status <> 10 AND cd.id_manager_certification =?
                        GROUP BY cd.id_cash_disbursement
                        ORDER BY cd.date,cd.id_cash_disbursement
                        ",[$id_manager_certification]);   

        $out['formatted_date'] = WebHelper::ReportDateFormatter($out['details']->ds,$out['details']->de);    

        return $out;
    }

    public function GenerateCDV($date_start,$date_end,$id_manager_certification){
        $param = [
            'start_date'=>$date_start,
            'end_date'=>$date_end,
        ];

        // $add_q = ($id_manager_certification == 0)?" AND loan.id_manager_certification is null":"";


        $union_q = "";
        if($id_manager_certification > 0){
            $param['id_mng'] = $id_manager_certification;
            $param['start_date2'] = $date_start;
            $param['end_date2'] = $date_end;

            // $union_q = "UNION ALL SELECT loan.id_loan,DATE_FORMAT(date_released,'%m/%d/%Y') as date,loan.id_cash_disbursement as cdv,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as payee,
            // loan.total_loan_proceeds as amount,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as purpose,1 as checked,loan.date_released,loan.id_cash_disbursement,loan.loan_token
            // FROM loan
            // LEFT JOIN member as m on m.id_member = loan.id_member
            // LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
            // WHERE loan.date_released >= :start_date2 and loan.date_released <= :end_date2 and loan.id_manager_certification = :id_mng ";
            $union_q = " UNION ALL SELECT cd.id_cash_disbursement,DATE_FORMAT(cd.date,'%m/%d/%Y') as date,cd.payee,cd.total as amount,cd.description  as purpose,1 as checked,cd.date as adate
                        FROM cash_disbursement as cd 
                        LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
                        LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
                        WHERE cd.status <> 10 AND cd.date >= :start_date2 AND cd.date <= :end_date2 AND cd.id_manager_certification =:id_mng
                        GROUP BY cd.id_cash_disbursement";
        }

        // dd($param);
        // $loans = DB::select("
        // SELECT * FROM (SELECT loan.id_loan,DATE_FORMAT(date_released,'%m/%d/%Y') as date,loan.id_cash_disbursement as cdv,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as payee,
        // loan.total_loan_proceeds as amount,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as purpose,0 as checked,loan.date_released,loan.id_cash_disbursement,loan.loan_token
        // FROM loan
        // LEFT JOIN member as m on m.id_member = loan.id_member
        // LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        // WHERE loan.date_released >= :start_date and loan.date_released <= :end_date and loan.status not in (4,5) AND loan.id_manager_certification is null $union_q ) as k
        // order by date_released ASC,id_cash_disbursement ASC",$param);
        $cdv_cat = $this->cdv_cat;
        $cdv = DB::select("
                        SELECT * FROM (
                        SELECT cd.id_cash_disbursement,DATE_FORMAT(cd.date,'%m/%d/%Y') as date,cd.payee,cd.total as amount,cd.description  as purpose,0 as checked,cd.date as adate
                        FROM cash_disbursement as cd 
                        LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
                        LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
                        WHERE cd.status <> 10 AND cd.date >= :start_date AND cd.date <= :end_date AND cd.id_manager_certification is null AND ca.id_chart_account_category in ($cdv_cat)
                        GROUP BY cd.id_cash_disbursement
                        $union_q ) as k ORDER BY adate,id_cash_disbursement;",$param);


        return $cdv;
    }

    public function post(Request  $request){
        $opcode = $request->opcode;
        $cdv = $request->cdv ?? [];
        $id_manager_certification = $request->id_manager_certification ?? 0;


        if(count($cdv) == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please select at least 1 CDV";

            return response($data);
        }



        $dates = DB::table('cash_disbursement')
                 ->select(DB::raw("MIN(date) as date_start,MAX(date) as date_end,SUM(total) as total"))
                 ->whereIn('id_cash_disbursement',$cdv)
                 ->first();

     
        $MCObj = [
            'date_start' => $dates->date_start,
            'date_end' => $dates->date_end,
            'total'=>$dates->total,
            'remarks'=>$request->remarks
        ];


        DB::beginTransaction();
        try{
            if($opcode == 0){
                // Add
                $MCObj['id_user'] = MySession::myId();

                DB::table('manager_certification')
                ->insert($MCObj);
                $id_manager_certification = DB::table('manager_certification')->max('id_manager_certification');
            }else{
                DB::table('manager_certification')
                ->where('id_manager_certification',$id_manager_certification)
                ->update($MCObj);

                DB::table('cash_disbursement')
                ->where('id_manager_certification',$id_manager_certification)
                ->update(['id_manager_certification'=>null]);
            }

            DB::table('cash_disbursement')
            ->whereIn('id_cash_disbursement',$cdv)
            ->whereNull('id_manager_certification')
            ->update(['id_manager_certification'=>$id_manager_certification]);
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "PLEASE CONTACT SYSTEM ADMIN";
            return response($data);
        }




        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['ID_MANAGER_CERTIFICATION'] = $id_manager_certification;

        return response($data);

        


    }


}
