<table class="table table-bordered table-allocation table-head-fixed mt-3" style="width:34cm">
    <thead>
        <tr>
            <th><b></b></th>
            <th><b>Member</b></th>
            <th><b>TSM</b></th>
            <th><b>ASM</b></th>
            <th><b>ISC</b></th>
            <th><b>INTEREST INCOME</b></th>
            <th><b>PR</b></th>
            <th><b>TOTAL</b></th>
            <th><b>RATE</b></th>
            <th><b>NET</b></th>
            <th><b>CBU</b></th>
        </tr>
    </thead>
        <?php
            if (!function_exists('formatAmount')) {
                function formatAmount($amount, $isExcel = false) {
                    return $isExcel ? $amount : number_format($amount, 2);
                }
            }
        ?>
        @php
            $counter = 1;
            $totalKeys = ['CBUTotal','AverageCBU','ICS','InterestTotal','PR','TotalPayables','Net','CBU_Retention'];
        @endphp
        @php
            $gTotals = array();


        @endphp
        @foreach($MemberTable as $i=>$m)
            <tr>
                <td class="text-center">{{ $counter }}</td>
                <td>{{ $m['member'] }}</td>
                <td class="class_amount">{{ formatAmount($m['CBUTotal'],$isExcel) }}</td>
                <td class="class_amount">{{ formatAmount($m['AverageCBU'],$isExcel) }}</td>
                <td class="class_amount b"><b>{{ formatAmount($m['ICS'],$isExcel) }}</b></td>
                <td class="class_amount">{{ formatAmount($m['InterestTotal'],$isExcel) }}</td>
                <td class="class_amount b"><b>{{ formatAmount($m['PR'],$isExcel) }}</b></td>
                <td class="class_amount b"><b>{{ formatAmount($m['TotalPayables'],$isExcel) }}</b></td>
                <td class="class_amount">{{ $m['NetRate'] }}%</td>
                <td class="class_amount b"><b>{{ formatAmount($m['Net'],$isExcel) }}</b></td>
                <td class="class_amount b"><b>{{ formatAmount($m['CBU_Retention'],$isExcel) }}</b></td>
            </tr>
            @foreach($totalKeys as $tk)
                <?php
                    $gTotals[$tk] = ($gTotals[$tk] ?? 0) + $m[$tk];
                ?>
            @endforeach
            <?php $counter++; ?>

        @endforeach
        <tr class="footer">
            <td colspan="2"></td>
            <td><b>{{ formatAmount($gTotals['CBUTotal'] ?? 0,$isExcel) }}</b></td>
            <td><b>{{ formatAmount($gTotals['AverageCBU'] ?? 0,$isExcel) }}</b></td>
            <td><b>{{ formatAmount($gTotals['ICS'] ?? 0,$isExcel) }}</b></td>
            <td><b>{{ formatAmount($gTotals['InterestTotal'] ?? 0,$isExcel) }}</b></td>
            <td><b>{{ formatAmount($gTotals['PR'] ?? 0,$isExcel) }}</b></td>
            <td><b>{{ formatAmount($gTotals['TotalPayables'] ?? 0,$isExcel) }}</b></td>
            <td><b></b></td>
            <td><b>{{ formatAmount($gTotals['Net'] ?? 0,$isExcel) }}</b></td>
            <td><b>{{ formatAmount($gTotals['CBU_Retention'] ?? 0,$isExcel) }}</b></td>
        </tr>

</table>