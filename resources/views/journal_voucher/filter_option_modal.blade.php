
<style type="text/css">
	.modal-p-50 {
		max-width: 50% !important;
		min-width: 50% !important;
		margin: auto;
	}
	.filter_label{
		margin-top: 8px;
	}
	.font-bold{
		font-weight: bold;
	}
	.control-label{
		font-size: 15px;
	}
</style>
<?php

?>
<div id="view_options" class="modal fade"  role="dialog" aria-hidden="false">
	<div class="modal-dialog  modal-p-50" role="document" style="width: 50%">
		<form id="filter_target">
			<div class="modal-content " style="">
				<div class="modal-header panel_header">
					<h4 class="modal-title"><i class="fa fa-eye"></i> View Options</h4>

					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body" style="max-height: calc(100vh - 210px);
				overflow-y: auto;overflow-x: auto">    
				<div class="form-horizontal" id="submit_filter">   
					<div class="form-group row" style="margin-top:15px">
						<label class="radio col-sm-2">
							<input type="radio" name="optradio" value='1' <?php echo ($fil_type == 1)?"checked":""; ?> >
							<span class="control-label">Date</span>
						</label>
						<div class="col-sm-4">
							<input type="date" title="Date" required="" class="form-control opt_sel form-in-text option_1" id="txt_date_from_fil"  disabled="" value="{{$date_from}}">
						</div>
						<div class="col-sm-4">
							<input type="date" title="Date" required="" class="form-control opt_sel form-in-text option_1" id="txt_date_to_fil"  disabled="" value="{{$date_to}}">
						</div>
					</div>
					<div class="form-group row" style="margin-top:8px">
						<label class="radio col-sm-2">
							<input type="radio" name="optradio" value='2' <?php echo ($fil_type == 2)?"checked":""; ?>>
							<span class="control-label">JV #</span>
						</label>
						<div class="col-sm-4">
							<input type="text" title="Date" required="" class="form-control opt_sel form-in-text option_2" id="txt_jv_no_search"  disabled="" value="{{$jv_search}}">
						</div>

					</div>
				</div>
			</div>
			<div class="modal-footer modal_body">
				<button type="submit" class="btn btn-md  bg-gradient-primary but"><i class="fa fa-search"></i>&nbsp;&nbsp;Search</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-dialog -->
	</form>
</div>
</div>
@push('scripts')
<script type="text/javascript">
	initialize_filter()
	$('input[name=optradio]').change(function() {
		initialize_filter()
	});

	function initialize_filter(){
		var val = $('input[name=optradio]:checked').val()
		console.log({val})
		$('.opt_sel').attr('disabled',true);
		$('.option_'+val).attr('disabled',false);
	}
	// $(document).on('click','#chk_sel_member',function(){
	// 	var checked = $(this).prop('checked');
	// 	$('#sel_id_member').prop('disabled',!checked);
	// 	console.log({checked})
	// })
	$('#filter_target').submit(function(e){
		e.preventDefault();
		var filter_data = {};
		var val = $('input[name=optradio]:checked').val();
		filter_data['fil_type'] = val;
		if(val == 1){ // date
			filter_data['date_from'] = $('#txt_date_from_fil').val();
			filter_data['date_to'] = $('#txt_date_to_fil').val();
		}else if(val == 2){
			filter_data['jv_search'] = $('#txt_jv_no_search').val();
		}
		var location_reload = '/journal_voucher?'+$.param(filter_data);
		window.location = location_reload;
		console.log({location_reload});
	})



</script>
@endpush

