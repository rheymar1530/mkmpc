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


class VoucherExport implements FromView, WithEvents,ShouldAutoSize,WithColumnFormatting,WithHeadingRow
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $data;

    function __construct($data,$type) {
        $this->data = $data;
        $this->type = $type;
        /*
            type => 1 - Cash in/out ; 2 - voucher summary
        */

        }
        public function view(): View
        {
            if($this->type == 1){
                return view('journal_report.excel',$this->data);
            }else{
                return view('transaction_summary.excel',$this->data);
            }
        }
        public function registerEvents(): array
        {

            return [
                AfterSheet::class => function(AfterSheet $event) {
                    $workSheet = $event->sheet->getDelegate();
                    if($this->type == 1){
                        $event->sheet->getStyle('A1:G1')->applyFromArray([
                            'font' => [
                                'bold' => true,
                            ],
                        ]); 
                    }

                },
            ];
        }
        public function columnFormats(): array
        {
            if($this->type == 1){
                if($this->data['fil_type'] == 'cash_disbursement'){
                    return [
                        "E"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
                    ];            
                }else{
                    return [
                        "F"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
                    ];              
                }


            }else{
                if($this->data['selected_type'] == 2){
                    return [
                        "F"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        "G"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
                    ]; 
                }else{
                    return [
                        "C"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        "D"=> NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
                    ];                     
                }

            }

            return [];

        }

    }

