
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
					<div class="form-group row" style="margin-top:10px">
						<div class="col-md-2">
							<div class="form-check">
							  <label class="form-check-label font-bold" for="chk_date_type" style="margin-left:-18px">
							   	Date
							  </label>
							</div>
						</div>						
						<div class="col-md-4">
							<input type="date" title="Date" required="" class="form-control opt_sel form-in-text fil_date" id="txt_fil_date_from"  value="{{$start_date}}" name="date_start">
						</div>
						<div class="col-md-4">
							<input type="date" title="Date" required="" class="form-control opt_sel form-in-text fil_date" id="txt_fil_date_to"  value="{{$end_date}}" name="date_end">
						</div>
					</div>
					<div class="form-group row">
						<?php
							$checked_payee = ($selected_id_member > 0)?"checked":"";
							$disabled_payee = ($selected_id_member > 0)?"":"disabled";
						?>
						<div class="col-md-2">
							<div class="form-check">
							  <input class="form-check-input" type="checkbox" value="" id="chk_sel_member" <?php echo $checked_payee ?> >
							  <label class="form-check-label font-bold" for="chk_sel_member" >Payee</label> 
							</div>
						</div>
						<div class="col-md-9" id="div_sel_id_member">
							<select id="sel_id_member" class="form-control p-0" name="id_member" required  <?php echo $disabled_payee ?> >
								@if(isset($selected_member))
									<option value="{{$selected_member->id_member}}">{{$selected_member->member_name}}</option>
								@endif

							</select>
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

	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});	

	$('#sel_loan_service').select2()
	intialize_select2()



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
	$(document).on('click','#chk_sel_member',function(){
		var checked = $(this).prop('checked');
		$('#sel_id_member').prop('disabled',!checked);
		console.log({checked})
	})
	$('#filter_target').submit(function(e){
		e.preventDefault();
		var filter_data = {};
		filter_data['start_date'] = $('#txt_fil_date_from').val();
		filter_data['end_date'] = $('#txt_fil_date_to').val();

		//MEMBER
		if($('#chk_sel_member').prop('checked')){
			var id_member = $('#sel_id_member').val();
			filter_data['id_member'] = id_member;
		}

		var location_reload = '/change?'+$.param(filter_data);
		window.location = location_reload;
		console.log({location_reload});
	})



</script>
@endpush

