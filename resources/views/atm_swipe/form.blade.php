@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.separator{
		margin-top: -0.5em;
	}
	.badge_term_period_label{
		font-size: 20px;
	}

	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif !important;

	}
	.tbl_jv tr>th {
		padding: 5px;
		vertical-align: top;
		font-size: 14px;
	}

	.tbl_jv tr.jv_row>td{
		padding: 0px !important;

	}
	.tbl_jv tr.jv_row_display>td{
		padding: 3px !important;
		font-weight: bold;
		font-size: 15px;

	}
	.tbl_jv input:not([type='checkbox']),.tbl_jv select {
		height: 25px !important;
		width: 100%;

	}	
	.table_header_dblue{
		text-align: center;
	}
	.btn_delete_jv{
		width: 100%;
		height: 23px !important;
		padding: 0px 2px 0px 2px !important;
	}
	.tbl_jv .select2-selection {
		height: 25px !important;
	}

	.select2-container--default .select2-selection--single {
		padding: unset;
	}
	.td_counter{
		font-weight: bold;
		text-align: center;
	}
	.select2-selection__rendered {
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 13px;
	}
	.class_amount{
		text-align: right;

	}
	.col_jv_entry{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 13px;
	}



	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.form-label{
		margin-bottom: 4px !important;
	}


	.text_center{
		text-align: center;
	}
	.text_bold{
		font-weight: bold;
	}

	.wrapper2{
		width: 1300px !important;
		margin: 0 auto;
	}
	.text-red{
		color: red;
	}
	.swal2-deny{
		padding: 0.375rem 0.75rem !important;
	}
	.select2-results__options {
		max-height: 250px;
		overflow:scroll;
	}
/*	.select-dropdown {
		position: static !important;
	}
	.select-dropdown .select-dropdown--above {
		margin-top: 336px !important;
	}*/
	.select2-chosen {
		background: #FFF;
		border-radius: 7px;
		margin-right: 0 !important;
	}
	.select2-drop.select2-drop-above {
		z-index: -1;
	}
	span {
		transition: all 0.25s;
		-moz-transition: all 0.25s;
		/* Firefox 4 */
		-webkit-transition: all 0.25s;
		-o-transition: all 0.25s;
		/* Opera */
	}
/* remove transition for select2 span to stop scroll-to-top behaviour on click */
span.select2-container {
	transition: none;
	-moz-transition: none;
	-webkit-transition: none;
	-o-transition: none;
}
.footer_fix {
	padding: 3px !important;
	background-color: #fff;
	border-bottom: 0;
	box-shadow: inset 0 1px 0 #dee2e6, inset 0 -1px 0 #dee2e6;
	position: -webkit-sticky;
	position: sticky;
	bottom: 0;
	z-index: 10;
}
.col-form-label{
	font-size: 14px !important;
}
.font-total{
	font-size: 17px !important;
}
.select2-dropdown--below {
	width: 170%;
}
	@media (min-width: 1200px) {
		.container{
			max-width: 780px;
		}
	}	
