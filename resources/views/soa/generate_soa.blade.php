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
		  	max-width: 100% !important;
		  	min-width: 100% !important;
		  }
		  .col_table_input_fil{
		  	border: 3px solid green !important;
		  }

		  .select2-selection__choice{
		  	background: #4d94ff !important;
		  }
		  .select2-search__field{
		  	/*color :white !important;*/
		  }


		  div.table-scroll {
		  	overflow: scroll;
		  	position: relative;
		  	height: 700px;
		  }
		  table{
		  	position: relative;
		  	border-collapse: collapse;
		  }
		  thead#tbl_heading th {
		  	position: -webkit-sticky; /* for Safari */
		  	position: sticky;
		  	top: 0;
		  	background: black;
		  	color: white;
		  }
		  .form-row{
		  	margin-top: -5px !important;
		  }
		  label.lbl_gen{
		  	margin-bottom: -10px !important;
		  	font-size: 13px;
		  	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		  }
		</style>

		<div class="container-fluid">
		<a class="btn btn-default btn-sm" href="{{(request()->get('href') == '')?'/admin/soa/index':request()->get('href')}}"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;SOA Control List</a>
			<div class="row" style="margin-top:10px">
				<div class="col-md-12">
					<div class="card  card-primary card-outline">
		<!-- 		<div class="card-header">
					<h3 class="card-title">Generate SOA</h3>
				</div> -->
				<form id="frm_submit_generate_soa">
					<div class="card-body">
						<h4 style="margin-bottom: 20px;">Generate SOA</h4>
						<div class="row">
							<div class="col-sm-8">
								<div class="form-row">
									<div class="form-group col-md-8">
										<label for="sel_account" class="lbl_gen">Account</label>
										<select class="form-control select2" id="sel_account" required>
											@if(isset($default_account))
												<option value="{{$default_account->id_client_profile}}">{{$default_account->account_no}}||{{$default_account->name}}</option>
											@endif
										</select>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-8">
										<label for="sel_cost_center" class="lbl_gen">Cost Center</label>
										<select class="form-control select2" id="sel_cost_center" multiple="multiple" required=""></select>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="id_chk_sel_all" disabled="">
											<label class="form-check-label" for="id_chk_sel_all">
												Select All
											</label>
										</div>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-4">
										<label class="lbl_gen">Date</label>
										<input type="date" class="form-control" id="txt_date_from" value="{{$current_date}}" required="" />
									</div>
									<div class="form-group col-md-4">
										<label class="lbl_gen">&nbsp;</label>
										<input type="date" class="form-control" id="txt_date_to" value="{{$current_date}}" required="" />
									</div>
								</div>
<!-- 								<div class="form-group row">
									<label for="sel_account" class="col-sm-2 col-form-label">Account</label>
									<div class="col-sm-10">
										<select class="form-control select2" id="sel_account">
										</select>
									</div>
								</div> -->
<!-- 								<div class="form-group row">
									<label for="sel_cost_center" class="col-sm-2 col-form-label" >Cost Center</label>
									<div class="col-sm-10">
										<select class="form-control select2" id="sel_cost_center" multiple="multiple">
										</select>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="id_chk_sel_all" disabled="">
											<label class="form-check-label" for="id_chk_sel_all">
											Select All
											</label>
										</div>
									</div>
								</div> -->
<!-- 								<div class="form-group row">
									<label for="txt_date" class="col-sm-2 col-form-label">Date From</label>
									<div class="col-sm-10">
										<input type="date" class="form-control" id="txt_date_from" value="{{$current_date}}"/>
									</div>
								</div>
								<div class="form-group row">
									<label for="txt_date" class="col-sm-2 col-form-label">Date To</label>
									<div class="col-sm-10">
										<input type="date" class="form-control" id="txt_date_to" value="{{$current_date}}"/>
									</div>
								</div> -->
							</div>
						</div>
					</div>
					<!-- /.card-body -->
					<div class="card-footer">
						<div class="float-right">
							<button type="submit" class="btn bg-gradient-info">Generate</button>

							<a class="btn btn-default">Back</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@include('soa.preview_modal')
