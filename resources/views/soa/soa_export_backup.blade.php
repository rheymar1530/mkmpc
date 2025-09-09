<!DOCTYPE html>
<html lang="en">   
	<head>
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	        <style>

		        @page {      
		            margin-left: 5%;
		            margin-right: 5%;
		            margin-top:6%;
		        }
			    div.header{
			    	text-align: center;
			    	line-height: 1px;
			    	font-size: 10px;
			    	 font-family: Arial, Helvetica, sans-serif;
			    }
	/*		    	        	@page :first{
	        		margin-top: 100px !important;
	        		margin-bottom: 300px;
	        	}*/
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
				.tbl_acct_details  tr>td,.tbl_acct_details  tr>th,
				.tbl_transactions  tr>td,.tbl_transactions  tr>th{
					padding:0px;
					vertical-align:top;
					font-family: Arial, Helvetica, sans-serif;
					font-size: 10px !important;
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

				.col_amount{
					text-align: right;
				}
				.group_padding{
		 			 padding-left: 20px !important;
				}
				.tbl_transactions{
					page-break-inside: always !important;
					/*page-break-after: always;*/
				}
				.page-break {
				    page-break-after: always;
				}
				div.absolute {
					position: absolute;
					bottom: 10px;
					width: 50%;
					/*border: 3px solid #8AC007;*/
				}
				.text_notes{
					font-family: Arial, Helvetica, sans-serif;
					font-size: 10px;
				}
				.border-bottom{
					border-bottom: 1px solid;
				}
				.pad_right{
					padding-right: 5px;
				}
	</style>
	</head>
	<!-- <body style="margin-bottom: 1% !important;margin-top: -0.5%;margin-left: -10px;margin-right: : -10px"> -->
	<body>
		<header>
	        <div class="header" style="margin-top: -36px">
	        	<p><b>LIBCAP SUPER EXPRESS CORPORATION <b></p>
	        	<p>{{ $details->branch_address }}</p>
	        	<p>Tel. No. {{ $details->contact }}</p>
	        	<p>TIN # 123-456-789</p> 
	        </div> 
	        <div class="header">
	        	<p><b>STATEMENT OF ACCOUNT</b></p>
	        </div> 
   		</header>

        <div class="row">
			<div class="columnLeft">
				<table style=" border-collapse: collapse;white-space: nowrap;" class="tbl_acct_details">
					<tbody>
						<tr>
							<td class="bold_lbl">{{ $details->name }}</td>
						</tr>
						<tr>
							<td>{{ $details->address }}</td>
						</tr>
						<tr>
							<td>TIN - {{ $details->tin }}</td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="columnRight">
				<table style="float: right; border-collapse: collapse;" class="tbl_acct_details">>
				    <tbody>
						<tr>
							<td class="details_tbl_lbl">Control No.:</td>
							<td class="bold_lbl">&nbsp;&nbsp;&nbsp;&nbsp; {{ $details->control_number }}</td>
						</tr>
						<tr>
							<td class="details_tbl_lbl">Account No.:</td>
							<td class="">&nbsp;&nbsp;&nbsp;&nbsp;{{ $details->account_no }}</td>
						</tr>
						<tr>
							<td class="details_tbl_lbl">Billing Period:</td>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $details->billing_period }}</td>
						</tr>
						<tr>
							<td class="details_tbl_lbl">Statement Date:</td>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $details->statement_date }} </td>
						</tr>
						<tr>
							<td class="details_tbl_lbl">Amount Due:</td>
							<td id="td_amount_due">&nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($amount_due,2) }}</td>
						</tr>
						<tr>
							<td class="details_tbl_lbl">Payment Due Date</td>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $details->due_date }}</td>
						</tr>
				    </tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black" class="tbl_transactions">
				<thead>
					<tr style="text-align: left !important;padding-bottom: 10px;">
				        <th scope="col" style="width: 12%">Date</th>
				        <th scope="col" style="width: 10%">Tracking No.</th>
				        <th scope="col">Destination</th>
				        <th scope="col" style="width: 13%;">Description</th>
				        <th scope="col">Date & Time Received</th>
				        <th scope="col">Received By</th>
				        <th scope="col" width="10%">Amount</th>
				    </tr>
				    <tr style="text-align: left !important;padding-bottom: 10px;">
				        @for($i=0;$i<6;$i++)
				        	<th>&nbsp;</th>
				        @endfor
				    </tr>
				</thead>
				<tbody>
					<?php $current_total = 0 ;$i=0;?>
				    @foreach($transactions as $cc=>$group)
						@if(count($transactions) >1 || (count($transactions) ==1 && $cc != ""))
							<?php $group_padding = "group_padding"; $nbsp = "&nbsp;&nbsp;&nbsp;"?>
							<tr>
								<th colspan="7" style="text-align: left;">{{ $cc }}</th>
							</tr>
						@endif
							<?php
     							$sub_total = 0;
    						?>
					    @foreach($group as $item)
						      <tr class="">
						      	<td class="{{$group_padding ?? ''}}"><?php echo $nbsp ?? '' ?>{{$item->transaction_date}}

						      	</td>
						      	<td>{{$item->hawb_no}}</td>
						      	<td>{{$item->destination}}</td>
						      	<td>{{$item->description}}</td>
						      	<td>{{$item->date_time_received}}</td>
						      	<td>{{$item->received_by}}</td>
						      	<td class="col_amount">{{number_format($item->total,2)}}</td>
						      </tr>
					      	<?php $current_total += $item->total; $sub_total += $item->total;$i++?>
					    @endforeach
						@if(count($transactions) > 1)
				            <tr style="">
								<th style="text-align: left;border-bottom: 1px solid black !important">Subtotal</th>
								<th colspan="6" style="text-align: right;border-bottom: 1px solid black !important">{{number_format($sub_total,2)}}</th>
				            </tr>
				        @endif
				    @endforeach

				</tbody>
				<tfoot>
					<tr>
		        		<th style="text-align: left;">Total</th>
		        		<th colspan="6" style="text-align: right">{{number_format($current_total,2)}}</th>
		        	</tr>
				</tfoot>
			</table>
		</div>
		<?php
			$taxable_amt = $current_total/1.12;
			$vat_amount = $current_total - $taxable_amt;
			$prev_amount_due = $previous_amt_due;
		?>
		<div class="row" style="margin-top: 10px;page-break-inside: avoid">
				<table style=" border-collapse: collapse;white-space: nowrap;border: 1px solid black;" class="tbl_acct_details" width="100%">
					<tbody>
						<tr>
							<th colspan="3" style="text-align:left;">&nbsp;Charge for this month</th>
						</tr>
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;Current Charges</td>
							<td></td>
							<td class="col_amount" style="padding-right:5px">{{ number_format($current_total,2) }}</td>
						</tr>
						<tr>
							<td>&nbsp;&nbsp;&nbsp;Taxable Amount</td>
							<td></td>
							<td class="col_amount" style="padding-right:5px">{{ number_format($taxable_amt,2) }}</td>
						</tr>
						<tr>
							<td>&nbsp;&nbsp;&nbsp;VAT Amount</td>
							<td></td>
							<td class="col_amount" style="padding-right:5px">{{ number_format($vat_amount,2)  }}</td>
						</tr>

					</tbody>
				</table>
				<table style=" border-collapse: collapse;white-space: nowrap;margin-top: 10px;border: 1px solid black;" class="tbl_acct_details" width="100%;">
					<tbody>
						<tr>
							<td colspan="3">&nbsp;BALANCE FORWARDED</td>
						</tr>
						<tr>
							<td>&nbsp;&nbsp;&nbsp;Previous Amount Due</td>
							<td></td>
							<td class="col_amount" style="padding-right:5px">{{number_format($prev_amount_due,2)}}</td>
						</tr>
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;Less Payment</td>
							<td>
								<?php $less_payment = 0;?>
								@if(count($payments) > 0)

									
									<table class="tbl_acct_details" style=" border-collapse: collapse;white-space: nowrap;width: 90%">
										<tr>
											<td>Date</td>
											<td>OR No.</td>
											<td >Amount</td>
										</tr>
										@foreach($payments as $pay)
											<tr>
												<td>{{$pay->transaction_date}}</td>
												<td>{{$pay->or_number }}</td>
												<td class="col_amount">{{number_format($pay->amount,2) }}</td>
											</tr>
											<?php $less_payment += $pay->amount;?>
										@endforeach

									</table>
								@endif
							</td>
							<!-- if(no less payment) -->
							<td class="col_amount">{{(count($payments) == 0)?'0.00':''}}</td>
						</tr>
						<!-- if payment -->
						@if(count($payments) > 0)
							<tr>
								<td class="col_amount" colspan="3" style="padding-right:5px">-{{number_format($less_payment,2)}}</td>
							</tr>
						@endif
						<tr>
							<td>&nbsp;&nbsp;&nbsp;Adjustment(s)</td>
							<td></td>
							<td class="col_amount" style="padding-right:5px">{{ number_format($adjustments,2) }}</td>
						</tr>
						<?php
							$sub_total_f = 0;
							$sub_total_f = $prev_amount_due-$less_payment+$adjustments;
							$total_amount_due = $current_total+$sub_total_f;
						?>
						<tr>
							<td>&nbsp;&nbsp;&nbsp;Subtotal</td>
							<td></td>
							<td class="col_amount" style="padding-right:5px">{{ number_format($sub_total_f,2) }}</td>
						</tr>
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;TOTAL AMOUNT DUE</td>
							<td class="col_amount bold_lbl" style="padding-right:5px" >{{ number_format($total_amount_due,2) }}</td>
						</tr>
