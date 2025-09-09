
<?php

	$field_not_amt = ['Name'];

	function check_zero($val){
		if($val == 0){
			return "-";
		}else{
			return number_format($val,2);
		}
	}


	$grand_total = 0;
?>
<style type="text/css">
	.text-centered{
		text-align: center;
	}
</style>
<table class="table table-bordered table-head-fixed table-hover tbl_accounts tbl_gl" style="margin-top:15px">
	<thead>
		<tr>
			<th width=""></th>
			@foreach($primes[0] as $key=>$val)
			<th class="text-centered">{{$key}}</th>
			<?php
				if(!in_array($key,$field_not_amt)){
					$total[$key] = 0;
				}
				
			?>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($primes as $c=>$row)
		<tr>
			<td class="text-centered">{{$c+1}}</td>
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
			<td style="font-weight: bold;font-size: 15px;" colspan="2">GRAND TOTAL</td>
			@for($i=0;$i<$end_month;$i++)
			
			@endfor
			@foreach($total as $tot)
			<td class="class_amount {{ ($tot==0)?'text-centered':''}}" style="font-weight: bold;font-size: 15px">{{check_zero($tot)}}</td>
			@endforeach
		</tr>
	</tbody>




</table>






