<style type="text/css">
	.form-row{
		margin-top: -5px !important;
	}
	label.lbl_gen{
		margin-bottom: -10px !important;
		font-size: 13px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	select{
		padding-bottom: 0px !important;
	}
	.modal-header{
		padding: 10px;
	}
</style>
<div class="modal fade bd-example-modal-xl" id="modal-filtering">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Filtering Option</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_submit_filter">
				<div class="modal-body">				
					<div class="row">
						<?php
							$chk_sel_all = ($sel_all == 1)?'checked':'';
							$dis_account = ($sel_all == 1)?'disabled':'';
						?>
						<div class="col-sm-8">
							<div class="form-row">
								<div class="form-group col-md-8">
									<label for="sel_account" class="lbl_gen">Account</label>
									<select class="form-control select2" id="sel_account" name="account" required="" {{$dis_account}}> 
										@if($selected_account != null && $sel_all ==0)
											<option value="{{ $selected_account->tag_id }}">{{ $selected_account->tag_value }}</option>
										@endif
									</select>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="id_chk_sel_all" name="select_all" {{ $chk_sel_all }}>
										<label class="form-check-label" for="id_chk_sel_all">
											Select All Accounts
										</label>
									</div>
								</div>
							</div>
							<br>
							<div class="form-row">
								<div class="form-group col-md-8">
									<label for="sel_date_filter" class="lbl_gen">Billing Date Filter Type</label>
									<select class="form-control" id="sel_billing">
										<option value="2">Billing Month</option>
										<option value="1">Billing Date Period</option>
									</select>
								</div>
							</div>
							<div id="bill_filter_fields">
								<div class="form-row" style="margin-top:10px !important" id="div_bill_date">
									<div class="form-group col-md-4">									
										<input type="date" class="form-control" id="txt_date_from" value="{{$billing_start}}"/>
									</div>
									<div class="form-group col-md-4">
										<input type="date" class="form-control" id="txt_date_to" value="{{$billing_end}}"/>
									</div>
								</div>
								<div class="form-row" style="margin-top:10px !important" id="div_bill_month">
									<div class="form-group col-md-4">
										<select class="form-control" id="sel_bill_month">
											@foreach($months as $c=>$month)
												<?php $selected = ($c+1 == $sel_bill_month)?"selected":""; ?>
												<option value="{{$c+1}}" {{$selected}}>{{$month}}</option>
											@endforeach
											
										</select>
									</div>
									<div class="form-group col-md-4">
										<select class="form-control" id="sel_bill_year">
											@foreach($years as $year)
												<?php $selected_year = ($year == $sel_bill_year)?"selected":"";?>
												<option value="{{$year}}" {{$selected_year}}>{{$year}}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="float-right">
						<button type="submit" class="btn btn-primary">Search</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</form>

		</div>
	</div>
</div>

@push('scripts')
<script type="text/javascript">
	var div_bill_date = $('#div_bill_date').detach();
	var div_bill_month = $('#div_bill_month').detach();

	$(document).ready(function(){
		$('#sel_billing').val('<?php echo $sel_billing ?>');
		if('<?php echo $sel_billing ?>' == 1){
			$('#bill_filter_fields').html(div_bill_date);
		}else{
			$('#bill_filter_fields').html(div_bill_month);
			$('#sel_bill_month').val('<?php echo $sel_bill_month ?>');
			$('#sel_bill_year').val('<?php echo $sel_bill_year ?>');
		}
	})
	
	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});
	$(document).on('change','#sel_billing',function(){
		var val = $(this).val();
		switch(val){
			case "1":
				$('#bill_filter_fields').html(div_bill_date);
				break;
			case "2":
				$('#bill_filter_fields').html(div_bill_month);
				break;
			default:
		}
		console.log({val});
	})
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
	$(document).on('click','#id_chk_sel_all',function(){
		var checked = $(this).prop('checked');
		if(checked){
			$('#sel_account').attr('disabled',true);
		}else{
			$('#sel_account').attr('disabled',false);
			// alert("not checked");
		}
	})
	$('#frm_submit_filter').submit(function(e){
		e.preventDefault();
		var chk  = ($('#id_chk_sel_all').prop('checked'))?1:0;
		var param_data = {
			'account_no'  : $('#sel_account').val(),
			'sel_all' : chk,
			'sel_billing' : $('#sel_billing').val(),
			'billing_start' : $('#txt_date_from').val(),
			'billing_end' : $('#txt_date_to').val(),
			'sel_bill_month' : $('#sel_bill_month').val(),
			'sel_bill_year' : $('#sel_bill_year').val()
		};
		window.location = "/admin/soa/index?"+$.param(param_data);
		console.log($.param(param_data));
	})
</script>
@endpush