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
		border-top: 3px solid !important;
		border-bottom: 3px solid !important;
	}
	td input.txt-input-amount{
		border: 2px solid !important;
	}
	.nowrap{
		white-space: nowrap;
	}
</style>
<?php
$rebatesOBJ = array();
$paymentModes=array(
	4=>'Check',1=>'Cash'
);
$check_types = [1=>"On-date",2=>"Post dated"];
?>
<div class="container-fluid">
	<?php $back_link = (request()->get('href') == '')?'/bulk-repayment':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment (Bulk) List</a>
	<div class="row">
		<div class="col-md-12 col-12">
			<div class="card">
				<div class="card-body">
					<div class="text-center">
						<h4 class="head_lbl">Repayment</h4>
					</div>
					<form>
						<div class="row d-flex align-items-end mt-3">
							<div class="form-group col-md-4">
								@if($opcode == 0)
								<label class="lbl_color mb-0">Barangay/LGU</label>
								@else
								<label class="lbl_color mb-0">@if($details->br_type == 1)Barangay @else LGU @endif</label>
								@endif
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
							<div class="form-group col-md-2">
								<button class="btn bg-gradient-primary2 btn-md">Generate</button>
							</div>
							@endif
						</div>
					</form>

					<h5 class="lbl_color">Loan Payment</h5>
					<div class="table-responsive mt-2">
						<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
							<thead>
								<tr class="text-center">
									<th>Member</th>
									<th>Loan Service</th>
									<th>Balance</th>
									<th>Current Due</th>
									<th>Loan Payment</th>
									<th>Penalty</th>
									<th>Rebates</th>
									<th>Total Payment</th>
								</tr>
							</thead>
							@foreach($loans as $id_member=>$loan)
							<tbody class="borders bmember" data-member-id="{{$id_member}}" lc="{{count($loan)}}" repayment-id="{{$rt_reference[$id_member] ?? 0}}">
								@foreach($loan as $c=>$lo)
								<tr class="rloan" data-loan="{{$lo->loan_token}}">
									@if($c == 0)
									<td class="font-weight-bold nowrap" rowspan="{{count($loan)}}"><i>{{$lo->member}}</i></td>
									@endif
									<td class="nowrap"><sup><a href="/loan/application/approval/{{$lo->loan_token}}" target="_blank">[{{$lo->id_loan}}] </a></sup>{{$lo->loan_name}}</td>
									<td class="text-right">{{number_format($lo->balance,2)}}</td>
									<td class="text-right">{{number_format($lo->current_due,2)}}</td>
									<td class="in"><input class="form-control p-2 text-right txt-input-amount in-loan-payment" value="{{number_format($lo->payment,2)}}"></td>
									<td class="in"><input class="form-control p-2 text-right txt-input-amount in-penalty" value="{{number_format($lo->penalty,2)}}"></td>
									<td class="in"><input class="form-control p-2 text-right in-rebates" value="0.00" disabled></td>
									<td class="in"><input class="form-control p-2 text-right in-total" value="0.00" disabled></td>
								</tr>

								<?php
								$rebatesOBJ[$lo->loan_token] = [
									'balance'=>floatval($lo->balance),
									'rebates'=>floatval($lo->rebates)
								];
								?>
								@endforeach
							</tbody>
							@endforeach
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-7 col-12">
			<div class="card h-100">
				<div class="card-body">
					<div class="text-center">
						<h4 class="head_lbl">Payment Summary</h4>
					</div>
					<div class="table-responsive mt-5">
						<table class="table table-bordered table-head-fixed tbl_pdc w-100">
							<thead>
								<tr class="text-center">
									<th width="35%">Member</th>
									<th>CBU</th>
									<th>Loan Payment</th>
									
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								@foreach($loans as $id_member=>$loan)
								<tr class="rmember" data-id="{{$id_member}}">
									<td class="font-weight-bold"><i>{{$loan[0]->member}}</i></td>
									<td class="in"><input class="form-control p-2 text-right txt-input-amount in-cbu" value="{{number_format($cbus[$id_member] ?? $def_cbu,2)}}"></td>
									<td class="in"><input class="form-control p-2 text-right in-total-loan" disabled value="0.00"></td>
									
									<td class="in"><input class="form-control p-2 text-right mem-total" disabled value="0.00"></td>
								</tr>
								@endforeach
							</tbody>
							<tfoot>
								<tr >
									<td class="font-weight-bold text-md" colspan="3">Grand Total</td>
									<td id="td-gtotal" class="text-right font-weight-bold text-md pr-2"></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>	
		<div class="col-md-5 col-12">	
			<div class="card h-100">
				<div class="card-body">
					<div class="text-center">
						<h4 class="head_lbl">Payment Details</h4>
					</div>
					<div class="form-row mt-4">
						<div class="form-group col-md-4">
							<label class="lbl_color text-sm mb-0">Transaction Date</label>
							<input type="date" class="form-control form-control-border frm-paymode" value="{{$details->date ?? MySession::current_date()}}" name="transaction_date">
						</div>
						
						<div class="form-group col-md-4">
							<label class="lbl_color text-sm mb-0">Payment Mode</label>
							<select class="form-control form-control-border frm-paymode" id="sel-paymode" name="paymode">
								@foreach($paymentModes as $val=>$desc)
								<option value="{{$val}}" <?php echo (($details->id_paymode ?? 4) == $val)?'selected':''; ?> >{{$desc}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-4">
							<label class="lbl_color text-sm mb-0">OR No.</label>
							<input type="text" class="form-control form-control-border frm-paymode" value="{{$details->or_number ?? ''}}" name="or_number">
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
							<label class="lbl_color mb-0 text-sm">Bank</label>
							<select class="form-control form-control-border p-0 frm-paymode" name="bank">
								@foreach($banks as $bank)
								<option value="{{$bank->id_bank}}" <?php echo (($details->id_bank ?? 1) == $bank->id_bank)?'selected':'';  ?> >{{$bank->bank_name}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-6 col-12">
							<label class="lbl_color mb-0 text-sm">Check Date</label>
							<input type="date" class="form-control form-control-border frm-paymode" name="check_date" value="{{$details->check_date ?? MySession::current_date()}}">
						</div>
						<div class="form-group col-md-6 col-12">
							<label class="lbl_color mb-0 text-sm">Check No.</label>
							<input type="text" class="form-control form-control-border frm-paymode" name="check_no" value="{{$details->check_no ?? ''}}">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-12">
							<label class="lbl_color mb-0 text-sm">Remarks</label>
							<input type="text" class="form-control form-control-border frm-paymode" value="{{$details->remarks ?? ''}}" name="remarks">
						</div>
					</div>
					<div class="row d-flex justify-content-end">
						<div class="form-group col-md-6">
							<label class="lbl_color mb-0 text-sm">Total Payment</label>
							<input type="text" class="form-control form-control-border text-right" disabled id="txt-total-payment">
						</div>
					</div>
				</div>
				<div class="card-footer py-2">
					<button class="btn round_button bg-gradient-primary2 float-right" onclick="post()"><i class="fa fa-save"></i>&nbsp;Save</button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	const BACK_LINK = `<?php echo $back_link; ?>`;
	const RebatesOBJ = jQuery.parseJSON('<?php echo json_encode($rebatesOBJ ?? []); ?>');
	const OPCODE = '{{$opcode}}';
	const ID_REPAYMENT = '{{$details->id_repayment ?? 0}}';
	let MemberSummary = [];
	const CheckDiv = $('#div_check div').detach();
	const CBU_DEF = {{$def_cbu}};
	const ID_BARANGGAY_LGU = {{$selected_branch}};


	$(document).ready(function(){
		$('tbody.bmember').each(function(){
			compute_member($(this).attr('data-member-id'),true);
		});
		init_paymode();
	})
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

	$(document).on('keyup','input.in-loan-payment,input.in-penalty',function(){
		var val = $(this).val();
		if(val == ''){
			val = '0.00';
			$(this).val(decode_number_format(val));	
		}
		
		compute_member($(this).closest('tbody').attr('data-member-id'),true);
	})
	$(document).on('keyup','input.in-cbu',function(){
		var val = $(this).val();
		 // || val == 'NaN'
		if(val == ''){
			val = '0.00';
			$(this).val(decode_number_format(val));	
		}
		
		compute_member($(this).closest('tr.rmember').attr('data-id'),false);
	})
	function compute_member(id_member,trigger=false){
		$total = 0;
		$(`tbody[data-member-id="${id_member}"]`).find('tr.rloan').each(function(){
			var loan_token = $(this).attr('data-loan');

			var loan = $(this).find('input.in-loan-payment').val();
			var penalty = $(this).find('input.in-penalty').val();
			
			loan = ($.isNumeric(loan))?roundoff(loan):decode_number_format(loan);
			penalty = ($.isNumeric(penalty))?roundoff(penalty):decode_number_format(penalty);

			var rebates = (loan == RebatesOBJ[loan_token]['balance'])?RebatesOBJ[loan_token]['rebates']:0;
			$(this).find('input.in-rebates').val(`(${number_format(rebates)})`);
			$row_total = (loan+penalty-rebates)
			$total += $row_total;
			
			$(this).find('input.in-total').val(number_format($row_total));
		});

		$sum_row =$(`tr.rmember[data-id="${id_member}"]`);
		$cbu = $sum_row.find('input.in-cbu').val();
		$cbu = ($.isNumeric($cbu))?roundoff($cbu):decode_number_format($cbu);

		if(trigger){
			if($total > 0 && $cbu ==0){
				$sum_row.find('input.in-cbu').val(number_format(CBU_DEF,2));
			}else if($total == 0){
				$sum_row.find('input.in-cbu').val('0.00');
			}			
		}


		$cbu = $sum_row.find('input.in-cbu').val();
		$cbu = ($.isNumeric($cbu))?roundoff($cbu):decode_number_format($cbu);		
		
		

		$sum_row.find('input.in-total-loan').val(number_format($total,2));
		$sum_row.find('input.mem-total').val(number_format(($total+$cbu),2));

		MemberSummary[id_member] =$total+$cbu;


		GrandTotal();
	}

	function GrandTotal(){
		let GrandTotal = 0;
		$.each(MemberSummary,function(i,val){

			GrandTotal += (val ?? 0);
		});
		$('td#td-gtotal').text(number_format(GrandTotal,2));

		$('#txt-total-payment').val(number_format(GrandTotal,2));
	}
	$(document).on('change','#sel-paymode',function(){
		init_paymode();
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
				temp['penalty'] = decode_number_format($(this).find('input.in-penalty').val());

				token_ = $(this).attr('data-loan');
				tout[token_] = temp;
			});
			temp_loan_out['id_member'] = member_id;
			temp_loan_out['loan_payment'] = tout;
			temp_loan_out['cbu'] = decode_number_format($(`tr.rmember[data-id="${member_id}"]`).find('input.in-cbu').val());
			temp_loan_out['id_repayment_transaction'] = $(this).attr('repayment-id');
			LoanOut.push(temp_loan_out);
		});
		let paymode = {};
		$('.frm-paymode').each(function(){
			let key = $(this).attr('name');
			paymode[key] = $(this).val()
		});
		let ajaxParam = {
			'loans' : LoanOut,
			'opcode' : OPCODE,
			'paymode' : paymode,
			'id_repayment' : ID_REPAYMENT,
			'br' : ID_BARANGGAY_LGU
		};

		$.ajax({
			type       :     'POST',
			url        :     '/bulk-repayment/post',
			data       :      ajaxParam,
			beforeSend  :  function(){
				show_loader();
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
						window.location ='/bulk-repayment/view/'+response.ID_REPAYMENT+"?href="+encodeURIComponent(BACK_LINK);
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
@endpush