@extends('adminLTE.admin_template')
@section('content')

<style type="text/css">
	.inv-table td, .inv-table th{
		padding: 2px;
		font-size: 0.85rem;
	}
	.v-center,.inv-table th{
		vertical-align: middle !important;
	}
	.b-top{
		border-top: 3px solid !important;
	}
	.hidden{
		display: none;
	}
</style>
</style>
<div class="container">
		<?php $back_link = (request()->get('href') == '')?'/investment-withdrawal':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Investment Withdrawal List</a>
	<div class="card">
		<div class="card-body">
			<div class="text-center">
				<h4 class="head_lbl">Investment Withdrawal <small>(ID# {{$batch_details->id_investment_withdrawal_batch}})</small></h4>
			</div>

			<div class="row">
				<div class="col-md-12 col-12">
					@if($batch_details->status == 1)
					<p class="mb-0 lbl_color"><b>Date Released: {{date('n/j/Y', strtotime($batch_details->date_released))}}</b></p>
					@endif

					<table class="table table-head-fixed table-hover table-bordered mt-3 inv-table">
						<thead>
							<tr>
								<th class="table_header_dblue">Reference</th>
								<th class="table_header_dblue">Investor</th>
								<th class="table_header_dblue">Investment Product</th>
								<th class="table_header_dblue">Maturity Date</th>
								<th class="table_header_dblue">Amount</th>
								@if($batch_details->close_request == 0)
								<th class="table_header_dblue">Status</th>
								@endif
								<th class="table_header_dblue act-but"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($withdrawals as $w)
							<tr>
								<td class="text-center">{{$w->id_investment_withdrawal}}</td>
								<td>{{$w->investor}}</td>
								<td>{{$w->product_name}} <a href="/investment/view/{{$w->id_investment}}" target="_blank">[ID #{{$w->id_investment}}]</a></td>
								<td>{{date('n/j/Y', strtotime($w->maturity_date))}}</td>
								<td class="text-right pl-2">{{number_format($w->amount,2)}}</td>
								@if($batch_details->close_request == 0)
								<td>
									<span class="text-xs badge badge-{{($w->status==1)?'success':'danger'}}">{{$w->status_description}}</span>
								</td>
								@endif
					
								<td class="act-but">
									@if($w->id_cash_disbursement > 0)
									<button class="btn btn-xs bg-gradient-primary2 text-xs btn-cdv" onclick="print_page('/cash_disbursement/print/{{$w->id_cash_disbursement}}')"><i class="fa fa-print"></i>&nbsp;Print CDV (#{{$w->id_cash_disbursement}})</button>
									@endif

									@if($allow_cancel_ind && $w->status != 10)
									<button class="btn btn-xs bg-gradient-danger2 text-xs" onclick="cancel_withdrawal('{{$w->id_investment_withdrawal}}')"><i class="fa fa-times"></i>&nbsp;Cancel</button>
									@endif
								</td>
					
							</tr>
							@endforeach

						</tbody>
					</table>
					@if($batch_details->status == 10)
					<p class="mb-0 text-muted"><b>Reason</b>: {{$batch_details->cancellation_remarks}} [Date Cancelled : {{date('n/j/Y H:i A', strtotime($batch_details->status_date))}}]</p>
					@endif
				</div>
				
			</div>
		</div>

		@if($allow_status_close_request && $batch_details->status == 0)
		<div class="card-footer">
			<button class="btn bg-gradient-primary2 round_button float-right ml-2" onclick="show_status_modal('{{$batch_details->id_investment_withdrawal_batch}}')">Update Status</button>
		</div>
		@elseif($batch_details->status == 1 && MySession::isAdmin())
		<div class="card-footer py-2">
			<button type="button" class="btn bg-gradient-danger2 float-right mr-1" onclick="print_page('/investment-withdrawal/batch-summary/{{$batch_details->id_investment_withdrawal_batch}}')"><i class="fa fa-print"></i>&nbsp;Print Summary</button>
		</div>
		@endif
	</div>
</div>

@include('global.print_modal')

@if($allow_cancel_ind || $allow_status_close_request)

@include('investment-withdrawal.indv-cancel-modal')

@endif

@if($allow_status_close_request)

@include('investment-withdrawal.status_modal')
@endif



@endsection

@push('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		if($('.btn-cdv').length == 0){
			$('.act-but').remove();
		}
	})
</script>
@endpush