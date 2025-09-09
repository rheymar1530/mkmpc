<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>CBU Report</title>
	<style type="text/css">
		.head{
			font-weight: bold !important;
		}
	</style>
</head>
<body>
	<?php 
		$total = 0;
	?>
	<table>

		<thead>
			<tr>
				<th style="font-weight:bold;text-align: center;">ID Member</th>
				<th style="font-weight:bold;text-align: center;">Member</th>
				<th style="font-weight:bold;text-align: center;">Amount</th>
			</tr>
		</thead>
		<tbody>
			@foreach($cbu as $c)
			<tr>
				<td>{{$c->id_member}}</td>
				<td>{{$c->member}}</td>
				<td class="class_amount">{{$c->amount}}</td>
			</tr>
			<?php
				$total += $c->amount;
			?>
			@endforeach
		</tbody>
		<tfoot>
			<tr>
				<td style="font-weight: bold;font-size: 12px;" colspan="2">GRAND TOTAL</td>
				<td style="font-weight: bold;font-size: 12px;" colspan="1">{{$total}}</td>
			</tr>
		</tfoot>
	</table>
</body>
</html>  	  	  	  	  	 
							

