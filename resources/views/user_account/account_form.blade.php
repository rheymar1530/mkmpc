@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	select{
		padding-bottom: 0px !important;
	}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<!-- general form elements -->
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title">{{ $opcode == 0 ? 'Add' : 'Edit' }} User</h3>
				</div>
				<form id="frm_submit" method="post" enctype="multipart/form-data">

					<div class="card-body">
						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="sel_loan_service">Select Member</label>

								<select class="form-control frm-inputs frm-parent-loan p-0" id="sel_id_member" key='id_member' name="id_member" required>
									@if(isset($selected_member))
									<option value="{{$selected_member->id_member}}">{{$selected_member->member_name}}</option>
									@endif
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="exampleInputEmail1">Name</label>
							<input type="text" class="form-control in_l" id="txt_name" placeholder="Name" value="{{ $user_details->name ?? '' }}">
						</div>
						<div class="form-group">
							<label for="sel_branch">Branch</label>
							<select class="form-control" id="sel_branch">
								@foreach($branches as $branch)
								<?php 
								$this_branch = $user_details->id_branch ?? '';
								$selected = ($branch->id_branch == $this_branch)?'selected':'';?>
								<option value="{{$branch->id_branch}}" {{$selected}}>{{$branch->branch_name}}</option>
								@endforeach

							</select>
						</div>
						<div class="form-group">
							<label for="exampleInputEmail1">Username</label>
							<input type="text" class="form-control in_l" id="txt_username" placeholder="Username" value="{{ $user_details->email ?? '' }}">
						</div>
						<div class="form-group">
							<label for="sel_privilege">Privilege</label>
							<select class="form-control select2" id="sel_privilege" style="width: 100%;" required="">
								@foreach($privileges as $priv)
								<option value="{{ $priv->id }}">{{ $priv->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group">
							<label for="exampleInputPassword1">Password</label>
							<input type="password" class="form-control in_l" id="txt_password" placeholder="Password" autocomplete="off">
							@if($opcode == 1)
							*Leave blank if no changes
							@endif
						</div>
						<div class="form-group">
							<label for="exampleInputFile">Photo</label>
							<div class="input-group">
								<div class="custom-file">
									<input type="file" class="custom-file-input in_l" id="txt_photo">
									<label class="custom-file-label" for="exampleInputFile">Choose file</label>
								</div>
							</div>
							<?php
							if($opcode == 0){
								$photo = '';
							}else{
								$p = $user_details->photo ?? '';

								$photo = (substr($user_details->photo, 0, 8) == "/storage")?substr($user_details->photo, 9):$user_details->photo;
								$photo = (asset("/storage/".$photo));		
							}
							?> 
							<div class="col-sm-5">
								<img width="100px" height="100px" src="{{$photo}}" id="prev_id" alt="Image">
							</div>
						</div>
					</div>
					<div class="card-footer">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
	var opcode = '<?php echo $opcode; ?>';
	var reference = '<?php echo $user_details->id ?? 0 ?>';
	var image_src = $('#prev_id').attr('src');
	initialize_select_member();
	function initialize_select_member(){       
		$("#sel_id_member").select2({
			minimumInputLength: 2,
			width: '100%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: '/search_member/loan_application',
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
	$('#sel_id_member').change(function(){
		var val = $(this).val();
		$.ajax({
			type        :       'GET',
			url         :       '/user/get_member_details',
			data        :       {'id_member' : val},
			success     :       function(response){
				console.log({response});
				$('#txt_name').val(response.details.name);
				$('#sel_branch').val(response.details.id_branch);
				$('#txt_username').val(response.details.email);
			}
		})
	})
	$(document).on('select2:open', (e) => {
		const selectId = e.target.id
		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
			key,
			value,
			) {
			value.focus()
		})
	}) 
	$('#sel_privilege').val('<?php echo $user_details->id_cms_privileges ?? 1 ?>');
	$('#sel_privilege').select2();

	$('#prev_id[src=""]').hide();
	$('#prev_id:not([src=""])').show();
	$('#frm_submit').submit(function(e){
		e.preventDefault();
		var extension = $('#txt_photo').val().split('.').pop().toLowerCase();
		console.log($('#txt_photo').val());

		if(opcode == 0){
			// validate_post(extension)
					Swal.fire({
				title: 'Do you want to save the changes?',
				showDenyButton: false,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: `Save`,
			}).then((result) => {
				if (result.isConfirmed) {
					post();
				} 
				  // else if (result.isDenied) {
				  //   Swal.fire('Changes are not saved', '', 'info')
				  // }
				})
		}else{
			if(extension == ''){
				Swal.fire({
					title: 'Do you want to save the changes?',
					icon: 'warning',
					showDenyButton: false,
					showCancelButton: true,
					confirmButtonText: `Save`,
				}).then((result) => {
					if (result.isConfirmed) {
						post();
					} 
					  // else if (result.isDenied) {
					  //   Swal.fire('Changes are not saved', '', 'info')
					  // }
					})
			}else{
				validate_post(extension)
			}
		}
	})
	function validate_post(extension){
		if ($.inArray(extension, ['gif', 'png', 'jpeg','jpg','jfif']) == -1) {
			Swal.fire({
				position: 'center',
				icon: 'warning',
				title: 'Please select valid image',
				showConfirmButton: false,
				cancelButtonText: `Close`,
				showCancelButton: true,
			})
			$('#prev_id[src=""]').hide();
			return;
		} else {
			Swal.fire({
				title: 'Do you want to save the changes?',
				showDenyButton: false,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: `Save`,
			}).then((result) => {
				if (result.isConfirmed) {
					post();
				} 
				  // else if (result.isDenied) {
				  //   Swal.fire('Changes are not saved', '', 'info')
				  // }
				})
		}
	}
	$('#txt_photo').change(function(){
		var input = this;
		var url = $(this).val();
		var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
		if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg" || ext=="jfif")) 
		{
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#prev_id').attr('src', e.target.result);
				$('#prev_id:not([src=""])').show();
			}
			reader.readAsDataURL(input.files[0]);

		}
		else
		{
			Swal.fire({
				position: 'center',
				icon: 'warning',
				title: 'Please select valid image',
				showConfirmButton: false,
				cancelButtonText: `Close`,
				showCancelButton: true,
			})
			$('#prev_id').attr('src', '');
			$('#prev_id[src=""]').hide();
		}
	});

	function clear_text(){
		$('.in_l').val('');
	}
	function post(){
		var form_data = new FormData();
		form_data.append('username',$('#txt_username').val());
		form_data.append('password',$('#txt_password').val());
		form_data.append('name',$('#txt_name').val());
		form_data.append('photo', $('#txt_photo').prop('files')[0]);
		form_data.append('opcode',opcode);
		form_data.append('id_user',reference);
		form_data.append('id_cms_privileges',$('#sel_privilege').val());
		form_data.append('id_branch',$('#sel_branch').val());
		form_data.append('id_member',$('#sel_id_member').val());
		console.log({form_data});
		$.ajax({
			type      :        'POST',
			url       :        '/admin/post_user',
			contentType: false,
			data      :         form_data,
			cache: false, // To unable request pages to be cached
			processData: false,
			beforeSend :       function(){
				show_loader();
			},
			success   :        function(response){
				console.log({response});
				setTimeout(
					function() {
						hide_loader();
						if(response.message == "success"){
							Swal.fire({
								position: 'center',
								icon: 'success',
								title: 'Account successfully saved !',
								showConfirmButton: false,
								timer: 1500
							}).then(function() {
								if(opcode == 0){
									window.location = '/user/edit?id='+response.id_user;
									clear_text();
									$('#prev_id').attr('src', '');
								}
							})
						}
					}, 1500);
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
	}
</script>
@endpush