@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">

	#tbl_list_maintenance  tr>td{
		padding:3px ;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}
	#tbl_list_maintenance  tr::nth-child(0)>th{
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


	.modal-conf {
		max-width:60% !important;
		min-width:60% !important;
		/*margin: auto;*/
	}
	.form-label{
		margin-bottom: 4px !important;
	}
	#tbl_list_maintenance_filter{
		/*width: 50%;*/
	}

</style>
<div class="container-fluid section_body" style="margin-top:-15px">
	<h3 class="lbl_color">{{$mod_title}} </h3>
	<!-- <a class="btn bg-gradient-info" onclick="show_create_modal()"><i class="fa fa-plus"></i>&nbsp;Add {{$mod_title}}</a> -->
	
	<div class="row mt-3">
		<div class="col-md-6">
			<!-- <button class="btn btn-sm bg-gradient-primary" onclick="open_pickup_modal()">Create Pickup Request</button> -->
			<table id="tbl_list_maintenance" class="table table-hover table-striped" style="margin-top: 10px;" >
				<thead>
					<tr class="table_header_dblue">
						<th width="1%"></th>
						<th>{{$primary_key_label}}</th>
						@foreach($table_fields as $f)
						<th>{{$f['label']}}</th>
						@endforeach
					</tr>
				</thead>
				<tbody id="list_body">
					@foreach($datasets as $list)
					<tr class="list_row" data-id="{{ $list->{$primary_key} }}">
						<td><input type="checkbox" name="" class="chk_list" value="{{ $list->{$primary_key} }}"></td>
						@foreach($list as $col)
						<td>{{$col}}</td>
						@endforeach
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header bg-gradient-primary2">
					<span id="txt_head font-weight-bold">Add {{$mod_title}}</span>&nbsp;&nbsp;&nbsp; 
					@if($credential->is_create)
					<span id="spn_form_button"><button class="btn btn-sm bg-gradient-success2" onclick="append_form()"><i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;Add</button>
					</span>
					@endif
					
				</div>
				<form id="submit_maintenance">
					<div class="card-body">
						<div style="max-height: calc(100vh - 177px);overflow-y: auto;overflow-x: auto;margin-top: 15px;padding-right: 10px;min-height: 500px;" id="form_body">
							<div class="card card-form c-border" id="bar">
								<div class="card-body" >
									<div class="row p-0" style="margin-top:-20px">
										<label class="col-sm-12 control-label col-form-label" style="text-align: right;font-size: 15px;"><a onclick="remove_card(this)"><i class="fa fa-times"></i></a></label>
									</div>
									<div class="row p-0" style="margin-top: 10px;">
										<div class="col-sm-12 p-1">
											@foreach($table_fields as $f)
											<?php
												$required = (($f['required'] ?? false)) ? 'required':'';
											?>
											<div class="form-group row p-0" >
												<label for="{{$f['field']}}" class="col-sm-3 control-label col-form-label" style="text-align: left">{{$f['label']}}&nbsp;</label>
												<div class="col-sm-9">
													@if($f['input'] == 'input')
													<input type="text" name="" class="form-control form_input {{$f['field']}}"  value="" key="{{$f['field']}}" <?php echo $required; ?>>
													@elseif($f['input'] == 'select')
													<select class="form-control form_input {{$f['field']}} p-0 select_maintenance" key="{{$f['field']}}" id="def-id" <?php echo $required; ?>>
														@foreach($f['select_options'] as $sel)
														<option value="{{$sel->value}}">{{$sel->description	}}</option>
														@endforeach
													</select>
													@endif
												</div> 
											</div>	
											@endforeach	
										</div>

									</div>
								</div>
							</div>
						</div>
						
					</div>
					<div class="card-footer">
						@if($credential->is_create || $credential->is_edit)
						<button class="btn bg-gradient-success2 float-right">Save</button>
						@endif
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')

<script type="text/javascript">
	var dt_table;
	var typingTimer;                
	var doneTypingInterval = 400;
	var id_chart_account_holder;
var opcode = 0; //Add
var POST_ROUTE = '/maintenance/post/'+'{{$type}}';
var VIEW_ROUTE = '/maintenance/view/'+'{{$type}}';
var current_id_reference = 0;
var DELETE_ROUTE = '/maintenance/delete/'+'{{$type}}';
var add_button = $('#spn_form_button').html();
var cancel_button = '<button class="btn btn-sm bg-gradient-danger2" onclick="cancel_edit()"><i class="fa fa-times"></i>&nbsp;&nbsp;&nbsp;Close Editing</button>';
var opcode = 0;

