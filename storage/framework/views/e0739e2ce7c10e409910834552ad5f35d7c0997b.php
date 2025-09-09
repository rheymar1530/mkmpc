<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo e($file_name); ?></title>
	<style type="text/css">
		@page  {      
			margin-left: 1cm;
			margin-right: 1cm;
			margin-top: 1cm;
			margin-bottom: 0.5cm; 

			size: legal portrait; 
		}
		div.a {
			line-height: 50%;
		}
		p{
			font-size: 13pt;
		}

		* {
			box-sizing: border-box;
			font-family:"Calibri" !important;
		}
		.head{
			font-size: 17pt !important;
		}
		.head_others{
			font-size: 15pt !important;
		}


		.font_head{
			font-size: 12px;
		}
		.foot-note{
			font-size: 10px;
		}
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
			line-height: 10%; 
		}

		.columnRight {         
			float: right;
			width: 50%;
			line-height: 10%;
		}

		table, th, td {
			border-collapse: collapse;
			padding-left: 1mm;
		}
		table td,table th{
			border : 1px solid;
		}
		table{
/*			border: 2px solid;*/
}
.with_border{
	border: 1px solid black;
}
.class_amount{
	text-align: right;
	padding-right: 2mm;
}
.text-center{
	text-align: center!important;
}
.mb-0{
	margin-bottom: 0px !important;
	margin-top: 0px !important;
}
.bold{
	font-weight: bold;
}
.name {
	display: inline-block;
}

.spacer {
	display: block;
	width: fit-content; /* Adjust the width to match the first <p> */

}
div.x {
	display: inline-block;
}
/*		div.x p{
			padding-left: 5mm;
			padding-right: 5mm;
		} */
	</style>
</head>
<body>
	<div class="text-center">
		<h3 style="font-size:14pt !important;font-weight: normal;" class="head mb-0"><?php echo e(config('variables.coop_name')); ?></h3>
		<h3 style="font-size:14pt !important;font-weight: normal;" class="head mb-0">Maasin, Iloilo</h3>
		<p class="head_others mb-0">CDA Registration No. <?php echo e(config('variables.fs_cda_reg_no')); ?></p>
<!-- 				<br>
		<h1 style="font-size:25pt !important" class="mb-0">MANAGER CERTIFICATION</h1>
		<br> -->
	</div>
	<div class="row" style="margin-top:1cm">
		<div style="">
			<p class="n mb-0" ><u>&nbsp;<?php echo e($details->statement_date); ?>&nbsp;</u></p>
		</div>
		<div class="x">
			<p class="n mb-0 name" ><u>&nbsp;<?php echo e($details->treasurer); ?>&nbsp;&nbsp;&nbsp;&nbsp;</u></p>
			<p class="mb-0 spacer" ><?php echo e($details->group_); ?> Treasurer&nbsp;</p>
			<p class="mb-0 spacer"><?php echo e($details->group_shortcut); ?> <?php echo e($details->baranggay_lgu); ?>&nbsp;</p>
		</div>
	</div>
	<div class="row" style="margin-top: 0.5cm;">
		<p class="mb-0">Maam/Sir:</p>
		<p>Good Day!</p>
		<p>I would like to inform you that the following names with have an approved/released loan at Maasin Kawayan MPC. Below is the deduction for the month of <?php echo e($details->month_due); ?></p>
	</div>
	<div class="row">
		<table class="tbl-mngr" width="100%">
			<thead>
				<tr class="">
					<th class="" style="width: 1cm;"></th>
					<th style="width: 10cm;">BORROWERS'S NAME</th>
					<th class="">LOAN TYPE</th>
					<th style="width: 3cm;">AMOUNT</th>
				</tr>
			</thead>
			<?php
			$total = 0;
			$count = 1;
			?>
			<?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id_member=>$loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tbody>
				<?php $__currentLoopData = $loan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$lo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr class="rloan">
					<?php if($c == 0): ?>
					<td class="font-weight-bold nowrap text-center" rowspan="<?php echo e(count($loan)); ?>"><?php echo e($count); ?></td>
					<td class="font-weight-bold nowrap" rowspan="<?php echo e(count($loan)); ?>"><?php echo e($lo->member); ?></td>
					<?php endif; ?>
					<td class="nowrap"><sup>[<?php echo e($lo->id_loan); ?>]</sup><?php echo e($lo->loan_name); ?></td>
					<td class="class_amount"><?php echo e(number_format($lo->current_due,2)); ?></td>
					<?php
					$total += $lo->current_due;
					?>
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
			<?php $count++;?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<footer>
				<tr>
					<th colspan="3" class="class_amount">TOTAL</th>
					<th class="footer_fix class_amount" ><?php echo e(number_format($total,2)); ?></th>

				</tr>
			</footer>

		</table>
	</div>
	<div class="row" style="margin-top: 0.5cm;">
		<p>Please implement the deduction of borrower's in their monthly ammortization, MKMPC, Liga and Barangay have MOA.</p>
	</div>
	<div class="row" style="margin-top: -0.5cm;">
		<p>Thank you, GO Cooperative....</p>
	</div>
	<div class="row" style="">
		<p>Very Truly yours,</p>
	</div>
	<div class="row" style="margin-top: 1cm;">
		<div class="x">
			<p class="n mb-0 name" >&nbsp;JOSEPHINE MANERO&nbsp;</p>
			<p class="mb-0 spacer text-center" >Manager</p>
		</div>
	</div>
</body>
</html><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment-statement/print.blade.php ENDPATH**/ ?>