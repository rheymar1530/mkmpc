<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;

class MySession extends Model
{
    public static function myId()
    {
        return Session::get('admin_id');
    }
}
