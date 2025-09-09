<?php $__env->startSection('content'); ?>
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
#tbl_member_list  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_member_list  tr::nth-child(0)>th{
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

	<h3>Member List&nbsp;&nbsp;
	<?php if($credential->is_create): ?>
		<a class="btn bg-gradient-info" onclick="redirect_add()"><i class="fa fa-plus"></i>&nbsp;Add Member</a>
	<?php endif; ?>
	</h3>
	<div class="row">
       <div class="col-md-12">
            <!-- <button class="btn btn-sm bg-gradient-primary" onclick="open_pickup_modal()">Create Pickup Request</button> -->
            <table id="tbl_member_list" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID</th>
                        <th>Member Code</th>
                        <th>Name</th>
                        <th>Branch</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Brgy/LGU</th>
                        <th>Status</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody id="list_body">
                	<?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                		<tr class="member_row" data-code="<?php echo e($list->member_code); ?>">
                			<td><?php echo e($list->id_member); ?></td>
                			<td><?php echo e($list->member_code); ?></td>
                			<td><?php echo e($list->full_name); ?></td>
                			<td><?php echo e($list->branch_name); ?></td>
                			<td><?php echo e($list->email); ?></td>
                			<td><?php echo e($list->member_type); ?></td>
                			<td><?php echo e($list->brgy_lgu); ?></td>
                			<td>
                				<?php if($list->status == 0): ?>
                				<span class="badge badge-danger tbl_badge">Inactive</span>
                				<?php endif; ?>

                			</td>
                			<td><?php echo e($list->date_created); ?></td>
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
	$('#tbl_member_list thead tr').clone(true).appendTo( '#tbl_member_list thead' );
	$('#tbl_member_list thead tr:eq(1)').addClass("head_rem")
	$('#tbl_member_list thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_member_list .head_rem').remove();
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
	var table = $("#tbl_member_list").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.member_row',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var member_code= $(this).attr('data-code');
			if(key == "view"){
				window.location = '/member/view/'+member_code+'?href='+'<?php echo e(urlencode(url()->full())); ?>';
			}
			console.log({id_chart_account})
        },
        items: {
        	"view": {name: "View Member Details", icon: "fas fa-eye"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});

function redirect_add(){
	window.location = '/member/create'+'?href='+'<?php echo e(urlencode(url()->full())); ?>';
}


</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/member/index.blade.php ENDPATH**/ ?>