@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif !important;

	}
	.tbl_entry tr>th {
		padding: 5px;
		vertical-align: top;
		font-size: 14px;
	}

	.tbl_entry tr.exp_row>td{
		padding: 0px !important;
	}
	.tbl_entry tr.exp_row_display>td{
		padding: 3px !important;
		font-weight: bold;
		font-size: 15px;

	}
	.tbl_entry input:not([type='checkbox']),.tbl_entry select {
		height: 25px !important;
		width: 100%;

	}	
	.table_header_dblue{
		text-align: center;
	}
	.btn_delete_exp{
		width: 100%;
		height: 23px !important;
		padding: 0px 2px 0px 2px !important;
	}
	.tbl_entry .select2-selection {
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
	.col_exp_entry{
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
		max-height: 250px; //This can be any height you want
		overflow:scroll;
	}
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

		-webkit-transition: all 0.25s;
		-o-transition: all 0.25s;

	}

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
	.control-label{
		padding-top: 5px !important;
	}
</style>
<?php
$paymode = [1=>"Cash",2=>"Check"];

$payee_type = [
	1=>"Supplier",
	2=>"Members",
		// 3=>"Employee",
	4=>"Others"
];
?>
<div class="wrapper2" id="expenses_main_div">
	<div class="container-fluid main_form" style="margin-top: -20px;" >
		<div class="card">
			<form id="frm_post_expenses">
				<div class="card-body col-md-12">
					<h3>Expenses</h3>
					<div class="row">
						<div class="col-md-12 row">	
							<div class="col-md-12" style="margin-top:15px">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group row p-0" >
											<label for="txt_transaction_date" class="col-md-2 control-label col-form-label" style="text-align: left">Date&nbsp;</label>
											<div class="col-md-4">
												<input type="date" name="" class="form-control jv_parent" value="{{$jv_details->date ?? $current_date}}" id="txt_transaction_date" key="date">
											</div>
											<label for="sel_branch" class="col-md-2 control-label col-form-label" style="text-align: left;padding-left: 30px !important">&nbsp;&nbsp;Branch&nbsp;</label>
											<div class="col-md-4">
												<select class="form-control form_input p-0 jv_parent" id="sel_branch" key="id_branch" required>

												</select>
											</div>
										</div>
										<div class="form-group row p-0" >
											<label for="sel_payee_type" class="col-md-2 control-label col-form-label" style="text-align: left">Payee&nbsp;</label>
											<div class="col-md-3">
												<select class="form-control form_input p-0 jv_parent" id="sel_payee_type" key="payee_type" required>
													@foreach($payee_type as $val=>$desc)
													<option value="{{$val}}">{{$desc}}</option>
													@endforeach
												</select>
											</div>
											
											<div class="col-md-7">
												<div id="id_payee_holder"></div>
												<div id="div_sel_reference">
													<select class="form-control form_input p-0 jv_parent" id="sel_reference" key="payee_reference" required>
														@if(isset($selected_reference_payee))
														<option value="{{$selected_reference_payee->id}}">{{$selected_reference_payee->name}}</option>
														@endif
													</select>
												</div>
												<div id="div_payee_others" >

													<input type="text" name="" class="form-control jv_parent" value="{{$payee ?? ''}}" id="txt_payee_others" key="payee" required>
												</div>
												
											</div>
										</div>
										<div class="form-group row p-0" >
											<label for="txt_address" class="col-md-2 control-label col-form-label" style="text-align: left">Address&nbsp;</label>
											<div class="col-md-10">
												<div id="id_payee_holder">
													<input type="text" name="" class="form-control jv_parent" value="" id="txt_address" key="address">
												</div>
											</div>
										</div>

										<div class="form-group row p-0" >
											<label for="sel_paymode" class="col-md-2 control-label col-form-label" style="text-align: left">Paymode&nbsp;</label>
											<div class="col-md-3">
												<select class="form-control form_input p-0 exp_parent" id="sel_paymode" key="paymode">
													@foreach($paymode as $val=>$desc)
													<option value="{{$val}}">{{$desc}}</option>
													@endforeach
												</select>
											</div>
											
											<div class="col-md-7">
												<select class="form-control form_input p-0 jv_parent" id="sel_pay_account" key="paymode_account" required>
													@foreach($chart_cash as $ch)
													<option value="{{$ch->id_chart_account}}">{{$ch->account_code}} - {{$ch->description}}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div id="check_details_holder"></div>
										<div class="form-group row p-0" id="div_check_details" >

											<label for="txt_check_no" class="col-md-2 control-label col-form-label" style="text-align: left;">Check no.&nbsp;</label>
											<div class="col-md-4">
												<input type="text" name="" class="form-control jv_parent" value="" id="txt_check_no" key="check_no">
											</div>
											<label for="txt_check_date" class="col-md-2 control-label col-form-label" style="text-align: left;padding-left: 10px !important">Check Date&nbsp;</label>
											<div class="col-md-4">
												<input type="date" name="" class="form-control jv_parent" value="{{$jv_details->date ?? $current_date}}" id="txt_check_date" key="check_date">
											</div>
										</div>
									</div>
									<!-- <div class="col-md-1">&nbsp;</div> -->
									<div class="col-md-6" style="padding-left:30px">
										<div class="form-group row p-0" >
											<label for="txt_reference" class="col-md-2 control-label col-form-label" style="text-align: left !important">Reference&nbsp;</label>
											<div class="col-md-10">
												<input type="text" name="" class="form-control jv_parent" value="" id="txt_reference" key="reference">
											</div>
										</div>
										<div class="form-group row p-0" >
											<label for="txt_amount" class="col-md-2 control-label col-form-label" style="text-align: left !important">Amount&nbsp;</label>
											<div class="col-md-5">
												
												<input type="text" name="" class="form-control jv_parent" value="" id="txt_amount" key="amount">
												
											</div>
										</div>
										<div class="form-group row p-0" >
											<label for="txt_amount" class="col-md-2 control-label col-form-label" style="text-align: left !important">Description&nbsp;</label>
											<div class="col-md-10">
												<textarea class="form-control jv_parent" rows="3" style="resize:none;" id="txt_description" key="description" required>{{$jv_details->description ?? ''}}</textarea>
											</div>

										</div>
									</div>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	var $div_sel_reference = $('#div_sel_reference').detach();
	var $div_payee_others = $('#div_payee_others').detach();
	var $div_reference_jv = $('#div_reference_jv').detach();
	var $div_check_details = $('#div_check_details').detach();
	const $cash_entry = jQuery.parseJSON('<?php echo json_encode($chart_cash) ?>');
	const $check_entry = jQuery.parseJSON('<?php echo json_encode($chart_check) ?>');
	$('#sel_payee_type').val('<?php echo $jv_details->payee_type ?? 0 ?>');
	$(document).on('change','#sel_payee_type',function(){
		initialize_payee_type(true,null);
	})
	$(document).ready(function(){
		$('#sel_pay_account').select2()
	})
	function initialize_payee_type(reset_reference,val){
		var payee_type = $('#sel_payee_type').val();
		$('#id_payee_holder').html('')
		if(payee_type <= 3){

			$('#id_payee_holder').html($div_sel_reference);
			if(reset_reference){
				$('#sel_reference').val(0).trigger("change");

			}
			if(val != null){
				$('#sel_reference').html(val);
			}
			intialize_select2(payee_type)

		}else{

			$('#id_payee_holder').html($div_payee_others);
		}

		animate_element($('#id_payee_holder'),1)
	}
	function intialize_select2(type){		
		var $link = '';
		if(type == 1){
			$link = '/search_supplier';
		}else if(type == 2){
			$link = '/search_member'
		}else if(type == 3){
			$link = '/search_employee';
		}

		$("#sel_reference").select2({
			minimumInputLength: 2,
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
	$(document).on('change','#sel_reference',function(){
		parseAddress();
		console.log("QWe")
	})
	function parseAddress(){

		if($('#sel_reference').val() == null){
			return;
		}

		$.ajax({
			type          :           'GET',
			url           :           '/journal_voucher/parse_address',
			data          :           {'reference'   :    $('#sel_reference').val(),
			'type'        :    $('#sel_payee_type').val()},
			success       :           function(response){
				console.log({response});
				$('#txt_address').val(response.response.address ?? '');
			},error: function(xhr, status, error) {

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
	$('#sel_paymode').change(function(){
		initialize_paymode();
	})
	//Paymode
	function initialize_paymode(){
		try{
			$('#sel_pay_account').select2('destroy');
		}catch(err){
			console.log({err})
		}
		var val = $('#sel_paymode').val();
		if(val == 1){
			$options = $cash_entry;
			$('#check_details_holder').html('')

		}else{
			$options = $check_entry;
			$('#check_details_holder').html($div_check_details)

		}		
		var out = '';
		console.log({$options})
		$.each($options,function(i,item){
			out += '<option value="'+item.id_chart_account+'">'+item.account_code+' - '+item.description+'</option>'
		})
		$('#sel_pay_account').html(out);
		$('#sel_pay_account').select2();
	}
</script>
@endpush