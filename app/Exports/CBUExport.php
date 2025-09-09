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

class CBUExport implements FromView, WithEvents,ShouldAutoSize,WithColumnFormatting,WithHeadingRow
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $cbu_data;
    protected $type;
    protected $month_counter;

    function __construct($cbu_data,$type,$month_counter=0) {
        $this->cbu_data = $cbu_data;
        $this->type = $type;
        $this->month_counter = $month_counter;
    }
    public function view(): View
    {
        // dd($this->month_counter);
        // $p = new PayrollController();
        // $data['payroll'] = $p->payroll_data($this->id_payroll);
        if($this->type == 1){
            $data['cbu_ledger'] = $this->cbu_data;
            return view('cbu.export_excel',$data);            
        }elseif($this->type == 2){
            $data['cbu'] = $this->cbu_data;
            return view('cbu.report_export_excel',$data);
        }elseif($this->type == 3){
            $data['cbus'] = $this->cbu_data;
            return view('cbu.monthly_export_excel',$data);
        }

    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $workSheet = $event->sheet->getDelegate();
                $startRow = 2;
                $endRow = $event->sheet->getHighestRow();

                if($this->type == 3){
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
                }
                // $workSheet->freezePane('C2'); // freezing here
            },
        ];
    }
    public function columnFormats(): array
    {
        if($this->type == 1){
            return [
                "D"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                "E"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                "F"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            ];
        }elseif($this->type == 2){
            return [
                "C"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
            ];           
        }elseif($this->type == 3){
           return [];

        }

    }

}
