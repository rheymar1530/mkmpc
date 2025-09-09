@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.chart_pie tr >td{
		padding: 2px !important;
	}
	.chart_container{
		zoom : 1.11 !important;
	}
	.vertical-center,.vertical-center th,.vertical-center td{
		vertical-align: middle;
		
	}
	.dashboard-table td,.dashboard-table th{
		padding-top: 5px;
		padding-bottom: 5px;
	}

</style>
<?php
function check_negative($val){
	$col_val = number_format(abs($val),2);


	// if($val == 0){
	// 	return '0';
	// }

	if($val < 0){
		$col_val = "($col_val)";
	}

	return $col_val;
}
?>
<div class="container-fluid" style="font-family: Roboto,Helvetica,Arial,sans-serif !important">
	<!-- REVENUE -->
	<div class="row">
		<div class="col-lg-7 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_MONTH}}</p>
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
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_RANGE}}</p>
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
	<!-- EXPENSES -->
	<div class="row">
		<div class="col-lg-7 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_RANGE}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-4">
								<h4 class="head_lbl">Expenses </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-7 col-12">
							<div class="chart-responsive">
								<canvas id="pieExpenses" height="200" class="chart_container"></canvas>
							</div>

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
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_RANGE}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-2">
								<h4 class="head_lbl">Expenses </h4>
							</div>			
						</div>
					</div>
					<div class="chart-responsive">
						<canvas id="expenses-year-chart" height="350" ></canvas>
					</div>
				</div>
			</div>
		</div>	
	</div>
	<!-- NET SURPLUS -->
	<div class="row">
		<div class="col-lg-6 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_MONTH}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-4">
								<h4 class="head_lbl">Net Surplus </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-7 col-12">
							<div class="chart-responsive">
								<canvas id="pieNet" height="200" class="chart_container"></canvas>
							</div>

						</div>
						
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-6 col-12 mb-3">
			<div class="card h-100 ">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_RANGE}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-2">
								<h4 class="head_lbl">Net Surplus </h4>
							</div>			
						</div>
					</div>
					<div class="chart-responsive chart_container">
						<canvas id="net-year"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- ASSETS -->
	<div class="row">
		<div class="col-lg-6 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-4">
								<h4 class="head_lbl text-xl">Assets </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-12 col-12">
							<table class="table" style="border-collapse: collapse;">
								<tr class="vertical-center lbl_color text-sm text-center">
									<th>Account</th>
									<th>Current<br>Yr.2022</th>
									<th>Previous<br>Yr.2021</th>
									<th>Changes<br>Last Year</th>
								</tr>
								<tbody>

									@foreach($ASSETS_LAST_2 as $row)
									<tr class="text-sm text-muted vertical-center dashboard-table">
										<td>{{$row->description}}</td>
										@foreach($year_key as $k)
										<td class="text-right">{{ check_negative($row->{$k}) }}</td>
										@endforeach
										<?php
										$previous = $row->{$year_key[1]};
										$current = $row->{$year_key[0]};
											// $previous = 1000000;
										$changes = $current - $previous;

										if($previous == 0 && $current != 0){
											$percentage = 100;
										}else{
											if($changes == 0){
												$percentage = 0;

											}else{
												$percentage = ROUND(($changes/$previous)*100,2);
											}
										}


										?>
										<td class="text-center">
											@if(abs($percentage) > 0)
											<span class="vertical-center text-{{($percentage < 0)?'danger':'success'}} text-md">{{abs($percentage)}}% 
												<i class="fas fa-arrow-{{($percentage < 0)?'down':'up'}}"></i>&nbsp;
											</span>
											@else
											-
											@endif
										</td>
									</tr>
									@endforeach
									
								</tbody>

							</table>

						</div>
						
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-6 col-12 mb-3">
			<div class="card h-100 ">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_5}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-2">
								<h4 class="head_lbl">Total Assets </h4>
							</div>			
						</div>
					</div>
					<div class="chart-responsive chart_container h-100">
						<canvas id="asset-year"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- LOAN RECEIVABLE -->

	<!-- CASH FLOW -->
	<div class="row">
		<div class="col-lg-5 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_MONTH}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-4">
								<h4 class="head_lbl">Cash Flow </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-7 col-12">
							<div class="chart-responsive">
								<canvas id="current-month-cashflow" height="200" class="chart_container"></canvas>
							</div>

						</div>
						
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-7 col-12 mb-3">
			<div class="card h-100 ">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_RANGE}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-2">
								<h4 class="head_lbl">Cash Flow </h4>
							</div>			
						</div>
					</div>
					<div class="chart-responsive chart_container">
						<canvas id="cash-flow-year"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- LIABILITIES -->
	<div class="row">
		<div class="col-lg-5 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">

						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-4">
								<h4 class="head_lbl text-xl">Liabilities </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-12 col-12">
							<table class="table" style="border-collapse: collapse;">
								<tr class="vertical-center lbl_color text-sm text-center">
									<th>Account</th>
									<th>Current<br>Yr.2022</th>
									<th>Previous<br>Yr.2021</th>
									<th>Changes<br>Last Year</th>
								</tr>
								<tbody>

									@foreach($LIABILITIES_LAST_2 as $row)
									<tr class="text-sm text-muted vertical-center dashboard-table">
										<td>{{$row->description}}</td>
										@foreach($year_key as $k)
										<td class="text-right">{{ check_negative($row->{$k}) }}</td>
										@endforeach
										<?php
										$previous = $row->{$year_key[1]};
										$current = $row->{$year_key[0]};
											// $previous = 1000000;
										$changes = $current - $previous;

										if($previous == 0 && $current != 0){
											$percentage = 100;
										}else{
											if($changes == 0){
												$percentage = 0;

											}else{
												$percentage = ROUND(($changes/$previous)*100,2);
											}
										}


										?>
										<td class="text-center">
											@if(abs($percentage) > 0)
											<span class="vertical-center text-{{($percentage < 0)?'danger':'success'}} text-md">{{abs($percentage)}}% 
												<i class="fas fa-arrow-{{($percentage < 0)?'down':'up'}}"></i>&nbsp;
											</span>
											@else
											-
											@endif
										</td>
									</tr>
									@endforeach
									
								</tbody>

							</table>

						</div>
						
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-7 col-12 mb-3">
			<div class="card h-100 ">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_5}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-2">
								<h4 class="head_lbl">Total Liabilities </h4>
							</div>			
						</div>
					</div>
					<div class="chart-responsive chart_container">
						<canvas id="liabilities-year"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- EQUITIES -->
	<div class="row">
		<div class="col-lg-5 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">

						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-4">
								<h4 class="head_lbl text-xl">Equities </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-12 col-12">
							<table class="table" style="border-collapse: collapse;">
								<tr class="vertical-center lbl_color text-sm text-center">
									<th>Account</th>
									<th>Current<br>Yr.2022</th>
									<th>Previous<br>Yr.2021</th>
									<th>Changes<br>Last Year</th>
								</tr>
								<tbody>

									@foreach($EQUITIES_LAST_2 as $row)
									<tr class="text-sm text-muted vertical-center dashboard-table">
										<td>{{$row->description}}</td>
										@foreach($year_key as $k)
										<td class="text-right">{{ check_negative($row->{$k}) }}</td>
										@endforeach
										<?php
										$previous = $row->{$year_key[1]};
										$current = $row->{$year_key[0]};
											// $previous = 1000000;
										$changes = $current - $previous;

										if($previous == 0 && $current != 0){
											$percentage = 100;
										}else{
											if($changes == 0){
												$percentage = 0;

											}else{
												$percentage = ROUND(($changes/$previous)*100,2);
											}
										}


										?>
										<td class="text-center">
											@if(abs($percentage) > 0)
											<span class="vertical-center text-{{($percentage < 0)?'danger':'success'}} text-md">{{abs($percentage)}}% 
												<i class="fas fa-arrow-{{($percentage < 0)?'down':'up'}}"></i>&nbsp;
											</span>
											@else
											-
											@endif
										</td>
									</tr>
									@endforeach
									
								</tbody>

							</table>

						</div>
						
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-7 col-12 mb-3">
			<div class="card h-100 ">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_5}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-2">
								<h4 class="head_lbl">Total Equities </h4>
							</div>			
						</div>
					</div>
					<div class="chart-responsive chart_container">
						<canvas id="equities-year"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<!-- STAT FUND -->
		<div class="col-lg-5 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">As of {{$FILTER_MONTH}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-4">
								<h4 class="head_lbl">Statutory Fund </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-7 col-12">
							<div class="chart-responsive">
								<canvas id="current-statutory-fund" height="200" class="chart_container"></canvas>
							</div>

						</div>
						
					</div>
				</div>
			</div>
		</div>		
		<!-- CBU FUND -->
		<div class="col-lg-7 col-12 mb-3">
			<div class="card h-100 ">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_5}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-2">
								<h4 class="head_lbl">Capital Share </h4>
							</div>			
						</div>
					</div>
					<div class="chart-responsive chart_container">
						<canvas id="cbu-yearly"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	let REVENUE_CURRENT_MONTH = jQuery.parseJSON('<?php echo json_encode($REVENUE_CURRENT_MONTH);?>');
	let REVENUE_CURRENT_YEAR = jQuery.parseJSON('<?php echo json_encode($REVENUE_CURRENT_YEAR);?>');

	let EXPENSES_CURRENT_MONTH = jQuery.parseJSON('<?php echo json_encode($EXPENSES_CURRENT_MONTH);?>');
	let EXPENSES_CURRENT_YEAR = jQuery.parseJSON('<?php echo json_encode($EXPENSES_CURRENT_YEAR);?>');

	let NET_SURPLUS_CURRENT_MONTH = jQuery.parseJSON('<?php echo json_encode($NET_SURPLUS_CURRENT_MONTH);?>');
	let NET_SURPLUS_CURRENT_YEAR = jQuery.parseJSON('<?php echo json_encode($NET_SURPLUS_CURRENT_YEAR);?>');


	let ASSET_LAST_5 = jQuery.parseJSON('<?php echo json_encode($ASSET_LAST_5);?>');

	let CASH_FLOW_CURRENT_MONTH = jQuery.parseJSON('<?php echo json_encode($CASH_FLOW_CURRENT_MONTH);?>');
	let CASH_FLOW_CURRENT_YEAR = jQuery.parseJSON('<?php echo json_encode($CASH_FLOW_CURRENT_YEAR);?>');


	let LIABILITIES_LAST_5 = jQuery.parseJSON('<?php echo json_encode($LIABILITIES_LAST_5);?>');


	let EQUITY_LAST_5 = jQuery.parseJSON('<?php echo json_encode($EQUITY_LAST_5);?>');

	let STAT_FUND_CURRENT = jQuery.parseJSON('<?php echo json_encode($STAT_FUND_CURRENT);?>');

	let CBU_LAST_5 = jQuery.parseJSON('<?php echo json_encode($CBU_LAST_5);?>');

	console.log({NET_SURPLUS_CURRENT_YEAR})


	$(document).ready(function(){
		initialize_pie_chart('pieRevenue',REVENUE_CURRENT_MONTH,'pie');
		initialize_pie_chart('pieExpenses',EXPENSES_CURRENT_MONTH,'pie');
		initialize_bar_chart('horizontalBar','revenue-year-chart',REVENUE_CURRENT_YEAR,false,false);	
		initialize_bar_chart('bar','expenses-year-chart',EXPENSES_CURRENT_YEAR,true,true);	

		initialize_pie_chart('pieNet',NET_SURPLUS_CURRENT_MONTH,'pie');
		initialize_line_chart('net-year',NET_SURPLUS_CURRENT_YEAR,false,false);
		initialize_line_chart('asset-year',ASSET_LAST_5,false,false);

		initialize_pie_chart('current-month-cashflow',CASH_FLOW_CURRENT_MONTH,'doughnut');
		initialize_line_chart('cash-flow-year',CASH_FLOW_CURRENT_YEAR,false,true);

		initialize_line_chart('liabilities-year',LIABILITIES_LAST_5,false,false);

		initialize_line_chart('equities-year',EQUITY_LAST_5,false,false);

		initialize_pie_chart('current-statutory-fund',STAT_FUND_CURRENT,'doughnut');
		initialize_line_chart('cbu-yearly',CBU_LAST_5,false,false);

	})

	function initialize_pie_chart($elem,$init_data,$type){
		var pieRevenueCanvas=$('#'+$elem).get(0).getContext('2d');
		
		var pieOptions={
			legend:{
				display:false,
				position:'right',
				maintainAspectRatio: true		
			},tooltips : {
				callbacks: { 
					label: function(tooltipItem, data) { 
						console.log({tooltipItem})
						console.log({data});


						return data.labels[tooltipItem.index]+': '+data.datasets[0].data[tooltipItem.index].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
					}, 

				},
				plugins: {
					datalabels: {
						color: 'black',
						anchor : 'end',
						clip : true,
				        formatter: function(value, context) {
				          return context.chart.data.labels[context.dataIndex];
				        }
					}
				}
			}
			var pieRevenue=new Chart(pieRevenueCanvas,
				{type:$type,
					data:$init_data,
					display: false,
					options:pieOptions});
		}
		function initialize_line_chart($elem,$init_data,$stack,$show_label){

			var ticksStyle = {
				fontColor: '#495057',
				fontStyle: 'bold'
			}
			var ticks = $.extend({
				beginAtZero: true,
				callback: function (value) {

					if ( Math.abs(value) >= 1000 && Math.abs(value) <= 999999) {
						value /= 1000
						value += 'k'
					}else if(Math.abs(value) >= 1000000){
						value /= 1000000
						value += 'M'            		
					}
					return '₱' + value
				}
			}, ticksStyle);

			var LineCanvas=$('#'+$elem).get(0).getContext('2d');
			var LineOptions={
				legend:{
					display:$show_label,
					position:"top",
					maintainAspectRatio:false
				},
				hover:{
					mode:"x",
					intersect:"intersect"
				},
				tooltips : {
					callbacks: { 
						label: function(tooltipItem, data) { 
							return tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
						}, 

					},
					scales:{
						yAxes:[
						{
							stacked:$stack,
							gridLines:{
								display:true,
								lineWidth:"4px",
								zeroLineColor:"transparent"
							},
							ticks:ticks
						}
						],
						"xAxes":[
						{
							"stacked":$stack,
							"display":true,
							"gridLines":{
								"display":true
							},
							"ticks":ticksStyle
						}
						]
					}
				}

				var mode = 'index';
				var intersect = true;
				var Line=new Chart(LineCanvas,
					{type:'line',
					data:$init_data,
					options : LineOptions
				});
			}
			function initialize_bar_chart($type,$elem,$init_data,$stack,$show_label){

				var ticksStyle = {
					fontColor: '#495057',
					fontStyle: 'bold'
				}
				var ticks = $.extend({
					beginAtZero: true,
            // Include a dollar sign in the ticks
            callback: function (value) {
            	if ( Math.abs(value) >= 1000 && Math.abs(value) <= 999999) {
            		value /= 1000
            		value += 'k'
            	}else if(Math.abs(value) >= 1000000){
            		value /= 1000000
            		value += 'M'            		
            	}
            	return '₱' + value
            }
        }, ticksStyle);


				if($type=='bar'){
					$y_tick = ticks;
					$x_tick = ticksStyle;
				}else{
					$x_tick = ticks;
					$y_tick = ticksStyle;
				}

				var mode = 'index'
				var intersect = true;

				var $salesChart = $('#'+$elem);
				var salesChart = new Chart($salesChart, {
					type: $type,
					data: $init_data,

					options: {
						maintainAspectRatio: false,

						hover: {

							mode: mode,
							intersect: intersect
						},
						tooltips : {
							mode: 'nearest',
							callbacks: { 
								label: function(tooltipItem, data) { 
									if($type == "horizontalBar"){
										return tooltipItem.xLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
									}else{
										return tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
									}

								}
							},
						},
						legend:{
							display:$show_label,
							position:"top",
							maintainAspectRatio:false
						},

						scales: {
							yAxes: [{
								stacked : $stack,
								gridLines: {
									display: true,
									lineWidth: '4px',
									color: 'rgba(0, 0, 0, .2)',
									zeroLineColor: 'transparent'
								},
								ticks: $y_tick
							}],
							xAxes: [{
								stacked : $stack,
						// display: true,
						gridLines: {
							display: true
						},
						ticks: $x_tick
					}]
				}
			}
		})
			}


	// function initialize_bar_chart2($type,$elem,$init_data,$stack){

	// 	var ticksStyle = {
	// 		fontColor: '#495057',
	// 		fontStyle: 'bold'
	// 	}

	// 	var mode = 'index'
	// 	var intersect = true

	// 	var $salesChart = $('#'+$elem);
	// 	var salesChart = new Chart($salesChart, {
	// 		type: 'bar',
	// 		data: $init_data,
	// 		options: {
	// 			plugins: {
	// 				title: {
	// 					display: true,
	// 					text: 'Chart.js Bar Chart - Stacked'
	// 				},
	// 			},
	// 			responsive: true,
	// 			scales: {
	// 				xAxes: [{stacked: true}],
	// 				yAxes: [{stacked: true}]
	// 			},   
	// 		}
	// 	})
	// }
</script>
@endpush