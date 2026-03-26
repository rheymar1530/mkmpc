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
        #tbl-foot, #tbl-foot td{
            border: none !important;
        }
        #details p{
            font-size: 15pt;
        }
    </style>
</head>

<body>
    @php $size = count($MemberFinalData);  $counter = 1; @endphp
    @foreach($MemberFinalData as $group=>$MemberTable)
        <header>
            <div class="header" style="margin-top: 100px !important;">
                <p style="font-size: 20px;margin-top: -15px"><b>{{ config('variables.coop_name') }}</b></p>
                <p style="font-size: 17px;margin-top: -21px">{{ config('variables.coop_address') }}</p>
                <p style="font-size: 20px;margin-top: -15px"><b>Patronage Refund and Interest on Capital Share Allocation</b></p>
                <p style="font-size: 20px;margin-top: -21px"><b><u>{{ $group }}</u></b></p>
            </div>
        </header>
        <?php
            $total = 0;
            $totalPayment = 0;
	    ?>
        <div class="row" style="margin-top: 0.5cm;" id="details">
            <div class="columnLeft">
                <p class="my-0"><b>ID: </b>{{ $details->id_patronage_capital_allocation }}</p>
                <p class="my-0"><b>Year: </b>{{ $details->year }}</p>
            </div>
            <div class="columnRight">
                <p class="my-0"><b>Interest on Capital Share: </b>
                    {{ number_format($details->capital_share_p,2) }} <i>(Rate @
                        {{ round($details->capital_share_rate * 100,2) }}
                        %)</i></p>
                <p class="my-0"><b>Patronage Refund: </b> {{ number_format($details->patronage_refund_p,2) }}
                    <i>(Rate @ {{ round($details->patronage_refund_rate *100,2) }}
                        %)</i></p>

            </div>
        </div>
        <div class="row">
            @include('patronage_refunds.allocation-table-print',['allocations'=>$MemberTable])
        </div>



    <div style="margin-top: 80px; width: 100%;">
        <table id="tbl-foot" style="width: 100%; text-align: center;">
            <tr>
                <td style="width: 33%;">
                    <div style="display: inline-block; text-align: center;">
                        <div style="border-bottom: 1px solid #000; width: 400px; margin: 0 auto;">
                            {{ $prepared_by ?? '' }}
                        </div>
                        <div><b>Prepared by</b></div>
                    </div>
                </td>

                <td style="width: 33%;">
                    <div style="display: inline-block; text-align: center;">
                        <div style="border-bottom: 1px solid #000; width: 300px; margin: 0 auto;">
                            {{ $checked_by ?? '' }}
                        </div>
                        <div><b>Checked by</b></div>
                    </div>
                </td>

                <td style="width: 33%;">
                    <div style="display: inline-block; text-align: center;">
                        <div style="border-bottom: 1px solid #000; width: 300px; margin: 0 auto;">
                            {{ $approved_by ?? '' }}
                        </div>
                        <div><b>Approved by</b></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

        @if($counter != $size)
            <div style="page-break-after: always"></div>
        @endif

        @php
            $counter++;
        @endphp
    @endforeach
</body>

</html>
