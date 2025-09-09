@extends('adminLTE.admin_template')

@section('content')

	<style type="text/css">
		#tbl_items tr > th,#tbl_items tr > td,
		#tbl_items_conf tr > th,#tbl_items_conf tr > td{
			padding:2px !important;
			vertical-align:top;
			font-family: Arial !important;
			font-size: 14px !important;
		}
		table#tbl_items  td,
		table#tbl_items_conf  td{
			position: relative;
		}
		#tbl_items input, #tbl_items a,#tbl_items select,#tbl_items button,
		#tbl_items_conf input, #tbl_items_conf a,#tbl_items_conf select,#tbl_items_conf button{
		    display: block;
		    height: 100%;
		    top:0;
		    left:0;
		    margin: 0;
		    width:100%;
		    font-weight: bold;
		    box-sizing: border-box;	
		}
		.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		      padding: 13px !important;
		      line-height: 1.42857143;
		      vertical-align: top;
		      border-top: 1px solid #ddd;
 		}
		#tbl_items select,
		#tbl_items_conf select{
    		font-size: 14px;
    		padding : 0 0 0 0;
  		}
  		.td_qty{
			max-width: 40px !important;
			min-width: 40px !important;
  		}
  		.col_table_input{
  			height: 22px !important;
  		}
  		.td_item_name{
  			max-width: 150px !important;
  			min-width: 150px !important;
  		}
/*  		.dropdown-menu {
		  max-height: 150px;
		  overflow-y: auto;
		}*/
		.modal-conf {
    		max-width: 95% !important;
    		min-width: 95% !important;
		}
		.col_table_input_fil{
			border: 3px solid green !important;
		}
	</style>

<?php 
	if(isset($details->status)){
		$status_valid = ($details->status == 0)?true:false;
	}else{
		$status_valid = false;
	}
