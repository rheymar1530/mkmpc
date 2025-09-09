<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>CBU Monthly</title>
	<style type="text/css">
		.head{
			font-weight: bold !important;
		}
	</style>
</head>
<body>
	<?php

		$field_not_amt = ['Name'];

		function check_zero($val){
			if($val == 0){
				return "-";
			}else{
				return $val;
			}
		}


		$grand_total = 0;
	?>
	<table>

		<thead>
		<tr>

			@foreach($cbus[0] as $key=>$val)
			<th style="font-weight:bold;text-align: center;">{{$key}}</th>
			<?php
				if(!in_array($key,$field_not_amt)){
					$total[$key] = 0;
				}
				
			?>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($cbus as $c=>$row)
		<tr>
			
			@foreach($row as $key=>$val)

			@if(!in_array($key,$field_not_amt))
			<td class="class_amount {{ ($val==0)?'text-centered':''}}">{{check_zero($val)}}</td>
			<?php 
				if($key=="Total"){
					$grand_total += $val;
				}
				$total[$key]+= $val;
			?>
			@else
			<td>{{$val}}</td>
			@endif
			@endforeach
		</tr>
		@endforeach
		<tr>
			<td style="font-weight: bold;font-size: 12px;" colspan="1">GRAND TOTAL</td>
			@foreach($total as $tot)
			<td class="class_amount {{ ($tot==0)?'text-centered':''}}" style="font-weight: bold;font-size: 12px">{{check_zero($tot)}}</td>
			@endforeach
		</tr>
	</tbody>
	</table>
</body>
</html>  	  	  	  	  	 
							

