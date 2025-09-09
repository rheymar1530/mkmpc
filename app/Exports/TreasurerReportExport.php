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

class TreasurerReportExport implements FromView, WithEvents,ShouldAutoSize,WithColumnFormatting,WithHeadingRow
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $data;
    function __construct($data) {
        $this->data = $data;
    }
    public function view(): View
    {
        return view('treasurer_report.excel',$this->data);

    }
    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function(AfterSheet $event) {
                $workSheet = $event->sheet->getDelegate();
                $startRow = 3;
                $endRow = $event->sheet->getHighestRow();
                $ar = [];

                // if($this->type <= 2){
                //     $letters = ["D","E","F","G","H"];
                // }else{
                //      $letters = ["C","D","E"];
                // }
                $letters = ["E","F"];
                foreach($letters as $l){
                    $ar[$l] = '#,##0.00;(#,##0.00)';
                }
                $columnFormats  = $ar;
                foreach ($columnFormats as $column => $format) {
                    $range = $column . $startRow . ':' . $column . $endRow;
                    $event->sheet->getStyle($range)->getNumberFormat()->setFormatCode($format);
                }
                // $event->sheet->getStyle('A1:'.$letters[count($letters)-1]."2")->applyFromArray([
                //     'font' => [
                //         'bold' => true,
                //     ],
                // ]);
            
                // $workSheet->freezePane('C2'); // freezing here
            },
        ];
    }
    public function columnFormats(): array
    {
        return [
          
        ];
    }
}

