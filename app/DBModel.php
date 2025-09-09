<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DBModel extends Model
{
    public static function lse_db(){
    	// return 'lse_db';
    	// return 'mysql';
    	// return 'cloud_db';
        return 'cloud_db_lse';
    }
    public static function server_tat(){
        return 'lse_db';
    }
}
