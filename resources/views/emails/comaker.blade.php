
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
		margin-top: 0.4rem !important;
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


<body style="background-color: white;margin:0 auto;">
	<div class="container">
		<div class="div_body">
			<div style="padding: 0px 10px 10px 16px;" class="div_email_body">
				<p>Dear {{$maker_name}}, </p>
				<p>We received a loan application under your name as a co-maker with the following details:</p>
				<br>
				<p class="nb"><b>Loaner's Name: </b>{{$details->member_name}}</p>
				@include('emails.mail_loan_details')
				<p>If you authorize this application, no further action is required. Our Credit Committee is reviewing it, and you will be updated soon. Otherwise, please contact our Credit Committee immediately at <b>{{config('variables.coop_contact')}}</b>.</p>
				<p>Thank You for your cooperation.</p>

				<br>
				<p class="nb">Sincerely,</p>
				<p class="nb">{{config('variables.coop_abbr')}}</p>
				<hr>
				
				@include('emails.disclaimer')
				
			</div>
		</div>
	</div>
</body>
</html>

