<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo e($date); ?></title>
	<style type="text/css">
		.head{
			font-weight: bold !important;
		}
	</style>
</head>
<body>

	<?php echo $__env->make('loan-deliquent.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>  	  	  	  	  	 
							

<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan-deliquent/excel.blade.php ENDPATH**/ ?>