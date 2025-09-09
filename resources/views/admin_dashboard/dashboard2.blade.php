@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.chart_pie tr >td{
		padding: 2px !important;
	}
	.chart_container{
		zoom : 110%;
	}
</style>

<div class="container-fluid" style="font-family: Roboto,Helvetica,Arial,sans-serif !important">
	<div class="row">
		<div class="col-lg-4 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body p-3">
					<div class="row">
						<div class="col-7">
							<label class="lbl_color text-bold text-lg">Revenue</label>
						</div>
						<div class="col-5 text-right">
							<p class="lbl_color mt-1 mb-0">{{$CURRENT_DATE_RANGE}}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<?php
							$revenue_difference = $MonthRevenue['total_revenue'] - $MonthRevenue['prev_revenue'];

							$rev_percentage_dif = number_format(($revenue_difference/$MonthRevenue['prev_revenue'])*100,2);
							?>
							<h4 class="lbl_color">₱{{number_format($MonthRevenue['total_revenue'],2)}}</h4>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<span class="text-{{($rev_percentage_dif < 0)?'danger':'success'}} text-md">
								<i class="fas fa-arrow-{{($rev_percentage_dif < 0)?'down':'up'}}"></i>&nbsp;{{abs($rev_percentage_dif)}}% Since Last Month
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body p-3">
					
					<div class="row">
						<div class="col-7">
							<label class="lbl_color text-bold text-lg">No. of Loan Transactions</label>
						</div>
						<div class="col-5 text-right">
							<p class="lbl_color mt-1 mb-0">{{$CURRENT_DATE_RANGE}}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-7">
							<h4 class="lbl_color">20</h4>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							
							<span class="text-success text-md">
								<i class="fas fa-arrow-up"></i>&nbsp;55% Since Last Month
							</span>
						</div>
					</div>					
					
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body p-3">
					<div class="row">
						<div class="col-7">
							<label class="lbl_color text-bold text-lg">New Member</label>
						</div>
						<div class="col-5 text-right">
							<p class="lbl_color mt-1 mb-0">{{$CURRENT_DATE_RANGE}}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-7 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$CURRENT_DATE_RANGE}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-4">
								<h4 class="head_lbl">Revenue </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-7 col-12">
							<div class="chart-responsive">
								<canvas id="pieRevenue" height="200" class="chart_container"></canvas>
							</div>

						</div>
						<div class="col-md-5 col-12">
							<table style="white-space:nowrap;width: 100%;" class="chart_pie">
								@foreach($MonthRevenue['revenue_list'] as $rev=>$amount)
								<tr>
									<td class="lbl_color">{{$rev}}</td>
									<td class="text-right lbl_color">{{number_format($amount,2)}}</td>
								</tr>
								@endforeach
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-5 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body chart_container">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$CURRENT_YEAR_LABEL}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-2">
								<h4 class="head_lbl">Revenue </h4>
							</div>			
						</div>
					</div>
					<div class="chart-responsive">
						<canvas id="revenue-year-chart" height="350" ></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-7 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$CURRENT_DATE_RANGE}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-4">
								<h4 class="head_lbl">Loan </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-7 col-12">
							<div class="chart-responsive">
								<canvas id="pieLoan" height="200" class="chart_container"></canvas>
							</div>

						</div>
						<div class="col-md-5 col-12">
							<table style="white-space:nowrap;width: 100%;" class="chart_pie">
								@foreach($LoanMonthly['loan_list'] as $rev=>$amount)
								<tr>
									<td class="lbl_color">{{$rev}}</td>
									<td class="text-right lbl_color">{{number_format($amount,2)}}</td>
								</tr>
								@endforeach
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-5 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body chart_container">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$CURRENT_YEAR_LABEL}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-2">
								<h4 class="head_lbl">Loan </h4>
							</div>			
						</div>
					</div>
					<div class="chart-responsive">


						<canvas id="loan-year-chart" height="350" ></canvas>



					</div>


				</div>
			</div>
		</div>
	</div>

</div>
@endsection


@push('scripts')
<script type="text/javascript">

	var pieRevenueCanvas=$('#pieRevenue').get(0).getContext('2d')
	var pieRevenueData= jQuery.parseJSON('<?php echo json_encode($MonthRevenue['chart']) ?? []; ?>');
	var pieOptions={legend:{display:false,position:'top',maintainAspectRatio: false}}
	var pieRevenue=new Chart(pieRevenueCanvas,
		{type:'pie',
		data:pieRevenueData,
		options:pieOptions});

	initialize_revenue_year();
	initialize_pie_loan();
	initialize_loan_year();


	// initialize_revenue_year();

	function randomColor(){
		random = Math.floor(Math.random()*16777215).toString(16);

		return "#"+random;
	}


	function initialize_pie_loan(){
		var pieRevenueCanvas=$('#pieLoan').get(0).getContext('2d')
		var pieRevenueData= jQuery.parseJSON('<?php echo json_encode($LoanMonthly['chart']) ?? []; ?>');
		var pieOptions={legend:{display:false,position:'top',maintainAspectRatio: false}}
		var pieRevenue=new Chart(pieRevenueCanvas,
			{type:'pie',
			
			data:pieRevenueData,
			options:pieOptions});		
	}

	function initialize_revenue_year(){

		var ticksStyle = {
			fontColor: '#495057',
			fontStyle: 'bold'
		}

		var mode = 'index'
		var intersect = true

		var $salesChart = $('#revenue-year-chart');
		var salesChart = new Chart($salesChart, {
			type: 'horizontalBar',
			data: {
				labels: jQuery.parseJSON('<?php echo json_encode($YearRevenue['chart']['label'] ?? []); ?>'),
				datasets: [
				{
					backgroundColor: '#3399ff',
					borderColor: '#3399ff',
					data: jQuery.parseJSON('<?php echo json_encode($YearRevenue['chart']['data'] ?? []); ?>'),
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
          ticks: ticksStyle
      }],
      xAxes: [{
      	display: true,
      	gridLines: {
      		display: false
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
      	
      }]
  }
}
})
}
	function initialize_loan_year(){

		var ticksStyle = {
			fontColor: '#495057',
			fontStyle: 'bold'
		}

		var mode = 'index'
		var intersect = true

		var $salesChart = $('#loan-year-chart');
		var salesChart = new Chart($salesChart, {
			type: 'bar',
			data: {
				labels: jQuery.parseJSON('<?php echo json_encode($YearLoan['chart']['label'] ?? []); ?>'),
				datasets: [
				{
					backgroundColor: '#3399ff',
					borderColor: '#3399ff',
					data: jQuery.parseJSON('<?php echo json_encode($YearLoan['chart']['data'] ?? []); ?>'),
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
}
</script>
@endpush