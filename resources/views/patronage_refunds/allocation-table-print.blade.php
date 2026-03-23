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

</style>

<table class="table table-bordered table-allocation table-head-fixed mt-3">
    <thead>
        <tr>
            <th></th>
            <th>Member</th>
            <th>Total<br>Capital Share</th>
            <th>Ave<br>Monthly CBU</th>
            <th>Interest<br>on Capital Share</th>
            <th>Total<br>Interest on Loan</th>
            <th>Patronage Refund</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalKeys =
            ['capital_share','ave_monthly_cbu','interest_capital_share','loan_interest','patronage_refund','total'];
            $gTotals = array();
            $counter = 1;
        @endphp



        @foreach($MemberFinalData as $group=>$MemberTable)
            <tr class="table-primary">
                <td colspan="8" class="font-weight-bold">{{ $group }}</td>
            </tr>

            @foreach($MemberTable as $i=>$m)
                <tr>
                    <td class="text-center">{{ $counter }}</td>
                    <td>{{ $m['Name'] }}</td>
                    <td class="class_amount">{{ number_format($m['capital_share'],2) }}</td>
                    <td class="class_amount">{{ number_format($m['ave_monthly_cbu'],2) }}</td>
                    <td class="class_amount b">
                        {{ number_format($m['interest_capital_share'],2) }}</td>
                    <td class="class_amount">{{ number_format($m['loan_interest'],2) }}</td>
                    <td class="class_amount b">{{ number_format($m['patronage_refund'],2) }}
                    </td>
                    <td class="class_amount b">{{ number_format($m['total'],2) }}</td>
                </tr>
                @foreach($totalKeys as $tk)
                    <?php
                        $gTotals[$tk] = ($gTotals[$tk] ?? 0) + $m[$tk];
                    ?>
                @endforeach
                <?php $counter++; ?>
            @endforeach
        @endforeach
    </tbody>


    <tr class="footer">
        <td colspan="2"></td>
        <td>{{ number_format($gTotals['capital_share'] ?? 0,2) }}</td>
        <td>{{ number_format($gTotals['ave_monthly_cbu'] ?? 0,2) }}</td>
        <td>{{ number_format($gTotals['interest_capital_share'] ?? 0,2) }}</td>
        <td>{{ number_format($gTotals['loan_interest'] ?? 0,2) }}</td>
        <td>{{ number_format($gTotals['patronage_refund'] ?? 0,2) }}</td>
        <td>{{ number_format($gTotals['total'] ?? 0,2) }}</td>
    </tr>

</table>
