
<?php
function check_amt($amt){
	return ($amt == 0)?'':number_format($amt,2);
}

$g_total_debit = 0;
$g_total_credit = 0;
?>
@foreach($general_ledger as $account=>$gl)
<tr>
	<th colspan="6" style="text-align:left">{{$account}}</th>
</tr>
<?php
$total_debit = 0;
$total_credit = 0;
?>
@foreach($gl as $row)
<tr>
	<td>{{$row->date}}</td>
	<td>{{$row->description}}</td>
	<td>{{$row->post_reference}}</td>
	<td class="class_amount"><?php echo check_amt($row->debit); ?></td>
	<td class="class_amount"><?php echo check_amt($row->credit); ?></td>
	<td>{{$row->remarks}}</td>
</tr>
<?php
$total_debit += $row->debit;
$total_credit += $row->credit;
?>
@endforeach
<tr style="text-align:left" class="add_border_bottom">
	<th colspan="3">TOTAL</th>
	<th class="class_amount">{{check_amt($total_debit)}}</th>
	<th class="class_amount">{{check_amt($total_credit)}}</th>
	<th></th>
</tr>
<?php
	$g_total_debit += $total_debit;
	$g_total_credit += $total_credit;
?>
@endforeach


<tr style="text-align:left;" >
	<th colspan="3" class="row_g_total">GRAND TOTAL</th>
	<th class="class_amount row_g_total">{{check_amt($g_total_debit)}}</th>
	<th class="class_amount row_g_total">{{check_amt($g_total_credit)}}</th>
	<th></th>
</tr>
