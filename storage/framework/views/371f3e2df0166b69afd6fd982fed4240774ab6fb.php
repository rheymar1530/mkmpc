<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo e($head_title); ?> - Export</title>
	<style>

		@page  {      
			margin-left: 1cm;
			margin-right: 1cm;
			margin-top:2cm;
			size : letter landscape;
		}
		div.header{
			text-align: center;
			font-size: 15px;
			font-family: Calibri, sans-serif;
		}
		* {
			box-sizing: border-box;
		}
		.columnLeft {         
			float: left;
			width: 50%;
		}
		.columnRight {         
			float: right;
			width: 50%;
		}
		#tbl_overdues  tr>td,#tbl_overdues  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Calibri, sans-serif;
			font-size: 11pt ;
		}
		.row_g_total{
			font-size: 18px !important;
		}
		.bold_lbl{
			font-weight: bold;
		}
		.details_tbl_lbl{
			text-align: right !important;
			width: 40% !important;
		}	
		.pd_left_10{
			/*padding-left: 10px !important;*/
		}
		.row:after {
			line-height: 10%;
			content: "";
			display: table;
			clear: both;
			height: 1px; 
		}

		.class_amount{
			text-align: right;
			margin-right: 5px !important;
		}

		 th {
			border: 1px solid;
		}
		thead tr{
			border-top: 1px solid;
			border-bottom: 1px solid;
		}

		tr.nb-top th{
			border-top: unset !important;
		}

		tr.nb-botom th{
			border-botom: unset !important;
		}
		table {
			width: 100%;
			border-collapse: collapse;

		}
		.highlight_amount{
			font-weight: bold;
			text-decoration: underline;
			font-size: 12px !important;
		}
		.bold-text{
			font-weight: bold;
		}
		.add_border_bottom{
			border-bottom: 1px solid;
		}
		#tbl_overdues tr{
			page-break-inside: avoid !important;
		}
		.center {
			margin-left: auto;
			margin-right: auto;
		}
		.width80{
			width: 80% !important;
		}
		.border-w-border{
			border : 1px solid !important;
		}

		tr.btop td{
			border-top: 1px solid;
		}

		tr.bbottom td{
			border-bottom: 1px solid;
		}

		.font-xs{
			font-size: 8pt !important;
		} 

		tr.bb td{
			border-top: 1px solid!important;
/*			font-color `-*/
		}
	</style>
</head>
<body>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			<p style="font-size: 20px;margin-top: -15px"><b><?php echo e(config('variables.coop_abbr')); ?> </b></p>
			<p style="font-size: 15px;margin-top: -15px"><b><?php echo e($head_title); ?></b></p>
			<p style="font-size: 15px;margin-top: -15px"><b><?php echo e($date); ?></b></p>
			<p></p>
			<p></p>
		</div> 
	</header>
	<div class="row">
		<?php echo $__env->make('loan-overdue.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	</div>



</body>
</html>

<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan-overdue/pdf.blade.php ENDPATH**/ ?>