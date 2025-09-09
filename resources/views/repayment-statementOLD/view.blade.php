@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.tbl_loan th, .tbl_loan td{
		padding: 0.2rem;
		font-size: 0.85rem;
	}
	.tbl_loan td.in{
		padding: 0;
	}
	td.in input{
		height: 27px !important;
		font-size: 0.8rem;
	}
	.tbl_loan th{
		padding: 0.4rem;
		font-size: 0.8rem;
	}
	.selected_rp{
		background: #ccffcc;
	}
	.borders{
		border-top: 3px solid gray!important;
		border-bottom: 3px solid gray!important;
	}
	td input.txt-input-amount{
		border: 2px solid !important;
	}
	.nowrap{
		white-space: nowrap;
	}
	.tbl_loan tfoot td {
		background: #666;
		color: white;
	}
</style>
<?php
$paymentModes=array(
	4=>'Check',1=>'Cash'
);
$check_types = [1=>"On-date",2=>"Post dated"];

?>
<div class="container main_form" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/repayment-statement':request()->get('href'); ?>

	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment Statement List</a>



	@if($allow_post)
	<button class="btn btn-warning btn-sm round_button float-right" onclick="show_status_modal()"><i class="fa fa-times"></i>&nbsp;Cancel Loan Payment Statement</button>

	@endif



	@if($details->status == 0)
	<a class="btn btn-primary btn-sm round_button float-right mr-2" href="/repayment-statement/edit/{{$details->id_repayment_statement}}?href={{$back_link}}"><i class="fa fa-print"></i>&nbsp;Edit Statement</a>
	<button class="btn btn-danger btn-sm round_button float-right mr-2" onclick="print_page('/repayment-statement/print/generated/{{$details->id_repayment_statement}}')"><i class="fa fa-print"></i>&nbsp;Print Statement</button>
	@elseif($details->status == 1)
	<button class="btn btn-danger btn-sm round_button float-right mr-2" onclick="print_page('/repayment-statement/print/remitted/{{$details->id_repayment_statement}}')"><i class="fa fa-print"></i>&nbsp;Print Remitted Statement</button>
	@endif


