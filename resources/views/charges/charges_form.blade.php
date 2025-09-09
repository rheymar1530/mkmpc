@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		width: 1200px;
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.form-row{
		margin-top: -3px;
	}
	.nav-step .active{
		background: #dc3545 linear-gradient(180deg,#e15361,#dc3545) repeat-x !important;

	}
	.nav-pills .nav-link{
		margin-right: 15px !important;
	}
	.dark-mode a.nav_sel:hover{
		color: white !important;
		background: #e6e6e64D;
	}
	a.nav_sel:hover{
		color: black !important;
		background: #e6e6e64D;
	}
	.class_amount,.class_number{
		text-align: right;

	}
	/*remove the spinner on number*/
	input[type=number]::-webkit-inner-spin-button, 
	input[type=number]::-webkit-outer-spin-button { 
		-webkit-appearance: none; 
		margin: 0; 
	}
	.dark-mode .mandatory{
		border-color: rgba(232, 63, 82, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
		outline: 0 none !important;
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
	#tbl_charges tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 12px;
	}
	#tbl_charges tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 12px;
	}
	.frm-charges{
		height: 28px !important;
		width: 100%;    
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 13px;
	}
	.hide{
		display:  none;
	}
	.tbl-amt{
		font-size: 0.9rem !important;
	}

