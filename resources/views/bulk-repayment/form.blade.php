@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.tbl_pdc th, .tbl_pdc td{
		padding: 0.2rem;
		font-size: 0.85rem;
	}
	.tbl_pdc td.in{
		padding: 0;
	}
	td.in input{
		height: 27px !important;
		font-size: 0.8rem;
	}
	.tbl_pdc th{
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
</style>
<div class="container-fluid main_form" style="margin-top: -20px;">
	<?php
	$paymentModes=array(
		4=>'Check',1=>'Cash'
	);
	$check_types = [1=>"On-date",2=>"Post dated"];

	?>
	<?php $back_link = (request()->get('href') == '')?'/repayment-bulk':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment Bulk List</a>


	<div class="card">
		<div class="card-body px-5">
			<div class="text-center mb-5">
				<h4 class="head_lbl">Loan Payment Bulk</h4>
			</div>
			<form>
				<div class="row d-flex align-items-end mt-3">
					<div class="form-group col-md-4">
						<label class="lbl_color mb-0">Date</label>
						<input type="date" name="date" class="form-control form-control-border" value="{{$date}}" <?php echo ($opcode == 1)?'disabled':''; ?>>
					</div>
					<div class="form-group col-md-4">
						<label class="lbl_color mb-0">Barangay/LGU</label>
						<select class="form-control form-control-border p-0" name="br" <?php echo ($opcode == 1)?'disabled':''; ?> >
							@foreach($branches as $branch_n=>$branch)
							<optgroup label="{{$branch_n}}">
								@foreach($branch as $br)
								<option value="{{$br->id_baranggay_lgu}}" <?php echo ($br->id_baranggay_lgu == $selected_branch)?'selected':''; ?> >{{$br->name}}</option>
								@endforeach
							</optgroup>
							@endforeach
						</select>
					</div>

					@if($opcode == 0)
					<div class="form-group col-md-3">
						<button class="btn bg-gradient-primary2 btn-md"><i class="fa fa-search"></i>&nbsp;Search</button>
					</div>
					@endif
				</div>
			</form>
			<div class="row">
				<div class="col-md-12">
					<button class="btn btn-sm round_button bg-gradient-info" onclick="SelectMember()"><i class="fa fa-users"></i>&nbsp;Select Member</button>
				</div>
				<div class="col-md-12">
					<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
						<thead>
							<tr class="text-center">
								<th>Member</th>
								<th>Loan Service</th>
								<th>Balance</th>
								<th>Current Due</th>
								<th>Loan Payment</th>
							</tr>
						</thead>

						<tr id='r-no-member'>
							<td class="text-center" colspan="5">Select at least 1 member</td>
						</tr>
						<footer>
							<tr>
								<th colspan="4" class="footer_fix text-left font-weight-normal" style="text-align:center;background: #808080 !important;color: white;"></th>
								<th class="footer_fix text-right td-total-payment"  style="background: #808080 !important;color: white;">{{number_format(0,2)}}</th>
								
								
							</tr>
						</footer>
					</table>
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
							<input type="text" class="form-control form-control-border frm-paymode txt-input-amount text-right" name="amount" value="{{number_format($details->amount ?? 0,2)}}">
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
		<div class="card-footer">
			<button class="btn bg-gradient-success2 round_button float-right" onclick="post()"><i class="fa fa-save"></i>&nbsp;Save</button>
		</div>
	</div>

</div>
@include('bulk-repayment.member_modal')
@endsection


@push('scripts')
<script type="text/javascript">
	const BACK_LINK = `<?php echo $back_link; ?>`;
	const OPCODE = '{{$opcode}}';
	const ID_BARANGGAY_LGU = {{$selected_branch}};
	const DATE = '{{$date}}';
	const ID_REPAYMENT = '{{$details->id_repayment ?? 0}}';
	const CheckDiv = $('#div_check div').detach();
	const REP_REFERENCE = jQuery.parseJSON('<?php echo json_encode($rt_reference ?? []); ?>');

	const SelectMember=()=>{
		$('#modal-employee').modal('show');
	}
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


		ComputeAll();
	});

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
	$(document).ready(function(){
		init_paymode();
		
	})	
	$(document).on('change','#sel-paymode',function(){
		init_paymode();
	})
	const ComputeAll=()=>{
		let TotalPayment = 0;
		$('.in-loan-payment').each(function(){
			var p = $(this).val();
			let payment = ($.isNumeric(p))?roundoff(p):decode_number_format(p);
			TotalPayment += payment;
		});
		// $('#txt-total-payment').val(number_format(TotalPayment));
		$('.td-total-payment').text(number_format(TotalPayment));
	}
