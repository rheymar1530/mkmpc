<style type="text/css">
	div#file {
		position: relative;
		overflow: hidden;
	}
	input#txt_photo {
		position: absolute;
		font-size: 50px;
		opacity: 0;
		right: 0;
		top: 0;
	}
</style>
<div class="form-row">
	<div class="form-group col-md-2">
		<img class="profile-user-img img-fluid" src="/storage/uploads/account_image/no_img.png" alt="User profile picture" style="height:160px !important;width: 160px !important;" id="preview_image">
	</div>
	<div class="form-group col-md-9">
		<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
			<div class="file btn btn-sm btn-lg bg-gradient-info" id="div_file"  style="padding-top: 7px !important;">
				<span><i class="fas fa-upload"></i>&nbsp;Upload File</span>
				<input type="file" name="file" id="txt_photo"/>
			</div>
			<button type="button" class="btn btn-sm bg-gradient-primary"><i class="fas fa-camera"></i>&nbsp;Open Camera</button>

			<div class="btn-group" role="group">
				<button id="btnGroupDrop1" type="button" class="btn bg-gradient-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Options
				</button>
				<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
					<a class="dropdown-item" onclick="remove_image()"><i class="fa fa-times"></i>&nbsp;&nbsp;Remove Image</a>
					<a class="dropdown-item"><i class="fas fa-undo"></i>&nbsp;&nbsp;Undo</a>
				</div>
			</div>
		</div>
	</div>

</div>
<div class="col-sm-12">
	<div class="form-row" >
		<div class="form-group col-md-4">
			<label for="txt_mem_date">Membership Date</label>
			<input type="date" class="form-control" id="txt_mem_date" value="{{$current_date}}" input-key="membership_date">
		</div>
	</div>					
	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="txt_first_name">First Name</label>
			<input type="text" class="form-control" id="txt_first_name" placeholder="First Name" input-key="first_name">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_middle_name">Middle Name</label>
			<input type="text" class="form-control" id="txt_middle_name" placeholder="Middle Name" input-key="middle_name">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_last_name">Last Name</label>
			<input type="text" class="form-control" id="txt_last_name" placeholder="Last Name" input-key="last_name">
		</div>
	</div>
</div>

<div class="col-sm-12">
	<div class="form-row">
		<div class="form-group col-md-3">
			<label for="txt_birthdate">Birth Date</label>
			<input type="date" class="form-control" id="txt_birthdate" input-key="birthdate">
		</div>
		<div class="form-group col-md-2">
			<label for="sel_gender">Gender</label>
			<select class="form-control p-0" id="sel_gender" input-key="gender">
				<option value="Male">Male</option>
				<option value="Female">Female</option>
			</select>
		</div>
		<div class="form-group col-md-3">
			<label for="sel_civil_status">Civil Status</label>
			<select class="form-control p-0" id="sel_civil_status" input-key="id_civil_status">
				@foreach($civil_status as $civ)
				<option value="{{$civ->id_civil_status}}">{{$civ->description}}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group col-md-4">
			<label for="txt_email">Email</label>
			<input type="email" class="form-control" id="txt_email" input-key="email">
		</div>
	</div>
</div>
<div class="col-sm-12">
	<div class="form-row">
		<div class="form-group col-md-8">
			<label for="txt_address">Address</label>
			<input type="text" class="form-control" id="txt_address" placeholder="Address" input-key="address">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_mobile">Mobile No. / Tel No.</label>
			<input type="text" class="form-control" id="txt_mobile" placeholder="Mobile No. / Tel No." input-key="contact">
		</div>

	</div>					
</div>
<div class="col-sm-12">
	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="sel_position">Position</label>
			<select class="form-control p-0" id="sel_position" input-key="id_position">
				@foreach($positions as $pos)
				<option value="{{$pos->id_position}}">{{$pos->description}}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group col-md-4">
			<label for="sel_coop_position">Coop Position</label>
			<select class="form-control p-0" id="sel_coop_position" input-key="id_coop_position">
				@foreach($coop_positions as $coop_pos)
				<option value="{{$coop_pos->id_coop_position}}">{{$coop_pos->description}}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group col-md-4">
			<label for="sel_pos_stat">Position Status</label>
			<select class="form-control p-0" id="sel_pos_stat" input-key="pos_stat">
				<option value="0">Not in Service</option>
				<option value="1">In Service</option>
			</select>
		</div>

	</div>
</div>
<div class="col-sm-12">
	<div class="form-row">
		<div class="form-group col-md-8">
			<label for="txt_school">School</label>
			<input type="text" class="form-control" id="txt_school" placeholder="School" input-key="school">
		</div>
	</div>
</div>

@push('scripts')
<script type="text/javascript">
	var no_image = '/storage/uploads/account_image/no_img.png';
	var last_photo = no_image;
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
		}
	});
	function remove_image(){
		$('#preview_image').attr('src', no_image);
		$('#txt_photo').val('');
	}
</script>
@endpush