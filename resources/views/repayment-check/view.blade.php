@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.tbl_pdc th, .tbl_pdc td{
		padding: 0.2rem;
		font-size: 0.8rem;
	}
	.tbl_pdc th{
		padding: 0.4rem;
		font-size: 0.8rem;
	}
	.selected_rp{
		background: #ccffcc;
	}
	.footer_fix {
		padding: 3px !important;
		background-color: #fff;
		border-bottom: 0;
		box-shadow: inset 0 1px 0 #dee2e6, inset 0 -1px 0 #dee2e6;
		position: -webkit-sticky;
		position: sticky;
		bottom: 0;
		z-index: 10;
	}
</style>
<?php 
	$back_link = (request()->get('href') == '')?'/repayment-check':request()->get('href'); 
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
<div class="container">
	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment Checks List</a>
	@if($details->status == 0)
	<a class="btn bg-gradient-primary2 btn-sm round_button float-right" href="/repayment-check/edit/{{$details->id_repayment_check}}" style="margin-bottom:10px"><i class="fas fa-edit"></i>&nbsp;&nbsp;Edit Loan Payment Check</a>
	@endif
	<div class="card">
		<div class="card-body">
			<div class="text-center mb-3">
				<h4 class="head_lbl">Loan Payment Check <small>(ID# {{$details->id_repayment_check}})</small>
					<span class="badge bg-gradient-{{status_class($details->status)}} text-md">{{$details->status_description}}</span>

				</h4>
			</div>	
			<div class="card c-border">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">Baranggay/Branch : {{$details->branch_name}}</span>
								<span class="text-sm  font-weight-bold lbl_color">Transaction Date: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->transaction_date}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Total Amount: <span class="ms-sm-2 font-weight-normal ml-2">{{number_format($details->amount,2)}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Remarks: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->remarks}}</span></span>
							</div>
						</div>
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">Check Type: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->check_type}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Check Date: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->check_date}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Check No: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->check_no}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Bank: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->bank_name}}</span></span>
							</div>
						</div>
					</div>

				</div>
			</div>		
			<div class="table-responsive mt-2">
				<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_pdc">
					<thead>
						<tr class="text-center">
							<th>Loan Payment ID</th>
							<th>Date</th>
							<th>Member</th>
							<th>Amount</th>
						</tr>
					</thead>

					<tbody>
						@foreach($repayments as $list)
						<tr class="rp-row">

							<td><a href="/repayment/view/{{$list->repayment_token}}" target="_blank">{{$list->id_repayment_transaction}}</a></td>
							<td>{{$list->date}}</td>
							<td>{{$list->member}}</td>
							<td class="text-right">{{number_format($list->total_payment,2)}}</td>


						</tr>
						@endforeach
					</tbody>
					<footer>
						<tr>
							<th colspan="3" class="footer_fix text-left font-weight-normal" style="text-align:center;background: #808080 !important;color: white;"></th>
							<th class="footer_fix text-right"  style="background: #808080 !important;color: white;">{{number_format($details->amount,2)}}</th>
							
						</tr>
					</footer>
				</table>
			</div>
		</div>
		@if($details->status == 0)
		<div class="card-footer">
			<button class="btn btn-md bg-gradient-success2 float-right" onclick="show_status_modal()">Update Status</button>
		</div>
		@endif
	</div>
</div>

@include('repayment-check.status_modal')
@endsection


