<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo e($file_name); ?></title>
	<style>
		.row:after {
			line-height: 10%;
			content: "";
			display: table;
			clear: both;
			height: 1px; 
		}
		.columnLeft {         
			float: left;
			width: 50%;
		}
		.columnRight {         
			float: right;
			width: 50%;
		}
		@page  {      
			margin-left: 1in;
			margin-right: 1in;
			margin-top:1in;
			size : letter landscape;
		}
		div.header{
			text-align: center;
			/*line-height: 1px;*/
			font-size: 15px;
			font-family: Calibri, sans-serif;
		}

		* {
			box-sizing: border-box;
			font-family: Calibri, sans-serif;
		}

		.tbl_gl  tr>td,.tbl_gl  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Arial, Helvetica, sans-serif !important;
			letter-spacing: 1px;
			font-size: 0.14in ;
		}



		.class_amount{
			text-align: right;
			padding-right: 2mm !important;
		}

		table.loan, .loan td,.loan th {
			border: 1px solid;
		}

		table.loan {
			width: 100%;
			border-collapse: collapse;
			border : 1px solid;	
		}

		.highlight_amount{
			font-weight: bold;
			text-decoration: underline;
			font-size: 12px !important;
		}
		.bold-text{
			font-weight: bold;
		}
		.tbl_head{
			border-top:  2px solid;
			border-bottom: 2px solid;
		}
		.col_border{
			/*border-left: 1px solid;*/
		}
		.year_head th{
			font-weight: normal !important;
		}
		.text-centered{
			text-align: center;
		}
		#head-tbl th{
			vertical-align: middle !important;
		}
		.mb-0{
			margin-bottom: 0 !important;
		}
		.my-0{
			margin-top: 0 !important;
			margin-bottom: 0 !important;
		}
		.font-weight-bold{
			font-weight: bold !important;
		}
	</style>
