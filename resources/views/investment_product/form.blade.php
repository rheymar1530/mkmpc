@extends('adminLTE.admin_template')
@section('content')
<style>
	@media (min-width: 1200px) {
		.container{
			max-width: 900px;
		}
	}
	.tbl_in_prod tr>th ,.tbl_fees tr>th,.tbl-inputs tr>th{
		padding: 3px !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 13px;
		text-align: center;
	}
	.tbl_in_prod tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 13px;
	}
	.frm_inv_prod{
		height: 27px !important;
		width: 100%;    
		font-size: 15px;
	}
	.select2-selection__choice{
		background: #4d94ff !important;
	}
	.deleted{
		display: none;
	}
</style>
<?php


$prod_status = array(
	0=>"Active",
	10=>"Inactive"
);
?>
<div class="container section_body">
	<?php $back_link = (request()->get('href') == '')?'/investment_product':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Investment Product List</a>
	<div class="card">
		<form id="frm_investment_form">
			<div class="card-body">
				<div class="text-center mb-4">
					<h3 class="head_lbl">Investment Product
						@if(isset($details->id_investment_product))
						<small>(ID# {{$details->id_investment_product}})</small>
						@endif
					</h3>
				</div>
				@if($opcode == 1)
				<div class="form-row mb-3">
					<div class="form-group col-md-4">
						<label class="mb-0">Status</label>
						<select class="form-control" id="sel_status">
							@foreach($prod_status as $code=>$status)
							<option value="{{$code}}" <?php echo($code == $details->status)?'selected':''; ?> >{{$status}}</option>
							@endforeach
							
						</select>
					</div>
				</div>
				@endif
				<div class="form-row">
					<div class="form-group col-md-8">
						<label class="mb-0">Product Name</label>
						<input type="text" class="form-control in-inv-details" name="product_name" value="{{$details->product_name ?? ''}}">
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-4">
						<label class="mb-0">Minimum Amount</label>
						<input type="text" class="form-control in-inv-details class_amount text-right" name="min_amount" value="{{number_format($details->min_amount ?? 0,2)}}">
					</div>
					<div class="form-group col-md-4">
						<label class="mb-0">Maximum Amount</label>
						<input type="text" class="form-control in-inv-details class_amount text-right" name="max_amount" value="{{number_format($details->max_amount ?? 0,2)}}">
					</div>
				</div>

				<div class="form-row">
					<!-- <div class="form-group col-md-4">
						<label class="mb-0">Withdrawable Part</label>
						<select class="form-control in-inv-details p-0" name="id_withdrawable_part">
							@foreach($withdraw_parts as $wp)
							<option value="{{$wp->id_withdrawable_part}}" <?php echo ($wp->id_withdrawable_part == ($details->id_withdrawable_part ?? 0))?'selected':''; ?>>{{$wp->description}}</option>
							@endforeach
						</select>
					</div> -->
					<div class="form-group col-md-4">
						<label class="mb-0">Interest Type</label>
						<select class="form-control in-inv-details p-0" name="interest_type">
							@foreach($interest_types as $it)
							<option value="{{$it->id_interest_type}}" <?php echo ($it->id_interest_type == ($details->id_interest_type ?? 0))?'selected':''; ?> >{{$it->description}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-md-4">
						<label class="mb-0">Interest Period</label>
						<select class="form-control in-inv-details p-0" name="interest_period" id="sel_interest_period">
							@foreach($interest_periods as $ip)
							<option value="{{$ip->id_interest_period}}" <?php echo ($ip->id_interest_period == ($details->id_interest_period ?? 0))?'selected':''; ?>>{{$ip->description}}</option>
							@endforeach						
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-8">
						<label class="mb-0">Principal Account</label>
						<select class="form-control in-inv-details p-0 sel_chart" name="id_chart_account" id="sel_acc_principal">
							@foreach($chart_accounts as $ca)
							<option value="{{$ca->id_chart_account}}" <?php echo ($ca->id_chart_account == ($details->id_chart_account ?? 0))?'selected':''; ?> >{{$ca->account}}</option>
							@endforeach
						</select>
					</div>

				</div>
				<div class="form-row">
					<div class="form-group col-md-8">
						<label class="mb-0">Interest Account</label>
						<select class="form-control in-inv-details p-0 sel_chart" name="interest_chart_account" id="sel_acc_interest">
							@foreach($chart_accounts as $ca)
							<option value="{{$ca->id_chart_account}}" <?php echo ($ca->id_chart_account == ($details->interest_chart_account ?? 0))?'selected':''; ?> >{{$ca->account}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-md-9 col-12">
						<h5 class="lbl_color">Terms</h5>
						<table class="table tbl_in_prod table-bordered">
							<thead>
								<tr class="table_header_dblue">
									<th><span id="spn_dur_type"></span></th>
									<th>Interest Rate (%)</th>
									<th style="width: 5%;"></th>
								</tr>
							</thead>
							<tbody id="terms_body">
								<tr class="terms_row">
									<td><input type="number" class="form-control  frm_inv_prod term_in" value="0" data-key="terms"></td>
									<td><input type="text" class="form-control  frm_inv_prod text-right term_in" data-key="interest_rate" value="0"></td>
									<td><a class="btn btn-xs bg-gradient-danger2 w-100" onclick="delete_terms(this)"><i class="fa fa-trash"></i></a></td>
								</tr>
							</tbody>
							
						</table>
						<a class="btn btn-sm bg-gradient-primary2 float-right" onclick="append_terms()"><i class="fa fa-plus pr-2"></i>Add Terms</a>
					</div>					
				</div>
				<div class="form-row d-flex align-items-end">
					<div class="form-group col-md-12">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="chk_withdraw_before_maturity" <?php echo (($details->withdraw_before_maturity ?? 0) == 1)?'checked':''; ?> >
							<label class="custom-control-label mt-1" for="chk_withdraw_before_maturity">Can be withdrawn before maturity date?</label>
						</div>									
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-12 col-12">
						<h5 class="lbl_color">Fees</h5>
						<table class="table tbl_in_prod table-bordered">
							<thead>
								<tr class="table_header_dblue">
									<th width="35%">Fee Type</th>
									<th width="20%">Fee Calculation</th>
									<th>Value</th>
									<th>Calculated Fee Based</th>
									<th width="2%"></th>
								</tr>
							</thead>
							<tbody id="fees_body">
								<tr class="row_fees">
									<td>
										<select required class="form-control frm_inv_prod p-0 sel-fee-type" key="id_fee_type">
											@foreach($fee_types as $fee_type)
											<option value="{{$fee_type->id_fee_type}}">{{$fee_type->description}}</option>
											@endforeach
										</select>
									</td>
									<td>
										<select required class="form-control frm_inv_prod p-0 sel_calculation_fee" key="id_fee_calculation">
											@foreach($fee_calculations as $cal)
											<option value="{{$cal->id_fee_calculation}}">{{$cal->description}}</option>
											@endforeach
										</select>
									</td>
									<td><input type="text" name="" required class="form-control frm_inv_prod class_amount" value="1" key="value"></td>
									<td>
										<select required class="form-control frm_inv_prod p-0 sel_calculation_fee_base" key="id_calculated_fee_base">
											@foreach($calculated_fee_base as $cal_b)
											<option value="{{$cal_b->id_calculated_fee_base}}">{{$cal_b->description}}</option>
											@endforeach
										</select>
									</td>
									<td><a class="btn btn-xs bg-gradient-danger2 w-100" onclick="remove_row(this)"><i class="fa fa-trash"></i></a></td>
								</tr>												
							</tbody>
						</table>
						<button type="button" class="btn btn-sm bg-gradient-primary2 float-right" onclick="append_fees()"><i class="fa fa-plus"></i>&nbsp;Add Fees</button>
					</div>
				</div>
				<hr>

				<div class="form-row d-flex align-items-end">
					<div class="form-group col-md-12">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="chk_member_only" <?php echo (($details->member_only ?? 0) == 1)?'checked':''; ?>>
							<label class="custom-control-label mt-1" for="chk_member_only">For members only?</label>
						</div>									
					</div>
				</div>
				<div class="form-row mt-3">
					<div class="form-group col-md-8" id="div_approver">
						<label class="mb-0">Approver(s)</label>
						<select class="form-control p-0" id="sel_approvers" multiple>
							@foreach($approvers as $ap)
							<option value="{{$ap->id}}" <?php echo (in_array($ap->id,$selected_approver ?? []) ? 'selected':'');  ?> >{{$ap->name}}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
			<div class="card-footer">
				@if($opcode == 1)
				<button class="btn btn-md bg-gradient-primary2 round_button float-right ml-2" id="btn_save_as">Save As</button>
				@endif
				<button class="btn btn-md bg-gradient-success2 round_button float-right" id="btn_save">Save</button>
			</div>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	const OPCODE = '<?php echo $opcode;?>';
	const ID_INVESTMENT_PRODUCT = '<?php echo ($details->id_investment_product ?? 0) ?>';
	const TERM_ROW = `<tr class="terms_row">${$('.terms_row').last().html()}</tr>`;

	
	let SAVE_MODE  = 0;


	$(document).ready(function(){
		initialize_period();
		$('.sel_chart').select2();
	});
	$(document).on('select2:open', (e) => {
		const selectId = e.target.id

		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
			key,
			value,
			) {
			value.focus()
		})
	})	
	$('#sel_approvers').select2({'parent' : $('#div_approver')});

	$(document).on("focus",".class_amount",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';
		}
		$(this).val(decode_number_format(val)); 
	})
	$(document).on("blur",".class_amount",function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			val = 0;
		}
		$(this).val(number_format(parseFloat(val)));
	});

	$(document).on('click','#btn_save_as',function(){
		SAVE_MODE = 1;
	});

	$(document).on('click','#btn_save',function(){
		SAVE_MODE = 0;
	});

	function initialize_period(){
		let $period_type = $('#sel_interest_period').val();

		if($period_type == 1){
			$t = "No of Month(s)";
		}else if($period_type == 2){
			$t = "No Quarter(s)";
		}else{
			$t = "No of Year(s)";
		}

		$('#spn_dur_type').text($t);
	}
	$('#sel_interest_period').change(function(){
		initialize_period();
	})
	$('#frm_investment_form').submit(function(e){
		e.preventDefault();

		console.log({SAVE_MODE});
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
	})
	function post(){
		var $product_details = {};
		$('.in-inv-details').each(function(){
			var val = $(this).val();
			val = $(this).hasClass('class_amount')?decode_number_format(val):val;
			$product_details[$(this).attr('name')]= val;
		});

		$product_details['can_be_with_maturity'] = ($('#chk_withdraw_before_maturity').prop('checked'))?1:0;
		$product_details['member_only'] = ($('#chk_member_only').prop('checked'))?1:0;



		var $fees = [];
		$('tr.row_fees').each(function(){
			var temp = {};
			$(this).find('.frm_inv_prod').each(function(){
				val = ($(this).attr('key') == 'value')?decode_number_format($(this).val()):$(this).val();
				temp[$(this).attr('key')] = val;
			});
			$fees.push(temp);
		})



		$compiled_terms = compile_terms();

		let $approvers = $('#sel_approvers').val();

		$.ajax({
			type       :        'GET',
			url        :        '/investment_product/post',
			data       :        {'opcode' : OPCODE,
			'id_investment_product' : ID_INVESTMENT_PRODUCT,
			'product_details' : $product_details,
			'approvers' : $approvers,
			'save_mode' : SAVE_MODE,
			'status' : $('#sel_status').val(),
			'terms' : $compiled_terms['output'],
			'deleted_terms' : $compiled_terms['deleted'],
			'fees' : $fees},
			beforeSend :        function(){
				$('.in-inv-details').removeClass('mandatory');
				$('.mandatory').removeClass('mandatory');
				show_loader();
			},
			success    :        function(response){
				console.log({response});
				hide_loader();

				if(response.RESPONSE_CODE == "SUCCESS"){
					var redirect_link = '/investment_product/view/'+response.id_investment_product;
					Swal.fire({

						title: "Investment product successfully Saved ",
						html : "<a href='"+redirect_link+"'>Investment Product ID#"+response.id_investment_product+"</a>",
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Create Another Investment Product',
						cancelButtonText: 'Back to Investment Product List',
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/investment_product/create?href="+'<?php echo $back_link;?>';
						}else{
							window.location = '<?php echo $back_link;?>';
						}
					});	
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showConfirmButton : false,
						timer : 2500
					});	


					if(response.fields != undefined){
						$fields = response.fields;
						$.each($fields,function(i,field){
							$(`.in-inv-details[name="${field}"]`).addClass('mandatory');
						})
					}

					if(response.term_field != undefined){
						$term_field =response.term_field;
						$.each($term_field,function(i,item){
							$.each(item,function(k,key){
								$('tr.terms_row').eq(i).find(`input[data-key="${key}"]`).addClass('mandatory')
							})
						});
					}
				}
			},error: function(xhr, status, error) {
				hide_loader();
				var errorMessage = xhr.status + ': ' + xhr.statusText;
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
	// $('tr.terms_row').eq(1).find('input[data-key="interest_rate"]').addClass('mandatory')

	function append_terms(){
		$('tbody#terms_body').append(TERM_ROW);
	}



	function delete_terms(elem){
		var parent_row = $(elem).closest('tr.terms_row');
		var id = $(parent_row).attr('id');


		parent_row.hide(300,function(){
			if(id == undefined){
				$(this).remove();
			}else{
				$(this).addClass('deleted');
			}
			// 
		})
	}

	function compile_terms(){
		var output = [];
		var deleted  =[];
		$('.terms_row').each(function(){
			var id = $(this).attr('id');
			if(!$(this).hasClass('deleted')){
				var temp={};
				$(this).find('.term_in').each(function(){
					var key = $(this).attr('data-key');
					temp[key] = $(this).val();
				});
				if(id != undefined){
					temp['id'] = id;
				}
				output.push(temp);				
			}else{
				deleted.push(id);
			}

		})

		return {output,deleted};
	}
</script>


@if($opcode == 1)
<script type="text/javascript">
	const TERMS = jQuery.parseJSON('<?php echo json_encode($terms ?? []); ?>');
	const FEES = jQuery.parseJSON('<?php echo json_encode($fees ?? []); ?>');
	$('tbody#terms_body').html('');

	$(document).ready(function(){
		$.each(TERMS,function(i,item){
			append_terms();
			$last_row = $('tr.terms_row').last();
			$last_row.attr('id',item.id_investment_product_terms);
			$($last_row).find('input[data-key="terms"]').val(item.terms);
			$($last_row).find('input[data-key="interest_rate"]').val(item.interest_rate);
		});		

		$.each(FEES,function(i,item){
			append_fees();
			$last = $('.row_fees').last();
			$last.find('.sel-fee-type').val(item.id_fee_type).trigger('change');
			$last.find('.frm_inv_prod[key="id_fee_calculation"]').val(item.id_fee_calculation).trigger('change');
			$last.find('.frm_inv_prod[key="id_fee_calculation"]').val(item.id_fee_calculation).trigger('change');
			$last.find('.frm_inv_prod[key="value"]').val(number_format(item.value,2));
			$last.find('.frm_inv_prod[key="id_calculated_fee_base"]').val(item.id_calculated_fee_base);
		})
	})

	console.log({TERMS});
</script>
@endif

<script type="text/javascript">
	const row_fees = '<tr class="row_fees">'+($('.row_fees').html())+'</tr>';
	const fee_options = jQuery.parseJSON('<?php echo json_encode($fee_types ?? []) ?>');
	const no_fees = '<tr id="tr_no_fees"><td colspan="5" class="text-center">No fees</td></tr>';


	$('#fees_body').html('');
	init_no_fees();

	function init_no_fees(){
		if($('tr.row_fees').length > 0){
			$('#tr_no_fees').remove();
		}else{
			$('#fees_body').html(no_fees);
		}
	}
	function append_fees(){
		$('#fees_body').append(row_fees);
		$('.row_fees').last().find('.sel_non_deduct_option').val(0)
		animate_element($('.row_fees').last(),1);
		// $('.row_fees').last().find('.sel-fee-type').val(0);
		init_fees($('.row_fees').last(),true);
		init_no_fees();
	}
	function remove_row(obj){
		var parent_row = $(obj).closest('tr.row_fees');
		parent_row.find('.sel-fee-type').val(0).trigger('change');
		parent_row.remove();
		// animate_element(parent_row,2);

		init_no_fees();
		// $this_select.
	}
	$(document).on('change','.sel_calculation_fee',function(){
		var val = $(this).val();
		var parent_row = $(this).closest('tr.row_fees');
		if(val == 1){ // percentage
			parent_row.find('.class_amount').val(0);
		}else{ // fix
			parent_row.find('.class_amount').val('0.00');
		}
		initialize_calculate_fee(parent_row,1)
	});
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

	function init_fees(current_row,add){
		$this_select = current_row.find('.sel-fee-type');
		var sel = [];
		var out = '';
		$('select.sel-fee-type').not($this_select).each(function(){
			sel.push(parseInt($(this).val()));
		});

		$.each(fee_options,function(i,item){
			if($.inArray(item.id_fee_type,sel) < 0){
				out += `<option value="${item.id_fee_type}">${item.description}</option>`;
			}
		});
		current_row.find('.sel-fee-type').html(out);

		$this_select.trigger('change');


		return;

		$('tr.row_fees').not(current_row).each(function(){
			var select = $(this).find('.sel-fee-type');
			var val = select.val();

			var out = '';

			$.each(fee_options,function(i,item){
				if($.inArray(item.id_fee_type,sel) < 0){
					out += `<option value="${item.id_fee_type}">${item.description}</option>`;
				}
			});

			select.html(out);

			if(!add){
				console.log({val})
				select.val(val);
			}
		});
	}

	// function init_select_fees()
	$(document).on('change','.sel-fee-type',function(){
		var parent_row = $(this).closest('tr.row_fees');
		var val_current = $(this).val();

		//remove all
		$('tr.row_fees').not(parent_row).each(function(){
			var select = $(this).find('.sel-fee-type');
			var val = select.val();		
			var cur_selected = [];
			$this_select = $(this).find('.sel-fee-type');
			$('select.sel-fee-type').not($this_select).each(function(){
				cur_selected.push(parseInt($(this).val()));
			});			
			var out = '';
			$.each(fee_options,function(i,item){
				if($.inArray(item.id_fee_type,cur_selected) < 0){
					out += `<option value="${item.id_fee_type}">${item.description}</option>`;
				}
			});
			$this_select.html(out);
			$this_select.val(val);
		})
		
	})
</script>
@endpush