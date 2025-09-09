
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


<body style="background-color: white;margin:0 auto;">
	<div class="container">
		<div class="div_body">
			<div style="padding: 0px 10px 10px 16px;" class="div_email_body">
				<p>Dear {{$details->name}}, </p>
				<p>Good day!</p>
				<p>We received your request to reset your password.</p>
				<p>Please click the link below to set a new password for your account.</p>
				<br>
				<a class="btn-link button" href="{{$currentDomain}}/forgot-password-form?{{$queryString}}">Set new password</a>
				<br>

				<p>If you did not initiate this request, please contact our customer support team immediately at <a href="mailto:{{config('variables.coop_email')}}">{{config('variables.coop_email')}}</a> or call/text our Customer Service hotline at <a href="tel:{{config('variables.coop_contact')}}">{{config('variables.coop_contact')}}</a></p>

				<br>
				@include('emails.disclaimer')
				<p class="nb">Sincerely,</p>
				<p class="nb">{{config('variables.coop_abbr')}}</p>
				<hr>
				
				<br>
			</div>
		</div>
	</div>

</body>
</html>

