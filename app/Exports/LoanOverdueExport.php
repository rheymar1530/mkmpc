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
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class LoanOverdueExport implements FromView, WithEvents,ShouldAutoSize,WithColumnFormatting,WithHeadingRow
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
        $data = $this->data;
        // dd($data);
        return view('loan-overdue.excel',$data); 

    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $workSheet = $event->sheet->getDelegate();
                $startRow = 2;
                $highestColumn = $workSheet->getHighestColumn();
                $endRow = $event->sheet->getHighestRow();
                // $event->sheet->getDelegate()->setPrintGridlines(true);
                $workSheet->getStyle('A1:' . $highestColumn . $endRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
  
                  $workSheet->freezePane('A3'); // freezing here

                  $workSheet->getStyle('A1:' . $highestColumn . $endRow)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);                
            },
        ];
    }
    public function columnFormats(): array
    {
        return [
            "C"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "D"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "E"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            
            "G"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "H"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "I"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "J"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "K"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];

    }

}
