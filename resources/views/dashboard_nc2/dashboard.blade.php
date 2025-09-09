@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.a_label{
		font-size: 18px;
		font-weight: bold;
	}
	.square {
		height: 100px;
		width: 100px;
		/*background-color: #555;*/

	}
	.row2 {
		width: 100%;
		display: flex;
		flex-direction: row;
		justify-content: left;
	}

	.arr_hor{
		font-size: 50px;
		
		line-height: 70px;
	}
	.arr_vert{
		font-size: 50px;
		top: -10px;

/*		padding-left: 0px;

		position:relative;
		top:-14px;
		margin-bottom: 13px;*/
	}
	.div_sec{
		height: 100px;
		width: 100px;			
	}
	.div_sec_vert{
		height: 40px;
		width: 100px;			
	}
	.div_sec_hor{
		height: 40px;
		width: 40px;			
	}
	.row2 > div{
		margin-right: 10px;
	}


	.div_center{
		text-align: center;
	}
	@media only screen and (max-width: 600px) {
		.arr_hor,.arr_vert {
			top: unset;
			font-size: 30px !important;
		}

	}

	@media only screen and (max-width: 768px) {
/*    .first_o {
        order: 1;
    }
    .second_o {
        order: 2;
    }
    .third_o {
        order: 3;
    }
    .fourth_o{
        order: 4;
    }
    .fifth_o{
        order: 5;
    }
    .sixth_o{
        order: 6;
    }*/
}

.div_dashboard img:hover {
	transform: scale(1.2);
}

