<div class="col-md-12">
	<?php
	$color = 0;
	?>
	<div class="row">
		<div class="col-12">
			<span class="text-bold text-md text-muted">Loan Service</span>
			<span class="text-bold text-md text-muted float-right">Principal</span>
			<hr class="mt-n0">
		</div>

	</div>
	@foreach($loans as $loan)
	<?php
	$total_paid = ROUND($loan->principal_amount - $loan->loan_balance,2);
	$percentage_complete = ROUND(($total_paid/$loan->principal_amount)*100,2);

    	// $prin_dis = ((floor($loan->principal_amount) == $loan))

	$percentage_incomplete = 100-$percentage_complete;
	$colors = [
		'primary2',
		'danger2',
		'success2',
		'warning2'
	];
	?>

	<div class="row">
		<div class="col-12">
			<div class="progress-group">
				<div class="row">
					<div class="col-lg-8 col-12">
						<span class="lbl_color text-bold font-normal">{{$loan->loan_service_name}} <a class="badge bg-gradient-dark font-normal ml-1 mb-1 rounded-pill" href="/loan/application/view/{{$loan->loan_token}}" target="_blank">{{$loan->id_loan}}</a></span>
					</div>
					<div class="col-lg-4 col-12">
						<span class="float-right lbl_color font-normal"><b>₱{{number_format($total_paid,2)}}</b>/₱{{number_format($loan->principal_amount,2)}}</span>
					</div>
					<div class="col-12">
						<div class="progress progress-md">
							<div class="progress-bar bg-gradient-{{$colors[$color]}}" style="font-size: 12px;width: <?php echo $percentage_complete; ?>%"><?php echo $percentage_complete; ?>%</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	if($percentage_complete > 0){
		$color++;
		if($color > 3){
			$color = 0;
		}			
	}

	?>
	@endforeach



</div>