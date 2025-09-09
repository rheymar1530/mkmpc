
@push('head')
<style type="text/css">
	#tbl_pending_loans  td,#tbl_pending_loans  th{
		padding-right: 8px;
		padding-left: 8px;
	}
</style>
@endpush
<?php

$status_badge = [
	0=>"info",
	1=>"primary",
	2=>"success",
	3=>"success",
	4=>"danger",
	5=>"danger",
	6=>"primary",
]
?>
<ul class="products-list product-list-in-card pl-2 pr-2 text-sm">
	@foreach($pending_loans as $pl)
	<li class="item">
		<div class="product-info ml-3">
			<span class="lbl_color text-bold product-title">{{$pl->loan_service_name}} <a class="badge bg-gradient-dark text-sm ml-2 rounded-pill" href="/loan/application/view/{{$pl->loan_token}}" target="_blank">{{$pl->id_loan}}</a>
				<span class="badge bg-gradient-{{$status_badge[$pl->status_code]}}2 float-right text-sm">{{$pl->loan_status_dec}}</span>
<!-- 			{{$pl->loan_service_name}}<a href="javascript:void(0)" class="product-title">
				<span class="badge bg-gradient-{{$status_badge[$pl->status_code]}}2 float-right text-sm">{{$pl->loan_status_dec}}</span></a> -->
				<span class="product-description">
					₱{{number_format($pl->principal_amount,2)}} ({{interest_rate_format($pl->interest_rate)}})
				</span>
			</div>
		</li>
	@endforeach
</ul>
<!-- <div class="table-responsive" >
	<table class="table m-0 font-normal" id="tbl_pending_loans">
		<thead class="text-muted text-center">
			<tr>
			<th class="border-top-0">Loan</th>
			<th class="border-top-0">Principal</th>
			<th class="border-top-0">Interest Rate</th>
			<th class="border-top-0">Status</th>

		</tr>
		</thead>
		<tbody>
		@foreach($pending_loans as $count=>$pl)
		@if($count < 5)
		<tr>
			<td><span class="lbl_color text-bold">{{$pl->loan_service_name}} <a href="/loan/application/view/{{$pl->loan_token}}" target="_blank">(ID #{{$pl->id_loan}})</a></span></td>
			<td>₱{{number_format($pl->principal_amount,2)}}</td>
			<td class="text_center">{{interest_rate_format($pl->interest_rate)}}</td>
			<td><span class="badge bg-gradient-{{$status_badge[$pl->status_code]}}2 text-sm">{{$pl->loan_status_dec}}</span></td>
		</tr>
		@endif
		@endforeach
		</tbody>
	</table>
</div> -->