</style>
<?php
$client_type = [
	2=>"Members",
	3=>"Employee",
	4=>"Others"
];
$jv_type = [
	1=>'Normal',
	2=>'Reversal/Adjustment'
];
?>

	<div class="container main_form section_body" style="margin-top: -20px;" >
		<?php $back_link = (request()->get('href') == '')?'/atm_swipe':request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to ATM Swipe List</a>
		@if($opcode == 1)
		<a class="btn bg-gradient-primary2 btn-sm" href="/atm_swipe/create?href={{urlencode($back_link)}}" style="margin-bottom:10px"><i class="fas fa-plus"></i>&nbsp;&nbsp;Create New ATM Swipe</a>
		@endif
		<div class="card" id="repayment_main_div">
			<form id="frm_post_atm_swipe">
				<div class="card-body col-md-12">
					<h3 class="head_lbl text-center">ATM Swipe
						@if($opcode == 1) <small>(ID# {{$atm_swipe_details->id_atm_swipe}})</small>  &nbsp;             
						@if($atm_swipe_details->status == 10)
						<span class="badge badge-danger bgd_cancel">Cancelled</span>
						@endif
						@endif
					</h3>
					<div class="row mt-3">
						<div class="col-md-12 row">	
							<div class="col-md-12" style="margin-top:15px">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group row p-0" >
											<label for="txt_transaction_date" class="col-sm-3 control-label col-form-label" style="text-align: left">Date&nbsp;</label>
											<div class="col-sm-4">
												<input type="date" name="" class="form-control swiping_parent" value="{{$atm_swipe_details->date ?? $current_date}}" id="txt_transaction_date" key="date">
											</div>
										</div>
										<div class="form-group row p-0" >
											<label for="sel_client_type" class="col-sm-3 control-label col-form-label" style="text-align: left">Client Type&nbsp;</label>
											<div class="col-sm-4">
												<?php
												$selected_client_type = $atm_swipe_details->client_type ?? 2;
												?>
												<select class="form-control form_input p-0 swiping_parent" id="sel_client_type" key="client_type" required>
													@foreach($client_type as $val=>$desc)
													<option value="{{$val}}" <?php echo ($selected_client_type==$val)?"selected":"";?>>{{$desc}}</option>
													@endforeach
												</select>
											</div> 
										</div>
										<div id="id_client_holder">

										</div>
										<div class="form-group row p-0" id="div_sel_reference">
											<label for="sel_client_type" class="col-sm-3 control-label col-form-label" style="text-align: left">&nbsp;</label>
											<div class="col-sm-8">
												<select class="form-control form_input p-0 swiping_parent" id="sel_reference" key="client_reference" required>
													@if(isset($selected_reference_payee))
													<option value="{{$selected_reference_payee->id}}">{{$selected_reference_payee->name}}</option>

													@endif
												</select>
											</div> 
										</div>							
										<div class="form-group row p-0" id="div_client_others" >
											<label for="txt_client_others" class="col-sm-3 control-label col-form-label" style="text-align: left">&nbsp;</label>
											<div class="col-sm-8">
												<input type="text" name="" class="form-control swiping_parent" value="{{$atm_swipe_details->client ?? ''}}" id="txt_client_others" key="client" required>
											</div>
										</div>	
										<div class="form-group row p-0">
											<label for="txt_address" class="col-sm-3 control-label col-form-label" style="text-align: left">Bank&nbsp;</label>
											<div class="col-sm-4">
												<select class="form-control p-0 swiping_parent" id="sel_bank" key="id_bank">
													<?php
													$selected_bank = $atm_swipe_details->id_bank ?? 2;
													?>
													@foreach($banks as $bank)
													<option value="{{$bank->id_bank}}" <?php echo ($selected_bank==$bank->id_bank)?"selected":""; ?> >{{$bank->bank_name}}</option>
													@endforeach
													
												</select>	
											</div>
										</div>
										<div class="form-group row p-0" >
											<label for="txt_swiping_amount" class="col-sm-3 control-label col-form-label" style="text-align: left">Amount&nbsp;</label>
											<div class="col-sm-5">
												<?php
												if(isset($atm_swipe_details)){
													$amount = number_format($atm_swipe_details->amount,2);
													$swiping = number_format($atm_swipe_details->transaction_charge,2);
													$change_payable = $atm_swipe_details->amount-$atm_swipe_details->transaction_charge;
												}else{
													$amount = '0.00';
													$sw = WebHelper::SwipingAmount();
													$swiping = number_format($sw,2);
													$change_payable = 0-$sw;
												}

												?>
												<input type="text" name="" class="form-control swiping_parent class_amount in_amt" value="{{$amount}}" id="txt_swiping_amount" key="amount">
											</div>
										</div>
										<div class="form-group row p-0">
											<label for="txt_transaction_charge" class="col-sm-3 control-label col-form-label" style="text-align: left">Transaction Charge&nbsp;</label>
											<div class="col-sm-5">
												<input type="text" name="" class="form-control swiping_parent class_amount in_amt" value="{{$swiping}}" id="txt_transaction_charge" key="transaction_charge">
											</div>
										</div>
										<div class="form-group row p-0">
											<label for="txt_transaction_charge" class="col-sm-3 control-label col-form-label" style="text-align: left">Change Payable&nbsp;</label>
											<div class="col-sm-5">
												<input type="text" disabled name="" class="form-control class_amount" id="txt_change_payable" value="{{number_format($change_payable,2)}}">
											</div>
										</div>
										<div class="form-group row p-0" >
											<label for="txt_remarks" class="col-sm-3 control-label col-form-label" style="text-align: left">Remarks&nbsp;</label>
											<div class="col-sm-9">
												<textarea class="form-control swiping_parent" rows="3" style="resize:none;" id="txt_remarks" key="remarks">{{$atm_swipe_details->remarks ?? ''}}</textarea>
											</div>
										</div>
										@if($opcode == 1)
										<div style="padding-top: 10px !important;">
											
											<span class="text-muted spn_details"><b>JV No. :</b> {{$atm_swipe_details->id_journal_voucher}}</span><br>
											<span class="text-muted spn_details"><b>CDV No. :</b> {{$atm_swipe_details->id_cash_disbursement}}</span><br>
											<span class="text-muted spn_details"><b>Date Created:</b> {{$atm_swipe_details->date_created}}</span><br>

											@if($atm_swipe_details->status == 10)
											<span class="text-muted spn_details"><b>Date Cancelled:</b> {{$atm_swipe_details->date_cancelled}}</span><br>
											<span class="text-muted spn_details"><b>Cancellation Reason:</b> {{$atm_swipe_details->cancellation_reason}}</span>
											@endif
										</div>
										@endif
									</div>
								</div>
							</div>
						</div>	
					</div>
				</div>
				<div class="card-footer">
					@if(($opcode == 0 && $credential->is_create) || ($opcode == 1 && $atm_swipe_details->status != 10 && $credential->is_edit))
					<button class="btn bg-gradient-success2 float-right" id="post_jv">Save</button>
					@endif
					@if($opcode == 1)
					<button type="button" class="btn bg-gradient-primary2 float-right" style="margin-right:10px" onclick="print_page('/atm_swipe/print_form/{{$atm_swipe_details->id_atm_swipe}}')"><i class="fas fa-print" ></i>&nbsp;Print Form</button>
					<button type="button" class="btn bg-gradient-danger2 float-right" style="margin-right:10px" onclick="print_page('/atm_swipe/entry/{{$atm_swipe_details->id_atm_swipe}}')"><i class="fas fa-print" ></i>&nbsp;Print Entry</button>
					@endif

					@if($opcode == 1 && $atm_swipe_details->status != 10 && $credential->is_cancel)
					<button type="button" class="btn bg-gradient-warning2 float-right" style="margin-right:10px;color:white" onclick="show_cancel_modal('{{$atm_swipe_details->id_atm_swipe}}')"><i class="fas fa-times" ></i>&nbsp;Cancel ATM Swipe</button>
					@endif
				</div>	

			</form>

		</div>

	</div>


@if($opcode == 1)
@include('global.print_modal')
@include('atm_swipe.cancel_modal')
@endif

@endsection




@push('scripts')
<script type="text/javascript">
	var $div_client_others = $('#div_client_others').detach();
	var $div_sel_reference = $('#div_sel_reference').detach();

	$(document).on('change','#sel_client_type',function(){
		initialize_client_type(true,null);
	})

	$(document).ready(function(){
		initialize_client_type(false,null);
	})

	function initialize_client_type(reset_reference,val){
		var client_type = $('#sel_client_type').val();
		$('#id_client_holder').html('')
		if(client_type <= 3){


			$('#id_client_holder').html($div_sel_reference);
			if(reset_reference){
				$('#sel_reference').val(0).trigger("change");


			}
			if(val != null){
				$('#sel_reference').html(val);
			}
			intialize_select2(client_type)
		}else{

			$('#id_client_holder').html($div_client_others);
		}
		animate_element($('#id_client_holder'),1)
	}


	function intialize_select2(type){		
		var $link = '';
		if(type == 1){
			$link = '/search_supplier';
			$t = "Supplier";
		}else if(type == 2){
			$link = '/search_member'
			$t = "Member";
		}else if(type == 3){
			$link = '/search_employee';
			$t="Employee";
		}

		$("#sel_reference").select2({
			minimumInputLength: 2,
			placeholder: `Select ${$t}`,
			width: '100%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: $link,
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

	$(document).on('select2:open', (e) => {
		const selectId = e.target.id
		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function(key,value,){
			value.focus()
		})
	}) 
	$(document).on("focus",".class_amount",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';

			$(this).val('');

			return; 
		}
		$(this).val(decode_number_format(val)); 
	})
	$(document).on("blur",".class_amount",function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			val = 0;
			$(this).val('');

			return;
		}
		$(this).val(number_format(parseFloat(val)));
	})
