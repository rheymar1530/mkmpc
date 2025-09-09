@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
.nav_sel{
	font-size: 14px;
}
.address_header{
	padding: 5px;
}
.list-address{
	padding: 0px;
	border: none;
	font-size: 14px;
}
.list-details{
	padding: 3px;
	font-size: 14px;
}
.address_body{
	padding: 3px;
	padding-left: 10px;
}
.nav-pills .active{
	background: #dc3545 linear-gradient(180deg,#e15361,#dc3545) repeat-x !important;
}
.verification_box{
	width: 1200px;
}
@media (max-width: 800px){
	.verification_box{
		width: 100%;
	}
}
label.lbl_gen{
	margin-bottom: -10px !important;
	font-size: 13px;
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
.list-group-item {
	padding: 3px;
}
.current_branch{
	text-decoration:underline ;
}
</style>
<div class="container verification_box">
	<div class="row">
		<div class="col-md-12" style="align-content: center !important;">
			<!-- Profile Image -->
			<div class="card card-danger card-outline">
				<div class="card-body box-profile ">
					<div class="col-md-12">
						<div class="text-center">
							<?php
								$photo_path = (substr($user_details->photo, 0, 8) == "/storage")?substr($user_details->photo, 9):$user_details->photo;
							?>
							<a href="/storage/{{ $photo_path }}" data-toggle="lightbox" data-gallery="example-gallery" data-type="image">
								<img class="profile-user-img img-fluid img-circle" src="/storage/{{ $photo_path }}" alt="User profile picture" style="height:200px !important;width: 200px !important;">    
							</a>
						</div>
						<h3 class="profile-username text-center">{{$user_details->name}}</h3>
						<p class="text-muted text-center">{{$user_details->priv_name}}</p>
						<ul class="list-group list-group-unbordered mb-3">
							<li class="list-group-item list-details"><strong>User ID</strong>
								<span style="float: right;">
									<a >{{$user_details->id_user}}</a>
								</span>
							</li>
							<li class="list-group-item list-details"><strong>ID Member</strong>
								<span style="float: right;">
									<a >{{$user_details->id_member}} || {{$user_details->member_code}}</a>
									
								</span>
							</li>

							<li class="list-group-item list-details">
								<strong>Branch: </strong>
								<a style="float:right;">{{$user_details->branch_name}}</a>
							</li>
						</ul>
					</div>
					<div class="col-md-12">
						<ul class="nav nav-pills">
							<li class="nav-item"><a class="nav-link nav_sel active" href="#change_photo" data-toggle="tab">Change Account Photo</a></li>
							<li class="nav-item"><a class="nav-link nav_sel" href="#chnge_pass" data-toggle="tab">Change Password</a></li>
							<li class="nav-item"><a class="nav-link nav_sel" href="#others" data-toggle="tab">Others</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="change_photo" style="margin-top:20px">
								<form class="form-horizontal" id="frm_update_photo" enctype="multipart/form-data" method="post">
									<h5>Change Account Photo</h5>
									<div class="form-row" style="margin-top:20px">
										<div class="form-group col-md-2">
											<img class="profile-user-img img-fluid" src="/storage/{{ $photo_path }}" alt="User profile picture" style="height:100px !important;width: 150px !important;" id="preview_image">
										</div>
										<div class="form-group col-md-9">
											<div class="input-group">
												<div class="custom-file">
													<input type="file" class="custom-file-input in_l" id="txt_photo">
													<label class="custom-file-label" for="exampleInputFile">Choose file</label>
												</div>
											</div>
											<button class="btn btn-sm btn-danger" style="margin-top:10px" type="button" onclick="remove_image()"><i class="fa fa-times" ></i>&nbsp;Remove Image</button>
											<button class="btn btn-sm btn-info" style="margin-top:10px" type="button" onclick="undo_image()"><i class="fas fa-undo-alt"></i>&nbsp;Undo</button>
										</div>
									</div>
									<button type="submit" class="btn btn-primary" style="float: right">Submit</button>
								</form>
							</div>
							<div class="tab-pane" id="chnge_pass" style="margin-top:20px">
								<form class="form-horizontal" id="frm_update_password" enctype="multipart/form-data" method="post">
									<h5>Change Password</h5>
								
									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="txt-current-password" class="lbl_gen">Password</label>
											<input type="password" class="form-control" id="txt-current-password" placeholder="Password" value="" required>
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="txt-new-password" class="lbl_gen">New Password</label>
											<input type="password" class="form-control" id="txt-new-password" placeholder="New Password" value="" required>
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="txt-new-password" class="lbl_gen">Re-type New Password</label>
											<input type="password" class="form-control" id="txt-re-new-password" placeholder="Re-type New Password" value="" required>
										</div>
									</div>
									<button type="submit" class="btn btn-primary" style="float: right">Submit</button>
								</form>
							</div>
							<div class="tab-pane" id="others" style="margin-top:20px">
								<a class="btn btn-sm bg-gradient-primary" href="/member/view/{{$user_details->member_code}}" target="_blank">Update Member Info</a>
							</div>
						</div>
					</div>
				</div>
				<!-- /.card-body -->
			</div>
			<!-- /.card -->
		</div>

	</div>
</div>


@endsection
@push('scripts')
<script type="text/javascript">
	var remove_img = 0;
	var default_ = '<?php  echo Config::get("global.no_image"); ?>';
	$(document).ready(function(){

	})
	$(document).on('click', '[data-toggle="lightbox"]', function(event){
		event.preventDefault();
		$(this).ekkoLightbox();
	});
	$('#frm_update_photo').submit(function(e){
		e.preventDefault();
		var extension = $('#txt_photo').val().split('.').pop().toLowerCase();
		if ($.inArray(extension, ['gif', 'png', 'jpeg','jpg','jfif']) == -1 && $('#txt_photo').val() != "") {
			Swal.fire({
				position: 'center',
				icon: 'warning',
				title: 'Please select valid image',
				showConfirmButton: false,
				cancelButtonText: `Close`,
				showCancelButton: true,
			})
			return;
		}
		Swal.fire({
			title: 'Do you want to save the changes?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				// alert("POSTED");
				post_update(1);
			} 
		})	
	});
	$('#frm_update_password').submit(function(e){
		e.preventDefault();

		if($('#txt-new-password').val() != $('#txt-re-new-password').val()){
			Swal.fire({
				position: 'center',
				icon: 'warning',
				title: 'Please check your new password',
				text: 'New password dont match',
				showConfirmButton: false,
				cancelButtonText: `Close`,
				showCancelButton: true,
			})

			return;
		}

		post_update(2);
	})
	function post_update(opcode){
		var form_data = new FormData();
		form_data.append('opcode',opcode);
		if(opcode == 1){
			
			form_data.append('image',$('#txt_photo').prop('files')[0])
			form_data.append('remove_img',remove_img);
		}else if(opcode == 2){
			form_data.append('password',$('#txt-current-password').val());
			form_data.append('new_password',$('#txt-new-password').val());
			form_data.append('re_new_password',$('#txt-re-new-password').val());
		}

		// form_data.append('contact_person',$('#txt_contact_person').val())
		// form_data.append('designation',$('#txt_designation').val())
		// form_data.append('contact_no',$('#txt_contact_no').val())
		// form_data.append('email', $('#txt_email').val())
		// form_data.append('business',$('#txt_business_type').val())
		// form_data.append('tin', $('#txt_tin').val())
		// form_data.append('address',$('#txt_address').val())
		// form_data.append('department',$('#txt_department').val())
		// form_data.append('building_name',$('#txt_building_name').val())
		// form_data.append('floor',$('#txt_floor_no').val())
		// form_data.append('city',$('#txt_city').val())


		$.ajax({
			type             :           'POST',
			url              :           '/user/post_user_update',
			contentType		 :           false,
			data             :           form_data,
			cache			 : 			 false, // To unable request pages to be cached
			processData	     : 	 	     false,
			beforeSend      :       function(){
				show_loader();
			},
			success          :           function(response){
				console.log({response});

				if(response.message == "success"){
					setTimeout(
						function() {
							hide_loader();
							Swal.fire({
								position: 'center',
								icon: 'success',
								title: 'Account information successfully updated !',
								showConfirmButton: false,
								timer: 1500
							}).then(function() {
								location.reload()	
							})

						}, 1500);
				}else if(response.message == "failed"){
					hide_loader();
					Swal.fire({
						position: 'center',
						icon: 'warning',
						title: 'Something went wrong',
						text : response.text,
						showConfirmButton: false,
						timer: 1500
					})
				}else if(response.message == "invalid_password"){
					Swal.fire({
						position: 'center',
						icon: 'warning',
						title: "Password don't match",
						text : "Please check your password",
						showConfirmButton: false,
						timer: 1500
					})
					$('#txt-current-password').focus();
				}
				hide_loader();
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
		});
		
	}
	$('#txt_photo').change(function(){
		var input = this;
		var url = $(this).val();
		console.log({url});
		var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
		if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg" || ext=="jfif")) 
		{
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#preview_image').attr('src', e.target.result);
				$('#preview_image:not([src=""])').show();
			}
			reader.readAsDataURL(input.files[0]);
			remove_img = 0;

		}else if(url == ''){ //cancelled
			$(this).val('')
			return;
		}
		else
		{
			$(this).val('')
			Swal.fire({
				position: 'center',
				icon: 'warning',
				title: 'Please select valid image',
				showConfirmButton: false,
				cancelButtonText: `Close`,
				showCancelButton: true,
			})
			// $('#preview_image').attr('src', '');
			// $('#preview_image[src=""]').hide();
		}
	});
	function remove_image(){
		$(this).val('')
		$('#preview_image').attr('src','/storage/'+default_);
		remove_img = 1;
		// $('#preview_image:not([src=""])').show();
	}
	function undo_image(){
		var path = '<?php echo $photo_path;  ?>';
		$(this).val('')
		$('#preview_image').attr('src','/storage/'+path);
		remove_img = 0;
	}
	function add_account(){

	}
</script>
@endpush