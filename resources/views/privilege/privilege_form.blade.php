@extends('adminLTE.admin_template')
@section('content')
@push('head')
	<style type="text/css">
		#tbl_prev  tr>td,#tbl_prev  tr>th{
			padding:4px !important;
			vertical-align:top;
			font-family: Arial !important;
			font-size: 14px !important;
		}
	</style>
@endpush
<div class="container-fluid">
	<div class='row'>
	 <div style="width:900px;margin:0 auto " class="col-sm-10">
	 	<div class="card card-primary">
            <div class="card-header">
                <strong>Privilege & Credentials</strong> 
            </div>
            <form id="submit_target">
            <div class="card-body clearfix">
            	
                    <div class='form-group'>
                        <label>Privilege Name</label>
                        <input type='text' class='form-control' name='name' required id="prev_name" value="{{$details->name ?? ''}}"/>
                    </div>
                     <div id='privileges_configuration' class='form-group'>
                     	<label>Privilege Roles</label>
                     	 <table class='table table-striped table-hover table-bordered' id="tbl_prev">
                     	 	<thead>
                     	 		<tr class="active">
                     	 			<th width='3%'>No</th>
                                	<th width='60%'>Modules Name</th>
                                	<th></th>
                                	<th>View</th>
                                	<th>Create</th>
                                	<th>Read</th>
                                	<th>Update</th>
                                	<th>Delete</th>
                                	<th>Confirm</th>
                                    <th>Confirm2</th>
                                    <th>Cancel</th>
                                	<th>Print</th>
                     	 		</tr>
	                     	 	<tr class='table-info'>
	                                <th>&nbsp;</th>
	                                <th>&nbsp;</th>
	                                <th>&nbsp;</th>
	                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_visible'/></td>
	                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_create'/></td>
	                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_read'/></td>
	                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_edit'/></td>
	                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_delete'/></td>
	                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_confirm'/></td>
                                    <td align="center"><input title='Check all vertical' type='checkbox' id='is_confirm2'/></td>
                                    <td align="center"><input title='Check all vertical' type='checkbox' id='is_cancel'/></td>
	                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_print'/></td>
	                            </tr>
                     	 	</thead>
                     	 	<tbody>
                     	 		<?php $i=1;?>
                     	 		@foreach($menus as $menu)
	                     	 		<?php
	                                    (isset($menu->is_view) && $menu->is_view)?$check_is_view='checked':$check_is_view='';
	                                    (isset($menu->is_create) && $menu->is_create)?$check_is_create='checked':$check_is_create='';
	                                    (isset($menu->is_read) && $menu->is_read)?$check_is_read='checked':$check_is_read='';
	                                    (isset($menu->is_edit) && $menu->is_edit)?$check_is_update='checked':$check_is_update='';
	                                    (isset($menu->is_delete) && $menu->is_delete)?$check_is_delete='checked':$check_is_delete='';
	                                    (isset($menu->is_print) && $menu->is_print)?$check_is_print='checked':$check_is_print='';
	                                    (isset($menu->is_confirm) && $menu->is_confirm)?$check_is_confirm='checked':$check_is_confirm='';
                                        (isset($menu->is_confirm2) && $menu->is_confirm2)?$check_is_confirm2='checked':$check_is_confirm2='';
                                        (isset($menu->is_cancel) && $menu->is_cancel)?$check_is_cancel='checked':$check_is_cancel='';
	                                ?>
                     	 			<tr class="row_prev">
	                     	 			<td class="col_id" style="display: none">{{$menu->id}}</td>
	                                    <td>{{$i}}</td>
	                                    <td>{{$menu->name}}</td>
	                                    <td class='info' align="center"><input type='checkbox' title='Check All Horizontal'  class='select_horizontal'/>
	                                    </td>

	                                    <td class='info' align="center"><input type='checkbox'   class='is_view' {{$check_is_view}}/>
	                                    </td>

	                                    <td class='info' align="center"><input type='checkbox'   class='is_create' {{$check_is_create}}/>
	                                    </td>

	                                    <td class='info' align="center"><input type='checkbox'   class='is_read' {{$check_is_read}}/>
	                                    </td>

	                                    <td class='info' align="center"><input type='checkbox'   class='is_update' {{$check_is_update}}/>
	                                    </td>

	                                    <td class='info' align="center"><input type='checkbox'   class='is_delete' {{$check_is_delete}}/>
	                                    </td>

	                               		<td class='info' align="center"><input type='checkbox'   class='is_confirm' {{$check_is_confirm}}/>
                                        <td class='info' align="center"><input type='checkbox'   class='is_confirm2' {{$check_is_confirm2}}/>
                                    	</td>
                                        <td class='info' align="center"><input type='checkbox'   class='is_cancel' {{$check_is_cancel}}/>
                                        </td>
	                                    <td class='info' align="center"><input type='checkbox'   class='is_print' {{$check_is_print}}/>
	                                    </td>
                                	</tr>
                                	<?php $i++;?>
                     	 			
                     	 		@endforeach
                     	 	</tbody>
                     	 </table>
                     </div>
            	
            </div>
            <div class="card-footer">
            	<div class="float-right"><button type="submit" class="btn btn-primary">Submit</button></div>
            </div>
            </form>
          </div>
	 </div>
	</div>
