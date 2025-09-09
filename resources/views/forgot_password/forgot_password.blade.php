@extends('adminLTE.admin_template_frame')
@section('content')
<link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
<style type="text/css">
	.col_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	.col_form select{
		padding-bottom: 0px;
	}
	.col_form label{
		margin-bottom: 0px;
		padding-top: 1px;
		font-size: 12px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	#captcha_valid{
		color: red;
	}
	.verification_box{
		width: 500px;
	}
	@media (max-width: 800px){
		.verification_box{
			width: 100%;
		}
	}
	#txt_email,#captcha{
		height: 32px !important;
	}
	#btn_back{
		cursor: pointer;
	}
</style>
<div class="container verification_box" style="padding-top: 40px;">
	{!! NoCaptcha::renderJs() !!}
	<div class="card c-border" style="">
		<div class="card-body col-md-12">
			<div class="row" id="verification_body">

				<div class="col-md-12 col_form" style="padding-left: 20px">
					<form  method="post" id="frm_forgot_password">
						<h5 class="lbl_color" style="text-align:center"><a style="float:left" id="btn_back" href="/login"><i class="fas fa-arrow-left" ></i></a>Forgot Password</h5>
						<div class="alrt_holder"></div>
						<div class="input-group mb-3" style="margin-top:20px">
							<input type="email" class="form-control" placeholder="Account Email" id="txt_email" required="">
							<div class="input-group-append">
								<div class="input-group-text" style="height: 32px">
									<span class="fas fa-envelope"></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12" style="zoom:111%">
								{!! NoCaptcha::display() !!}
							</div>
						</div>

						<div class="row mt-2">
							<div class="col-12">
								<button type="submit" class="btn bg-gradient-primary btn-block">Request new password</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
	var alert_invalid = `<div class="alert alert-warning alert-dismissible fade show  bg-gradient-warning" role="alert">
						  <strong>Invalid Account</strong> Please check your email address.
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						    <span aria-hidden="true">&times;</span>
						  </button>
						</div>`;

	$('#frm_forgot_password').submit(function(e){
		e.preventDefault();
		$.ajax({
			type               :        'POST',
			url                :        '/forgot-password/post_request',
			data               :        {
										 'email' : $('#txt_email').val(),
										 'g-recaptcha-response' : grecaptcha.getResponse()},
			beforeSend         :        function(){
										Swal.fire({
											title: 'Creating a Request ...',
														// text : 'Please wait ..',
											allowOutsideClick: false,
											showConfirmButton: false,
											allowEscapeKey: false,
											onBeforeOpen: () => {
												Swal.showLoading()
											},
										})
			},
			success            :        function(response){

										setTimeout(function() {
											Swal.close();
											console.log({response});
											if(response.RESPONSE_CODE == "ERROR"){
												toastr.error(response.message);
												grecaptcha.reset();		
											}else if(response.RESPONSE_CODE == "INVALID_ACCOUNT"){
												$('#captcha_modal').modal('hide');
												$('.alrt_holder').html(alert_invalid);
												$('#txt_email').focus();
												grecaptcha.reset();		
												return;
											}else if(response.RESPONSE_CODE == "SUCCESS"){
												set_verification(response.RESPONSE_EMAIL);
												$('#captcha_modal').modal('hide');
												
											}
										},1500)

			},error: function(xhr, status, error){
				hide_loader();
				var errorMessage = xhr.status + ': ' + xhr.statusText;
				Swal.fire({
					position: 'center',
					icon: 'warning',
					title: "Error-"+errorMessage,
					showConfirmButton: false,
					showCancelButton: true,
					cancelButtonText : "Close"
				})
	        }  
		})
	})
	function set_verification(email){
		var verification_sent = `<div class="col-md-12 col_form" style="padding-left: 20px">
						<h5 style="text-align:center"><a style="float:left" id="btn_back" onclick="window.location='/login'"><i class="fas fa-arrow-left" ></i></a>Reset Password</h5>
						<div class="alrt_holder"></div>
						<div style="text-align: center;font-size: 60px;"><i class="fas fa-user-lock"></i></div>
						<p>A Verification email has been sent to this email address <a href="mailto:`+email+`">`+email+`</a>. Please check it.</p>
					</div>`;
		$('#verification_body').html(verification_sent);
	
	}
</script>
@endpush