var form_html = $('#form_body').html();
var MODULE_TITLE = "{{$mod_title}}"
$(function() {
	$.contextMenu({
		selector: '.list_row',
		callback: function(key, options) {
			var m = "clicked: " + key;
			var id_reference= $(this).attr('data-id');

			if(key == "edit"){
				view_details(id_reference)
			}
		},
		items: {
			"edit": {name: "Edit", icon: "fas fa-eye"},
			"sep1": "---------",
			"quit": {name: "Close", icon: "fas fa-times"}
		}
	});   
});

$(document).ready(function(){
	//Initialize Datatable
	$('#tbl_list_maintenance thead tr').clone(true).appendTo( '#tbl_list_maintenance thead' );
	$('#tbl_list_maintenance thead tr:eq(1)').addClass("head_rem")
	$('#tbl_list_maintenance thead tr:eq(1) th').each( function (i) {
		if(i > 0){
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
		}
	});
	dt_table    = init_table();
	$('#tbl_list_maintenance .head_rem').remove();
	$('.dt-buttons').removeClass('btn-group')
	$('.dt-buttons').find('button').removeClass('btn-secondary');

	if($('.select_maintenance').length > 0){
		$('.select_maintenance').select2({dropdownParent: $('#bar')});
	}
})
$(document).on('select2:open', (e) => {
	const selectId = e.target.id
	$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
		key,
		value,
		) {
		value.focus()
	})
}) 
function append_form(){
	$('#form_body').append(form_html);
	var card_id = makeid(8);
	var select_id = makeid(8)

	$('.card-form').last().attr('id',card_id);

	if($('.card-form').last().find('.select_maintenance').length > 0){
		$('.card-form').last().find('.select_maintenance').attr('id',select_id);
		$('#'+select_id).select2({dropdownParent: $('#'+card_id)});
	}
}
function makeid(length){
	var result           = '';
	var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	var charactersLength = characters.length;
	for(var i = 0; i < length; i++ ) {
		result += characters.charAt(Math.floor(Math.random() * charactersLength));
	}
	return result;
}
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
	var table = $("#tbl_list_maintenance").DataTable(config)
	console.log({table});
	return table;
}
function remove_card(obj){
	if(opcode == 1){
		return;
	}
	var parent_card = $(obj).closest('.card');
	parent_card.remove()
}
function post(){
	var post_data = [];
	$('.card-form').each(function(){
		var temp = {};
		var card_inputs = $(this).find('.form_input');

		card_inputs.each(function(){
			var key = $(this).attr('key')
			temp[key] = $(this).val()
		})

		post_data.push(temp);
	})


	console.log({post_data})
	$.ajax({
		type         :    "POST",
		url          :    POST_ROUTE,
		data         :   {'post_object'  : post_data,
		'opcode'       : opcode,
		'id_reference' : current_id_reference},
		beforeSend   :   function(){
			show_loader();
		},
		success      :   function(response){
			console.log({response});
			hide_loader()
			if(response.RESPONSE_CODE == "SUCCESS"){
				
				Swal.fire({
					title: "{{$mod_title}} successfully saved",
					text: '',
					icon: 'success',
					showConfirmButton : false,
					timer  : 1300
				}).then((result) => {
					location.reload()
				});		
			}else if(response.RESPONSE_CODE == "CREDENTIAL_ERROR"){
				Swal.fire({
					title: response.message,
					text: '',
					icon: 'warning',
					showConfirmButton : false,
					timer : 1500
				})
			}	
		},error: function(xhr, status, error) {
			hide_loader()
			var errorMessage = xhr.status + ': ' + xhr.statusText
			Swal.fire({
				title: "Error-" + errorMessage,
				text: '',
				icon: 'warning',
				confirmButtonText: 'OK',
				confirmButtonColor: "#DD6B55"
			});
		}
	})
}
$('#submit_maintenance').submit(function(e){
	e.preventDefault();
	var card_length = $('.card-form').length;
	if(card_length == 0){
		Swal.fire({
			title: "Invalid Request",
			text: '',
			icon: 'warning',
			confirmButtonText: 'Close',
			confirmButtonColor: "#DD6B55"
		});		

		return;
	}	
	Swal.fire({
		title: 'Do you want to save this?',
		icon: 'warning',
		showDenyButton: false,
		showCancelButton: true,
		confirmButtonText: `Save`,
	}).then((result) => {
		if (result.isConfirmed) {

			post();
		} 
	})	
})
function view_details($id_reference){
	$.ajax({
		type      :        'GET',
		url       :        VIEW_ROUTE,
		data      :        {'id_reference'  : $id_reference},
		beforeSend:        function(){
			show_loader()
		},
		success   :        function(response){
			hide_loader()
			//Clear the forms
			$('#form_body').html('');
			//Append 1 form
			$('#form_body').append(form_html);

			current_id_reference = response.id_reference;
			$.each(response.details,function(key,val){
				console.log({key})
				$('.'+key).val(val);
			})
			var card_id = makeid(8);
			var select_id = makeid(8)

			$('.card-form').last().attr('id',card_id);

			if($('.card-form').last().find('.select_maintenance').length > 0){
				$('.card-form').last().find('.select_maintenance').attr('id',select_id);
				$('#'+select_id).select2({dropdownParent: $('#'+card_id)});
			}
			$('#spn_form_button').html(cancel_button);
			$('#txt_head').html("Edit "+MODULE_TITLE+" ("+current_id_reference+")");
			opcode = 1;
		},error: function(xhr, status, error) {
			hide_loader()
			var errorMessage = xhr.status + ': ' + xhr.statusText
			Swal.fire({
				title: "Error-" + errorMessage,
				text: '',
				icon: 'warning',
				confirmButtonText: 'OK',
				confirmButtonColor: "#DD6B55"
			});
		}
	})
}
function cancel_edit(){
	opcode = 0;
	current_id_reference = 0;
	$('#spn_form_button').html(add_button);
	$('#txt_head').html("Add "+MODULE_TITLE);
	//Clear the forms
	$('#form_body').html('');
	//Append 1 form
	$('#form_body').append(form_html);
}


