<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Maasin Loan Waiver</title>
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

		* {
			box-sizing: border-box;
			font-family:"Calibri, sans-serif";
		}
		.head{
			font-size: 15pt important;
		}
		.head_others{
			font-size: 10pt !important;
		}
		.fill{
			font-weight: bold;
		}
		.p_body,.body_no_indent{
			line-height: 13pt;
			font-size: 10pt;
		}
		.p_body{
			margin-bottom: -3mm !important;
		}
		.tbl_sig td,.fn{
			font-size: 10pt;
		}
		.p_body3,.p_body2{
			line-height: 0.8rem;
		}
		.body_div{
			margin-top: -15px;
			white-space: normal;
			line-height: 18px;
			text-align: justify
		}
		.p_body:before,.p_body3:before { 
			content: "\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0 "; 
		}
		.columnLeft {         
			float: left;
			width: 50%;

		}
		.columnRight {         
			float: right;
			width: 50%;

		}

		.row:after {
			line-height: 10%;
			content: "";
			display: table;
			clear: both;
			height: 1px; 
		}
		.font{
			font-size: 13px;
		}
		.fill_bottom{
			width: 300px;
			border-bottom: 1px solid;
			text-align: center;
			padding-bottom: 5px;
		}
		.fill_label{
			width: 300px;
			text-align: center;
		}
		.page_break { 
			page-break-before: always; 
		}
		.font-sd{
			font-size: 16px;
		}
		tr.foot_lbl  td{

			font-size: 0.75rem;
			text-align: center;
			vertical-align: bottom; !important;

		}
		.text-right{
			text-align: right;
		}

		.pl{
			padding-left: 0.3cm;
		}
		.p-top{
			padding-top: 1cm !important;
		}
		.list_ts p{
			margin-bottom: 0rem;
			line-height: 0.8rem;

		}
		ol.list_ts > li{
			margin-top: -0.6rem !important;
			font-size: 0.8rem;
		}
		.text-center{
			text-align: center;	
		}


		table.bordered,.bordered td,.bordered th {
			border: 1px solid;
		}

		table.bordered {
			width: 100%;
			border-collapse: collapse;
		}
		.bold{
			font-weight: 600;
		}
		.wspace{
			white-space: nowrap !important;
		}
		.bbtom td,.bbtom th,div.bbtom,div.div-loan-app{
			border-bottom:  1px solid;
		}
		.nb{
			border-bottom: none !important;
		}
		.app_table td{
			font-size: 10pt !important;
		}
		th.pad,td.pad{
			padding-left: 3mm;
			padding-right: 3mm;
		}
		.app_table2 td{
			font-size: 9pt !important;
		}
		.pdtop td{
			padding-top: 2mm !important;
		}
		.ftcustom th{
			font-size: 10pt !important;
		}
		.maker-co{
			font-size: 8pt !important;
		}
	</style>
</head>
<?php

	$worded_amount = WebHelper::amountToWords($loan_details->amtz);
	$worded_principal = WebHelper::amountToWords($loan_details->principal_num);
	$worded_interest = WebHelper::amountToWords($loan_details->interest_rate);
