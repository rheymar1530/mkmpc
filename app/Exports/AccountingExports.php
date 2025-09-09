<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;


class AccountingExports implements FromView, WithEvents,ShouldAutoSize,WithColumnFormatting,WithHeadingRow
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $prime_data;
    protected $type;
    protected $month_counter;

    function __construct($data) {
        $this->data = $data;
    }
    public function view(): View
    {
        // dd($this->data);
       return view('general_ledger.excel',$this->data);

    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $workSheet = $event->sheet->getDelegate();
            },
        ];
    }
    public function columnFormats(): array
    {
        if($this->data['filter_type'] == 2){

            //GL
            return [
                "E"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                "F"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            ];           
        }else{
            return [
                "B"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                "C"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            ];                   
        }
        // if($this->type == 1){
        //     return [
        //         "D"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        //         "E"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        //         "F"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        //     ];
        // }elseif($this->type == 2){
        //     return [
        //         "C"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        //     ];           
        // }elseif($this->type == 3){
        //    return [];

        // }

        return [];

    }

}

