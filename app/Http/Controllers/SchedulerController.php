<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MySession as MySession;
use App\CredentialModel;
use App\SchedulerCommands;

use DB;
use DateTime;
use Session;
use Carbon\Carbon;


class SchedulerController extends Controller
{
    /*****
     * SCHEDULER TYPE
     * 1 -  JV
     * 2 -  CDV
     * 3 -  CRV
     * ******/

    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        $data['head_title'] = "Schedulers";
        $data['current_tab'] = $request->status ?? 1;
        $param = [];
        $where = '';
        if($data['current_tab'] < 2){
            $where = " WHERE s.status = ?";
            $param = [$data['current_tab']]; 
        }

        $data['schedules'] = DB::select("SELECT s.id_scheduler,DATE_FORMAT(s.date,'%m/%d/%Y') as date,
                                        sc.description as type,st.description as schedule_type,DATE_FORMAT(s.stop_date,'%m/%d/%Y') as stop_date,DATE_FORMAT(s.last_execution,'%m/%d/%Y') as last_run,DATE_FORMAT(s.next_execution,'%m/%d/%Y') as next_run,if(s.status=1,'Active','Inactive') as status,s.reference as reference_no,DATE_FORMAT(s.date_created,'%m/%d/%Y') as date_created,s.type as type_code,cdv.type as cdv_type,sc.id_book as books,if(s.type <=3,if(sc.id_book=1,'JV',if(sc.id_book=2,'CDV','CRV')),'') as book_type
                                        FROM scheduler as s
                                        LEFT JOIN scheduler_type as st on st.id_scheduler_type = s.id_scheduler_type
                                        LEFT JOIN schedule_cmd_type as sc on sc.id_schedule_cmd_type = s.type
                                        LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = s.reference and sc.id_book = 1
                                        LEFT JOIN cash_disbursement as cdv on cdv.id_cash_disbursement = s.reference and sc.id_book = 2
                                        $where
                                        ORDER BY s.id_scheduler DESC",$param);
        $counts = DB::select("SELECT status,count(*) as count FROM scheduler GROUP BY status");

        $g = new GroupArrayController();
        $data['counts'] = $g->array_group_by($counts,['status']);

        // dd($data);
        return view('scheduler.index_card',$data);
    }
    public function create(Request $request){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/scheduler/index');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Create scheduler";

        $data['opcode'] = 0;

        $s_data= $request->s_data ?? null;

        
        if(isset($s_data)){
            $s_data = json_decode($s_data,true);

            $data['selected_reference'] =$this->parseReferenceDetails($s_data['type'],$s_data['reference']);
        }
        $data['stype'] = ($s_data['type'] ?? 1);
        return view('scheduler.scheduler_form_view',$data);
    }
    public function view($id_scheduler){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/scheduler/index');
        if(!$data['credential']->is_edit){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['opcode'] = 1;
        $data['id_scheduler'] = $id_scheduler;
        $data['details']=DB::select("SELECT s.id_scheduler,DATE_FORMAT(s.date,'%m/%d/%Y') as date,
                                    @type:=CASE WHEN s.type=1 THEN 'JV' 
                                    WHEN s.type=2 THEN 'CDV'
                                    ELSE '' END as type,st.description as schedule_type,DATE_FORMAT(s.stop_date,'%m/%d/%Y') as stop_date,concat(@type,'# ',s.reference) as reference,DATE_FORMAT(s.last_execution,'%m/%d/%Y') as last_run,DATE_FORMAT(s.next_execution,'%m/%d/%Y') as next_run,if(s.status=1,'Active','Inactive') as status,s.reference as reference_no,DATE_FORMAT(s.date_created,'%m/%d/%Y') as date_created,s.type as type_code,cdv.type as cdv_type,sc.id_book as books,s.date as rdate,s.id_scheduler_type,s.stop_date as rstop_date,s.status as rstatus,s.remarks
                                    FROM scheduler as s
                                    LEFT JOIN scheduler_type as st on st.id_scheduler_type = s.id_scheduler_type
                                    LEFT JOIN schedule_cmd_type as sc on sc.id_schedule_cmd_type = s.type
                                    LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = s.reference and sc.id_book = 1
                                    LEFT JOIN cash_disbursement as cdv on cdv.id_cash_disbursement = s.reference and sc.id_book = 2
                                    WHERE s.id_scheduler = ?",[$id_scheduler])[0];

        $data['sdate'] = ($data['details']->rstatus==1)?$data['details']->rdate:MySession::current_date();
        $data['stype'] = ($data['details']->type_code ?? 1);

        $data['history'] = DB::table('scheduler_task as st')
                           ->select(DB::raw("DATE_FORMAT(st.date,'%m/%d/%Y') as date,if(new_reference = 0,'',new_reference) as new_reference,DATE_FORMAT(date_executed,'%m/%d/%Y') as date_executed,if(st.status=0,'Pending',if(st.status=1,'Posted','Cancelled')) as status,if(st.status=0,'primary',if(st.status=1,'success','danger')) as status_c"))
                           ->where('id_scheduler',$id_scheduler)
                           ->where('status','<>',10)
                           ->orderBy('st.id_scheduler_task','DESC')
                           ->get();
        $data['selected_reference'] = $this->parseReferenceDetails($data['details']->type_code,$data['details']->reference_no);

        return view('scheduler.scheduler_form_view',$data);
    }

    public function parseReferenceDetails($type_code,$reference_no){
        $selected_reference = null;
        switch($type_code){
            case '1':
                $selected_reference = DB::table('journal_voucher')
                ->select(DB::raw("concat('JV# ',id_journal_voucher,' ',description) as tag_value,CONCAT(id_journal_voucher) as tag_id"))
                ->where('id_journal_voucher',$reference_no)
                ->first();
                break;
            case '2':
                $selected_reference = DB::table('cash_disbursement')
                ->select(DB::raw("concat('CDV# ',id_cash_disbursement,' ',description) as tag_value,CONCAT(id_cash_disbursement) as tag_id"))
                ->where('id_cash_disbursement',$reference_no)
                ->first();
                break;                
        }        
        return $selected_reference;
    }
    public function post_scheduler(Request $request){
        $data['RESPONSE_CODE'] = "SUCCESS";
        // $data['']
        // dd($request->all());

        // $id_scheduler = $request->id_scheduler ?? 0;


        if($request->TYPE <= 3 && !isset($request->REFERENCE)){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please select a valid reference";
            return response($data);
        }

        
        $scheduler = DB::table('scheduler')
                        ->where('id_scheduler',$request->id_scheduler ?? 0)
                        ->first();

        $opcode = isset($scheduler)? 1 : 0;
        $id_scheduler = 0;
        $active = $request->is_active;

        $with_changes = false;


        if($opcode == 0){
            $valid = $this->check_reference($request->TYPE,$request->REFERENCE);
            if(!$valid){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Reference has existing scheduler";

                return response($data);
            }
        }

        //Stop date validation
        if(isset($request->stop_date)){
            if($opcode == 0){
                //insert
                if($request->stop_date <= $request->date){
                    $data['RESPONSE_CODE'] = "ERROR";
                    $data['message'] = "Invalid Stop Date";
                    return response($data);
                }                
            }else{
                //update
                $next_execution =DB::table('scheduler_task')->where('id_scheduler',$scheduler->id_scheduler)->where('status',0)->max('date');
                if($request->stop_date <= $next_execution){
                    $data['RESPONSE_CODE'] = "ERROR";
                    $data['message'] = "Invalid Stop Date";
                    return response($data);
                }  
            }
        }

        if($opcode == 1 && ($scheduler->type != $request->TYPE || $scheduler->reference != $request->REFERENCE)){
            $executed_count = DB::table('scheduler_task')->where('id_scheduler',$scheduler->id_scheduler)->where('status',1)->count();

            if($executed_count > 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Scheduler has already executed command. Please create new scheduler";

                return response($data);
            }else{
                $valid = $this->check_reference($request->TYPE,$request->REFERENCE);
                if(!$valid){
                    $data['RESPONSE_CODE'] = "ERROR";
                    $data['message'] = "Reference has existing scheduler";

                    return response($data);
                }
            }
        }

        $set_as_active = false;
        if($opcode == 1 && $active != $scheduler->status){
            if($active == 0){
                // to be set as inactive
                DB::table('scheduler')
                ->where("id_scheduler",$scheduler->id_scheduler)
                ->update(['status'=>0,
                          'remarks'=>$request->remarks]);

                DB::table('scheduler_task')
                ->where('id_scheduler',$scheduler->id_scheduler)
                ->where('status',0)
                ->update(['status'=>10]);

                if($scheduler->type == 1){
                    DB::table('journal_voucher')
                    ->where('id_journal_voucher',$scheduler->reference)
                    ->update(['id_scheduler'=>0]);
                }elseif($scheduler->type == 2){
                    DB::table('cash_disbursement')
                    ->where('id_cash_disbursement',$scheduler->reference)
                    ->update(['id_scheduler'=>0]);                   
                }

                $data['RESPONSE_CODE'] =  "SUCCESS";
                $data['ID_SCHEDULER'] = $scheduler->id_scheduler;
                $data['message'] = "Scheduler status successfully updated";

                return response($data);
            }else{
                $set_as_active = true;
                DB::table('scheduler')
                ->where("id_scheduler",$scheduler->id_scheduler)
                ->update(['status'=>1]);
            }
        }

        if($opcode == 0){
            $insert_obj =[  
                'date'=>$request->date ?? MySession::current_date(),
                'reference'=>$request->REFERENCE,
                'type'=>$request->TYPE,
                'status'=>1,
                'id_scheduler_type'=>$request->sched_type ?? 3,
                'stop_date'=>$request->stop_date,
                'remarks'=>$request->remarks
            ];

            DB::table('scheduler')
            ->insert($insert_obj);

            $id_scheduler = DB::table('scheduler')->max('id_scheduler');
            $data['message'] = "Scheduler successfully posted";

        }else{

            $data['message'] = "Scheduler successfully updated";
            if(($scheduler->date != $request->date) || ($scheduler->id_scheduler_type != $request->sched_type) || $set_as_active){

                $id_scheduler = $scheduler->id_scheduler;
                DB::table('scheduler')
                ->where('id_scheduler',$id_scheduler)
                ->update([
                    'id_scheduler_type'=>$request->sched_type ?? 3,
                    'date'=>$request->date,
                    'stop_date'=>$request->stop_date,
                    'remarks'=>$request->remarks
                ]);

                DB::table('scheduler_task')
                ->where('id_scheduler',$id_scheduler)
                ->where('status',0)
                ->update(['status'=>10]);
            }elseif($scheduler->type != $request->TYPE || $scheduler->reference != $request->REFERENCE){
                $id_scheduler = $scheduler->id_scheduler;

                DB::table('scheduler')
                ->where('id_scheduler',$id_scheduler)
                ->update(['type'=>$request->TYPE,
                          'reference'=>$request->REFERENCE,
                          'remarks'=>$request->remarks]);

                DB::table('scheduler_task')
                ->where('id_scheduler',$id_scheduler)
                ->where('status',0)
                ->update(['status'=>10]);
            }else{
                if($request->stop_date != $scheduler->stop_date){

                    $with_changes = true;
                    DB::table('scheduler')
                    ->where('id_scheduler',$scheduler->id_scheduler)
                    ->update([
                        'stop_date'=>$request->stop_date,
                        'remarks'=>$request->remarks
                    ]);    
                }
            }
            
        }

        if($id_scheduler > 0){
            $this->generate_task($id_scheduler,$request->date,1);
            if($request->TYPE == 1){
                DB::table('journal_voucher')
                ->where('id_journal_voucher',$request->REFERENCE)
                ->update(['id_scheduler'=>$id_scheduler]);
            }elseif($request->TYPE == 2){
                
                DB::table('cash_disbursement')
                ->where('id_cash_disbursement',$request->REFERENCE)
                ->update(['id_scheduler'=>$id_scheduler]);
                    // DB::commit();
            }
        }else{
            if(!$with_changes){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "No Changes";   

                if($request->remarks != $scheduler->remarks){
                    DB::table('scheduler')
                    ->where('id_scheduler',$scheduler->id_scheduler)
                    ->update([
                        'remarks'=>$request->remarks
                    ]);
                    $id_scheduler = $scheduler->id_scheduler;
                    $data['RESPONSE_CODE'] = "SUCCESS";
                    $data['message'] = "Remarks Successfully updated";                                            
                }             
            }
        }
        $data['ID_SCHEDULER'] = $id_scheduler;

        return response($data);
    }


    public function check_reference($type,$reference){
        $valid = true;
        if($type == 1){
            $c = 'journal_voucher';
        }elseif($type == 2){
            $c = 'cash_disbursement';
        }

        if($type <= 3){
            $count = DB::table("$c")->where("id_$c",$reference)->where('id_scheduler','>',0)->count();
            $valid = ($count > 0)?false:true;
        }

        return $valid;

    }
    public function  generate_task($id_scheduler,$dt,$start_now=0){
        $scheduler_details = DB::table('scheduler')->where('id_scheduler',$id_scheduler)->first();
        $current_date = MySession::current_date();
        if($scheduler_details->stop_date != $current_date){
            // generate task
            if($start_now == 1){
                $date = $dt;
            }else{
                $date = $this->generate_next_date($dt,$scheduler_details->id_scheduler_type);
            }
            
            $task = [
                'id_scheduler'=>$id_scheduler,
                'main_reference'=>$scheduler_details->reference,
                'date'=>$date
            ];

            DB::table('scheduler_task')
            ->insert($task);

            DB::table('scheduler')
            ->where('id_scheduler',$id_scheduler)
            ->update(['next_execution'=>$date]);

        }else{
            // set scheduler as inactive
            DB::table('scheduler')->where('id_scheduler',$id_scheduler)->update(['status'=>0]);
        }
    }

    public function generate_next_date($date,$id_scheduler_type){
        // Parse the input date
        $parsedDate = Carbon::parse($date);


        switch($id_scheduler_type){
            case 1: 
                //daily
                $newDate = $parsedDate->addDay();
                break;
            case 2:
                //weekly
                $newDate = $parsedDate->addDay(7);
                break;
            case 3:
                //monthly
                $newDate = $parsedDate->addMonthNoOverflow();
                $newDay = min($parsedDate->day, $newDate->daysInMonth);
                $newDate->day = $newDay; 
                break;
            case 4:
                //quarterly
                $newDate = $parsedDate->addMonthNoOverflow(4);
                $newDay = min($parsedDate->day, $newDate->daysInMonth);
                $newDate->day = $newDay; 
                break;
            case 5:
                //yearly
                $newDate = $parsedDate->addYear();
                break;
            default:
                break;
        }

        return $newDate->toDateString();
    }


    public function execute_task($dt=null){
        $current_date = $dt ?? MySession::current_date();
        
        $tasks = DB::table('scheduler as s')
                 ->select(DB::raw("s.id_scheduler,st.id_scheduler_task,s.type,s.reference,DATE(s.next_execution) as dt"))
                 ->leftJoin('scheduler_task as st','st.id_scheduler','s.id_scheduler')
                 ->where('s.status',1)
                 ->where('st.status',0)
                 ->where('st.date','<=',$current_date)
                 ->get();

        foreach($tasks as $task){
            $new_reference = 0;
            if($task->type == 1){
                // JV
                $new_reference = SchedulerCommands::JV($current_date,$task->reference);
            }elseif($task->type == 2){
                //CDV
                $new_reference = SchedulerCommands::CDV($current_date,$task->reference);
            }elseif($task->type == 4){
                //Depreciation
                $new_reference = SchedulerCommands::Depreciation($current_date);
            }elseif($task->type == 5){
                $new_reference =   SchedulerCommands::NetSurplus($current_date);
            }

            DB::table('scheduler')
            ->where('id_scheduler',$task->id_scheduler)
            ->update(['last_execution'=>$current_date]);

            DB::table('scheduler_task')
            ->where('id_scheduler_task',$task->id_scheduler_task)
            ->update([
                'new_reference'=>$new_reference,
                'status'=>1,
                'date_executed'=>DB::raw("now()")
            ]);

            $this->generate_task($task->id_scheduler,$task->dt);
        }
        //stop date
        DB::select("UPDATE scheduler as s
        LEFT JOIN  scheduler_task as st on st.id_scheduler = s.id_scheduler and st.status=1
        SET s.status=0,st.status=10
        where s.stop_date = ? and s.status=1;",[$current_date]);

        //end stop date

        return "SUCCESS";

    }
    public function view_details(Request $request){
        if($request->ajax()){
            $id_scheduler = $request->id_scheduler;

            $data['details']=DB::select("SELECT s.id_scheduler,DATE_FORMAT(s.date,'%m/%d/%Y') as date,
                                        @type:=CASE WHEN s.type=1 THEN 'JV' 
                                        WHEN s.type=2 THEN 'CDV'
                                        ELSE '' END as type,st.description as schedule_type,DATE_FORMAT(s.stop_date,'%m/%d/%Y') as stop_date,concat(@type,'# ',s.reference) as reference,DATE_FORMAT(s.last_execution,'%m/%d/%Y') as last_run,DATE_FORMAT(s.next_execution,'%m/%d/%Y') as next_run,if(s.status=1,'Active','Inactive') as status,s.reference as reference_no,DATE_FORMAT(s.date_created,'%m/%d/%Y') as date_created,s.type as type_code,cdv.type as cdv_type,s.books,s.date as rdate,s.id_scheduler_type,s.stop_date as rstop_date,s.status as rstatus
                                        FROM scheduler as s
                                        LEFT JOIN scheduler_type as st on st.id_scheduler_type = s.id_scheduler_type
                                        LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = s.reference and s.books = 1
                                        LEFT JOIN cash_disbursement as cdv on cdv.id_cash_disbursement = s.reference and s.books = 2
                                        WHERE s.id_scheduler = ?",[$id_scheduler])[0];
            $data['sdate'] = ($data['details']->rstatus==1)?$data['details']->rdate:MySession::current_date();

            $data['history'] = DB::table('scheduler_task as st')
                               ->select(DB::raw("DATE_FORMAT(st.date,'%m/%d/%Y') as date,if(new_reference = 0,'',new_reference) as new_reference,DATE_FORMAT(date_executed,'%m/%d/%Y') as date_executed,if(st.status=0,'Pending',if(st.status=1,'Executed','Cancelled')) as status,if(st.status=0,'primary',if(st.status=1,'success','danger')) as status_c"))
                               ->where('id_scheduler',$id_scheduler)
                               ->where('status','<>',10)
                               ->orderBy('st.id_scheduler_task','DESC')
                               ->get();

            return response($data);

        }
    }
    public function search_reference(Request $request){
        $type = $request->type;
        $search = $request->term;
        if($type == 1){
            $result = DB::table('journal_voucher')
            ->select(DB::raw("concat('JV# ',id_journal_voucher,' ',description) as tag_value,CONCAT(id_journal_voucher) as tag_id"))
            ->where(function ($query) use ($search){
                // $query->where(DB::raw("id_journal_voucher"), '=', "$search");

                $query->where('id_journal_voucher','=',$search);
            })->get();
        }elseif($type == 2){
            $result = DB::table('cash_disbursement')
            ->select(DB::raw("concat('CDV# ',id_cash_disbursement,' ',description) as tag_value,CONCAT(id_cash_disbursement) as tag_id"))
            ->where(function ($query) use ($search){
                // $query->where(DB::raw("id_cash_disbursement"), '=', "$search");

                $query->where('id_cash_disbursement','=',$search);
            })->get();
        }
        $data['accounts'] = $result;
        return response($data);
    }
}
