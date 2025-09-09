<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Faker;
use DB;
use Illuminate\Support\Facades\Hash;

class DummyDataController extends Controller
{
    public function test(){
        // return $this->random_address();
        return $this->update_name();
    }

    public function random_address(){
        $faker = Faker\Factory::create();

        return preg_replace("/[\n\r]/","",$faker->address); 
        // return  ;   
    }
    public function random_name(){
        $faker = Faker\Factory::create();
        $name =  str_replace("'","",$faker->name);

        // return $faker->email;

        $ex = explode(" ",$name);



        if(count($ex) == 2){
            return $ex;
        }else{
           return $this->random_name();
        }
    }

    public function update_name(){
        $members = DB::table('member')
        ->select('id_member','spouse')
        ->get();

        foreach($members as $member){
            $nm = $this->random_name();
            $spouse = $this->random_name()[0]." ".$nm[1];
            DB::table('member')
            ->where('id_member',$member->id_member)
            ->update([
                'first_name'=>$nm[0],
                'last_name'=>$nm[1],
                'email'=>strtolower($nm[0]."_".$nm[1]."@gmail.com"),
                'address'=>$this->random_address(),
                'spouse' =>DB::raw("if(spouse is null OR spouse ='',null,'$spouse')")

            ]);
        }

        // UPDATE EMPLOYEE
        DB::select("UPDATE employee as e
        LEFT JOIN member as m on m.id_member = e.id_member
        SET e.first_name = m.first_name,e.middle_name = m.middle_name,e.last_name=m.last_name,e.address = m.address,e.email=m.email;");


        // UPDATE JOURNAL VOUCHER
        DB::select("UPDATE 
            journal_voucher as jv
            LEFT JOIN member as m on m.id_member = jv.id_member
            LEFT JOIN employee as e on e.id_employee = jv.id_employee
            SET jv.payee= CASE
            WHEN jv.id_member is not null OR jv.id_member > 0 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
            WHEN jv.id_employee is not null OR jv.id_employee > 0 THEN FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
            END,jv.id_branch=CASE
            WHEN jv.id_member is not null OR jv.id_member > 0 THEN m.id_branch
            WHEN jv.id_employee is not null OR jv.id_employee THEN e.id_branch
            END,
            jv.address =CASE
            WHEN jv.id_member is not null OR jv.id_member > 0 THEN m.address
            WHEN jv.id_employee is not null OR jv.id_employee THEN e.address
            END 
            WHERE (jv.id_member is not null AND jv.id_member > 0) OR (jv.id_employee is not null AND jv.id_employee > 0);");

        //UPDATE CASH DISBURSEMENT
        DB::select("UPDATE 
            cash_disbursement as jv
            LEFT JOIN member as m on m.id_member = jv.id_member
            LEFT JOIN employee as e on e.id_employee = jv.id_employee
            SET jv.payee= CASE
            WHEN jv.id_member is not null OR jv.id_member > 0 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
            WHEN jv.id_employee is not null OR jv.id_employee > 0 THEN FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
            END,jv.id_branch=CASE
            WHEN jv.id_member is not null OR jv.id_member > 0 THEN m.id_branch
            WHEN jv.id_employee is not null OR jv.id_employee THEN e.id_branch
            END,
            jv.address =CASE
            WHEN jv.id_member is not null OR jv.id_member > 0 THEN m.address
            WHEN jv.id_employee is not null OR jv.id_employee THEN e.address
            END 
            WHERE (jv.id_member is not null AND jv.id_member > 0) OR (jv.id_employee is not null AND jv.id_employee > 0);");

        //UPDATE CASH RECEIPT VOUCHER
        DB::select("UPDATE 
            cash_receipt_voucher as jv
            LEFT JOIN member as m on m.id_member = jv.id_member
            LEFT JOIN employee as e on e.id_employee = jv.id_employee
            SET jv.payee= CASE
            WHEN jv.id_member is not null OR jv.id_member > 0 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
            WHEN jv.id_employee is not null OR jv.id_employee > 0 THEN FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
            END,jv.id_branch=CASE
            WHEN jv.id_member is not null OR jv.id_member > 0 THEN m.id_branch
            WHEN jv.id_employee is not null OR jv.id_employee THEN e.id_branch
            END,
            jv.address =CASE
            WHEN jv.id_member is not null OR jv.id_member > 0 THEN m.address
            WHEN jv.id_employee is not null OR jv.id_employee THEN e.address
            END 
            WHERE (jv.id_member is not null AND jv.id_member > 0) OR (jv.id_employee is not null AND jv.id_employee > 0);");

            $users = DB::select("SELECT c.id,c.email FROM  cms_users as c
            LEFT JOIN member as m on m.id_member = c.id_member
            WHERE (c.id <> 29 AND c.id_cms_privileges <> 1)");


            $c = array_chunk($users,10);



            foreach($c as $users){
                foreach($users as $u){
                    $hashed_password = Hash::make($u->email, ['rounds' => 12]);

                    DB::table('cms_users')
                    ->where('id',$u->id)
                    ->update(['password'=>$hashed_password]);

                    // return $hashed_password;
                }                
            }


        return "SUCCESS";
    }
}