.div_dashboard img:hover {
	transition: transform .5s ease;
}
/*	.row [class*='col-'] {
		text-align: center;
		background-color: #cceeee;
		background-clip: content-box;

	}*/

	ul.main_list {
		display: inline-block;
	}

	.div_left{
		text-align: left !important;
	}
	.bg-gradient-primary2 {
		background-image: linear-gradient(195deg, #49a3f1 0%, #1A73E8 100%);
		color: #fff;
		box-shadow: 0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(0 188 212 / 40%) !important;
	}
	.card-cust {
		box-shadow: 0 4px 6px -1px rgb(0 0 0 / 10%), 0 2px 4px -1px rgb(0 0 0 / 6%);
		border: 0 solid rgba(0, 0, 0, 0.125);
		border-radius: 0.75rem;

	}
	.shadow-info {

	}
	.badge-lbl{
		font-size: 15px;
		white-space: normal;
	}
	.badge-lbl a{
		color: #fff;
		text-align: left !important;
	}
	.f-big{
		font-size: 20px;
	}
	.bg-gradient-success2 {
	    background-image: linear-gradient(195deg, #66BB6A 0%, #43A047 100%);
	    color : #fff !important;
	    box-shadow: 0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(76 175 80 / 40%)
	}
	.bg-gradient-danger2{
		background-image: linear-gradient(195deg, #EC407A 0%, #D81B60 100%);
		color : #fff !important;
		box-shadow: 0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(233 30 99 / 40%)
	}
	.bg-gradient-dark {
	    background-image: linear-gradient(195deg, #42424a 0%, #191919 100%);
	    color : #fff !important;
	    box-shadow: 0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(64 64 64 / 40%) !important;
	}
/*	ul.main_list > div{
		text-align: left !important;
	}*/
</style>
<?php


?>
<div class="px-4">
	<h1 class="mt-n4">Dashboard</h1>
	<div class="row div_dashboard">
		<!-- LOAN APPLICATION -->
		<div class="col-lg-5 d-flex  mt-4">
			<div class="card flex-fill card-cust">
				<div class="card-header border-0 div_center">
					<h1 class="mt-n4-5"><span class="badge bg-gradient-success2">Loan Application</span></h1>
				</div>
				<div class="card-body">
					@include('dashboard_nc2.loan_application')
				</div>
			</div>
		</div>
		<!-- END LOAN APPLICATION -->
		<div class="col-lg-7 d-flex  mt-4">
			<div class="card flex-fill">
				<div class="card-header border-0 div_center">
					<h1 class="mt-n4-5"><span class="badge bg-gradient-primary2">Loan Loan Payment</span></h1>
				</div>
				<div class="card-body">
					@include('dashboard_nc2.repayment')
				</div>
			</div>
		</div>
	</div>

	<div class="row div_dashboard" id="2nd_level">
		<div class="col-lg-6">
			<div class="row p-0">
				<div class="col-lg-12 first_o mt-4">
					<div class="card">
						<div class="card-header border-0 div_center">
							
							<h1 class="mt-n4-5"><span class="badge bg-gradient-danger2">Cash Transactions</span></h1>
						</div>
						<div class="card-body">
							<?php
							$content = array(
								array('description'=>'IN','icon'=>'in.png','col'=>2,'width'=>'40px','font-size'=>'25px','class'=>'f-big'),
								array('description'=>'Receive Payment','icon'=>'payment.png','col'=>3,'href'=>'/cash_receipt/add'),
								array('description'=>'CBU','icon'=>'cbu.png','col'=>3,'href'=>'/cbu'),
							);
							?>
							<!-- label -->
							<div class="row">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}" style="font-size: <?php echo $con['font-size']??'' ?> "> <span class="badge badge-lbl bg-gradient-dark {{$con['class']??''}}">{{$con['description']}}</span></a>
								</div>
								@endforeach
							</div>
							<!-- END LABEL -->

							<!-- ICONS -->
							<div class="row">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}"><img src="{{URL::asset('dist/icons/'. $con['icon']  )}}" width="{{ ($con['width'])??80 }}px" height="80px" ></a>
								</div>
								@endforeach
							</div>	

							<?php
							$content = array(
								array('description'=>'OUT','icon'=>'up.png','col'=>2,'width'=>'40px','font-size'=>'25px','class'=>'f-big'),
								array('description'=>'Pay Expenses','icon'=>'expenses.png','col'=>3,'href'=>'/cdv/expenses/create'),
								array('description'=>'Purchases','icon'=>'assets.png','col'=>3,'href'=>'/cdv/asset_purchase/create'),
								array('description'=>'Other Cash Disbursement','icon'=>'cash disbursement others.png','col'=>4,'href'=>'/cdv/others/create'),
							);
							?>			
							<!-- ICONS -->
							<div class="row mt-4">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}"><img src="{{URL::asset('dist/icons/'. $con['icon']  )}}" width="{{ ($con['width'])??80 }}px" height="80px"></a>
								</div>
								@endforeach
							</div>
							<!-- label -->
							<div class="row mt-2">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}" style="font-size: <?php echo $con['font-size']??'' ?> "><span class="badge badge-lbl bg-gradient-dark {{$con['class']??''}}"> {{$con['description']}}</span></a>
								</div>
								@endforeach
							</div>
							<!-- END LABEL -->	
						</div>
					</div>
				</div>
				<div class="col-lg-12 fourth_o mt-4">
					<div class="card">
						<div class="card-header border-0 div_center">
							<h1 class="mt-n4-5"><span class="badge bg-gradient-primary2">Bank</span></h1>
						</div>
						<div class="card-body">
							<?php
							$content = array(
								array('description'=>'Deposit','icon'=>'deposit.png','col'=>3,'href'=>'/bank_transaction/create?def_trans=1'),
								array('description'=>'Withdraw','icon'=>'withdraw.png','col'=>3,'href'=>'/bank_transaction/create?def_trans=2'),
								array('description'=>'Transfer Fund','icon'=>'transfer fund.png','col'=>3,'href'=>'/bank_transaction/create?def_trans=3'),
								array('description'=>"ATM Swipe",'icon'=>'atm swipe.png','col'=>3,'href'=>'/atm_swipe'),
							);
							?>					

							<!-- ICONS -->
							<div class="row mt-1">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}"><img src="{{URL::asset('dist/icons/'. $con['icon']  )}}" width="{{ ($con['width'])??80 }}px" height="80px"></a>
								</div>
								@endforeach
							</div>
							<!-- label -->
							<div class="row mt-2">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}" style="font-size: <?php echo $con['font-size']??'' ?> "><span class="badge badge-lbl bg-gradient-dark"> {{$con['description']}}</span></a>
								</div>
								@endforeach
							</div>
							<!-- END LABEL -->								
						</div>
					</div>	
				</div>
				<div class="col-lg-12 second_o mt-4">
					<div class="card">
						<div class="card-header border-0 div_center">
							<h1 class="mt-n4-5"><span class="badge bg-gradient-success2">Member</span></h1>
						</div>
						<div class="card-body">
							<?php
							$content = array(

								array('description'=>'Add Member','icon'=>'member.png','col'=>3,'href'=>'/member/create'),
								array('description'=>'CBU','icon'=>'cbu.png','col'=>3,'href'=>'/cbu'),
								array('description'=>'Loans','icon'=>'loans.png','col'=>3,'href'=>'/loan'),
								array('description'=>"Member List",'icon'=>'members login.png','col'=>3,'href'=>'/member/list'),
							);
							?>					

							<!-- ICONS -->
							<div class="row mt-1">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}"><img src="{{URL::asset('dist/icons/'. $con['icon']  )}}" width="{{ ($con['width'])??80 }}px" height="80px"></a>
								</div>
								@endforeach
							</div>
							<!-- label -->
							<div class="row mt-2">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}" style="font-size: <?php echo $con['font-size']??'' ?> "> <span class="badge badge-lbl bg-gradient-dark">{{$con['description']}}</span></a>
								</div>
								@endforeach
							</div>
							<!-- END LABEL -->								
						</div>
					</div>	
				</div>

				<div class="col-lg-12 fifth_o mt-4">
					<div class="card">
						<div class="card-header border-0 div_center">
							
							<h1 class="mt-n4-5"><span class="badge bg-gradient-danger2">Employee</span></h1>
						</div>
						<div class="card-body">
							<?php
							$content = array(
								array('description'=>'Add Employee','icon'=>'employee.png','col'=>6,'href'=>'/employee/create'),
								array('description'=>'Payroll','icon'=>'Payroll.png','col'=>6,'href'=>'/payroll/create'),

							);
							?>					

							<!-- ICONS -->
							<div class="row mt-1">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}"><img src="{{URL::asset('dist/icons/'. $con['icon']  )}}" width="{{ ($con['width'])??80 }}px" height="80px"></a>
								</div>
								@endforeach
							</div>
							<!-- label -->
							<div class="row mt-2">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}" style="font-size: <?php echo $con['font-size']??'' ?> "><span class="badge badge-lbl bg-gradient-dark"> {{$con['description']}}</span></a>
								</div>
								@endforeach
							</div>
							<!-- END LABEL -->							
						</div>
					</div>

				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="row p-0">
				<div class="col-lg-12 third_o mt-4" id="div_accounting">
					<div class="card">
						<div class="card-header border-0 div_center">
							<h1 class="mt-n4-5"><span class="badge bg-gradient-success2">Accounting</span></h1>
						</div>
						<div class="card-body">
							<?php
							$content = array(
								array('description'=>'Journal Voucher','icon'=>'journal voucher.png','col'=>3,'href'=>'/journal_voucher/create'),
								array('description'=>'Chart of Accounts','icon'=>'chart of accounts.png','col'=>3,'href'=>'/charts_of_account'),
								array('description'=>'General Ledger','icon'=>'general ledger.png','col'=>3,'href'=>'/general_ledger'),
								array('description'=>'Financial Statement','icon'=>'financial statement.png','col'=>3,'href'=>'/financial_statement/comparative'),
							);
							?>					

							<!-- ICONS -->
							<div class="row mt-1">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}"><img src="{{URL::asset('dist/icons/'. $con['icon']  )}}" width="{{ ($con['width'])??80 }}px" height="80px"></a>
								</div>
								@endforeach
							</div>
							<!-- label -->
							<div class="row mt-2">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}" style="font-size: <?php echo $con['font-size']??'' ?> "> <span class="badge badge-lbl bg-gradient-dark">{{$con['description']}}</span></a>
								</div>
								@endforeach
							</div>
							<!-- END LABEL -->							
						</div>
					</div>

				</div>
				<div class="col-lg-12 fifth_o mt-4" id="div_asset">
					<div class="card">
						<div class="card-header border-0 div_center">
							<h1 class="mt-n4-5"><span class="badge bg-gradient-danger2">Assets</span></h1>
						</div>
						<div class="card-body">

							<?php
							$content = array(
								array('description'=>'Add Asset','icon'=>'assets.png','col'=>4,'href'=>'/asset/asset_adjustment/add'),
								array('description'=>'Depreciation','icon'=>'depreciation.png','col'=>4,'href'=>'/asset_maintenance'),
								array('description'=>'Dispose Asset','icon'=>'asset dispose.png','col'=>4,'href'=>'/asset_disposal/create')
							);
							?>					

							<!-- ICONS -->
							<div class="row mt-1">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}"><img src="{{URL::asset('dist/icons/'. $con['icon']  )}}" width="{{ ($con['width'])??80 }}px" height="80px"></a>
								</div>
								@endforeach
							</div>
							<!-- label -->
							<div class="row mt-2">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-{{$con['col']}} div_center">
									<a href="{{ $con['href'] ?? 'javascript:void(0)' }}" style="font-size: <?php echo $con['font-size']??'' ?> "> <span class="badge badge-lbl bg-gradient-dark">{{$con['description']}}</span></a>
								</div>
								@endforeach
							</div>
							<!-- END LABEL -->							

						</div>
					</div>

				</div>

				<div class="col-lg-12 fourth_o mt-4">
					<div class="card" id="div_rep">
						<div class="card-header border-0 div_center">
							<h1 class="mt-n4-5"><span class="badge bg-gradient-primary2">Reports</span></h1>
						</div>
						<div class="card-body">
							<?php
							$l =5;
							$content = array(

								array('description'=>'','icon'=>'reports journal.png','col'=>6,'sub'=>array(
									['description'=>'Cash-in Summary','href'=>'/journal/report/cash_receipt'],
									['description'=>'Cash-out Summary Journal','href'=>'/journal/report/cash_disbursement'],
									['description'=>'Voucher Summary','href'=>'/voucher_summary'],
								)),
								array('description'=>'','icon'=>'reports CBU.png','col'=>6,'sub'=>array(
									['description'=>'CBU Report','href'=>'/cbu/report'],
									['description'=>'Active Loan Summary','href'=>'/loan'],
									['description'=>'Change Release Summary','href'=>'/change?show_print=1'],
									['description'=>'Interest Paid Per Member Summary (Monthly)','href'=>'/summary/paid_interest'],
									['description'=>'CBU Per Member Summary (Monthly)','href'=>'/cbu/monthly'],
								)),

							);
							?>					
							<!-- ICONS -->
							<div class="row mt-1">
								@foreach($content as $con)
								<div class="col-lg-{{$con['col']}} col-6 div_center p-0">
									<div class="col-lg-12">
										<a href="{{ $con['href'] ?? 'javascript:void(0)' }}"><img src="{{URL::asset('dist/icons/'. $con['icon']  )}}" width="{{ ($con['width'])??80 }}px" height="80px"></a>
									</div>
									<div class="col-lg-12 mt-3">
										<ul class="main_list">
											@for($i=0;$i<$l;$i++)
											@if(isset($con['sub'][$i]['description']))
											<li class="mt-1">
												<div class="row">

													<div class="col-lg-12 div_left">
														<a class="a_label" style="font-size: <?php echo $con['font-size']??'' ?>" href="{{ $con['sub'][$i]['href'] ?? '' }}"><span class="badge badge-lbl bg-gradient-dark"> {{$con['sub'][$i]['description'] ?? ''}}</span></a>
													</div>

												</div>
											</li>
											@endif
											@endfor	
										</ul>
									</div>
								</div>
								@endforeach
							</div>
							<!-- labels -->	


							<!-- END LABEL -->								
						</div>
					</div>	
				</div>


			</div>
		</div>
	</div>
	<div class="row div_dashboard">
		<div class="col-lg-12 fourth_o mt-4">
			<div class="card">
				<div class="card-header border-0 div_center">
					<h1 class="mt-n4-5"><span class="badge bg-gradient-success2">Maintenance</span></h1>
				</div>
				<div class="card-body">
					<?php
					$l =4;
					$content = array(

						array('description'=>'','icon'=>'chart of accounts.png','col'=>3,'sub'=>array(
							['description'=>'COA Category','href'=>'/maintenance/chart_account_category'],
							['description'=>'COA Line Item','href'=>'/maintenance/chart_account_line_item'],
							['description'=>'COA Account Type','href'=>'/maintenance/chart_account_type'],
						)),
						array('description'=>'','icon'=>'Payroll.png','col'=>3,'sub'=>array(
							['description'=>'Position','href'=>'/maintenance/position'],
							['description'=>'Department','href'=>'/maintenance/department'],
							['description'=>'Allowances','href'=>'/maintenance/allowance'],
							['description'=>'Employment Status','href'=>'/maintenance/employment_status'],
						)),
						array('description'=>'','icon'=>'Maintenance others.png','col'=>3,'sub'=>array(
							['description'=>'Payment Type','href'=>'/maintenance/payment_type'],
							['description'=>'Supplier','href'=>'/supplier'],
							['description'=>'Banks','href'=>'/maintenance/bank'],
							['description'=>'Branch','href'=>'/maintenance/branch'],
						)),
						array('description'=>'','icon'=>'Loan Services.png','col'=>3,'sub'=>array(
							['description'=>'Loan Service','href'=>'/loan_service'],
							['description'=>'Penalty & Charges','href'=>'/charges'],
							['description'=>'Fees','href'=>'/maintenance/loan_fees'],

						)),

					);
					?>					
					<!-- ICONS -->
					<div class="row mt-1">
						@foreach($content as $con)
						<div class="col-lg-{{$con['col']}} col-6 div_center p-0">
							<div class="col-lg-12">
								<a href="{{ $con['href'] ?? 'javascript:void(0)' }}"><img src="{{URL::asset('dist/icons/'. $con['icon']  )}}" width="{{ ($con['width'])??80 }}px" height="80px"></a>
							</div>
							<div class="col-lg-12 mt-3">
								<ul class="main_list">
									@for($i=0;$i<$l;$i++)

									@if(isset($con['sub'][$i]['description']))
									<li>
										<div class="row">
											<div class="col-lg-12 div_left">
												<a class="a_label" style="font-size: <?php echo $con['font-size']??'' ?>" href="{{ $con['sub'][$i]['href'] ?? '' }}"><span class="badge badge-lbl bg-gradient-dark"> {{$con['sub'][$i]['description'] ?? ''}}</span></a>
											</div>

										</div>
									</li>

									@endif
									@endfor	
								</ul>
							</div>
						</div>
						@endforeach
					</div>
					<!-- labels -->	
					<!-- END LABEL -->


					<?php
					$l =4;
					$content = array(

						// array('description'=>'','icon'=>'Maintenance others.png','col'=>3,'sub'=>array(
						// 	['description'=>'Payment Type','href'=>'/maintenance/payment_type'],
						// 	['description'=>'Supplier','href'=>'/supplier'],
						// 	['description'=>'Banks','href'=>'/maintenance/bank'],
						// 	['description'=>'Branch','href'=>'/maintenance/branch'],
						// )),
						// array('description'=>'','icon'=>'Loan Services.png','col'=>3,'sub'=>array(
						// 	['description'=>'Loan Service','href'=>'/loan_service'],
						// 	['description'=>'Penalty & Charges','href'=>'/charges'],
						// 	['description'=>'Fees','href'=>'/maintenance/loan_fees'],

						// )),

					);
					?>					
					<!-- ICONS -->
					<div class="row mt-3">
						@foreach($content as $con)
						<div class="col-lg-{{$con['col']}} col-6 div_center p-0">
							<div class="col-lg-12">
								<a href="{{ $con['href'] ?? 'javascript:void(0)' }}"><img src="{{URL::asset('dist/icons/'. $con['icon']  )}}" width="{{ ($con['width'])??80 }}px" height="80px"></a>
							</div>
							<div class="col-lg-12 mt-3">
								<ul class="main_list">
									@for($i=0;$i<$l;$i++)
									@if(isset($con['sub'][$i]['description']))
									<li>
										<div class="row">

											<div class="col-lg-12 div_left">
												<a class="a_label" style="font-size: <?php echo $con['font-size']??'' ?>" href="{{ $con['sub'][$i]['href'] ?? '' }}"> {{$con['sub'][$i]['description'] ?? ''}}</a>
											</div>

										</div>
									</li>
									@endif
									@endfor	

								</ul>
							</div>
						</div>
						@endforeach
					</div>
					<!-- labels -->	
					<!-- END LABEL -->




				</div>
			</div>	
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	$(window).resize(function() {
		// resize();
	});	
	$(document).ready(function(){
		resize();
		$('.div_dashboard').find('a').each(function(){
			var href=$(this).attr('href')
			if(href != "javascript:void(0)"){
				$(this).attr("target","_blank");
			}
		})

	})

	function resize(){
		$('#div_rep').height($('#2nd_level').height() - ($('#div_accounting').height() + $('#div_asset').height()+87)+'px')
	}
</script>

@endpush