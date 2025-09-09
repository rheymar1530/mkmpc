@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.tbl_pdc th, .tbl_pdc td{
		padding: 0.2rem;
		font-size: 0.8rem;
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
	.vcenter{
/*		vertical-align: middle !important;*/
	}

</style>
<?php
	function status_class($status){
		switch ($status) {
			case 0:
				return 'primary';
				break;
			case 1:
				return 'success';
				break;
			case 10:
				return 'danger';
				break;
			default:
				// code...
				break;
		}
	}


	if($details->id_paymode == 4){
		$PaymentOBJ = array(
			'Check Type'=>'check_type',
			'Check Bank'=>'check_bank',
			'Check Date'=>'check_date',
			'Check No'=>'check_no',
			'Amount'=>'amount',
			'Remarks'=>'remarks'
		);
		$PColCount = 5;
	}else{

	}

	$totalPayment = 0;
?>
<div class="container-fluid">
	<?php $back_link = (request()->get('href') == '')?'/repayment-bulk':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment (Bulk) List</a>


	@if($details->status == 0 && $details->deposit_status == 0)
	<a class="btn bg-gradient-primary2 btn-sm round_button float-right" href="/repayment-bulk/edit/{{$details->id_repayment}}?href={{$back_link}}" style="margin-bottom:10px"><i class="fas fa-edit"></i>&nbsp;&nbsp;Edit Loan Payment</a>
	@endif

	@if($details->id_cash_receipt_voucher > 0 && $details->change_payable > 0)
		<a class="btn bg-gradient-danger2 btn-sm round_button float-right mr-2" onclick="print_page('/cash_receipt_voucher/print/{{$details->id_cash_receipt_voucher}}')"><i class="fa fa-print"></i>&nbsp;Print Change CRV ({{$details->id_cash_receipt_voucher}})</a>
	@endif
	<a class="btn bg-gradient-warning2 btn-sm round_button float-right mr-2" onclick="printOR('{{$details->id_repayment}}')" style="margin-bottom:10px"><i class="fas fa-print"></i>&nbsp;&nbsp;Print OR</a>

	<button class="btn bg-gradient-info btn-sm round_button float-right mr-2" onclick="print_page('/repayment-bulk/export/{{$details->id_repayment}}')"><i class="fa fa-print"></i>&nbsp;Print Loan Payment</button>
	<div class="card">
		<div class="card-body">
			<div class="text-center">
				<h4 class="head_lbl">Loan Payment <small>(ID# {{$details->id_repayment}})</small>
						<span class="badge badge-{{status_class($details->status)}}">{{$details->status_description}}</span>
				</h4>
			</div>

			<div class="card c-border">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							
						</div>
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">
								
								<span class="text-sm  font-weight-bold lbl_color">Date: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->date}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">OR Number: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->or_number}}</span></span>
								
								<span class="text-sm  font-weight-bold lbl_color">Paymode: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->paymode}}</span></span>
							
								
							</div>
						</div>
							<div class="col-lg-6 col-12">
								<div class="d-flex flex-column">
									<span class="text-sm  font-weight-bold lbl_color">Total Amount: <span class="ms-sm-2 font-weight-normal ml-2">{{number_format($details->total_amount,2)}}</span></span>
									<span class="text-sm  font-weight-bold lbl_color">Remarks: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->remarks}}</span></span>
							</div>
						</div>
					</div>

				</div>
			</div>	
			<?php
				$total = 0;
			?>			
			<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
				<thead>
					<tr class="text-center">
						<th>Member</th>
						<th>Loan Service</th>
		
						<th>Payment</th>
						<th width="10%"></th>
						
					</tr>
				</thead>
				@if($details->payment_for_code == 2)


					@foreach($statamentData as $statementDescription=>$MemberData)
					<tr class="statement-head bg-gradient-success2 font-weight-bold">
						<td colspan="4">{{$statementDescription}}</td>
					</tr>

						@foreach($MemberData as $m=>$data)
						<?php $length = count($data);?>
							@foreach($data as $c=>$row)

							<tr>
								@if($c == 0)
								<td class="font-weight-bold nowrap td-mem" rowspan="{{$length}}"><i>{{$row->member}} </i></td>
								@endif
								<td><sup><a href="/loan/application/approval/{{$row->loan_token}}" target="_blank">[{{$row->id_loan}}] </a></sup>{{$row->loan_name}}</td>
								<td class="text-right">{{number_format($row->payment,2)}}</td>
								<?php $total += $row->payment; ?>
								@if($c == 0)
								<td class="font-weight-bold text-center" rowspan="{{$length}}">
									@if($row->id_cash_receipt_voucher  > 0)
									<a class="btn btn-xs bg-gradient-danger h-100" onclick="print_page('/cash_receipt_voucher/print/{{$row->id_cash_receipt_voucher}}')"><i class="fa fa-print"></i>&nbsp;Print CRV ({{$row->id_cash_receipt_voucher}})</a>
									@endif
								</td>
								@endif
							</tr>
							@endforeach
						@endforeach
					@endforeach

				@else
					@foreach($Loans as $member=>$rows)
					<?php
						$length = count($rows);
					?>
						@foreach($rows as $c=>$row)
						<tr>
							@if($c == 0)
							<td class="font-weight-bold nowrap td-mem" rowspan="{{$length}}"><i>{{$row->member}} </i></td>
							@endif
							<td><sup><a href="/loan/application/approval/{{$row->loan_token}}" target="_blank">[{{$row->id_loan}}] </a></sup> {{$row->loan_name}}</td>
							<td class="text-right">{{number_format($row->payment,2)}}</td>
							<?php $total += $row->payment; ?>
							@if($c == 0)
								<td class="font-weight-bold text-center" rowspan="{{$length}}">
									@if($row->id_cash_receipt_voucher  > 0)
									<a class="btn btn-xs bg-gradient-danger h-100" onclick="print_page('/cash_receipt_voucher/print/{{$row->id_cash_receipt_voucher}}')"><i class="fa fa-print"></i>&nbsp;Print CRV ({{$row->id_cash_receipt_voucher}})</a>
									@endif
								</td>
							@endif
						</tr>
						@endforeach
					@endforeach

				@endif
				<tfoot>
					<tr>
						<th colspan="2" class="footer_fix text-left font-weight-normal" style="text-align:center;background: #808080 !important;color: white;"></th>
						<th class="footer_fix text-right td-total-payment"  style="background: #808080 !important;color: white;">{{number_format($total,2)}}</th>
						<th class="footer_fix text-left font-weight-normal" style="text-align:center;background: #808080 !important;color: white;"></th>
						
					</tr>
				</tfoot>
			</table>

			@if($details->id_paymode ==4)
			<div class="row mt-4">
				<div class="col-md-12">
					<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
						<thead>
							<tr class="text-center">
								<th></th>
								@foreach($PaymentOBJ as $head=>$key)
								<th>{{$head}}</th>
								@endforeach

								
								<th>Deposit Details</th>
								
							</tr>
						</thead>
						<tbody>
							@foreach($paymentDetails as $i=>$pd)
							<tr>
								<td class="text-center">{{$i+1}}</td>
								@foreach($PaymentOBJ as $key)
								<td class="<?php echo ($key=='amount')?'text-right':''; ?>" >
									@if($key=='amount')
									{{ number_format($pd->{$key},2) }}
									<?php
									$totalPayment += $pd->{$key};
									?>
									
									@else
									{{ $pd->{$key} }}
									@endif
									</td>
								@endforeach

								<td>
									@if(isset($deposits[$pd->id_repayment_payment]))
									<?php $dep = $deposits[$pd->id_repayment_payment][0]; ?>

									ID# <a href="/check-deposit/view/{{$dep->id_check_deposit}}" target="_blank">{{$dep->id_check_deposit}}</a>  - {{$dep->bank_name}} [{{$dep->date_deposit}}]
									@endif

								</td>
							</tr>
							@endforeach
						</tbody>
						@if(count($paymentDetails) > 1)
						<tr>
							<td colspan="{{$PColCount}}" class="font-weight-bold">Total</td>
							<td class="text-right font-weight-bold">{{number_format($totalPayment,2)}}</td>
							<td></td>
							<td></td>
						</tr>
						@endif
						@if($details->change_payable > 0)
						<tr class="">
							<td colspan="{{$PColCount}}" class="font-weight-bold">Change</td>
							<td class="text-right font-weight-bold">{{number_format($details->change_payable,2)}}</td>
							<td colspan="2"></td>
						</tr>
						<?php
							$totalChange = collect($changes)->sum('total_amount');
							$bal =ROUND($details->change_payable-$totalChange,2);
							$totalClass = ($bal== 0)?'success':'danger'; 
						?>
						<tr class="{{$bal == 0?'text-success':''}}">
							<td colspan="{{$PColCount}}" class="font-weight-bold">Total Change Released</td>
							<td class="text-right font-weight-bold">{{number_format($totalChange,2)}}</td>
							<td colspan="2"></td>
						</tr>
						@if($bal > 0)
						<tr class="text-danger">
							<td colspan="{{$PColCount}}" class="font-weight-bold">Balance</td>
							<td class="text-right font-weight-bold">{{number_format($bal,2)}}</td>
							<td colspan="2"></td>
						</tr>						
						@endif
						@endif
					</table>

				</div>

			</div>
			@endif

			@if(count($changes) > 0)
			<h6 class="lbl_color font-weight-bold mt-2"><u>Change List</u></h6>
			<div class="row">
				<div class="col-md-12">
					<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
						<thead>
							<tr class="text-center">
								<th></th>
								<th>ID</th>	
								<th>Date</th>
								<th>Total Amount</th>
								<th>Remarks</th>
								<th>Date Posted</th>
							</tr>	
						</thead>	
						<tbody>
							@foreach($changes as $c=>$change)
							<tr>
								<td class="text-center">{{$c+1}}</td>
								<td class="text-center"><a href="/change-payable/view/{{$change->id_change_payable}}" target="_blank">{{$change->id_change_payable}}</a></td>
								<td>{{$change->change_date}}</td>
								<td class="text-right">{{number_format($change->total_amount,2)}}</td>
								<td>{{$change->remarks}}</td>
								<td class="text-xs">{{$change->date_posted}}</td>
							</tr>
							@endforeach
						</tbody>			
						<tr>
							<td class="font-weight-bold" colspan="3">Total</td>
							<td class="text-right font-weight-bold">{{number_format($totalChange,2)}}</td>
							<td colspan="2"></td>
								
							
							
						</tr>
					</table>
				</div>
			</div>
			@endif

			@if($details->status == 10)
			<p class="text-muted">Reason : {{$details->reason}} ({{$details->status_date}})</p>
			@endif
		</div>
		@if($details->status == 0 && $details->deposit_status == 0)
		<div class="card-footer">
			<button class="btn bg-gradient-danger2 round_button float-right" onclick="show_status_modal()">Cancel</button>
		</div>

		@endif
	</div>
</div>

<iframe id="print_frame_or"  style="display: none;"> </iframe>


@include('global.print_modal')
@if($details->status == 0)
@include('repayment-bulk.status_modal')
@endif
@endsection

@push('scripts')
<script type="text/javascript">
	const ID_REPAYMENT = {{$details->id_repayment}};
	const printOR = (id_repayment) =>{
		$('#print_frame_or').attr('src',`/repayment-bulk/print-or/${id_repayment}`);
	}
</script>
@endpush