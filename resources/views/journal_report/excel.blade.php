<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{$description}}</title>
	<style type="text/css">
		.head{
			font-weight: bold !important;
		}
	</style>
</head>
<body>

		@if($selected_type == 1)
		@include('journal_report.table')
		@else
		@include('journal_report.table_entry')
		@endif
</body>
</html>  	  	  	  	  	 
							

