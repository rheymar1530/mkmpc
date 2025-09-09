<?php $__env->startSection('content'); ?>
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
#tbl_payroll  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_payroll  tr::nth-child(0)>th{
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
.class_amount{
	text-align: right;
	padding-right: 10px !important;
}
.dt-buttons{
	display: none;
}
</style>
<div class="container-fluid main_form" style="margin-top:-20px">
	<h3>Payroll&nbsp;&nbsp;	</h3>
	<div class="row">
       <div class="col-md-12">
            <table id="tbl_payroll" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID</th>
                    	<th>Date Released</th>
                        <th>Payroll Mode</th>
                        <th>Period Start</th>
                        <th>Period End</th>
                        <th>No of days</th>
                        <th>Salary Mode</th>
                        <th>Status</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody id="list_body">
                	<?php $__currentLoopData = $payroll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                		<tr class="payroll_row" data-code="<?php echo e($list->id_payroll); ?>" >
                			<td><?php echo e($list->id_payroll); ?></td>
                			<td><?php echo e($list->date_released); ?></td>
                			<td><?php echo e($list->description); ?></td>
                			<td><?php echo e($list->period_start); ?></td>
                			<td><?php echo e($list->period_end); ?></td>
                			<td><?php echo e($list->no_days); ?></td>
                			<td><?php echo e($list->salary_mode); ?></td>
                			<td><?php if($list->status == 10): ?>
                				<span class="badge badge-danger tbl_badge">Cancelled</span>
                			    <?php endif; ?></td>
                			<td><?php echo e($list->date_created); ?></td>
                		</tr>
                	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
	</div>
</div>
<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
	$('#tbl_payroll thead tr').clone(true).appendTo( '#tbl_payroll thead' );
	$('#tbl_payroll thead tr:eq(1)').addClass("head_rem")
	$('#tbl_payroll thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_payroll .head_rem').remove();
	$('.dt-buttons').removeClass('btn-group')
	$('.dt-buttons').find('button').removeClass('btn-secondary');
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
		buttons: ['excel']
	}
	
	var table = $("#tbl_payroll").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.payroll_row',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var id_payroll = $(this).attr('data-code');
			console.log({id_payroll})

			if(key == "view"){
				window.location = '/payroll/view/'+id_payroll +'?href='+'<?php echo e(urlencode(url()->full())); ?>';
			}else{
				$.ajax({
					type            :            'GET',
					url             :            '/payroll/check_status',
					data            :            {'id_payroll' : id_payroll},
					success         :            function(response){
												 console.log({response});
												 if(response.RESPONSE_CODE == "ALLOWED"){
													if(key == "excel"){
														window.location = '/payroll/excel_summary/'+id_payroll;
													}else if(key == "print"){
														print_page('/payroll/print_summary/'+id_payroll)
													}else if(key == "print_payslip"){
														print_page('/payroll/print_payroll_payslip/'+id_payroll)
													}
												 }else{
													Swal.fire({
														title: "Cancelled Payroll",
														text: '',
														icon: 'warning',
														showCancelButton : false,
														showConfirmButton : false,
														timer : 2500
													});	

												 }

											
					}
				})
			}

	

        },
        items: {
        	"view": {name: "View Payroll", icon: "fas fa-eye"},
        	"print": {name: "Print Payroll Summary", icon: "fas fa-print"},
        	"excel": {name: "Download Payroll Summary (Excel)", icon: "fas fa-download"},
        	"print_payslip": {name: "Print Payslip", icon: "fas fa-print"},

            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});
function redirect_add(){
	window.location = '/payroll/create'+'?href='+'<?php echo e(urlencode(url()->full())); ?>';
}


</script>
<?php if($credential->is_create): ?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_payroll_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Create Payroll</a>')
	})
</script>
<?php endif; ?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_payroll_filter').append('<a class="btn bg-gradient-primary" onclick="show_filter()" style="float:left;margin-left:10px"><i class="fa fa-eye"></i>&nbsp;Filtering Option</a>')
	})
	function show_filter(){
		$('#view_options').modal('show')
	}
	function show_repayment_summary(){
		$('#summary_date_modal').modal('show');
	}
</script>


<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/payroll/index.blade.php ENDPATH**/ ?>