<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GroupArrayController extends Controller
{
    function array_group_by($array, $cond){
        $key_ = $cond[0];
        $func = (!is_string($key_) && is_callable($key_) ? $key_ : null);
        $_key = $key_;
        // Load the new array, splitting by the target key
        $grouped = [];
        foreach ($array as $key => $value){
            $key_ = null;
            if(is_callable($func)){
                $key_ = call_user_func($func, $value);
            }elseif (is_object($value) && property_exists($value, $_key)){
                $key_ = $value->{$_key};
            }elseif (isset($value[$_key])){
                $key_ = $value[$_key];
            }
            if($key_ === null){
                continue;
            }
            
            $grouped[$key_][] = $value;
        }
        if(count($cond) > 1){
            $args = func_get_args();
            unset($cond[0]);
            $cond= array_values($cond);
            foreach ($grouped as $keys => $value) {
                $grouped[$keys] = $this->array_group_by($value, $cond);
            }
        }
        return $grouped;
    }
}