</script>


@if($credential->is_delete)
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_list_maintenance_filter').append('<a class="btn bg-gradient-danger2" onclick="delete_confirmation()" style="float:left"><i class="fa fa-trash"></i>&nbsp;Delete '+MODULE_TITLE+'</a>')
	})
	function post_delete(){
	// $(".chk_list:checkbox:checked").length
	var reference_array = [];
	$('.chk_list').each(function(){
		var checked = $(this).prop('checked');
		if(checked){
			reference_array.push($(this).val());
		}
	})
	$.ajax({
		type       :    "POST",
		url        :    DELETE_ROUTE,
		data       :   {'id_references' : reference_array},
		beforeSend   :   function(){
			show_loader();
		},
		success      :   function(response){
			console.log({response});
			hide_loader()
			if(response.RESPONSE_CODE == "SUCCESS"){
				
				Swal.fire({
					title: "{{$mod_title}} successfully deleted",
					text: '',
					icon: 'success',
					showConfirmButton : false,
					timer  : 1300
				}).then((result) => {
					location.reload()
				});		
			}else if(response.RESPONSE_CODE == "CREDENTIAL_ERROR"){
				Swal.fire({
					title: response.message,
					text: '',
					icon: 'warning',
					showConfirmButton : false,
					timer : 1500
				})
			}		
		},error: function(xhr, status, error) {
			hide_loader()
			var errorMessage = xhr.status + ': ' + xhr.statusText
			Swal.fire({
				title: "Error-" + errorMessage,
				text: '',
				icon: 'warning',
				confirmButtonText: 'OK',
				confirmButtonColor: "#DD6B55"
			});
		}
	})
}

function delete_confirmation(){
	var refernce_length = $(".chk_list:checkbox:checked").length;

	if(refernce_length == 0){
		Swal.fire({
			title: "Please select atleast 1 reference",
			text: '',
			icon: 'warning',
			confirmButtonText: 'Close',
			confirmButtonColor: "#DD6B55"
		});		
		return;
	}
	Swal.fire({
		title: 'Are you sure you want to delete the selected '+MODULE_TITLE+'?',
		icon: 'warning',
		showDenyButton: false,
		showCancelButton: true,
		confirmButtonText: `Save`,
	}).then((result) => {
		if (result.isConfirmed) {

			post_delete();
		} 
	})	
}
</script>
@endif
@endpush
