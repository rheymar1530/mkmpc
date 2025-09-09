<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>

</head>
<style type="text/css">
	@page {      
		margin-left: 1.8cm;
		margin-right: 1.8cm;
		margin-top: 1.5cm;
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
		font-size: 13px !important;
	}
	.head_others{
		font-size: 15px !important;
	}
	.fill{
		font-weight: bold;
	}
	.p_body{
		line-height: 17px;
		font-size: 13px;
		/*text-indent: 70px;		*/
	}
	.p_body3,.p_body2{
		line-height: 17px;
		font-size: 15px;
	}
	.body_div{
		margin-top: -15px;
		white-space: normal;
		line-height: 18px;
		text-align: justify
	}
	.p_body:before,.p_body3:before { content: "\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0 "; }
    .columnLeft {         
        float: left;
        width: 50%;
        line-height: 10%; }
   
    .columnRight {         
        float: right;
        width: 50%;
        line-height: 10%;}
     
    .row:after {
        line-height: 10%;
        content: "";
        display: table;
        clear: both;
        height: 1px; }
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
     .page_break { page-break-before: always; }
     .font-sd{
     	font-size: 16px;
     }
</style>
<body>
	<div class="a">
		<h3 style="font-size:18px" class="head"><center>{{config('variables.coop_name')}}</center></h3>
		<p style="margin-top:-3px" ><center class="head_others">{{config('variables.coop_district')}}</center></p>
		<p style="margin-top:10px" ><center class="head_others">{{config('variables.coop_address')}}</center></p>
		<br>
		<h3 style="font-size:18px" class="head"><center>LOAN APPLICATION WAIVER</center></h3>
		<br>
	</div>
	<div class="a body_div">
		<p class="p_body">
			I <u class="fill">{{$loan_details->name}}</u> of <u class="fill">{{config('variables.school_name')}}</u>	                                                 
			applying the <u class="fill">{{$loan_details->loan_service}}</u>   Loan in the     {{config('variables.coop_abbr')}}  in   the amount of <u class="fill"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>{{$loan_details->principal_amount}}</u> with an interest rate of <u class="fill">{{$loan_details->interest_rate}}%</u> payable within <u class="fill">{{$loan_details->duration}}</u>.
		</p>
	</div>
	<div class="a body_div">
		<p class="p_body">
			I authorize the District Supervisor/Principal and Disbursing Officer of the School to deduct from my salary the amount due to me or remit my monthly dues to the {{config('variables.coop_abbr')}} Treasurer.
		</p>
	</div>

	<div class="a body_div">
		<p class="p_body">
			With my accounts to the cooperative, I hereby deposit my Automated Teller Machine (ATM) card to the representative and give full authority to withdraw the amount equivalent to my monthly amortization and other charges with the {{config('variables.coop_abbr')}}.
		</p>
	</div>
	<div class="a body_div">
		<p class="p_body">
			Incase my default in payment as herein agreed the entire balance of my loan shall become immediately due and payable at the option of the cooperative. Each party to this loan application and waiver whether a loaner/member or a co-maker severally waives presentation of payment, demand, and notice of protest and dishonor of the same.
		</p>
	</div>
	<div class="a body_div">
		<p class="p_body">
			It if further agreed by party hereto, that in case payment shall not be made at maturity by the maker/loaner, the co-makers shall pay the total loan amount due by the maker/loaner.
		</p>
	</div>
<!-- 	<div class="row" style="margin-top:40px !important;white-space: normal;">
		<div class="columnLeft" style="background:red"><p class="fill_bottom font">{{$loan_details->name}}</p></div>
		<div class="columnRight" style="background:blue;max-width: 300px"><p class="fill_bottom font" style="margin-left: 30px;max-width: 270px;white-space: nowrap;">&nbsp;dqweqw qweqweqweqweqwe qweqweqweqweqweqw</p></div>
	</div> -->
	<div class="row" style="margin-top:40px !important;white-space: normal;">
		<!-- <div class="columnLeft" style="background:red"><p class="fill_bottom font">{{$loan_details->name}}</p></div> -->
		<div class="columnLeft font fill_bottom" style="max-width: 300px;line-height: 10px;white-space: nowrap;overflow: hidden;text-align: left;text-align: center;padding-top: 5px">{{$loan_details->name}}</div>
		<div class="columnRight font fill_bottom" style="max-width: 300px;line-height: 10px;white-space: nowrap;overflow: hidden;text-align: left;padding-top: 5px">{{$loan_details->address}}</div>
	</div>
	<div class="row" style="margin-top: -5px;white-space: normal;">
		<div class="columnLeft"><p class="font fill_label">Name and Signature of Loaner</p></div>
		<div class="columnRight" ><p class="font fill_label" style="float: right !important;">Address</p></div>
	</div>

	<div class="row" style="margin-top:-3px !important;white-space: normal;">
		<div class="columnLeft"><p class="fill_bottom font">
		@if(isset($loan_details->mobile_no))
			{{$loan_details->mobile_no}}
		@else
			&nbsp;
		@endif
		</p></div>
		
	</div>
	<div class="row" style="margin-top: -15px;white-space: normal;">
		<div class="columnLeft"><p class="font fill_label">Loaners Mobile Number</p></div>
	</div>

	
	@foreach($comakers as $cm)
