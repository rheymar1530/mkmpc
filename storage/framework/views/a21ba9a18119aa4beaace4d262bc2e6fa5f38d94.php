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
	<?php if(count($financial_statement['data']) > 0): ?>
	<?php echo $__env->make('financial_statement.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php else: ?>
	<h4 style="text-align:center;">No Record Found</h4>
	<?php endif; ?>
</body>
</html>  	  	  	  	  	 
							

<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/financial_statement/excel_fs.blade.php ENDPATH**/ ?>