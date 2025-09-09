<style type="text/css">
	#tbl_beneficiaries tr>th{
		padding: 4px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 12px;
	}
	#tbl_beneficiaries tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 12px;
	}
	.frm-ben{
		height: 25px !important;
		width: 100%;    
	}
</style>
<div class="col-sm-12">				
	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="sel_income_source">Occupation / Source of Income</label>
			<input type="text" class="form-control" id="sel_income_source" placeholder="Source of Income" input-key="income_source">
<!-- 			<select class="form-control p-0" id="sel_income_source" input-key="income_source">
				@foreach($income_source as $inc)
					<option value="{{$inc->id_income_source}}">{{$inc->description}}</option>
				@endforeach				
			</select> -->
		</div>
		<div class="form-group col-md-4">
			<label for="txt_annual_income">Annual Income</label>
			<input type="text" class="form-control class_amount" id="txt_annual_income" placeholder="Annual Income" input-key="annual_income" value="0.00">
		</div>
	</div>
	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="txt_spouse_occupation">Spouse Occupation / Source of Income</label>
			<input type="text" class="form-control" id="txt_spouse_occupation" placeholder="Occupation" input-key="spouse_occupation">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_spouse_annual_income">Spouse Annual Income</label>
			<input type="text" class="form-control class_amount" id="txt_spouse_annual_income" placeholder="Spouse Annual Income" input-key="spouse_annual_income" value="0.00">
		</div>
<!-- 		<div class="form-group col-md-4">
			<label for="txt_spouse">Spouse</label>
			<input type="text" class="form-control" id="txt_spouse" placeholder="Spouse" input-key="spouse">
		</div>
		<div class="form-group col-md-4">
			<label for="txt_spouse_occupation">Occupation</label>
			<input type="text" class="form-control" id="txt_spouse_occupation" placeholder="Occupation" input-key="spouse_occupation">
		</div> -->

	</div>

</div>
<!-- <div class="col-sm-12">
	<button type="button" class="btn btn-default prev-step bg-light round_button"><i class="fa fa-chevron-left"></i> Back</button>
	<button class="btn btn-info next-step float-right bg-gradient-primary2 round_button">Next <i class="fa fa-chevron-right"></i></button>
</div> -->
<!-- <div class="col-sm-12">
	<h5>Beneficiaries&nbsp;&nbsp;<button class="btn btn-sm bg-gradient-info btn-sm" type="button" onclick="append_beneficiary()"><i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;Add beneficiary</button></h5>
	<div class="col-sm-8 p-0">
		<table class="table table-bordered table-stripped" id="tbl_beneficiaries" style="white-space: nowrap;">
			<thead>
				<tr class="table_header_dblue">
					<th style="width:60%">Name</th>
					<th>Relationship</th>
					<th style="min-width: 10px;max-width: 10px;"></th>
				</tr>
			</thead>
			<tbody id="beneficiaries_body">

			</tbody>
		</table>
	</div>
</div> -->


<!-- inputs ["sel_income_source","txt_annual_income","txt_spouse","txt_spouse_occupation","txt_no_dependent"] -->
<!--keys ["income_source","annual_income","spouse","spouse_occupation","no_dependents"] -->

