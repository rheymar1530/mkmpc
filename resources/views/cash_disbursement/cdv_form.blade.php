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

	.tbl_entry tr.entry_row>td{
		padding: 0px !important;
	}
	.tbl_entry tr.entry_row_display>td{
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
	.btn_delete_entry{
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
	.col_entry_entry{
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
		color: white;
	}
	.select2-dropdown--below {
		width: 170%;
	}
	.control-label{
		padding-top: 5px !important;
	}
	.not_balance_amount{
		color: #ff8080;
	}
	.removed_attachment{
		display: none;
	}
</style>
<?php
$paymode = [1=>"Cash",2=>"Check"];

$payee_type = [
	1=>"Supplier",
	2=>"Members",
	3=>"Employee",
	4=>"Others"
];
?>
<div class="" id="entryenses_main_div">
	<div class="container-fluid main_form section_body" style="margin-top: -20px;" >
		<?php $back_link = (request()->get('href') == '')?'/cdv/'.$route:request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to {{$title_mod}} List</a>
		<div class="card">
			<form id="frm_post_entry">
				<div class="card-body col-md-12">
					<h3 class="head_lbl text-center mb-4">{{$title_mod}} 
						@if($opcode == 1)
						<small>(ID# {{$cdv_details->id_cash_disbursement}})</small>
						@if($cdv_details->status == 10)
						<span class="badge badge-danger bgd_cancel">Cancelled</span>
						@endif
						@endif
					</h3>
					<div class="row">
						<div class="col-md-12 row" id="main_cdv_form">	
							<div class="col-md-12" style="margin-top:15px">
								<div class="row">
									<div class="col-md-12 p-0">
										<div class="col-md-6">

											<div class="form-group row p-0" >
												<label for="txt_transaction_date" class="col-md-2 control-label col-form-label" style="text-align: left">Date&nbsp;</label>
												<div class="col-md-4">
													<input type="date" name="" class="form-control entry_parent" value="{{$cdv_details->date ?? $current_date}}" id="txt_transaction_date" key="date">
												</div>
												<label for="sel_branch" class="col-md-2 control-label col-form-label" style="text-align: left;padding-left: 30px !important">&nbsp;&nbsp;Branch&nbsp;</label>
												<div class="col-md-4">
													<select class="form-control form_input p-0 entry_parent" id="sel_branch" key="id_branch" required>
														@foreach($branches as $branch)

														<option value="{{$branch->id_branch}}" <?php echo (isset($cdv_details->id_branch)  && $cdv_details->id_branch == $branch->id_branch)?'selected':''; ?> >{{$branch->branch_name}}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="form-group row p-0" >
												<label for="sel_payee_type" class="col-md-2 control-label col-form-label" style="text-align: left">Payee&nbsp;</label>
												<div class="col-md-3">
													<select class="form-control form_input p-0 entry_parent" id="sel_payee_type" key="payee_type" required>
														@foreach($payee_type as $val=>$desc)
														<option value="{{$val}}">{{$desc}}</option>
														@endforeach
													</select>
												</div>

												<div class="col-md-7">
													<div id="id_payee_holder"></div>
													<div id="div_sel_reference">
														<select class="form-control form_input p-0 entry_parent" id="sel_reference" key="payee_reference">
															@if(isset($selected_reference_payee))
															<option value="{{$selected_reference_payee->id}}">{{$selected_reference_payee->name}}</option>
															@endif
														</select>
													</div>
													<div id="div_payee_others" >

														<input type="text" name="" class="form-control entry_parent" value="{{$payee ?? ''}}" id="txt_payee_others" key="payee" required>
													</div>

												</div>
											</div>
										</div>
										

									</div>
									<div class="col-md-6">
										

										<div class="form-group row p-0" >
											<label for="txt_address" class="col-md-2 control-label col-form-label" style="text-align: left">Address&nbsp;</label>
											<div class="col-md-10">
												<div id="id_payee_holder">
													<input type="text" name="" class="form-control entry_parent" value="{{$cdv_details->address ?? ''}}" id="txt_address" key="address">
												</div>
											</div>
										</div>

										<div class="form-group row p-0" >
											<label for="sel_paymode" class="col-md-2 control-label col-form-label" style="text-align: left">Paymode&nbsp;</label>
											<div class="col-md-3">
												<select class="form-control form_input p-0 entry_parent" id="sel_paymode" key="paymode">

													@foreach($paymode as $val=>$desc)
													<?php
													$selected = (isset($cdv_details->paymode) && $cdv_details->paymode == $val)?"selected":"";
													?>
													<option value="{{$val}}" <?php echo $selected; ?> >{{$desc}}</option>
													@endforeach
												</select>
											</div>
											@if(!$entry_table)
											
											<div class="col-md-7">
												<select class="form-control form_input p-0 entry_parent" id="sel_pay_account" key="paymode_account" required>
													<?php
													if($opcode == 1){
														$paymode_acc = ($cdv_details->paymode_account == 1)?$chart_cash:$chart_check;
													}else{
														$paymode_acc = $chart_cash;
													}
													?>
													@foreach($paymode_acc as $ch)
													<?php $selected_paymode_account = $cdv_details->paymode_account ?? config('variables.default_bank_chart'); ?>
													<option value="{{$ch->id_chart_account}}" <?php echo ($selected_paymode_account == $ch->id_chart_account)?'selected':''; ?> >{{$ch->account_code}} - {{$ch->description}}</option>
													@endforeach
												</select>
											</div>
											@endif
										</div>
										<div id="check_details_holder"></div>
										<div class="form-group row p-0" >
											<label for="txt_amount" class="col-md-2 control-label col-form-label" style="text-align: left !important">Amount&nbsp;</label>
											<div class="col-md-5">
												<input type="text" name="" class="form-control entry_parent class_amount" value="{{number_format(($cdv_details->total ?? 0),2)}}" id="txt_amount" key="amount">
											</div>
										</div>
										<div class="form-group row p-0" id="div_check_details" >

											<label for="txt_check_no" class="col-md-2 control-label col-form-label" style="text-align: left;">Check no.&nbsp;</label>
											<div class="col-md-4">
												<input type="text" name="" class="form-control entry_parent" value="{{$cdv_details->check_no ?? ''}}" id="txt_check_no" key="check_no">
											</div>
											<label for="txt_check_date" class="col-md-2 control-label col-form-label" style="text-align: left;padding-left: 10px !important">Check Date&nbsp;</label>
											<div class="col-md-4">
												<input type="date" name="" class="form-control entry_parent" value="{{$cdv_details->check_date ?? $current_date}}" id="txt_check_date" key="check_date">
											</div>
										</div>
									</div>
									<!-- <div class="col-md-1">&nbsp;</div> -->
									<div class="col-md-6" style="padding-left:30px">
										<div class="form-group row p-0" >
											<label for="txt_reference" class="col-md-2 control-label col-form-label" style="text-align: left !important">Reference&nbsp;</label>
											<div class="col-md-10">
												<input type="text" name="" class="form-control entry_parent"  id="txt_reference" key="reference" value="{{$cdv_details->reference ?? ''}}">
											</div>
										</div>

										<div class="form-group row p-0" >
											<label for="txt_description" class="col-md-2 control-label col-form-label" style="text-align: left !important;">Description&nbsp;</label>
											<div class="col-md-10">
												<textarea class="form-control entry_parent" rows="3" style="resize:none;" id="txt_description" key="description" required>{{$cdv_details->description ?? ''}}</textarea>
											</div>

										</div>
									</div>
								</div>
							</div>	
							<?php
							$select_width = (!$entry_table)?'500px':'100%';
							$header_width = (!$entry_table)?'500px':'100px';
							?>
							@if($entry_table)
							@include('cash_disbursement.form_table_others')
							@else
							@include('cash_disbursement.form_table')
							@endif
						</div>
					</div>
					@if($opcode == 1)
					<div style="padding-top: 10px !important;">
						<span class="text-muted spn_details"><b>Date Created:</b> {{$cdv_details->date_created}}</span><br>
						@if($cdv_details->status == 10)
						<span class="text-muted spn_cdv_details"><b>Date Cancelled:</b> {{$cdv_details->date_cancelled}}</span><br>
						<span class="text-muted spn_cdv_details"><b>Cancellation Reason:</b> {{$cdv_details->cancellation_reason}}</span>
						@endif
					</div>
					@endif
				</div>
				<div class="card-footer">
					@if($allow_post)
					<button class="btn bg-gradient-success2 float-right" id="post_cdv">Post {{$title_mod}}</button>
					@endif
					@if($opcode ==1)
					<button type="button" class="btn bg-gradient-danger2 float-right" style="margin-right:10px" onclick="print_page('/cash_disbursement/print/{{$cdv_details->id_cash_disbursement}}')"><i class="fas fa-print" ></i>&nbsp;Print Cash Disbursement Voucher</button>
					@endif

					@if($allow_post)
					@if($opcode == 1)
					
					@if($cdv_details->status < 10)
					<button type="button" class="btn bg-gradient-warning2 float-right" style="margin-right:10px;color:white" onclick="show_cancel_modal()"><i class="fas fa-times" ></i>&nbsp;Cancel CDV</button>
					@endif
					@endif
					@endif

					@if($opcode == 1 && $allow_post)
					<button type="button" class="btn bg-gradient-primary2 float-right mr-2" onclick="redirect_scheduler()"><i class="fa fa-calendar"></i>&nbsp;Add to scheduler</button>
					@endif
				</div>
			</form>
		</div>
	</div>
</div>

@if($opcode == 1)
@include('global.print_modal')

@if($cdv_details->status < 10)
@include('cash_disbursement.cancellation_modal')
@endif
@endif
@endsection

@push('scripts')
@if($opcode == 1)
<script type="text/javascript">
	function redirect_scheduler(){
		@if($cdv_details->id_scheduler > 0)
			window.open('/scheduler/view/'+{{$cdv_details->id_scheduler}},'_blank');
		@else
			var p = {
				type : 2,
				reference : {{$cdv_details->id_cash_disbursement ?? 0}}
			};
			var encodedObject = encodeURIComponent(JSON.stringify(p));
			window.open('/scheduler/create?s_data='+encodedObject,'_blank');
		@endif
	}
</script>
@endif
<script type="text/javascript" id="entry_table">
	const entry_row_html = '<tr class="entry_row">'+$('tr.entry_row').html()+'</tr>';
	const POST_URL = '/cdv/'+'{{$route}}'+'/post';

	const chart = <?php echo json_encode($charts) ?>;
	var chart_option = {};
	const $opcode = <?php echo $opcode; ?>

	$.each(chart,function(i,item){
		var temp = {};
		temp['id_chart_account'] = item.id_chart_account;
		temp['account_code'] = item.account_code;
		temp['description'] = item.description;

		chart_option[item.id_chart_account] = temp;
	})
	$('tr.entry_row').remove();
	add_entry();

	function add_entry(){

		$('#entry_body').append(entry_row_html);
		$('tr.entry_row').last().hide().show(300);
		var id_acc_code =makeid(8);
		var id_account_name = makeid(8);
		$('tr.entry_row').last().find('select.sel_account_code').attr('id',id_acc_code);
		// $('tr.entry_row').last().find('select.sel_account_description').attr('id',id_account_name);

		$('#'+id_acc_code).val(0);
		// $('#'+id_account_name).val(0);

		$('#'+id_acc_code).select2({width:'{{$select_width}}',dropdownAutoWidth :true,dropdownPosition: 'below'});
		// $('#'+id_account_name).select2({width : '355px',dropdownAutoWidth :true,dropdownPosition: 'below'});
		set_counter_table();
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
	function remove_entry(obj){
		var parent_row = $(obj).closest('tr.entry_row');
		parent_row.fadeOut(300, function(){ 
			$(this).remove();
			set_counter_table();
			set_entry_total();

		});

	}
</script>
<script type="text/javascript">
	var $div_sel_reference = $('#div_sel_reference').detach();
	var $div_payee_others = $('#div_payee_others').detach();
	var $div_reference_jv = $('#div_reference_jv').detach();
	var $div_check_details = $('#div_check_details').detach();
	const $cash_entry = jQuery.parseJSON('<?php echo json_encode($chart_cash) ?>');
	const $check_entry = jQuery.parseJSON('<?php echo json_encode($chart_check) ?>');
	$('#sel_payee_type').val('<?php echo $cdv_details->payee_type ?? 0 ?>');
	// add_entry();
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
	const def_bank = {{$selected_paymode_account ?? config('variables.default_bank_chart')}};

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
			out += '<option value="'+item.id_chart_account+'" '+((def_bank==item.id_chart_account)?'selected':'')+'>'+item.account_code+' - '+item.description+'</option>'
		})
		$('#sel_pay_account').html(out);
		$('#sel_pay_account').select2();
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
</script>

@if($opcode == 1)
<script type="text/javascript">
	initialize_payee_type(false,null);
	initialize_paymode();
	const entries = jQuery.parseJSON('<?php echo json_encode($entries ?? [])?>');
	$('tr.entry_row').remove();
	
	$.each(entries,function(i,item){
		add_entry();
		$('tr.entry_row').last().find('.txt_debit').val(check_zero(item.debit))
		$('tr.entry_row').last().find('.txt_credit').val(check_zero(item.credit))
		$('tr.entry_row').last().find('.txt_details').val(item.remarks)
		$('tr.entry_row').last().find('.sel_account_code').val(item.id_chart_account).trigger("change");

		console.log({item})
	})

	$(document).ready(function(){
		var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_print_cdv"));
		console.log({redirect_data});
		if(redirect_data != null){
			if(redirect_data.show_print_cdv == 1){
				if(redirect_data.id_cash_disbursement == '<?php echo $cdv_details->id_cash_disbursement; ?>'){
					print_page("/cash_disbursement/print/"+redirect_data.id_cash_disbursement)
					console.log("SHOW PRINT MODAL")
					localStorage.removeItem("redirect_print_cdv");
				}
			}
		}
		<?php
		if($allow_post == 0){
			echo "$('#main_cdv_form').find('input,select,a,textarea').attr('disabled',true);";
		}

		?>
	})
	function check_zero($in){
		$in = parseFloat($in);
		return ($in == 0 || isNaN($in))?'':number_format($in,2);
	}
</script>

@endif
@if($allow_post)
<!-- POST -->
<script type="text/javascript">
	let removed_attachments = [];
	function post(){
		var entry_parent = {};
		$('.entry_parent').each(function(){
			var data_key = $(this).attr('key');
			var val  =  $(this).val();
			if($(this).hasClass('class_amount')){
				val = decode_number_format(val);
			}
			entry_parent[data_key] = val;
		})
		var entry_account = parseEntryRow();



		var form_data = new FormData();
		form_data.append("entry_parent",JSON.stringify(entry_parent));
		form_data.append("entry_account",JSON.stringify(entry_account));
		form_data.append("opcode",$opcode);
		form_data.append("id_cash_disbursement",'<?php echo $cdv_details->id_cash_disbursement ?? 0 ?>');
		form_data.append("deleted_attachment",JSON.stringify(removed_attachments));
		// form_data.append("attachments",JSON.stringify(populate_attachment()));

		var attachments = populate_attachment();

		for(var $i=0;$i<attachments.length;$i++){
			form_data.append("attachment_"+$i,attachments[$i]);
		}
		form_data.append("attachment_length",attachments.length);

		$.ajax({

			type          :          'POST',
			url           :          POST_URL,
			contentType		 :           false,
			data             :           form_data,
			cache			 : 			 false, // To unable request pages to be cached
			processData	     : 	 	     false,
			// data          :          {'entry_parent'  : entry_parent,
			// 						  'entry_account' : entry_account,
			// 						  'opcode'   : $opcode,
			// 						  'id_cash_disbursement' : '<?php echo $cdv_details->id_cash_disbursement ?? 0 ?>'},
			beforeSend    :     	 function(){
				show_loader();
			},
			success       :          function(response){
				hide_loader();
				console.log({response});
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
					var link = "/cdv/"+'{{$route}}'+"/view/"+response.id_cash_disbursement+"?href="+encodeURIComponent('<?php echo $back_link;?>');
					Swal.fire({
						title: '{{$title_mod}}'+" Successfully Saved !",
						html : "<a href='"+link+"'>CDV ID# "+response.id_cash_disbursement+"</a>",
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Create More '+'{{$title_mod}}',
						showDenyButton: true,
						denyButtonText: `Print CDV`,
						cancelButtonText: 'Close',
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location =  "/cdv/"+'{{$route}}'+"/create?href="+encodeURIComponent('<?php echo $back_link;?>');
						}else if (result.isDenied) {
							var redirect_data = {
								'show_print_cdv' : 1,
								'id_cash_disbursement' : response.id_cash_disbursement
							}
							localStorage.setItem("redirect_print_cdv",JSON.stringify(redirect_data));
							window.location = 	link;
						}else{
							window.location = '<?php echo $back_link;?>';
						}
					});

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
	function populate_attachment(){
		var out = [];
		
		$('.file_attachment').each(function(){
			var file_length = $(this).prop('files').length;

			for(var $i=0;$i<file_length;$i++){
				var file = $(this).prop('files')[$i];
				console.log({file})
				
				if(file != undefined){
					out.push(file)
				}				
			}

		})
		console.log({out});
		return out;

		console.log({out})
	}
	function pop_test(){
		$('.file_attachment').each(function(){
			var file = $(this).prop('files').length;
			alert(file)
		})	
	}
</script>
@endif
@endpush


