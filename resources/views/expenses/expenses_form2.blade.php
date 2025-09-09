@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif !important;

	}
	.tbl_exp tr>th {
		padding: 5px;
		vertical-align: top;
		font-size: 14px;
	}

	.tbl_exp tr.exp_row>td{
		padding: 0px !important;

	}
	.tbl_exp tr.exp_row_display>td{
		padding: 3px !important;
		font-weight: bold;
		font-size: 15px;

	}
	.tbl_exp input:not([type='checkbox']),.tbl_exp select {
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
	.tbl_exp .select2-selection {
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
</style>
<?php
$paymode = [1=>"Cash",2=>"Check"];
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
											<label for="sel_jv_type" class="col-sm-2 control-label col-form-label" style="text-align: left">Paymode&nbsp;</label>
											<div class="col-sm-9">
												<select class="form-control form_input p-0 exp_parent" id="sel_paymode" key="paymode">
													@foreach($paymode as $val=>$desc)
													<option value="{{$val}}">{{$desc}}</option>
													@endforeach
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="table-responsive" style="max-height: calc(100vh - 300px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
									<table class="table table-bordered table-stripped table-head-fixed tbl_exp" style="white-space: nowrap;">
										<thead>
											<tr>
												<th class="table_header_dblue"  width="20px"></th>
												<th class="table_header_dblue" width="100px">Account Code</th>
												<th class="table_header_dblue" width="350px">Description</th>
												<th class="table_header_dblue" width="150px">Debit</th>
												<th class="table_header_dblue" width="150px">Credit</th>
												<th class="table_header_dblue">Details</th>

												<th class="table_header_dblue" width="30px"></th>
											</tr>
										</thead>
										<tbody id="exp_body">
											<?php
											$total_debit = 0;
											$total_credit = 0;
											?>
											<tr class="exp_row" id="row_paymode">
												<td class="td_counter">1</td>
												<td>
													<select class="form-control p-0 select2 sel_account_code sel_chart col_exp_entry sel_entry_paymode" id="sel_paymode_account_code">
														@foreach($charts as $chart)
														<option value="{{$chart->id_chart_account}}" data-key="{{$chart->account_code}}">{{$chart->account_code}} - {{$chart->description}}</option>
														@endforeach
													</select>
												</td>
												<td>
													<select class="form-control p-0 select2 sel_account_description sel_chart col_exp_entry sel_entry_paymode" id="sel_paymode_description">
														@foreach($charts as $chart)
														<option value="{{$chart->id_chart_account}}">{{$chart->account_code}} - {{$chart->description}}</option>
														@endforeach
													</select>
												</td>
												<td><input class="col_exp_entry form-control class_amount txt_debit" value=""></td>
												<td><input class="col_exp_entry form-control class_amount txt_credit" value=""></td>
												<td><input class="col_exp_entry form-control txt_details" value=""></td>
												<td><a class="btn btn-xs bg-gradient-danger btn_delete_exp" onclick="remove_exp(this)"><i class="fa fa-trash"></i></a></td>
											</tr>
										</tbody>
										<tfoot>

											<tr>
												<th class="footer_fix font-total" colspan="3" style="text-align:center;background: #808080;color: white;">T&nbsp;&nbsp;O&nbsp;&nbsp;T&nbsp;&nbsp;A&nbsp;&nbsp;L</th>
												<th class="footer_fix class_amount font-total" style="padding-right:12px;background: #808080;color: white;" id="td_tot_debit">{{number_format($total_debit,2)}}</th>
												<th class="footer_fix class_amount font-total" style="padding-right:12px;background: #808080;color: white;" id="td_tot_credit">{{number_format($total_credit,2)}}</th>
												<th class="footer_fix" colspan="2" style="background: #808080;color: white;"></th>
											</tr>
										</tfoot>
									</table>    
								</div> 
								<button type="button" class="btn btn-xs bg-gradient-primary col-md-12" onclick="add_exp()"><i class="fa fa-plus"></i> Add Item</button>
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
	const exp_row_html = '<tr class="exp_row exp_row_in">'+$('tr.exp_row').html()+'</tr>';
	const $cash_entry = jQuery.parseJSON('<?php echo json_encode($chart_cash) ?>');
	const $check_entry = jQuery.parseJSON('<?php echo json_encode($chart_check) ?>');
	const $chart = jQuery.parseJSON('<?php echo json_encode($charts) ?>');
	var chart_option = {};


	$.each($chart,function(i,item){
		var temp = {};
		temp['id_chart_account'] = item.id_chart_account;
		temp['account_code'] = item.account_code;
		temp['description'] = item.description;

		chart_option[item.id_chart_account] = temp;
	})
	$.each($cash_entry,function(i,item){
		var temp = {};
		temp['id_chart_account'] = item.id_chart_account;
		temp['account_code'] = item.account_code;
		temp['description'] = item.description;

		chart_option[item.id_chart_account] = temp;
	})
	$.each($check_entry,function(i,item){
		var temp = {};
		temp['id_chart_account'] = item.id_chart_account;
		temp['account_code'] = item.account_code;
		temp['description'] = item.description;

		chart_option[item.id_chart_account] = temp;
	})
	$(document).ready(function(){

		initialize_paymode()

		add_exp()
	})
	$(document).on('select2:open', (e) => {
		const selectId = e.target.id
		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function(key,value,){
			value.focus()
		})
	}) 

	$(document).on('change','#sel_paymode',function(){
		initialize_paymode();
	})
	function initialize_paymode(){
		try{
			$('.sel_entry_paymode').select2('destroy');
		}catch(err){
			console.log({err})
		}
		var val = $('#sel_paymode').val();
		if(val == 1){
			$options = $cash_entry;
		}else{
			$options = $check_entry;
		}		
		var out = '';
		console.log({$options})
		$.each($options,function(i,item){
			out += '<option value="'+item.id_chart_account+'">'+item.account_code+' - '+item.description+'</option>'
		})
		$('.sel_entry_paymode').html(out);
		$('.sel_entry_paymode').val(0)
		$('#sel_paymode_account_code').select2({dropdownAutoWidth :true,dropdownPosition: 'below'});
		$('#sel_paymode_description').select2({width : '355px',dropdownAutoWidth :true,dropdownPosition: 'below'});
		$('#row_paymode').find('.btn_delete_exp').remove();

		$('#row_paymode').find('.txt_debit').attr('readonly',true)
	}
	function add_exp(){
		$('#row_paymode').before(exp_row_html);
		$('tr.exp_row_in').last().hide().show(300);
		var id_acc_code =makeid(8);
		var id_account_name = makeid(8);
		$('tr.exp_row_in').last().find('select.sel_account_code').attr('id',id_acc_code).removeClass("sel_entry_paymode");
		$('tr.exp_row_in').last().find('select.sel_account_description').attr('id',id_account_name).removeClass("sel_entry_paymode");
		$('tr.exp_row_in').last().find('.txt_credit').attr('readonly',true);
		$('#'+id_acc_code).val(0);
		$('#'+id_account_name).val(0);
		$('#'+id_acc_code).select2({dropdownAutoWidth :true,dropdownPosition: 'below'});
		$('#'+id_account_name).select2({width : '355px',dropdownAutoWidth :true,dropdownPosition: 'below'});


		set_counter_table()
	}
	function makeid(length){
		var result           = '';
		var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		var charactersLength = characters.length;
		for(var i = 0; i < length; i++ ) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}
	function set_counter_table(){
		$('td.td_counter').each(function(i){
			$(this).text(i+1);
		})
	}
	$(document).on('change','.sel_account_code',function(e,wasTriggered){
		if(!wasTriggered){
			fill_description_v_code($(this));
		}
		var id = $(this).attr("id");
		var val = parseInt($(this).val());
		$('span#select2-'+id+'-container').text(chart_option[val]['account_code'])
	})
	$(document).on('change','.sel_account_description',function(e,wasTriggered){
		if(!wasTriggered){
			fill_code_v_description($(this));
		}
		var id = $(this).attr("id");
		var val = parseInt($(this).val());
		$('span#select2-'+id+'-container').text(chart_option[val]['description'])
	})
	function fill_description_v_code(obj){

		var parent_row = $(obj).closest('tr.exp_row');
		var val = $(obj).val();
		parent_row.find('.sel_account_description').val(val).trigger('change',true)
		set_entry_total()
		
	}
	function fill_code_v_description(obj){

		var parent_row = $(obj).closest('tr.exp_row');
		var val = $(obj).val();
		parent_row.find('.sel_account_code').val(val).trigger('change',true)

		set_entry_total();
		
	}
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
	function set_entry_total(){
		var $credit = 0;
		var $debit = 0;

		$('tr.exp_row').each(function(){
			var parent_row = $(this);
			var id_chart_account = $(this).find('.sel_account_code').val();

			if(id_chart_account != null){
				var deb = parent_row.find('.txt_debit').val();
				deb = (deb=='')?0:deb;
				deb = (!$.isNumeric(deb))?decode_number_format(deb):parseFloat(deb);
				$debit+= deb;

				var cred = parent_row.find('.txt_credit').val();
				cred = (cred=='')?0:cred;
				cred = (!$.isNumeric(cred))?decode_number_format(cred):parseFloat(cred);
				$credit+= cred;
			}
			
		})
		$debit = (isNaN($debit))?0:$debit;
		$credit = (isNaN($credit))?0:$credit;
		$('#td_tot_debit').text(number_format($debit,2));
		$('#td_tot_credit').text(number_format($credit,2));

		var output = {};
		output['debit'] = $debit;
		output['credit'] = $credit;
		console.log({$debit,$credit});

		return output;
	}
	$(document).on('keyup','.txt_credit,.txt_debit',function(){
		set_entry_total()
	})
	function remove_exp(obj){
		var parent_row = $(obj).closest('tr.exp_row');
		parent_row.fadeOut(300, function(){ 
			$(this).remove();
			set_counter_table();
			set_entry_total();

		});

	}
</script>
@endpush