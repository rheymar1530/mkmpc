<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use App\Http\Controllers\PayrollController;

class InterestExport implements FromView, WithEvents,ShouldAutoSize,WithColumnFormatting,WithHeadingRow
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $data;
    protected $month_counter;


    function __construct($data,$month_counter) {
        $this->data = $data;
        $this->month_counter = $month_counter;

    }
    public function view(): View
    {
        return view('reports.paid_interest_excel_export',$this->data);

    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $workSheet = $event->sheet->getDelegate();
                $startRow = 2;
                $endRow = $event->sheet->getHighestRow();

               
                $ar = [];

                $letter = "B";
                $counter = $this->month_counter+2;

                for($i=0;$i<$counter;$i++){
                    $ar[$letter] = NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;             
                    $letter = ++$letter;
                }
                $columnFormats  = $ar;
                foreach ($columnFormats as $column => $format) {
                    $range = $column . $startRow . ':' . $column . $endRow;
                    $event->sheet->getStyle($range)->getNumberFormat()->setFormatCode($format);
                }
            
                // $workSheet->freezePane('C2'); // freezing here
            },
        ];
    }
    public function columnFormats(): array
    {
            return [];

    }

}
