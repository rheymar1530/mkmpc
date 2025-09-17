<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Print OR</title>
	<style type="text/css">
		@page {
			margin:  0;
			size: portrait;
		}
		@media print {
			@page {
				size: portrait;
				margin-top: 0.2cm;
				margin-left: 0.4cm;
				padding: 0;
/*				margin-top: 0.65cm;
				margin-left: 0.2cm;
				padding: 0;*/


			}
		}
		*{
			font-family: 'Calibri';
		}
		#tn_box{
			width: 9.3cm !important;
			overflow: hidden;
			padding: 0;
			margin: 0;
			height: 12.8cm;
        }
		.column {
			float: left;
		}
		#head-section-date{
			width: 100%;
			height: 2.7cm;
			padding: 0;
			margin: 0;
		}
		#payment-details-section{
			width: 100%;
			height: 2.2cm;
			padding: 0;
			margin: 0;
			position: relative;
		}
		#transaction-details-section{
			width: 100%;
			height: 4.1cm;
			padding: 0;
			margin: 0;
		}
		#foot-details-section{
		    width: 100%;
		    height: 3.8cm;
		    padding: 0;
		    margin: 0;
		    display: flex; /* so children align horizontally */
		    align-items: stretch; /* make them match parent height */
		}
		.foot-box {
		    height: 100%; /* relative to parent height */
		}

		.foot-box-1 {
		    width: 4.6cm;
		    position: relative;
		    height: 100%;
		}

		.foot-box-1 span {
		    position: absolute;
		    top: 1cm;
		    left: 0.5cm;
		}
		.foot-box-2 {
		    width: 4.7cm;
		    position: relative;
		    height: 100%;
		}
		.text-center{
			text-align: center !important;
		}
		.text-right{
			text-align: right !important;
			padding-right: 2mm;
		}
		.item_description{
			height: 0.5cm ;
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;
			box-sizing: border-box;
			font-size: 13pt;
			padding-top: 1mm !important;
			padding-left: 1mm;
		}
      </style>

      <style type="text/css">
    	table {
    		width: 100%;
    		border-collapse: collapse !important;
    	}
    	.table_width{
    		width: 6.2cm;
    	}
    	.table_column_1_width_item{
    		width: 3cm;
    	}
    	.table_column_2_width_item{
    		width: 3.2cm;
    	}
    	.header_height{
    		height: 0.80cm;
    	}
    	td.table-column > div.item {
			box-sizing: border-box;
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;
			font-size: 10pt;
			vertical-align: text-bottom;
		}
		.item_3{
			height: 0.3cm !important;
		}
		.item_4{
			height: 0.4cm !important;
			padding-top: 0mm !important;
		}
		.item_55{
			height: 0.55cm !important;
		/*			padding-top: 0mm !important;*/

		}
		.item_7{
			height: 0.7cm !important;
			padding-top: 2mm !important;
		}
		.item_7_np{
			height: 0.7cm !important;
			padding-top: -5mm;

		}
		.item_6{
			height: 0.6cm !important;
			padding-top: 1mm !important;
		}

		.item_8{
			height: 0.8cm !important;
			padding-top: 2mm !important;
		}
		.amt{
			padding-right: 1mm !important;
		}
		.pmode{
			height: 1.5cm;
		}

		.hp4{
			height: 0.4cm !important;
			padding-top: 1mm !important;
		}
		.hp45{
			height: 0.45cm !important;
			padding-top: 1mm !important;
		}
		.hp5{
			height: 0.5cm !important;
			padding-top: 1mm !important;
		}
		.hp55{
			height: 0.55cm !important;
			padding-top: 1mm !important;
		}
		.f-item > div.item{
			font-size: 8pt !important;
		}
	</style>
	<style type="text/css">
		.or_details{
			width: 12.1cm;
		}
		.header_height2{
			height: 2.7cm;
		}
		.div-relative{
			position: relative;
		}
		p.bottom-text{
			position: absolute; margin: 0; bottom: 0;
			width: 100%;
			box-sizing: border-box;
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;
			font-size: 10pt;
			vertical-align: text-bottom;
		}
		#tbl-comp div{
			padding-left: 2mm;
			padding-right: 2.5mm;
		}
		.td-tbl{
			padding-right: 1.25cm !important;
			padding-left: 1mm !important;
		}
	</style>
	<style type="text/css">
		/*.add_border_main {
			box-shadow:-1px 0 1px 1px rgba(0, 0, 0, 0.75), inset -1px 0 0 1px rgba(0, 0, 0, 0.75);
		}
		.add_border {
			box-shadow:-1px 0 1px 1px rgba(0, 0, 0, 0.75), inset -1px 0 0 1px rgba(0, 0, 0, 0.75);
		}*/
	</style>
	<style type="text/css">
		.dt-text {
		    position: absolute;
		    width: 100%;
		    bottom: 0;
		    margin-left: 7.2cm;
		    padding-bottom: 0.5mm;
		}
		.fs-details{
			font-size: 8pt;
		}
		.fs-transaction{
			font-size: 8pt !important;
		}
		.fs-amount{
			font-size: 8pt !important;
		}
	</style>
