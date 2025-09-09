	<!-- REVENUE -->
	<div class="row">
		<div class="col-lg-7 col-12 mb-3" id="div_revenue_month">
			<div class="card h-100" >
				<div class="card-body chart_container" >

					<?php
					echo expand_button('div_revenue_month');
					?>
					
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_MONTH}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-4">
								<h4 class="head_lbl text-lg">Revenue (Month) <br>₱{{number_format($REVENUE_CURRENT_MONTH_AMOUNT,2)}}</h4>
							</div>			
						</div>
					</div>
					<div class="row">
						<div id="chartLegend"></div>
					</div>
					
					<div class="row">
						<div class="col-md-12 col-12">
							<div class="chart-responsive">
								<canvas id="pieRevenue"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-5 col-12 mb-3" id="div_rev_year">
			<div class="card h-100">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_rev_year');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_RANGE}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-lg">Revenue (Year)<br>₱{{number_format($REVENUE_TOTAL_YEAR,2)}}</h4>
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
	<!-- TOP PRODUCTS AND  TOP CUSTOMER -->
	<div class="row">
		<div class="col-lg-7 col-12 mb-3" id="div_top_product">
			<div class="card h-100">
				<div class="card-body">
					<?php
					echo expand_button('div_top_product');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">

						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-4">
								<h4 class="head_lbl text-xl">Top Product <span class="text-md lbl_color font-weight-normal float-right">{{$FILTER_MONTH}}</span> </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12 col-12 div_table" style="overflow-y: auto;">
							<table class="table table-head-fixed" style="border-collapse: collapse;">
								<thead>
									<tr class="vertical-center lbl_color text-sm text-center">
										<th>Loan Product</th>
										<th>Principal<br>Released</th>
										<th>Changes<br>Last Month</th>
										<th>Interest<br>Income</th>
										<th>Changes<br>Last Month</th>
									</tr>
								</thead>
								<tbody>

									@foreach($TOP_PRODUCT as $tp)
									<?php
									$p = perc_difference($tp->previous_principal,$tp->current_principal);
									$percentage = $p['percentage'];
									$change = $p['change'];
									$color = $p['color'];
									?>
									<tr class="text-md font-weight-bold lbl_color vertical-center dashboard-table">
										<td style="line-height: 1.5rem" class="pb-0">
											<span class="c_font badge bg-gradient-dark">{{$tp->loan_service}}</span><br>
											<span class="text-primary mt-1">{{$tp->loan_count}} Loan Transaction(s)</span><br>
											<span class="text-success">{{$tp->new}} New</span>
											<span class="text-primary">{{$tp->renew}} Renew</span>
										</td>
										<td class="text-right">{{number_format($tp->current_principal,2)}}</td>
										<td class="text-center">
											@if(abs($percentage) > 0)
											<span class="vertical-center text-{{$color}} text-md">{{abs($percentage)}}% 
												<i class="fas fa-arrow-{{$change}}"></i>&nbsp;
											</span>
											@else
											-
											@endif
										</td>
										<td class="text-right">{{number_format($tp->current_interest,2)}}</td>
										<?php
										$p = perc_difference($tp->previous_interest,$tp->current_interest);
										$percentage = $p['percentage'];
										$change = $p['change'];
										$color = $p['color'];
										?>

										<td class="text-center">
											@if(abs($percentage) > 0)
											<span class="vertical-center text-{{$color}} text-md">{{abs($percentage)}}% 
												<i class="fas fa-arrow-{{$change}}"></i>&nbsp;
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
		<div class="col-lg-5 col-12 mb-3" id="div_top_customer">
			<div class="card h-100">
				<div class="card-body">
					<?php
					echo expand_button('div_top_customer');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="card c-border mt-3">
								<div class="card-body">
									<h4 class="head_lbl text-lg">Loan Released Summary <small>({{$FILTER_YEAR_RANGE}})</small></h4>
									<div class="row mt-2">
										<div class="col-md-6 col-sm-6 col-12">
											<div class="info-box shadow">
												<span class="info-box-icon bg-warning"><i class="fas fa-list"></i></span>
												<div class="info-box-content">
													<span class="info-box-text">Loan Count</span>
													<span class="info-box-number">{{number_format($LOAN_SUMMARY->total_transaction,0)}}</span>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-6 col-12">
											<div class="info-box shadow">
												<span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
												<div class="info-box-content">
													<span class="info-box-text">Principal Released</span>
													<span class="info-box-number">₱{{number_format($LOAN_SUMMARY->total_principal,2)}}</span>
												</div>

											</div>

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card c-border">
							<div class="card-body">
					<div class="row mb-2">
						
								

								<div class="col-lg-12 col-12">
									<div class="text-left mb-3">
										<h4 class="head_lbl text-xl">Top Customer  </h4>
									</div>			
								</div>
								<label class="control-label text-md col-lg-1 my-0">Top</label>
								<div class="col-lg-2">
									<select class="form-control form-control-border" onchange="change_top(this)">
										@for($top=10;$top <=50; $top+=10)
										<option value="{{$top}}">{{$top}}</option>
										@endfor
									</select>
								</div>
							</div>
							<div class="row">

								<div class="col-md-12 col-12 div_top"  style="max-height: calc(100vh + 200px);overflow-y: auto;">
									<table class="table table-head-fixed" style="">
										<thead>
											<tr class="vertical-center lbl_color text-sm text-center">
												<th>Rank</th>
												<th>Name</th>
												<th>Principal Amount</th>

											</tr>
										</thead>
										<tbody id="top_customer">
											@foreach($TOP_CUSTOMER as $c=>$tp)
											@foreach($tp as $cc=>$t)
											<tr class="text-md font-weight-bold lbl_color vertical-center">
												@if($cc==0)
												<td class="text-center border-right" rowspan="{{count($tp)}}">{{($c+1)}}</td>
												@endif
												<td>{{$t->member}}</td>
												<td class="text-right">{{number_format($t->amount,2)}}</td>
											</tr>
											@endforeach

											@endforeach



										</tbody>
									</table>
								</div>
								<div class="col-md-12 col-12 text-right font-weight-bold lbl_color">
									<i>For the period {{$FILTER_MONTH}}</i>
								</div>
							</div>



						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>

	<!-- EXPENSES -->
	<div class="row">
		<div class="col-lg-7 col-12 mb-3" id="div_expenses_month">
			<div class="card h-100">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_expenses_month');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_MONTH}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-4">
								<h4 class="head_lbl text-lg">Expenses <br>₱{{number_format($EXPENSES_CURRENT_MONTH_AMOUNT,2)}}</h4>

							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-12 col-12">
							<div class="chart-responsive">
								<canvas id="pieExpenses" height="200" class=""></canvas>
							</div>

						</div>
						
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-5 col-12 mb-3" id="div_expenses_year">
			<div class="card h-100">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_expenses_year');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_RANGE}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-lg">Expenses <br>₱{{number_format($EXPENSES_TOTAL_YEAR,2)}}</h4>

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
	<!-- EXPENSES VS BUDGET-->
	<div class="row">
		<div class="col-lg-12 col-12 mb-3" id="div_expenses_budget">
			<div class="card h-100">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_expenses_budget');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_RANGE}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-center mb-4">
								<h4 class="head_lbl">Expenses (Actual vs Budgeted)</h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-12 col-12">
							<div class="chart-responsive">
								<canvas id="expenses-budget-year" class=""></canvas>
							</div>

						</div>
						
					</div>
				</div>
			</div>
		</div>		

	</div>
	<!-- NET SURPLUS -->
	<div class="row">
		<div class="col-lg-6 col-12 mb-3" id="div_net_month">
			<div class="card h-100">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_net_month');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_MONTH}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-lg">Net Surplus ₱{{number_format($NET_SURPLUS_CURRENT_MONTH_AMOUNT,2)}}</h4>

							</div>				
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-12 col-12">
							<div class="chart-responsive">
								<canvas id="pieNet" class=""></canvas>
							</div>

						</div>
						
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-6 col-12 mb-3" id="div_net_year">
			<div class="card h-100 ">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_net_year');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_RANGE}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">

							<div class="text-left mb-2">
								<h4 class="head_lbl text-lg">Net Surplus ₱{{number_format($NET_SURPLUS_TOTAL_YEAR,2)}}</h4>

							</div>				
						</div>
					</div>
					<div class="chart-responsive">
						<canvas id="net-year"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	@push('scripts')
	<script type="text/javascript">
		let REVENUE_CURRENT_MONTH = jQuery.parseJSON('<?php echo json_encode($REVENUE_CURRENT_MONTH);?>');
		let REVENUE_CURRENT_YEAR = jQuery.parseJSON('<?php echo json_encode($REVENUE_CURRENT_YEAR);?>');

		let EXPENSES_CURRENT_MONTH = jQuery.parseJSON('<?php echo json_encode($EXPENSES_CURRENT_MONTH);?>');
		let EXPENSES_CURRENT_YEAR = jQuery.parseJSON('<?php echo json_encode($EXPENSES_CURRENT_YEAR);?>');

		let NET_SURPLUS_CURRENT_MONTH = jQuery.parseJSON('<?php echo json_encode($NET_SURPLUS_CURRENT_MONTH);?>');
		let NET_SURPLUS_CURRENT_YEAR = jQuery.parseJSON('<?php echo json_encode($NET_SURPLUS_CURRENT_YEAR);?>');


		let EXPENSES_BUDGET = jQuery.parseJSON('<?php echo json_encode($EXPENSES_BUDGET);?>');

		$(document).ready(function(){
			initialize_pie_chart('pieRevenue',REVENUE_CURRENT_MONTH,'doughnut','right');
			initialize_pie_chart('pieExpenses',EXPENSES_CURRENT_MONTH,'doughnut','right');


			initialize_bar_chart('Horizontalbar','revenue-year-chart',REVENUE_CURRENT_YEAR,false,false,false);	
			initialize_bar_chart('bar','expenses-year-chart',EXPENSES_CURRENT_YEAR,true,true,false);	

			initialize_bar_chart('bar','expenses-budget-year',EXPENSES_BUDGET,false,false,false);	


			initialize_pie_chart('pieNet',NET_SURPLUS_CURRENT_MONTH,'doughnut','right');
			initialize_line_chart('net-year',NET_SURPLUS_CURRENT_YEAR,false,false);

		})
	</script>
	@endpush