<!-- 	<div class="btn-group float-right">
		<button type="button" class="btn btn-sm  bg-gradient-danger2 dropdown-toggle mr-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i>&nbsp;
			Print
		</button>
		<div class="dropdown-menu">
			<a class="dropdown-item" href="javascript:void(0)" onclick="print_page('/repayment-statement/print/generated/{{$details->id_repayment_statement}}')">Print Statement</a>
			if($details->status == 1)
			<a class="dropdown-item" onclick="print_page('/repayment-statement/print/remitted/{{$details->id_repayment_statement}}')">Print Remitted Statement</a>
			endif
		</div>
	</div> -->
	<div class="card">
		<div class="card-body px-5 py-4">
			<div class="text-center mb-3">
				<h4 class="head_lbl">Loan Payment Statement @if($details->status > 0)<span class="badge bg-gradient-{{$details->status_class}}2 text-md">{{$details->status_description}}</span> @endif</h4>
			</div>
			<div class="card c-border">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">No.: {{$details->month_year}}-{{$details->id_repayment_statement}}</span>
								<span class="text-sm  font-weight-bold lbl_color">{{$details->group_}}: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->baranggay_lgu}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Treasurer: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->treasurer}}</span></span>
							</div>		
						</div>
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">

								<span class="text-sm  font-weight-bold lbl_color">Statement Date: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->statement_date}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Month Due: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->month_due}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Total Amount: <span class="ms-sm-2 font-weight-normal ml-2" id="amount_due">0.00</span></span>
							</div>		
						</div>
					</div>
					@if($details->status == 10)
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">Cancellation Reason: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->status_remarks}} <i>({{$details->status_date}})</i></span></span>
							</div>
						</div>
					</div>
					@endif
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-md-12">
					<?php
					$GLOBALS['total'] = 0;
					?>
					<div class="table">
						<table class="table table-bordered table-head-fixed tbl_loan w-100" id="tbl_loan">
							<thead>
								<tr class="text-center">
									<th>BORROWER'S NAME</th>
									<th>LOAN TYPE</th>
									<th>AMOUNT</th>
									<th>PAYMENT</th>
									@if($details->status > 0)
									<th>CRV</th>
									@endif
								</tr>
							</thead>
							@foreach($loans as $id_member=>$loan)
							<tbody class="borders bmember" data-member-id="{{$id_member}}">
								@foreach($loan as $c=>$lo)
								<tr class="rloan" data-loan="{{$lo->loan_token}}" data-id="{{$lo->id_loan}}" loan-due="{{$lo->current_due}}" rp-id="{{$lo->id_repayment_statement_details}}">
									@if($c == 0)
									<td class="font-weight-bold nowrap" rowspan="{{count($loan)}}"><i>{{$lo->member}}</i></td>
									@endif
									<td class="nowrap"><sup><a href="/loan/application/approval/{{$lo->loan_token}}" target="_blank">[{{$lo->id_loan}}] </a></sup>{{$lo->loan_name}}</td>
									<td class="text-right ">{{number_format($lo->current_due,2)}}</td>
									<?php $GLOBALS['total'] += $lo->current_due; ?> 
									<td class="in"><input class="form-control p-2 text-right txt-input-amount in-payment" value="{{number_format($lo->amount_paid,2)}}"></td>
									@if($details->status > 0)
									@if($c == 0)
									<td class="p-0" rowspan="{{count($loan)}}" style="vertical-align: middle;">
										@if(isset($lo->id_cash_receipt_voucher))
										<button class="btn btn-xs bg-gradient-danger2 w-100" onclick="print_page('/cash_receipt_voucher/print/{{$lo->id_cash_receipt_voucher}}')">CRV# {{$lo->id_cash_receipt_voucher}}</button>
										@endif
									</td>
									@endif
									@endif
								</tr>
								@endforeach
							</tbody>
							@endforeach
							<tfoot>
								<td colspan="2">Grand Total</td>
								<td class="text-right">{{number_format($GLOBALS['total'],2)}}</td>
								<td class="text-right td-total-payment">0.00</td>
								@if($details->status >0)
								<td></td>
								@endif
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="card c-border h-100">
				<div class="card-body px-5">
					<h5 class="lbl_color">Payment Details</h5>
					<div class="form-row mt-4">
						<div class="form-group col-md-4">
							<label class="lbl_color text-sm mb-0">Date Received</label>
							<input type="date" class="form-control form-control-border frm-paymode" value="{{$details->date_received ?? MySession::current_date()}}" name="date_received">
						</div>
						<div class="form-group col-md-4">
							<label class="lbl_color text-sm mb-0">OR No.</label>
							<input type="text" class="form-control form-control-border frm-paymode" value="{{$details->or_number ?? ''}}" name="or_number">
						</div>						
						<div class="form-group col-md-4">
							<label class="lbl_color text-sm mb-0">Payment Mode</label>
							<select class="form-control form-control-border frm-paymode" id="sel-paymode" name="paymode">
								@foreach($paymentModes as $val=>$desc)
								<option value="{{$val}}" <?php echo (($details->id_paymode ?? 4) == $val)?'selected':''; ?> >{{$desc}}</option>
								@endforeach
							</select>
						</div>

					</div>
					<div class="row" id="div_check">
						<div class="form-group col-md-4 col-12">
							<label class="lbl_color mb-0 text-sm">Check Type</label>
							<select class="form-control form-control-border p-0 frm-paymode" name="check_type">
								@foreach($check_types as $val=>$desc)
								<option value="{{$val}}" <?php echo (($details->id_check_type ?? 1) == $val)?'selected':'';  ?> >{{$desc}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-8 col-12">
							<label class="lbl_color mb-0 text-sm">Check Bank</label>
							<input type="text" class="form-control form-control-border frm-paymode" name="check_bank" value="{{$details->check_bank ?? ''}}">
							
						</div>
						<div class="form-group col-md-4 col-12">
							<label class="lbl_color mb-0 text-sm">Check Date</label>
							<input type="date" class="form-control form-control-border frm-paymode" name="check_date" value="{{$details->check_date ?? MySession::current_date()}}">
						</div>
						<div class="form-group col-md-4 col-12">
							<label class="lbl_color mb-0 text-sm">Check No.</label>
							<input type="text" class="form-control form-control-border frm-paymode" name="check_no" value="{{$details->check_no ?? ''}}">
						</div>
						<div class="form-group col-md-4 col-12">
							<label class="lbl_color mb-0 text-sm">Check Amount</label>
							<input type="text" class="form-control form-control-border frm-paymode txt-input-amount text-right" name="check_amount" value="{{number_format($details->check_amount ?? $GLOBALS['total'],2)}}">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-12">
							<label class="lbl_color mb-0 text-sm">Remarks</label>
							<input type="text" class="form-control form-control-border frm-paymode" value="{{$details->remarks ?? ''}}" name="remarks">
						</div>
					</div>
				</div>
			</div>
		</div>

		@if($allow_post)
		<?php
		$saveText = ($details->status == 0)?"Save Payment":"Update Payment";
		?>
		<div class="card-footer py-2">
			<button class="btn round_button bg-gradient-success2 float-right" onclick="post()"><i class="fa fa-save"></i>&nbsp;{{$saveText}}</button>
			

		</div>
		@endif
	</div>
</div>


@include('global.print_modal')
@if($allow_post)

@include('repayment-statement.status_modal')
@endif
@endsection


@push('scripts')


<script type="text/javascript">
	const BACK_LINK = `<?php echo $back_link; ?>`;
	const CheckDiv = $('#div_check div').detach();
	const ID_REPAYMENT_STATEMENT = {{$details->id_repayment_statement ?? 0}};
	$(document).ready(function(){
		init_paymode();
		$('#amount_due').text(number_format('{{$GLOBALS["total"]}}'));
		ComputeAll();
	})	
	const init_paymode=()=>{
		var val = $('#sel-paymode').val();
		if(val == 1){
			//Cash
			$('#div_check').html('');
		}else{
			//Check
			$('#div_check').html(CheckDiv);
		}
	}
	const ComputeAll=()=>{
		let TotalPayment = 0;
		$('.in-payment').each(function(){
			var p = $(this).val();
			let payment = ($.isNumeric(p))?roundoff(p):decode_number_format(p);
			TotalPayment += payment;
		});
		$('#txt-total-payment').val(number_format(TotalPayment));
		$('.td-total-payment').text(number_format(TotalPayment));
	}
</script>

@if($allow_post)
<script type="text/javascript">

	$(document).on("focus",".txt-input-amount",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';
		}
		$(this).val(decode_number_format(val));	
	});

	$(document).on("blur",".txt-input-amount",function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			var decoded_val = decode_number_format(val);
			val = (!isNaN(decoded_val))?decoded_val:0;
		}
		$(this).val(number_format(parseFloat(val)));
	});
	$(document).on('change','#sel-paymode',function(){
		init_paymode();
	})

	$(document).on('keyup','.in-payment',function(){
		ComputeAll();

		

	});

