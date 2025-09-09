
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
		margin-bottom: 0 !important;
		margin-top: 0 !important;
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
	$status_badge = [
		0=>"info",
		1=>"primary",
		2=>"success",
		3=>"success",
		4=>"danger",
		5=>"danger",
		6=>"primary",
	];
	
	$status = $details->status;

?>
<body style="background-color: white;margin:0 auto;">
	<div class="container">
		<div class="div_body">
			<div style="padding: 0px 10px 10px 16px;" class="div_email_body">
				<p>Dear {{$details->member_name}} </p>

				@if($details->status == 0)
				<!-- SUBMITTED -->
				<p>We would like to inform you that we have received your loan application with the following details:</p>
				@include('emails.mail_loan_details')
				<p>Please be advised that our Credit Committee is currently reviewing your application. We will promptly provide you with an update on the status of your loan application as soon as their evaluation is complete. </p>
				<p>If you did not submit this application, kindly contact our Credit Committee immediately at <b>{{config('variables.coop_contact')}}</b>.</p>

				
				@elseif($details->status == 2)
				<!-- LOAN APPROVED -->
				<p>Congratulations! We are pleased to inform you that your loan application with the following details has been <b>APPROVED</b>:</p>
				@include('emails.mail_loan_details')
				<p>To proceed with the loan disbursement, please submit the required documents listed below to our {{config('variables.coop_abbr')}} office at Leon Central School, Poblacion Leon, Iloilo:</p>
				<ul>
					<li>Complete Loan Agreement</li>
					<li>Proof of Identity</li>
					<li>Income Verification (Payslips)</li>
					<li>Any Other Required Documents (if applicable)</li>
				</ul>
				<p>If you have any questions or need assistance, please contact our Credit Committee at <b>{{config('variables.coop_contact')}}</b>.</p>
				<p>We appreciate your valued patronage of our services.</p>

				@elseif($details->status == 5)
				<!-- DECLINED -->
				<p>We regret to inform you that your loan application with the following details has been <b>declined due to {{$details->cancellation_reason}}</b>:</p>
				@include('emails.mail_loan_details')
				<p>If you have any questions or would like further clarification, please contact our Credit Committee at <b>{{config('variables.coop_contact')}}</b>.</p>
				<p>We apologize for any inconvenience that caused or may cause you.</p>
				<p>We appreciate your valued patronage of our services.</p>

				@elseif($details->status == 3)
				<!-- Released -->

				@endif
				
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