?>
<body>
	<div class="a" style="text-align:center;">
		<h3 class="head"><span><?php echo e(config('variables.coop_name')); ?></span></h3>
		<p style="margin-top:-10px" ><span class="head_others"><b>(<?php echo e(config('variables.coop_abbr')); ?>)</b> TIN-<?php echo e(config('variables.coop_tin')); ?></span></p>
		<p style="margin-top:-10px" ><span class="head_others"><?php echo e(config('variables.coop_address')); ?></span></p>
		
	</div>
	<?php if($loan_details->memb_type > 1): ?>
	<div class="a" style="text-align:center;">
		<h3 style="font-size:12pt;margin-top: 0.1cm;" class="head"><span>AUTHORIZATION</span></h3>
	</div>

	<div class="a body_div">
		<p class="body_no_indent">
			I hereby authorize the <?php if($loan_details->memb_type == 3): ?> Local Government Unit, Maasin, Municipal Accounting Office <?php else: ?> Barangay Treasurer of Barangay <u class="fill"><?php echo e($loan_details->brgy_lgu); ?></u> <?php endif; ?> to deduct the amount of <u class="fill"><?php echo e($worded_amount); ?></u> (<u class="fill">P<?php echo e(number_format($loan_details->amtz,2)); ?></u>) from my monthly salary, bonuses, cash gift, clothing or from other allowances, differentials, remuneration including terminal leave benefits effective from the date of my loan granted up to date of maturity representing payment of my loan to MKMPPC. 
		</p>
		<p class="body_no_indent" style="margin-top:-0.3cm !important">Conforme:</p>
	</div>


	<table width="100%" class="tbl_sig" style="border-spacing: 10px 0px 10px 0px;border-collapse: separate;margin-top:-0.7cm" >
		<tr>
			<td class="text_ex p-top text-center" style="width: 33.33%;border-bottom: 1px solid;"><?php echo e($loan_details->treasurer); ?></td>
			<td class="text_ex p-top text-center" style="width: 33.33%;border-bottom: 1px solid;"><?php echo e($loan_details->chairman); ?></td>
			<td class="text_ex p-top text-center" style="width: 33.33%;border-bottom: 1px solid;"><?php echo e($loan_details->name); ?></td>
		</tr>
		<tr class="foot_lbl">
			<td class="text_ex" style="width: 33.33%;"><?php echo e($loan_details->memb_type == 2?'Bgry. Treasurer':'Admin Officer IV'); ?></td>
			<td class="text_ex" style="width: 33.33%;"><?php echo e($loan_details->memb_type == 2?'Bgry. Chairman':'Municipal Accountant'); ?></td>
			<td class="text_ex" style="width: 33.33%;"><b>Borrower</b> <small>(Signature over printed name)</small></td>
		</tr>

	</table>
	<?php endif; ?>
	<div class="row" style="margin-top:3mm">
		<div class="columnLeft">
			<p class="fn"><b>PM No.: ________________</b></p>
		</div>
		<div class="columnRight">
			<h3 style="margin-top:2.5mm"><u>PROMISSORY NOTE</u></h3>
		</div>
	</div>
	<div class="row">
		<table class="tbl_sig" style="width:16cm" >
			<tr>
				<td class="wspace" style="width:4.4cm"><b>NAME OF BORROWER:</b></td>
				<td style="border-bottom: 1px solid;"><?php echo e($loan_details->name); ?></td>
			</tr>
			<tr>
				<td class="wspace" style="width:4.4cm"><b>AMOUNT GRANTED:</b></td>
				<td style="border-bottom: 1px solid;"><?php echo e($loan_details->principal_amount); ?></td>
			</tr>
			<tr>
				<td class="wspace" style="width:4.4cm"><b>DATE GRANTED:</b></td>
				<td style="border-bottom: 1px solid;"><?php echo e($loan_details->date_released); ?></td>
			</tr>
			<tr>
				<td class="wspace" style="width:4.4cm"><b>DUE GRANTED:</b></td>
				<td style="border-bottom: 1px solid;"><?php echo e($loan_details->maturity_date); ?></td>
			</tr>
		</table>
	</div>
	<div class="a body_div" style="margin-top: 0mm;">
		<p class="p_body"><b>FOR VALUE RECEIVED</b>, I/We promise to pay jointly and severally to the order of <b><?php echo e(config('variables.coop_name')); ?></b>, a cooperatively duly organized and existing under Philippine laws with principal office located at <?php echo e(config('variables.coop_address')); ?> the sum of <b>PESOS:</b> <u class="fill"><?php echo e($worded_principal); ?></u> (<u class="fill">P<?php echo e(number_format($loan_details->principal_num,2)); ?></u>) Philippine Currency with interest rate of <u class="fill"><?php echo e($worded_interest); ?></u> percent (<?php echo e($loan_details->interest_rate); ?>%) per annum from this date hereof, until fully paid on or before the due date, according to the attached schedule of payments. The said principal and interest shall be paid on monthly installment of <b>PESOS: </b><u class="full"><?php echo e($worded_amount); ?></u> (<u class="fill">P<?php echo e(number_format($loan_details->amtz,2)); ?></u>) Philippine Currency, for <u class="fill"><?php echo e($loan_details->terms ?? 1); ?></u> months effective <u class="fill"><?php echo e($loan_details->date_released); ?></u> and every month thereafter until <u class="fill"><?php echo e($loan_details->maturity_date); ?></u>
		</p>
		<p class="p_body">In case of default in the payment of any of the installment payments as it falls due, all other installment shall immediately become due and payable. In each event, borrower agrees to pay a penalty charge of two per cent __2___%) per month of the amount due from date of default until fully paid as liquidated damages. </p>
		<p class="p_body">
			In case of judicial and extrajudicial enforcement of this obligation of any part of it, the debtors waive all their rights under the provisions of <b>Rule 39, Section 12, of the Rules of Courts</b>, and the borrower, co-maker(s), and endorser(s) shall pay jointly and severally ten per cent(10%) of the amount due on the note as attorney’s fees which in no case shall be less than <b>ONE THOUSAND PESOS</b> (P1,000.00) exclusively of all costs and fees allowed by law and/or as stipulated in the contract of real estate/chattel mortgage and/or collateral/security agreement executed, if any.
		</p>
		<p class="p_body">
			I/We hereby agree and authorize <b>MKMPC</b> to encumber, assign or sell to any person or entity any right which may have under this Note and/or assignment, mortgage lien, pledge or other encumbrances constituted in favor of <b>MKMPC</b> pursuant to the provision of the Loan Agreement and this Note, if any. The consent herein granted is recognized and acknowledged by me/us as waiver to all intents and purposes, of whatever right l/We may have to notice of actual encumbrances or assignment. 
		</p>
		<p class="p_body">
			I/We authorize and empower <b>MKMPC</b> or its successors and assigns, without need of notice irrespective of the date of maturity, to deduct set off and apply any fund, securities or assets I/We have with the Cooperative in reduction of the amounts due under this Promissory Note. This Note s further subject to the terms and conditions of the Loan Application and Agreement, I/We have executed in favor of <b>MKMPC</b> and to its rules and regulations.
		</p>
		<p class="p_body">
			I/We further authorize <b>MKMPC</b> to deduct from my monthly salary bonuses, cash gift, clothing and other allowances/remuneration including terminal leave and retirement benefits if applicable, the amount representing monthly payments of my loan with <b>MKMPC</b>.
		</p>
		<p class="p_body">
			I/We further expressly submit to the jurisdiction of the proper courts in Iloilo City in the event of litigation arising from this Note.
		</p>
		<p class="p_body">
			<b>DEMAND AND DISHONOR WAIVED</b>.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Holder may accept partial payment reserving his right recourse against the accommodation co-maker/s and each all makers of this Note.
		</p>
	</div>
	
	<div class="row" style="margin-top:12mm">
		<table class="tbl_sig" width="100%">
			<tr class="text-center">
				<td width="50%" style="padding-left: 1mm;padding-right: 1mm"><div style="border-bottom:1px solid;"><?php echo e($loan_details->name); ?></div></td>
				<td width="50%">_________________________________________</td>
			</tr>
			<tr class="text-center">
				<td width="50%">(Signature Over Printed Name of Borrower)</td>
				<td width="50%">(Signature Over Printed Name of Spouse)</td>
			</tr>
		</table>
		<table class="tbl_sig" width="100%" style="margin-top: 9mm;">
			<tr class="text-center">
				<td width="50%" style="padding-left: 1mm;padding-right: 1mm"><div style="border-bottom:1px solid;"><?php echo e($comakers->comaker_name ?? ''); ?></div></td>
				
			</tr>
			<tr class="text-center">
				<td width="50%">(Signature Over Printed Name of Co-Maker)</td>
				<td width="50%"></td>
			</tr>
		</table>
	</div>
	<div class="text-center">
		<h4>SIGNED IN THE PRESENCE OF :</h4>
	</div>
	<div class="row" style="margin-top:10mm">
		<table class="tbl_sig" width="100%">
			<tr class="text-center">
				<td width="50%">_________________________________________</td>
				<td width="50%">_________________________________________</td>
			</tr>
			<tr class="text-center">
				<td width="50%">(Signature Over Printed Name of Witness)</td>
				<td width="50%">(Signature Over Printed Name of Witness)</td>
			</tr>
		</table>

	</div>

	<div class="page_break"></div>

	<?php echo $__env->make('loan.maasin_waiver_pg2', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/maasin_waiver.blade.php ENDPATH**/ ?>