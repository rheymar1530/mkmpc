@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	@media (min-width: 1200px) {
		.container{
			max-width: 620px;
		}
	}
</style>

<?php
	function status_class($status){
		$class = "";
		switch($status){
			case 0:
				$class="primary";
				break;
			case 1:
				$class ="success";
				break;
			default:
				$class="danger";
				break;
		}
		return $class;
	}
?>

<div class="container">
	<?php $back_link = (request()->get('href') == '')?'/investment-withdrawal':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Investment Withdrawal List</a>
	<div class="card c-withdrawal-form">
		<div class="card-body">
			<div class="text-center">
				<h3 class="head_lbl">Investment Withdrawal <small>(ID# {{$details->id_investment_withdrawal}})</small> <span class="badge badge-{{status_class($details->status)}}">{{$details->status_description}}</span></h3>
			</div>
			<p class="lbl_color mb-0"><b class="pr-2">Investment ID:</b> {{$details->id_investment}}</p>
			<p class="lbl_color mb-0"><b class="pr-2">Investor:</b> {{$details->investor}}</p>
			<p class="lbl_color mb-0"><b class="pr-2">Investment Product:</b> {{$details->product_name}}</p>
			
			@if($details->status == 0)
			<p class="lbl_color mb-0"><b>Withdrawable Amount:</b> {{number_format($details->withdrawable,2)}}</p>
			<div class="form-row mt-3">
				<div class="form-group col-md-5 col-12">
					<label class="lbl_color mb-0">Amount</label>
					<input class="form-control form-control-border text-right class_amount text-with-amt" value="{{number_format($details->amount_withdrawn,2)}}">
				</div>
			</div>
			@else
			<p class="lbl_color mb-0"><b class="pr-2">Amount:</b> {{number_format($details->amount_withdrawn,2)}}</p>
			@if($details->status == 1)
			<p class="lbl_color mb-0"><b class="pr-2">Date Released:</b> {{$details->date_released}}</p>
			@endif
			@endif
			@if($details->status == 10)
			<p class="text-right text-muted font-italic mb-0">Date Cancelled: {{$details->status_date}}</p>
			<p class="text-right text-muted font-italic mb-0">Reason: {{$details->cancellation_remarks}}</p>
			@endif

		</div>
		@if($details->status == 0)
		<div class="card-footer">
			<button class="btn bg-gradient-success2 round_button float-right" onclick="post()">Save</button>
			<button class="btn bg-gradient-primary round_button float-right mr-2" onclick="show_status_modal('{{$details->id_investment_withdrawal}}')">Update Status</button>
		</div>
		@elseif($details->status == 1 && MySession::isAdmin() && $details->id_cash_disbursement > 0)
		<div class="card-footer">
			<button id="btn_print_cdv" class="btn bg-gradient-danger2 round_button float-right" onclick="print_page('/cash_disbursement/print/{{$details->id_cash_disbursement}}')">Print CDV (CDV# {{$details->id_cash_disbursement}})</button>
			
		</div>
		@endif
	</div>
</div>
@if($details->status == 0)
@include('investment-withdrawal.post-js')
@include('investment-withdrawal.status_modal')
@elseif($details->status == 1 && MySession::isAdmin())
@include('global.print_modal')

@endif
@endsection

@push('scripts')
<script type="text/javascript">
	const opcode = {{$opcode}};
	const ID_INVESTMENT_WITHDRAWAL = {{$details->id_investment_withdrawal}};
	const STATUS_UPDATE_MODE = 2;

	$(document).ready(function(){
		if(localStorage.getItem('cdv-print-trigger') == 1){
			$('#btn_print_cdv').trigger("click");
			localStorage.removeItem('cdv-print-trigger');
		}
	})
</script>
@endpush