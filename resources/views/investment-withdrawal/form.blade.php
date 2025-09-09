@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.inv-table td, .inv-table th{
		padding: 2px;
		font-size: 0.85rem;
		vertical-align: middle;
	}
	td.td-in{
		padding: 0px;
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
	.text-with-amt.mandatory{
		font-weight: bold;
		color:red !important;
	}
</style>

<?php
	$withdrawables_validation = array();
	$investor_key = array();
?>

@include('investment-withdrawal.form-php')
<div class="container">
	<?php $back_link = (request()->get('href') == '')?'/investment-withdrawal':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Investment Withdrawal List</a>
	<div class="card">
		<div class="card-body">
			<div class="text-center">
				<h4 class="head_lbl">
					Investment Withdrawal
					@if($opcode == 1)
					<small>(ID# {{$batch_details->id_investment_withdrawal_batch}})</small>
					@endif
				</h4>
			</div>
			<button class="btn bg-gradient-danger2 btn-md" onclick="show_withrawables()"><i class="fa fa-list"></i>&nbsp;Select Withdrawables</button>
			<div class="row mt-3">
				
				<div class="col-md-12 col-12">
					<table class="table table-striped table-bordered inv-table">
						<thead>
							<tr class="table_header_dblue">
								<th>Investor</th>
								<th>Investment Product</th>
								<th>Principal</th>
								<th>Interest</th>
								<th>Total Withdrawables</th>
								<th></th>
								<th width="2%"></th>
							</tr>
						</thead>
						<tbody id="with_form_body">
						
						</tbody>
					</table>

<!-- 					<div id="withdrawal_cards">

					</div> -->
				</div>
			</div>
		</div>
		<div class="card-footer p-2">
			@if($opcode == 1)
			<button class="btn bg-gradient-primary2 round_button float-right ml-2" onclick="show_status_modal('{{$batch_details->id_investment_withdrawal_batch}}')">Update Status</button>
			@endif
			<button class="btn bg-gradient-success2 round_button float-right" onclick="post()">Save</button>
		</div>
	</div>
</div>

@include('investment-withdrawal.investment-modal')

@if($opcode == 1)
@include('investment-withdrawal.status_modal')
@endif

@include('investment-withdrawal.post-js')
@endsection

@push('scripts')






@endpush