<!-- 	<div class="row" style="margin-top:30px !important;white-space: normal;">
		<div class="columnLeft"><p class="fill_bottom font"> {{$cm->comaker_name}} </p></div>
		<div class="columnRight"><p style="float: right !important;" class="fill_bottom font">&nbsp;</p></div>
	</div>
	<div class="row" style="margin-top: -15px;white-space: normal;">
		<div class="columnLeft"><p class="font fill_label">Name and Signature of Co-Maker</p></div>
		<div class="columnRight"><p class="font fill_label" style="float: right !important;">Address</p></div>
	</div> -->
	<div class="row" style="margin-top:40px !important;white-space: normal;">
		<!-- <div class="columnLeft" style="background:red"><p class="fill_bottom font">{{$loan_details->name}}</p></div> -->
		<div class="columnLeft font fill_bottom" style="max-width: 300px;line-height: 10px;white-space: nowrap;overflow: hidden;text-align: left;text-align: center;padding-top: 5px">{{$cm->comaker_name}}</div>
		<div class="columnRight font fill_bottom" style="max-width: 300px;line-height: 10px;white-space: nowrap;overflow: hidden;text-align: left;padding-top: 5px">{{$cm->address}}</div>
	</div>
	<div class="row" style="margin-top: -5px;white-space: normal;">
		<div class="columnLeft"><p class="font fill_label">Name and Signature of Co-Maker</p></div>
		<div class="columnRight" ><p class="font fill_label" style="float: right !important;">Address</p></div>
	</div>
	@endforeach

	<div class="row" style="margin-top: 10px;white-space: normal;">
		<p style="font-size:15px;font-weight:bold">CREDIT COMMITTEE ACTION:</p>
	</div>
	<div class="a" style="margin-top:15px">
		<p><center class="head_others font">EMELIZA P. BUNCAG</center></p>
		<p style="margin-top:-5px"><center class="head_others font">____________________________________</center></p>
		<p><center class="head_others font">Credit Committee Chairperson</center></p>
		<br>
	</div>
	<div class="row" style="margin-top:-5px;white-space: normal;">
		<p class="font">Approved:</p>
	</div>

	<div class="row" style="margin-top:25px !important;white-space: normal;">
		<div class="columnLeft"><p class="fill_bottom font">WILFREDO S. CAOYONAN</p></div>
		<div class="columnRight"><p style="float: right !important;" class="fill_bottom font">LIZA C. CALA-OR</p></div>
	</div>
	<div class="row" style="margin-top: -15px;white-space: normal;">
		<div class="columnLeft"><p class="font fill_label">BOD Chairperson</p></div>
		<div class="columnRight"><p class="font fill_label" style="float: right !important;">Acting General Manager</p></div>
	</div>

	<div class="page_break">
		<div class="a">
			<h3  class="head"><center style="font-size:20px !important">AUTHORIZATION FOR SALARY DEDUCTION</center></h3>
			<br>
		</div>
		<div class="row" style="margin-top: -20px;white-space: normal;">
			<p style="font-size:15px;font-weight:bold">TO THE {{config('variables.coop_abbr')}}</p>
		</div>
		<div class="a body_div">
			<p class="p_body2">
				Sir/Madam:
			</p>
		</div>
		<div class="a body_div">
			<p class="p_body3">
				I hereby authorized you to deduct from my check/ATM salary the amount of  <u class="fill"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>{{$loan_details->total_due}}</u> every month for <u class="fill">{{$loan_details->duration}}</u> until the total amount of <u class="fill"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>{{$loan_details->principal_amount}}</u> shall have been deducted.
			</p>
		</div>
		<div class="a body_div">
			<p class="p_body3">
				The amount deducted shall be withdrawn from my check/ATM in favor of which in turn will be credited to me as to my monthly amortization of the financial assistance granted to me.
			</p>
		</div>
		<div class="a body_div">
			<p class="p_body3">
				This authorization of salary deduction is irrevocable until obligations as indicated above has been paid fully.
			</p>
		</div>

		<div class="row" style="margin-top:40px !important;white-space: normal;">
			<div class="columnRight"><p style="float: right !important;" class="fill_bottom font-sd">{{$loan_details->name}}</p></div>
		</div>
		<div class="row" style="margin-top: -15px;white-space: normal;">
			<div class="columnRight"><p class="font-sd fill_label" style="float: right !important;">Name and Signature of the Borrower </p></div>
		</div>

		<div class="a" style="margin-top:25px">
			<h3  class="head"><center style="font-size:18px !important">SIGNED IN THE PRESENCE OF </center></h3>
			<br>
		</div>
		<div class="row" style="margin-top:15px !important;white-space: normal;">
			<div class="columnLeft"><p class="fill_bottom font">&nbsp;</p></div>
			<div class="columnRight"><p style="float: right !important;" class="fill_bottom font">&nbsp;</p></div>
		</div>
		<div class="row" style="margin-top: -15px;white-space: normal;">
			<div class="columnLeft"><p class="font-sd fill_label">Witness</p></div>
			<div class="columnRight"><p class="font-sd fill_label" style="float: right !important;">Witness</p></div>
		</div>
		<div class="a body_div" style="margin-top:30px !important">
			<p class="p_body3">
				SUBSCRIBE AND SWORN to before me this <u class="fill">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </u> day of <u class="fill">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u> &nbsp;Iloilo City, affiant exhibited to me his/her Community Tax Cert. No. <u class="fill">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>&nbsp; issued in <u class="fill">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>&nbsp; on <u class="fill">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>.
			</p>
		</div>
		<div class="a body_div" style="margin-top:20px">
			<p class="p_body2">Doc. No. :__________________</p>
			<p class="p_body2" style="margin-top:-10px">Page No.:__________________</p>
			<p class="p_body2" style="margin-top:-10px">Book No.:__________________</p>
			<p class="p_body2" style="margin-top:-10px">Series of 20 ________________</p>
		</div>
	</div>
</body>
</html>