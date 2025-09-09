	<div class="row">
		<div class="col-lg col-12 mb-3">
			<div class="card h-100">
				<div class="card-body pb-1">
					<div class="row">
						<div class="col-7">
							<label class="text-bold lbl_color text-lg mb-0">Assets</label>
						</div>
						<div class="col-5 text-right lbl_color">
							<p class="mt-1 mb-0 text-sm">{{$FILTER_YEAR_RANGE}}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							
							<h4 class="lbl_color text-lg mb-0">₱{{number_format($ASSET_CURRENT_YEAR_AMOUNT,2)}}</h4>
							<?php
							$percentage = $ASSET_PERCENTAGE_DIFF;
							?>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							@if($percentage != 0)
							<span class="font-{{($percentage < 0)?'down':'up'}} text-md">
								<i class="fas fa-arrow-{{($percentage < 0)?'down':'up'}}"></i>&nbsp;{{abs($percentage)}}% Since Last Year
							</span>
							@else
							<span class="text-muted text-sm">
								<i>No Changes Since Last Year</i>
							</span>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg col-12 mb-3">
			<div class="card h-100">
				<div class="card-body pb-1">
					<div class="row">
						<div class="col-7">
							<label class="text-bold lbl_color text-lg mb-0">Liabilities</label>
						</div>
						<div class="col-5 text-right lbl_color">
							<p class="mt-1 mb-0 text-sm">{{$FILTER_YEAR_RANGE}}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							
							<h4 class="lbl_color text-lg mb-0">₱{{number_format($LIABILITIES_CURRENT_YEAR_AMOUNT,2)}}</h4>
							<?php
							$percentage = $LIABILITIES_PERCENTAGE_DIFF;
							?>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							@if($percentage != 0)
							<span class="font-{{($percentage < 0)?'down':'up'}} text-md">
								<i class="fas fa-arrow-{{($percentage < 0)?'down':'up'}}"></i>&nbsp;{{abs($percentage)}}% Since Last Year
							</span>
							@else
							<span class="text-muted text-sm">
								<i>No Changes Since Last Year</i>
							</span>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg col-12 mb-3">
			<div class="card h-100">
				<div class="card-body pb-1">
					<div class="row">
						<div class="col-7">
							<label class="text-bold text-lg lbl_color mb-0">Equities</label>
						</div>
						<div class="col-5 text-right lbl_color">
							<p class="mt-1 mb-0 text-sm">{{$FILTER_YEAR_RANGE}}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<h4 class="lbl_color text-lg mb-0">₱{{number_format($EQUITIES_CURRENT_YEAR_AMOUNT,2)}}</h4>
							<?php
							$percentage = $EQUITIES_PERCENTAGE_DIFF;
							?>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							@if($percentage != 0)
							<span class="font-{{($percentage < 0)?'down':'up'}} text-md">
								<i class="fas fa-arrow-{{($percentage < 0)?'down':'up'}}"></i>&nbsp;{{abs($percentage)}}% Since Last Year
							</span>
							@else
							<span class="text-muted text-sm">
								<i>No Changes Since Last Year</i>
							</span>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-12 mb-3">
			<div class="card h-100">
				<div class="card-body pb-1">
					<div class="row">
						<div class="col-7">
							<label class="text-bold text-lg lbl_color mb-0">Cash Flow</label>
						</div>
						<div class="col-5 text-right">
							<p class="mt-1 mb-0 lbl_color">{{$FILTER_YEAR_RANGE}}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-7">
							<h4 class="lbl_color text-lg mb-0">₱{{number_format($CASH_FLOW_CURRENT,2)}}</h4>
						</div>
						<div class="col-5">
							<div class="row">
								<div class="col-lg-3 col-3 p-0"><p class="lbl_color mb-0 text-md font-up" >IN</p></div>
								<div class="col-lg-9 col-9 p-0"><p class="lbl_color mb-0 text-md font-up">₱{{number_format($CASH_FLOW_CURRENT_IN,2)}}</p></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-7">
							<?php
								$percentage = $CASH_FLOW_LAST_YEAR_PERCENTAGE;
							?>
							@if($percentage != 0)
							<span class="font-{{($percentage < 0)?'down':'up'}} text-md">
								<i class="fas fa-arrow-{{($percentage < 0)?'down':'up'}}"></i>&nbsp;{{abs($percentage)}}% Since Last Year
							</span>
							@else
							<span class="text-muted text-sm">
								<i>No Changes Since Last Year</i>
							</span>
							@endif
						</div>
						<div class="col-5">
							<div class="row">
								<div class="col-lg-3 col-3 p-0"><p class="lbl_color mb-0 text-md font-down" >OUT</p></div>
								<div class="col-lg-9 col-9 p-0"><p class="lbl_color mb-0 text-md font-down">₱{{number_format($CASH_FLOW_CURRENT_OUT,2)}}</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>