</style>
<div class="section_body container" style="margin-top:-15px">
	<?php
	$deduct_option = [
		1=>"Yes",
		0=>"No"
	];

	$application_fee_type = [
		1=> "All",
		2=> "First Loan",
		3=> "Renewal"
	];

	$active_selection = [
		1=>"Active",
		0=>"Inactive"
	];

	$non_deduct_option = [
		1 => "Fixed Amount",
		2 => "Divide Equally"
	];
	?>
	<?php $back_link = (request()->get('href') == '')?'/charges':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Charges Group List</a>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<form id="frm_submit_charges">
					<div class="card-body">
						<h3 class="head_lbl text-center">Fees & Charges</h3>
						<div class="row">
							<div class="col-md-12">
								<div style="margin-top:20px">
									<div class="col-md-12 p-0">
										<div class="form-row">
											<div class="form-group col-md-6">
												<label for="txt_char_name">Name</label>
												<input type="text" required class="form-control form-charges-group" id="txt_char_name" key="name" value="">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6">
												<label for="txt_char_description">Description</label>
												<textarea class="form-control form-charges-group form-control-border" id="txt_char_description" key="description" style="resize:none" rows="3"></textarea>
											</div>
										</div>
										@if($opcode == 1)
										<div class="form-row">
											<div class="form-group col-md-3">
												<label for="sel_is_active">Status</label>
												<select required class="form-control form-charges-group p-0" id="sel_is_active" key="active">
													@foreach($active_selection as $val=>$act)
													<option value="{{$val}}">{{$act}}</option>
													@endforeach
												</select>
											</div>
										</div>
										@endif
									</div>
									<div class="col-md-12 p-0">
										<button type="button" class="btn btn-sm bg-gradient-primary2" onclick="append_charges()"><i class="fa fa-plus"></i>&nbsp;Add Charges</button>
										<button type="button" class="btn btn-sm bg-gradient-danger2" onclick="create_loan_fee()"><i class="fa fa-plus"></i>&nbsp;Create Loan Fee</button>

										<div>
											<div class="row" id="tbl_charges">
												<div class="col-md-12 mt-4" id="charges_body">
													<div class="card row_charges c-border" data-id="0">
														<div class="card-body p-4">
															<div class="form-row mt-2">
																<div class="form-group col-md-12">
																	<a class="btn btn-xs bg-gradient-danger2 float-right" onclick="remove_row(this)"><i class="fas fa-times"></i></a>
																</div>
																<div class="form-group col-md-3">
																	<label class="lbl_color">Loan Fee</label>
																	<select required class="form-control frm-charges p-0" key="id_loan_fees">
																		@foreach($loan_fees as $fee)
																		<option value="{{$fee->id_loan_fees}}">{{$fee->name}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="form-group col-md-3">
																	<label class="lbl_color">Fee Calculation</label>
																	<select required class="form-control frm-charges p-0 sel_calculation_fee" key="id_fee_calculation">
																		@foreach($fee_calculations as $cal)
																		<option value="{{$cal->id_fee_calculation}}">{{$cal->description}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="form-group col-md-2">
																	<label class="lbl_color">Value</label>
																	<input type="text" name="" required class="form-control frm-charges class_amount" value="1" key="value">
																</div>
																<div class="form-group col-md-4">
																	<label class="lbl_color">Fee Base</label>
																	<select required class="form-control frm-charges p-0 sel_calculation_fee_base" key="id_calculated_fee_base">
																		@foreach($calculated_fee_base as $cal_b)
																		<option value="{{$cal_b->id_calculated_fee_base}}">{{$cal_b->description}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="form-group col-md-3">
																	<label class="lbl_color">Deduct from loan</label>
																	<select required class="form-control frm-charges p-0 sel_is_deduct" key="is_deduct">
																		@foreach($deduct_option as $val=>$ded)
																		<option value="{{$val}}">{{$ded}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="form-group col-md-4">
																	<label class="lbl_color">Option (for non deducted)</label>
																	<select required class="form-control frm-charges p-0 sel_non_deduct_option" key="non_deduct_option" disabled>
																		@foreach($non_deduct_option as $val=>$non_d)
																		<option value="{{$val}}">{{$non_d}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="form-group col-md-4">
																	<label class="lbl_color">Loan Application Fee</label>
																	<select required class="form-control frm-charges p-0" key="application_fee_type">
																		@foreach($application_fee_type as $val=>$type)
																		<option value="{{$val}}">{{$type}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="form-group col-md-12">
																	<input type="checkbox" class="has_range"><label class="lbl_color pl-2 mb-0">With Range?</label>	

																</div>
															</div>
															<div class="row parent_div_range">
																<div class="col-md-6 div_range">
																	<table class="table table-bordered">
																		<thead class="table_header_dblue">
																			<tr>
																				<th>Minimum</th>
																				<th>Maximum</th>
																				<th>Value</th>
																				<th></th>
																			</tr>
																		</thead>
																		<tbody class="range_body">
																			<tr class="row-range">
																				<td>
																					<input type="text" class="form-control frm-range tbl-amt text-right" value="0.00" key="minimum">
																				</td>
																				<td>
																					<input type="text" class="form-control frm-range tbl-amt text-right" value="0.00" key="maximum">
																				</td>
																				<td>
																					<input type="text" class="form-control frm-range tbl-amt text-right" value="0.00" key="value">
																				</td>	
																				<td class="px-2">
																					<a class="btn bg-gradient-danger2 btn-xs" onclick="remove_range(this)"><i class="fa fa-times"></i></a>
																				</td>							
																			</tr>
																		</tbody>
																	</table>
																	<button type="button" class="btn bg-gradient-primary2 btn-sm" onclick="append_range(this)"><i class="fa fa-plus"></i>&nbsp;Add More</button>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>


									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer p-2">
						@if($opcode == 1 && $credential->is_create)
						<button class="btn bg-gradient-primary2 float-right" id="btn_save_as"><i class="far fa-save"></i>&nbsp;Save As</button>
						@endif
						@if(($opcode == 0 && $credential->is_create) || ($opcode == 1 && $credential->is_edit))
						<button class="btn bg-gradient-success2 float-right" id="btn_save" style="margin-right: 10px"><i class="far fa-save"></i>&nbsp;Save</button>
						@endif

							
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


@endsection



@push('scripts')

<script type="text/javascript">
	var opcode = '{{$opcode}}';
	var submit_via; //1 SAVE ; 2 SAVE AS

	const rangeRowHTML = `<tr class="row-range">${$('tr.row-range').html()}</tr>`;
	const rangeHTML = `<div class="col-md-6 div_range">${$('.div_range').detach().html()}</div>`;

	const CurRange = jQuery.parseJSON('<?php echo json_encode($range ?? []);?>');



	$('.sel_non_deduct_option').val(0)
	$(document).on('click','#btn_save_as',function(){
		submit_via = 2;
	})
	$(document).on('click','#btn_save',function(){
		submit_via = 1;
	})
	$('#frm_submit_charges').submit(function(e){
		e.preventDefault();
		if(!validate_value()){
			Swal.fire({
				title: "Invalid Value",
				text: '',
				icon: 'warning',
				showConfirmButton : false,
				confirmButtonText: 'OK',
				confirmButtonColor: "#DD6B55",
				timer : 1500
			}); 

			return;
		}
		Swal.fire({
			title: 'Do you want to save this?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				// alert("POSTED");
				post();
			} 
		})	
	})

	// const row_charges = '<tr class="row_charges">'+($('.row_charges').html())+'</tr>'
	const row_charges = '<div class="card row_charges c-border">'+($('.row_charges').html())+'</tr>'

	function append_charges(){
		$('#charges_body').append(row_charges);
		$('.row_charges').last().find('.sel_non_deduct_option').val(0)
		// animate_element($('.row_charges').last(),1);

	}
	function remove_row(obj){
		var parent_row = $(obj).closest('div.row_charges');
		var id = parent_row.attr('data-id');

		if(id > 0){
			parent_row.addClass('hide deleted');
		}else{
			animate_element(parent_row,2);
		}
		// 
	}

	function post(){
		var charges = [];
		var charges_group = {};
		var deleted = [];
		$('.form-charges-group').each(function(){
			var key = $(this).attr('key');
			charges_group[key] = $(this).val();
		})
		$('div.row_charges').each(function(){
			var id = $(this).attr('data-id');

			if($(this).hasClass('deleted')){
				deleted.push(id);
				// break;
			}else{
				var form_charges_row = $(this).find('.frm-charges');
				var temp = {};
				var fee_calculation = $(this).find('select.sel_calculation_fee').val();
				temp['with_range'] = $(this).find('input.has_range').prop('checked') ? 1 : 0;
				temp['id_charges'] = id;
				$.each(form_charges_row,function(){
					var key = $(this).attr('key');
					if(fee_calculation == 2 && $(this).hasClass('class_amount')){
						temp[key] = decode_number_format($(this).val());
					}else{
						temp[key] = $(this).val();
					}
				});

				if(temp['with_range'] == 1){
					var temp_range = [];
					$(this).find('tr.row-range').each(function(){
						var t={};
						$(this).find('.tbl-amt').each(function(){
							t[$(this).attr('key')] = decode_number_format($(this).val());
						})
						temp_range.push(t);
					});

					console.log({temp_range});
					temp['ranges'] = temp_range;
				}



				charges.push(temp);				
			}


		})

		console.log({charges_group,charges,deleted});



		$.ajax({
			type          :          'POST',
			url           :          '/charges/post',
			data          :          {
				'parent_charges'   :  charges_group,
				'charges'          :  charges,
				'opcode'           :  opcode,
				'submit_via'       :  submit_via, 
				'deleted'          :   deleted,
				'id_charges_group' :  '<?php echo $details->id_charges_group ?? 0 ?>'
			},
			beforeSend    :          function(){
				show_loader()
			},
			success       :          function(response){
				hide_loader()
				console.log({response})
				if(response.RESPONSE_CODE == "success"){
					Swal.fire({
						title: "Charges successfully saved",
						text: '',
						icon: 'success',
						showConfirmButton : false,
						timer  : 1300
					}).then((result) => {
						window.location = '/charges/view/'+response.id_charges_group;
					});						
				}else if(response.RESPONSE_CODE == "CREDENTIAL_ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showConfirmButton : false,
						timer : 1500
					});	
				}
			},error: function(xhr, status, error) {
				hide_loader()
				var errorMessage = xhr.status + ': ' + xhr.statusText
				isClick = false;
				Swal.fire({
					title: "Error-" + errorMessage,
					text: '',
					icon: 'warning',
					confirmButtonText: 'OK',
					confirmButtonColor: "#DD6B55"
				});
			}
		})
		
	}

	function format_value(obj){

		console.log({fee_calculation});
	}

	function number_format(number){
		var result =  number.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		return result;
	}

	function decode_number_format(number){
		var result=number.replace(/\,/g,''); // 1125, but a string, so convert it to number
		result=parseFloat(result,10);
		return result;
	}

	$(document).on("focus",".class_amount",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';
		}
		$(this).val(decode_number_format(val));	
	})

	$(document).on("blur",".class_amount",function(){
		var val = $(this).val();
		var parent_row = $(this).closest('div.row_charges');
		var fee_calculation = parent_row.find('select.sel_calculation_fee').val();

		console.log({fee_calculation})
		if(!$.isNumeric(val)){
			val = 0;
		}
		if(fee_calculation == 2){
			$(this).val(number_format(parseFloat(val)));

		}
	})
	$(document).on("focus",".tbl-amt",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';
		}
		$(this).val(decode_number_format(val));	
	})

	$(document).on("blur",".tbl-amt",function(){
		var val = $(this).val();
		
		if(!$.isNumeric(val)){
			val = 0;
		}
		$(this).val(number_format(parseFloat(val)));
	})

	$(document).on('change','.sel_calculation_fee',function(){
		var val = $(this).val();
		var parent_row = $(this).closest('div.row_charges');
		if(val == 1){ // percentage
			parent_row.find('.class_amount').val(0);
		}else{ // fix
			parent_row.find('.class_amount').val('0.00');
		}
		initialize_calculate_fee(parent_row,1)
	})
	function validate_value(){
		var valid = true;
		$('.class_amount').each(function(){

			if(!$(this).is(":hidden") && !$(this).prop('disabled')){
				if($(this).val() == 0 || $(this).val() == '0.00'){
					$(this).addClass('mandatory');
					valid = false;
				}		
			}

		})

		return valid;
	}
	$(document).on("keyup",".class_amount",function(){
		var val = $(this).val();
		if(val != 0 && val != '0.00'){
			$(this).removeClass('mandatory')
		}
	})
	function initialize_calculate_fee(parent_row,value){
		var fee_cal = parent_row.find('select.sel_calculation_fee').val();
		if(fee_cal == 1){
			$fee_base = false;
			$value = value;
		}else{
			$fee_base = true;
			$value = 0;			
		}
		parent_row.find('select.sel_calculation_fee_base').attr('disabled',$fee_base);
		parent_row.find('select.sel_calculation_fee_base').val($value);
	}

	function create_loan_fee(){
		window.open('/maintenance/loan_fees','_blank')
	}
	function initialize_non_deducted_option(parent_row,value){
		if(value == 1){
			parent_row.find('.sel_non_deduct_option').attr('disabled',true);
		}else{
			parent_row.find('.sel_non_deduct_option').attr('disabled',false);
		}
	}
	$(document).on("change",".sel_is_deduct",function(){
		var val = $(this).val();
		var parent_row = $(this).closest('div.row_charges');
		if(val == 0){
			parent_row.find('.sel_non_deduct_option').val(1)
		}else{
			parent_row.find('.sel_non_deduct_option').val(0)
		}
		initialize_non_deducted_option(parent_row,val)
	})

	const append_range = (obj)=>{
		var parent_card = $(obj).closest('div.row_charges');
		parent_card.find('.range_body').append(rangeRowHTML);

	}
	const remove_range = (obj)=>{
		$(obj).closest('.row-range').remove();
	}

	$(document).on('click','.has_range',function(){
		var checked = $(this).prop('checked');
		var parent_card = $(this).closest('div.row_charges');

		if(checked){
			parent_card.find('.parent_div_range').html(rangeHTML);
		}else{
			parent_card.find('.parent_div_range').html('');

		}
		parent_card.find('.frm-charges[key="value"]').prop('disabled',checked)
	})
