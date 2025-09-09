@extends('adminLTE.admin_template')
@section('content')

<style type="text/css">
	.card-widgets {
		background-color: #fff;
		border-radius: 10px;
		border: none;
		position: relative;
		margin-bottom: 30px;
		box-shadow: 0 0.46875rem 2.1875rem rgba(90,97,105,0.1), 0 0.9375rem 1.40625rem rgba(90,97,105,0.1), 0 0.25rem 0.53125rem rgba(90,97,105,0.12), 0 0.125rem 0.1875rem rgba(90,97,105,0.1);
	}
	.l-bg-cherry {
		background: linear-gradient(to right, #493240, #f09) !important;
		color: #fff;
	}

	.l-bg-blue-dark {
		background: linear-gradient(to right, #373b44, #4286f4) !important;
		color: #fff;
	}

	.l-bg-green-dark {
		background: linear-gradient(to right, #0a504a, #38ef7d) !important;
		color: #fff;
	}

	.l-bg-orange-dark {
		background: linear-gradient(to right, #a86008, #ffba56) !important;
		color: #fff;
	}

	.card .card-statistic-3 .card-icon-large .fas, .card .card-statistic-3 .card-icon-large .far, .card .card-statistic-3 .card-icon-large .fab, .card .card-statistic-3 .card-icon-large .fal {
		font-size: 90px;
	}

	.card .card-statistic-3 .card-icon {
		text-align: center;
		line-height: 50px;
		margin-left: 15px;
		color: #000;
		position: absolute;
		right: 10px;
		top: 20px;
		opacity: 0.1;
	}

	.l-bg-cyan {
		background: linear-gradient(135deg, #289cf5, #84c0ec) !important;
		color: #fff;
	}


	.l-bg-cyan {
		background: linear-gradient(135deg, #289cf5, #84c0ec) !important;
		color: #fff;
	}
	.card-title{
		font-size: 25px !important;
	}
	.card-title2{
		font-size: 25px !important;
	}
	.l-bg-blue{
		background: -webkit-linear-gradient(to left, #396afc, #2948ff); 
		background: linear-gradient(to left, #396afc, #2948ff);
		color: #fff;
	}

	.l-bg-green{
		background: #11998e;
		background: -webkit-linear-gradient(to right, #38ef7d, #11998e); 
		background: linear-gradient(to right, #38ef7d, #11998e);
		color: #fff;
	}
	.l-bg-orange{
		background: #FF512F;
		background: -webkit-linear-gradient(to right, #F09819, #FF512F); 
		background: linear-gradient(to right, #F09819, #FF512F);
		color: #fff;
	}
	.l-bg-orange-light{
		background: #FF8008;
		background: -webkit-linear-gradient(to right, #FFC837, #FF8008);
		background: linear-gradient(to right, #FFC837, #FF8008);
		color: #fff;
	}

	.l-bg-torq{
		background: #4CB8C4;
		background: -webkit-linear-gradient(to right, #3CD3AD, #4CB8C4);
		background: linear-gradient(to right, #3CD3AD, #4CB8C4);

		color: #fff;
	}
	.tbl_dash td{
		vertical-align: top !important;
		padding:5px;
	}
	.tbl_dash th{
		vertical-align: top !important;
		padding: 5px;
	}

	.no-border-bottom{
		border-bottom: none !important;
	}
	.no-border-top{
		border-top: none !important;
	}
	.tbl_badge{
		font-size: 15px;
	}

</style>
<div class="row">
	<!-- MY ACCOUNT -->
	<div class="col-lg col-6">
		<div class="card card-widgets l-bg-blue">
			<div class="card-statistic-3 p-3">
				<div class="card-icon card-icon-large"><i class="fas fa-user"></i></div>
				<div class="mb-5">
					<h1 class="card-title mb-0">My Account</h1>
				</div>
				<div class="row align-items-center mb-2 d-flex">
					<div class="col-12 text-right">
						<h4>&nbsp;</h4>
					</div>
				</div>
				
			</div>
		</div>
	</div>

	<!-- CAPITAL BUILDUP -->
	<div class="col-lg col-6">
		<div class="card card-widgets l-bg-green">
			<div class="card-statistic-3 p-3">
				<div class="card-icon card-icon-large"><i class="far fa-money-bill-alt"></i></div>
				<div class="mb-5">
					<h1 class="card-title mb-0">Capital Build Up</h1>
				</div>
				<div class="row align-items-center mb-2 d-flex">
					<div class="col-12 text-right">
						<h4>₱ {{number_format($total_cbu,2)}}</h4>
					</div>
				</div>
				
			</div>
		</div>
	</div>

	<!-- LOANS -->
	<div class="col-lg col-6">
		<div class="card card-widgets l-bg-orange">
			<div class="card-statistic-3 p-3">
				<div class="card-icon card-icon-large"><i class="far fa-list-alt"></i></div>
				<div class="mb-5">
					<h1 class="card-title mb-0">Active Loans</h1>
				</div>
				<div class="row align-items-center mb-2 d-flex">
					<div class="col-12 text-right">
						<h4>{{$active_loan_count}}</h4>
					</div>
				</div>
				
			</div>
		</div>
	</div>	
	<!-- MY DUES -->
	<div class="col-lg col-6">
		<div class="card card-widgets l-bg-orange-light">
			<div class="card-statistic-3 p-3">
				<div class="card-icon card-icon-large"><i class="fas fa-calendar-check"></i></div>
				<div class="mb-5">
					<h1 class="card-title mb-0">My Dues</h1>
				</div>
				<div class="row align-items-center mb-2 d-flex">
					<div class="col-12 text-right">
						<h4>₱ {{number_format($loan_dues_amount,2)}}</h4>
					</div>
				</div>
				
			</div>
		</div>
	</div>

	<!-- SAVINGS AND INVESTMENT -->
<!-- 	<div class="col-lg col-6">
		<div class="card card-widgets l-bg-torq">
			<div class="card-statistic-3 p-3">
				<div class="card-icon card-icon-large"><i class="fas fa-wallet"></i></div>
				<div class="mb-5">
					<h1 class="card-title mb-0" style="font-size:20px !important">Savings & Investment</h1>
				</div>
				<div class="row align-items-center mb-2 d-flex">
					<div class="col-12 text-right">
						<h4>0 </h4>
					</div>
				</div>
				
			</div>
		</div>
	</div> -->
</div>

<div class="row p-0">
	<div class="col-lg-6 col-12 p-0">
		<div class="col-lg-12 col-12" style="zoom:110% !important">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Capital Build Up</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-minus"></i>
						</button>

					</div>
				</div>
				<div class="card-body">
					<div class="d-flex">
						<p class="d-flex flex-column">
							<span class="text-bold text-lg">₱ {{number_format($total_cbu,2)}}</span>
							<!-- <span>Sales Over Time</span> -->
						</p>
						<!-- <p class="ml-auto d-flex flex-column text-right">
							<span class="text-success">
								<i class="fas fa-arrow-up"></i> 33.1%
							</span>
							<span class="text-muted">Since last month</span>
						</p> -->
					</div>
					<!-- /.d-flex -->

					<div class="position-relative mb-4">
						<canvas id="sales-chart" height="200"></canvas>
					</div>

					<div class="d-flex flex-row justify-content-end">
<!-- 						<span class="mr-2">
							<i class="fas fa-square text-primary"></i> This year
						</span>

						<span>
							<i class="fas fa-square text-gray"></i> Last year
						</span> -->
					</div>
				</div>
			</div>
		</div>

		<!-- LOAN SERVICES -->
		<div class="col-lg-12 col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">List of Loan Products</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>

				<div class="card-body p-0">
					<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
						<table class="table table-head-fixed table-hover tbl_dash">
							<thead>
								<tr class="table_header_dblue">
									<th class="table_header_dblue text-center"></th>
									<th class="table_header_dblue text-center">Loan Service</th>
									<th class="table_header_dblue text-center">Amount</th>
									<th class="table_header_dblue text-center">Term/Period</th>
									<th class="table_header_dblue text-center">Interest Rate</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$ls_count = 1;
								?>
								@foreach($loan_services as $loan_service)
								<?php
								$ls_length = count($loan_service);
								?>
								@foreach($loan_service as $c=>$ls)
								<tr>
									@if($c == 0)
									<td class="text-center" rowspan="{{count($loan_service)}}">{{$ls_count}}</td>
									<td rowspan="{{count($loan_service)}}">{{$loan_service[0]->name}}</td>
									<td rowspan="{{count($loan_service)}}">{{$loan_service[0]->amount}}</td>
									@endif

									<td class="pl-0 {{($c==0)?'':'no-border-bottom no-border-top'}}"><li>{{$ls->term_period}}</li></td>
									<td class="pl-0 {{($c==0)?'':'no-border-bottom no-border-top'}}">{{$ls->interest_rate}}%</td>
								</tr>
								@endforeach
								<?php $ls_count++; ?>
								@endforeach
							</tbody>

						</table>
					</div>
				</div>

			</div>
		</div>
	</div>

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
	<div class="col-lg-6 col-12 p-0">
		<!-- LOAN SERVICES -->
		<div class="col-lg-12 col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">List of Loans</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>

				<div class="card-body p-0">
					<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
						<table class="table table-head-fixed table-hover tbl_dash">
							<thead>
								<tr class="table_header_dblue">
									<th class="table_header_dblue text-center"></th>
									<th class="table_header_dblue text-center">Date</th>
									<th class="table_header_dblue text-center">Loan Service</th>
									<th class="table_header_dblue text-center">Principal Amount</th>
									<th class="table_header_dblue text-center">Loan Balance</th>
									<th class="table_header_dblue text-center">Status</th>
								</tr>
							</thead>
							<tbody>
								@foreach($loans as $c=>$loan)
								<tr>
									<td class="text-center">{{$c+1}}</td>
									<td>{{$loan->loan_date}}</td>
									<td>{{$loan->loan_service_name}}</td>
									<td class="text-right">{{number_format($loan->principal_amount,2)}}</td>
									<td class="text-right">{{number_format($loan->loan_balance,2)}}</td>
									<td><span class="badge badge-{{$status_badge[$loan->status_code]}} tbl_badge">{{$loan->loan_status_dec}}</span></td>
									
								</tr>
								@endforeach
							</tbody>

						</table>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>







@endsection


@push('scripts')
<script type="text/javascript">
	const cbu_g_label = jQuery.parseJSON('<?php echo json_encode($cbu_graph_label);?>');
	const cbu_g_val = jQuery.parseJSON('<?php echo json_encode($cbu_graph_value);?>');

	$(function () {
		'use strict'
		
		var ticksStyle = {
			fontColor: '#495057',
			fontStyle: 'bold'
		}

		var mode = 'index'
		var intersect = true

		var $salesChart = $('#sales-chart')
  // eslint-disable-next-line no-unused-vars
  var salesChart = new Chart($salesChart, {
  	type: 'bar',
  	data: {
  		labels: cbu_g_label,
  		datasets: [
  		{
  			backgroundColor: '#007bff',
  			borderColor: '#007bff',
  			data: cbu_g_val
  		}
  		]
  	},
  	options: {
  		maintainAspectRatio: false,
  		tooltips: {
  			mode: mode,
  			intersect: intersect
  		},
  		hover: {
  			mode: mode,
  			intersect: intersect
  		},
  		legend: {
  			display: false
  		},
  		scales: {
  			yAxes: [{
          // display: false,
          gridLines: {
          	display: true,
          	lineWidth: '4px',
          	color: 'rgba(0, 0, 0, .2)',
          	zeroLineColor: 'transparent'
          },
          ticks: $.extend({
          	beginAtZero: true,
            // Include a dollar sign in the ticks
            callback: function (value) {
            	if (value >= 1000) {
            		value /= 1000
            		value += 'k'
            	}
            	return '₱' + value
            }
          }, ticksStyle)
        }],
        xAxes: [{
        	display: true,
        	gridLines: {
        		display: false
        	},
        	ticks: ticksStyle
        }]
      }
    }
  })


})
</script>
@endpush

