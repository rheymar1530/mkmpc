<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style type="text/css">
		@media print {
			@page { margin: 0; }
			body { margin: 1.6cm; }
		}
		.class_amount{
			text-align: right;
		}
		table{
			border-collapse: collapse;
		}
	</style>
	<title></title>
</head>
<body onload="window.print();">
	<table width="100%">
		<tbody>
			<tr>
				<td>{{$cash_receipt->member_name}}</td>
				<td>{{$cash_receipt->transaction_date}}</td>
			</tr>
		</tbody>
	</table>
	<table style="margin-top:0.5cm"  width="100%">
		<tbody>
			@foreach($cash_receipt_details as $crd)
			<tr>
				<td>{{$crd->payment_description}}</td>
				<td class="class_amount">{{number_format($crd->amount,2)}}</td>
			</tr>
			@endforeach
		</tbody>
		<tfoot>
			<tr>
				<td  style="border-top: 1px solid !important;"></td>
				<td  style="border-top: 1px solid !important;font-weight: bold" class="class_amount">{{number_format($cash_receipt->total_payment,2)}}</td>
			</tr>
		</tfoot>
	</table>
</body>
</html>