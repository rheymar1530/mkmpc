<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\MySession as MySession;
use DB;
class WebHelper extends Model
{
    public static function sidebarMenu($is_maintenance,$is_report=0)
    {   

        $me = MySession::me();
        $my_privilege_id = MySession::myPrivilegeId();
        $menu_active = DB::table('cms_menus')->whereRaw("cms_menus.id IN (select id_cms_menus from cms_menus_privileges where id_cms_privileges = '".$my_privilege_id."')")->where('parent_id', 0)->where('is_active', 1)->where('is_dashboard', 0)->orderby('sorting', 'asc')->select('cms_menus.*')
        ->where('is_maintenance',$is_maintenance)
        ->where('is_report',$is_report)
        ->get();
        foreach ($menu_active as &$menu){
            $url = $menu->path;
            $menu->is_broken = false;

            $menu->url = $url;
            $menu->url_path = trim(str_replace(url('/'), '', $url), "/");

            $child = DB::table('cms_menus')->whereRaw("cms_menus.id IN (select id_cms_menus from cms_menus_privileges where id_cms_privileges = '".$my_privilege_id."')")->where('is_dashboard', 0)->where('is_active', 1)->where('parent_id', $menu->id)->select('cms_menus.*')->orderby('sorting', 'asc')->get();
            if (count($child)) {
                foreach ($child as &$c) {
                    $url = $c->path;
                    $c->is_broken = false;
                    $c->url = $url;
                    $c->url_path = trim(str_replace(url('/'), '', $url), "/");
                }
                $menu->children = $child;
            }
        }
        return $menu_active;
    }

