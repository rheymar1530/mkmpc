@extends('adminLTE.admin_template')
@section('content')
<style>
	@media (min-width: 1200px) {
		.container{
			max-width: 900px;
		}
	}
	.tbl_in_prod tr>th ,.tbl_fees tr>th,.tbl-inputs tr>th{
		padding: 3px !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
		text-align: center;
	}
	.tbl_in_prod tr>td{
		padding: 2px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
</style>

<?php
function status_class($status){
	$class = "";
	switch($status){
		case 0:
		$class="info";
		break;
		case 1:
		$class ="info";
		break;
		case 2:
		$class = "success";
		break;
		case 5:
		$class="primary";
		break;
		case 8:
		$class="warning";
		break;
		default:
		$class="danger";
		break;
	}

	return $class;
}
?>
<div class="container section_body">
	<?php $back_link = (request()->get('href') == '')?'/investment':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Investment List</a>

	<div class="dropdown float-right dp_menu">
		<button class="btn bg-gradient-danger2 dropdown-toggle" type="button"
		data-toggle="dropdown">
		Options  
	</button>
	<div class="dropdown-menu dp_menu" id="dp_menu">
		@if($allow_edit)
		<?php
		$edit_link = request()->fullUrlWithQuery(['edit' => 1]);
		?>
		<a class="dropdown-item" href="{{$edit_link}}">Edit Investment</a>
		@endif

		@if(MySession::isAdmin())
		@if(($investment_app_details->id_cash_receipt_voucher ?? 0) > 0)

		<a class="dropdown-item" onclick="print_page('/cash_receipt_voucher/print/{{$investment_app_details->id_cash_receipt_voucher}}')">Print CRV (CRV# {{$investment_app_details->id_cash_receipt_voucher}})</a>
		@endif

		@if(($investment_app_details->id_journal_voucher ?? 0) > 0)
		<a class="dropdown-item" onclick="print_page('/journal_voucher/print/{{$investment_app_details->id_journal_voucher}}')">Print JV (JV# {{$investment_app_details->id_journal_voucher}})</a>
		@endif
		
		@if($investment_app_details->status_code == 2)
		<a class="dropdown-item" onclick="print_or()">
			Print OR
		</a>
		@endif

		@endif
		@if($investment_app_details->status_code == 2 && $investment_app_details->close_request == 0 && $investment_app_details->renewal_status == 0)
		<a class="dropdown-item" onclick="close_request()">
			Withdraw Investment (Full)
		</a>

		@if($investment_app_details->is_matured == 1)
		<a class="dropdown-item" onclick="renew_investment()">
			Renew
		</a>
		@endif
		@endif

	</div>
</div>
<div class="card">
	<div class="card-body">
		<div class="card c-border">
			<div class="card-body">
				@if($investment_app_details->close_request >= 1 && $investment_app_details->close_request <= 2)
				<div class="row mt-2">
					<div class="col-md-12 col-12">
						<div class="alert bg-gradient-success2">
							@if($investment_app_details->close_request == 1)
							<p class="mb-0"><i class="fa fa-info"></i>&nbsp;&nbsp;&nbsp;You have a pending withdrawal request amounting to {{number_format($full_w_request,2)}}</p>
							@elseif($investment_app_details->close_request == 2)
							<p class="mb-0">Withdrawal Request has been approved
								<br><b>Amount: </b>{{number_format($full_w_request,2)}}<br>
								Please wait for the disbursement of withdrawal request <a href="/investment-withdrawal/view/{{$close_request_details->id_investment_withdrawal_batch}}" target="_blank">[Reference ID: {{$close_request_details->id_investment_withdrawal_batch}}]</a>
							</p>
							@endif
						</div>
					</div>
				</div>
				@endif
				@if($investment_app_details->renewal_status > 0)
				<div class="row mt-2">
					<div class="col-md-12 col-12">
						<div class="alert bg-gradient-success2">
							<p class="mb-0">
								@if($investment_app_details->renewal_status == 1)
								New Renewal Investment Account
								@else
								Renewed on 
								@endif
								
								<a href="/investment/view/{{$investment_app_details->id_new_investment}}" target="_blank">[INVESTMENT ID# {{$investment_app_details->id_new_investment}}]</a></p>
						</div>
					</div>
				</div>
				@endif
				<div class="text-center">
					<h4 class="head_lbl">Investment</h4>
				</div>


				<div class="row">
					<div class="col-md-12 col-12">
						<p class="mb-0 lbl_color"><b class="lbl_color pr-2">Investment ID: </b> {{$investment_app_details->id_investment}}</p>
						<p class="mb-0 lbl_color"><b class="lbl_color pr-2">Investor: </b>{{$investment_app_details->member_name}} </p>
						<p class="mb-0 lbl_color"><b class="lbl_color pr-2">Application Date: </b> {{$investment_app_details->date_created}}</p>
						<p class="mb-0 lbl_color"><b class="lbl_color pr-2">Status: </b> <span class="badge bg-gradient-{{status_class($investment_app_details->status_code)}} text-sm">{{$investment_app_details->status}}</span></p>


						<p class="mb-0 lbl_color"><b class="lbl_color pr-2">OR Number: </b> {{$investment_app_details->or_number}}</p>

						@if($investment_app_details->status_code ==3 || $investment_app_details->status_code ==4)
						<p class="mb-0 lbl_color"><b class="lbl_color pr-2">Reason: </b><i class="text-muted">{{$investment_app_details->cancellation_remarks}} [Date Cancelled: {{$investment_app_details->date_cancelled}}]</i></p>
						@endif

						@if($investment_app_details->id_prev_investment > 0)
						<p class="mb-0 lbl_color"><b class="lbl_color pr-2">Previous Investment ID: </b> <a href="/investment/view/{{$investment_app_details->id_prev_investment}}" target="_blank">{{$investment_app_details->id_prev_investment}}</a></p>
						@endif
					</div>
				</div>
				@if($show_inv_full_transaction)
				@if($investment_app_details->is_withdrawable == 1 && $investment_app_details->status_code !=5 && $withdrawables > 0 && $investment_app_details->close_request == 0 && $investment_app_details->renewal_status == 0)

				<div class="row mt-2">
					<div class="col-md-12 col-12">
						<div class="alert bg-gradient-success2">
							<h5>Withdrawables</h5>
							@foreach($withdrawables_details as $label=>$value)
							<p class="mb-0">{{$label}}: {{number_format($value,2)}}</p>
							@endforeach

							@if(count($withdrawables_details) > 1)
							<p class="mb-0 font-weight-bold">Total: {{number_format($withdrawables,2)}}</p>
							@endif
						</div>
					</div>
				</div>
				@endif
				@endif
				@if($show_inv_full_transaction)
				<div class="row mt-2 px-2">
					<button class="btn btn-sm bg-gradient-dark text-center col-md-12" onclick="show_withdrawals()">Show Withdrawal Summary</button>
				</div>
				@endif



			</div>
		</div>
		@include('investment.frame_content')
		<div class="card c-border">
			<div class="card-body">
				<div class="text-center">
					<h5 class="lbl_color">Benefactors</h5>
				</div>
				<div class="row mb-3">
					<div class="col-md-12 col-12">
						<table class="table tbl_in_prod table-bordered">
							<thead>
								<tr class="table_header_dblue">
									<th style="width: 5%;"></th>
									<th>Name</th>
									<th>Relationship</th>
									
								</tr>
							</thead>
							<tbody id="benefactor_body">
								@if(count($benefactors) > 0)
								@foreach($benefactors as $c=>$ben)
								<tr class="benefactor_row">
									<td class="text-center ben_counter">{{$c+1}}</td>
									<td>{{$ben->name}}</td>
									<td>{{$ben->relationship}}</td>
									
								</tr>
								@endforeach
								@else
								<tr>
									<tr class="benefactor_row">
										<td colspan="3" class="text-center">No Benefactors</td>
									</tr>
								</tr>
								@endif
							</tbody>
							
						</table>
						
					</div>					
				</div>
			</div>
		</div>

		</div>
		<div class="card-footer">
			@if($allow_status_update || $confirm_close_request)
			<button class="btn btn-md bg-gradient-success2 round_button float-right" onclick="show_status_modal()">Update Status</button>
			@endif
		</div>
	</div>
</div>


<div class="modal fade" id="PrintAlertModal" tabindex="-1" role="dialog" aria-labelledby="PrintAlertModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-body mt-5">
        <div class="text-center">
        	<!-- Confirm button -->
        <button type="button" class="btn btn-primary btn-lg" id="confirmBtn">Print OR</button>
        <!-- Deny button -->
        <button type="button" class="btn btn-danger btn-lg" id="denyBtn">Print CRV</button>
        <!-- Close button -->
        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
</div>

@if($show_inv_full_transaction)
@include('investment.history_modal')
@endif

@if($allow_status_update || $confirm_close_request)
@include('investment.status_modal')

@endif


@include('global.print_modal')


@if(MySession::isAdmin() && $investment_app_details->status_code == 2)
@include('investment.or_modal')
@include('investment.or_frame')
@endif


@if($investment_app_details->status_code == 2 && $investment_app_details->close_request == 0 && $investment_app_details->renewal_status == 0)
@include('investment.close-modal')
@include('investment.renew')
@endif


@endsection

@push('scripts')
<script type="text/javascript">
	const ID_INVESTMENT = '<?php echo $investment_app_details->id_investment ?? 0;?>';
	const ID_CASH_RECEIPT_VOUCHER = '<?php echo $investment_app_details->id_cash_receipt_voucher ?? 0; ?>';
	$(document).ready(function(){
		if($('#dp_menu a').length == 0){
			$('.dp_menu').remove();
		}
		if(localStorage.getItem('show_print_ask') == 1){

			$('#PrintAlertModal').modal('show');

			// Swal.fire({
			// 	title: "",
			// 	text: '',
			// 	showCancelButton : true,
			// 	confirmButtonText: 'Print OR',
			// 	showDenyButton: true,
			// 	denyButtonText: `Print CRV`,
			// 	cancelButtonText: 'Close',
			// 	showConfirmButton : true,     
			// 	allowEscapeKey : false,
			// 	allowOutsideClick: false
			// }).then((result) => {
			// 	if(result.isConfirmed){
			// 		print_or();
			// 	}else if (result.isDenied) {
			// 		print_page(`/cash_receipt_voucher/print/${ID_CASH_RECEIPT_VOUCHER}`);
			// 	}
			// });
			localStorage.removeItem('show_print_ask');			
		}
	});

	$(document).on('click','#confirmBtn',function(){
		print_or();
		// $('#PrintAlertModal').modal('hide');
	})
	$(document).on('click','#denyBtn',function(){
		window.open(`/cash_receipt_voucher/print/${ID_CASH_RECEIPT_VOUCHER}`,'_blank')
		// print_page(`/cash_receipt_voucher/print/${ID_CASH_RECEIPT_VOUCHER}`);
	})
</script>




@endpush