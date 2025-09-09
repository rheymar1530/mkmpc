	<div class="a" style="text-align:center;">
		<h3 class="head"><span>{{config('variables.coop_name')}}</span></h3>
		<p style="margin-top:-10px" ><span class="head_others"><b>({{config('variables.coop_abbr')}})</b></span></p>
		<p style="margin-top:-10px" ><span class="head_others">{{config('variables.coop_address')}}</span></p>
		<p style="margin-top:-10px;text-align: right !important;" ><span class="head_others">CONTROL NO. _________</span></p>

		<h3 style="font-size:13pt;margin-top: 0.7cm;" class="head"><span>LOAN APPLICATION FORM</span></h3>
	</div>
	<?php
	if(!function_exists('check_val')){
		function check_val($val){
			if($val == null || $val == ''){
				$val = '&nbsp;';
			}
			return $val;
		}	
	}

	$max = 12;
	$maker = array(
		['title'=> 'Name of Applicant:','value'=>check_val($loan_details->name),'key-co'=>'comaker_name'],
		['title'=> 'Type of Loan Applied:','value'=>check_val($loan_details->loan_service),'key-co'=>''],
		['title'=> 'Purpose of Loan','value'=>check_val($loan_details->purpose),'key-co'=>''],
		['title'=> 'Residential Address:','value'=>check_val($loan_details->address),'key-co'=>'address'],
		['title'=> 'Contact Number/Mobile No.','value'=>check_val($loan_details->mobile_no),'key-co'=>'mobile_no'],
		['title'=> 'Office Name/Department','value'=>'&nbsp;','key-co'=>''],
		['title'=> 'Position','value'=>'&nbsp;','key-co'=>''],
		['title'=> 'Civil Status','value'=>check_val($loan_details->civil_status),'key-co'=>'civil_status'],
		['title'=> 'Date of Birth','value'=>check_val($loan_details->birthday),'key-co'=>'birthday'],
		['title'=> 'Name of Spouse','value'=>check_val($loan_details->spouse),'key-co'=>'spouse'],
		['title'=> 'Occupation of Spouse','value'=>check_val($loan_details->spouse_occupation),'key-co'=>'spouse_occupation'],
		['title'=> 'Monthly Source if Income','value'=>check_val(''),'key-co'=>'']
	);
	?>
	<!-- style="border-spacing: 5mm 0mm 5mm 5mm;border-collapse: separate" -->
	<div class="row">
		<table id="tbl-maker-co" class="app_table" width="100%">
			<colgroup ></colgroup>
			<colgroup width="35%"></colgroup>
			<colgroup ></colgroup>
			<colgroup width="35%"></colgroup>
			<tr class="">
				<th width="30%" class="">&nbsp;</th>
				<th class="pad"><div class="div-loan-app">MAKER</div></th>
				<th class="nb" style="width:1mm !important">&nbsp;</th>
				<th class=""><div class="div-loan-app">CO-MAKER</div></th>
			</tr>
			@for($i=0;$i<=$max-1;$i++)
			<tr class="">
				<td class="nb"><div class="div-loan-app nb">{{$maker[$i]['title']}}</div></td>
				<td class="nb pad"><div class="div-loan-app maker-co"><?php echo $maker[$i]['value'];?></div></td>
				<td class="nb text-center maker-co">{{$i+1}}</td>
				<td>
					<div class="div-loan-app maker-co">
						@if(isset($comakers))
							@if(isset($comakers->{$maker[$i]['key-co']} ))
								{{ $comakers->{$maker[$i]['key-co']} }}
							@else
							&nbsp;
							@endif

						@else
						&nbsp;
						@endif
					</div>
				</td>
			</tr>
			@endfor
			<tr class="">					
				<td class="nb"><div class="div-loan-app nb"><b>MONTHLY NET INCOME</b></div></td>
				<td class="nb pad"><div class="div-loan-app">P&nbsp;</div></td>
				<td class="nb text-center">{{$i+2}}</td>
				<td>
					<div class="div-loan-app">&nbsp;</div>						
				</td>
			</tr>
		</table>
	</div>
	<div class="a body_div" style="margin-top:-0.5mm">
		<p class="body_no_indent">
			As a member of {{config('variables.coop_name')}} <b>({{config('variables.coop_abbr')}})</b>, I hereby apply for a loan in the amount of PESOS: <u class="fill">{{$worded_principal}}</u>(<u class="fill">P{{number_format($loan_details->principal_num,2)}}</u>) at interest rate of <u class="fill">{{$worded_interest}}</u> percent <u class="fill">({{$loan_details->interest_rate}}%)</u> for a period of <u class="fill">{{$loan_details->terms ?? 1}}</u> months
		</p>
		<p class="p_body">
			I hereby certify that the above information is true and correct to the best 0f my knowledge, that i read and fully understood the terms and conditions of the loan applied for. I certify further that I have no pending administrative and/or criminal case.
		</p>
	</div>
	<div class="row" style="margin-top:8mm">
		<table class="tbl_sig" width="100%">
			<tr class="text-center">
				<td width="50%" style="padding: 0 2mm 0 2mm"><div style="border-bottom:1px solid">{{$loan_details->name}}</div></td>
				<td width="50%" style="padding: 0 2mm 0 2mm"><div style="border-bottom:1px solid">{{$comakers->comaker_name ?? ''}}</div></td>
			</tr>
			<tr class="text-center">
				<td width="50%">Applicant's Signature Over Printed Name</td>
				<td width="50%">Co-Maker's Signature Over Printed Name</td>
			</tr>
		</table>
	</div>
	<div style="margin-top:2mm;border-top:dashed 1px #000;width: 100%;">&nbsp;</div>
	<div style="margin-top:-4.5mm;border-top:dashed 1px #000;width: 100%;">
		<div class="a body_div" style="margin-top:-3mm !important">
			<p class="body_no_indent">
				<b>(This portion to be filled-up by The Coop personnel)</b>
			</p>
			<div style="padding-left: 2cm;">
				<table class="app_table2"  style="margin-top: 1mm;">
					<colgroup></colgroup>
					<tr>
						<td style="width:5cm"><span style="font-family: 'DejaVu Sans',sans-serif !important">&#9654;</span> Total Capital Build-Up (CBU):</td>
						<td style="width:2mm">P</td>
						<td class="nb pad" style="width:4cm"><div class="div-loan-app">&nbsp;{{number_format($cbu,2)}}</div></td>
						<td style="width:20mm !important"><small>as of (Date)</small></td>
						<td class="nb" style="width:4cm"><div class="div-loan-app">&nbsp;{{$cbu_as_of}}</div></td>
					</tr>
					<tr class="pdtop">
						<td><span style="font-family: 'DejaVu Sans',sans-serif !important">&#9654;</span> Savings/Time Deposit:</td>
						<td>P</td>
						<td class="nb pad"><div class="div-loan-app">&nbsp;</div></td>
						<td><small>as of (Date)</small></td>
						<td class="nb"><div class="div-loan-app">&nbsp;</div></td>
					</tr>
					<tr class="pdtop">
						<td></td>
						<td></td>
						<td class="nb pad"><div class="div-loan-app">&nbsp;</div></td>
						<td><small>as of (Date)</small></td>
						<td class="nb"><div class="div-loan-app">&nbsp;</div></td>
					</tr>
				</table>
			</div>
		</div>	
	</div>
	<div class="row">
		<table width="100%" class="app_table2" style="border-spacing: 10px 0px 10px 0px;border-collapse: separate;margin-top:0.3cm">
			<tr class="ftcustom bbtom">
				<th>Existing Loan/s</th>
				<th style="width: 3cm;">Amount Granted</th>
				<th style="width: 3cm;">Date Granted</th>
				<th style="width: 2cm;">Date Due</th>
				<th style="width:2cm">&nbsp;</th>
				<th style="width:2cm">Status</th>
			</tr>
			<tbody>
				@foreach($active_loan as $al)
				<tr class="bbtom">
					<td>{{$al->loan}}</td>
					<td class="text-right">{{number_format($al->principal_amount,2)}}</td>
					<td>{{$al->date_released}}</td>
					<td>{{$al->maturity_date}}</td>
					<td class="text-right">{{number_format($al->balance,2)}}</td>
					<td></td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="a body_div" style="margin-top:-1mm">
		<p class="p_body">I hereby certify that the above records of the applicant with the cooperative are true and correct:</p>
	</div>
	<div class="row">
		<div class="columnLeft">
			<p class="body_no_indent"><b><i>Certified Correct</i></b></p>
		</div>
		<div class="columnRight">
			<p class="body_no_indent" style="text-align:right;">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Approved&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Disapproved</p>
		</div>

	</div>
	<div class="row" style="margin-top:-3mm">
		<div class="columnLeft" style="width:45% !important;margin-bottom:1px solid">
			<p class="body_no_indent" style="padding-bottom: 1mm">by <span style="border-bottom:1px solid">{{config('variables.loan_disbursement_prepared')}}</span></p>
		</div>
		<div class="columnRight" style="width:55% !important">
			<p class="body_no_indent" style="text-align:left;padding-bottom: 1mm"><b>Approved by Coop Manager:</b>&nbsp;<span style="border-bottom:1px solid">{{config('variables.v_checked_by')}}</span></p>
		</div>

	</div>
	<div class="text-center" style="margin-top:-5mm">
		<h4><u>ACTION TAKEN</u></h4>
	</div>
	<div class="row" style="margin-top:-5mm">
		<table class="tbl_sig" width="100%">
			<tr>
				<td width="45%"><h3>CREDIT COMMITTEE:</h3></td>
				<td width="10%"></td>
				<td width="45%"><h3>BOARD OF DIRECTOR:</h3></td>
			</tr>
			<tr>
				<td>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Approved&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Disapproved</td>
				<td></td>
				<td>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Approved&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Disapproved</td>
			</tr>
			<tr>
				<td style="font-size:5pt !important">&nbsp;</td>
				<td style="font-size:5pt !important">&nbsp;</td>
				<td style="font-size:5pt !important">&nbsp;</td>
			</tr>
			<tr class="bbtom text-center">
				<td>JOSEFA L. JAMELO</td>
				<td class="nb"></td>
				<td></td>
			</tr>
			<tr class="text-center">
				<td>Chairperson</td>
				<td></td>
				<td>Chairperson</td>
			</tr>

			<!-- SECOND  -->
			<tr>
				<td style="font-size:5pt !important">&nbsp;</td>
				<td style="font-size:5pt !important">&nbsp;</td>
				<td style="font-size:5pt !important">&nbsp;</td>
			</tr>
			<tr>
				<td>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Approved&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Disapproved</td>
				<td></td>
				<td>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Approved&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Disapproved</td>
			</tr>
			<tr>
				<td style="font-size:5pt !important">&nbsp;</td>
				<td style="font-size:5pt !important">&nbsp;</td>
				<td style="font-size:5pt !important">&nbsp;</td>
			</tr>
			<tr class="bbtom text-center">
				<td>MA. JONAH T. JACOBO</td>
				<td class="nb"></td>
				<td></td>
			</tr>
			<tr class="text-center">
				<td>Vice Chairperson</td>
				<td></td>
				<td>Vice Chairperson</td>
			</tr>

			<!-- THIRD  -->
			<tr>
				<td style="font-size:5pt !important">&nbsp;</td>
				<td style="font-size:5pt !important">&nbsp;</td>
				<td style="font-size:5pt !important">&nbsp;</td>
			</tr>
			<tr>
				<td>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Approved&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;Disapproved</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td style="font-size:5pt !important">&nbsp;</td>
				<td style="font-size:5pt !important">&nbsp;</td>
				<td style="font-size:5pt !important">&nbsp;</td>
			</tr>
			<tr class="bbtom text-center">
				<td>NOEMIE MARIE MAGULLADO</td>
				<td class="nb"></td>
				<td class="nb"></td>
			</tr>
			<tr class="text-center">
				<td>Secretary</td>
				<td></td>
				<td></td>
			</tr>
		</table>

	</div>