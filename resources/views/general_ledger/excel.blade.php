<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{$head_title}}</title>
	<style type="text/css">
		.head{
			font-weight: bold !important;
		}
	</style>
</head>
<body>

	<table>

		<thead>
			@if($filter_type == 1)
			<tr>
				<th style="font-weight:bold;text-align: center;">Account</th>
				<th style="font-weight:bold;text-align: center;">Debit</th>
				<th style="font-weight:bold;text-align: center;">Credit</th>
			</tr>
			@else
			<tr>
				<th style="font-weight:bold;text-align: center;"></th>
				<th style="font-weight:bold;text-align: center;">Date</th>
				<th style="font-weight:bold;text-align: center;">Description</th>
				<th style="font-weight:bold;text-align: center;">Post Reference</th>
				<th style="font-weight:bold;text-align: center;">Debit</th>
				<th style="font-weight:bold;text-align: center;">Credit</th>
				<th style="font-weight:bold;text-align: center;">Remarks</th>
			</tr>
			@endif

		</thead>
			<tbody>
				@if($filter_type == 1)
					@include('general_ledger.table_summary')
				@else
					@include('general_ledger.table')
				@endif	
			</tbody>
	</table>
</body>
</html>  	  	  	  	  	 
							

