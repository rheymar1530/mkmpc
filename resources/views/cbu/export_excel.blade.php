<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>CBU Account</title>
	<style type="text/css">
		.head{
			font-weight: bold !important;
		}
	</style>
</head>
<body>
	<?php 
		$total = 0;$sum_credit=0;$sum_debit=0;
		function amount($amt){
			return ($amt == 0)?'-':$amt;
		}
	?>
	<table>

		<thead>
			<tr>
				<th style="font-weight:bold;text-align: center;">Transaction Date</th>
				<th style="font-weight:bold;text-align: center;">Description</th>
				<th style="font-weight:bold;text-align: center;">Reference</th>
				<th style="font-weight:bold;text-align: center;">Debit</th>
				<th style="font-weight:bold;text-align: center;">Credit</th>
				<th style="font-weight:bold;text-align: center;">Ending Balance</th>
			</tr>
		</thead>
		<tbody>
			@foreach($cbu_ledger as $cbu)
			<tr>
				<td>{{$cbu->transaction_date}}</td>
				<td>{{$cbu->description}}</td>
				<td>{{$cbu->reference}}</td>

				<!-- <td class="class_amount">{{number_format($cbu->amount,2)}}</td> -->
				<td class="class_amount">{{amount($cbu->debit)}}</td>
				<td class="class_amount">{{amount($cbu->credit)}}</td>
				
				<?php 
					$sum_credit+= $cbu->credit;
					$sum_debit+= $cbu->debit;
					$total+= $cbu->amount;
				?>
				<td class="class_amount">{{$total}}</td>
			</tr>
			@endforeach
		

		</tbody>

	</table>
</body>
</html>  	  	  	  	  	 
							

