<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Payroll Summary</title>
	<style type="text/css">
		.head{
			font-weight: bold !important;
		}
	</style>
</head>
<body>
			<!-- "id_employee","name","branch_name","department","emp_status","position","basic_pay","cola","ot","total_allowance","thir_month","incentives","paid_leaves","holiday","others","salary_adjustment","gross_income","sss","hdmf","philhealth","wt","insurance","sss_loan","hdmf_loan","ca","absences","late","total_deduction","net_income" -->
	<table>

		<thead>
			<tr>
				<th style="font-weight:bold;text-align: center;background:#808080;">EMP NO</th>
				<th style="font-weight:bold;text-align: center;background:#808080;">EMPLOYEE NAME</th>
				<th style="font-weight:bold;text-align: center;background:#808080;">BANK</th>
				<th style="font-weight:bold;text-align: center;background:#808080;">ACCOUNT NO</th>
				<th style="font-weight:bold;text-align: center;background:#808080;">BRANCH</th>
				<th style="font-weight:bold;text-align: center;background:#808080;">DEPT</th>
				<th style="font-weight:bold;text-align: center;background:#808080;">STATUS</th>
				<th style="font-weight:bold;text-align: center;background:#808080;">POSITION</th>
				<th style="font-weight:bold;text-align: center;background: #002b80;color:white">BASIC</th>
				<!-- <th style="font-weight:bold;text-align: center;background: #002b80;color:white">COLA</th> -->
				<th style="font-weight:bold;text-align: center;background: #002b80;color:white">OT</th>
				<!-- <th style="font-weight:bold;text-align: center;background: #002b80;color:white">Night Shift Differential</th> -->
				<th style="font-weight:bold;text-align: center;background: #002b80;color:white">ALLOWANCES</th>
				<th style="font-weight:bold;text-align: center;background: #002b80;color:white">13TH MONTH</th>
				<th style="font-weight:bold;text-align: center;background: #002b80;color:white">INCENTIVE</th>
				<th style="font-weight:bold;text-align: center;background: #002b80;color:white">PAID LEAVES</th>
				<!-- <th style="font-weight:bold;text-align: center;background: #002b80;color:white">HOLIDAY PAY</th> -->
				<th style="font-weight:bold;text-align: center;background: #002b80;color:white">OTHERS</th>
				<th style="font-weight:bold;text-align: center;background: #002b80;color:white">ADJUSTMENT</th>
				<th style="font-weight:bold;text-align: center;background: #002b80;color:white">GROSS PAY </th>
				<th style="font-weight:bold;text-align: center;background: #b32d00;color: white;">ABSENCES</th>
				<th style="font-weight:bold;text-align: center;background: #b32d00;color: white;">LATE</th>
				<th style="font-weight:bold;text-align: center;background: #b32d00;color: white;">SSS</th>
				<th style="font-weight:bold;text-align: center;background: #b32d00;color: white;">PAG-IBIG</th>
				<th style="font-weight:bold;text-align: center;background: #b32d00;color: white;">PHILHEALTH</th>
				<th style="font-weight:bold;text-align: center;background: #b32d00;color: white;">W/HOLDING TAX</th>
				<th style="font-weight:bold;text-align: center;background: #b32d00;color: white;">INSURANCE</th>
				<th style="font-weight:bold;text-align: center;background: #b32d00;color: white;">SSS LOAN</th>
				<th style="font-weight:bold;text-align: center;background: #b32d00;color: white;">PAG-IBIG LOAN</th>
				<th style="font-weight:bold;text-align: center;background: #b32d00;color: white;">CASH ADVANCES</th>

				<th style="font-weight:bold;text-align: center;background: #b32d00;color: white;">TOTAL DEDUCTIONS</th>
				<th style="font-weight:bold;text-align: center;background: #004d00;color: white;">NET PAY</th>
				<th style="font-weight:bold;text-align: center;background:#002b80;color:white;">REMARKS</th>
			</tr>
		</thead>
		<tbody>
			<?php
			// "cola","night_shift_dif,,"holiday"
				$keys = ["basic_pay","ot","total_allowance","thir_month","incentives","paid_leaves","others","salary_adjustment","gross_income","absences","late","sss","hdmf","philhealth","wt","insurance","sss_loan","hdmf_loan","ca","total_deduction","net_income"];

				$totals = array();
				foreach($keys as $k){
					$totals[$k] = 0;
				}
			?>
			@foreach($payroll as $p)
			<tr>
				<td style="text-align: center;">{{$p->id_employee}}</td>
				<td style="white-space:nowrap">{{$p->name}}</td>
				<td></td>
				<td></td>
				<td>{{$p->branch_name}}</td>
				<td>{{$p->department}}</td>
				<td>{{$p->emp_status}}</td>
				<td>{{$p->position}}</td>
				@foreach($keys as $key)
					<td>{{ $p->{$key} }}</td>
					<?php
						$totals[$key] += $p->{$key};
					?>
				@endforeach
				<td>{{$p->remarks}}</td>
			</tr>
			@endforeach

		</tbody>
		<tr>
			<th colspan="8"  style="background-color:black;color:white;font-weight: bold;">Grand Total</th>
			@foreach($keys as $k)
				<th  style="background-color:black;color:white;font-weight: bold;">{{$totals[$k]}}</th>
			@endforeach
			<td style="background-color:black;color:white;font-weight: bold;"></td>
		</tr>
	</table>
</body>
</html>  	  	  	  	  	 
							