@include('soa.attachment_option_preview')
@endsection
@push('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		<?php
			if(isset($default_account)){
				echo "parseCostCenter('".$default_account->id_client_profile."')";
			}

		?>
		$("#modal-preview-data").on("hidden.bs.modal", function () {
			$('#iframe_prev_data').attr('src','');
		});
	})
	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});
	$("#sel_account").select2({
		minimumInputLength: 2,
		createTag: function (params) {
			return null;
		},
		ajax: {
			tags: true,
			url: '/admin/serch_account',
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
					results: $.map(data.accounts, function (item) {
						return {
							text: item.tag_value,
							id: item.tag_id
						}
					})
				};
			}
		}
	});
	$('#sel_account').on('change',function(){
		var val = $(this).val();
		parseCostCenter(val);
	})
	function parseCostCenter(val){
		$.ajax({
			type            :             'GET',
			url             :             '/admin/parse_cost_center',
			data            :             {'id_client_profile' : val},
			success         :             function(response){
				console.log({response});
				var out ='<option value="0">*No Cost Center*</option>';
				var array_id = [];
				$('#id_chk_sel_all').prop('disabled',false);
				array_id.push(0);
				$.each(response.cost_centers,function(i,item){
					array_id.push(item.id_tbl_client_cost_center);
					out += "<option value='"+item.id_tbl_client_cost_center+"'>"+item.description+"</option>";
				})
				$('#sel_cost_center').html(out);
				$('#sel_cost_center').select2();
				if(response.cost_centers.length > 0){
					$('#sel_cost_center').val([]).change();
					$('#id_chk_sel_all').prop('checked',false);
					$('#id_chk_sel_all').prop('disabled',false);

					$('#sel_cost_center').prop('disabled',false);
				}else{
					$('#sel_cost_center').val([0]).change();
					$('#id_chk_sel_all').prop('disabled',true);
					$('#id_chk_sel_all').prop('checked',true);
					$('#sel_cost_center').prop('disabled',true);
				}
				$('#id_chk_sel_all').click(function(){
					var checked = $(this).prop('checked');
					console.log({checked});
					if(checked){
						$('#sel_cost_center').val(array_id).change();
					}else{
						$('#sel_cost_center').val([]).change();
					}
				})
			}
		})
	}
	$('#frm_submit_generate_soa').submit(function(e){
		e.preventDefault();
		
		$('#modal-preview-data').modal('show');
		var form_data = {
			'date_from'  : $('#txt_date_from').val(),
			'date_to'    : $('#txt_date_to').val(),
			'account_no' : $('#sel_account').val(),
			'cost_center' : $('#sel_cost_center').val()
		}
		var src = '/admin/preview_frame?'+$.param(form_data);
		$('#iframe_prev_data').attr("src",src)

		// $.ajax({
		// 	type             :         'GET',
		// 	url              :         '/admin/generate_soa/get_soa_data',
		// 	data             :         form_data,
		// 	success          :         function(response){
		// 								console.log({response});
		// 	}
		// })
		console.log({form_data});
	})
	function soa_saved(control_number){
		$('#modal-preview-data').modal('hide');
		window.location = '/admin/view_soa?control_number='+control_number;
	}
	function show_option_attachment(){
		$('#modal-attachment-option').modal('show');
	}
	function redirect_soa_attachment_preview(){
		var form_data = {
			'date_from'  : $('#txt_date_from').val(),
			'date_to'    : $('#txt_date_to').val(),
			'account_no' : $('#sel_account').val(),
			'cost_center' : $('#sel_cost_center').val()
		}
		var src = 'soa_attachment/preview?'+$.param(form_data);
		window.open(src,'_blank');
	}
</script>
@endpush
