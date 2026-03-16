<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>DTR Print</title>

    <style>
        @@media print {

            body * {
                visibility: hidden;
            }

            .paper,
            .paper * {
                visibility: visible;
            }

            .paper {
                position: absolute;
                left: 0;
                top: 0;
            }

        }

        /* ============================= */
        /* MAIN VARIABLES                */
        /* ============================= */

        :root {

            --paper-width: 13.5cm;
            --paper-height: 21.2cm;

            --offset-left: 0.5cm;

            --first-row-top: 6.5cm;
            --row-height: 0.8cm;

            /* COLUMN POSITIONS */
            --col-day: 0.3cm;
            --col-am-in: 2cm;
            --col-am-out: 4cm;
            --col-pm-in: 5.7cm;
            --col-pm-out: 7.7cm;

            /* span width for special text */
            --span-am-pm: 6cm;

            --font-size: 11pt;
            --row-height-last: 0.5cm;
            --row-gap-last: 0.1cm;
            /* 1mm gap from row 15 */
        }

        @@page {
            size: 13.5cm 21.2cm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
        }

        /* ============================= */
        /* PAPER                         */
        /* ============================= */

        .paper {
            width: var(--paper-width);
            height: var(--paper-height);
            position: relative;
            font-family: Arial, sans-serif;
            font-size: var(--font-size);
        }

        /* ============================= */
        /* HEADER                        */
        /* ============================= */

        .field {
            position: absolute;
        }

        /* NAME */

        .name {
            top: 3.4cm;
            left: 2.2cm;
            font-weight: bold;
        }

        /* BRANCH */

        .branch {
            top: 3.4cm;
            right: 1.8cm;
        }

        /* ============================= */
        /* PERIOD                        */
        /* ============================= */

        .period {
            position: absolute;
            top: 4.3cm;
            left: 0;
        }

        .period span {
            position: absolute;
            white-space: nowrap;
        }

        /* FIRST DATE */

        .p-date1 {
            left: 2.5cm;
        }

        .p-year1 {
            left: 6.7cm;
        }

        /* SECOND DATE */

        .p-date2 {
            left: 8.5cm;
        }

        .p-year2 {
            left: 11.5cm;
        }

        /* ============================= */
        /* ROWS                          */
        /* ============================= */

        .row {
            position: absolute;
            left: var(--offset-left);
            top: calc(var(--first-row-top) + (var(--row-index) * var(--row-height)));
        }

        /* CELLS */

        .cell {
            position: absolute;
            height: var(--row-height);
            line-height: var(--row-height);
            white-space: nowrap;
        }

        /* COLUMN ALIGNMENT */

        .day {
            left: var(--col-day);
        }

        .am_in {
            left: var(--col-am-in);
        }

        .am_out {
            left: var(--col-am-out);
        }

        .pm_in {
            left: var(--col-pm-in);
        }

        .pm_out {
            left: var(--col-pm-out);
        }

        /* ============================= */
        /* SPECIAL DESCRIPTION (SUNDAY)  */
        /* ============================= */

        .special-full {
            left: var(--col-am-in);
            width: var(--span-am-pm);
            text-align: center;
            line-height: var(--row-height);
            letter-spacing: 0.25cm;
            font-weight: bold;
            display: flex;
            justify-content: space-evenly;
        }

        /* AM HALF DAY */
        .special-am {
            left: var(--col-am-in);
            width: calc(var(--col-pm-in) - var(--col-am-in));
            text-align: center;
            line-height: var(--row-height);
            font-weight: bold;
        }

        /* PM HALF DAY */
        .special-pm {
            left: var(--col-pm-in);
            width: calc(var(--col-pm-out) - var(--col-pm-in) + 2cm);
            text-align: center;
            line-height: var(--row-height);
            font-weight: bold;
        }

        .row.last {
            top: calc(var(--first-row-top) + (15.6 * var(--row-height)) - 0.6cm);
        }

        .row.last .cell {
            height: 0.5cm;
            line-height: 0.5cm;
            font-size: 9pt;
        }

    </style>
</head>

<body>

    <?php $empCount = 1;
         $empSize = count($employees);
    ?>
    @foreach($employees as $employee_name=>$data)

        <div class="paper">




            <!-- NAME -->
            <div class="field name">{{ $employee_name }}</div>

            <!-- BRANCH -->
            <div class="field branch">{{ $branch }}</div>

            <!-- PERIOD -->
            <div class="field period">

                <span class="p-date1">{{ $dt_s }}</span>
                <span class="p-year1">{{ $year_s }}</span>

                <span class="p-date2">{{ $dt_e }}</span>
                <span class="p-year2">{{ $year_e }}</span>

            </div>


            <!-- ROW DATA -->

            <div>

                @foreach($data as $c=>$d)
                    @php
                        $isLast = $c == 15;
                    @endphp

                    <div class="row {{ $isLast ? 'last' : '' }}"
                        style="--row-index:{{ $c }}">

                        <div class="cell day">{{ $d['day'] }}</div>

                        @if($d['mode'] == "normal")

                            <div class="cell am_in">{{ $d['am_in'] }}</div>
                            <div class="cell am_out">{{ $d['am_out'] }}</div>
                            <div class="cell pm_in">{{ $d['pm_in'] }}</div>
                            <div class="cell pm_out">{{ $d['pm_out'] }}</div>

                        @endif


                        @if($d['mode'] == "full")

                            <div class="cell special-full">
                                {{ $d['description'] }}
                            </div>

                        @endif


                        @if($d['mode'] == "am")

                            <div class="cell special-am">
                                {{ $d['description'] }}
                            </div>

                            <div class="cell pm_in">
                                {{ $d['pm_in'] ?? '' }}</div>
                            <div class="cell pm_out">
                                {{ $d['pm_out'] ?? '' }}</div>

                        @endif


                        @if($d['mode'] == "pm")

                            <div class="cell am_in">
                                {{ $d['am_in'] ?? '' }}</div>
                            <div class="cell am_out">
                                {{ $d['am_out'] ?? '' }}</div>

                            <div class="cell special-pm">
                                {{ $d['description'] }}
                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

        </div>

        @if(count($employees) > 1 && $empSize != $empCount)
            <div style="page-break-before: always"></div>
        @endif
        <?php $empCount++; ?>
    @endforeach


    <script>
        window.addEventListener('load', function () {

            setTimeout(() => {
                window.print();
            }, 300);

            window.onafterprint = function () {
                window.close();
            }

        });

    </script>

</body>

</html>
