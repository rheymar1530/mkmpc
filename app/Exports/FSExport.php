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


class FSExport implements FromView, WithEvents,ShouldAutoSize,WithColumnFormatting,WithHeadingRow
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $data;
    protected $type;
    protected $endRow;
    function __construct($data,$type) {
        $this->data = $data;
        $this->type = $type;
        /**
         * TYPE 
         * 1 => Income Statement and Balance Sheet
         * 2 => Cash Flow
         * 3 => Changes in Equities
         * **/
    }
    public function view(): View
    {
        if($this->type == 1){
            return view('financial_statement.excel_fs',$this->data);
        }elseif($this->type == 2){
            return view('cash_flow.excel',$this->data);
        }elseif($this->type == 3){
            return view('changes_equity.excel',$this->data);
        }
    }
    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function(AfterSheet $event) {
                $workSheet = $event->sheet->getDelegate();
                $startRow = 3;
                $endRow = $event->sheet->getHighestRow();
                $ar = [];

                if($this->type <= 2){
                    $letters = ["D","E","F","G","H"];
                }else{
                     $letters = ["C","D","E"];
                }
                
                foreach($letters as $l){
                    $ar[$l] = '#,##0.00;(#,##0.00)';
                }
                $columnFormats  = $ar;
                foreach ($columnFormats as $column => $format) {
                    $range = $column . $startRow . ':' . $column . $endRow;
                    $event->sheet->getStyle($range)->getNumberFormat()->setFormatCode($format);
                }
                $event->sheet->getStyle('A1:'.$letters[count($letters)-1]."2")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            
                // $workSheet->freezePane('C2'); // freezing here
            },
        ];
    }
    public function columnFormats(): array
    {
        return [];
    }
}