</div>
@endsection
@push('scripts')
	<script type="text/javascript">
		$(function () {
        
        $("#is_visible").click(function () {
            var is_ch = $(this).prop('checked');
            console.log('is checked create ' + is_ch);
            $(".is_view").prop("checked", is_ch);
            console.log('Create all');
        })
        $("#is_create").click(function () {
            var is_ch = $(this).prop('checked');
            console.log('is checked create ' + is_ch);
            $(".is_create").prop("checked", is_ch);
            console.log('Create all');
        })
        $("#is_read").click(function () {
            var is_ch = $(this).is(':checked');
            $(".is_read").prop("checked", is_ch);
        })
        $("#is_edit").click(function () {
            var is_ch = $(this).is(':checked');
            $(".is_update").prop("checked", is_ch);
        })
        $("#is_delete").click(function () {
            var is_ch = $(this).is(':checked');
            $(".is_delete").prop("checked", is_ch);
        })
         $("#is_print").click(function () {
            var is_ch = $(this).is(':checked');
            $(".is_print").prop("checked", is_ch);
         })

        $("#is_confirm").click(function () {
            var is_ch = $(this).is(':checked');
            $(".is_confirm").prop("checked", is_ch);
         })
        $("#is_confirm2").click(function () {
            var is_ch = $(this).is(':checked');
            $(".is_confirm2").prop("checked", is_ch);
         })
        $("#is_cancel").click(function () {
            var is_ch = $(this).is(':checked');
            $(".is_cancel").prop("checked", is_ch);
         })
        $(".select_horizontal").click(function () {
            var p = $(this).parents('tr');
            var is_ch = $(this).is(':checked');
            p.find("input[type=checkbox]").prop("checked", is_ch);
        })

        $('#submit_target').submit(function(event){
            event.preventDefault();

           Swal.fire({
	              title: 'Are you sure you want to save this ?',
	              icon: 'warning',
	              showDenyButton: false,
	              showCancelButton: true,
	              confirmButtonColor: '#ff3333',
	              confirmButtonText: `Yes`,
	            }).then((result) => {
	                if (result.isConfirmed) {
	                	post();
	                } 
	            })
        })
        function post(){
        	var is_view =[],is_create=[],is_read=[],is_update=[],is_delete=[],is_print=[],id_menu=[],is_confirm = [],is_cancel=[],is_confirm2=[];

            $("table#tbl_prev tr.row_prev").each(function(){
                id_menu.push($(this).find('td.col_id').text());
                is_view.push(GetCheck($(this).find('input.is_view'))); 
                is_create.push(GetCheck($(this).find('input.is_create'))); 
                is_read.push(GetCheck($(this).find('input.is_read'))); 
                is_update.push(GetCheck($(this).find('input.is_update'))); 
                is_delete.push(GetCheck($(this).find('input.is_delete')));   
                is_print.push(GetCheck($(this).find('input.is_print'))); 
                is_confirm.push(GetCheck($(this).find('input.is_confirm')));
                is_confirm2.push(GetCheck($(this).find('input.is_confirm2')));
                is_cancel.push(GetCheck($(this).find('input.is_cancel')));
               
            })
            console.log('---------------------------------------');
            console.log({id_menu,is_view,is_create,is_read,is_update,is_delete,is_print,is_confirm,is_cancel});

            console.log('---------------------------------------');

            $.ajax({	
                type               :    'POST',
                url                :    '{{URL::to('/admin/privilege/post')}}',
                data               :    {'id_menu':id_menu,
                                         'is_view':is_view,
                                         'is_create':is_create,
                                         'is_read':is_read,
                                         'is_update':is_update,
                                         'is_delete':is_delete,
                                         'is_print':is_print,
                                         'is_confirm':is_confirm,
                                         'is_confirm2':is_confirm2,
                                         'is_cancel':is_cancel,
                                         'prev_name':$('#prev_name').val(),
                                         'opcode' : '<?php echo $opcode;?>',
                                         'id_prev':'<?php echo $details->id ?? 0?>'},
                success             :  function(response){
                    console.log({response});
                    if(response.message == 'success'){
                        window.location ='/admin/privilege/index';
                    }
                }
            })
        }

        function GetCheck(obj){
          if(obj.prop('checked') == true){
                    return 1;
          }else{
                    return 0;
          }
        }
    })
	</script>
@endpush