?>
<div class="container-fluid">
   <div class="row">
        <div class="col-md-12">
        	    <div class="ribbon-wrapper ribbon-lg">
        	  	@if($opcode ==0)
			    <div class="ribbon bg-info">
			      	Create Request
			    </div>
			    @else
			    <div class="ribbon bg-{{ ($details->status==0)?'primary':(($details->status == 1)?'success':'danger') }}">
			      	{{$details->status_name}}
			    </div>
			    @endif
			  </div>
          	<div class="card card-info">
				<div class="card-header">
					<h3 class="card-title">Request Form</h3>
				</div>
              	<form id="frm_submit_request">
	                <div class="card-body">
	                	<div class="row">
							<div class="col-sm-6">
								<div class="form-group row">
									<label for="txt_date" class="col-sm-3 col-form-label">Date</label>
									<div class="col-sm-9">
										<input type="date" class="form-control" id="txt_date" value="{{$current_date}}"/>
									</div>
								</div>
								<div class="form-group row">
									<label for="txt_requested_by" class="col-sm-3 col-form-label">Requested by</label>
									<div class="col-sm-9">
										<select class="form-control select2" id="sel_request_by">
											@if($opcode == 1)
												<option value="{{ $details->id_employee }}">{{ $details->requested_by }}</option>
											@endif
										</select>
										<!-- <input type="text" class="form-control" id="txt_requested_by" placeholder="Requested By"> -->
									</div>
								</div>
								<div class="form-group row">
									<label for="sel_request_type" class="col-sm-3 col-form-label">Request Type</label>
									<div class="col-sm-9">
										<select class="form-control select2" id="sel_type">
											@foreach($request_types as $type)
												<?php 
													if(isset($details)){
														$checked = ($type->id_type == $details->id_type)?'selected':'';
													}
												?>
												<option value="{{$type->id_type}}" {{ $checked ?? '' }}>{{$type->description}}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group row">
									<label for="txt_remarks" class="col-sm-3 col-form-label">Remarks</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="txt_remarks" value="{{ $details->remarks ?? '' }}">
									</div>
								</div>
								<div class="form-group row">
									<label for="txt_reason" class="col-sm-3 col-form-label">Reason</label>
									<div class="col-sm-9">
										<textarea class="form-control" rows="4" id="txt_reason" required="">{{ $details->reason ?? '' }}</textarea>
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								@if($opcode == 0 || isset($details->status) && $details->status == 0)
									<button class="btn btn-primary btn-xs" type="button" onclick="add_item()"><i class="far fa-plus-square"></i> Add Item</button>
								@endif
								<div class="table-responsive" style="max-height: calc(100vh - 250px);overflow-y: auto;margin-top: 10px">
									 <table class="table table-hover table-striped" id="tbl_items" style="white-space: nowrap;">
										<thead>
											<tr class="table_header">
												<th>Item Name</th>
												<th>Quantity</th>
												<th>UOM</th>
												@if(!$status_valid && $opcode == 1)
													<th>Approved Details</th>
												@endif
												<th>Remarks</th>
												<th width="1%"></th>
											</tr>
										</thead>
										<tbody id="item_body">
											@if(isset($items))
												@foreach($items as $item)
												<tr class="row_item">
													<td class="td_item_name">
														<input type="text" name="item_name" class="col_name col_table_input" placeholder="Enter item name or item code" list="items-{{ $item->id_request_details ?? 4421 }}" value="{{ $item->item_name }}" >
														<datalist id="items-{{ $item->id_request_details ?? 4421 }}"></datalist>
													</td>
													<td class="td_qty"><input type="number" name="Quantity" class="col_quantity col_table_input" placeholder="Quantity" value="{{ $item->quantity }}"></td>
													<td><select class="col_uom col_table_input">
														<?php
															$uom_list = explode(",",$item->uom_list);
														?>
														@foreach($uom_list as $uom)
															<?php
																$uom_parse = explode('-', $uom);
																$selected = ($item->id_uom == $uom_parse[0]) ?'selected':'';
															?>
															<option value="{{ $uom_parse[0] }}" {{$selected}}>{{ $uom_parse[1] }}</option>
														@endforeach
														
													</select></td>
													@if(!$status_valid && $opcode == 1)
														<td class="td_qty"><input type="text" name="Quantity" class="col_table_input" placeholder="Quantity" value="{{ $item->approved_details }}"></td>
													@endif
													<td><input type="text" name="Remarks" class="col_remarks col_table_input" placeholder="Remarks" value="{{ $item->remarks }}"></td>
													<td><a class="btn btn-xs btn-edit delete_row" title="Remove" onclick="remove_row(this)"><i class="fa fa-times"></i>&nbsp;&nbsp;</a></td>
													<td class="col_id_item hide">{{$item->id_item}}</td>
												</tr>
												@endforeach
											@endif
										</tbody>
									</table>
								</div>
							</div>
						</div>
	           		</div>

	                <!-- /.card-body -->
	                <div class="card-footer">
	                	<div class="float-right">
	                		@if($opcode == 0 || ($opcode == 1 && $credential->is_edit == 1))
	                		@if($opcode == 0 || ($opcode == 1 && isset($details->status) && $details->status == 0))
								<button type="submit" class="btn btn-success">Save</button>
								@if($opcode == 1 && $credential->is_confirm == 1)
									<button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-request-status">Update Status</button>
								@endif
							@endif
							@endif
							<a class="btn btn-default" onclick="window.location='{{ isset($request->return_url)?$request->return_url:url()->previous() }}'">Back</a>
	                	</div>
	                </div>
             	</form>
            </div>
        </div>
    </div>
</div>
@if(isset($details->status) && $details->status == 0)
	@include('request.confirmation_modal')
