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
?>
<div class="container-fluid">
	<?php $back_link = (request()->get('href') == '')?'/bulk-repayment':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment (Bulk) List</a>

	@if($details->status == 0)
	<a class="btn bg-gradient-primary2 btn-sm round_button float-right" href="/repayment-bulk/edit/{{$details->id_repayment}}?href={{$back_link}}" style="margin-bottom:10px"><i class="fas fa-edit"></i>&nbsp;&nbsp;Edit Loan Payment</a>
	@endif
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
								<span class="text-sm  font-weight-bold lbl_color">@if($details->br_type == 1)Barangay @else LGU @endif : {{$details->br}}</span>
								<span class="text-sm  font-weight-bold lbl_color">Transaction Date: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->date}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">OR Number: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->or_number}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Total Amount: <span class="ms-sm-2 font-weight-normal ml-2">{{number_format($details->total_amount,2)}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Paymode: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->paymode}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Payment Date Received: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->date_received}}</span></span>
								
							</div>
						</div>
						@if($details->id_paymode == 4)
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">
								
								<span class="text-sm  font-weight-bold lbl_color">Check Type: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->check_type}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Check Date: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->check_date}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Check No: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->check_no}}</span></span>
								
							</div>
						</div>
						@endif
						<div class="col-lg-6 col-12">
							<span class="text-sm  font-weight-bold lbl_color">Remarks: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->remarks}}</span></span>
						</div>
					</div>

				</div>
			</div>	
			<div class="table-responsive mt-2">
				<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_pdc">
					<thead>
						<tr class="text-center">
							<th>Member</th>
							<th>Loan Service</th>
							<th>Loan Payment</th>
							
							<th></th>
						</tr>
					</thead>
						<?php
							$total_summary = array();
						?>
			
						@foreach($loans as $id_member=>$loan)
						<?php
							$member_total_payment = 0;
							$cbu_amount = $cbus[$id_member] ?? 0;
						?>
						<tbody class="borders mbody" data-id="{{$id_member}}">
							@foreach($loan as $c=>$l)
							<?php
							$rebates = number_format($l->rebates,2);

							$rebates = ($l->rebates > 0)?"($rebates)":$rebates;
							$total_loan_payment = $l->payment + $l->penalty - $l->rebates;
							$member_total_payment += $total_loan_payment;

							
							?>
							<tr>
								@if($c == 0)
								<td class="font-weight-bold" rowspan="{{count($loan)}}"><i>{{$l->member}}</i></td>
								@endif
								<td><sup><a href="/loan/application/approval/{{$l->loan_token}}" target="_blank">[{{$l->id_loan}}] </a></sup>{{$l->loan_name}}</td>
								<td class="text-right">{{number_format($l->payment,2)}}</td>

								
								@if($c == 0)
								
									
								<td class="text-center" rowspan="{{count($loan)}}">
									@if($l->id_cash_receipt_voucher  > 0)
									<a class="btn btn-xs bg-gradient-danger" onclick="print_page('/cash_receipt_voucher/print/{{$l->id_cash_receipt_voucher}}')"><i class="fa fa-print"></i>&nbsp;Print CRV ({{$l->id_cash_receipt_voucher}})</a>
									@endif
								</td>
								@endif
								<!-- <td class="text-right">{{number_format($total_loan_payment,2)}}</td> -->
							</tr>
							@endforeach
						</tbody>
						<?php
							$total_summary[$id_member] = $member_total_payment+$cbu_amount;
						?>
						@endforeach
						<?php
							$g_total = array_sum($total_summary);


						?>
					<footer>
						<tr>
							<th colspan="2" class="footer_fix text-left font-weight-normal" style="text-align:center;background: #808080 !important;color: white;"></th>
							<th class="footer_fix text-right"  style="background: #808080 !important;color: white;">{{number_format($g_total,2)}}</th>
							<th style="background: #808080 !important;color: white;"></th>
							
						</tr>
					</footer>
				</table>
			</div>

			@if($details->status == 10)
			<p class="text-muted">Reason : {{$details->reason}} ({{$details->status_date}})</p>
			@endif
		</div>
		@if($details->status == 0)
		<div class="card-footer">
			<button class="btn bg-gradient-danger2 round_button float-right" onclick="show_status_modal()">Cancel</button>
		</div>

		@endif
	</div>
</div>
@include('bulk-repayment.status_modal')
@include('global.print_modal')
@endsection

@push('scripts')
<script type="text/javascript">
	const TOTALS =  jQuery.parseJSON('<?php echo json_encode($total_summary ?? []); ?>');
	const ID_REPAYMENT = {{$details->id_repayment}};
	$(document).ready(function(){
		$('tbody.mbody').each(function(){
			var id = $(this).attr('data-id');
			$(this).find('.mtotal').text(number_format(TOTALS[id],2));
		})
	})
</script>
@endpush