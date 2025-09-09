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
	.card-widgets{
		cursor: pointer;
	}

.bg-ls-card{
	background-color: #344767;
}
/*.crd-gradient{
background: #ECE9E6; 
background: -webkit-linear-gradient(to top, #FFFFFF, #ECE9E6); 
background: linear-gradient(to top, #FFFFFF, #ECE9E6); /

}*/
	@media (max-width: 768px) {
		#tbl_pending_loans{
			white-space: nowrap;
		}
	}
</style>
<style type="text/css">
	.crd-soa:hover{
		background-color: #d2d9df;
		cursor: pointer;
	}
	.txt-desc{
		font-size: 18px;
	}
	.badge-dash{
		font-size: 15px;
	}
	.font-widgets{
		font-size: 20px !important;
	}
	.font-normal{
		font-size: 13px !important;
	}
</style>
<div class="container-fluid" style="font-family: Roboto,Helvetica,Arial,sans-serif !important">

	<div class="container-fluid">
	<div class="row">
		<!-- MY ACCOUNT -->
		<div class="col-lg col-6 px-3">
			<div class="card card-widgets bg-gradient-primary2"  onclick="window.open('/member/view/{{MySession::MemberCode()}}','_blank')">
				<div class="card-statistic-3 p-3">
					<div class="card-icon card-icon-large"><i class="fas fa-user"></i></div>
					<div class="mb-5">
						<h1 class="card-title mb-0 font-widgets">My Account</h1>
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
		<div class="col-lg col-6 px-3">
			<div class="card card-widgets bg-gradient-success2" onclick="window.open('/cbu','_blank')">
				<div class="card-statistic-3 p-3">
					<div class="card-icon card-icon-large"><i class="far fa-money-bill-alt"></i></div>
					<div class="mb-5">
						<h1 class="card-title mb-0 font-widgets">Capital Build Up</h1>
					</div>
					<div class="row align-items-center mb-2 d-flex">
						<div class="col-12 text-right">
							<h4 class="font-widgets">₱{{number_format($total_cbu,2)}}</h4>
						</div>
					</div>

				</div>
			</div>
		</div>

		<!-- LOANS -->
		<div class="col-lg col-6 px-3">
			<div class="card card-widgets bg-gradient-warning2" onclick="window.open('/loan?filter_status=3','_blank')">
				<div class="card-statistic-3 p-3">
					<div class="card-icon card-icon-large"><i class="far fa-list-alt"></i></div>
					<div class="mb-5">
						<h1 class="card-title mb-0 font-widgets">Active Loans ({{$active_loan_count}})</h1>
					</div>
					<div class="row align-items-center mb-2 d-flex">
						<div class="col-12 text-right">
							<h4 class="font-widgets">₱{{number_format($total_loan_balance,2)}}</h4>
						</div>
					</div>

				</div>
			</div>
		</div>	
		<!-- MY DUES -->
		<div class="col-lg col-6 px-3">
			<div class="card card-widgets bg-gradient-danger2" onclick="window.open('/my_dues','_blank')">
				<div class="card-statistic-3 p-3">
					<div class="card-icon card-icon-large"><i class="fas fa-calendar-check"></i></div>
					<div class="mb-5">
						<h1 class="card-title mb-0 font-widgets">My Dues</h1>
					</div>
					<div class="row align-items-center mb-2 d-flex">
						<div class="col-12 text-right">
							<h4 class="font-widgets">₱{{number_format($loan_dues_amount,2)}}</h4>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
	<div class="container-fluid">
		<div class="row mt-2">
			@include('dashboard.carousel_ls')
		</div>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-7 col-12 px-3  mt-4">
				<div class="card card-cust h-100 mb-0" style="zoom:110% !important">
					<div class="card-header text-center card-head-nb py-2 pt-3">
						<h4 class="head_lbl">Capital Build up</h4>
					</div>
					<div class="card-body mt-3">
						<div class="d-flex">
							<?php
							// $total_cbu = $total_cbu -5000;
							?>
							<p class="d-flex flex-column">
								<span class="text-muted text-sm">{{$beginning_label}} : ₱ {{number_format($beg_cbu_bal,2)}} </span><br>
							</p>
							<p class="ml-auto d-flex flex-column text-right">
								<span class="text-muted text-sm">Ending Balance: ₱ {{number_format($total_cbu,2)}} </span>
								<?php
								$beg = $beg_cbu_bal;
								$end = $total_cbu;
								$diff = $end-$beg;
								if($beg > 0){
									$per = ROUND(($diff/$beg)*100,2);
								}else{
									if($diff == 0){
										$per = 0;
									}else{
										$per = 100;
									}
									
								}
								
								$class=($per > 0)?"success":"danger";
								$arrow = ($per > 0)?"up":"down";
								?>
								@if($per != 0)
								<span class="text-{{$class}} text-sm">
									<i class="fas fa-arrow-{{$arrow}}"></i> ₱{{number_format(abs($diff),2)}} ({{abs($per)}}%)
								</span>
								@else
								<span class="text-muted">-</span>
								@endif
							</p>
						</div>


						<div class="position-relative mb-4">
							<canvas id="sales-chart" height="200"></canvas>
						</div>

					</div>
				</div>


				<?php
				function interest_rate_format($val){
					if($val <= 0){
						return '0%';
					}
					return ((floor($val) == $val)?number_format($val,0):$val).'%';
				}
				?>
			</div>
			<div class="col-lg-5 col-12  px-3  mt-4">
				<div class="card card-cust h-100 ">
					<div class="card-header text-center card-head-nb py-2 pt-3">
						<h4 class="head_lbl">Recent Loans</h4>
					</div>
					<div class="card-body p-0">
						
						@include('dashboard.dm_pending_loan')
					</div>
					<div class="card-footer text-center p-2">
						<a href="/loan?filter_status=ALL&filter_date_type=1&filter_start_date={{$st_year}}&filter_end_date={{$end_year}}" class="uppercase" target="_blank">View All Recent Loans ({{$pending_loan_count}})</a>
					</div>
				</div>

			</div>


		</div>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-5 col-12  px-3  mt-4">
				<!-- LOAN SERVICES -->

				<div class="card card-cust h-100">
					<div class="card-header text-center card-head-nb py-2 pt-3">
						<h4 class="head_lbl">Active Loans ({{count($loans)}})</h4>
					</div>

					<div class="card-body">
						@include('dashboard.dm_loan_active')
					</div>

				</div>
			</div>
			<div class="col-lg-7 col-12  px-3 mt-4">
				<!-- LOAN SERVICES -->

				<div class="card card-cust h-100">
					<div class="card-header text-center card-head-nb py-2 pt-3">
						<h4 class="head_lbl">Payments</h4>
					</div>

					<div class="card-body p-0">
						@include('dashboard.dm_payments')
					</div>
					<div class="card-footer text-center p-2">
						<a href="/payments" target="_blank" class="uppercase">View All Payments</a>
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
  			backgroundColor: '#3399ff',
  			borderColor: '#3399ff',
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
  						plugins: {
					legend: {
						display : false,
						position: 'top',
						maintainAspectRatio: true	
					},
				},
				scales: {
					x: {
						
						ticks :ticksStyle
					},
					y: {
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
					}
				}

}
})


})
</script>
@endpush

