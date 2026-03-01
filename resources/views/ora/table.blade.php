@if($exportMode <= 2)
<?php
	function number_formats($val){
		return number_format($val,2);
	}

	$styleError = "";
?>
@else
<?php
	function number_formats($val){
		return $val;
	}
	$styleError = 'style="background-color:#ffb3b3"';
?>
@endif


<table class="table table-bordered table-head-fixed" id="tbl-or" width="100%">
	<thead>
		<tr>
			<th>Date</th>
			<th>OR Number</th>
			<th>Payee</th>
			<th>Reference</th>
			<th>Amount</th>
			<th>Status</th>
		</tr>
	</thead>

	<tbody>
		@foreach($or_output as $o)
		<tr class="series-row">
			<td colspan="6"><b>Series: {{$o['series']['start']}} - {{$o['series']['end']}}</b></td>
		</tr>
			@foreach($o['content'] as $contents)
				<?php
					$multiple = count($contents) > 1 ? true : false;
				?>
				@foreach($contents as $c =>$content)
				<?php
					$styleErrorF = $content->missing == 1?$styleError:'';
				?>
				<tr class="{{$content->missing == 1?'missing':''}}">
					<td <?php echo $styleErrorF; ?>>
						@if($content->within_date == 1)
							<b>{{$content->date}}</b>
						@else
							{{$content->date}}
						@endif


					</td>
					<td <?php echo $styleErrorF; ?>>{{$content->or_number}} @if($multiple)<sup>[{{$c+1}}]</sup>@endif</td>
					<td <?php echo $styleErrorF; ?> class="{{$content->missing == 1?'bold':''}}">
						@if($exportMode  == 1)
							{!! $content->description !!}
						@else
							{!! strip_tags($content->description) !!}
						@endif

					</td>
					<td <?php echo $styleErrorF; ?>>
						@if($exportMode  == 1)
							{!! $content->reference !!}
						@else
							{!! strip_tags($content->reference) !!}
						@endif

					</td>
					<td <?php echo $styleErrorF; ?> class="text-right">{{number_formats($content->total_amount)}}</td>
					<td <?php echo $styleErrorF; ?>>{{$content->status}}</td>
				</tr>
				@endforeach

			@endforeach
		@endforeach
	</tbody>
	<tr class="btop">


	</tr>
</table>