@endif
@endsection
@push('scripts')
<script type="text/javascript">
	$(document).on('select2:open', () => {
    	document.querySelector('.select2-search__field').focus();
  	});
  	var opcode = "{{ $opcode }}";
  	var is_edit_valid = true;
	$('#sel_type').select2();
	var set_status = "{{ isset($details->status) }}";

	if(opcode == 0){
		add_item();
	}
	<?php
		if(isset($details->status)){
			if($details->status > 0){
				echo "$('#frm_submit_request').find('input').attr('readonly',true);
				      $('#frm_submit_request').find('textarea').attr('readonly',true);
				      $('#frm_submit_request').find('select').attr('disabled',true);
				      is_edit_valid = false;";
			}
		}

	?>
	// $('.sel_data').each(function(){
	// 	initialize_select($(this));
	// })
	$("#sel_request_by").select2({
	    minimumInputLength: 2,
	       createTag: function (params) {
	        return null;
	    },
	    ajax: {
	    	tags: true,
	        url: '/admin/search_employee',
	        dataType: 'json',
	        type: "GET",
	        quietMillis: 1000,
	        data: function (params) {

	            var queryParameters = {
	                term: params.term
	            }
	            return queryParameters;
	        },
		    processResults: function (data) {
		    	console.log({data});
		        return {
		            results: $.map(data.items, function (item) {
		                return {
		                    text: item.tag_value,
		                    id: item.tag_id
		                }
		            })
		        };
		    }
	    }
	});

	function initialize_select(obj){
		$(obj).select2({
		    minimumInputLength: 2,
		       createTag: function (params) {
		        return null;
		    },
		    ajax: {
		    	tags: true,
		        url: '/admin/search_employee',
		        dataType: 'json',
		        type: "GET",
		        quietMillis: 1000,
		        data: function (params) {

		            var queryParameters = {
		                term: params.term
		            }
		            return queryParameters;
		        },
			    processResults: function (data) {
			    	console.log({data});
			        return {
			            results: $.map(data.items, function (item) {
			                return {
			                    text: item.tag_value,
			                    id: item.tag_id
			                }
			            })
			        };
			    }
		    }
		});
	}
	function add_item(){
		var list_id = makeid(6);
		var out = `	<tr class="row_item">
						<td class="td_item_name">
							<input type="text" name="item_name" class="col_name col_table_input" placeholder="Enter item name or item code" list="items-`+list_id+`" value="">
							<datalist id="items-`+list_id+`"></datalist>
						</td>
						<td class="td_qty"><input type="number" name="Quantity" class="col_quantity col_table_input" placeholder="Quantity"></td>
						<td><select class="col_uom col_table_input">
							
						</select></td>
						<td><input type="text" name="Remarks" class="col_remarks col_table_input" placeholder="Remarks"></td>
						<td><a class="btn btn-xs btn-edit delete_row" title="Remove" onclick="remove_row(this)"><i class="fa fa-times"></i>&nbsp;&nbsp;</a></td>
						<td class="col_id_item hide"></td>
					</tr>`;
		$('#item_body').append(out);
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
	function remove_row(obj){
		var parent_row = $(obj).closest('tr.row_item');
		var id_item = parent_row.find('td.col_id_item').text();

		// isset($details->status)
		if($('tr.row_item').length == 1 || !is_edit_valid){
			return;
		}

		if(id_item == ""){
			parent_row.hide("slow", function(){ $(this).remove(); });
			return;
		}
	    Swal.fire({
	      title: 'Do you want to remove this row ?',
	      icon: 'warning',
	      showDenyButton: false,
	      showCancelButton: true,
	      confirmButtonColor: '#ff3333',
	      confirmButtonText: `Delete`,
	    }).then((result) => {
	        if (result.isConfirmed) {
				parent_row.hide("slow", function(){ $(this).remove(); });
	        } 
	    })
	}
	var typingTimer;                
 	var doneTypingInterval = 700;
	$(document).on('keyup','input.col_name',function(e){
		clearTimeout(typingTimer);
		var val = $(this).val();
		var obj = $(this);
		var list = $(this).attr('list');
		console.log("HAHAHAHAHAHHAAAAAAAAAAAAAAAAAAA");
		if(val.length > 2){
			$('#'+list).html("<option data-value='x' value='Searching for "+val+" ...'>");
			typingTimer = setTimeout(function(){filter_product(obj)}, doneTypingInterval);   
		}
	});

	$(document).on('keydown','input.col_name',function(e){
		var list = $(this).attr('list');
		clearTimeout(typingTimer);
	});

	function filter_product(obj){
		var parent_row = $(obj).closest('tr.row_item');
		var list = $(obj).attr('list');
    	var searchField = parent_row.find($(obj)).val();
  		$.ajax({
  			type              :       'GET',
  			url               :       '/admin/search_item',
  			data              :       {'search' : searchField},
  			beforeSend        :       function(){
  									   	// $('#'+list).html("");
  			},	
  			success           :       function(response){
  					$('#'+list).html(response);
  				    $(obj).on('input', function(){
					var val = this.value;
					if($('#'+list+' option').filter(function(){
					  return this.value.toUpperCase() === val.toUpperCase();        
					}).length){
            			var val = this.value;
            			var id_item = $('#'+list+' option').filter(function(){
              				return this.value == val;
            			}).data('value');
            			if(id_item == "x"){
							$(obj).val('');
							parent_row.find('td.col_id_item').text('');
							$('#'+list).html('');
							return;
          				}
			            var product = val.split('|');
			            $(obj).val($.trim(product[0])); // item_name
			            // parent_row.find('input.col_item_code').val($.trim(product[0])); // item code
			            $('#'+list).html('');
			            $(obj).blur();

			            if(parent_row.find('td.col_id_item').text() == id_item){
			              return;
			            }          
						// if(!check_item_dup(id_item)){
						//   swal({
						//     title: "Duplicate item found",
						//     text: $.trim(product[1]) +"-"+$.trim(product[0]) ,
						//     type: 'warning',
						//     confirmButtonText: 'OK',
						//     confirmButtonColor: "#DD6B55"});

						//   $(obj).val('');
						//   parent_row.find('input.col_item_code').val('');
						//   return;
						// }
           				 parent_row.find('td.col_id_item').text(id_item); // id_item
           				 parseUoM(id_item,parent_row);
          			}
     			});
  			}
  		})
    	console.log({searchField});
	}

	function parseUoM(id_item,parent_row){
		$.ajax({
			type         :        'GET',
			url          :        '/admin/item/get_uom',
			data         :        {'id_item' : id_item},
			success      :        function(response){
								  parent_row.find('select.col_uom').html(response);
			}
		})
	}
	$('#frm_submit_request').submit(function(e){
		e.preventDefault()
		var requested_by = $('#sel_request_by').val();
		if(requested_by == null){
			Swal.fire({
				position: 'center',
				icon: 'warning',
				title: 'Please fill requested by',
				showConfirmButton: false,
				cancelButtonText: `Close`,
				showCancelButton: true,
			});
			$('#sel_request_by').focus();
			return;
		}
		var id_item=[],quantity=[],id_uom=[],item_remarks=[];
		$('tr.row_item').each(function(){
			if($(this).find('td.col_id_item').text() != ""){
				id_item.push($(this).find('td.col_id_item').text());
				quantity.push($(this).find('input.col_quantity').val());
				id_uom.push($(this).find('select.col_uom').val());
				item_remarks.push($(this).find('input.col_remarks').val());
			}
		})

		if(id_item.length == 0){
			Swal.fire({
				position: 'center',
				icon: 'warning',
				title: 'Please select atleast 1 item',
				showConfirmButton: false,
				cancelButtonText: `Close`,
				showCancelButton: true,
			});
			return;
		}
		Swal.fire({
			title: 'Do you want to save the changes?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				post(id_item,quantity,id_uom,item_remarks);
			} 
		// else if (result.isDenied) {
		//   Swal.fire('Changes are not saved', '', 'info')
		// }
		})	
		console.log({id_item,quantity,id_uom,item_remarks});
	})
	function post(id_item,quantity,id_uom,item_remarks){
		var date = $('#txt_date').val();
		var requested_by = $('#sel_request_by').val();
		var request_type = $('#sel_type').val();
		var remarks = $('#txt_remarks').val();
		var reason = $('#txt_reason').val();
		$.ajax({
			type           :        'POST',
			url            :        '/admin/request/post_request',
			data           :        {
											'date' : date,
											'requested_by' : requested_by,
											'request_type' : request_type,
											'remarks'	: remarks,
											'reason' : reason,
											'opcode' : opcode,
											'id_request' : "{{ $details->id_request ?? 0 }}",

											'id_item' : id_item,
											'quantity' : quantity,
											'id_uom' : id_uom,
											'item_remarks' : item_remarks								 
									},
			beforeSend      :       function(){
									show_loader();
			},
			success         :       function(response){
									console.log({response});
									setTimeout(
									function() {
										hide_loader();
										if(response.message == "success"){
											Swal.fire({
												position: 'center',
												icon: 'success',
												title: 'Request Successfully saved',
												showConfirmButton: false,
												timer: 1500
											}).then(function() {
												if(opcode == 0){
													window.location = '/admin/request/view?id_request='+response.id_request;
												}
											})
										}
									}, 1500);
			},error: function(xhr, status, error){
				hide_loader();
				var errorMessage = xhr.status + ': ' + xhr.statusText;
				Swal.fire({
					position: 'center',
					icon: 'warning',
					title: "Error-"+errorMessage,
					showConfirmButton: false,
					showCancelButton: true,
					cancelButtonText : "Close"
				})
	        } 	
		});
	}

</script>
@endpush

