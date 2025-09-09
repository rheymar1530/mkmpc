
@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.head_crd .fa {
		transition: .3s transform ease-in-out;
	}
	.head_crd .collapsed .fa {
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
	.frm_loans,.frm-requirements{
		height: 27px !important;
		width: 100%;    
		font-size: 13px;
	}
	.text_center{
		text-align: center;
		font-weight: bold;
	}
	.select2-container--default .select2-selection--single {
		padding: unset;
		padding-top: 3px;
	}
	.select2-selection__rendered {
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
	}
	span.select2-container {
		transition: none;
		-moz-transition: none;
		-webkit-transition: none;
		-o-transition: none;
	}
	@media (min-width: 768px) {

		.col-md-cus{
			flex: 12.666667% !important;
			max-width: 12.666667% !important;
		}
		.col-md-cus2{
			flex: 5.666667% !important;
			max-width: 5.666667% !important;
		}	
		.col-md-cus3{
			flex: 8.666667% !important;
			max-width: 8.666667% !important;
		}
	}
	.control-label{
		font-size: 14px !important;
	}
	tr.asset_row[aria-deleted="true"]{
		display: none;
	}
</style>
<div class="container-fluid section_body">
	<form id="frm_submit_asset">
		<div class="row">

			<?php
			$months = [
				1=>"January",
				2=>"February",
				3=>"March",
				4=>"April",
				5=>"May",
				6=>"June",
				7=>"July",
				8=>"August",
				9=>"September",
				10=>"October",
				11=>"November",
				12=>"December"
			];
			?>
			<div class="col-md-12">
				<?php 
				$back = ($type ==1)?'/asset/asset_purchase':'/asset/asset_adjustment';
				$back_link = (request()->get('href') == '')?$back:request()->get('href'); 
				?>
				<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to {{$module_title}} List</a>
				<div class="card">
					<div class="card-body">
						<h4 class="head_lbl text-center mb-4">{{$module_title}}
							@if($opcode == 1)	
							<small>(Asset ID# {{$asset->id_asset}})</small>
							@if($asset->status == 10)
							<span class="badge badge-danger">Cancelled</span>
							@endif
							@endif
						</h4>
						@if($type == 1)
						<div class="form-row">
							<div class="form-group col-md-2" style="">
								<label>Cash Disbursement ID</label>
								<input type="text" class="form-control asset-input" id="txt_cdv_list" placeholder="Click to select CDV" readonly onclick="parse_cdv_list()" value="{{$asset->id_cash_disbursement ?? ''}}">
							</div>
							<div class="form-group col-md-3" style="">
								<label>Branch</label>
								<input type="text" class="form-control asset-input" id="txt_branch_name" value="{{$asset->branch_name ?? ''}}" disabled>
							</div>		
							<div class="form-group col-md-2" style="">
								<label>Purchase Date</label>
								<input type="date" class="form-control asset-input" id="txt_purchase_date" value="{{$asset->purchase_date ?? ''}}" disabled>
							</div>		
							<div class="form-group col-md-2" style="">
								<label>Valuation Date</label>
								<select class="form-control asset-input p-0" id="sel_val_month">
									@foreach($months as $val=>$desc)
									<option value="{{$val}}" <?php echo ($val_month == $val)?"selected":""; ?> >{{$desc}}</option>
									@endforeach								
								</select>
							</div>	
							<div class="form-group col-md-2" style="">
								<label>&nbsp;</label>
								
								<select class="form-control asset-input p-0" id="sel_val_year">
									@for($i=2000;$i<=2050;$i++)
									<option value="{{$i}}" <?php echo ($val_year == $i)?"selected":""; ?>>{{$i}}</option>
									@endfor								
								</select>
							</div>						
						</div>
						
						
						@elseif($type == 2)

						<div class="form-row d-flex align-items-end" style="margin-top:15px">
							@if($opcode == 0)
							<div class="form-group col-md-2" style="">
								<label>&nbsp;</label>
								<button type="button" class="btn btn-sm bg-gradient-primary2 " style="height:27px !important;line-height: 10px;" onclick="window.location='/asset/asset_purchase/add?href={{$back_link}}'">Cash Disbursement</button>
							</div>
							@endif
							<div class="form-group col-md-3" style="">
								<label>Branch</label>
								<select class="form-control asset-input p-0" id="sel_branch">
									@foreach($branches as $br)
									<?php
									$sel_id_branch = $asset->id_branch ?? 1;
									?>
									<option value="{{$br->id_branch}}" <?php echo ($sel_id_branch == $br->id_branch)?'selected':''; ?> >{{$br->branch_name}}</option>
									@endforeach
								</select>
							</div>		
							<div class="form-group col-md-3" style="">
								<label>Purchase Date</label>
								<input type="date" class="form-control asset-input" id="txt_purchase_date" value="{{$asset->purchase_date ?? $current_date}}">
							</div>		
							<div class="form-group col-md-2" style="">
								<label>Valuation Date</label>
								<select class="form-control asset-input p-0" id="sel_val_month">
									@foreach($months as $val=>$desc)
									<option value="{{$val}}" <?php echo ($val_month == $val)?"selected":""; ?> >{{$desc}}</option>
									@endforeach								
								</select>
							</div>	
							<div class="form-group col-md-2" style="">
								<label>&nbsp;</label>
								
								<select class="form-control asset-input p-0" id="sel_val_year">
									@for($i=2000;$i<=2050;$i++)
									<option value="{{$i}}" <?php echo ($val_year == $i)?"selected":""; ?>>{{$i}}</option>
									@endfor								
								</select>
							</div>							
						</div>
						@endif

						@if($opcode == 1 && $asset->status == 10)
						<b>Cancellation Reason:</b> {{$asset->cancellation_reason}}
						@endif
					</div>
				</div>		
			</div>
			<div class="col-md-12" id="main_asset_div">
				<?php
				$card_count = ($opcode ==0)?1:count($asset_items);
				?>
				@for($j=0;$j<$card_count;$j++)
				<div class="card asset_card c-border" data-asset-id="{{$id_charts[$j] ?? 0}}">
				<!-- <div class="card-header custom_card_header bg-gradient-primary2">
					<h5>
						<span class="float-right">	
							<a href="#asset_{{$j+1}}" class="btn btn-success btn-circle btn-toggle" data-toggle="collapse" data-target="#asset_{{$j+1}}" aria-expanded="true" aria-controls="#asset_{{$j+1}}" style="margin-right:<?php echo ($type==1)?10:0 ?>px "><i class="fa fa-chevron-down"></i></a>
							@if($type == 2)
							<button type="button" class="btn btn-danger btn-circle" style="margin-right: 10px;" onclick="remove_asset(this)"><i class="fas fa-times"></i></button>
							@endif
						</span>
					</h5>
				</div> -->
				<div class="card-body">
					<div class="form-group row p-0 mb-0" style="margin-top:5px">
						<div class="col-md-12">
							<h5 class="head_crd">
								<span class="float-right">	
									<a href="#asset_{{$j+1}}" class="btn btn-success btn-circle btn-toggle" data-toggle="collapse" data-target="#asset_{{$j+1}}" aria-expanded="true" aria-controls="#asset_{{$j+1}}" style="margin-right:<?php echo ($type==1)?10:0 ?>px "><i class="fa fa-chevron-down"></i></a>
									@if($type == 2)
									<button type="button" class="btn btn-danger btn-circle" style="margin-right: 10px;" onclick="remove_asset(this)"><i class="fas fa-times"></i></button>
									@endif
								</span>
							</h5>
						</div>
					</div>
					<div class="form-group row p-0" style="margin-top:5px">
						
						<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Asset Account&nbsp;</label>
						<div class="col-md-5">
							<?php $disabled_chart = ($type == 1)?"disabled":""; ?>
							<select class="form-control asset-input p-0 sel_asset_account" <?php echo $disabled_chart;?> >

								@if($type == 1 && $opcode == 1)
								<option value="{{$id_charts[$j]}}">{{$asset_items[$id_charts[$j]]['account_description']}}</option>
								@endif

								@if($type == 2)
								<?php
								$selected_chart = $id_charts[$j] ?? 0;
								?>
								@foreach($charts as $c)
								<option value="{{$c->id_chart_account}}" <?php echo ($c->id_chart_account == $selected_chart)?"selected":"";?>  >{{$c->account_code}} - {{$c->description}}</option>
								@endforeach
								@endif
							</select>
						</div>
					</div>
					<div class="form-group row p-0" style="margin-bottom: -10px !important;">
						<?php
						$total_amt = ($opcode ==0)?'0.00':number_format($asset_items[$id_charts[$j]]['total_amount'],2);
						?>
						<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Total Amount&nbsp;</label>
						<div class="col-md-3">
							<input type="text" class="form-control asset-input class_amount txt_total_amount" disabled value="{{$total_amt}}">
						</div>
						@if($type == 1)
						<div class="col-md-1 control-label col-form-label col-md-cus">
							<label class="lbl_total_per_asset">(0.00)</label>
						</div>
						@endif
					</div>
					<div class="form-row show asset_main_form" id="asset_{{$j+1}}">
						<div class="col-md-12 p-0">
							<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
								<table class="table table-bordered table-stripped table-head-fixed tbl-inputs" style="white-space: nowrap;">
									<thead>
										<tr>
											<th class="" width="2%"></th>
											@if($opcode == 1)
											<th>Asset Code</th>
											@endif
											<th class="" width="30%">Description</th>
											<th class="" width="20%">Serial No.</th>
											<th class="" width="5%">Qty</th>
											<th class="">Total Cost</th>
											<th class="" width="8%">Life (yrs)</th>
											<th class="">Salvage Value</th>
											<th class="" width="2%"></th>
										</tr>
									</thead>
									<tbody class="asset_body">
										<?php

										$item_count = ($opcode == 0)?1:count($asset_items[$id_charts[$j]]['items']);	
										if($opcode == 1){
											$this_items = $asset_items[$id_charts[$j]]['items'];								
										}
										?>
										@for($i=0;$i<$item_count;$i++)
										<tr class="asset_row" aria-deleted="false" <?php echo (isset($this_items[$i]->asset_code))?"asset-code='".$this_items[$i]->asset_code."'":"" ?> >
											<td class="text_center asset_item_counter">{{$i+1}}</td>
											@if($opcode == 1)
											<td><input type="text" name="" required class="form-control asset-input  txt_asset_code" value="{{$this_items[$i]->asset_code ?? ''	}}" style="font-size:13px;font-weight:bold" disabled></td>
											@endif
											<td><input type="text" name="" required class="form-control asset-input asset_item txt_description" key="description" value="{{$this_items[$i]->description ?? ''	}}" required></td>
											<td><input type="text" name="" required class="form-control asset-input asset_item txt_serial_no" key="serial_no" value="{{$this_items[$i]->serial_no ?? ''	}}" required></td>
											<td><input type="text" name="" required class="form-control asset-input asset_item txt_qty" key="quantity" value="{{$this_items[$i]->quantity ?? 0	}}"></td>
											<td><input type="text" name="" required class="form-control asset-input asset_item txt_total_cost class_amount"  key="total_cost" value="{{isset($this_items[$i]->total_cost)?number_format($this_items[$i]->total_cost,2):'0.00'}}"></td>

											<td><input type="text" name="" required class="form-control asset-input asset_item txt_life" key="life" value="{{$this_items[$i]->life ?? 0	}}"></td>
											<td><input type="text" name="" required class="form-control asset-input asset_item class_amount txt_salvage_value" value="{{isset($this_items[$i]->salvage_value)?number_format($this_items[$i]->salvage_value,2):'0.00'}}" key="salvage_value"></td>
											<td><a onclick="remove_row(this)" style="margin-left:5px;margin-top: 10px !important;"><i class="fa fa-times"></i></a></td>
										</tr>
										@endfor

									</tbody>
								</table>
							</div>
						</div>
						<button type="button" class="btn btn-xs bg-gradient-primary2 col-md-12" onclick="add_asset_item(this)"><i class="fa fa-plus"></i> Add Asset Item</button>
					</div>
				</div>
			</div>
			@endfor

		</div>

		@if($type == 2)
		<div class="col-md-12">
			<button type="button" class="btn btn-sm bg-gradient-success2 col-md-12" onclick="add_asset()" style="margin-bottom:10px"><i class="fas fa-plus"></i>&nbsp;Add Asset</button>
		</div>
		@endif	

		@if($allow_post)
		<div class="col-md-12">
			<button class="btn  bg-gradient-success2 float-right">Save Asset</button>
			@if($opcode == 1)
			<button class="btn  bg-gradient-warning2 float-right" type="button" onclick="show_status_modal()" style="margin-right:10px">Cancel {{$module_title}}</button>
			@endif
			
		</div>
		@endif

		
	</div>
</form>
</div>
@if($type == 1 && $opcode == 0)
@include('asset.cdv_list_modal')
@endif


@if($opcode == 1 && $allow_post)
@include('asset.status_modal')
@endif
@endsection
@push('scripts')

<script type="text/javascript">
	const asset_item_row = '<tr class="asset_row" >'+$('.asset_row').eq(0).html()+'</tr>';

	const no_asset = '<tr class="no_asset_row"><td colspan="'+{{($opcode==1)?9:8}}+'" style="text-align:center">No Item</td></tr>';
	let asset_maintenance = jQuery.parseJSON('<?php echo json_encode($asset_maintenance ?? []) ?>');
	let id_cdv_holder = <?php echo $asset->id_cash_disbursement ?? 0;?>;
	const type = {{$type}}
	const asset_id = '<?php echo $asset->id_asset ?? 0?>';
	// alert(asset_id);

	const asset_card = '<div class="card asset_card c-border">'+$('.asset_card').eq(0).html()+'</div>';
	<?php
	if($opcode == 0){
		echo "$('.asset_card').eq(0).remove();";	 	
	}else{
		if($type == 2){
			echo "$('.sel_asset_account').select2({dropdownAutoWidth :true,dropdownPosition: 'below'});";
		}
	}
	?>
	$(document).ready(function(){
		compute_card();
	})
	function compute_card(){
		$('.asset_card').each(function(){
			var parent_card = $(this);
			var total = compute_asset_total_amount(parent_card);
			set_parent_card_value(total,parent_card)			
		})

	}
	// $('.asset_card').eq(0).remove();
	function makeid(length){
		var result           = '';
		var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		var charactersLength = characters.length;
		for(var i = 0; i < length; i++ ) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}
	function add_asset_item(obj){
		var parent_card = $(obj).closest(".asset_card");
		append_asset_item(parent_card);
	}
	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});	
	function append_asset_item(parent_card){
		parent_card.find('tbody.asset_body').append(asset_item_row);
		set_item_counter(parent_card);			
		var asset_main = asset_maintenance[$(parent_card).attr('data-asset-id') ?? 0];
		var life_span = (asset_main==undefined)?0:asset_main['life_span'];

		var last_asset_row = parent_card.find('tr.asset_row').last();
		$(last_asset_row).find('.txt_description').focus();
		$(last_asset_row).find('.asset_item').val('');
		$(last_asset_row).find('.class_amount').val('0.00');
		$(last_asset_row).find('.txt_asset_code').val('')
		parent_card.find('.txt_life').last().val(life_span)
	}
	function set_item_counter(parent_card){
		var counter = 0;
		parent_card.find('.asset_item_counter').each(function(i){
			var parent_row = $(this).closest('.asset_row');
			var attr = $(parent_row).attr('aria-deleted');
			console.log({attr})

			if(attr == undefined || attr == "false"){
				console.log({counter})
				$(this).text(counter+1);
				counter++;				
			}

		})

		if(counter == 0){
			parent_card.find('tbody.asset_body').html(no_asset)
		}else{
			parent_card.find('tr.no_asset_row').remove();
		}
	}
	function remove_row(obj){
		var parent_card = $(obj).closest(".asset_card");

		var parent_row = $(obj).closest('tr.asset_row');

		if($(parent_row).attr('aria-deleted') == "false"){
			$(parent_row).attr('aria-deleted',"true");
		}else{
			parent_row.remove();
		}
		
		set_item_counter(parent_card);
		var total = compute_asset_total_amount(parent_card);
		set_parent_card_value(total,parent_card)
	}
	function append_asset_account(){
		$('#main_asset_div').append(asset_card);
		var parent_card = $('.asset_card').last();
		parent_card.find('tbody.asset_body').html('');
		append_asset_item(parent_card);

		var card_id = makeid(5);

		parent_card.find('.asset_main_form').attr('id',card_id);
		parent_card.find('.btn-toggle').attr('href','#'+card_id).attr('data-target','#'+card_id).attr('aria-controls','#'+card_id);

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
	$(document).on('keyup','.txt_total_cost',function(){
			//SET SALVAGE VALUE
		var val = parseFloat($(this).val());
		var parent_row = $(this).closest('.asset_row');
		var parent_card = $(this).closest('.asset_card');
		var asset_id = $(this).closest('.asset_card').attr('data-asset-id') ?? 0;

		var salvage_percentage = (asset_maintenance[asset_id] == undefined)?0:parseFloat(asset_maintenance[asset_id]['salvage_percentage']/100);
		console.log({salvage_percentage});
		parent_row.find('.txt_salvage_value').val(number_format(salvage_percentage*val))

		var total = compute_asset_total_amount(parent_card);
		set_parent_card_value(total,parent_card)
	})
	function compute_asset_total_amount(parent_card){
		var row = parent_card.find('input.txt_total_cost');
		var total = 0;
		$(row).each(function(){
			var parent_row = $(this).closest('.asset_row');
			var attr = $(parent_row).attr('aria-deleted');
			console.log({attr})
			if(attr == undefined || attr == "false"){
				total += decode_number_format($(this).val());			
			}
			
		})
		console.log({total});
		return total;
	}
</script>
@if($type == 1)
<script type="text/javascript">
	// parseCDV();
	function set_parent_card_value(total,parent_card){
		var asset_id = parent_card.attr('data-asset-id');
		var amount = asset_maintenance[asset_id]['amount'];

		if(total != amount){
			parent_card.find('.lbl_total_per_asset').addClass('text-danger');
		}else{
			parent_card.find('.lbl_total_per_asset').removeClass('text-danger');
		}
		console.log({amount,total});
		parent_card.find('.lbl_total_per_asset').text("("+number_format(total)+")")
	}
	function parseCDV(id_cdv){
		$.ajax({
			type        :         'GET',
			url         :         '/asset/get_cdv',
			data        :         {'id_cdv' : id_cdv},
			beforeSend  :       function(){
				show_loader();
			},
			success     :         function(response){
				console.log({response});
				hide_loader();
				$('#main_asset_div').html('');
				$.each(response.entry,function(i,item){
					var temp = {};
					append_asset_account();
					$('.asset_card').last().attr('data-asset-id',item.id_chart_account);

				  	//initialize account selected
					$('.sel_asset_account').last().html('<option value="'+item.id_chart_account+'">'+item.ca_account+'</option>')
					$('.sel_asset_account').last().select2();
				  	//initialize amount
					$('.txt_total_amount').last().val(number_format(parseFloat(item.amount)));

				  	//initialize account code maintenance
					temp['id_chart_account'] =item.id_chart_account;
					temp['life_span'] = item.life_span;
					temp['salvage_percentage'] = parseFloat(item.salvage_percentage);
					temp['amount'] = parseFloat(item.amount);
					asset_maintenance[item.id_chart_account] = temp;


					id_cdv_holder = id_cdv;



					$('.asset_card').last().find('.txt_life').val(parseFloat(item.life_span))
					animate_element($('.asset_card').last(),1)
				})
				$('#txt_cdv_list').val(id_cdv);
				$('#txt_branch_name').val(response.cdv_details.branch_name);
				$('#txt_purchase_date').val(response.cdv_details.date);

				$('#modal_cdv_list').modal('hide')
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
	}

</script>


@elseif($type == 2)
<script type="text/javascript">
	function add_asset(){
		append_asset_account()
		$('.sel_asset_account').last().val(0)
		$('.sel_asset_account').last().select2({dropdownAutoWidth :true,dropdownPosition: 'below'});
		var parent_card = $('.sel_asset_account').last().closest('.asset_card');
		$(parent_card).find('tbody.asset_body').html('');
		append_asset_item(parent_card);

		animate_element($('.asset_card').last(),1)
	}
	function remove_asset(obj){
		var parent_card = $(obj).closest('.asset_card');
		// parent_card.remove();
		animate_element(parent_card,2)
	}
	function set_parent_card_value(total,parent_card){
		parent_card.find('.txt_total_amount').val(number_format(total));
	}
	$(document).on('change','.sel_asset_account',function(){
		var val = $(this).val();
		var parent_card = $(this).closest('.asset_card');
		$.ajax({
			type        :       'GET',
			url         :       '/asset/parse/account_details',
			data        :       {'id_chart_account' : val},
			success     :       function(response){
				console.log({response});
				var temp = {};
				var item = response.entry;
				parent_card.attr('data-asset-id',item.id_chart_account);
				temp['id_chart_account'] =item.id_chart_account;
				temp['life_span'] = item.life_span;
				temp['salvage_percentage'] = parseFloat(item.salvage_percentage);
				asset_maintenance[item.id_chart_account] = temp;

				  	//recompute salvage value of row and set life span with 0 value
				var asset_row = parent_card.find('.asset_row');
				$(asset_row).each(function(){
				  		//life span

					if($(this).find('.txt_life').val() == 0){
						$(this).find('.txt_life').val(temp['life_span']);
					}
					

				  		//salvage value
					var total_cost_obj =$(this).find('.txt_total_cost');
					var value = decode_number_format($(total_cost_obj).val())*(temp['salvage_percentage']/100);
					$(this).find('.txt_salvage_value').val(number_format(value));	
				})
			}
		})
	})
</script>
@endif



@if($allow_post)
<!-- JAVASCRIPT FOR DATA POSTING -->
<script type="text/javascript">
	$('#frm_submit_asset').submit(function(e){
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
		var populated_parent = {};
		var asset_to_remove = [];

		$('.card-error').removeClass('card-error');
		$('.mandatory').removeClass('mandatory');
		
		populated_parent = {	
			'type' :  type,
			'id_branch' : $('#sel_branch').val() ?? 0,
			'purchase_date' : $('#txt_purchase_date').val(),
			'val_month' : $('#sel_val_month').val(),
			'val_year' : $('#sel_val_year').val(),
			'id_cash_disbursement' : id_cdv_holder,
		};
		console.log({populated_parent});

		var populated_items = {};

		$('.asset_card').each(function(){
			var account_array = [];
			var id_account = $(this).attr('data-asset-id');
			$(this).find('.asset_row').each(function(){
				var attr = $(this).attr('aria-deleted');
				var asset_code = $(this).attr('asset-code');
				if(attr == undefined || attr == "false"){
					var temp_obj = {};
					temp_obj['asset_code'] = asset_code ?? null;
					$(this).find('.asset_item').each(function(){
						var val = $(this).val();
						var key = $(this).attr('key');
						val = $(this).hasClass('class_amount')? decode_number_format(val):val;
						temp_obj[key] = val;
					})
					account_array.push(temp_obj);		
				}else{
					asset_to_remove.push($(this).attr('asset-code'))
				}
			})
			var content = (account_array.length > 0)?account_array:"NO_ITEMS";
			populated_items["acc_"+id_account.toString()] = content;
		})

		console.log({populated_items,populated_parent,asset_to_remove});
		// return;
		$.ajax({
			type        :    'GET',
			url         :    '/post/asset',
			data        :    {
				'parent'  : populated_parent,
				'items'  : populated_items,
				'id_asset' : asset_id,
				'asset_to_remove' : asset_to_remove,
				'opcode' : '<?php echo $opcode; ?>'
			},
			beforeSend  :    function(){

				show_loader();
			},
			success     :    function(response){
				hide_loader();
				console.log({response});
				if(response.RESPONSE_CODE == "SUCCESS"){
					var link = "/asset/view/"+response.id_asset+"?href="+encodeURIComponent('<?php echo $back_link;?>');
					Swal.fire({
						title: "Asset Successfully Saved !",
						html : "<a href='"+link+"'>Asset ID# "+response.id_asset+"</a>",
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Add More Asset',
						showDenyButton: false,
						cancelButtonText: 'Close',
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/asset/"+"{{$index_route}}/add?href="+encodeURIComponent('<?php echo $back_link;?>');
						}else{
							window.location = '<?php echo $back_link;?>';
						}
					});
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});

					var asset_id_no_item = response.asset_id ?? [];
					for(var $i=0;$i<asset_id_no_item.length;$i++){
						$('.asset_card[data-asset-id='+asset_id_no_item[$i]+']').addClass('card-error');
					}
					var invalid_inputs = response.invalid_inputs ?? [];

					$.each(invalid_inputs,function(i,item){
						var card = $('.asset_card[data-asset-id='+i+']');
						card.addClass('card-error');
						$.each(item,function(index,keys){
							var row = $(card).find('tr.asset_row').eq(index);
							$.each(keys,function(c,key){
								row.find('.asset_item[key="'+key+'"]').addClass('mandatory')
							})
						})
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


	}
</script>
@endif
@endpush

