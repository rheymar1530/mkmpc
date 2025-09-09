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

		<?php if($selected_type == 1): ?>
		<?php echo $__env->make('transaction_summary.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<?php elseif($selected_type == 2): ?>
		<?php echo $__env->make('transaction_summary.table_entry', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<?php else: ?>
		<?php echo $__env->make('transaction_summary.table_account', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<?php endif; ?>
</body>
</html>  	  	  	  	  	 
							

<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/transaction_summary/excel.blade.php ENDPATH**/ ?>