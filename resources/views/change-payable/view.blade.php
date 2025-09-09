@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	@media (min-width: 1200px) {
		.container{
			max-width: 780px;
		}
	}
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


<div class="main_form container" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/change-payable':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Change Payable List</a>


	<button class="btn bg-gradient-info btn-sm round_button float-right" onclick="print_page('/change-payable/print/{{$details->id_change_payable}}')"><i class="fa fa-print"></i>&nbsp;Print</button>
	@if($details->status == 0)
	<button class="btn bg-gradient-warning2 btn-sm round_button float-right mr-2" onclick="show_status_modal()"><i class="fa fa-times"></i>&nbsp;Cancel Change Payable</button>

	<a class="btn bg-gradient-primary2 btn-sm round_button float-right mr-2" href="/change-payable/edit/{{$details->id_change_payable}}?href={{$back_link}}"><i class="fa fa-edit"></i>&nbsp;Edit Change Payable</a>
	@endif

	<?php
		$types = array();
	?>

	<div class="card">
		<div class="card-body px-5">
			<div class="text-center mb-5">
				<h4 class="head_lbl">Change Payable</h4>
			</div>
			<div class="card c-border">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-5 col-12">
							<div class="d-flex flex-column">
								<span class="text-md  font-weight-bold lbl_color">Change ID: {{$details->id_change_payable}}</span>
								
								<span class="text-md  font-weight-bold lbl_color">Date: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->date}}</span></span>
								<span class="text-md  font-weight-bold lbl_color">Loan Payment ID: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->id_repayment}}</span></span>
							</div>		
						</div>
						<div class="col-lg-7 col-12">
							<div class="d-flex flex-column">
								<span class="text-md  font-weight-bold lbl_color">Total Amount: <span class="ms-sm-2 font-weight-normal ml-2">{{number_format($details->total_amount,2)}}</span></span>
								<span class="text-md  font-weight-bold lbl_color">Remarks: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->remarks}}</span></span>
								<span class="text-md  font-weight-bold lbl_color">Status: <span class="ms-sm-2 font-weight-normal ml-2 badge bg-gradient-{{$details->status_badge}} text-md">{{$details->status_description}}</span></span>
							</div>		
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="d-flex flex-column">
								<span class="text-md  font-weight-bold lbl_color">Change For:  <span class="ms-sm-2 font-weight-normal ml-2"><?php echo $details->change_for; ?></span></span>
							</div>		
						</div>
					</div>
					@if($details->status == 10)
					<div class="row mt-2">
						<div class="col-lg-12 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">Cancellation Reason: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->reason}} <i>({{$details->status_date}})</i></span></span>
							</div>
						</div>
					</div>
					@endif
				</div>
			</div>

			@if(isset($Applications[1]))
				<h5 class="lbl_color mb-0">Member</h5>
				<div style="max-height: calc(100vh - 140px);overflow-y: auto;">
					<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
						<thead>
							<tr class="text-center">
							
								<th width="5%"></th>
								<th>Member Name</th>
								<th>Amount</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($Applications[1] as $c=>$row)
							<tr>
								<td class="text-center">{{$c+1}}</td>
								<td>{{$row->reference}}</td>
								<td class="text-right">{{number_format($row->amount,2)}}</td>
								<td>
									@if($row->id_cash_disbursement  > 0)
									<a class="btn btn-xs bg-gradient-danger h-100" onclick="print_page('/cash_disbursement/print/{{$row->id_cash_disbursement}}')"><i class="fa fa-print"></i>&nbsp;Print CDV ({{$row->id_cash_disbursement}})</a>
									@endif

								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@endif
			
			@if(isset($Applications[2]))
				<h5 class="lbl_color mb-0">Other Income <a class="btn btn-xs bg-gradient-danger h-100" onclick="print_page('/cash_disbursement/print/{{$details->id_cash_disbursement}}')"><i class="fa fa-print"></i>&nbsp;Print CDV ({{$details->id_cash_disbursement}})</a></h5>
				<div style="max-height: calc(100vh - 140px);overflow-y: auto;">
					<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
						<thead>
							<tr class="text-center">
							
								<th width="5%"></th>
								<th>Account</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody>
							@foreach($Applications[2] as $c=>$row)
							<tr>
								<td class="text-center">{{$c+1}}</td>
								<td>{{$row->reference}}</td>
								<td class="text-right">{{number_format($row->amount,2)}}</td>
							</tr>
							@endforeach

						</tbody>
					</table>
				</div>
			@endif
			<h5 class="float-right">Total Change: <span class="text-success">{{number_format($details->total_amount,2)}}</span></h5>

		</div>

	</div>

	
</div>


@if($details->status == 0)
@include('change-payable.status_modal')
@endif

@include('global.print_modal')

@endsection







@push('scripts')
<script type="text/javascript">
	const BACK_LINK = `<?php echo $back_link; ?>`;
	const ID_CHANGE_PAYABLE = {{$details->id_change_payable ?? 0}};

	
</script>

@endpush