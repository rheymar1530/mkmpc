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

	#frm_post input, #frm_post select, legend{
		font-size: 14px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}

	legend{
		width: unset;
		font-size: 19px !important;
	}
	.p_add_text{
		font-size: 14px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		line-height: 1.1em;
		color:  #737373;
	}

	hr{
		margin-top: 1rem;
	}
	#captcha_valid{
		color: red;
	}

	@media (min-width: 1200px) {
		.container{
			max-width: 650px;
		}
	}
	.form-row label{
		margin-bottom: 0 !important;
		font-size: 0.9rem;
	}
	.mandatory{
		border-color: rgba(232, 63, 82, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
		outline: 0 none;
	}
	.optional{
		border-color: rgba(255, 148, 77, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(255, 133, 51, 0.6) !important;
		outline: 0 none;
	}
	.mandatory:focus{
		border-color: rgba(232, 63, 82, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
		outline: 0 none !important;
	}
	label.required:after{
		content: ' *';
		color: red;
	}
	.header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 20px 0;
	}
</style>
<div class="container mt-5">
	{!! NoCaptcha::renderJs() !!}
	<!-- <a class="btn btn-default round_button btn-sm mb-2" href="/login"><i class="fas fa-chevron-left"></i>&nbsp;Back to login</a> -->

	<div class="card c-border">
		<form id="frm_post">
			<div class="card-body px-3">
				<div class="row mt-n2">
					<div class="col-md-3 col-3 ml-n2">
						<img src="{{URL::asset('dist/img/LEPSTA 1 LOGO2.png')}}" class="img-logo" style="clip-path: inset(5px 5px 5px 5px);" width="120px" height="120px">
					</div>
					<div class="col-md-6 col-9 ml-2">
						<div class="text-center" style="justify-content: center !important;">
							<h5 class="lbl_color pt-4" style="">{{env('APP_NAME')}} <br>Member Registration Portal</h5>
						</div>
					</div>
				</div>
				
				<div class="form-row mt-4">
					<div class="form-group col-md-10 col-12">
						<label for="txt_email" class="lbl_color required">Member's Name</label>
						<select class="form-control form-control-border" id="sel_member" required></select>
					</div>
					<div class="form-group col-md-12 col-12">
						<label for="txt_address" class="lbl_color">Address</label>
						<input type="text" class="form-control form-control-border" id="txt_address" disabled>
					</div>
					<div class="form-group col-md-8 col-12">
						<label for="txt_email" class="lbl_color required">Email</label>
						<input type="email" class="form-control form-control-border reg-in" id="txt_email" name="email" placeholder="juandelaruz@gmail.com" required>
					</div>
					<div class="form-group col-12 mt-0" style="zoom:111% !important">
						{!! NoCaptcha::display() !!}
					</div>
				</div>
			</div>
			<div class="card-footer py-2">
				<button type="submit" class="btn btn-md bg-gradient-success round_button float-right">Sign up</button>
			</div>
		</form>
	</div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});	
	intialize_select2()

	function intialize_select2(){		
		$("#sel_member").select2({
			minimumInputLength: 2,
			width: '100%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: '/registration/search-member',
				dataType: 'json',
				type: "GET",
				quietMillis: 1000,
				data: function (params) {
					var queryParameters = {
						term: params.term
					}
					return queryParameters;
				},
				processResults: function (data) {
					console.log({data});
					return {
						results: $.map(data.accounts, function (item) {
							return {
								text: item.tag_value,
								id: item.tag_id
							}
						})
					};
				}
			}
		});
	}
	$(document).on('change','#sel_member',function(){
		var val = $(this).val();
		if(val == null){
			return;
		}
		$.ajax({
			type       :  'GET',
			url        :  '/registration/parse-member',
			data       :  {'id_member' : val},
			success    :  function(response){
				console.log({response});
				$('#txt_email').val(response.details.email ?? '');
				$('#txt_address').val(response.details.address ?? '');

			}
		})
	})

	$('#frm_post').submit(function(e){
		e.preventDefault();
		Swal.fire({
			title: 'Do you want to save this?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				post();
			} 
		})	
		

	});

	function post(){
		let ajaxParam  = {
			'id_member' : $('#sel_member').val(),
			'email' : $('#txt_email').val(),
			'g-recaptcha-response' : grecaptcha.getResponse()
		};

		$.ajax({
			type      :        'POST',
			url       :        '/registration/post',
			data      :        ajaxParam,
			beforeSend :       function(){
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
			},
			success   :        function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: "Account Successfully Registered",
						text: `Please check your email ${$('#txt_email').val()} to set account password. If you don't receive an email, please contact system administrator`,
						icon: 'success',
						confirmButtonText: 'Close',
						confirmButtonColor: "#DD6B55"
					}).then((result) => {
						location.reload();
					});
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: response.message2 ?? '',
						icon: 'warning',
						confirmButtonText: 'Close',
						confirmButtonColor: "#DD6B55"
					});
					grecaptcha.reset();				
				}
			},error: function(xhr, status, error) {
				hide_loader()
				var errorMessage = xhr.status + ': ' + xhr.statusText
				Swal.fire({
					title: "Error-" + errorMessage,
					text: '',
					icon: 'warning',
					confirmButtonText: 'OK',
					confirmButtonColor: "#DD6B55"
				});
				grecaptcha.reset();
			}

		});

	}
</script>

@endpush