</head>
<body>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			<p style="font-size: 20px;margin-top: -15px"><b><?php echo e(config('variables.coop_name')); ?></b></p>
			<p style="font-size: 17px;margin-top: -21px"><?php echo e(config('variables.coop_address')); ?></p>
			<p style="font-size: 20px;margin-top: -15px"><b>Loan Payment Summary</b></p>

		</div> 
	</header>
	<?php
		$total = 0;


		if($details->id_paymode == 4){
			$PaymentOBJ = array(
				'Check Type'=>'check_type',
				'Check Bank'=>'check_bank',
				'Check Date'=>'check_date',
				'Check No'=>'check_no',
				'Amount'=>'amount',
				'Remarks'=>'remarks'
			);

			$PColCount = 5;
		}else{

		}

		$totalPayment = 0;
	?>	
	<div class="row" style="margin-top: 0.5cm;">
		<div class="columnLeft">
			<p class="my-0"><b>Loan Payment ID: </b><?php echo e($details->id_repayment); ?></p>
			<p class="my-0"><b>Date: </b><?php echo e($details->date); ?></p>
			<p class="my-0"><b>OR Number: </b><?php echo e($details->or_number); ?></p>
		</div>
		<div class="columnRight">
			<p class="my-0"><b>Paymode: </b><?php echo e($details->paymode); ?></p>
			<p class="my-0"><b>Total Amount: </b><?php echo e(number_format($details->total_amount,2)); ?></p>
			<p class="my-0"><b>Remarks: </b><?php echo e($details->remarks); ?></p>
		</div>		
	</div>
	<div class="row">
		<table class="tbl-mngr loan" width="100%">
			<thead>
				<tr class="text-center">
					<th>Member</th>
					<th>Loan Service</th>
					<th>Payment</th>
					<th width="10%"></th>
				</tr>
			</thead>
			<tbody>
			<?php if($details->payment_for_code == 2): ?>
					<?php $__currentLoopData = $statamentData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statementDescription=>$MemberData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr class="statement-head bg-gradient-success2 font-weight-bold">
						<td colspan="4"><b><?php echo e($statementDescription); ?></b></td>
					</tr>

						<?php $__currentLoopData = $MemberData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m=>$data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php $length = count($data);?>
							<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

							<tr>
								<?php if($c == 0): ?>
								<td class="" rowspan="<?php echo e($length); ?>"><i><?php echo e($row->member); ?> </i></td>
								<?php endif; ?>
								<td><?php echo e($row->loan_name); ?></td>
								<td class="class_amount"><?php echo e(number_format($row->payment,2)); ?></td>
								<?php $total += $row->payment; ?>
								<?php if($c == 0): ?>
								<td class="font-weight-bold text-center" rowspan="<?php echo e($length); ?>">
									<?php if($row->id_cash_receipt_voucher  > 0): ?>
									CRV-<?php echo e($row->id_cash_receipt_voucher); ?>

									<?php endif; ?>
								</td>
								<?php endif; ?>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				<?php else: ?>
					<?php $__currentLoopData = $Loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member=>$rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php
						$length = count($rows);
					?>
						<?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<?php if($c == 0): ?>
							<td class="" rowspan="<?php echo e($length); ?>"><i><?php echo e($row->member); ?> </i></td>
							<?php endif; ?>
							<td><?php echo e($row->loan_name); ?></td>
							<td class="class_amount"><?php echo e(number_format($row->payment,2)); ?></td>
							<?php $total += $row->payment; ?>
							<?php if($c == 0): ?>
								<td class="font-weight-bold text-center" rowspan="<?php echo e($length); ?>">
									<?php if($row->id_cash_receipt_voucher  > 0): ?>
									CRV-<?php echo e($row->id_cash_receipt_voucher); ?>

									<?php endif; ?>
								</td>
							<?php endif; ?>
						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				<?php endif; ?>
			</tbody>
			<tr>
				<td colspan="2" class="bold-text">Total</td>
				<td class="class_amount bold-text"><?php echo e(number_format($total,2)); ?></td>
				<td></td>
			</tr>

		</table>
	</div>

	<?php if($details->id_paymode ==4): ?>
	<div class="row" style="margin-top:1cm">
		<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
			<thead>
				<tr class="text-center">
					<th></th>
					<?php $__currentLoopData = $PaymentOBJ; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $head=>$key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<th><?php echo e($head); ?></th>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tr>
			</thead>
			<tbody>
				<?php $__currentLoopData = $paymentDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$pd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td class="text-centered"><?php echo e($i+1); ?></td>
					<?php $__currentLoopData = $PaymentOBJ; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<td class="<?php echo ($key=='amount')?'class_amount':''; ?>" >
						<?php if($key=='amount'): ?>
						<?php echo e(number_format($pd->{$key},2)); ?>

						<?php
						$totalPayment += $pd->{$key};
						?>
						
						<?php else: ?>
						<?php echo e($pd->{$key}); ?>

						<?php endif; ?>
						</td>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
			<?php if(count($paymentDetails) > 1): ?>
			<tr>
				<td class="font-weight-bold" colspan="<?php echo e($PColCount); ?>">Total Payment</td>
				<td class="class_amount font-weight-bold"><?php echo e(number_format($totalPayment,2)); ?></td>
				<td></td>
			</tr>
			<?php endif; ?>
			<?php if($details->change_payable > 0): ?>
			<tr class="text-success">
				<td colspan="<?php echo e($PColCount); ?>" class="font-weight-bold">Change</td>
				<td class="class_amount font-weight-bold"><?php echo e(number_format($details->change_payable,2)); ?></td>
				<td></td>
			</tr>
			<?php endif; ?>
		</table>
	</div>
	<?php endif; ?>
<table width="100%" style="border-spacing: 0.5cm !important;border-collapse: separate; margin-top: 1.5cm;">
	<tr>
		<td class="text_ex" style="border-bottom: 1px solid"></td>
		<td class="text_ex" style="border-bottom: 1px solid;"></td>
		
		<td class="text_ex" style="border-bottom: 1px solid"></td>
	</tr>
</table>
<table width="100%" style="margin-top: -0.3cm;font-size:3.9mm !important">
	<tr>
		<td class="text_ex" style="width: 33.33%;text-align: center;">Prepared by</td>
		<td class="text_ex" style="width: 33.33%;text-align: center">&nbsp;Noted by</td>
		
		<td class="text_ex" style="width: 33.33%;text-align: center">&nbsp;Approved by</td>
		
	</tr>
</table>
</body>
</html><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment-bulk/print.blade.php ENDPATH**/ ?>