</script>


@if($allow_post)
<script type="text/javascript">
	$('#frm_post_atm_swipe').submit(function(e){
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
		var data = {};

		$('.swiping_parent').each(function(){
			var key = $(this).attr('key');
			if($(this).hasClass('class_amount')){
				data[key] = decode_number_format($(this).val());
			}else{
				data[key] = $(this).val();
			}
		})

		console.log({data});

		$.ajax({
			type    :      'POST',
			url     :      '/atm_swipe/post',
			data    :      {
				'swiping' : data,
				'opcode' : <?php echo $opcode;?>,
				'id_atm_swipe' : <?php echo $atm_swipe_details->id_atm_swipe ?? 0 ?>	
			},
			beforeSend :   function(){
				show_loader();
				$('.mandatory').removeClass('mandatory');
			},
			success :      function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					var html_swal = '';
					var link = "/atm_swipe/view/"+response.id_atm_swipe+"?href="+encodeURIComponent('<?php echo $back_link;?>');


					html_swal = "<a href='"+link+"'>ATM Swipe ID# "+response.id_atm_swipe+"</a>";

					Swal.fire({

						title: "ATM Swipe Successfully Saved",
						html : html_swal,
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Create Another ATM Swipe',
						cancelButtonText: 'Back to List of ATM Swipe',
						showDenyButton: true,
						denyButtonText: `Print Entry`,
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/atm_swipe/create?href="+encodeURIComponent('<?php echo $back_link;?>');
						}else if (result.isDenied) {
							var redirect_data = {
								'show_print_entry' : 1,
								'id_atm_swipe' : response.id_atm_swipe
							}
							localStorage.setItem("redirect_print_entry",JSON.stringify(redirect_data));
							window.location = 	link;
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

    				if(response.highlight_amount){
    					$('#txt_swiping_amount').addClass('mandatory');
    				}

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
	}

	$(document).on('keyup','.in_amt',function(){
		var swiping_amount = $('#txt_swiping_amount').val();
		var transaction_fee = $('#txt_transaction_charge').val();

		swiping_amount = decode_number_format(swiping_amount);
		transaction_fee = decode_number_format(transaction_fee);


		var change_payable = swiping_amount-transaction_fee;

		$('#txt_change_payable').val(number_format(change_payable,2));
	})
</script>
@endif


@if($opcode == 1)
<script type="text/javascript">
	$(document).ready(function(){
		var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_print_entry"));
		console.log({redirect_data});
		if(redirect_data != null){
			if(redirect_data.show_print_entry == 1){
				if(redirect_data.id_atm_swipe == '<?php echo $atm_swipe_details->id_atm_swipe; ?>'){
	
					print_page("/atm_swipe/entry/"+redirect_data.id_atm_swipe)
					console.log("SHOW PRINT MODAL")
					localStorage.removeItem("redirect_print_entry");
				}
			}
		}
	})
</script>
@endif
@endpush


							