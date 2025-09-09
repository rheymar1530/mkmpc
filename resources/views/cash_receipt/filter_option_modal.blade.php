
<style type="text/css">
	.modal-p-50 {
		max-width: 50% !important;
		min-width: 50% !important;
		margin: auto;
	}
	.filter_label{
		margin-top: 8px;
	}
</style>
<?php
$filter_type_selection = [
	1 => "Date Received",
	2 => "Date Created",
	3 => "OR No"
];

$receive_from = [
	1=>"**ALL**",
	2=>"Member",
	3=>"Non-member"
];
?>
<div id="view_options" class="modal fade"  role="dialog" aria-hidden="false">
	<div class="modal-dialog  modal-p-50" role="document" style="width: 50%">
		<form id="filter_target">
			<div class="modal-content " style="">
				<div class="modal-header panel_header">
					<h4 class="modal-title lbl_header"><i class="fa fa-eye"></i> View Options</h4>

					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body" style="max-height: calc(100vh - 210px);
				overflow-y: auto;overflow-x: auto">    
				<div class="form-horizontal" id="submit_filter">   

					<div class="form-group row">
						<label for="sel_filter_type" class="col-md-2 control-label filter_label" style="text-align: left">Filter Type</label>
						<div class="col-md-4">
							<?php $selected_filter_type = request()->get('filter_type') ?? 1 ?>
							<select class="form-control p-0" id="sel_filter_type" name="filter_type">
								@foreach($filter_type_selection as $val=>$desc)
								<option value="{{$val}}" <?php echo ($selected_filter_type == $val)?"selected":""; ?> >{{$desc}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div id="fil_form_date">
						<div class="form-group row">
							<label for="txt_date_received_filter" class="col-md-2 control-label filter_label" style="text-align: left">Date</label>
							<div class="col-md-4">
								<input type="date" title="Date" required="" class="form-control opt_sel form-in-text" id="txt_fil_date_from"  value="{{$date_start}}" name="date_start">
							</div>
							<div class="col-md-4">
								<input type="date" title="Date" required="" class="form-control opt_sel form-in-text" id="txt_fil_date_to"  value="{{$date_end}}" name="date_end">
							</div>
						</div>
						<div class="form-group row" id="div_rec_from">
							<label for="txt_date_received_filter" class="col-md-2 control-label filter_label" style="text-align: left">Received from</label>
							<div class="col-md-3">
								<select class="form-control p-0" name="receive_from" id="receive_from">
									<?php $selected_receive_from = request()->get('receive_from') ?? 1 ?>
									@foreach($receive_from as $k=>$r)
										<option value="{{$k}}" <?php echo ($k == $selected_receive_from)?"selected":""; ?> >{{$r}}</option>
									@endforeach
									
								</select>
							</div>

						</div>
					</div>
					<div class="form-group row" id="fil_form_or">
						<label for="txt_date_received_filter" class="col-md-2 control-label filter_label" style="text-align: left">OR No.</label>
						<div class="col-md-4">
							<input type="text" title="OR No" required="" class="form-control opt_sel form-in-text" id="txt_fil_or"  value="{{$or_no}}" name="or_no">
						</div>
					</div>
					<div id="fil_form"></div>				
				</div>
			</div>
			<div class="modal-footer modal_body">
				<button type="submit" class="btn btn-md  bg-gradient-primary2 but"><i class="fa fa-search"></i>&nbsp;&nbsp;Search</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-dialog -->
	</form>
</div>
</div>
@push('scripts')
<script type="text/javascript">
	var $date_form = $('#fil_form_date').detach();
	var $or_form = $('#fil_form_or').detach();
	var $selection = jQuery.parseJSON('<?php echo json_encode($member_selected ?? [])?>');
	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});	
	initialize_filter();
	initialize_select_member();
	function initialize_filter(){
		var filter_type = $('#sel_filter_type').val();

		if(filter_type == 1 || filter_type == 2){ // Date received and created
			$('#fil_form').html($date_form);
		}else if(filter_type == 3){ // Or no
			$('#fil_form').html($or_form);
		}
		console.log({filter_type});
	}
	$(document).on("change","#sel_filter_type",function(){
		initialize_filter();
	})

	function intialize_select2(){		
		$("#sel_id_member").select2({
			minimumInputLength: 2,
			width: '80%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: '/search_member',
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
	}
	$(document).on('change','#receive_from',function(){
		initialize_select_member()
	})
	function initialize_select_member(){
		var val = $('#receive_from').val();

		if(val == 2){ // if member
			

			console.log({$selection});
			$('#div_rec_from').append(`<div class="col-md-7" id="div_sel_id_member">
								<select id="sel_id_member" class="form-control p-0" name="id_member" required></select>
							</div>`);

			if(Object.keys($selection).length > 0){
				$('#sel_id_member').html("<option value='"+$selection.tag_id+"'>"+$selection.tag_value+"</option>")
			}
			intialize_select2();
		}else{
			$('#div_sel_id_member').remove();
		}
	}

</script>
@endpush