    public static function myPrivilegeSwitchList(){
       return  DB::select("SELECT id_cms_privilege_s,name FROM privileges_switch as ps
                    LEFT JOIN cms_privileges as cp on cp.id = ps.id_cms_privilege_s
                    WHERE id_cms_privileges = ?
                    ORDER BY id_cms_privilege_s;",[MySession::myParentPrivilegeID()]);
    }
    public static function ReportDateFormatter($date_from,$date_to){
        if($date_from == $date_to){
            $output_date = date("F d, Y",strtotime($date_from));
        }else{

            $start = self::parseDateFormat($date_from);
            $end = self::parseDateFormat($date_to);

            if($start['year'] == $end['year'] && $start['month'] == $end['month']){
                $output_date = $start['month']." ".$start['day']." to ".$end['day'].", ".$start['year'];
            }else{
                $output_date = date("F d, Y",strtotime($date_from))." to ".date("F d, Y",strtotime($date_to));
            }
        }
        return $output_date;
    }
    public static function parseDateFormat($date){
        return array(
            'month' => date("F",strtotime($date)),
            'day' => date("d",strtotime($date)),
            'year' => date("Y",strtotime($date))
        );
    }

    // public static function ConvertDatePeriod($date){
    //     $day = date("j",strtotime($date));
    //     $month = date("n",strtotime($date));
    //     $year = date("Y",strtotime($date));
    //     $minimum_date = env('INTEREST_GRACE_PERIOD');
    //     if($day < $minimum_date){
    //         $month = $month-1;
    //         $in = ($month == 2)?"t":30;
    //         return date("Y-m-$in",strtotime("$year-$month-$day"));
    //     }
    //     $in = ($month == 2)?"t":30;
    //     return date("Y-m-$in",strtotime($date));
    // }
    // public static function ConvertDatePeriod($date){
    //     $day = date("j",strtotime($date));
    //     $month = date("n",strtotime($date));
    //     $year = date("Y",strtotime($date));
    //     $minimum_date = env('INTEREST_GRACE_PERIOD');

    //     if($day < $minimum_date){
    //         $month = $month-1;
    //         $in = ($month == 2)?"t":30;
    //         return date("Y-m-$in",strtotime("$year-$month-$day"));
    //     }elseif($minimum_date == 0){
    //         if($month == 12){
    //             $y = $year+1;
    //             // $in = ($month == 2)?"t":30;
    //             $d = date('Y-m-30',strtotime("$y-01-01"));
    //         }else{
    //             $m = $month+1;
    //             $d=date("Y-m-t",strtotime("$year-$m-$day"));
    //         }
    //         return $d;

    //         // $d = date("Y-m-t",strtotime($date));
    //         // $date = new DateTime($d);
    //         // $date->modify('+1 month');
    //         // dd($date);
    //         // dd(date("Y-m-t",strtotime('+1 Month',strtotime($d))));
    //     }
    //     $in = ($month == 2)?"t":30;
    //     return date("Y-m-$in",strtotime($date));
    // }
    public static function ConvertDatePeriod($date){
        $day = date("j",strtotime($date));
        $month = date("n",strtotime($date));
        $year = date("Y",strtotime($date));
        $minimum_date = env('INTEREST_GRACE_PERIOD');
        if($day < $minimum_date){
            $month = $month-1;
            $in = "t";
            // $in = ($month == 2)?"t":30;
            return date("Y-m-$in",strtotime("$year-$month-$day"));
        }
        // $in = ($month == 2)?"t":30;
        $in = "t";
        return date("Y-m-$in",strtotime($date));
    }
    public static function ConvertDatePeriod2($date){

        /*************FOR RENEWAL*****************************/
        $day = date("j",strtotime($date));
        $month = date("n",strtotime($date));
        $year = date("Y",strtotime($date));
        $minimum_date = env('RENEWAL_GRACE_PERIOD');
        if($day <= $minimum_date){
            $month = $month-1;
            // $in = ($month == 2)?"t":30;
            $in = "t";
            return date("Y-m-$in",strtotime("$year-$month-$day"));
        }
        $in = "t";
        // $in = ($month == 2)?"t":30;
        return date("Y-m-$in",strtotime($date));
    }
    public static function SwipingAmount(){
        return floatval(DB::table('tbl_payment_type')->select('default_amount')->where('id_payment_type',18)->first()->default_amount ?? 0);
    }
    public static function amountToWords($amount) {
        $number = number_format($amount, 2, '.', '');
        $decimal_part = intval(substr($number, -2));
        $integer_part = intval(substr($number, 0, -3));
        $words = '';
        
        $ones = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine');
        $tens = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety');
        $teens = array('eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');
        $hundreds = array('', 'one hundred', 'two hundred', 'three hundred', 'four hundred', 'five hundred', 'six hundred', 'seven hundred', 'eight hundred', 'nine hundred');
        $thousands = array('', 'thousand', 'million', 'billion', 'trillion');
        
        if ($integer_part == 0) {
            $words = 'zero';
        }
        
        $thousands_index = 0;
        
        while ($integer_part > 0) {
            if ($integer_part % 1000 > 0) {
                $hundreds_index = floor(($integer_part % 1000) / 100);
                $tens_index = floor(($integer_part % 100) / 10);
                $ones_index = $integer_part % 10;
                $thousands_word = $thousands[$thousands_index];
                $hundreds_word = $hundreds[$hundreds_index];
                $tens_word = '';
                $ones_word = '';
                
                if ($tens_index == 1 && $ones_index > 0) {
                    $tens_word = $teens[$ones_index - 1];
                    $ones_word = '';
                } else {
                    $tens_word = $tens[$tens_index];
                    $ones_word = $ones[$ones_index];
                }
                
                $words = $hundreds_word . ' ' . $tens_word . ' ' . $ones_word . ' ' . $thousands_word . ' ' . $words;
            }
            
            $integer_part = floor($integer_part / 1000);
            $thousands_index++;
        }
        
        // $words .= ' dollars';
        
        if ($decimal_part > 0) {
            $words .= ' and ';
            
            // if ($decimal_part >= 11 && $decimal_part <= 19) {
            //     $words .= $teens[$decimal_part - 11];
            // } elseif ($decimal_part >= 20 || $decimal_part == 10) {
            //     $tens_digit = floor($decimal_part / 10);
            //     $words .= $tens[$tens_digit];
            //     $decimal_part -= $tens_digit * 10;
            // }
            
            // if ($decimal_part >= 1 && $decimal_part <= 9) {
            //     $words .= ' ' . $ones[$decimal_part];
            // }
            
            // $words .= ' cents';

            $words.= "$decimal_part"."/"."100";
        }
        
        return ucwords(trim($words));
    }
}