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
	.hidden-member{
		display: none;
	}
</style>

<div class="main_form" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/check-deposit':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Check Deposit List</a>

	<?php
		$types = array();
	?>
	<div class="card">
		<div class="card-body px-5">
			<div class="text-center mb-5">
				<h4 class="head_lbl">Check Deposit</h4>
			</div>
			<div style="max-height: calc(100vh - 140px);overflow-y: auto;">
				<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
					<thead>
						<tr class="text-center">
							<th width="3%" class="text-center"><input type="checkbox" id="selAll"></th>
							<th>Date</th>
							<th>Description</th>
							<th>Reference</th>
							<th>Check No</th>
							<th>Check Bank</th>
							<th>Check Date</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						@foreach($checks as $ch)
						<tr class="lbl_color row-check" data-id="{{$ch->id_repayment_payment}}">
							<td class="text-center"><input type="checkbox" class="chk-check" <?php echo($ch->selected==1)?'checked':''; ?>></td>
							<td>{{$ch->transaction_date}}</td>
							<td><?php echo $ch->description;?></td>
							<td>{{$ch->reference}}</td>
							<td>{{$ch->check_no}}</td>
							<td>{{$ch->check_bank}}</td>
							<td>{{$ch->check_date}}</td>
							<td class="text-right pr-2 col-amount">{{number_format($ch->amount,2)}}</td>
						</tr>
						@endforeach
					</tbody>


				</table>
			</div>
			<div class="card c-border mt-4">
				<div class="card-body py-5 px-4">
					<div class="row">
						<div class="form-group col-md-3">
							<label class="lbl_color mb-0">Date Deposit</label>
							<input type="date" class="form-control form-control-border" value="{{$details->date_deposited ?? MySession::current_date()}}" id="date_deposited">
						</div>
						<div class="form-group col-md-5">
							<label class="lbl_color mb-0">Bank</label>
							<select class="form-control form-control-border" id="sel_bank">
								@foreach($banks as $bank)
								<option value="{{$bank->id_bank}}" <?php echo ($bank->id_bank==($details->id_bank ?? config('variables.default_bank')))?'selected':''; ?>>{{$bank->bank_name}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-3">
							<label class="lbl_color mb-0">Total Amount</label>
							<input type="text" class="form-control form-control-border text-right" value="0.00" id="txt-total-amount" disabled>
						</div>
					</div>
					<div class="row mt-3">
						<div class="form-group col-md-10">
							<label class="lbl_color mb-0">Remarks</label>
							<input type="text" class="form-control form-control-border" id="txt_remarks" value="{{$details->remarks ?? ''}}">
						</div>
					</div>
				</div>
			</div>


		</div>
		<div class="card-footer py-2">
			<button class="btn bg-gradient-success2 btn-md float-right" onclick="post()">Save</button>
		</div>
	</div>

	
</div>
@endsection


@push('scripts')
<script type="text/javascript">
	const BACK_LINK = `<?php echo $back_link; ?>`;
	const OPCODE = '{{$opcode}}';
	const ID_CHECK_DEPOSIT = {{$details->id_check_deposit ?? 0}};
	// let TYPES =
	$(document).ready(function(){
		computeSelectedAmount();
	})
	$('#selAll').on('click',function(){
		var checked = $(this).prop('checked');

		$('.chk-check').prop('checked',checked);
		computeSelectedAmount();
	});

	$(document).on('click','.chk-check',function(){
		computeSelectedAmount();
	})
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
				post_deposit();
			}
		});
	}
	const post_deposit = ()=>{

		let postChecks = [];
		$('input.chk-check:checked').each(function(){
			var temp = {};
			var tr = $(this).closest('tr.row-check');
			temp['DATA_ID'] = $(tr).attr('data-id');

			postChecks.push(temp);
		});
		let DepositDetails = {
			'date_deposited' : $('#date_deposited').val(),
			'bank' : $('#sel_bank').val(),
			'remarks' : $('#txt_remarks').val()

		};

		let ajaxParam = {
			'DepositDetails' : DepositDetails,
			'postChecks' : postChecks,
			'opcode' : OPCODE,
			'id_check_deposit' : ID_CHECK_DEPOSIT

		};

		console.log({ajaxParam});
		$.ajax({
			type       :       'GET',
			url        :       '/check-deposit/post',
			data       :       ajaxParam,
			beforeSend  :  function(){
				show_loader();
			},
			success    :       function(response){
							   console.log({response});
								hide_loader();
								if(response.RESPONSE_CODE == "SUCCESS"){
									Swal.fire({
										title: 'Check Deposit Successfully saved',
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
										window.location ='/check-deposit/view/'+response.ID_CHECK_DEPOSIT+"?href="+encodeURIComponent(BACK_LINK);
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

									// let invalid_fields = response.invalid_fields ?? [];

									// for(var i=0;i<invalid_fields.length;i++){
									// 	$(`.frm-paymode[name="${invalid_fields[i]}"]`).addClass('mandatory');
									// }
							   	
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

	const computeSelectedAmount = ()=>{
		var totalDeposit = 0;
		$('input.chk-check:checked').each(function(){
			var tr = $(this).closest('tr.row-check');
			totalDeposit += decode_number_format($(tr).find('td.col-amount').text());

		});		
		$('#txt-total-amount').val(number_format(totalDeposit,2));
	}
	
</script>

@endpush