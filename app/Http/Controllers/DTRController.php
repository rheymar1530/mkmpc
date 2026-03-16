<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DTRController extends Controller
{
    public function index(){
        $data['employees'] = [
            'Jhon Rheymar Caluza',
            'Kervin Tabunggao',
            'Xerxes Rodriguez',
            'Jenalyn Villamor',
            'Justine Torib',
            'Kyle Revin Alimpuangon',
            'Justine Cerge Loot',
            'Abner Alemania',
            'Vergie Mae Cabalse'
        ];
        return view('dtr.form',$data);
    }
    public function print(Request $request)
    {

        $payload = json_decode($request->dtr_payload,true);
        $branch = $payload['branch'];


        $start = Carbon::parse($payload['start']);
        $end   = Carbon::parse($payload['end']);

        // format for your blade template
        $dt_s = $start->format('F j');
        $year_s = $start->format('Y');

        $dt_e = $end->format('F j');
        $year_e = $end->format('Y');

        $employees = $payload['employees'];

        // dd($request->all());

        return view('dtr.print',[
            'employees'=>$employees,
            'dt_s'=>$dt_s,
            'year_s'=>$year_s,
            'dt_e'=>$dt_e,
            'year_e'=>$year_e,
            'branch'=>$branch
        ]);

    }
}
