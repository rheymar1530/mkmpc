
@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.card-header .fa {
	  transition: .3s transform ease-in-out;
	}
	.card-header .collapsed .fa {
	  transform: rotate(90deg);
	  padding-right: 3px;
	}
	.class_amount{
		text-align: right;

	}
	.custom_card_header{
		padding: 2px 2px 2px 10px !important;
	}
	.custom_card_header > h5{
		margin-bottom: unset;
		font-size:25px;
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.btn-circle {
		width: 25px;
		height: 25px;
		text-align: center;
		padding: 6px 0;
		font-size: 13px;
		line-height: 1;
		border-radius: 15px;
		
	}
	.asset-input{
		height: 27px !important;
		width: 100%;    
		font-size: 13px;
	}
	.tbl-inputs tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
		text-align: center;
	}
	.tbl-inputs tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.frm_loans,.frm-requirements{
		height: 27px !important;
		width: 100%;    
		font-size: 13px;
	}
	.text_center{
		text-align: center;
		font-weight: bold;
	}
</style>

		
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="form-group row p-0" style="margin-top:15px">
					<label for="sel_id_payroll_mode" class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Asset Account&nbsp;</label>
					<div class="col-md-5">
						<select class="form-control in_payroll p-0" id="sel_id_payroll_mode" key="id_payroll_mode">

						</select>
					</div>
				</div>
				<div class="form-group row p-0" style="">
					<label for="sel_id_payroll_mode" class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Total Amount&nbsp;</label>
					<div class="col-md-3">
						<input type="text" class="form-control in_payroll class_amount" id="sel_id_payroll_mode" value="0.00">
					</div>
				</div>
				@for($j=0;$j<3;$j++)
				<div class="card">
					<div class="card-header custom_card_header bg-gradient-primary">
						<h5><!-------Asset {{$j+1}} --------->
						<span class="float-right">
							<a href="#asset_{{$j+1}}" class="btn btn-success btn-circle" data-toggle="collapse" data-target="#asset_{{$j+1}}" aria-expanded="true" aria-controls="asset_{{$j+1}}"><i class="fa fa-chevron-down"></i></a>
								 <button type="button" class="btn btn-danger btn-circle" style="margin-right: 10px;"><i class="fas fa-times"></i></button></span>
			            </h5>
			        </div>
					<div class="card-body">
						<div class="form-row">
							<div class="form-group col-md-5" style="">
								<label>Description</label>
								<input type="text" name="" class="form-control asset-input" value="" key="first_name">
							</div>
							<div class="form-group col-md-1" style="">
								<label>Quantity</label>
								<input type="text" name="" class="form-control asset-input" value="" key="middle_name">
							</div>
							<div class="form-group col-md-2" style="">
								<label>Unit Cost</label>
								<input type="text" name="" class="form-control asset-input class_amount" value="" key="last_name">
							</div>
							<div class="form-group col-md-2" style="">
								<label>Total Amount</label>
								<input type="text" name="" class="form-control asset-input class_amount" value="" key="last_name">
							</div>
						</div>	
						<div class="form-row show" id="asset_{{$j+1}}">
							<div class="col-md-10 p-0">
								<!-- <h5>Other Fees</h5> -->
								<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
									<table class="table table-bordered table-stripped table-head-fixed tbl-inputs" style="white-space: nowrap;">
										<thead>
											<tr>
												<th class="table_header_dblue" width="3%"></th>
												<th class="table_header_dblue">Serial No.</th>
												<th class="table_header_dblue">Remaks</th>
											</tr>
										</thead>
										<tbody id="loan_fee_display">
											@for($i=0;$i<5;$i++)
											<tr class="row_fees" >
												<td class="text_center">{{$i+1}}</td>
												
												<td><input type="text" name="" required class="form-control asset-input txt_fee_amount"></td>
												<td><input type="text" name="" required class="form-control asset-input txt_fee_amount" placeholder="Optional"></td>
											</tr>
											@endfor
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				@endfor
			</div>
		</div>
	</div>
</div>

@endsection

