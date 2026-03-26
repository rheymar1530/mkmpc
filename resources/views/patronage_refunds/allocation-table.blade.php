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
            <th>INTEREST INCOME</th>
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
            $gTotals = [];
            $counter = 1;
        @endphp

        @foreach($MemberFinalData as $group=>$MemberTable)

            {{-- 🔹 Reset group totals --}}
            @php
                $groupTotals = [];
            @endphp

            {{-- 🔹 Group Header --}}
            <tr class="table-primary">
                <td colspan="11" class="font-weight-bold">{{ $group }}</td>
            </tr>

            {{-- 🔹 Members --}}
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

                {{-- 🔹 Totals computation --}}
                @foreach($totalKeys as $tk)
                    @php
                        // grand total
                        $gTotals[$tk] = ($gTotals[$tk] ?? 0) + $m[$tk];

                        // group total
                        $groupTotals[$tk] = ($groupTotals[$tk] ?? 0) + $m[$tk];
                    @endphp
                @endforeach

                @php $counter++; @endphp
            @endforeach

            {{-- 🔹 Subtotal Row --}}
            <tr class="footer" style="background-color: #f5f5f5;">
                <td colspan="2" class="text-right b">Subtotal</td>
                <td>{{ number_format($groupTotals['CBUTotal'] ?? 0,2) }}</td>
                <td>{{ number_format($groupTotals['AverageCBU'] ?? 0,2) }}</td>
                <td>{{ number_format($groupTotals['ICS'] ?? 0,2) }}</td>
                <td>{{ number_format($groupTotals['InterestTotal'] ?? 0,2) }}</td>
                <td>{{ number_format($groupTotals['PR'] ?? 0,2) }}</td>
                <td>{{ number_format($groupTotals['TotalPayables'] ?? 0,2) }}</td>
                <td></td>
                <td>{{ number_format($groupTotals['Net'] ?? 0,2) }}</td>
                <td>{{ number_format($groupTotals['CBU_Retention'] ?? 0,2) }}</td>
            </tr>

        @endforeach
    </tbody>

    {{-- 🔹 GRAND TOTAL --}}
    <tfoot>
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
    </tfoot>
</table>