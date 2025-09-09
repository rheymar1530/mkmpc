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
	.required:after {
		content:" *";
		color: red;
	}
	.mandatory{
		border-color: rgba(232, 63, 82, 0.8);
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6);
		outline: 0 none;
	}
	.mandatory:focus{
		border-color: rgba(232, 63, 82, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
		outline: 0 none !important;
	}
	.list-validation{
		padding: 5px;
		padding-left: 20px;
		border: none;
		font-size: 14px;
		color: grey;
	}
	.address_body{
		padding: 3px;
		padding-left: 10px;
	}


	#frm_post input, #frm_post select, legend{
		font-size: 14px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}


	.p_add_text{
		font-size: 14px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		line-height: 1.5em;
		color:  #737373;
	}
	.form-row{
		margin-top: -5px;
	}
	hr{
		margin-top: 1rem;
	}
	
	.verification_box{
		width: 800px;
	}
	@media (max-width: 800px){
		.verification_box{
			width: 400px;
		}
		/* CSS that should be displayed if width is equal to or less than 800px goes here */
	}
	.class_pass{
		padding-right: 40px;
	}
	.valid_pass{
		color: green;
	}
</style>
<div class="container verification_box" style="padding-top: 40px;">
	<div class="card c-border" style="">
		<div class="card-body col-md-12">
			<div class="row">
				<div class="col-md-12 col_form" style="padding-left: 20px">
					<h5>Registration Verified</h5>
					<p class="p_add_text">Please set your account password to finish the registration. If you have a question you can contact us at <a href="tel:{{config('variables.coop_contact')}}">{{config('variables.coop_contact')}}</a> or by email at <a href="mailto:{{config('variables.coop_email')}}">{{config('variables.coop_email')}}</a>.</p>
					<hr>
					<form id="frm_post_password" autocomplete='off' method="post" action="{{ route('PostPasswordRegister') }}">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="txt_email">Email</label>
									<input type="text" class="form-control form-control-border" value="{{$details->email}}" disabled="">
								</div>
								
								<input type="hidden" name="reg_token" value="{{$details->token}}">
								<input type="hidden" name="reg_id" value="{{$details->id_registration}}">
								<input type="hidden" name="_token" value="{{ csrf_token() }}"/>
								<div class="form-group">
									<label for="txt_password">Password</label>
									<input type="password" name="password" autocomplete="current-password" required="" id="txt_password" class="form-control class_pass form-control-border">
									<i class="far fa-eye" id="togglePassword" style="margin-right: 10px;margin-top: -20px; cursor: pointer;float:right"></i>
								</div>
								<div class="form-group">
									<label for="txt_re_password">Re-type Password</label>
									<input type="password" name="re_password" autocomplete="current-password" required="" id="txt_re_password" class="form-control class_pass form-control-border">
									<i class="far fa-eye" id="togglePasswordRe" style="margin-right: 10px;margin-top: -20px; cursor: pointer;float:right"></i>
								</div>

							</div>
							<div class="col-md-6">
								<ul class="list-group">
									<li class="list-group-item list-validation" id="li_lower">At least one lowercase character</li>
									<li class="list-group-item list-validation" id="li_upper">At least one uppercase character</li>
									<li class="list-group-item list-validation" id="li_number">At least one number</li>
									<li class="list-group-item list-validation" id="li_length">8-15 characters</li>
								</ul>
							</div>
							<div class="col-md-12">
								<button type="submit" class="btn btn-primary" disabled="">Submit</button>
							</div>
						</div>
					</form>	
					@push('scripts')
					<script type="text/javascript">
						const togglePassword = document.querySelector('#togglePassword');
						const password = document.querySelector('#txt_password');

						togglePassword.addEventListener('click', function (e) {
									    // toggle the type attribute
							const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
							password.setAttribute('type', type);
									    // toggle the eye slash icon
							this.classList.toggle('fa-eye-slash');
						});

						const togglePasswordRe = document.querySelector('#togglePasswordRe');
						const password_re = document.querySelector('#txt_re_password');

						togglePasswordRe.addEventListener('click', function (e) {
									    // toggle the type attribute
							const type2 = password_re.getAttribute('type') === 'password' ? 'text' : 'password';
							password_re.setAttribute('type', type2);
									    // toggle the eye slash icon
							this.classList.toggle('fa-eye-slash');
						});
						$('.class_pass').on('keyup',function(){
							var p = $.trim($('#txt_password').val());
							var r = $.trim($('#txt_re_password').val());
							var valid = true;

							if(hasNumber(p)){
								$('#li_number').addClass('valid_pass');
							}else{
								$('#li_number').removeClass('valid_pass');
								valid = false;
								console.log("POTA4");
							}
							if(hasLowerCase(p)){
								$('#li_lower').addClass('valid_pass');
							}else{
								$('#li_lower').removeClass('valid_pass');
								valid = false;
							}
							if(hasUpperCase(p)){
								$('#li_upper').addClass('valid_pass');
							}else{
								$('#li_upper').removeClass('valid_pass');
								valid = false;

							}
							if(p.length >=8 && p.length <= 15){
								$('#li_length').addClass('valid_pass')
							}else{
								$('#li_length').removeClass('valid_pass')
								valid = false;
							}
							console.log({valid})
							if(((p != r) || (p =="" || r == "")) || !valid){
								$('#frm_post_password').find('button').prop('disabled',true);
							}else{
								$('#frm_post_password').find('button').prop('disabled',false);
							}
						})
						function hasNumber(myString) {
							return /\d/.test(myString);
						}
						function hasLowerCase(str) {
							return (/[a-z]/.test(str));
						}
						function hasUpperCase(str) {
							return (/[A-Z]/.test(str));
						}
						$('#frm_post_password').submit(function(){
							Swal.fire({
								title: 'Saving ...',
											// text : 'Please wait ..',
								allowOutsideClick: false,
								showConfirmButton: false,
								allowEscapeKey: false,
								onBeforeOpen: () => {
									Swal.showLoading()
								},
							})
						})
					</script>
					@endpush	
				</div>
			</div>
		</div>
	</div>
</div>
@endsection