</script>
@if($opcode == 1)
<script type="text/javascript">
	var charges = jQuery.parseJSON('<?php echo json_encode($charges) ?>');
	var details = jQuery.parseJSON('<?php echo json_encode($details) ?>');

	$('.form-charges-group').each(function(){
		var key =  $(this).attr('key');
		$(this).val(details[key]);
	})
	$('#charges_body').html('');

	$.each(charges,function(i,item){
		append_charges();

		var last_card = $('div.row_charges').last();
		last_card.attr('data-id',item.id_charges)


		var last_form_charges = last_card.find('.frm-charges');
		$(last_form_charges).each(function(){
			var attr = $(this).attr('key')
			$(this).val(item[attr])
		})
		initialize_non_deducted_option(last_card,item.is_deduct)
		initialize_calculate_fee(last_card,item.id_calculated_fee_base);

		if(item.with_range == 1){

			last_card.find('.has_range').trigger('click');
			last_card.find('.range_body').html('');
			let ranges = CurRange[item.id_charges] ?? [];

			$.each(ranges,function(r,ra){
				append_range(last_card);
				var lastRange = $('tr.row-range').last();
				lastRange.find('.tbl-amt').each(function(){
					$(this).val(number_format(parseFloat(ra[$(this).attr('key')]),2) )
				});
			})
			// return;
		}
	})

	console.log({charges})
</script>
@endif
@endpush