<!-- 						<tr>
							<td colspan="3">&nbsp;</td>
						</tr> -->
					</tbody>
					
				</table>

		</div>

		<div class="row" style="page-break-inside: avoid;height: 200px;margin-bottom: 80px">
			<table class="tbl_acct_details" style="border-collapse: collapse" width="100%">
				<tr>
					<td width="100%" colspan="2">Please make your check payable and crossed to <b>LIBCAP SUPER EXPRESS CORPORATION</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td width="100%" colspan="2">Your usual prompt payment will be highly appreciated. However, a 2% interest per month will be charged to your account if Total Amount Due is not paid within fifteen (15) days of Payment Due Date. Please Notify LIBCAP SUPER EXPRESS CORPORATION within three (3) days from receipt thereof, of any correction or adjustment in writing otherwise, you agree that this Statement of Account is true and correct as to all matters contained herein, including of its attachments. Thank you for giving us the opportunity to be of service to you.</td>
				</tr>
			</table>
			<table class="tbl_acct_details" style="border-collapse: collapse;float: right;margin-top: 10px;" width="30%">
				<tr>
					<td>Received statement and supporting original copies of Tracking Summary and PODs</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td>_______________________________________</td>
				</tr>
				<tr>
					<td style="text-align:center;">Please print name and sign</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td>Date:___________________________________</td>
				</tr>
			</table>
		</div>
		<div class="absolute row" style="margin-bottom: -40px;width: 100%;">
			<div class="row">
				<div class="header row">
					<p>CUSTOMER'S COPY</p>
				</div>
				<div class="row header line_head" style="border-top: 1px dashed;position: relative;margin-right: 15px">
						<?php
							$path = public_path().'\storage\uploads\LIBCAP_LOGO.jpg';
						?>
					<img src="{{'data:image/png;base64,' . base64_encode(file_get_contents(public_path().'\storage\uploads\sc.png'))}}" alt="image" width="10px" height="10px" style="float: right;margin-top: -6px;margin-right: -10px">
					<p>LIBCAP'S COPY</p>
				</div>
				<div class="row">
					<p style="font-size: 10px;margin-top: -10px;">Note: Please enclosed this portion with your payment. This serves as LIBCAP's copy of your instruction</p>
				</div>
				<div class="row">
					<div class="columnLeft">
						<table style=" border-collapse: collapse;white-space: nowrap;" class="tbl_acct_details">
							<tbody>
								<tr>
									<td class="bold_lbl">{{ $details->name }}</td>
								</tr>
								<tr>
									<td>{{ $details->address }}</td>
								</tr>
								<tr>
									<td>TIN - {{ $details->tin }}</td>
								</tr>
								<tr>
									<td></td>
								</tr>
								<tr>
									<td></td>
								</tr>
								<tr>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="columnRight">
						<table style="float: right; border-collapse: collapse;" class="tbl_acct_details">>
						    <tbody>
								<tr>
									<td class="details_tbl_lbl">Control No.:</td>
									<td class="bold_lbl">&nbsp;&nbsp;&nbsp;&nbsp; {{ $details->control_number }}</td>
								</tr>
								<tr>
									<td class="details_tbl_lbl">Account No.:</td>
									<td class="">&nbsp;&nbsp;&nbsp;&nbsp;{{ $details->account_no }}</td>
								</tr>
								<tr>
									<td class="details_tbl_lbl">Billing Period:</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $details->billing_period }}</td>
								</tr>
								<tr>
									<td class="details_tbl_lbl">Statement Date:</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $details->statement_date }} </td>
								</tr>
								<tr>
									<td class="details_tbl_lbl">Amount Due:</td>
									<td id="td_amount_due">&nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($amount_due,2) }}</td>
								</tr>
								<tr>
									<td class="details_tbl_lbl">Payment Due Date</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $details->due_date }}</td>
								</tr>
						    </tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

	</body>
</html>