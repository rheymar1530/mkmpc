		<table  class="table tbl_loan repayment-pad" style="white-space: nowrap;margin-top: -3px;border: 1px solid black !important">
			<?php
				$colspan = 5;
				$colspan_total = 1;
				if(isset($show_repayment)){
					$colspan =9;
					$colspan_total = 2;
				}elseif($service_details->id_loan_payment_type == 2){
					$colspan = 6;
					$colspan_total = 2;
					// $colspan_total = 2;{{date('m/d/Y',strtotime($service_details->due_date))}}
				}
			?>
			<tr class="table_header_dblue">
				<th colspan="{{$colspan}}" class="center">LOAN SCHEDULE</th>
			</tr>
			<tr>
				<!-- <th class="border center">{{($service_details->id_loan_payment_type == 1)?"No":"Due Date"}}</th> -->
				<th class="border center">No</th>
				@if(isset($show_repayment) || $service_details->id_loan_payment_type == 2)
				<th class="border center">Due Date</th>
	

				@endif
				<th class="border center">Principal</th>
				<th class="border center">Interest</th>
				<th class="border center">Fees</th>
				<th class="border center">Total Amount Due</th>
				@if(isset($show_repayment))
				<th class="border center">Amount Paid</th>
				<th class="border center">Balance</th>
				<th class="border center">Remarks</th>
				@endif
			</tr>			
			@foreach($loan['LOAN_TABLE'] as $row)
			<tr>
				
				<td class="border center">{{$row['count']}}</td>
				@if(isset($show_repayment))
				<td class="border">{{$row['due_date']}}</td>
				@elseif($service_details->id_loan_payment_type == 2)
				<td class="border">{{date('m/d/Y',strtotime($service_details->due_date))}}</td>
				<!-- <td class="border">{{$service_details->due_date}}</td> -->
				@endif
				<td class="border col_amount">{{number_format($row['repayment_amount'],2)}}</td>
				<td class="border col_amount">{{number_format($row['interest_amount'],2)}}</td>
				<td class="border col_amount">{{number_format($row['fees'],2)}}</td>
				<td class="border col_amount">{{number_format($row['total_due'],2)}}</td>
				@if(isset($show_repayment))
				<td class="border col_amount">{{number_format($row['paid_amount'],2)}}</td>
					<?php
						if($row['remarks'] == "Paid"){
							$badge = "success";
						}elseif($row['remarks'] == "Renewed"){
							$badge = "info";
						}else{
							$badge = "primary";
						}

					?>
				<td class="border col_amount">{{number_format(($row['total_due']-$row['paid_amount']),2)}}</td>
				<td class="border">@if($row['remarks'] != "")<span class="badge badge-{{$badge}} badge_table">{{$row['remarks']}}</span>@endif</td>

				@endif
			</tr>
			@endforeach
			<tr>
				<th class="border center xl-text" colspan="{{$colspan_total}}">TOTAL</th>
				<th class="border col_amount xl-text">{{number_format($loan['PRINCIPAL_AMOUNT'],2)}}</th>
				<th class="border col_amount xl-text">{{number_format($loan['TOTAL_INTEREST'],2)}}</th>
				<th class="border col_amount xl-text">{{number_format($loan['TOTAL_NOT_DEDUCTED_CHARGES'],2)}}</th>
				<th class="border col_amount xl-text">{{number_format($loan['TOTAL_AMOUNT_DUE'],2)}}</th>
				@if(isset($show_repayment))
				<th class="border col_amount xl-text">{{number_format($loan['TOTAL_PAID_AMOUNT'],2)}}</th>
				<th class="border col_amount xl-text">{{number_format($loan['CURRENT_LOAN_BALANCE'],2)}}</th>
				<th class="border center xl-text"></th>
				@endif
			</tr>
			@if(isset($show_repayment))
				@if($service_details->paid_principal > 0 || $service_details->paid_interest > 0 || $service_details->paid_fees > 0)
				<?php
					$pp = ($service_details->paid_principal > 0)?number_format(($service_details->paid_principal*-1),2):'';
					$pi = ($service_details->paid_interest > 0)?number_format(($service_details->paid_interest*-1),2):'';
					$pf = ($service_details->paid_fees > 0)?number_format(($service_details->paid_fees*-1),2):'';
				?>
				<tr>
					<th class="border center sm-text" colspan="{{$colspan_total}}"></th>
					<th class="border col_amount2 sm-text">{{$pp}}</th>
					<th class="border col_amount2 sm-text">{{$pi}}</th>
					<th class="border col_amount2 sm-text">{{$pf}}</th>
					<th colspan="4"></th>
				</tr>
				<tr>
					<th class="border center" colspan="{{$colspan_total}}">Balance</th>
					<th class="border col_amount2 sm-text2">{{number_format($loan['PRINCIPAL_AMOUNT']-$service_details->paid_principal,2)}}</th>
					<th class="border col_amount2 sm-text2">{{number_format($loan['TOTAL_INTEREST']-$service_details->paid_interest,2)}}</th>
					<th class="border col_amount2 sm-text2">{{number_format($loan['TOTAL_NOT_DEDUCTED_CHARGES']-$service_details->paid_fees,2)}}</th>
					<th colspan="4" class="border"></th>
				</tr>
				@endif
			@endif			
		</table>