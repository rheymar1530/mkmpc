<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>

		@page {      

		}


		* {
			box-sizing: border-box;
			 font-family: Arial, sans-serif;
		}
		.columnLeft {         
			float: left;
			width: 50%;
		}
		.columnRight {         
			float: right;
			width: 50%;
		}
/*		.tbl_payroll_summary  tr>td,.tbl_payroll_summary  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: "Calibri, sans-serif";
			font-size: 12px ;
		}*/

		.details_tbl_lbl{
			text-align: right !important;
			width: 40% !important;
		}	

		.row:after {
			line-height: 10%;
			content: "";
			display: table;
			clear: both;
			height: 2.5mm; 
		}

		.col_amount, .col_number{
			text-align: right;
			padding-right: 0.5cm !important;
		}
		.payslip_content{
			font-size: 4.2mm;
			font-family: Arial, sans-serif !important;
		}
		table, td, th {
			/*border: 1px solid;*/
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}
		.box{
			border: 1px solid;
			height: 4.33in;
			width: 100%;
		}
		.no_b{
			/*border-style: dotted;*/
			border: none !important;
		}
		.bold{
			font-weight: bold;
		}
		.align-right{
			text-align: right;
		}
		.test td{
			/*border: 1px solid;*/
		}

		.normal_pad{
			padding-left: 0.5cm !important;
		}
		.normal_pad2{
			padding-left: 0.7cm !important;
		}
		.head_padding{
			padding-left: 0.3cm !important;
		}
		.bold{
			font-weight: bold;
		}
		.payslip_details{
			padding-left: 3mm;
		}

		table#tbl_payslip,table#tbl_payslip th,table#tbl_payslip td {
		  border-right: 1px dashed;
		  border-left: none !important;
		  border-collapse: collapse;

		  font-size: 4mm;
		}
		.ad_font{
			font-size: 4.1mm !important;
		}
		.ad_font2{
			font-size: 3.9mm !important;
		}
		#tbl_payslip{

		}

		.head_pay{

			border-bottom: 1px dashed;
		}
	</style>
</head>

