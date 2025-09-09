	<!-- ASSET -->
	<div class="row">
		<div class="col-lg-6 col-12 mb-3" id="div_asset_last_2">
			<div class="card h-100">
				<div class="card-body">
					<?php
					echo expand_button('div_asset_last_2');
					?>
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
									<th>Current<br>Yr.{{$selected_year}}</th>
									<th>Previous<br>Yr.{{$selected_year-1}}</th>
									<th>Changes<br>Last Year</th>
								</tr>
								<tbody>

									@foreach($ASSETS_LAST_2 as $row)
									<tr class="text-md font-weight-bold lbl_color vertical-center dashboard-table">
										<td>{{$row->description}}</td>
										@foreach($year_key as $k)
										<td class="text-right">{{ check_negative($row->{$k}) }}</td>
										@endforeach
										<?php
										$previous = $row->{$year_key[1]};
										$current = $row->{$year_key[0]};

										$p = perc_difference($previous,$current);
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
		<div class="col-lg-6 col-12 mb-3" id="div_asset_year_5">
			<div class="card h-100 ">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_asset_year_5');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_5}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Assets: ₱{{number_format($TOTAL_ASSET,2)}} </h4>
							</div>			
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="chart-responsive">
								<canvas id="asset-year"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- LOAN RECEIVABLE -->
	<div class="row">
		<div class="col-lg-6 col-12 mb-3" id="div_loan_receivable">
			<div class="card h-100">
				<div class="card-body">
					<?php
					echo expand_button('div_loan_receivable');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-4">
								<h4 class="head_lbl text-xl">Loan Receivable </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-12 col-12  div_table" style="overflow-y: auto;">
							<table class="table table-head-fixed" style="border-collapse: collapse;">
								<tr class="vertical-center lbl_color text-sm text-center">
									<thead>
										<th>Loan Product</th>
										<th>Current</th>
										<th>Overdue</th>
										<th>Total</th>
									</thead>
								</tr>
								<tbody>
									<?php
									$total_loan_receivable = 0;
									?>

									@foreach($LOAN_RECEIVABLES as $row)
									<tr class="text-md font-weight-bold lbl_color vertical-center dashboard-table">
										<td>{{$row->name}}</td>
										<td class="text-right">{{number_format($row->current,2)}}</td>
										<td class="text-right">{{number_format($row->overdue,2)}}</td>
										<td class="text-right">{{number_format($row->total,2)}}</td>

										<?php $total_loan_receivable += $row->total;?>
									</tr>
									@endforeach
									
								</tbody>

							</table>

						</div>
						
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-6 col-12 mb-3" id="div_loan_receivable_tot">
			<div class="card h-100 ">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_loan_receivable_tot');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md"></p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Loan Receivable ₱{{number_format($total_loan_receivable,2)}} </h4>
							</div>			
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="chart-responsive">
								<canvas id="loan-receivable"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- CASH FLOW -->
	<div class="row">
		<div class="col-lg-6 col-12 mb-3" id="div_cash_flow_month">
			<div class="card h-100">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_cash_flow_month');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_MONTH}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-4">
								<h4 class="head_lbl text-xl">Cash Flow </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-12 col-12">
							<div class="chart-responsive">
								<canvas id="current-month-cashflow"></canvas>
							</div>

						</div>
						
					</div>
				</div>
			</div>
		</div>		
		<div class="col-lg-6 col-12 mb-3" id="div_cash_flow_year">
			<div class="card h-100 ">

				<div class="card-body chart_container">
					<?php
					echo expand_button('div_cash_flow_year');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_RANGE}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Cash {{number_format($TOTAL_CASH,2)}} </h4>
							</div>			
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="chart-responsive">
								<canvas id="cash-flow-year"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- LIABILITIES -->
	<div class="row">
		<div class="col-lg-5 col-12 mb-3" id="div_liabilities_last_2">
			<div class="card h-100">
				<div class="card-body">
					<?php
					echo expand_button('div_liabilities_last_2');
					?>
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
									<th>Current<br>Yr.{{$selected_year}}</th>
									<th>Previous<br>Yr.{{$selected_year-1}}</th>
									<th>Changes<br>Last Year</th>
								</tr>
								<tbody>

									@foreach($LIABILITIES_LAST_2 as $row)
									<tr class="text-md font-weight-bold lbl_color vertical-center dashboard-table">
										<td>{{$row->description}}</td>
										@foreach($year_key as $k)
										<td class="text-right">{{ check_negative($row->{$k}) }}</td>
										@endforeach
										<?php
										$previous = $row->{$year_key[1]};
										$current = $row->{$year_key[0]};
										
										$p = perc_difference($previous,$current);
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
		<div class="col-lg-7 col-12 mb-3" id="div_liabilities_last_5">
			<div class="card h-100 ">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_liabilities_last_5');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_5}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Liabilities: ₱{{number_format($TOTAL_LIABILITIES,2)}}</h4>
							</div>			
						</div>
					</div>
					<div class="chart-responsive">
						<canvas id="liabilities-year"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- EQUITIES -->
	<div class="row">
		<div class="col-lg-5 col-12 mb-3" id="div_equities_last_2">
			<div class="card h-100">
				<div class="card-body">
					<?php
					echo expand_button('div_equities_last_2');
					?>
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
									<th>Current<br>Yr.{{$selected_year}}</th>
									<th>Previous<br>Yr.{{$selected_year-1}}</th>
									<th>Changes<br>Last Year</th>
								</tr>
								<tbody>

									@foreach($EQUITIES_LAST_2 as $row)
									<tr class="text-md font-weight-bold lbl_color vertical-center dashboard-table">
										<td>{{$row->description}}</td>
										@foreach($year_key as $k)
										<td class="text-right">{{ check_negative($row->{$k}) }}</td>
										@endforeach
										<?php
										$previous = $row->{$year_key[1]};
										$current = $row->{$year_key[0]};

										$p = perc_difference($previous,$current);
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
		<div class="col-lg-7 col-12 mb-3" id="div_equities_last_5">
			<div class="card h-100 ">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_equities_last_5');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_5}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Equities: ₱{{number_format($TOTAL_EQUITY,2)}}</h4>
							</div>			
						</div>
					</div>
					<div class="chart-responsive">
						<canvas id="equities-year"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<!-- STAT FUND -->
		<div class="col-lg-6 col-12 mb-3" id="div_stat_fund">
			<div class="card h-100">
				<div class="card-body chart_container">
					<?php
					echo expand_button('div_stat_fund');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">As of {{$FILTER_MONTH}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left">
								<h4 class="head_lbl text-xl">Statutory Fund </h4>
							</div>			
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Statutory Fund : ₱{{number_format($TOTAL_STAT_FUND,2)}}</h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-12 col-12">
							<div class="chart-responsive">
								<canvas id="current-statutory-fund" class=""></canvas>
							</div>

						</div>
						
					</div>
				</div>
			</div>
		</div>		
		<!-- CBU FUND -->
		<div class="col-lg-6 col-12 mb-3" id="div_capital_share_5">
			<div class="card h-100 ">
				<div class="card-body">
					<?php
					echo expand_button('div_capital_share_5');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">{{$FILTER_YEAR_5}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-xl">Capital Share </h4>
							</div>			
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Capital Share: ₱{{number_format($TOTAL_CBU,2)}}</h4>
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


	<!-- CAPITAL SHARE -->
	<div class="row">
		<!-- CAPITAL SHARE -->
		<div class="col-lg-7 col-12 mb-3" id="div_capital_share">
			<div class="card h-100">
				<div class="card-body">
					<?php
					echo expand_button('div_capital_share');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">As of {{$FILTER_MONTH}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left">
								<h4 class="head_lbl text-xl">Capital Share </h4>
							</div>			
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-4">
								<h4 class="head_lbl text-md">Total Capital Share: ₱{{number_format($CAPITAL_SHARE_TOTAL,2)}}  </h4>
							</div>			
						</div>
					</div>
					
					<div class="row">

						<div class="col-md-12 col-12">
							<div class="chart-responsive chart_container">
								<canvas id="capital-share" class=""></canvas>
							</div>

						</div>
						
					</div>
				</div>
			</div>
		</div>		

		<div class="col-lg-5 col-12 mb-3" id="div_top_cbu">
			<div class="card h-100 ">
				<div class="card-body">
					<?php
					echo expand_button('div_top_cbu');
					?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="text-right">
								<p class="mb-0 lbl_color text-md">As of {{$FILTER_MONTH}}</p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-xl">Top CBU </h4>
							</div>			
						</div>
					</div>
					<div class="row mb-2">
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

						<div class="col-md-12 col-12 div_top"  style="max-height: calc(100vh - 250px);overflow-y: auto;">
							<table class="table table-head-fixed" style="border-collapse: collapse;">
								<thead>
									<tr class="vertical-center lbl_color text-sm text-center">
										<th>Rank</th>
										<th>Name</th>
										<th>Total CBU</th>

									</tr>
								</thead>
								<tbody id="top_cbu">

									@foreach($MEMBER_TOP_CBU as $c=>$tp)

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
						
					</div>
				</div>
			</div>
		</div>
	</div>
	@push('scripts')
	<script type="text/javascript">



		let ASSET_LAST_5 = jQuery.parseJSON('<?php echo json_encode($ASSET_LAST_5);?>');



		let LOAN_RECEIVABLES_CURRENT_MONTH = jQuery.parseJSON('<?php echo json_encode($LOAN_RECEIVABLES_CURRENT_MONTH);?>');
		

		let CASH_FLOW_CURRENT_MONTH = jQuery.parseJSON('<?php echo json_encode($CASH_FLOW_CURRENT_MONTH);?>');
		let CASH_FLOW_CURRENT_YEAR = jQuery.parseJSON('<?php echo json_encode($CASH_FLOW_CURRENT_YEAR);?>');


		let LIABILITIES_LAST_5 = jQuery.parseJSON('<?php echo json_encode($LIABILITIES_LAST_5);?>');


		let EQUITY_LAST_5 = jQuery.parseJSON('<?php echo json_encode($EQUITY_LAST_5);?>');

		let STAT_FUND_CURRENT = jQuery.parseJSON('<?php echo json_encode($STAT_FUND_CURRENT);?>');

		let CBU_LAST_5 = jQuery.parseJSON('<?php echo json_encode($CBU_LAST_5);?>');

		let CAPITAL_SHARE = jQuery.parseJSON('<?php echo json_encode($CAPITAL_SHARE);?>');


		



		$(document).ready(function(){

			initialize_line_chart('asset-year',ASSET_LAST_5,false,false);

			initialize_pie_chart('current-month-cashflow',CASH_FLOW_CURRENT_MONTH,'doughnut','right');
			initialize_pie_chart('loan-receivable',LOAN_RECEIVABLES_CURRENT_MONTH,'doughnut','right');


			initialize_line_chart('cash-flow-year',CASH_FLOW_CURRENT_YEAR,false,true);

			initialize_line_chart('liabilities-year',LIABILITIES_LAST_5,false,false);

			initialize_line_chart('equities-year',EQUITY_LAST_5,false,false);

			initialize_pie_chart('current-statutory-fund',STAT_FUND_CURRENT,'doughnut','right');
			initialize_line_chart('cbu-yearly',CBU_LAST_5,false,false);


			initialize_line_chart('capital-share',CAPITAL_SHARE,false,false);

			// initialize_bar_chart('bar','capital-share',CAPITAL_SHARE,false,false,true);	

			



		})

	</script>
	@endpush