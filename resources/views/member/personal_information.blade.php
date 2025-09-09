<style type="text/css">


</style>

<div class="col-sm-12">
	<div class="form-row" >
		<div class="form-group col-md-4">
			<label for="txt_mem_date">Membership Date</label>
			<input type="date" class="form-control frm-inputs" id="txt_mem_date" value="{{$current_date}}" input-key="membership_date">
		</div>

		
	</div>	
			
	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="txt_first_name">First Name</label>
			<input type="text" class="form-control frm-inputs" id="txt_first_name" placeholder="First Name" input-key="first_name" required>
		</div>
		<div class="form-group col-md-3">
			<label for="txt_middle_name">Middle Name</label>
			<input type="text" class="form-control frm-inputs" id="txt_middle_name" placeholder="Middle Name" input-key="middle_name">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_last_name">Last Name</label>
			<input type="text" class="form-control frm-inputs" id="txt_last_name" placeholder="Last Name" input-key="last_name">
		</div>
		<div class="form-group col-md-1">
			<label for="txt_suffix">Suffix</label>
			<input type="text" class="form-control frm-inputs" id="txt_suffix" placeholder="Suffix" input-key="suffix">
		</div>
	</div>
</div>

<div class="col-sm-12">
	<div class="form-row">
		<div class="form-group col-md-3">
			<label for="txt_birthdate">Date of birth</label>
			<input type="date" class="form-control frm-inputs" id="txt_birthdate" input-key="date_of_birth">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_place_birth">Place of birth</label>
			<input type="text" class="form-control frm-inputs" id="txt_place_birth" placeholder="Place of birth" input-key="place_of_birth">
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
	</div>
</div>
<div class="col-sm-12">
	<div class="form-row">
		<div class="form-group col-md-8">
			<label for="txt_address">Address</label>
			<input type="text" class="form-control frm-inputs" id="txt_address" placeholder="Address" input-key="address">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_mobile">Mobile No. / Tel No.</label>
			<input type="text" class="form-control frm-inputs" id="txt_mobile" placeholder="Mobile No. / Tel No." input-key="mobile_no">
		</div>
	</div>					
</div>
<div class="col-sm-12">
	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="txt_tin">TIN</label>
			<input type="text" class="form-control frm-inputs" id="txt_tin" placeholder="TIN" input-key="tin">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_email">Email</label>
			<input type="email" class="form-control frm-inputs" id="txt_email" input-key="email" placeholder="Email">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_religion">Religion/Social Affiliation</label>
			<input type="text" class="form-control frm-inputs" id="txt_religion" placeholder="Religion/Social Affiliation" input-key="religion">
		</div>
	</div>					
</div>

<div class="col-sm-12">
	<div class="form-row">
		<div class="form-group col-md-5">
			<label for="sel_educ_att">Highest Educational Attainment</label>
			<select class="form-control p-0" id="sel_educ_att" input-key="id_educational_attainment">
				@foreach($educational_attainment as $e)
				<option value="{{$e->id_educational_attainment}}">{{$e->description}}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="col-sm-12">
	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="txt_spouse">Spouse</label>
			<input type="text" class="form-control" id="txt_spouse" placeholder="Spouse" input-key="spouse">
		</div>
		<div class="form-group col-md-2">
			<label for="txt_no_dependent">No. of dependent</label>
			<input type="number" class="form-control class_number" id="txt_no_dependent" placeholder="No. of dependent" input-key="no_dependents" value="0">
		</div>
	</div>
</div>
<!-- <div class="col-sm-12">
	<button class="btn bg-gradient-primary2 next-step float-right round_button">Next <i class="fa fa-chevron-right"></i></button>
</div> -->

@push('scripts')
<script type="text/javascript"></script>

@endpush