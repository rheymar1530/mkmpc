<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title></title>
	<style type="text/css">
		@page {      
			margin-left: 0.8cm;
			margin-right: 0.8cm;
			margin-top: 0.5cm;
			margin-bottom: 0.5cm; 

			size: legal portrait; 
		}
		div.a {
			line-height: 50%;
		}
		.fnormal,.table_entry_font{
			font-weight: 200 !important;
		}
		.fbold{
			font-weight: 700 !important;
		}
		* {
			box-sizing: border-box;
			font-family:"Calibri" !important;
		}
		.columnLeft {         
			float: left;
			width: 60%;

		}

		.columnRight {         
			float: right;
			width: 40%;

		}

		.row:after {
			line-height: 10%;
			content: "";
			display: table;
			clear: both;
			height: 1px; 
		}

		.page_break { 
			page-break-before: always; 
		}

		.text-center{
			text-align: center;	
		}


		table.bordered,.bordered td,.bordered th {
			border: 1px solid;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}
		.my-0{
			margin-top: 0;
			margin-bottom: 0;
		}
		.px-2{
			margin-right: 0.75rem;
			margin-left: 0.75rem;
		}
		.text-right,.class_amount{
			text-align: right;
			padding-right: 0.6rem;
		}
		.bbottom{
			border-bottom: 1px solid;
		}
		.bold{
			font-weight: 600;
		}
		.ftsize{
			font-size: 13pt !important;
		}
		.td-p{
			padding-left: 0.6rem;
		}
		.b{
			border: 1px solid !important;
		}
		.bl-none{
			border-left: none !important;
		}
		.br-none{
			border-right: none !important;
		}
		.br{
			border-right: 1px solid;
		}
		.b-top{
			border-top: 1px solid;
		}
		.b-bottom{
			border-bottom: 1px solid;
		}
		#tbl_m td{
			padding: 0.6rem;
			font-size: 14pt;
		}
	</style>
