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
	if($type == 1){
		// per borrower
		$groupFieldHead = "Borrower";
		$groupField = 'member';

		$referenceHead = "Loan Service";
		$reference = 'loan_service';
	}elseif($type >= 2){
		// per loan service
		$groupFieldHead = "Loan Service";
		$groupField = 'loan_service';

		$referenceHead = "Borrower";
		$reference = 'member';
	}else{
		// per Brgy
		$groupFieldHead = "Loan Service";
		$groupField = 'loan_service';

		$referenceHead = "Borrower";
		$reference = 'member';
	}
	
?>
<?php
	$ref = '';

	if($exportMode == 0){
		$ref = "href='#' onclick='view(this)'";
	}

	$rspanTotal = ($type == 1)?2:(($type==2)?1:2);
?>
<table class="table table-bordered table-head-fixed" id="tbl_overdues" width="100%">
	@if($exportMode == 1)
	<colgroup>
		<col width="25%">
		@if($type == 1)
		<col width="20%">
		@endif
	</colgroup>
	@endif
	<thead>
		<tr class="nb-bottom">

			@if($type == 1)
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>{{$groupFieldHead}}</b></th>
			@endif
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>{{$referenceHead}}</b></th>

			@if($type == 3)
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>LOAN SERVICE</b></th>
			@endif
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>PRINCIPAL <br>LOAN</b></th>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>Loan Period</b></th>

			<th class="table_header_dblue" colspan="3" <?php echo $style; ?>><b>INTEREST AS OF {{strtoupper($asOf)}}</b></th>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>SURCHARGE</b></th>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>TOTAL</b></th>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>PAYMENT</b></th>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>BALANCE</b></th>
		</tr>

		<tr class="nb-top">
			<th class="table_header_dblue" <?php echo $style; ?>><b>INTEREST</b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>MONTH</b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>TOTAL</b></th>
		</tr>
	</thead>

	<tbody>
		<?php
			$counter = 1;
			$totals = array();

			$sumField = [
				'principal_amount','interest_amount','interest_total','total','payment','balance','surcharge_total'
			];

			foreach($sumField as $f){
				$totals[$f] = 0;
			}
		?>
		@foreach($overdues as $idMember=>$loans)
		<?php
			$Stotal = [];
			foreach($sumField as $f){
				$Stotal[$f] = 0;
			}
		?>
		@if($type >= 2)
		<tr>
			<td colspan="9"><b>{{$idMember}}</b></td>
		</tr>
		@endif
		<?php
			$lCount = ($type == 1)?count($loans):1;
		?>
			@foreach($loans as $c=>$loan)
				<?php
					$class= ($c == 0 && $type == 1)?'bb':'';
				?>
				<tr class="{{$class}}" tk="{{$exportMode==0?$loan->loan_token:0}}">
					
					@if($c == 0 && $type == 1)
					<!-- <td rowspan="{{$lCount}}">{{$counter}}.</td> -->
					<td rowspan="{{$lCount}}" >&nbsp;{{$loan->{$groupField} }}</td>
					@endif
					<td>{{$loan->{$reference} }} &nbsp;
						@if($type < 3)<a <?php echo $ref;?> ><sup>[{{$loan->id_loan}}]</sup></a>@endif</td>

					@if($type == 3)
					<td>{{$loan->loan_service}} @if($type == 3)<a <?php echo $ref;?> ><sup>[{{$loan->id_loan}}]</sup></a>@endif</td>
					@endif
					<td class="class_amount">{{number_formats($loan->principal_amount)}}</td>
					<td class="font-xs">{{date("m/d/Y", strtotime($loan->start_period))}} to {{date("m/d/Y", strtotime($loan->maturity_date))}}</td>
					<td class="class_amount">{{number_formats($loan->interest_amount)}}</td>
					<td class="class_amount">{{$loan->month}}</td>
					<td class="class_amount">{{number_formats($loan->interest_total)}}</td>
					<td class="class_amount">{{number_formats($loan->surcharge_total)}}</td>
					<td class="class_amount">{{number_formats($loan->total,2)}}</td>
					<td class="class_amount">{{number_formats($loan->payment)}}</td>
					<td class="class_amount">{{number_formats($loan->balance)}}</td>

				</tr>
				<?php
					foreach($sumField as $field){
						$Stotal[$field] += $loan->{$field};
						$totals[$field] += $loan->{$field};
					}
					$counter++;
				?>

			@endforeach
		

			@if(count($loans) > 1)
				@if($type >= 2)
				<tr class="bbottom">
					<td colspan="{{$rspanTotal}}" style="font-weight: bold">Sub-Total</td>
					<td class="class_amount" style="font-weight: bold">{{number_formats($Stotal['principal_amount'])}}</td>
					<td></td>
					<td class="class_amount" style="font-weight: bold">{{number_formats($Stotal['interest_amount'])}}</td>
					<td></td>
					<td class="class_amount" style="font-weight: bold">{{number_formats($Stotal['interest_total'])}}</td>
					<td class="class_amount" style="font-weight: bold">{{number_formats($Stotal['surcharge_total'])}}</td>
					<td class="class_amount" style="font-weight: bold">{{number_formats($Stotal['total'])}}</td>
					<td class="class_amount" style="font-weight: bold">{{number_formats($Stotal['payment'])}}</td>
					<td class="class_amount" style="font-weight: bold">{{number_formats($Stotal['balance'])}}</td>

				</tr>	
				@endif
			@endif			
		

		@endforeach
	</tbody>
	<tr class="btop">
		<td colspan="{{$rspanTotal}}" style="font-weight: bold">Grand Total</td>
		<td class="class_amount" style="font-weight: bold">{{number_formats($totals['principal_amount'])}}</td>
		<td></td>
		<td class="class_amount" style="font-weight: bold">{{number_formats($totals['interest_amount'])}}</td>
		<td></td>
		<td class="class_amount" style="font-weight: bold">{{number_formats($totals['interest_total'])}}</td>
		<td class="class_amount" style="font-weight: bold">{{number_formats($totals['surcharge_total'])}}</td>
		<td class="class_amount" style="font-weight: bold">{{number_formats($totals['total'])}}</td>
		<td class="class_amount" style="font-weight: bold">{{number_formats($totals['payment'])}}</td>
		<td class="class_amount" style="font-weight: bold">{{number_formats($totals['balance'])}}</td>

	</tr>
</table>