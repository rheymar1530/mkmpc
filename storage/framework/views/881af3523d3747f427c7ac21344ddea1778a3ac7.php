<?php $__env->startSection('content'); ?>
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
#tbl_loan_service  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_loan_service  tr::nth-child(0)>th{
    padding:34px !important;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}  
.col_search{
    padding: 1px 1px 1px 1px !important;
}

.dataTables_scrollHead table.dataTable th, table.dataTable tbody td {
    padding: 9px 10px 1px;
}

.head_search{
    height: 24px;
}


.form-label{
	margin-bottom: 4px !important;
}
.tbl_badge{
	font-size: 13px;
}

</style>
<div class="container-fluid main_form" style="margin-top:-20px">
	<h3>Loan Service&nbsp;&nbsp; 
		<?php if($credential->is_create): ?>
		<a class="btn bg-gradient-info" onclick="redirect_add()"><i class="fa fa-plus"></i>&nbsp;Create Loan Service</a>
		<?php endif; ?>
	</h3>
	<div class="row">
       <div class="col-md-12">
            <table id="tbl_loan_service" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID</th>
                        <th>Name</th>
                        <th>Disbursement Type</th>
                        <th>Amount Range</th>
                        <th>Default Amount</th>
                        <th>Interest Method</th>
                        <th>Payment Method</th>
                        <th>Period</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="list_body">
                	<?php $__currentLoopData = $loan_service_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                		<tr class="loan_service_row" data-code="<?php echo e($list->id_loan_service); ?>">
                			<td><?php echo e($list->id_loan_service); ?></td>
                			<td><?php echo e($list->name); ?></td>
                			<td><?php echo e($list->disbursement_type); ?></td>
                			<td><?php echo e($list->amount_range); ?></td>
                			<td><?php echo e($list->default_amount); ?></td>
                			<td><?php echo e($list->interest_method); ?></td>
                			<td><?php echo e($list->loan_payment_method); ?></td>
                			<td><?php echo e($list->period); ?></td>
                			<td>
                				<span class="badge badge-<?php echo e(($list->status == 'Inactive'?'danger':'success')); ?> tbl_badge"><?php echo e($list->status); ?></span>
                			</td>
                		</tr>
                	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
var dt_table;
var typingTimer;                
var doneTypingInterval = 400;
var id_chart_account_holder;
var opcode = 0; //Add
$(document).ready(function(){

		//Initialize Datatable
	$('#tbl_loan_service thead tr').clone(true).appendTo( '#tbl_loan_service thead' );
	$('#tbl_loan_service thead tr:eq(1)').addClass("head_rem")
	$('#tbl_loan_service thead tr:eq(1) th').each( function (i) {
		var title = $(this).text();
		$(this).addClass('col_search');
		$(this).html( '<input type="text" placeholder="Search '+title+'" style="width:100%;" class="txt_head_search head_search"/> ' );
		$( 'input', this ).on( 'keyup change', function (){
			var val = this.value;
			clearTimeout(typingTimer);
			typingTimer = setTimeout(function(){
				if(dt_table.column(i).search() !== val ) {
					dt_table
					.column(i)
					.search( val )
					.draw();
				}
			}, doneTypingInterval);
		});
	});
	dt_table    = init_table();
	$('#tbl_loan_service .head_rem').remove();
	$('.dt-buttons').removeClass('btn-group')
	$('.dt-buttons').find('button').removeClass('btn-secondary');

	$('#chart_modal').on('hidden.bs.modal', function (e) {
		if(opcode == 1){ //if edit
			$('#div_chart_form').html(''); // Clear the chart cards
		}
	})
})

function init_table(){
	var config = {
		order: [],
		"lengthChange": true, 
		"autoWidth": false,
		scrollCollapse: true,
		scrollY: '70vh',
		scrollX : true,
		orderCellsTop: true,
		"bPaginate": false,
		dom: 'Bfrtip',
		buttons: [
			

		]
	}
	var table = $("#tbl_loan_service").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.loan_service_row',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var id_loan_service = $(this).attr('data-code');
			if(key == "view"){
				window.location = '/loan_service/view/'+id_loan_service +'?href='+'<?php echo e(urlencode(url()->full())); ?>';
			}
			console.log({id_chart_account})
        },
        items: {
        	"view": {name: "View Loan Service", icon: "fas fa-eye"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});
function redirect_add(){
	window.location = '/loan_service/create'+'?href='+'<?php echo e(urlencode(url()->full())); ?>';
}


</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan_service/index.blade.php ENDPATH**/ ?>