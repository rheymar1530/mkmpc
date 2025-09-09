@if($exportMode <= 1)
<?php
	function number_formats($val){
		return number_format($val,2);
	}

	$style = "";
?>
@else
<?php
	function number_formats($val){
		return $val;
	}
	$style = 'style="text-align:center"';
?>
@endif

<?php
	$ref = '';

	if($exportMode == 0){
		$ref = "href='#' onclick='view(this)'";
	}

	$AmtKeys = ['prev_due_balance','cur_due_balance','payment'];
	$gTotal = [];

	foreach($AmtKeys as $field){
		$gTotal[$field] = 0;
	}
	$gTotal['total_due'] = 0;

?>
<table class="table table-bordered table-head-fixed" id="tbl_overdues" width="100%">
	@if($exportMode == 1)
	<colgroup>

	</colgroup>
	@endif
	<thead>
		<tr class="nb-bottom">
			<th class="table_header_dblue" <?php echo $style; ?>><b>BORROWER</b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>LOAN TYPE</b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>TERMS</b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>REFERENCE</b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>LOAN PERIOD</b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>PREVIOUS DUE</b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>CURRENT DUE <small>({{$asOf}})</small></b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>TOTAL DUE</b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>PAYMENT</b></th>
		</tr>
	</thead>
	<tbody>
		@foreach($repayment_data as $service=>$rows)
		<tr class="">
			<td colspan="7"><b>{{$service}}</b></td>
		</tr>
		<?php
			$maxCount = count($rows) - 1;
			$sTotal = [];

			foreach($AmtKeys as $field){
				$sTotal[$field] = 0;
			}
			$sTotal['total_due'] = 0;
		?>
		@foreach($rows as $c=>$row)
			<?php
				foreach($AmtKeys as $field){
					$sTotal[$field] += $row->{$field};
					$gTotal[$field] += $row->{$field};
				}

				$TOTALDUE =		$row->prev_due_balance+$row->cur_due_balance;
				$sTotal['total_due'] += $TOTALDUE;
				$gTotal['total_due'] += $TOTALDUE;
			?>
			<tr tk="{{$row->loan_token}}">
				<td>{{$row->member_name}}</td>
				<td>{{$row->service_name}}</td>
				<td class="text-center">{{$row->terms}}</td>
				<td class="text-center"><a <?php echo $ref; ?>>{{$row->id_loan}}</a></td>
				<td>{{$row->date_released}} - {{$row->maturity_date}}</td>
				<td class="class_amount">{{number_formats($row->prev_due_balance)}}</td>
				<td class="class_amount">{{number_formats($row->cur_due_balance)}}</td>
				<td class="class_amount">{{number_formats($TOTALDUE)}}</td>
				<td class="class_amount">{{number_formats($row->payment)}}</td>
			</tr>
		@endforeach

		<tr class="b-bottom">
			<td colspan="5"><b>SUB-TOTAL</b></td>
			<td class="class_amount"><b>{{number_formats($sTotal['prev_due_balance'])}}</b></td>
			<td class="class_amount"><b>{{number_formats($sTotal['cur_due_balance'])}}</b></td>
			<td class="class_amount"><b>{{number_formats($sTotal['total_due'])}}</b></td>
			<td class="class_amount"><b>{{number_formats($sTotal['payment'])}}</b></td>
		</tr>

		@endforeach
	</tbody>

	@if($exportMode == 2)
	<tr>
		@for($i=0;$i<9;$i++)
		
		@endfor
	</tr>
	@endif

	<tr class="b-bottom">
		<td colspan="5"><b>GRAND TOTAL</b></td>
		<td class="class_amount"><b>{{number_formats($gTotal['prev_due_balance'])}}</b></td>
		<td class="class_amount"><b>{{number_formats($gTotal['cur_due_balance'])}}</b></td>
		<td class="class_amount"><b>{{number_formats($gTotal['total_due'])}}</b></td>
		<td class="class_amount"><b>{{number_formats($gTotal['payment'])}}</b></td>
	</tr>

</table>