<body>

	<?php

		$adj_obj = [
			['key'=>'thir_month','label'=>'13TH MONTH','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'incentives','label'=>'INCENTIVES','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'paid_leaves','label'=>'PAID LEAVES','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'holiday','label'=>'HOLIDAY','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'others','label'=>'OTHERS','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'salary_adjustment','label'=>'SALARY ADJUSTMENT','is_label'=>false,'class'=>"normal_pad ad_font"],
			['key'=>'night_shift_dif','label'=>'NIGHT SHIFT DIFFERENTIAL','is_label'=>false,'class'=>"normal_pad ad_font2"]
		];

		$deduction_obj = [
			['key'=>'absences','label'=>'ABSENCES','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'late','label'=>'LATE','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'wt','label'=>'W/H TAX','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'sss','label'=>'SSS','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'philhealth','label'=>'PHILHEALTH','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'hdmf','label'=>'PAG-IBIG','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'insurance','label'=>'INSURANCE','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'ca','label'=>'CASH ADVANCES','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'','label'=>'LOANS:','is_label'=>false,'class'=>"head_padding bold"],
			['key'=>'sss_loan','label'=>'SSS LOAN','is_label'=>false,'class'=>"normal_pad"],
			['key'=>'hdmf_loan','label'=>'PAG-IBIG LOAN:','is_label'=>false,'class'=>"normal_pad"],
		];

		$summary_obj = [
			['key'=>'basic_pay','label'=>'BASIC PAY:','is_label'=>false,'class'=>"head_padding bold"],
			['key'=>'cola','label'=>'COLA:','is_label'=>false,'class'=>"normal_pad2"],
			['key'=>'ot','label'=>'OVERTIME:','is_label'=>false,'class'=>"normal_pad2"],
			['key'=>'total_allowance','label'=>'ALLOWANCES:','is_label'=>false,'class'=>"normal_pad2"],
			['key'=>'total_adjustments','label'=>'ADJUSTMENTS:','is_label'=>false,'class'=>"normal_pad2"],
			['key'=>'gross_income','label'=>'GROSS PAY:','is_label'=>false,'class'=>"head_padding bold"],
			['key'=>'total_deduction','label'=>'DEDUCTIONS:','is_label'=>false,'class'=>"normal_pad2"],
		];


	?>

	@foreach($payroll as $i=> $p)
	<div class="row" style="margin-top: <?php echo ($i>0)?'0.4cm':'0px' ?> ;page-break-inside:avoid !important;" > 
		<div class="box">
			<table width="100%" class="no_b">
				<tr>
					<th class="no_b" colspan="6" style="font-size:4.9mm">{{config('variables.coop_name')}}</th>
				</tr>
				<tr>
					<th class="no_b" colspan="6" style="font-weight:normal;">Payroll Payslip</th>
				</tr>
				<tr style="border: 1px dashed;border-left: none;">
					<td class="no_b payslip_content bold payslip_details" colspan="3">{{$p->payslip_description}}</td>
					<th class="no_b payslip_content align-right" >PERIOD:</th>
					<td class="no_b payslip_content "colspan="2" style="" >&nbsp;{{$p->period}}</td>
				</tr>
				<tr style="border: 1px dashed;border-left: none;">
					<td class="no_b payslip_content bold payslip_details" colspan="3">EMPLOYEE NAME : <span style="font-weight:normal">{{$p->name}}</span></td>
					<th class="no_b payslip_content align-right" >STATUS:</th>
					<td class="no_b payslip_content "colspan="2" style="" >&nbsp;{{$p->emp_status}}</td>
				</tr>
				<tr style="border: dashed 1px;border-left: none;">
					<td class="no_b payslip_content bold payslip_details" colspan="3">EMPLOYEE NO : <span style="font-weight:normal">{{$p->id_employee}}</span></td>
					<th class="no_b payslip_content align-right" >POSITION:</th>
					<td class="no_b payslip_content "colspan="2" style="" >&nbsp;{{$p->position}}</td>
				</tr>

			</table>
			<table width="100%" class="no_b" id="tbl_payslip">
				<tr class="head_pay">
					<th width="20%">ADJUSTMENTS</th>
					<th width="13.34%">AMOUNT</th>
					<th width="16.67%">DEDUCTION</th>
					<th width="16.67%">AMOUNT</th>
					<th colspan="2" style="border-right:none !important">SUMMARY</th>
				</tr>
				<?php
					$max_length = 5;
				?>
				@for($j=0;$j<11;$j++)
					<tr class="test">
						<td class="{{$adj_obj[$j]['class'] ?? ''}}">{{$adj_obj[$j]['label'] ?? ''}}</td>
						<td class="col_amount">
							@if(isset($adj_obj[$j]['key']) && $adj_obj[$j]['key'] != "")
								{{number_format( $p->{$adj_obj[$j]['key']},2) }}
							@endif
						</td>

						<td class="{{$deduction_obj[$j]['class'] ?? ''}}">{{$deduction_obj[$j]['label'] ?? ''}}</td>
						<td class="col_amount">
							@if(isset($deduction_obj[$j]['key']) && $deduction_obj[$j]['key']!="")
								{{number_format( $p->{$deduction_obj[$j]['key']},2) }}
							@endif
						</td>
						
						<td class="{{$summary_obj[$j]['class'] ?? ''}}">{{$summary_obj[$j]['label'] ?? ''}}</td>
						<td class="col_amount" style="border-right:none !important">
							@if(isset($summary_obj[$j]['key']) && $summary_obj[$j]['key'] != "")
								{{number_format( $p->{$summary_obj[$j]['key']},2) }}
							@endif
						</td>
					</tr>
				@endfor
				<tr style="border-top: 1px solid;">
					<td colspan="4"></td>
					<td style="padding-left: 0.3cm;font-weight: bold;">NET PAY:</td>
					<td class="col_amount" style="border-right:none !important">{{number_format($p->net_income,2)}}</td>
				</tr>
				<tr style="border-top: 1px solid;">
					
					<td style="padding-left: 0.3cm;border-right: 1px solid;" colspan="3">PREPARED BY:</td>
					<td style="padding-left: 0.3cm;border-right:none !important" colspan="3">RECEIVED BY:</td>
				</tr>
			</table>
		</div>
	</div>

	@endforeach



</body>
</html>




