<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Voucher Summary</title>
	<style type="text/css">
		.head{
			font-weight: bold !important;
		}
	</style>
</head>
<body>

		@if($selected_type == 1)
		@include('transaction_summary.table')
		@elseif($selected_type == 2)
		@include('transaction_summary.table_entry')
		@else
		@include('transaction_summary.table_account')
		@endif
</body>
</html>  	  	  	  	  	 
							

