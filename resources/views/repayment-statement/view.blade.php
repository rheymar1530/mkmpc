@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.tbl_loan th, .tbl_loan td{
		padding: 0.2rem;
		font-size: 0.85rem;
	}
	.tbl_loan td.in{
		padding: 0;
	}
	td.in input{
		height: 27px !important;
		font-size: 0.8rem;
	}
	.tbl_loan th{
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
	.tbl_loan tfoot td {
		background: #666;
		color: white;
	}
</style>
<?php
$paymentModes=array(
	4=>'Check',1=>'Cash'
);
$check_types = [1=>"On-date",2=>"Post dated"];

?>
<div class="container main_form" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/repayment-statement':request()->get('href'); ?>

	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment Statement List</a>



	@if($allow_post && $details->status == 0)
	<button class="btn btn-warning btn-sm round_button float-right" onclick="show_status_modal()"><i class="fa fa-times"></i>&nbsp;Cancel Loan Payment Statement</button>

	@endif



	@if($details->status == 0)
	<a class="btn btn-primary btn-sm round_button float-right mr-2" href="/repayment-statement/edit/{{$details->id_repayment_statement}}?href={{$back_link}}"><i class="fa fa-print"></i>&nbsp;Edit Statement</a>
	@endif

	<button class="btn btn-danger btn-sm round_button float-right mr-2" onclick="print_page('/repayment-statement/print/generated/{{$details->id_repayment_statement}}')"><i class="fa fa-print"></i>&nbsp;Print Statement</button>


<!-- 	<div class="btn-group float-right">
		<button type="button" class="btn btn-sm  bg-gradient-danger2 dropdown-toggle mr-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i>&nbsp;
			Print
		</button>
		<div class="dropdown-menu">
			<a class="dropdown-item" href="javascript:void(0)" onclick="print_page('/repayment-statement/print/generated/{{$details->id_repayment_statement}}')">Print Statement</a>
			if($details->status == 1)
			<a class="dropdown-item" onclick="print_page('/repayment-statement/print/remitted/{{$details->id_repayment_statement}}')">Print Remitted Statement</a>
			endif
		</div>
	</div> -->
	<div class="card">
		<div class="card-body px-5 py-4">
			<div class="text-center mb-3">
				<h4 class="head_lbl">Loan Payment Statement @if($details->status > 0)<span class="badge bg-gradient-{{$details->status_class}}2 text-md">{{$details->status_description}}</span> @endif</h4>
			</div>
			<div class="card c-border">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">No.: {{$details->month_year}}-{{$details->id_repayment_statement}}</span>
								<span class="text-sm  font-weight-bold lbl_color">{{$details->group_}}: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->baranggay_lgu}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Treasurer: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->treasurer}}</span></span>
							</div>		
						</div>
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">

								<span class="text-sm  font-weight-bold lbl_color">Statement Date: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->statement_date}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Month Due: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->month_due}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Total Amount: <span class="ms-sm-2 font-weight-normal ml-2" id="amount_due">0.00</span></span>
							</div>		
						</div>
					</div>
					@if($details->status == 10)
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">Cancellation Reason: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->status_remarks}} <i>({{$details->status_date}})</i></span></span>
							</div>
						</div>
					</div>
					@endif
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-md-12">
					<?php
					$GLOBALS['total'] = 0;

					$keysTotal = ['previous','current','surcharge'];

					$totalsKey = [];
					?>
					<div class="table">
						<table class="table table-bordered table-head-fixed tbl_loan w-100" id="tbl_loan">
							<thead>
								<tr class="text-center">
									<th>BORROWER'S NAME</th>
									<th>LOAN TYPE</th>
									<th>PREVIOUS<br>BALANCE</th>
									<th>CURRENT<br>BALANCE</th>
									<th>SURCHARGE</th>
									<th>AMOUNT</th>
								</tr>
							</thead>
							@foreach($loans as $id_member=>$loan)
							<tbody class="borders bmember" data-member-id="{{$id_member}}">
								@foreach($loan as $c=>$lo)
								<tr class="rloan" data-loan="{{$lo->loan_token}}" data-id="{{$lo->id_loan}}" loan-due="{{$lo->statement_amount}}" rp-id="{{$lo->id_repayment_statement_details}}">
									@if($c == 0)
									<td class="font-weight-bold nowrap" rowspan="{{count($loan)}}"><i>{{$lo->member}}</i></td>
									@endif
									<td class="nowrap"><sup><a href="/loan/application/approval/{{$lo->loan_token}}" target="_blank">[{{$lo->id_loan}}] </a></sup>{{$lo->loan_name}}</td>
									<td class="font-weight-bold nowrap text-right">{{number_format($lo->previous,2)}}</td>
									<td class="font-weight-bold nowrap text-right">{{number_format($lo->current,2)}}</td>
									<td class="font-weight-bold nowrap text-right">{{number_format($lo->surcharge,2)}}</td>
									<td class="text-right ">{{number_format($lo->statement_amount,2)}}</td>
									<?php $GLOBALS['total'] += $lo->statement_amount; ?> 
								</tr>

								<?php
									foreach($keysTotal as $k){
										$totalsKey[$k] = ($totalsKey[$k] ?? 0) + $lo->{$k};
									}
								?>
								@endforeach
							</tbody>
							@endforeach
							<tfoot>
								<td colspan="2">Grand Total</td>
								@foreach($keysTotal as $k)
								<td class="text-right">{{number_format($totalsKey[$k],2)}}</td>
								@endforeach
								<td class="text-right">{{number_format($GLOBALS['total'],2)}}</td>
								

							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>


	</div>
</div>


@include('global.print_modal')
@if($allow_post)

@include('repayment-statement.status_modal')
@endif
@endsection


@push('scripts')


<script type="text/javascript">
	const BACK_LINK = `<?php echo $back_link; ?>`;
	const CheckDiv = $('#div_check div').detach();
	const ID_REPAYMENT_STATEMENT = {{$details->id_repayment_statement ?? 0}};


</script>


@if($details->status == 0)
<script type="text/javascript">
	$(document).ready(function(){
		if(window.localStorage.getItem("for_print") == ID_REPAYMENT_STATEMENT){
			window.localStorage.removeItem("for_print");
			print_page(`/repayment-statement/print/generated/${ID_REPAYMENT_STATEMENT}`);
		}
	})
</script>
@elseif($details->status == 1)
<script type="text/javascript">
	$(document).ready(function(){
	
		if(window.localStorage.getItem("for_print") == ID_REPAYMENT_STATEMENT){
			print_page(`/repayment-statement/print/remitted/${ID_REPAYMENT_STATEMENT}`);
			window.localStorage.removeItem("for_print");
			
		}
	})
</script>
@endif

@endpush