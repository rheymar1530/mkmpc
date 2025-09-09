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
									<th>Current<br>Yr.<?php echo e($selected_year); ?></th>
									<th>Previous<br>Yr.<?php echo e($selected_year-1); ?></th>
									<th>Changes<br>Last Year</th>
								</tr>
								<tbody>

									<?php $__currentLoopData = $ASSETS_LAST_2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr class="text-md font-weight-bold lbl_color vertical-center dashboard-table">
										<td><?php echo e($row->description); ?></td>
										<?php $__currentLoopData = $year_key; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<td class="text-right"><?php echo e(check_negative($row->{$k})); ?></td>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php
										$previous = $row->{$year_key[1]};
										$current = $row->{$year_key[0]};

										$p = perc_difference($previous,$current);
										$percentage = $p['percentage'];
										$change = $p['change'];
										$color = $p['color'];
										?>
										<td class="text-center">
											<?php if(abs($percentage) > 0): ?>
											<span class="vertical-center text-<?php echo e($color); ?> text-md"><?php echo e(abs($percentage)); ?>% 
												<i class="fas fa-arrow-<?php echo e($change); ?>"></i>&nbsp;
											</span>
											<?php else: ?>
											-
											<?php endif; ?>
										</td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									
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
								<p class="mb-0 lbl_color text-md"><?php echo e($FILTER_YEAR_5); ?></p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Assets: ₱<?php echo e(number_format($TOTAL_ASSET,2)); ?> </h4>
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

									<?php $__currentLoopData = $LOAN_RECEIVABLES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr class="text-md font-weight-bold lbl_color vertical-center dashboard-table">
										<td><?php echo e($row->name); ?></td>
										<td class="text-right"><?php echo e(number_format($row->current,2)); ?></td>
										<td class="text-right"><?php echo e(number_format($row->overdue,2)); ?></td>
										<td class="text-right"><?php echo e(number_format($row->total,2)); ?></td>

										<?php $total_loan_receivable += $row->total;?>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									
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
								<h4 class="head_lbl text-md">Total Loan Receivable ₱<?php echo e(number_format($total_loan_receivable,2)); ?> </h4>
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
								<p class="mb-0 lbl_color text-md"><?php echo e($FILTER_MONTH); ?></p>
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
								<p class="mb-0 lbl_color text-md"><?php echo e($FILTER_YEAR_RANGE); ?></p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Cash <?php echo e(number_format($TOTAL_CASH,2)); ?> </h4>
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
									<th>Current<br>Yr.<?php echo e($selected_year); ?></th>
									<th>Previous<br>Yr.<?php echo e($selected_year-1); ?></th>
									<th>Changes<br>Last Year</th>
								</tr>
								<tbody>

									<?php $__currentLoopData = $LIABILITIES_LAST_2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr class="text-md font-weight-bold lbl_color vertical-center dashboard-table">
										<td><?php echo e($row->description); ?></td>
										<?php $__currentLoopData = $year_key; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<td class="text-right"><?php echo e(check_negative($row->{$k})); ?></td>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php
										$previous = $row->{$year_key[1]};
										$current = $row->{$year_key[0]};
										
										$p = perc_difference($previous,$current);
										$percentage = $p['percentage'];
										$change = $p['change'];
										$color = $p['color'];


										?>
										<td class="text-center">
											<?php if(abs($percentage) > 0): ?>
											<span class="vertical-center text-<?php echo e($color); ?> text-md"><?php echo e(abs($percentage)); ?>% 
												<i class="fas fa-arrow-<?php echo e($change); ?>"></i>&nbsp;
											</span>
											<?php else: ?>
											-
											<?php endif; ?>
										</td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									
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
								<p class="mb-0 lbl_color text-md"><?php echo e($FILTER_YEAR_5); ?></p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Liabilities: ₱<?php echo e(number_format($TOTAL_LIABILITIES,2)); ?></h4>
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
									<th>Current<br>Yr.<?php echo e($selected_year); ?></th>
									<th>Previous<br>Yr.<?php echo e($selected_year-1); ?></th>
									<th>Changes<br>Last Year</th>
								</tr>
								<tbody>

									<?php $__currentLoopData = $EQUITIES_LAST_2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr class="text-md font-weight-bold lbl_color vertical-center dashboard-table">
										<td><?php echo e($row->description); ?></td>
										<?php $__currentLoopData = $year_key; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<td class="text-right"><?php echo e(check_negative($row->{$k})); ?></td>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php
										$previous = $row->{$year_key[1]};
										$current = $row->{$year_key[0]};

										$p = perc_difference($previous,$current);
										$percentage = $p['percentage'];
										$change = $p['change'];
										$color = $p['color'];


										?>
										<td class="text-center">
											<?php if(abs($percentage) > 0): ?>
											<span class="vertical-center text-<?php echo e($color); ?> text-md"><?php echo e(abs($percentage)); ?>% 
												<i class="fas fa-arrow-<?php echo e($change); ?>"></i>&nbsp;
											</span>
											<?php else: ?>
											-
											<?php endif; ?>
										</td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									
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
								<p class="mb-0 lbl_color text-md"><?php echo e($FILTER_YEAR_5); ?></p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Equities: ₱<?php echo e(number_format($TOTAL_EQUITY,2)); ?></h4>
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
								<p class="mb-0 lbl_color text-md">As of <?php echo e($FILTER_MONTH); ?></p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left">
								<h4 class="head_lbl text-xl">Statutory Fund </h4>
							</div>			
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Statutory Fund : ₱<?php echo e(number_format($TOTAL_STAT_FUND,2)); ?></h4>
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
								<p class="mb-0 lbl_color text-md"><?php echo e($FILTER_YEAR_5); ?></p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-xl">Capital Share </h4>
							</div>			
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-2">
								<h4 class="head_lbl text-md">Total Capital Share: ₱<?php echo e(number_format($TOTAL_CBU,2)); ?></h4>
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
								<p class="mb-0 lbl_color text-md">As of <?php echo e($FILTER_MONTH); ?></p>
							</div>
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left">
								<h4 class="head_lbl text-xl">Capital Share </h4>
							</div>			
						</div>
						<div class="col-lg-12 col-12">
							<div class="text-left mb-4">
								<h4 class="head_lbl text-md">Total Capital Share: ₱<?php echo e(number_format($CAPITAL_SHARE_TOTAL,2)); ?>  </h4>
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
								<p class="mb-0 lbl_color text-md">As of <?php echo e($FILTER_MONTH); ?></p>
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
								<?php for($top=10;$top <=50; $top+=10): ?>
								<option value="<?php echo e($top); ?>"><?php echo e($top); ?></option>
								<?php endfor; ?>
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

									<?php $__currentLoopData = $MEMBER_TOP_CBU; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$tp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

									<?php $__currentLoopData = $tp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cc=>$t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr class="text-md font-weight-bold lbl_color vertical-center">
										<?php if($cc==0): ?>
										<td class="text-center border-right" rowspan="<?php echo e(count($tp)); ?>"><?php echo e(($c+1)); ?></td>
										<?php endif; ?>
										<td><?php echo e($t->member); ?></td>
										<td class="text-right"><?php echo e(number_format($t->amount,2)); ?></td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									
								</tbody>

							</table>

						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $__env->startPush('scripts'); ?>
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
	<?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/admin_dashboard/financial.blade.php ENDPATH**/ ?>