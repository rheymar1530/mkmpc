<div class="col-sm-12">				
	<div class="form-row">

		<div class="form-group col-md-4">
			<label for="sel_branch">Branch</label>
			<select class="form-control p-0" id="sel_branch" input-key="id_branch">
				@foreach($branches as $branch)
				<option value="{{$branch->id_branch}}">{{$branch->branch_name}}</option>
				@endforeach				

			</select>
		</div>
	</div>
	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="sel_mem_type">Membership Type</label>
			<select class="form-control p-0" id="sel_mem_type" input-key="memb_type">

				@foreach($membership_types as $types)
				<option value="{{$types->id_membership_type}}">{{$types->description}}</option>		
				@endforeach

			</select>
		</div>
		<div class="form-group col-md-4">
			<label for="sel_brgy_lgu" id="lbl-brgy-lgu">Barangay</label>
			<select class="form-control p-0" id="sel_brgy_lgu" input-key="brgy_lgu">
	

			</select>
		</div>
		<div class="form-group col-md-4">
			<label for="txt_mem_id">Membership ID <small>(Auto generate if empty)</small></label>
			<input type="text" class="form-control" id="txt_mem_id" value="{{$details->membership_id ?? ''}}">
		</div>
	</div>
	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="txt_position">Position</label>
			<input type="text" class="form-control" id="txt_position" placeholder="Position" input-key="member_position">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_bod_reso">BOD Resolution no.</label>
			<input type="text" class="form-control" id="txt_bod_reso" placeholder="BOD Resolution no." input-key="bod_resolution">
		</div>
	</div>
	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="txt_no_shares">Number of shares subscribe</label>
			<input type="number" class="form-control class_number" id="txt_no_shares" placeholder="Number of shares subscribe" input-key="num_share" value="0">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_amt_shares">Amount per shares</label>
			<input type="text" class="form-control class_amount" id="txt_amt_shares" placeholder="Amount per shares" input-key="amount_per_shares" value="100.00" readonly>
		</div>
		<div class="form-group col-md-4">
			<label for="txt_in_capital">Initial capital paid-up</label>
			<input type="text" class="form-control class_amount" id="txt_in_capital" placeholder="Number of shares subscribe" input-key="initial_paidup" value="0.00" readonly>
		</div>
	</div>
</div>

<!-- <div class="col-sm-12">
	<button type="button" class="btn btn-default prev-step bg-light round_button"><i class="fa fa-chevron-left"></i> Back</button>
	<button class="btn btn-info next-step float-right bg-gradient-primary2 round_button">Next <i class="fa fa-chevron-right"></i></button>
</div> -->


<!-- inputs ["sel_branch","sel_mem_type","txt_bod_reso","txt_no_shares","txt_amt_shares","txt_in_capital"] -->
<!--keys ["id_branch","memb_type","bod_resolution","num_share","amount_per_shares","initial_paidup"] -->