</script>

<script type="text/javascript">
	function post(){
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
				post_repayment();
			}
		});
	}
	function post_repayment(){
		let LoanOut = [];
		$('tbody.bmember').each(function(id_member,loans){
			member_id = $(this).attr('data-member-id');
			var temp_loan_out = {};
			tout = {};
			$(this).find('tr.rloan').each(function(){
				var temp = {};
				temp['loan_payment'] = decode_number_format($(this).find('input.in-loan-payment').val());
		

				token_ = $(this).attr('data-loan');
				tout[token_] = temp;
			});
			temp_loan_out['id_member'] = member_id;
			temp_loan_out['loan_payment'] = tout;
			temp_loan_out['cbu'] = 0;
			temp_loan_out['id_repayment_transaction'] = $(this).attr('repayment-id');
			LoanOut.push(temp_loan_out);
		});
		let paymode = {};
		$('.frm-paymode').each(function(){
			let key = $(this).attr('name');
			paymode[key] = $(this).val()
		});

		paymode['amount'] = decode_number_format(paymode['amount']);
        // let paymode = {
        //     'check_type' : 1,
        //     'check_bank' : "BPI CHECK",
        //     'bank' : 1,
        //     'check_date' : '2024-11-10',
        //     'check_no' : '12345',
        //     'paymode' : 4,
        //     'transaction_date' : '2024-11-10',
        //     'remarks' : "TEST",
        //     'or_number' : 123456

        // };

		let ajaxParam = {
			'loans' : LoanOut,
			'opcode' : OPCODE,
			'paymode' : paymode,
			'id_repayment' : ID_REPAYMENT,
			'br' : ID_BARANGGAY_LGU,
			'date' : DATE
		};



		$.ajax({
			type       :     'GET',
			url        :     '/repayment-bulk/post',
			data       :      ajaxParam,
			beforeSend  :  function(){
				show_loader();
				$('.in-loan-payment.text-danger').removeClass('text-danger');
				$('.mandatory').removeClass('mandatory');
			},
			success    :      function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: 'Loan Payment Successfully saved',
						icon: 'success',
						showCancelButton: false,
						showConfirmButton : false,
						cancelButtonText : 'Back to Loan Payment list',
						confirmButtonText: `Close`,
						allowOutsideClick: false,
						allowEscapeKey: false,
						timer : 2500
					}).then((result) => {
						// location.reload();
						window.location ='/repayment-bulk/view/'+response.ID_REPAYMENT+"?href="+encodeURIComponent(BACK_LINK);
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
						$(`tr.rloan[data-loan="${val}"]`).find('.in-loan-payment').addClass('text-danger');
					}) 					   	
				}
			},error: function(xhr, status, error) {
                hide_loader()
                var errorMessage = xhr.status + ': ' + xhr.statusText
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

@if($opcode == 1)
<script type="text/javascript">
	const MEMBERS_SELECTED = jQuery.parseJSON(`<?php echo json_encode($selected_members ?? []);?>`);

	$.each(MEMBERS_SELECTED,function(i,val){
		$(`tr.row-member[data-id="${val}"]`).find('.chk-member').prop('checked',true);
	});
	ChooseMember();
</script>
@endif

@endpush