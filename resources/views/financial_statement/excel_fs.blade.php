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
	@if(count($financial_statement['data']) > 0)
	@include('financial_statement.table')
	@else
	<h4 style="text-align:center;">No Record Found</h4>
	@endif
</body>
</html>  	  	  	  	  	 
							

