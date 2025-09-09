
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
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
/*	.btn-link{
		margin-top: -10px;
		margin-bottom: 5px;
		color: white !important;
		background-color: #007bff;
		border-color: #007bff;
		box-shadow: none;   
		padding: .25rem .5rem;
		font-size: .875rem;
		line-height: 1.5;
		border-radius: .2rem;
		text-decoration: none;
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
</style>

</head>

<?php

?>
<body style="background-color: white;margin:0 auto;">
	<div class="container">
		<div class="div_body">
			<div style="padding: 0px 10px 10px 16px;" class="div_email_body">
				<p>Dear {{$details->member_name}} </p>
		
				<p>We would like to inform you that we have received your recent payment for your loan with the following details: </p>

				<p class="nb"><b>Transaction ID: </b>{{$details->id_repayment_transaction}}</p>
				<p class="nb"><b>Payment Date: </b>{{$details->transaction_dt}}</p>
				<p class="nb"><b>Payment Type: </b>{{$details->paymode}}</p>

				@if($details->transaction_type == 2)
				<p class="nb"><b>Swiping Amount: </b>{{number_format($details->swiping_amount,2)}}</p>
				@endif
				<p class="nb"><b>Total Amount Paid: </b>{{number_format($details->total_payment,2)}}</p>
				@if($details->change > 0)
				<p class="nb"><b>Change: </b>{{number_format($details->change,2)}}</p>
				@endif
				@if(isset($details->or_no))
				<p class="nb"><b>OR No: </b>{{$details->or_no}}</p>

				@endif
				<br>


				<a class="btn-link button" href="{{$currentDomain}}/payments/{{$details->id_repayment_transaction}}">Click to View Loan Payment Details</a>
				<p>Thank You for your payment. It has been successfully credited to your loan account.</p>
				<p>If you have any questions or need further assistance, please don't hesitate to contact our Credit Committee at <b>{{config('variables.coop_contact')}}</b>. </p>
				<p>We appreciate your valued patronage of our services.</p>

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

