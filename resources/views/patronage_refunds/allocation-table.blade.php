<style>
    .table-allocation th,
    td {
        font-size: 0.9rem;


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
            <th>TSM</th>
            <th>ASM</th>
            <th>ISC</th>
            <th>INTEREST ON LOAN</th>
            <th>PR</th>
            <th>TOTAL</th>
            <th>RATE</th>
            <th>NET</th>
            <th>CBU</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalKeys = ['CBUTotal','AverageCBU','ICS','InterestTotal','PR','TotalPayables','Net','CBU_Retention'];
            $gTotals = array();
            $counter = 1;
        @endphp




        @foreach($MemberFinalData as $group=>$MemberTable)
            <tr class="table-primary">
                <td colspan="11" class="font-weight-bold">{{ $group }}</td>
            </tr>

            @foreach($MemberTable as $i=>$m)
                <tr>
                    <td class="text-center">{{ $counter }}</td>
                    <td>{{ $m['member'] }}</td>
                    <td class="text-right">{{ number_format($m['CBUTotal'],2) }}</td>
                    <td class="text-right">{{ number_format($m['AverageCBU'],2) }}</td>
                    <td class="text-right b">{{ number_format($m['ICS'],2) }}</td>
                    <td class="text-right">{{ number_format($m['InterestTotal'],2) }}</td>
                    <td class="text-right b">{{ number_format($m['PR'],2) }}</td>
                    <td class="text-right b">{{ number_format($m['TotalPayables'],2) }}</td>
                    <td class="text-right">{{ $m['NetRate'] }}%</td>
                    <td class="text-right b">{{ number_format($m['Net'],2) }}</td>
                    <td class="text-right b">{{ number_format($m['CBU_Retention'],2) }}</td>
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

    <tfoot>
        <tr class="footer">
            <td colspan="2"></td>
            <td>{{ number_format($gTotals['CBUTotal'] ?? 0,2) }}</td>
            <td>{{ number_format($gTotals['AverageCBU'] ?? 0,2) }}</td>
            <td>{{ number_format($gTotals['ICS'] ?? 0,2) }}</td>
            <td>{{ number_format($gTotals['InterestTotal'] ?? 0,2) }}</td>
            <td>{{ number_format($gTotals['PR'] ?? 0,2) }}</td>
            <td>{{ number_format($gTotals['TotalPayables'] ?? 0,2) }}</td>
        </tr>
    </tfoot>
</table>
