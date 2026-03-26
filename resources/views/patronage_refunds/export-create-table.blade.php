<table class="table table-bordered table-allocation table-head-fixed mt-3" style="width:34cm">
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
        @php
            $gTotals = array();
            $counter = 1;
            $TOTALSHT = 10000;
        @endphp
        @foreach($MemberTable as $i=>$m)
            <tr>
                <td class="text-center">{{ $counter }}</td>
                <td>{{ $m['member'] }}</td>
                <td class="class_amount">{{ number_format($m['CBUTotal'],2) }}</td>
                <td class="class_amount">{{ number_format($m['AverageCBU'],2) }}</td>
                <td class="class_amount b">{{ number_format($m['ICS'],2) }}</td>
                <td class="class_amount">{{ number_format($m['InterestTotal'],2) }}</td>
                <td class="class_amount b">{{ number_format($m['PR'],2) }}</td>
                <td class="class_amount b">{{ number_format($m['TotalPayables'],2) }}</td>
                <td class="class_amount">{{ $m['NetRate'] }}%</td>
                <td class="class_amount b">{{ number_format($m['Net'],2) }}</td>
                <td class="class_amount b">{{ number_format($m['CBU_Retention'],2) }}</td>
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
            <td>{{ number_format($gTotals['CBUTotal'] ?? 0,2) }}</td>
            <td>{{ number_format($gTotals['AverageCBU'] ?? 0,2) }}</td>
            <td>{{ number_format($gTotals['ICS'] ?? 0,2) }}</td>
            <td>{{ number_format($gTotals['InterestTotal'] ?? 0,2) }}</td>
            <td>{{ number_format($gTotals['PR'] ?? 0,2) }}</td>
            <td>{{ number_format($gTotals['TotalPayables'] ?? 0,2) }}</td>
            <td></td>
            <td>{{ number_format($gTotals['Net'] ?? 0,2) }}</td>
            <td>{{ number_format($gTotals['CBU_Retention'] ?? 0,2) }}</td>
        </tr>

        <?php
            foreach($totalKeys as $key){
                $GLOBALS['grandtotals'][$key] += $gTotals[$key];
            }
        ?>
</table>