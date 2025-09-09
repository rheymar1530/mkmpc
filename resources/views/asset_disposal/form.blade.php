@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.control-label{
		font-size: 14px;
	}
	.asset-input{
		height: 27px !important;
		width: 100%;    
		font-size: 15px;
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
	.tbl-inputs .select2-selection {
		height: 27px !important;
	}

	.select2-container--default .select2-selection--single {
		padding: unset;
	}
	.select2-container--default .select2-selection--single {
		padding: unset;
	}
	.text_center{
		text-align: center;
	}
	.class_amount{
		text-align: right;
	}
	.ind_loss_gain{
		font-size: 15px;
	}
	.swal2-deny{
		padding: 0.375rem 0.75rem !important;
	}
</style>
<div class="container-fluid section_body">
	<div class="col-md-12">
		<?php $back_link = (request()->get('href') == '')?'/asset_disposal':request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Asset Disposal List</a>
		<div class="card">
			<form id="frm_submit">
				<div class="card-body">
					<h3 class="head_lbl text-center mb-3">Asset Disposal 
						@if($opcode ==1)
						<small>(ID# {{$details->id_asset_disposal}})</small>
						@endif
					</h3>
					<div class="col-md-12 p-0">
						<div class="form-group row p-0" style="margin-top: 20px !important;">
							<label class="col-md-2 control-label col-form-label col-md-cus" style="text-align: left">Date of Disposal&nbsp;</label>
							<div class="col-md-3">
								<input type="date" class="form-control" id="txt_disposal_date"  value="{{$details->date ?? $current_date}}" onkeydown="return false">
							</div>
						</div>
					</div>
					<div class="col-md-12 p-0">
						<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
							<table class="table table-bordered table-stripped table-head-fixed tbl-inputs" style="white-space: nowrap;">
								<thead>
									<tr>
										<th class="" width="2%"></th>

										<th class="" width="40%">Asset</th>
										<th class="" >Qty</th>
										<th class="">Purchase Cost</th>
										<th class="">Accumulated Depreciation</th>
										<th class="" width="8%">Current Value</th>
										<th class="" width="8%">Qty Disposed</th>

										<th class="" width="2%"></th>
									</tr>
								</thead>
								<tbody id="asset_body">
									<?php
									$loop_count = ($opcode==0)?1:count($assets);
									?>

									@for($i=0;$i<$loop_count;$i++)
									<tr class="asset_row" row-asset="{{$assets[$i]->asset_code ?? ''}}">
										<td class="text_center asset_item_counter">{{($i+1)}}</td>

										<td>
											<select class="form-control p-0 select2 asset-input asset_item sel-asset-in w_border" required <?php echo ($allowed_post)?'':'disabled' ?>>
												@if(isset($assets[$i]->asset_code))
												<option value="{{$assets[$i]->asset_code}}">{{$assets[$i]->description}}</option>
												@endif
											</select>
										</td>

										<td><input type="text" name="" required class="form-control w_border asset-input asset_item txt_rem_qty" value="{{$assets[$i]->quantity_remaining ?? ''}}" disabled></td>

										<td><input type="text" name="" required class="form-control w_border asset-input asset_item txt_pur_cost class_amount" value="{{$assets[$i]->purchase_cost ?? ''}}" disabled></td>
										<td><input type="text" name="" required class="form-control w_border asset-input asset_item txt_acc_dep class_amount" value="{{$assets[$i]->accumulated_depreciation ?? ''}}" disabled></td>
										<td><input type="text" name="" required class="form-control w_border asset-input asset_item txt_cur_value class_amount" value="{{$assets[$i]->current_value ?? ''}}" disabled></td>

										<td><input type="number" name="" required class="form-control w_border asset-input asset_item txt_qty" value="{{$assets[$i]->quantity ?? 0}}" min="1" <?php echo ($allowed_post)?'':'disabled' ?>></td>
										<td><a onclick="remove_row(this)" style="margin-left:5px;margin-top: 10px !important;"><i class="fa fa-times"></i></a></td>
									</tr>
									@endfor
								</tbody>
							</table>
						</div>
					</div>
					@if($allowed_post)
					<button type="button" class="btn btn-xs bg-gradient-primary2 col-md-12" onclick="add_asset_item()"><i class="fa fa-plus"></i> Add Asset</button>
					@endif
					<div class="col-md-12 p-0">
						<div class="form-group row p-0" style="margin-top: 20px !important;">
							<label class="col-md-2 control-label col-form-label col-md-cus" style="text-align: left">Total Disposed Amount&nbsp;</label>
							<div class="col-md-3">
								<input type="text" class="form-control asset-input class_amount" id="txt_total_disposed_amount" disabled="" value="0.00">
							</div>
						</div>
					</div>
					<?php
					if(isset($crv_details)){

					}

					?>
					<div class="col-md-12 p-0">
						<div class="form-group clearfix">
							<div class="icheck-primary d-inline">
								<input type="checkbox" id="chk_cash_proceed" <?php echo (isset($crv_details))?"checked":""; ?>  <?php echo ($allowed_post)?'':'disabled' ?> >
								<label for="chk_cash_proceed">
									With Cash Proceeds ?
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-12 p-0" id="div_cash_proceed" style="<?php echo (!isset($crv_details))?'display: none;':''; ?>">
						<div class="form-group row" style="">
							<label class="col-md-2 control-label col-form-label col-md-cus" style="text-align: left">OR Number&nbsp;</label>
							<div class="col-md-3">
								<select class="form-control asset-input p-0" id="sel_or">
									@if(isset($crv_details))
									<option value="{{$crv_details->id_cr}}">{{$crv_details->description}}</option>
									@endif
								</select>
							</div>
							<div class="col-md-2"><button type="button" class="btn btn-sm bg-gradient-danger2" style="height: 28px;" onclick="remove_or()">Remove</button></div>

						</div>
						<div class="form-group row p-0">
							<label class="col-md-2 control-label col-form-label col-md-cus" style="text-align: left">Amount Received&nbsp;</label>
							<div class="col-md-3">
								<input type="text" class="form-control asset-input class_amount" id="txt_amount_received" value="0.00" disabled>
							</div>
						</div>
						<div class="form-group row p-0">
							<label class="col-md-2 control-label col-form-label col-md-cus" style="text-align: left">Gain/Loss&nbsp;</label>
							<div class="col-md-3">
								<input type="text" class="form-control asset-input class_amount" id="txt_loss_gain" value="0.00" disabled>
							</div>
							<div class="col-md-3"><span class="badge  ind_loss_gain"></span></div>
						</div>
					</div>
				</div>

				<div class="card-footer">
					@if($allowed_post)
					<button class="btn  bg-gradient-success2 float-right" style="margin-left:10px">Save</button>
					@endif
					@if($opcode == 1)
					<button type="button" class="btn bg-gradient-danger2 float-right" style="margin-left:10px" onclick="print_page('/journal_voucher/print/{{$details->id_journal_voucher}}')"><i class="fas fa-print" ></i>&nbsp;Print Journal Voucher (JV# {{$details->id_journal_voucher}})</button>
					@if($allowed_post)
					<button class="btn  bg-gradient-warning2 float-right" type="button" onclick="show_status_modal()">Cancel Asset Disposal</button>
					@endif
					@endif
				</div>

			</form>
		</div>
	</div>
</div>
@if($opcode == 1)
@include('global.print_modal')
@endif

@if($allowed_post && $opcode == 1)
@include('asset_disposal.status_modal')
@endif
@endsection

@push('scripts')

@if($opcode == 1)
<script type="text/javascript">
	$(document).ready(function(){
		var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_print_jv"));
		console.log({redirect_data});
		if(redirect_data != null){
			if(redirect_data.show_print_jv == 1){
				if(redirect_data.id_journal_voucher == '<?php echo $details->id_journal_voucher; ?>'){
					print_page("/journal_voucher/print/"+redirect_data.id_journal_voucher)
					console.log("SHOW PRINT MODAL")
					localStorage.removeItem("redirect_print_jv");
				}
			}
		}

		// var localStorage.setItem("show_print_waiver", 1);
	})
</script>
@endif
<script type="text/javascript">
	// intialize_select2();
	const asset_row = '<tr class="asset_row" >'+$('.asset_row').eq(0).html()+'</tr>';

	let crdv_holder = {};

	<?php

	if(isset($crv_details)){
		echo "crdv_holder['id_crv']=".$crv_details->id_cr.";";

		echo "crdv_holder['amount_received']=".$crv_details->amount.";";

	}
	?>

	$(document).ready(function(){
		<?php

		if($allowed_post){
			echo 'intialize_select_or();';
		}


		if($opcode == 0){
			echo "intialize_select2($('.sel-asset-in').last()); ";
		}else{
			if($allowed_post){
				echo "intialize_select2($('.sel-asset-in')); ";
			}

			echo "	compute_disposed_amount();loss_gain_compute();";

			if(isset($crv_details)){
				echo "$('#txt_amount_received').val(number_format($crv_details->amount,2))";
			}

			

		}


		?>		


		
	})



	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});	


	function compute_disposed_amount(){

		var total = 0;
		$('tr.asset_row').each(function(){
			var row_asset = $(this).attr('row-asset');
			if(row_asset != undefined){
				var amount = decode_number_format($(this).find('.txt_cur_value').val())
				var qty = $(this).find('.txt_qty').val();

				total += (amount*qty);

			}
		})
		$('#txt_total_disposed_amount').val(number_format(total));

		return total;
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
		if(!$.isNumeric(val)){
			val = 0;
		}
		$(this).val(number_format(parseFloat(val)));
	}) 


	$(document).on('click','#chk_cash_proceed',function(){
		var checked = $(this).prop('checked');
		if(checked){
			$('#div_cash_proceed').show();
		}else{
			$('#div_cash_proceed').hide();
		}
	})

	$('#sel_or').change(function(){
		var val = $(this).val();

		if(val == null){
			console.log("----------------------")
			return;
		}

		$.ajax({
			type        :        'GET',
			url         :        '/asset_disposal/crv/details',
			data        :        {'id_crv' : val},
			beforeSend  :        function(){
				show_loader();
			},success   :       function(response){
				crdv_holder = {};

				console.log({response});
				hide_loader();

				if(response.result != null){
					crdv_holder['id_crv'] = val;
					crdv_holder['amount_received'] = parseFloat(response.result.amount);
					$('#txt_amount_received').val(number_format(response.result.amount));
					loss_gain_compute();
				}
			},error: function(xhr, status, error) {
				hide_loader()
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
	})

	function loss_gain_compute(){

		if(!$('#chk_cash_proceed').prop('checked')){
			return;
		}
		var disposed_amount = compute_disposed_amount();
		var crv_amount = crdv_holder['amount_received'] ?? 0;


		var loss_gain = crv_amount-disposed_amount;
		$('#txt_loss_gain').val(number_format(loss_gain));


		$('.ind_loss_gain').text('');
		$('.ind_loss_gain').removeClass('bg-success');
		$('.ind_loss_gain').removeClass('bg-danger');
		if(loss_gain > 0){
			$('.ind_loss_gain').addClass('bg-success');
			$('.ind_loss_gain').text('Gain');

		}else if(loss_gain < 0){
			$('.ind_loss_gain').addClass('bg-danger');
			$('.ind_loss_gain').text('Loss');			
		}

		console.log({disposed_amount,crv_amount});
	}

</script>


@if($allowed_post)
<script type="text/javascript">

	var prev_date = $('#txt_disposal_date').val();

	$(document).on('change','#txt_disposal_date',function(){
		var date = $(this).val();

		var formatted_prev =format_date(prev_date);
		var formatted_current = format_date(date);

		if(formatted_prev['month'] != formatted_current['month'] || formatted_prev['year'] != formatted_current['year']){
			refresh_depreciation_table();
		}

		prev_date = date;
		// console.log(format_date(date));
		// alert(123);
	})

	function refresh_depreciation_table(){
		var assets = [];
		$('tr.asset_row').each(function(){
			
			assets.push($(this).find('.sel-asset-in').val());
		})

		if(assets.length == 0){
			return;
		}

		var date = $('#txt_disposal_date').val();

		$.ajax({
			type         :       'GET',
			url          :       '/asset_disposal/refresh_asset_table',
			data         :       {'assets' : assets,'date' : date,'id_asset_disposal' : <?php echo $details->id_asset_disposal ?? 0 ?>},
			beforeSend   :       function(){
				show_loader();
			},
			success      :       function(response){
				hide_loader();
				console.log({response});

				if(response.count > 0){
					var asset_result = response.asset_depreciation;

					$.each(asset_result,function(key,data){
						console.log({key,data});
						var parent_row = $('tr.asset_row[row-asset="'+key+'"]');
						parent_row.find('.txt_rem_qty').val(data.remaining_quantity);
						parent_row.find('.txt_pur_cost').val(number_format(data.purchase_cost));
						parent_row.find('.txt_acc_dep').val(number_format(data.accumulated_depreciation));
						parent_row.find('.txt_cur_value').val(number_format(data.current_value));
						compute_disposed_amount();
						loss_gain_compute();
					})
				}

			},error: function(xhr, status, error) {
				hide_loader()
				var errorMessage = xhr.status + ': ' + xhr.statusText;
				Swal.fire({
					title: "Error-" + errorMessage,
					text: '',
					icon: 'warning',
					confirmButtonText: 'OK',
					confirmButtonColor: "#DD6B55"
				});
			}

		});

		console.log({assets});
	}





	function format_date(date){
		var formattedDate = new Date(date);
		var out = {};


		out['month'] = formattedDate.getMonth()+1;
		out['year'] =formattedDate.getFullYear();



		return out;
	}
	function remove_or(){
		$('#sel_or').val(0).trigger("change");
		$('.ind_loss_gain').text('');
		$('.ind_loss_gain').removeClass('bg-success');
		$('.ind_loss_gain').removeClass('bg-danger');
		$('#txt_loss_gain').val(number_format(0));
		$('#txt_amount_received').val(number_format(0));
		crdv_holder = {};
	}
	function add_asset_item(){
		console.log({asset_row});
		$('#asset_body').append(asset_row);
		$('tr.asset_row').last().find('.asset_item').val('');
		intialize_select2($('.sel-asset-in').last());
		set_counter_table();
	}
	function set_counter_table(){
		$('td.asset_item_counter').each(function(i){
			$(this).text(i+1);
		})
	}
	function remove_row(obj){
		var parent_row = $(obj).closest('tr.asset_row');
		parent_row.remove();
		set_counter_table();
		compute_disposed_amount();
		loss_gain_compute();
	}




	$(document).on('keyup','.txt_qty',function(){
		compute_disposed_amount();
		loss_gain_compute();
	})

	$(document).on('change','.sel-asset-in',function(){
		var val = $(this).val();
		var parent_row = $(this).closest('tr.asset_row');

		var sel = $(this);


		if(val == null){
			return;
		}
		if($('tr.asset_row[row-asset="'+val+'"]').length > 0){
			Swal.fire({
				title: "Asset Already Exists",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 1500
			});

			$(this).val('');
			$(this).trigger('change');

			return;
		}

		$.ajax({
			type       :       'GET',
			url        :       '/asset_disposal/parseDetails',
			data       :       {'asset_code' : val,
			'date' : $('#txt_disposal_date').val()},
			success    :       function(response){
				console.log({response});

				if(parseInt(response.details.remaining_quantity) == 0){
					Swal.fire({
						title: val+" has no remaining quantity",
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2000
					});

					$(sel).val('');
					$(sel).trigger('change');

					return;									
				}

				parent_row.attr('row-asset',val);
				parent_row.find('.txt_rem_qty').val(response.details.remaining_quantity);
				parent_row.find('.txt_pur_cost').val(number_format(response.details.purchase_cost));
				parent_row.find('.txt_acc_dep').val(number_format(response.details.accumulated_depreciation));
				parent_row.find('.txt_cur_value').val(number_format(response.details.current_value));
				parent_row.find('.txt_qty').val(1);
				compute_disposed_amount();
				loss_gain_compute();

			}
		})

		console.log({val})
	});

	$('#frm_submit').submit(function(e){
		e.preventDefault();

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

	})

	function post(){
		var assetData = [];

		$('tr.asset_row').each(function(){
			var temp = {};
			temp['asset_code'] = $(this).find('.sel-asset-in').val();
			temp['quantity_disposed'] = $(this).find('.txt_qty').val();

			assetData.push(temp);
		})


		if(!$('#chk_cash_proceed').prop('checked')){
			crdv_holder = {};
		}
		$.ajax({
			type          :       'GET',
			url           :       '/asset_disposal/post',
			data          :       {
				'assetData'  : assetData,
				'date' : $('#txt_disposal_date').val(),
				'id_crv' : crdv_holder['id_crv'],
				'id_asset_disposal' : <?php echo $details->id_asset_disposal ?? 0 ?>,
				'opcode' : <?php echo $opcode?>,
				'cash_proceed' : ($('#chk_cash_proceed').prop('checked'))?1:0
			},
			beforeSend    :       function(){
				show_loader();
			},
			success       :       function(response){
				console.log({response})
				hide_loader()
				if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});
				}else if(response.RESPONSE_CODE == "SUCCESS"){
					var link = "/asset_disposal/view/"+response.id_asset_disposal+"?href="+encodeURIComponent('<?php echo $back_link;?>');
					Swal.fire({
						title: "Asset Disposal Successfully Saved !",
						html : "<a href='"+link+"'>Asset Disposal ID# "+response.id_asset_disposal+"</a>",
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Add More Asset Disposal',
						denyButtonText: `Print JV`,
						showDenyButton: true,
						cancelButtonText: 'Close',
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/asset_disposal/create?href="+encodeURIComponent('<?php echo $back_link;?>');
						}else if (result.isDenied) {
							var redirect_data = {
								'show_print_jv' : 1,
								'id_journal_voucher' : response.id_journal_voucher
							}
							localStorage.setItem("redirect_print_jv",JSON.stringify(redirect_data));
							window.location = 	link;
						}else{
							window.location = '<?php echo $back_link;?>';
						}
					});
				}
			},error: function(xhr, status, error) {
				hide_loader()
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

		console.log({assetData});
	}
	function intialize_select2(select2){

		$(select2).select2({
			placeholder : "Search Asset",
			minimumInputLength: 4,
			width: '100%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: '/asset/search_asset',
				dataType: 'json',
				type: "GET",
				quietMillis: 1000,
				data: function (params) {
					var queryParameters = {
						search: params.term
					}
					return queryParameters;
				},
				processResults: function (data) {
					console.log({data});
					return {
						results: $.map(data.assets, function (item) {
							return {
								text: item.description,
								id: item.asset_code
							}
						})
					};
				}
			}
		});
	}

	function intialize_select_or(){		
		$('#sel_or').select2({
			placeholder : "Search OR Number",
			minimumInputLength: 4,
			width: '100%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: '/asset_disposal/search_crv',
				dataType: 'json',
				type: "GET",
				quietMillis: 1000,
				data: function (params) {
					var queryParameters = {
						search: params.term,
						id_asset_disposal : <?php echo $details->id_asset_disposal ?? 0 ?>
					}
					return queryParameters;
				},
				processResults: function (data) {
					console.log({data});
					return {
						results: $.map(data.crv, function (item) {
							return {
								text: item.description,
								id: item.id_crv
							}
						})
					};
				}
			}
		});
	}
</script>
@endif
@endpush