</head>
<!-- onload="window.print()" -->
<body onload="window.print()">
	<div class="row add_border_main" id="tn_box">
		<!-- Date -->
		<div class="column add_border_main" id="head-section-date" style="padding:0px !important;position: relative !important;">
			<span class="dt-text fs-details"><?php echo e($paymentDetails['date']); ?></span>
		</div>

		<!-- Payment Details -->
		<div class="column add_border_main" id="payment-details-section" style="padding:0px !important;">
			<table class="or_details add_border"  border="0" cellspacing="0" cellpadding="0" style="position: absolute;width: 100%;">
				<!-- margin-top: 0.4cm; -->
				<tr>
					<td class="table-column add_border" style="height: 0.4cm;min-width:2.8cm;max-width:2.8cm;padding-left: 1.8cm;" colspan="2">
						<div class="item fs-transaction">
							<?php echo e($paymentDetails['payee']); ?>

						</div>
					</td>
				</tr>
				<tr>
					<td class="table-column add_border" style="height: 0.7cm;min-width:2.8cm;max-width:2.8cm;">
						<div class="item fs-transaction">
							
						</div>
					</td>
					
					<td class="table-column add_border" style="height: 0.7cm;min-width:6.5cm;max-width:6.5cm;">
						<div class="item fs-transaction">
							<!-- <?php echo e($paymentDetails['payee']); ?>  -->
							<?php echo e($paymentDetails['or_number']); ?> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo e($paymentDetails['check_no']); ?>

							
						</div>
					</td>
				</tr>

				<tr>
					<td class="table-column add_border" style="height: 0.5cm;min-width:2.8cm;max-width:2.8cm;">
						<div class="item fs-transaction">
							
						</div>
					</td>
					
					<td class="table-column add_border" style="height: 0.5cm;min-width:6.5cm;max-width:6.5cm;">
						<div class="item fs-transaction">
							<!-- <?php echo e($paymentDetails['tin']); ?> -->
							
						</div>
					</td>
				</tr>

				<tr>
					<td class="table-column add_border" style="height: 0.6cm;min-width:2.8cm;max-width:2.8cm;">
						<div class="item fs-transaction">
							
						</div>
					</td>
					
					<td class="table-column add_border" style="height: 0.6cm;min-width:6.5cm;max-width:6.5cm;">
						<div class="item fs-transaction">
							<!-- <?php echo e($paymentDetails['address']); ?> -->
						</div>
					</td>
				</tr>
			</table>
		</div>

		<div class="column add_border_main" id="transaction-details-section" style="padding:0px !important;position: relative !important;">
			<table class="or_details add_border"  border="0" cellspacing="0" cellpadding="0" style="position: absolute;width: 100%;margin-top: 0.7cm;">
				<?php
					$itemHeight = [0.5,0.45,0.45,0.4,0.5,0.5,0.6];
				?>
				<?php $__currentLoopData = $itemHeight; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td class="table-column add_border" style="height: <?php echo $h;?>cm;min-width:4.6cm;max-width: 4.6cm;">
						<div class="item fs-transaction">
							<?php if(isset($Transactions[$i])): ?>
							<?php echo e($Transactions[$i]['description']); ?>

							<?php endif; ?>
						</div>
					</td>
			
					<td class="table-column add_border" style="height: <?php echo $h;?>cm;min-width:1.2cm;max-width: 1.2cm;">
						<div class="item fs-transaction">
							
						</div>
					</td>
					<td class="table-column add_border" style="height: <?php echo $h;?>cm;min-width:1.4cm;max-width: 1.4cm;">
						<div class="item fs-transaction">
							
						</div>
					</td>
					<td class="table-column add_border" style="height: <?php echo $h;?>cm;min-width:2.1cm;max-width: 2.1cm;">
						<div class="item fs-transaction text-right">
							<?php if(isset($Transactions[$i])): ?>
							<?php echo e(number_format($Transactions[$i]['amount'],2)); ?>

							<?php endif; ?>
							
						</div>
					</td>
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</table>
		</div>

		<div class="column add_border_main" id="foot-details-section" style="padding:0px !important;">
		    <div class="foot-box foot-box-1 add_border">
		    	<span class="fs-amount"><?php echo e(number_format($paymentDetails['total_amount'],2)); ?></span>

		    	<br>
		    	<span class="fs-amount" style="margin-top:0.8cm !important">Agnes Amorte</span>
		    </div>
		    <div class="foot-box foot-box-2 add_border">
		    	<table class="or_details add_border"  border="0" cellspacing="0" cellpadding="0" style="position: absolute;width: 100%">
		    	<tr>
					<td class="table-column add_border" style="height: 0.5cm;min-width:2.6cm;max-width:2.6cm;">
						<div class="item fs-amount">
							
						</div>
					</td>
					
					<td class="table-column add_border" style="height: 0.5cm;min-width:2.1cm;max-width:2.1cm;">
						<div class="item fs-amount text-right">
							<?php echo e(number_format($paymentDetails['total_amount'],2)); ?>

						</div>
					</td>
				</tr>
				<tr>
					<td class="table-column add_border" style="height: 0.65cm;min-width:2.6cm;max-width:2.6cm;">
						<div class="item fs-amount">
							
						</div>
					</td>
					
					<td class="table-column add_border" style="height: 0.65cm;min-width:2.1cm;max-width:2.1cm;">
						<div class="item fs-amount text-right">
							
						</div>
					</td>
				</tr>
				<tr>
					<td class="table-column add_border" style="height: 0.5cm;min-width:2.6cm;max-width:2.6cm;">
						<div class="item fs-amount">
							
						</div>
					</td>
					
					<td class="table-column add_border" style="height: 0.5cm;min-width:2.1cm;max-width:2.1cm;">
						<div class="item fs-amount text-right">
							
						</div>
					</td>
				</tr>
				<tr>
					<td class="table-column add_border" style="height: 0.5cm;min-width:2.6cm;max-width:2.6cm;">
						<div class="item fs-amount">
							
						</div>
					</td>
					
					<td class="table-column add_border" style="height: 0.5cm;min-width:2.1cm;max-width:2.1cm;">
						<div class="item fs-amount text-right">
							<?php echo e(number_format($paymentDetails['total_amount'],2)); ?>

						</div>
					</td>
				</tr>
		    	</table>

		    </div>
		</div>


	</div>
</body>
</html><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/cash_receipt/mk-or.blade.php ENDPATH**/ ?>