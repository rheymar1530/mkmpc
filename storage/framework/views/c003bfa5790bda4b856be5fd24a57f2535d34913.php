<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>

		@page  {      
			margin-left: 1cm;
			margin-right: 1cm;
			margin-top:2cm;
			size : letter landscape;
		}
		div.header{
			text-align: center;
			/*line-height: 1px;*/
			font-size: 16px;
			font-family: Calibri, sans-serif;
		}

		* {
			box-sizing: border-box;
		}
		.columnLeft {         
			float: left;
			width: 50%;
		}
		.columnRight {         
			float: right;
			width: 50%;
		}
		.tbl_repayment_summary  tr>td,.tbl_repayment_summary  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Calibri, sans-serif;
			font-size: 16px ;
		}
		.bold_lbl{
			font-weight: bold;
		}
		.details_tbl_lbl{
			text-align: right !important;
			width: 40% !important;
		}	
		.pd_left_10{
			/*padding-left: 10px !important;*/
		}
		.row:after {
			line-height: 10%;
			content: "";
			display: table;
			clear: both;
			height: 1px; 
		}

		.col_amount, .col_number{
			text-align: right;
			margin-right: 5px !important;
		}

		table, td, th {
			border: 1px solid;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}
		.highlight_amount{
			font-weight: bold;
			text-decoration: underline;
			font-size: 12px !important;
		}
		.bold-text{
			font-weight: bold;
		}

		#nb,#nb td,#nb th{
			border: none !important;
			font-family: Calibri, sans-serif;
		}
		.highlight{
			font-weight: bold;
			font-size: 20px;
			
		}
		.highlight2{
			font-weight: bold;
			font-size: 17px;
			
		}
		.highlight .col_amount{
			text-decoration: underline !important;
		}


	</style>
</head>

<body>
	<?php
		$total_paid_key = array();
		$change_key = "";
		foreach($transaction_summary as $t=>$v){
			
			if($v[0]->payment > 1 && $v[0]->payment < 999){
				array_push($total_paid_key,$t);
			}

			if($v[0]->payment != 999){
				$trans_summary[$t] = $t;
			}else{
				$change_key =$t;
			}
			
		}
		// $trans_summary1 = array(
		// 	// 'Cash on Hand' => 'Cash on Hand',
		// 	// 'Cash in Bank-BDO' => 'Cash in Bank-BDO',
		// 	// 'Cash in Bank-SMRB' => 'Cash in Bank-SMRB',
		// 	'Loans Receivables' => 'Loans Receivables',
		// 	'Interest Income on Loans' => 'Interest Income on Loans',
		// 	'Penalty & Surcharges' => 'Penalty & Surcharges',
		// 	'Miscellaneous Income' => 'Miscellaneous Income',
		// 	'Capital Share' => 'Capital Share',
		// 	'Members Benefits Fund Payable'=>'Members Benefits Fund Payable',
		// 	'Change Payable' => 'Change Payable'
		// );



		// $total_paid_key  = ['Loans Receivables','Interest Income on Loans','Penalty & Surcharges','Miscellaneous Income','Capital Share','Members Benefits Fund Payable'];

		$total_payment_received = 0;

		$total_paid = 0;
	?>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			<p style="font-size: 20px;margin-top: -15px"><b><?php echo e(config('variables.coop_abbr')); ?> </b></p>
			<p style="font-size: 15px;margin-top: -15px"><b>Summary of Loan Loan Payment</b></p>
			<p style="font-size: 15px;margin-top: -15px"><b><?php echo e($transaction_date); ?> (<?php echo e($trans); ?>)</b></p>
			<p></p>
			<p></p>
		</div> 
	</header>
	<div class="row">
		<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black" class="tbl_repayment_summary">
			<thead>
				<tr>
					<th width="1cm">Item</th>
					<th>Name of Borrower</th>
					<th>Loan Dues</th>
					<!-- <th style="width:3cm">Amount Due</th> -->
					<th style="width:3cm">Payment</th>
					<th style="width:4cm">Signature</th>
				</tr>
			</thead>
			<tbody>
				<?php $counter = 1; ?>
				<?php $__currentLoopData = $repayment_summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name=>$rs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php $item_content_count = count($rs); 
						$total_due = 0;
						$total_payment = 0;

						$ex = ['Check Amount','Change'];


				?>

				<?php $__currentLoopData = $rs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

			
				<tr>
					<?php if($c == 0): ?>
					<td rowspan="<?php echo e($item_content_count); ?>" style="text-align:center;"><?php echo e($counter); ?></td>
					<td rowspan="<?php echo e($item_content_count); ?>"><?php echo e($name); ?></td>
					<?php endif; ?>

					<?php
					$highlight_amount = (in_array($val->description,['Total Payment','Check Amount','Change']))?"highlight_amount":"";
					$bold_text = (in_array($val->description,['Total Payment','Check Amount','Change']))?"bold-text":"";


					$due = (in_array($val->description,$ex))?'':number_format($val->due,2);
					$amt = number_format(abs($val->amount),2);
					$amt = ($val->amount < 0)?"($amt)":$amt;
					?>
					<td class="<?php echo e($bold_text); ?>"><?php echo e($val->description); ?></td>


					<!-- <td class="col_amount"><?php echo e($due); ?></td> -->

					<td class="col_amount"><?php echo e($amt); ?></td>
					<?php
						$total_due += $val->due;
						$total_payment += $val->amount;
					?>
					<?php if($c == 0): ?>
					<td rowspan="<?php echo e($item_content_count); ?>"></td>
					<?php endif; ?>

				</tr>

				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				<?php $counter++; ?>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			</tbody>

		</table>
		<h3 style="margin-top: 1cm;font-family: Calibri, sans-serif;padding-bottom: unset;">Total Summary</h3>
		<table style="border:none !important;width: 50%;margin-top: -0.3cm;font-size: 17px;" id="nb">
			<?php $__currentLoopData = $trans_summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php if(isset($transaction_summary[$key])): ?>
				<?php
					$amt = $transaction_summary[$key][0]->amount;
					if($key == 'Members Benefits Fund Payable'){
						$amt_text = "(".number_format($amt,2).")";
						$amt = $amt*-1;
					}else{
						$amt_text = number_format($amt,2);
					}

					$class = ($key == "Change Payable")?"highlight":"";
				
					if(in_array($key,$total_paid_key)){
						$total_paid += $amt;
					}
					// id_chart_account_category
					if($transaction_summary[$key][0]->id_chart_account_category <=2){
						$total_payment_received += $transaction_summary[$key][0]->amount;
					}
					
				?>
				<tr class="<?php echo e($class); ?>">
					<td><?php echo e($desc); ?></td>
					<td class="col_amount"><?php echo e($amt_text); ?></td>
				</tr>
				<?php endif; ?>

			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<tr class="highlight2">
				<td>Total Paid Amount</td>
				<td class="col_amount"><?php echo e(number_format($total_paid,2)); ?></td>
			</tr>	

			<?php if($trans != "Cash"): ?>
			<tr class="highlight2">
				<td>Total Amount Received</td>
				<td class="col_amount"><?php echo e(number_format($total_payment_received,2)); ?></td>
			</tr>

			<?php endif; ?>

			<?php if($change_key != ""): ?>
			<tr class="highlight">
				<td><?php echo e($change_key); ?></td>
				<td class="col_amount"><?php echo e(number_format($transaction_summary[$change_key][0]->amount,2)); ?></td>
			</tr>
			<?php endif; ?>
		</table>
	</div>




</body>
</html>

<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment/print_summary.blade.php ENDPATH**/ ?>