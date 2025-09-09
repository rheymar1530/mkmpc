@extends('adminLTE.admin_template_frame')
@section('content')

<style type="text/css">
	thead th{
		vertical-align: middle !important;
	}
	table td, table th{
		font-size: 0.9rem;
		padding: 6px !important;
	}
</style>
<?php
	function status_class($status){
		if($status == 0){
			$class="primary";
		}elseif($status == 1){
			$class="success";
		}else{
			$class= "danger";
		}

		return $class;
	}
?>

<div class="container-fluid">
	<div class="table-responsive">
		<table class="table table-bordered table-stripped table-head-fixed">
			<thead>
				<tr class="text-center">
					<th>Reference</th>
					<th>Date Released</th>
					<th>Principal</th>
					<th>Interest</th>
					<th>Total Amount</th>
					<th>Status</th>
					<th>Date Created</th>
					<th>Type</th>
				</tr>
			</thead>
			<tbody>
				@foreach($withdrawals as $w)
				<tr>
					<td class="text-center">{{$w->reference}}</td>
					<td>{{$w->status_date}}</td>
					<td class="text-right">{{number_format($w->principal,2)}}</td>
					<td class="text-right">{{number_format($w->interest,2)}}</td>
					<td class="text-right">{{number_format($w->total_amount,2)}}</td>
					<td><span class="badge badge-{{status_class($w->status_code)}}">{{$w->status}}</span></td>
					<td class="text-muted">{{$w->date_created}}</td>
					<td class="text-muted"><i>{{$w->type}} @if($w->id_new_investment > 0)<a href="/investment/view/{{$w->id_new_investment}}" target="_blank">[Investment ID# {{$w->id_new_investment}}]</a> @endif</i></td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection