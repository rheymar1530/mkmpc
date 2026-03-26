<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $file_name }}</title>
    <style>
        .row:after {
            line-height: 10%;
            content: "";
            display: table;
            clear: both;
            height: 1px;
        }

        .columnLeft {
            float: left;
            width: 50%;
        }

        .columnRight {
            float: right;
            width: 50%;
        }

        @page {
            margin-left: 1in;
            margin-right: 1in;
            margin-top: 1in;
            size: letter landscape;
        }

        div.header {
            text-align: center;
            /*line-height: 1px;*/
            font-size: 15px;
            font-family: Calibri, sans-serif;
        }

        * {
            box-sizing: border-box;
            font-family: Calibri, sans-serif;
        }

        .tbl_gl tr>td,
        .tbl_gl tr>th {
            padding: 3px;
            vertical-align: top;
            font-family: Arial, Helvetica, sans-serif !important;
            letter-spacing: 1px;
            font-size: 0.14in;
        }



        .class_amount {
            text-align: right;
            padding-right: 2mm !important;
        }

        table.loan,
        .loan td,
        .loan th {
            border: 1px solid;
        }

        table.loan {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid;
        }

        .highlight_amount {
            font-weight: bold;
            text-decoration: underline;
            font-size: 12px !important;
        }

        .bold-text {
            font-weight: bold;
        }

        .tbl_head {
            border-top: 2px solid;
            border-bottom: 2px solid;
        }

        .col_border {
            /*border-left: 1px solid;*/
        }

        .year_head th {
            font-weight: normal !important;
        }

        .text-centered {
            text-align: center;
        }

        #head-tbl th {
            vertical-align: middle !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .my-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .font-weight-bold {
            font-weight: bold !important;
        }

    </style>

    <style>
    .table-allocation {
        border-collapse: collapse;
    }

    .table-allocation th,
    td {
        font-size: 12pt;
        border: 1px solid;
    }

    .table-allocation th {
        vertical-align: middle !important;
        padding: 3px;

    }

    .table-allocation td {
        padding: 3px;
    }

    .footer td {
        font-weight: bold;
        text-align: right;
    }

    .b {
        font-weight: bold;
    }
    .text-center{
        text-align: center;
    }
    #p-head p{
        font-size: 13pt;
    }
</style>
</head>

<body>
    @php
        $size = count($MemberFinalData);  $counter = 1;
        $totalKeys = ['CBUTotal','AverageCBU','ICS','InterestTotal','PR','TotalPayables','Net','CBU_Retention'];
        $GLOBALS['grandtotals'] = array();

        foreach($totalKeys as $key){
            $GLOBALS['grandtotals'][$key] = 0;
        }


    @endphp

        <header>
            <div class="header" style="margin-top: 100px !important;">
                <p style="font-size: 20px;margin-top: -15px">
                    <b>{{ config('variables.coop_name') }}</b>
                </p>
                <p style="font-size: 17px;margin-top: -21px">{{ config('variables.coop_address') }}
                </p>
                <p style="font-size: 20px;margin-top: -15px"><b>Patronage Refund and Interest on Capital Share
                        Allocation</b></p>
                <p style="font-size: 17px;margin-top: -21px">{{$sel_year}}</p>

            </div>
        </header>
        <?php
		$total = 0;
        $TOTALSHT = 0;
		$totalPayment = 0;
	?>
        <div class="row" style="margin-top: 0.5cm;" id="p-head">
            <div class="columnLeft">
                <p class="my-0"><b>Total ASM</b>: <span id="total-asm">{{number_format($totals['AverageCBU'],2)}}</span> </p>
                <p class="my-0"><b>Interest on Capital Share: </b>
                    {{ number_format($icsp,2) }} <i>(Rate @
                        {{ round($ISCRate * 100,2) }}
                        %)</i></p>
                <p class="my-0"><b>Total Interest Income</b>: <span id="total-pr">{{number_format($totals['InterestTotal'],2)}}</span> </p>
                <p class="my-0"><b>Patronage Refund: </b> {{ number_format($prp,2) }}
                    <i>(Rate @ {{ round($PRRate *100,2) }}
                        %)</i></p>

            </div>
            <div class="columnRight">
                <p class="my-0"><b>Net</b>: <span id="total-net">{{number_format($totals['Net'],2)}}</span> </p>
                <p class="my-0"><b>CBU Retention</b>: <span id="total-cbu">{{number_format($totals['CBU_Retention'],2)}}</span> </p>
                <p class="my-0"><b>Total Payables</b>: <span id="total-payables">{{number_format($totals['TotalPayables'],2)}}</span> </p>




            </div>
        </div>
        <div class="row">
            @foreach($MemberFinalData as $group=>$MemberTable)
            <div style="page-break-inside: avoid">
                <h4 style="margin-bottom:0">{{strtoupper($group)}}</h4>
                @include('patronage_refunds.export-create-table',['MemberTable'=>$MemberTable])
                </div>
            @endforeach
        </div>

        @php
            $counter++;
        @endphp

</body>


</script>
</html>
