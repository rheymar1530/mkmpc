<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\WebHelper;
use App\MySession;
use Illuminate\Support\Facades\Mail;
use App\Mail\OverdueNotificationMail;
use App\Jobs\NotificationMailJob;
use App\CredentialModel;

class DueNotificationController extends Controller
{
    public function show_overdue(){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }


        $date = MySession::current_date();
        $overdues = $this->parseOverDue($date);


        $data['dt'] = WebHelper::ConvertDatePeriod($date);



        $data['overdues'] = $overdues;
        // dd($data);
        return view('over-due',$data);

        dd($overdues);        
    }
    public function insert(){
        $date = MySession::current_date();
        $overdues = $this->parseOverDue($date);
        $dt = WebHelper::ConvertDatePeriod($date);


        foreach($overdues as $id_member=>$od){
            DB::table('overdue_email')
            ->insert(['id_member'=>$id_member,'payloads'=>json_encode($od),'month_due'=>$dt]);
        }

        dd($overdues);
    }
    public function PushMail(){


        $overdues = DB::table('overdue_email as om')
                    ->select(DB::raw("om.id_member,UPPER(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,om.payloads,m.email"))
                    ->leftJoin('member as m','m.id_member','om.id_member')
                    ->where('om.status',0)
                    ->limit(1)
                    ->get();


                    // dd($overdues);
        foreach($overdues as $od){
        // $data['member_name'] = $od->member_name;
        // $data['overdue'] = json_decode($od->payloads);
        // return view('emails.overdue',$data);
        // dd($data);

       //  $subject = "Loan Overdue";

       //  $emails_to = ['caluzarheymar@gmail.com'];


       // return $this->to($emails_to)
       //          ->view('emails.overdue',$data)
            Mail::send(new OverdueNotificationMail($od));

            // $failures = Mail::failures();


            // DB::table('overdue_email')
            // ->where('id_member',$od->id_member)
            // ->where('status',0)
            // ->update(['status'=>1]);

        }

        dd("SUCCESS");


        return view('emails.overdue',$data);
    }

    public function parseOverDue($date,$id_loans=array()){
        $query_date = WebHelper::ConvertDatePeriod($date);
        $start_date = date("Y-m-01",strtotime("$query_date"));

        $param = array_fill(0,3,$query_date);

        // UPPER(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,
        // $overdues = DB::select("SELECT *,principal_balance+interest_balance as total_due FROM (
        // SELECT loan.id_member,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_name',MAX(due_date) as as_of,getPrincipalBalanceAsOf(loan.id_loan,?) as principal_balance,getInterestBalanceAsOf(loan.id_loan,?) as interest_balance
        // FROM loan
        // LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
        // LEFT JOIN member as m on m.id_member = loan.id_member
        // LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        // WHERE lt.due_date <= ? AND loan.loan_status = 1
        // GROUP BY loan.id_loan) as loans
        // WHERE (loans.principal_balance+interest_balance) > 0
        // ORDER BY id_member,id_loan;",$param);

        $param = [
            'st1' => $start_date,
            'end1' => $query_date,
            'end2' => $query_date,
            'end3' => $query_date,
            'end4' => $query_date
        ];
        $add_where = "";
        if(count($id_loans) > 0){
            $k = array();
            foreach($id_loans as $ind=>$id_loan){
                $param['id'.$ind] = $id_loan;
                array_push($k,':id'.$ind);
            }
            $add_where = "AND loan.id_loan in (".implode(",",$k).")";
        }
        $overdues = DB::select("SELECT loans.*,@current_payment:=getLoanTotalPaymentMonth(loans.id_loan,:st1,:end1) as current_payment,principal_balance+interest_balance as total_due,ROUND(@current_payment+(principal_balance+interest_balance),2) as month_total_due,ifnull(oe.status,10) as notif_status FROM (
        SELECT loan.id_member,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_name',MAX(due_date) as as_of,getPrincipalBalanceAsOf(loan.id_loan,:end2) as principal_balance,getInterestBalanceAsOf(loan.id_loan,:end3) as interest_balance,UPPER(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,loan.loan_token,m.email
        FROM loan
        LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
        LEFT JOIN member as m on m.id_member = loan.id_member
        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        WHERE lt.due_date <= :end4 AND loan.loan_status = 1 $add_where
        GROUP BY loan.id_loan) as loans
        LEFT JOIN overdue_email as oe on oe.id_loan = loans.id_loan AND oe.month_due = '$query_date' AND oe.status < 9
        WHERE (loans.principal_balance+interest_balance) > 0 
        GROUP BY loans.id_loan
        ORDER BY member_name,loans.id_loan;",$param);

        $g = new GroupArrayController();

        $overdues = $g->array_group_by($overdues,['id_member']);

        return $overdues;

        dd($overdues);
    }

    public function PushNotif(Request $request){
        $id_loans = $request->id_loans ?? [];
        $date = MySession::current_date();
        $dt = WebHelper::ConvertDatePeriod($date);

        if(count($id_loans) ==  0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please select at least 1 loan";

            return response($data);
        }

        $emails = DB::table('loan')
                  ->select(DB::raw("UPPER(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,m.email"))
                  ->leftJoin('member as m','m.id_member','loan.id_member')
                  ->whereIn('loan.id_loan',$id_loans)
                  ->where(function($query){
                    $query->whereNull('email')->orWhere('email','');
                  })
                  ->groupBy('loan.id_member')
                  ->get();
                  
        if(count($emails) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] ="The following member has invalid email";
            $data['emails_invalid'] = $emails;

            return response($data);
        }

        $dues = $this->parseOverDue($date,$id_loans);
        $pushObj = array();
        foreach($dues as $id_member=>$due){
            foreach($due as $d){
                $t =[
                    'id_loan'=>$d->id_loan,
                    'id_member'=>$id_member,
                    'month_total_due'=>$d->month_total_due,
                    'current_payment'=>$d->current_payment,
                    'total_due'=>$d->total_due,
                    'status'=>0,
                    'month_due'=>$dt
                ];
                array_push($pushObj,$t);
            }
        }
        $batch_token = $this->generateRandomString(20);

        //pushing of notification
        foreach($pushObj as $po){
            $validator  = DB::table('overdue_email')->select('status','id_overdue_email')->where('id_loan',$po['id_loan'])->where('month_due',$dt)->whereNotIn('status',[9,10])->orderBy('id_overdue_email','DESC')->first();

            if(isset($validator) && $validator->status == 3){
                DB::table('overdue_email')
                ->where('id_overdue_email',$validator->id_overdue_email)
                ->update(['status'=>9]);
            }

            if(!isset($validator) || $validator->status == 3){
                $po['batch_token'] = $batch_token;
                DB::table('overdue_email')
                ->insert($po);
            }
        }

        dispatch(new NotificationMailJob($dt))->delay(now()->addMinutes(10));
        // $this->Dispatcher();

        $data['RESPONSE_CODE'] = "SUCCESS";

        return response($data);

        dd($id_loans);
    }

    public function Dispatcher(){
        $date = MySession::current_date();
        $dt = WebHelper::ConvertDatePeriod($date);

        dispatch(new NotificationMailJob($dt))->delay(now()->addMinutes(2));;


        return;

        // dd("success");


        $this->sendMail($dt);
        return;

        //parse data for sending notification
        $loans = DB::table('overdue_email as oe')
                 ->select(DB::raw("oe.id_member,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_name',month_total_due,current_payment,total_due,month_due,UPPER(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,loan.loan_token,m.email"))
                 ->leftJoin('loan','loan.id_loan','oe.id_loan')
                 ->leftJoin('member as m','m.id_member','oe.id_member')
                 ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
                 ->where('oe.month_due',$dt)
                 ->where('oe.status','<=',1)
                 ->orderBy('oe.id_overdue_email')
                 ->get();

  

        $g = new GroupArrayController();
        $loans = $g->array_group_by($loans,['id_member']);

        //Update all push notif status to 1 (pickedup by queue)
        DB::table('overdue_email')
        ->where('status',0)
        ->where('month_due',$dt)
        ->update(['status'=>1]);
        foreach($loans as $loan){
            // dispatch(new NotificationMailJob($loan,$dt));
            // ->delay(now()->addMinutes(2));
        }
    }

    public function sendMail($dt){

        $loans = DB::table('overdue_email as oe')
                 ->select(DB::raw("oe.id_member,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_name',month_total_due,current_payment,total_due,month_due,UPPER(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,loan.loan_token,m.email"))
                 ->leftJoin('loan','loan.id_loan','oe.id_loan')
                 ->leftJoin('member as m','m.id_member','oe.id_member')
                 ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
                 ->where('oe.month_due',$dt)
                 ->where('oe.status','<=',1)
                 ->orderBy('oe.id_overdue_email')
                 ->get();

        $g = new GroupArrayController();

        $loans = $g->array_group_by($loans,['id_member']);


        foreach($loans as $loan){
            $data['member_name'] = $loan[0]->member_name;
            $data['email'] = $loan[0]->email;
            $data['id_member'] = $loan[0]->id_member;  

            $data['loans'] = $loan;
            $data['id_loans'] = collect($loan)->pluck('id_loan');
            $data['month_date'] = $dt;

            DB::table('overdue_email')
            ->whereIn('id_loan',$data['id_loans'])
            ->where('status','<=',1)
            ->where('month_due',$data['month_date'])
            ->update(['status'=>2]);

            Mail::send(new OverdueNotificationMail($data));


        }
        dd($loans);






        $data['member_name'] = $loan[0]->member_name;
        $data['email'] = $loan[0]->email;
        $data['id_member'] = $loan[0]->id_member;

        //validation scripts ............
        $loan_valid = array();

        foreach($loan as $l){
            $c = DB::table('overdue_email')
                 ->where('id_loan',$l->id_loan)
                 ->where('month_due',$dt)
                 ->where('status',1)
                 ->count();
            if($c > 0){
                array_push($loan_valid,$l);
            }
        }

        // dd($loan);


        $data['loans'] = $loan_valid;

        dd($data['loans']);
        $data['month_date'] = $dt;
        $data['id_loans']= array();

        foreach($data['loans'] as $l){
            array_push($data['id_loans'],$l->id_loan);
        }

        // //status sending
        // DB::table('overdue_email')
        // ->whereIn('id_loan',$data['id_loans'])
        // ->where('status',1)
        // ->where('month_due',$data['month_date'])
        // ->update(['status'=>2]);

        // //validation script for sending mail
        // Mail::send(new OverdueNotificationMail($data));

    }
    public function cancel_notif(Request $request){
        if($request->ajax()){
            $id_loan = $request->id_loan;
            // $id_loan = 719;
            $date = MySession::current_date();
            $dt = WebHelper::ConvertDatePeriod($date);

            $validation = DB::table('overdue_email')
                          ->where('month_due',$dt)
                          ->where('id_loan',$id_loan)
                          ->where('status','<=',1)
                          ->count();

            if($validation == 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Invalid Request";

                return response($data);
            }

            DB::table('overdue_email')    
            ->where('month_due',$dt)
            ->where('id_loan',$id_loan)
            ->where('status','<=',1)
            ->update([
                'status'=>10
            ]);

            $data['RESPONSE_CODE'] = "SUCCESS";
            return response($data);
        }
    }
    public static function generateRandomString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
