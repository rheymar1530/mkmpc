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
		@page {      
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

		table, td, th {
			border: 1px solid;
		}

		table {
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
		.v-center{
			vertical-align: middle !important;
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
			<p style="font-size: 20px;margin-top: -15px"><b>Change Payables</b></p>

		</div> 
	</header>
	<?php
		$total = 0;
	?>
	<div class="row" style="margin-top: 0.5cm;">
		<div class="columnLeft">
			<p class="my-0"><b>Change ID: </b><?php echo e($details->id_change_payable); ?></p>
			<p class="my-0"><b>Date: </b><?php echo e($details->date); ?></p>
			<p class="my-0"><b>Loan Payment ID: </b><?php echo e($details->id_repayment); ?></p>
		</div>
		<div class="columnRight">
			<p class="my-0"><b>Total Amount: </b><?php echo e(number_format($details->total_amount,2)); ?></p>
			<p class="my-0"><b>Remarks: </b><?php echo e($details->remarks); ?></p>
			
		</div>		
	</div>
	<div class="row">
		<table class="tbl-mngr loan" width="100%">
			<thead>
				<tr class="text-center">
					<th width="5%"></th>
					<th>Member Name/Account</th>
					<th>Amount</th>
					<th>Reference</th>
					<th width="25%">Signature</th>
				</tr>
			</thead>
			<tbody>
				<?php $r=0; ?>
				<?php if(isset($Applications[1])): ?>
					<?php $__currentLoopData = $Applications[1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td class="text-centered"><?php echo e($c+1); ?></td>
						<td><?php echo e($row->reference); ?></td>
						<td class="class_amount"><?php echo e(number_format($row->amount,2)); ?></td>
						<td>
							<?php if($row->id_cash_disbursement  > 0): ?>
							CDV-<?php echo e($row->id_cash_disbursement); ?>

							<?php endif; ?>

						</td>
						<td><div style="height:1.4cm !important"></div></td>
					</tr>
					<?php $r = ($c+1); $total += $row->amount;?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php endif; ?>

				<?php if(isset($Applications[2])): ?>
					<?php $__currentLoopData = $Applications[2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td class="text-centered"><?php echo e($r+($c+1)); ?></td>
						<td><?php echo e($row->reference); ?></td>
						<td class="class_amount"><?php echo e(number_format($row->amount,2)); ?></td>
						<?php if($c == 0): ?>
						<td class="v-center" rowspan="<?php echo e(count($Applications[2])); ?>">CDV-<?php echo e($details->id_cash_disbursement); ?></td>
						<?php endif; ?>
						<td></td>
					</tr>
					<?php  $total += $row->amount;?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php endif; ?>
				<tr>
					<td colspan="2" class="font-weight-bold">Total</td>
					<td class="class_amount font-weight-bold"><?php echo e(number_format($total,2)); ?></td>
					<td colspan="2"></td>
				
				</tr>
			</tbody>
			
		</table>
	</div>

</body>
</html><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/change-payable/print.blade.php ENDPATH**/ ?>