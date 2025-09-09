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
	.charges_text{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif !important;
	}
	.text_undeline{
		text-decoration: underline;
		font-size: 20px;
	}
	.tbl_loans tr>th ,.tbl_fees tr>th,.tbl-inputs tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
		text-align: center;
	}
	.tbl-inputs-text{
		padding-left: 5px !important;
		padding-right: 5px !important;
		/*padding: px !important;*/
	}
	.tbl_loans tr>td,.tbl_fees tr>td,.tbl_repayment_display tr>td{
		padding: 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
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

	.class_amount{
		text-align: right;

	}
	.cus-font{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px !important;       
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.form-label{
		margin-bottom: 4px !important;
	}

	.modal-conf {
		max-width:98% !important;
		min-width:98% !important;

	}
	.text_center{
		text-align: center;
	}
	.text_bold{
		font-weight: bold;
	}
	.spn_t{
		font-weight: bold;
		font-size: 16px;
	}
	.spn_txt{
		word-wrap:break-word;
		overflow: hidden;
		text-align: right;
	}
	.label_totals{
		margin-top: -13px !important;
	}
	.border-top{
		border-top:2px solid !important; 
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
</style>
<?php
$transaction_type = [
	2=>"ATM Swipe",
	1=>"Cash",
	
];

$js_allow_post = ($allow_post)?1:0;
?>
<div class="wrapper2">
	<div class="container-fluid main_form" style="margin-top: -20px;" >
		<?php $back_link = (request()->get('href') == '')?'/repayment':request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment Transaction List</a>
		@if(($opcode == 1 && $allow_post))
		<a class="btn bg-gradient-danger btn-sm float-right" style="margin-bottom:10px" onclick="cancel_repayment()"><i class="fas fa-times"></i>&nbsp;&nbsp;Cancel Loan Payment</a>
		@endif
		<div class="card" id="repayment_main_div">
			<div class="card-body col-md-12">
				<h3>Loan Payment @if($opcode == 1)(Loan Payment ID# {{$repayment_transaction->id_repayment_transaction}})@endif</h3>
				<div class="row">
					<div class="col-md-12 row">	
						<div class="col-md-12">
							<div class="form-row">
								<div class="form-group col-md-3" style="margin-top:20px">
									<label for="txt_transaction_date">Transaction Date</label>
									<input type="date" name="" class="form-control" value="{{$repayment_transaction->transaction_date ?? $current_date}}" id="txt_transaction_date">
								</div>
								<div class="form-group col-md-3" style="margin-top:20px">
									<label for="sel_transaction_type">Payment Mode</label>
									<select class="form-control p-0" id="sel_transaction_type">
										@foreach($transaction_type as $val=>$text)
										<?php
										$selected = ($opcode == 1 && $repayment_transaction->transaction_type == $val)?"selected":"";
										?>
										<option value="{{$val}}" {{$selected}}>{{$text}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-5">
									<label for="txt_description">Select Borrower</label>
									<select class="form-control select2" id="sel_borrower">
										@if($opcode == 1)
										<option value="{{$repayment_transaction->id_member}}">{{$repayment_transaction->selected_member}}</option>
										@endif
									</select>
								</div>
								@if($opcode == 0)
								<!-- <div class="form-group col-md-1">
									<label for="txt_description">&nbsp;</label>
									<button class="btn btn-sm bg-gradient-success form-control" onclick="reload_member_dues()">Reload</button>
								</div> -->
								@endif
							</div>
							<div class="form-row">
								<div class="form-group col-md-5">
									<label for="txt_description">Due Date</label>
									<select class="form-control p-0" id="txt_date">
										
										@if($opcode == 1)
										<option value="{{$repayment_transaction->date}}">{{$repayment_transaction->due_date_text}}</option>
										@endif
									</select>
									<!-- <input type="date" name="" class="form-control" value="{{$repayment_transaction->date ?? $current_date}}" id="txt_date"> -->
								</div>
							</div>

							<div class="form-row" id="div_swiping_amount_holder">
								<div id="">
									<div class="form-group col-md-4 div_swiping_amount">
										<label for="txt_description">Bank</label>
										<select class="form-control" id="sel_bank" required>
											<?php
												if($opcode == 0){
													$selected_bank = 2;
												}else{
													$selected_bank = ($repayment_transaction->id_bank > 0)?$repayment_transaction->id_bank:2;
												}
												
											?>
											@foreach($banks as $bank)
												<option value="{{$bank->id_bank}}" <?php echo ($selected_bank==$bank->id_bank)?'selected':''; ?> >{{$bank->bank_name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group col-md-4 div_swiping_amount">
										<label for="txt_description">Swiping Amount</label>
										<input type="text" name="" required class="form-control class_amount" id="swiping_amount" value="{{number_format(($repayment_transaction->swiping_amount ?? 0),2)}}">
									</div>

								</div>
							</div>
						</div>
						<div class="col-md-8" style="margin-top:20px">
							<div class="form-group col-md-12 p-0" style="margin-bottom:unset;">
								<div class="card">
									<div class="card-body">
										<table style="width:30%">
											<tr>
												<td class="spn_t">Current Balance:</td>
												<td class="class_amount" width="30%" id="spn_current_balance">0.00</td>
												<td></td>
											</tr>
											<tr>
												<td class="spn_t">Previous Balance:</td>
												<td class="class_amount" width="30%" id="spn_previous_balance">0.00</td>
												<td></td>
											</tr>

										</table>	
									</div>
								</div>
								<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
									<table class="table table-bordered table-stripped table-head-fixed tbl-inputs" style="white-space: nowrap;">
										<thead>
											<tr>
												<th class="table_header_dblue">Loan Dues</th>
												<th class="table_header_dblue">Amount</th>
												<th class="table_header_dblue" ondblclick="fill_zero()">Payment</th>
											</tr>
										</thead>
										<tbody id="loan_dues_body">


										</tbody>
										<tfoot>
											<tr>
												<td class="tbl-inputs-text text_bold">TOTAL</td>
												<td class="class_amount text_bold tbl-inputs-text" id="txt_total_loan_due">0.00</td>
												<td class="class_amount text_bold tbl-inputs-text" id="txt_col_total_payment" style="padding-right:12px !important"></td>
											</tr>
										</tfoot>
									</table>    
								</div>  
							</div>

							<div class="col-md-12 p-0">
								<h5>Penalty&nbsp;&nbsp;@if($allow_post)<button class="btn btn-sm bg-gradient-primary" onclick="append_penalty()"><i class="fa fa-plus"></i>&nbsp;Add</button>@endif</h5>
								<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
									<table class="table table-bordered table-stripped table-head-fixed tbl-inputs" style="white-space: nowrap;" id="tbl_penalty">
										<thead>
											<tr>
												<th class="table_header_dblue" width="8%"></th>
												<th class="table_header_dblue" width="50%">Penalty Type</th>
												<th class="table_header_dblue">Amount</th>
												<th class="table_header_dblue" width="5%"></th>
											</tr>
										</thead>
										<tbody id="repayment_penalty_body">
											<tr class="row_penalty">
												<td class="col-count text_center">1</td>
												<td><select class="form-control p-0 sel_penalty frm_loans sel_penalty" key="id_payment_type">
													@foreach($penalties as $p)
													<option value="{{$p->id_payment_type}}">{{$p->description}}</option>
													@endforeach
												</select></td>
												<td><input type="text" name="" required class="form-control class_amount frm_loans txt_penalty_amount" value="0.00" key="amount"></td>
												<td class="text_center"><a onclick="remove_penalty(this)" style="margin-top: 10px !important;" class="frm-other-lending"><i class="fa fa-times"></i></a></td>
											</tr>

										</tbody>
									</table>
								</div>
							</div>
							<div class="col-md-8 p-0">
								<h5>Other Fees</h5>
								<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
									<table class="table table-bordered table-stripped table-head-fixed tbl-inputs" style="white-space: nowrap;">
										<thead>
											<tr>
												<th class="table_header_dblue" width="8%"></th>
												<th class="table_header_dblue">Fee</th>
												<th class="table_header_dblue">Amount</th>
											</tr>
										</thead>
										<tbody id="loan_fee_display">
											@foreach($repayment_fee as $count=>$rf)
											<tr class="row_fees" data-id="{{$rf->id_payment_type}}">
												<td class="text_center">{{$count+1}}</td>
												<td style="padding-left: 10px">{{$rf->description}}</td>
												<td><input type="text" name="" required class="form-control class_amount frm_loans txt_fee_amount" value="{{number_format($rf->amount,2)}}" key="amount"></td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-4" style="margin-top:10px">
							
							<div class="card">
								<div class="card-body">
									<h5 id="or_text_holder">
										@if(isset($repayment_transaction->or_no))
										<span >OR No. {{$repayment_transaction->or_no}}</span>
										<a style="margin-left:20px;font-size: 14px;" href="#" onclick="update_or()"><i class="nav-icon fas fa-cog"></i></a>
										@endif
									</h5>
									<h5><u>Loan Payment Summary</u></h5>
									<table style="width:100%">
										<tr>
											<td class="spn_t">Total Loan Amount Due:</td>
											<td class="class_amount" width="30%" id="spn_total_loan_amount_due">0.00</td>
											<td></td>
										</tr>
										<tr>
											<td class="spn_t">Total Others Fees:</td>
											<td class="class_amount" width="30%" id="spn_total_loan_fees">0.00</td>
											<td></td>
										</tr>
										<tr>
											<td class="spn_t">Total Loan Penalty:</td>
											<td class="class_amount" width="30%" id="spn_total_loan_penalty">0.00</td>
											<td></td>
										</tr>
										<tr>
											<td class="spn_t">Total Amount Due:</td>
											<td class="class_amount" width="30%" id="spn_total_amount_due">0.00</td>
											<td></td>
										</tr>
										<tr class="row_swiping_amount">
											<td class="spn_t">Swiping Amount:</td>
											<td class="class_amount" width="30%" id="spn_swiping_amount">0.00</td>
											<td></td>
										</tr>
										<tr>
											<td class="spn_t">Total Paid Amount:</td>
											<td class="class_amount" width="30%" id="spn_total_paid_amount">0.00</td>
											<td></td>
										</tr>
										<tr class="row_swiping_amount">
											<td class="spn_t">Change:</td>
											<td class="class_amount" width="30%" id="spn_change">0.00</td>
											<td></td>
										</tr>
									</table>

								</div>
							</div>
							@if(isset($repayment_change) && count($repayment_change) > 0)
							<div class="card">
								<div class="card-body">
									<h5><u>Change Released</u></h5>
									<table style="width:100%">

										<tr>
											<th>Ref#</th>
											<th>Date</th>
											<th>Amount</th>
										</tr>
										<?php $total_change = 0; ?>
										@foreach($repayment_change as $rc)
										<tr>
											<td>{{$rc->id_repayment_change}}</td>
											<td>{{$rc->date}}</td>
											<td class="class_amount">{{number_format($rc->amount,2)}}</td>
										</tr>
										<?php $total_change += $rc->amount;?>
										@endforeach
										<tr style="border-top:1px solid;">
											<th colspan="2">Total</th>
											<th class="class_amount">{{number_format($total_change,2)}}</th>
										</tr>
										<tr>
											<th colspan="2">Remaining Change</th>
											<th class="class_amount">{{number_format(($repayment_transaction->change-$total_change),2)}}</th>
										</tr>
									</table>
								</div>
							</div>
							@endif
						</div>
					</div>	
					<div class="col-md-12" style="margin-top:10px">

						<fieldset class="border" style="padding-left: 10px;padding-right: 10px;padding-bottom: 10px;">
							<legend class="w-auto p-1">Loan Payment Remarks</legend>
							<textarea class="form-control" rows="3" style="resize:none;" id="txt_remarks">{{$repayment_transaction->remarks ?? ''}}</textarea>
						</fieldset>					

					</div>
				</div>
			</div>
			
			<div class="card-footer">
				@if($allow_post)
				@if($opcode == 0 || ($opcode ==1 && $repayment_transaction->or_no == null))
				<button class="btn bg-gradient-success float-right" onclick="save_repayment()" disabled id="btn_save_repayment">Save & Print Loan Payment</button>
				@endif
				@endif
				@if($opcode == 1 && $repayment_transaction->status < 10)
				<!-- <button type="button" class="btn bg-gradient-danger float-right" style="margin-right:10px" onclick="print_page('/journal_voucher/print/{{$repayment_transaction->id_journal_voucher}}')"><i class="fas fa-print" ></i>&nbsp;Print Journal Voucher (JV #{{$repayment_transaction->id_journal_voucher}})</button> -->
				<button type="button" class="btn bg-gradient-danger float-right" style="margin-right:10px" onclick="print_page('/cash_receipt_voucher/print/{{$repayment_transaction->id_cash_receipt_voucher}}')"><i class="fas fa-print" ></i>&nbsp;Print CRV (CRV #{{$repayment_transaction->id_cash_receipt_voucher}})</button>
				<button class="btn bg-gradient-primary float-right" onclick="print_or()" id="btn_print_or" style="margin-right: 10px;">Print OR</button>
				@endif
			</div>
			
		</div>
	</div>
</div>





@if($opcode == 1)
@include('repayment.repayment_or')
@include('global.print_modal')
@if($credential->is_cancel)
@include('repayment.cancel_modal')
@endif
@endif




@endsection
@push('scripts')


<script type="text/javascript">

	function gg(){

	}
	$penalty_row = '<tr class="row_penalty">'+$('tr.row_penalty').html()+'</tr>';
	$no_penalty_row = '<tr class="row_no_penalty"><td colspan="4" style="text-align:center">No Loan Payment Penalty</td></tr>'
	$div_swiping_amount = $('.div_swiping_amount').detach();
	$('#repayment_penalty_body').html($no_penalty_row)
	const opcode = '<?php echo $opcode;?>';
	let total_loan_due = 0;
	initialize_transaction_type()
	$(document).on('select2:open', (e) => {
		const selectId = e.target.id
		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
			key,
			value,
			) {
			value.focus()
		})
	}) 
	intialize_select2();
	function intialize_select2(){       
		$("#sel_borrower").select2({
			minimumInputLength: 2,
			width: '100%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: '/search_member',
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
	function append_penalty(){
		$('#repayment_penalty_body').append($penalty_row)
		animate_element($('.row_penalty').last(),1);
		// animate_element($('tr.row_no_penalty'),2);
		$('tr.row_no_penalty').remove();
		set_penalty_count()
	}

	function set_penalty_count(){
		var count = 0;
		$('tr.row_penalty').each(function(i){
			$(this).find('.col-count').text(i+1);
			count = i+1;
		})
		if(count == 0){
			$('#repayment_penalty_body').html($no_penalty_row)
		}
	}
	function remove_penalty(obj){
		var parent_row = $(obj).closest('tr.row_penalty');
		// animate_element(parent_row,2);
		parent_row.remove();
		set_penalty_count();
		computeAll();
	}
	function parseTotalPenalty(){
		var total_penalty = 0;
		$('tr.row_penalty').each(function(i){
			total_penalty += decode_number_format($(this).find('.txt_penalty_amount').val());
		})
		$('#spn_total_loan_penalty').text(number_format(total_penalty,2));

		return total_penalty;
	}
	function parseTotalFees(){
		var total_fees = 0;
		$('tr.row_fees').each(function(i){
			total_fees += decode_number_format($(this).find('.txt_fee_amount').val());
		})
		$('#spn_total_loan_fees').text(number_format(total_fees,2));
		return total_fees;
	}
	function parseTotalLoanPayment(){
		var total_loan_due_payment = 0;
		$('tr.row_loan_dues').each(function(i){
			total_loan_due_payment += decode_number_format($(this).find('.txt_loan_payment_amount').val());
		})
		$('#txt_col_total_payment').text(number_format(total_loan_due_payment,2));
		return total_loan_due_payment;
	}
	function computeAll(is_fill_dues){
		
		var total_fees = parseTotalFees();
		var total_penalty = parseTotalPenalty();


		var transaction_type = $('#sel_transaction_type').val();

		var total_amount_due = total_fees+total_penalty+total_loan_due;
		$('#spn_total_amount_due').text(number_format(total_amount_due,2));

		var swiping_amount = $('#swiping_amount').val();
		swiping_amount = (swiping_amount == undefined)?0:decode_number_format(swiping_amount);

		console.log({swiping_amount})
		if(swiping_amount >= total_amount_due && is_fill_dues && transaction_type == 2){
			FillDues();
			console.log("_______________________________________________")
		}
		var total_paid_loan_due = parseTotalLoanPayment();
		var total_paid_amount  = total_paid_loan_due+total_penalty+total_fees;
		

		if(transaction_type == 1){
			swiping_amount = total_paid_amount;
		}else{
			swiping_amount = (!$.isNumeric(swiping_amount))?decode_number_format(swiping_amount):swiping_amount;
		}
		
		$('#spn_swiping_amount').text(number_format(parseFloat(swiping_amount),2));
		
		var change = swiping_amount - total_paid_amount;
		var swiping_amount_valid = true;


		if(change < 0){
			if(transaction_type == 2){
				$('#spn_total_paid_amount').addClass('text-red');
			}else{
				$('#spn_total_paid_amount').removeClass('text-red');
			}
			swiping_amount_valid = false;
		}else{
			$('#spn_total_paid_amount').removeClass('text-red');
		}

		console.log({total_paid_amount})

		$('#spn_total_paid_amount').text(number_format(parseFloat(total_paid_amount),2));
		$('#spn_change').text(number_format(parseFloat(change),2));

		return swiping_amount_valid;

		// $('#spn_total_loan_amount_due').
	}
	$(document).on('change','#sel_borrower',function(){
		var val = $(this).val();

		if(val != null){
			// parseMemberLoanDues(val);
			parseDueDates(val);
		}
	});
	$(document).on('change','#txt_date',function(){
		var val = $(this).val();
		if(val != null){
			parseMemberLoanDues(val);

			// parseDueDates(val);
		}
	})
	function reload_member_dues(){
		var id_member = $('#sel_borrower').val();
		if(id_member == null){
			Swal.fire({
				title: "Please Select Borrower",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 1500
			});
			return;
		}
		parseMemberLoanDues(id_member);
	}

	let repayment_date ='<?php echo $repayment_transaction->date ?? null;?>';
	let id_member_holder = '<?php echo $repayment_transaction->id_member ?? 0;?>';



	function display_loan_dues(loan_dues,total_loan_due_,is_load){

		var $loan_dues = loan_dues;
		total_loan_due = parseFloat(total_loan_due_);
		var $total_current = 0;
		var $total_previous = 0;
		var out = "";
		$.each($loan_dues,function($c,$dues){
			$.each($dues,function($k,$due){
				var loan_status =($due.loan_status == '')?'':'&nbsp;&nbsp;&nbsp;<span class="badge badge-danger">Closed</span>';
				var $class= ($c >0 && $k == 0)?"border-top":"";
				out += '<tr class="'+$class+' row_loan_dues">';
				out += '	<td class="tbl-inputs-text">'+$due.loan_service+(($due.type == "previous")?' <small>('+$due.service_remarks+')</small>':'')+loan_status+'</td>';
				out +=  '	<td class="tbl-inputs-text class_amount">'+(number_format(parseFloat($due.amount),2))+'</td>';
				out +=  '	<td><input type="text" name="" required class="form-control class_amount frm_loans txt_loan_payment_amount" key="amount" value="'+number_format(parseFloat($due.paid_amount),2)+'" table-reference="'+$due.id_loan_table+'" loan-token="'+$due.loan_token+'" payment-type="'+$due.type+'" due="'+$due.amount+'" amount-type="'+$due.amount_type+'"></td>';
				out += '</tr>'

				if($due.type == "current"){
					$total_current += parseFloat($due.amount);
				}else{
					$total_previous += parseFloat($due.amount);
				}
			})
		})
		
		$('#txt_total_loan_due').text(number_format(parseFloat(total_loan_due),2));
		$('#spn_total_loan_amount_due').text(number_format(parseFloat(total_loan_due),2));

		$('#spn_current_balance').text(number_format($total_current,2));
		$('#spn_previous_balance').text(number_format($total_previous,2));
		$('#loan_dues_body').html(out)
		console.log({$total_current,$total_previous})



		
		$('#loan_dues_body').html(out)
		if('<?php echo $js_allow_post;?>' == 0){
			$('#repayment_main_div').find('input,select').attr('disabled',true)
		}


		var autofill = (is_load==1)?false:true;
		computeAll(autofill);
		// alert(123);
	}
	$(document).on('keyup','.txt_fee_amount,.txt_penalty_amount,.txt_loan_payment_amount',function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			$(this).val(0);
		}
		computeAll();
	});

	$(document).on('keyup','#swiping_amount',function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			$(this).val(0);
		}
		computeAll(true);

	});   
	$(document).on('keyup','.txt_loan_payment_amount',function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			$(this).val(0);
		}    	
	})
	function FillDues(){
		$('.txt_loan_payment_amount').each(function(){
			$(this).val(number_format(parseFloat($(this).attr('due')),2))
		})
		parseTotalLoanPayment();
	}
	function fill_zero(){
		$('.txt_loan_payment_amount').val("0.00");
		computeAll();
	}

	$(document).on('dblclick','tr.row_loan_dues',function(){
		var parent_row = $(this);
		parent_row.find('.txt_loan_payment_amount').val(number_format(parseFloat(parent_row.find('.txt_loan_payment_amount').attr('due')),2));
		computeAll();
	})

	$(document).on('change','#sel_transaction_type',function(){
		var val = $(this).val();
		initialize_transaction_type()
		console.log({val})
	})

	function initialize_transaction_type(){
		var transaction_type = $('#sel_transaction_type').val();
		var swiping_fee = 0;
		var disable_swiping = false;

		if(transaction_type == 1){
			$('#div_swiping_amount_holder').html('');
			$('.row_swiping_amount').hide();
			$('#spn_total_paid_amount').removeClass('text-red');
			disable_swiping = true;

		}else{
			swiping_fee = 25;
			$('#div_swiping_amount_holder').html($div_swiping_amount);
			animate_element($('#div_swiping_amount_holder'),1);
			// $('.row_swiping_amount').show();
			animate_element($('.row_swiping_amount'),1);
			computeAll();
		}

		$('tr.row_fees[data-id="18"]').find('.txt_fee_amount').val(number_format(swiping_fee,2));
		$('tr.row_fees[data-id="18"]').find('.txt_fee_amount').attr('disabled',disable_swiping);
	}


</script>
<!-- FOR OR PRINTING -->
<script type="text/javascript">

</script>
@if($opcode == 1)
<script type="text/javascript">
	const loan_dues = jQuery.parseJSON('<?php echo json_encode($loan_dues ?? [])?>');
	const repayment_penalties = jQuery.parseJSON('<?php echo json_encode($repayment_penalty ?? [])?>');
	const repayment_fee_val = jQuery.parseJSON('<?php echo json_encode($repayment_fee_val ?? [])?>');


	$('#sel_borrower,#txt_date').attr('disabled',true);
	$(document).ready(function(){


 //   	var redirect_data = {
	// 	'show_print_or' : 1,
	// 	'repayment_token' : response.REPAYMENT_TOKEN
	// }
	// localStorage.setItem("redirect_print_or",JSON.stringify(redirect_data));

	var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_print_or"));
	console.log({redirect_data});
	if(redirect_data != null){
		if(redirect_data.show_print_or == 1){
			if(redirect_data.repayment_token == '<?php echo $repayment_transaction->repayment_token; ?>'){
				print_or();
					// alert(123);
					localStorage.removeItem("redirect_print_or");
				}
			}
		}
		// alert(123);

		display_loan_dues(loan_dues,'<?php echo $total_loan_due;?>',1);
		//Initialize Loan Fees
		$.each(repayment_fee_val,function(i,item){


			$('tr[data-id="'+item.id_payment_type+'"]').find('.txt_fee_amount').val(number_format(parseFloat(item.amount ?? 0),2))
		})
		$('#repayment_penalty_body').html('')

		//Initialize penalties
		$.each(repayment_penalties,function(i,item){
			$('#repayment_penalty_body').append($penalty_row);
			$('.sel_penalty').last().val(item.id_payment_type);
			$('.txt_penalty_amount').last().val(number_format(parseFloat(item.amount),2))
			set_penalty_count()
		})

		if(repayment_penalties.length == 0){
			$('#repayment_penalty_body').html($no_penalty_row);
		}

		$('#btn_save_repayment').attr("disabled",false)
		computeAll(false);
	})
	console.log({loan_dues})
</script>
@endif


@if($opcode == 0)
<script type="text/javascript">
	function parseMemberLoanDues($date){
		$.ajax({
			type          :         'GET',
			url           :         '/repayment/member/loan_dues',
			data          :          {'id_member':id_member_holder,
			'date' : $date},
			beforeSend    :          function(){
				show_loader();
			},
			success       :          function(response){
				hide_loader();
				console.log({response});
				if(response.is_valid_loan){
					repayment_date = response.repayment_date;
					id_member_holder = response.member_info.id_member;

				}else{
					repayment_date = "";
					id_member_holder = 0;

					Swal.fire({
						title: "No Active Loan(s) Found",
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});
				}
				$('#btn_save_repayment').attr("disabled",!response.is_valid_loan)

				display_loan_dues(response.loan_dues,response.total_loan_due);

				// computeAll(true);
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

	function parseDueDates($id_member){
		$.ajax({
			type        :     'GET',
			url         :     'repayment/get_due_dates/',
			data        :     {'id_member' : $id_member},
			beforeSend    :          function(){
				$('#spn_current_balance').text(number_format(0,2));
				$('#spn_previous_balance').text(number_format(0,2));
				$('#loan_dues_body').html('')	
				display_loan_dues([],0)
				computeAll();
				show_loader();
			},
			success     :     function(response){
				var out = '';
				hide_loader();
				if(response.dues.length > 0){
					id_member_holder = $id_member;
					$.each(response.dues,function(i,item){
						out += '<option value="'+item.due_date+'">'+item.due_date_text+'</option>';
					})
				}else{
					id_member_holder = 0;

					Swal.fire({
						title: "No Loan Dues Found",
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});			

				}
				$('#txt_date').html(out)
				$('#txt_date').val(0)
				console.log({response});
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
@endif

@if($allow_post)
<script type="text/javascript">
	function post(){
		var current_payments = {};
		var previous_payments = {};
		var amount_valid = true;
		$('.txt_loan_payment_amount').removeClass('mandatory');
		$('.txt_loan_payment_amount').each(function(){
			var payment_type = $(this).attr('payment-type');
			var token = $(this).attr('loan-token');
			var amount_type = $(this).attr('amount-type');
			var due = $(this).attr("due");

			if(decode_number_format($(this).val()) > parseFloat(due)){
				amount_valid = false;
				$(this).addClass('mandatory');
			}

			if(payment_type == "previous"){
				try{
					previous_payments[token][amount_type] = decode_number_format($(this).val());
				}catch(e){
					previous_payments[token] = {};
					previous_payments[token][amount_type] = decode_number_format($(this).val());
				}	
			}else if(payment_type == "current"){
				try{
					current_payments[token][amount_type] = decode_number_format($(this).val());
				}catch(e){
					current_payments[token] = {};
					current_payments[token][amount_type] = decode_number_format($(this).val());
				}	
			}
		})

		if(!amount_valid){
			Swal.fire({
				title: "Paid Amount must not be greater than dues",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 2500
			});
			return;
		}
		var is_swiping_amount_valid = computeAll();
		if(!is_swiping_amount_valid){
			Swal.fire({
				title: "Swiping Amount must be greater than or equal to total payment",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 2500
			});

			return;
		}	
    	//Penalties
    	var penalties = [];
    	$('.row_penalty').each(function(){
    		var temp = {};
    		temp['id_payment_type'] = $(this).find('.sel_penalty').val();
    		temp['amount'] = decode_number_format($(this).find('.txt_penalty_amount').val());
    		penalties.push(temp);
    	})

    	//fees
    	var fees = [];
    	$('.row_fees').each(function(){
    		var id = $(this).attr('data-id');
    		fees[id] = decode_number_format($(this).find('.txt_fee_amount').val());
    	})
    	var sp = ($('#sel_transaction_type').val() == 1)?0: decode_number_format($('#swiping_amount').val())

    	$.ajax({
    		type        :          "GET",
    		url         :          "/test/post",
    		data        :          {'transaction_type' : $('#sel_transaction_type').val(),
    		'transaction_date' : $('#txt_transaction_date').val(),
    		'id_bank' : $('#sel_bank').val(),
    		'opcode' : '<?php echo $opcode?>',
    		'current_payments' : current_payments,
    		'previous_payments' : previous_payments,
    		'repayment_date' : repayment_date,
    		'id_member' : id_member_holder,
    		'swiping_amount' :sp,
    		'fees' : fees,
    		'penalties' : penalties,
    		'repayment_token' : '<?php echo $repayment_transaction->repayment_token ?? '' ?>',
    		'remarks' : $('#txt_remarks').val()},
    		beforeSend    :          function(){
    			show_loader();
    		},
    		success     :          function(response){
    			console.log({response});
    			hide_loader()
    			if(response.RESPONSE_CODE == "SUCCESS"){
    				var link = "/repayment/view/"+response.REPAYMENT_TOKEN+"?href="+encodeURIComponent('<?php echo $back_link;?>');
    				Swal.fire({
    					title: "Loan Payment Successfully Saved !",
    					html : "<a href='"+link+"'>Loan Payment ID# "+response.ID_REPAYMENT_TRANSACTION+"</a>",
    					text: '',
    					icon: 'success',
    					showCancelButton : true,
    					confirmButtonText: 'Create More Loan Payment',
    					showDenyButton: true,
    					denyButtonText: `Print OR`,
    					cancelButtonText: 'Close',
    					showConfirmButton : true,     
    					allowEscapeKey : false,
    					allowOutsideClick: false
    				}).then((result) => {
    					if(result.isConfirmed) {
    						window.location = "/repayment/create?href="+encodeURIComponent('<?php echo $back_link;?>');
    					}else if (result.isDenied) {
    						var redirect_data = {
    							'show_print_or' : 1,
    							'repayment_token' : response.REPAYMENT_TOKEN
    						}
    						localStorage.setItem("redirect_print_or",JSON.stringify(redirect_data));
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
    	console.log({current_payments});
    }

    function save_repayment(){
    	var id_member = $('#sel_borrower').val();
    	if(id_member == null){
    		Swal.fire({
    			title: "Please Select Borrower",
    			text: '',
    			icon: 'warning',
    			showCancelButton : false,
    			showConfirmButton : false,
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
    			post();
    		} 
    	})	
    	// alert(123);
    }


</script>
@endif
<script type="text/javascript">
	function update_or(){
		$or_opcode = 1;
		$('#or_modal').modal('show')
	}
</script>

@if(!$allow_post)
<script type="text/javascript">
	$('#repayment_main_div').find('input,select').attr('disabled',true)
</script>
@endif



@endpush
