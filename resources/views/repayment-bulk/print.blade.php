<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{$file_name}}</title>
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
			margin-top:1in;
			size : letter landscape;
		}
		div.header{
			text-align: center;
			/*line-height: 1px;*/
			font-size: 15px;
			font-family: Calibri, sans-serif;
		}

		* {
			box-sizing: border-box;
			font-family: Calibri, sans-serif;
		}

		.tbl_gl  tr>td,.tbl_gl  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Arial, Helvetica, sans-serif !important;
			letter-spacing: 1px;
			font-size: 0.14in ;
		}



		.class_amount{
			text-align: right;
			padding-right: 2mm !important;
		}

		table.loan, .loan td,.loan th {
			border: 1px solid;
		}

		table.loan {
			width: 100%;
			border-collapse: collapse;
			border : 1px solid;	
		}

		.highlight_amount{
			font-weight: bold;
			text-decoration: underline;
			font-size: 12px !important;
		}
		.bold-text{
			font-weight: bold;
		}
		.tbl_head{
			border-top:  2px solid;
			border-bottom: 2px solid;
		}
		.col_border{
			/*border-left: 1px solid;*/
		}
		.year_head th{
			font-weight: normal !important;
		}
		.text-centered{
			text-align: center;
		}
		#head-tbl th{
			vertical-align: middle !important;
		}
		.mb-0{
			margin-bottom: 0 !important;
		}
		.my-0{
			margin-top: 0 !important;
			margin-bottom: 0 !important;
		}
		.font-weight-bold{
			font-weight: bold !important;
		}
	</style>
</head>
<body>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			<p style="font-size: 20px;margin-top: -15px"><b>{{config('variables.coop_name')}}</b></p>
			<p style="font-size: 17px;margin-top: -21px">{{config('variables.coop_address')}}</p>
			<p style="font-size: 20px;margin-top: -15px"><b>Loan Payment Summary</b></p>

		</div> 
	</header>
	<?php
		$total = 0;


		if($details->id_paymode == 4){
			$PaymentOBJ = array(
				'Check Type'=>'check_type',
				'Check Bank'=>'check_bank',
				'Check Date'=>'check_date',
				'Check No'=>'check_no',
				'Amount'=>'amount',
				'Remarks'=>'remarks'
			);

			$PColCount = 5;
		}else{

		}

		$totalPayment = 0;
	?>	
	<div class="row" style="margin-top: 0.5cm;">
		<div class="columnLeft">
			<p class="my-0"><b>Loan Payment ID: </b>{{$details->id_repayment}}</p>
			<p class="my-0"><b>Date: </b>{{$details->date}}</p>
			<p class="my-0"><b>OR Number: </b>{{$details->or_number}}</p>
		</div>
		<div class="columnRight">
			<p class="my-0"><b>Paymode: </b>{{$details->paymode}}</p>
			<p class="my-0"><b>Total Amount: </b>{{number_format($details->total_amount,2)}}</p>
			<p class="my-0"><b>Remarks: </b>{{$details->remarks}}</p>
		</div>		
	</div>
	<div class="row">
		<table class="tbl-mngr loan" width="100%">
			<thead>
				<tr class="text-center">
					<th>Member</th>
					<th>Loan Service</th>
					<th>Payment</th>
					<th width="10%"></th>
				</tr>
			</thead>
			<tbody>
			@if($details->payment_for_code == 2)
					@foreach($statamentData as $statementDescription=>$MemberData)
					<tr class="statement-head bg-gradient-success2 font-weight-bold">
						<td colspan="4"><b>{{$statementDescription}}</b></td>
					</tr>

						@foreach($MemberData as $m=>$data)
						<?php $length = count($data);?>
							@foreach($data as $c=>$row)

							<tr>
								@if($c == 0)
								<td class="" rowspan="{{$length}}"><i>{{$row->member}} </i></td>
								@endif
								<td>{{$row->loan_name}}</td>
								<td class="class_amount">{{number_format($row->payment,2)}}</td>
								<?php $total += $row->payment; ?>
								@if($c == 0)
								<td class="font-weight-bold text-center" rowspan="{{$length}}">
									@if($row->id_cash_receipt_voucher  > 0)
									CRV-{{$row->id_cash_receipt_voucher}}
									@endif
								</td>
								@endif
							</tr>
							@endforeach
						@endforeach
					@endforeach

				@else
					@foreach($Loans as $member=>$rows)
					<?php
						$length = count($rows);
					?>
						@foreach($rows as $c=>$row)
						<tr>
							@if($c == 0)
							<td class="" rowspan="{{$length}}"><i>{{$row->member}} </i></td>
							@endif
							<td>{{$row->loan_name}}</td>
							<td class="class_amount">{{number_format($row->payment,2)}}</td>
							<?php $total += $row->payment; ?>
							@if($c == 0)
								<td class="font-weight-bold text-center" rowspan="{{$length}}">
									@if($row->id_cash_receipt_voucher  > 0)
									CRV-{{$row->id_cash_receipt_voucher}}
									@endif
								</td>
							@endif
						</tr>
						@endforeach
					@endforeach

				@endif
			</tbody>
			<tr>
				<td colspan="2" class="bold-text">Total</td>
				<td class="class_amount bold-text">{{number_format($total,2)}}</td>
				<td></td>
			</tr>

		</table>
	</div>

	@if($details->id_paymode ==4)
	<div class="row" style="margin-top:1cm">
		<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
			<thead>
				<tr class="text-center">
					<th></th>
					@foreach($PaymentOBJ as $head=>$key)
					<th>{{$head}}</th>
					@endforeach
				</tr>
			</thead>
			<tbody>
				@foreach($paymentDetails as $i=>$pd)
				<tr>
					<td class="text-centered">{{$i+1}}</td>
					@foreach($PaymentOBJ as $key)
					<td class="<?php echo ($key=='amount')?'class_amount':''; ?>" >
						@if($key=='amount')
						{{ number_format($pd->{$key},2) }}
						<?php
						$totalPayment += $pd->{$key};
						?>
						
						@else
						{{ $pd->{$key} }}
						@endif
						</td>
					@endforeach
				</tr>
				@endforeach
			</tbody>
			@if(count($paymentDetails) > 1)
			<tr>
				<td class="font-weight-bold" colspan="{{$PColCount}}">Total Payment</td>
				<td class="class_amount font-weight-bold">{{number_format($totalPayment,2)}}</td>
				<td></td>
			</tr>
			@endif
			@if($details->change_payable > 0)
			<tr class="text-success">
				<td colspan="{{$PColCount}}" class="font-weight-bold">Change</td>
				<td class="class_amount font-weight-bold">{{number_format($details->change_payable,2)}}</td>
				<td></td>
			</tr>
			@endif
		</table>
	</div>
	@endif
<table width="100%" style="border-spacing: 0.5cm !important;border-collapse: separate; margin-top: 1.5cm;">
	<tr>
		<td class="text_ex" style="border-bottom: 1px solid"></td>
		<td class="text_ex"></td>
		
		<td class="text_ex"></td>
	</tr>
</table>
<table width="100%" style="margin-top: -0.3cm;font-size:3.9mm !important">
	<tr>
		<td class="text_ex" style="width: 33.33%;text-align: center;">Prepared by</td>
		<td class="text_ex" style="width: 33.33%;text-align: center"></td>
		
		<td class="text_ex" style="width: 33.33%;text-align: center"></td>
		
	</tr>
</table>
</body>
</html>