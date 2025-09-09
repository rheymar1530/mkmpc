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
use DB;
use App\Http\Controllers\PayrollController;

class PayrollSummaryExport implements FromView, WithEvents,ShouldAutoSize,WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $payroll_data;

    function __construct($payroll_data) {
        $this->payroll_data = $payroll_data;
    }
    public function view(): View
    {
        // $p = new PayrollController();
        // $data['payroll'] = $p->payroll_data($this->id_payroll);
        $data['payroll'] = $this->payroll_data;

        return view('payroll.payroll_summary',$data);
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $workSheet = $event->sheet->getDelegate();
                $workSheet->freezePane('C2'); // freezing here
            },
        ];
    }
    public function columnFormats(): array
    {
        return [
            "I"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "J"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "K"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "L"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "M"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "N"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "O"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "P"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "Q"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "R"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "S"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "T"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "U"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "V"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "W"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "X"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "Y"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "Z"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "AA"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "AB"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "AC"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "AD"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "AE"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
            // 'J'
        ];
    }
}