</script>

<script type="text/javascript">
	const post = ()=>{
		Swal.fire({
			title: 'Are you sure you want to save this ?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Yes`,
			allowOutsideClick: false,
			allowEscapeKey: false,
		}).then((result) => {
			if (result.isConfirmed) {
				postPayment();
			}
		});
	}
	const postPayment = ()=>{
		let StatementPayment = [];
		$('tr.rloan').each(function(){
			var rid = $(this).attr('rp-id');
			var temp = {};
			temp['id_repayment_statement'] = rid;
			var p = $(this).find('.in-payment').val();
			let paymentRow = ($.isNumeric(p))?roundoff(p):decode_number_format(p);
			temp['amount_paid'] = paymentRow;

			StatementPayment.push(temp);
		});

		let paymode = {};
		$('.frm-paymode').each(function(){
			let key = $(this).attr('name');
			paymode[key] = $(this).val()
		});

		paymode['check_amount'] = decode_number_format(paymode['check_amount']);

		let AjaxParam = {
			'loans' : StatementPayment,
			'ID_REPAYMENT_STATEMENT' : ID_REPAYMENT_STATEMENT,
			'paymode' : paymode
		};

		$.ajax({
			type          :     'GET',
			url           :     '/repayment-statement/post-amount',
			data          :    	AjaxParam,
			beforeSend    :     function(){
				show_loader();
				$('.in-payment.text-danger').removeClass('text-danger');
			},
			success       :     function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: response.message,
						icon: 'success',
						showCancelButton: false,
						showConfirmButton : false,
						cancelButtonText : 'Back to Loan Payment Statement list',
						confirmButtonText: `Close`,
						allowOutsideClick: false,
						allowEscapeKey: false,
						timer : 2500
					}).then((result) => {
										// location.reload();
						window.localStorage.setItem('for_print',response.ID_REPAYMENT_STATEMENT);
						window.location ='/repayment-statement/view/'+response.ID_REPAYMENT_STATEMENT+"?href="+encodeURIComponent(BACK_LINK);

					})
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});	

					let invalid_fields = response.invalid_fields ?? [];

					for(var i=0;i<invalid_fields.length;i++){
						$(`.frm-paymode[name="${invalid_fields[i]}"]`).addClass('mandatory');
					}

					let invalidLoans = response.loanError ?? [];
					$.each(invalidLoans,function(i,val){
						$(`tr.rloan[data-id="${val}"]`).find('.in-payment').addClass('text-danger');
					}) 					   	
				}
			},error: function(xhr, status, error){
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
</script>
@else
<script type="text/javascript">
	$(document).ready(function(){
		$('.frm-paymode,.in-payment').prop('disabled',true);
	})
	
</script>
@endif


@if($details->status == 0)
<script type="text/javascript">
	$(document).ready(function(){
		if(window.localStorage.getItem("for_print") == ID_REPAYMENT_STATEMENT){
			window.localStorage.removeItem("for_print");
			print_page(`/repayment-statement/print/generated/${ID_REPAYMENT_STATEMENT}`);
		}
	})
</script>
@elseif($details->status == 1)
<script type="text/javascript">
	$(document).ready(function(){
	
		if(window.localStorage.getItem("for_print") == ID_REPAYMENT_STATEMENT){
			print_page(`/repayment-statement/print/remitted/${ID_REPAYMENT_STATEMENT}`);
			window.localStorage.removeItem("for_print");
			
		}
	})
</script>
@endif

@endpush