@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	@media (min-width: 1200px) {
		.container{
			max-width: 780px;
		}
	}
</style>
<div class="container">
	<?php
	$scheduler_types = DB::table('scheduler_type')->get();
	$status_o = ['Inactive','Active'];
	$schedule_cmd_type =DB::table('schedule_cmd_type')->get();
	?>

	<?php
	if($opcode  == 1){
		$link_route = '#';

		switch($details->books){
			case '1':
				$link_route = "/journal_voucher/view/";
				break;
			case '2':
				if($details->cdv_type == 2){
					$ct = 'expenses';
				}elseif($details->cdv_type == 3){
					$ct= 'asset_purchase';
				}elseif($details->cdv_type == 4){
					$ct = 'others';
				}
				$link_route = "/cdv/$ct/view/";
				break;
		}		
	}

?>
	<?php $back_link = (request()->get('href') == '')?'/scheduler/index':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Scheduler List</a>
	<div class="card">
		<form id="frm_post_schedule">
			<div class="card-body">
				<div class="text-center mb-5">
					<h5 class="head_lbl">Journal Entry Scheduler @if($opcode == 1)<small>(ID# {{$details->id_scheduler}}) @if($details->rstatus == 0)<span class="badge badge-danger">Inactive</span>@else <span class="badge badge-success">Active</span>  @endif</small> @endif</h5>
				</div>
				<div class="form-row">
					<div class="form-group col-md-4">
						<label class="lbl_color mb-0">Date Start</label>
						<input type="date" class="form-control form-control-border" name="" value="{{$sdate ?? MySession::current_date()}}" id="txt_sched_date">
					</div>
					<div class="form-group col-md-4">
						<label class="lbl_color mb-0">Date End</label>
						<input type="date" class="form-control form-control-border" name="" value="{{$details->rstop_date ?? ''}}" id="txt_stop_date">
					</div>
					<div class="form-group col-md-4">
						<label class="lbl_color mb-0">Schedule</label>
						<select class="form-control form-control-border p-0" id="sel_sched_type">
							@foreach($scheduler_types as $st)
							<option value="{{$st->id_scheduler_type}}" <?php echo ($st->id_scheduler_type == ($details->id_scheduler_type ?? 3))?"selected":""; ?> >{{$st->description}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-4">
						<label class="lbl_color mb-0">Type</label>
						<select class="form-control form-control-border p-0" id="sel_type">
							@foreach($schedule_cmd_type as $stt)
							<option value="{{$stt->id_schedule_cmd_type}}" <?php echo ($stt->id_schedule_cmd_type == $stype)?"selected":""; ?> >{{$stt->description}}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group col-md-8">
						<label class="lbl_color mb-0">Reference</label>
						<select class="form-control form-control-border form_input p-0 jv_parent" id="sel_reference" <?php echo(($details->type_code ?? 1) > 3)?'disabled':''; ?> >
							@if(isset($selected_reference))
							<option value="{{$selected_reference->tag_id}}">{{$selected_reference->tag_value}}</option>
							@endif
						</select>
					</div>
					@if($opcode == 1)
					<div class="form-group col-md-6">
						<label class="lbl_color mb-0">Status</label>
						<select class="form-control form-control-border p-0" id="sel_status">
							@foreach($status_o as $val=>$s)
							<option value="{{$val}}" <?php echo (($details->rstatus ?? 0))==$val?"selected":""; ?> >{{$s}}</option>
							@endforeach
						</select>
					</div>
					@endif
				</div>
				<div class="form-row">
					<div class="form-group col-md-12">
						<label class="lbl_color mb-0">Remarks</label>
						<textarea class="form-control" rows="3" style="resize:none" id="txt_remarks">{{$details->remarks ?? ''}}</textarea>
					</div>
				</div>
				@if($opcode == 1)
				<table class="tbl_scheduler table-head-fixed table-bordered" width="100%">
					<thead>
						<tr class="text-center">
							<th class="table_header_dblue">Date</th>
							<th class="table_header_dblue">New Reference</th>
							<th class="table_header_dblue">Date Posted</th>
							<th class="table_header_dblue">Status</th>
						</tr>
					</thead>
					<tbody id="tbody_history">
						@if(count($history) > 0)
						@foreach($history as $h)
						<tr>
							<td>{{$h->date}}</td>
							<td class="text-center"><a href="{{$link_route}}{{$h->new_reference}}" target="_blank">{{$h->new_reference}}</a></td>
							<td>{{$h->date_executed}}</td>
							<td><span class="badge text-sm badge-{{$h->status_c}}">{{$h->status}}</span></td>
						</tr>
						@endforeach
						@else
						<tr>
							<td class="text-center" colspan="4">No Record</td>
						</tr>
						@endif

					</tbody>
				</table>	
				@endif		
			</div>
			<div class="card-footer">
				<button class="btn btn-sm bg-gradient-success float-right">&nbsp;Set Scheduler</button>
			</div>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	const ID_SCHEDULER = {{$id_scheduler ?? 0}};
	$(document).on('select2:open', (e) => {
		const selectId = e.target.id
		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function(key,value,){
			value.focus()
		})
	}) 
	$("#sel_reference").select2({
		minimumInputLength: 2,
		width: '100%',
		placeholder : "Select JV#",
		createTag: function (params) {
			return null;
		},
		ajax: {
			tags: true,
			url: '/search_reference',
			dataType: 'json',
			type: "GET",
			quietMillis: 1000,
			
			data: function (params) {
				var queryParameters = {
					term: params.term,
					type : $('#sel_type').val()
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
	$('#sel_type').change(function(){
		var val = $(this).val();
		$('#sel_reference').val(0).trigger('change');
		if(val <= 3){
			$('#sel_reference').prop('disabled',false);
			$place_holder = val == 1?'JV#':'CDV#';
			$('#sel_reference').next().find('.select2-selection__placeholder').text(`Select ${$place_holder}`);

		}else{
			$('#sel_reference').next().find('.select2-selection__placeholder').text('');
			$('#sel_reference').prop('disabled',true);
		}
	})
	$('#frm_post_schedule').submit(function(e){
		e.preventDefault();
		Swal.fire({
			title: 'Do you want to post this scheduler ?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				post_schedule();
			} 
		})	
	});

	function post_schedule(){
		var ajaxParam = {
			'date'  : $('#txt_sched_date').val(),
			'sched_type' : $('#sel_sched_type').val(),
			'REFERENCE' : $('#sel_reference').val(),
			'TYPE' : $('#sel_type').val(),
			'stop_date' : $('#txt_stop_date').val(),
			'is_active' : $('#sel_status').val(),
			'id_scheduler' : ID_SCHEDULER,
			'remarks' : $('#txt_remarks').val()
		};

		$.ajax({
			'type'       :       'POST',
			'url'        :       '/scheduler/post',
			'data'		 :       ajaxParam,
			beforeSend   :       function(){
				show_loader();
			},
			success      :       function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text : '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 3000
					});
				}else if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: response.message,
						text : '',
						text: '',
						icon: 'success',
						showCancelButton : true,
						cancelButtonText: 'Close',
						showDenyButton: false,
						showConfirmButton : false,   
						timer : 2500  
					}).then((result) => {
						window.location = `/scheduler/view/${response.ID_SCHEDULER}`;
					});
				}
			},error: function(xhr, status, error) {
				hide_loader()
				var errorMessage = xhr.status + ': ' + xhr.statusText;
				Swal.fire({
					title: "Error-" + errorMessage,
					text: '',
					icon: 'warning',
					confirmButtonText: 'OK',
					confirmButtonColor: "#DD6B55"
				});
			}
		});
		console.log({ajaxParam});
	}
</script>
@endpush