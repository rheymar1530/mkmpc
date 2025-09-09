<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<style type="text/css">
		table, td,th{
			border : 1px solid;
			font-size: 11pt;
		}
	</style>
</head>
<body>
	<?php

	?>
	<table style="border-collapse: collapse;">
		<tr>
			<th>Series</th>
			<th>OR</th>
			<th style="width : 9cm">Payor</th>
			<th>Payment</th>
			<th>Remarks</th>
		</tr>
		<tbody>
			@foreach($chunked as $ch)
				@foreach($ch as $count=>$c)
				<tr>
					@if($count == 0)
					<td rowspan="{{count($ch)}}">{{$ch[0]->or_no}} - {{$ch[49]->or_no}}</td>
					@endif

					<td>{{$c->or_no}}</td>
					<td>{{$c->payor}}</td>
					<td class="" style="text-align:right;">{{number_format($c->payment,2)}}</td>
					<td>{{($c->exist == 0)?'Missing':''}}</td>
				</tr>
				@endforeach

			@endforeach
		</tbody>
	</table>
</body>
</html>