</head>
<body style="padding-top:0.3cm">
	<!-- border-bottom: 4px double #000; -->
	<div style="border: 1px solid;padding-top:0.6;">
		<div class="row" style="border-bottom: 1px solid;">
			<div class="columnLeft">
				<h2 class="my-0 px-2" style="margin-top:0.5cm !important">CASH RECEIPT VOUCHER</h2>
			</div>
			<div class="columnRight" style="padding-left:1.9cm">
				<h2 class="my-0"><?php echo e(env('APP_NAME')); ?><b></h2>
					<p class="my-0 fnormal" style="font-size: 13pt"><?php echo e(config('variables.coop_district')); ?></p>
					<p class="my-0 fnormal" style="padding-bottom:0.6;font-size: 13pt"><?php echo e(config('variables.coop_address')); ?></p>
				</div>
			</div>
			<div class="row" style="padding:0.6rem;padding-bottom: 0;padding-left: 0;padding-right: 0;">
				<table class="ftsize" width="100%">
					<colgroup width="25%"></colgroup>
					<colgroup width="25%"></colgroup>
					<colgroup width="30%"></colgroup>
					<colgroup width="20%"></colgroup>
					<tr>
						<td class="td-p" colspan="3">Received from: <span class="fnormal"><?php echo e($crv_details->payee); ?></span></td>
						<td class="td-p">Date: <span class="fnormal"><?php echo e($crv_details->date); ?></span></td>
					</tr>
					<tr>
						<td class="td-p" colspan="3">Address: <span class="fnormal"><?php echo e($crv_details->address); ?></span></td>
						<td class="td-p">CRV No.: <span class="fnormal"><?php echo e($crv_details->id_cash_receipt_voucher); ?></span></td>
					</tr>
					<tr class="">
						<td class="td-p b" style="border-left: none !important;">Payment Type:  <span class="fnormal"><?php echo e($crv_details->paymode); ?></span></td>
						<td class="td-p b">OR/AR No:  <span class="fnormal"><?php echo e($crv_details->or_no); ?></span></td>
						<td class="td-p b">Amount: <span class="fnormal"><?php echo e(number_format($crv_details->total_amount,2)); ?></span></td>
						<td class="td-p b">Branch: <span class="fnormal"><?php echo e($crv_details->branch_name); ?></span></td>
					</tr>
				</table>	
			</div>
			<div class="row" style="padding:0.6rem">
				<p class="my-0 ftsize"><b>Particulars: </b><span class="fnormal"><?php echo e($crv_details->description); ?></span></p>
			</div>
			<div class="row" style="padding:0.6rem;padding-bottom: 0;padding-left: 0;padding-right: 0;">
				<table class="ftsize">
					<colgroup style="width: 2cm;"></colgroup>
					<colgroup style="width: 9cm;"></colgroup>
					<colgroup style="width: 3.5cm;"></colgroup>
					<colgroup style="width: 3.5cm;"></colgroup>
					<colgroup ></colgroup>
					<thead>
						<tr class="">
							<th class="td-p b bl-none">Code</th>
							<th class="td-p b">Accounts</th>
							<th class="td-p b">Debit</th>
							<th class="td-p b">Credit</th>
							<th class="td-p b br-none">Remarks</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$total_debit=0; $total_credit = 0;
						?>
						<?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $crv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td class="table_entry_font td-p br"><?php echo e($crv->account_code); ?></td>
							<td class="table_entry_font td-p br"><?php echo e($crv->description); ?></td>

							<td class="table_entry_font class_amount td-p br"><?php echo e(($crv->debit > 0)?number_format($crv->debit,2):''); ?></td>
							<td class="table_entry_font class_amount td-p br"><?php echo e(($crv->credit > 0)?number_format($crv->credit,2):''); ?></td>
							<td class="table_entry_font td-p" style="font-size: 10pt !important"><?php echo e($crv->details); ?></td>
							<?php
							$total_debit += $crv->debit;
							$total_credit += $crv->credit;
							?>
						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
					<tr>
						<td class="td-p br b-top b-bottom" colspan="2">Total</td>
						<td class="class_amount br b-top b-bottom"><?php echo e(number_format($total_debit,2)); ?></td>
						<td class="class_amount br b-top b-bottom"><?php echo e(number_format($total_credit,2)); ?></td>
						<td class="b-top b-bottom"></td>
					</tr>
				</table>	
			</div>

			<div style="padding:0rem;margin-top: -0.5mm;margin-left: 0.5mm !important;">
				<table class="" id="tbl_m">
					<colgroup style="width:5.5cm"></colgroup>
					<colgroup style="width:5.5cm"></colgroup>
					<colgroup style="width:7cm"></colgroup>
					<colgroup></colgroup>
					<tr>
						<td class="td-p br ftsize" style="padding-bottom: 0.5cm;">Prepared by: <br> <br>
							<span class="fnormal" style="padding-top:1.5cm !important">&nbsp;<?php echo e(config('variables.treasurer')); ?></span>
						</td>
						<td class="td-p br ftsize" style="padding-bottom: 0.5cm;">Checked by: <br> <br>
							<span class="fnormal" style="padding-top:1.5cm !important">&nbsp;<?php echo e(config('variables.v_checked_by')); ?></span>
						</td>
						<td class="td-p br ftsize" style="padding-bottom: 0.5cm;">Approved by: <br> <br>
							<span class="fnormal" style="padding-top:1.5cm !important">&nbsp;<?php echo e(config('variables.v_approved_by')); ?></span>
						</td>
						<td class="td-p ftsize" style="padding-bottom: 0.5cm;">Received by: <br> <br>
							<span class="fnormal" style="padding-top:1.5cm !important">&nbsp;<?php echo e(config('variables.treasurer')); ?></span>
						</td>
					</tr>
				</table>	
			</div>

		</div>
			<div class="row">
		<table style="margin-top:0.1cm" width="100%">
			<tr>
				<td class="font_head foot-note fnormal" colspan="3" style="text-align:right"><i><strong>Printed by:</strong>&nbsp;<?php echo e(MySession::PrintNote()); ?></i></td>
			</tr>		

		</table>
	</div>
	</body>

	</html><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/cash_receipt/print_crv_new.blade.php ENDPATH**/ ?>