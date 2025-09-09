
<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<style type="text/css">
		@page{
			margin-right: 30% !important;
			margin-left: 20% !important;
		}
		.container{
			display: flex;
			justify-content: center;
			height: 100% !important;
			min-height: 100% !important;
		}
		}*/
		.button {
			border-radius: 3px;
			box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
			color: #fff;
			display: inline-block;
			text-decoration: none;
			-webkit-text-size-adjust: none;
		}
		.btn-link,
		.button-primary {
			background-color: #3490dc;
			border-top: 10px solid #3490dc;
			border-right: 18px solid #3490dc;
			border-bottom: 10px solid #3490dc;
			border-left: 18px solid #3490dc;
			color : white !important;
		}

		.div_email_body p, .div_email_body i{
			font-size: 15px !important;
			font-family: Arial;
			text-align: justify;
			color: black;
		}
		.div_body{
			background-color: white;
			width: 80%;
		}
		@media only screen and (max-width: 600px) {
			.div_body{
				background-color: white;
				width: 100%;
			}
		}
		.nb{
			margin-bottom: 0px !important;
			margin-top: 0rem !important;
		}
		.text-success{
			color : green;
		}
		.text-danger{
			color : red;
		}
		.text-primary{
			color: blue;
		}
		table, td, th {
		  border: 1px solid !important;
		  padding-right: 0.5rem;
		  padding-left: 0.5rem;
		  font-size: 0.85rem;
		}

		table {
		  width: 100%;
		  border-collapse: collapse;
		}
	</style>

	</head>
	<?php
		$outstanding = collect($loans)->sum('month_total_due');
		$payments = collect($loans)->sum('current_payment');
		$deficit = collect($loans)->sum('total_due');

		$border = 'style="border: 1px solid;"';


	?>
	<body style="background-color: white;margin:0 auto;">
		<div class="container">
			<div class="div_body">
				<div style="padding: 0px 10px 10px 16px;" class="div_email_body">
					<p>Dear {{$member_name}} </p>
					<p>This is a friendly reminder regarding your loan account with the following details: </p>
	<!-- 				<p>You have a due amounting to ₱{{number_format($deficit,2)}}</p>
					<p>We appreciate your valued patronage of our services.</p> -->
					<div style="overflow-x:auto;">
					<table width="100%" style="border: 1px solid !important;border-collapse: collapse;">
						<thead>
							<tr>
								<th <?php echo $border; ?>>Reference</th>
								<th <?php echo $border; ?>>Loan</th>

								<th <?php echo $border; ?>>Outstanding Balance</th>
								<th <?php echo $border; ?>>Amount Paid</th>

								<th <?php echo $border; ?>>Deficit Amount</th>
							</tr>
						</thead>
						<tbody>
							@foreach($loans as $ov)
							<tr>
								<td style="text-align:center;border: 1px solid">{{$ov->id_loan}}</td>
								<td style="white-space: normal !important;border: 1px solid">{{$ov->loan_name}}</td>


								<td style="text-align: right;border: 1px solid">{{number_format($ov->month_total_due,2)}}</td>
								<td style="text-align: right;border: 1px solid">{{number_format($ov->current_payment,2)}}</td>

								<td style="text-align: right;border: 1px solid">{{number_format($ov->total_due,2)}}</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2" style="text-align: left;border: 1px solid">Total Dues</th>
								<th style="text-align:right;border: 1px solid">₱{{number_format($outstanding,2)}}</th>
								<th style="text-align:right;border: 1px solid">₱{{number_format($payments,2)}}</th>
								<th style="text-align:right;border: 1px solid">₱{{number_format($deficit,2)}}</th>
							</tr>
						</tfoot>
					</table>
					</div>
					<p>We noticed a deficit in your recent loan payment (<b>Total Amount: ₱{{number_format($deficit,2)}}</b>). Please settle the deficit amount within five days to avoid penalties and surcharges. </p>
					<p>Additionally, we emphasize the importance of regular and timely loan repayments. We offer a cash incentive program as part of our commitment to rewarding responsible members/borrowers. Schools whose members consistently pay their loan dues promptly will receive a cash incentive every quarter as a token of appreciation for their financial discipline and cooperation. </p>

					<p>If you have any questions or need assistance, contact our Credit Committee at {{config('variables.coop_contact')}} </p>
					<p>Thank you for your cooperation. </p>
					<br>
					<p class="nb">Sincerely,</p>
					<p class="nb">{{config('variables.coop_abbr')}}</p>
					<hr>
					@include('emails.disclaimer')
					<br>
				</div>
			</div>
		</div>

	</body>
	</html>

