<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\DBModel;
use App\MySession as MySession;

class ParseReportController extends Controller
{
    public function sql_connection(){
        return DBModel::server_tat();
    }

    public function parseReport($id_report,$field_group,$type,$where_statement){
        try{
            $sql_select = $this->parseSelect_Order($id_report,$type);
            $select_fields = explode(",",$sql_select->select_keys);

            //SQl  SELECT,ORDER  AND HEADER Statement
            $select = $sql_select->select_statement;
            if($select == null) return "NO";
            $headers = explode(",",$sql_select->header);
            $ordering = ($sql_select->ordering == "NO")?"":"ORDER BY ".$sql_select->ordering;
            $id_fields = explode(",",$sql_select->id_field);
            $sum_keys = ($sql_select->sum_key == "NO")?[]:explode(",",$sql_select->sum_key);

            //SQL STATEMENT SELECT APPEND GROUP
            $group_select = $this->parseGroupFields($id_report,$type);
            $group_field = ($group_select->group_select == "NO")?"":$group_select->group_select.",";

            $group_key = ($group_select->group_key == null)?[]:explode(",",$group_select->group_key);
           
            $sql_statement = "SELECT $group_field$select
                              FROM lse.hawb_info
                              LEFT JOIN lse.tbl_client_cost_center on tbl_client_cost_center.id_tbl_client_cost_center = hawb_info.cost_center
                              LEFT JOIN lse.service_info  on service_info.service_id = hawb_info.service_id
                              LEFT JOIN lse.tbl_client_account on tbl_client_account.hawb_no = hawb_info.hawb_no
                              $where_statement
                              GROUP BY hawb_info.hawb_no
                              $ordering";

            $data['sql_statement'] = $sql_statement;
            $data['headers'] = $headers;
            $data['group_keys'] = $group_key;
            $data['select_fields'] = $select_fields;
            $data['data_types'] = $this->get_data_type($id_fields);
            $data['sum_fields'] = $sum_keys;

            return $data;
        }catch(\Illuminate\Database\QueryException $ex){ 
            return $ex->getMessage();
        }
    }
    
    //returns select, order and header of select statement
    public function parseSelect_Order($id_report,$type){
        //type 0 - via tbl_request ; 1 - custom
        if($type == 0){
            $sql = "SELECT 
                    group_concat(f.id_field) as id_field,
                    group_concat(
                    concat(
                        if(f.custom is not null,f.custom,concat(tb.table_name,'.',f.field))
                    )) as 'select_statement',
                    concat(
                        group_concat(concat(custom_col))
                    ) as header,
                    ifnull(group_concat(
                        if(r_field.order > 0,CONCAT('CAST(',if(f.is_function=1,'',concat(tb.table_name,'.')),f.field,' as UNSIGNED)', if(r_field.order=1,' ASC ',' DESC ')),null)
                    ),'NO') as ordering,
                    group_concat(f.group_key) as select_keys,
                    ifnull(group_concat(if(r_field.is_sum =1,f.group_key,null)),'NO') as sum_key
                    FROM lse_new_reports.tbl_report_fields r_field
                    LEFT JOIN lse_new_reports.tbl_field as f on f.id_field = r_field.id_field
                    LEFT JOIN lse_new_reports.tbl_table as tb on tb.id_table = f.id_table
                    WHERE r_field.id_report = $id_report
                    ORDER BY r_field.id_report_fields";
            $results = DB::connection($this->sql_connection())
                        ->select($sql);
            return $results[0];

            
        }
    }
    //returns group fields
    public function parseGroupFields($id_report,$type){
        //type 0 - via tbl_request ; 1 - custom
        if($type == 0){
            $sql="SELECT 
                    ifnull(
                    group_concat(concat(
                        if(f.custom is not null,f.custom,f.field)
                    )),'NO') as 'group_select',
                    group_concat(group_key) as group_key
                    FROM lse_new_reports.tbl_groupings g
                    LEFT JOIN lse_new_reports.tbl_field as f on f.id_field = g.id_Field
                    WHERe g.id_report = $id_report
                    ORDER by g.id_groupings;";
                    $results = DB::connection($this->sql_connection())
                                ->select($sql);
                    return $results[0];
        }
    }
    public function get_data_type($id_fields){
        // return 123;
        $data_type = DB::connection($this->sql_connection())
                     ->table('lse_new_reports.tbl_field as f')
                     ->select('f.group_key','d.data_type')
                     ->LeftJoin('lse_new_reports.tbl_data_type as d','d.id_data_type','f.id_data_type')
                     ->whereIn('f.id_field',$id_fields)
                     ->get();
        foreach($data_type as $row){
            $d[$row->group_key] = $row->data_type;
        }
        return $d;
    }
}
