<div class="col-md-12">
	<div class="card c-border">
		<div class="card-body" style="padding-bottom: 0px !important;">
			<div class="table-responsive" style="max-height: calc(100vh - 300px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
				<table class="table table-bordered table-stripped table-head-fixed tbl_entry" style="white-space: nowrap;">
					<thead>
						<tr>
							<th class="table_header_dblue"  width="20px"></th>
							<th class="table_header_dblue" width="{{$header_width}}">Account Code</th>
							<th class="table_header_dblue" width="350px">Description</th>
							<th class="table_header_dblue" width="120px">Debit</th>
							<th class="table_header_dblue" width="120px">Credit</th>
							<th class="table_header_dblue" width="300px">Details</th>
							<th class="table_header_dblue" width="30px"></th>
						</tr>
					</thead>
					<tbody id="entry_body">
						<?php
						$total_debit = 0;
						$total_credit = 0;
						?>
						<tr class="entry_row" id="row_paymode">
							<td class="td_counter">1</td>
							<td>
								<select class="form-control w_border p-0 select2 sel_account_code sel_chart col_entry_entry sel_entry_paymode" id="sel_paymode_account_code">
									@foreach($charts as $chart)
									<option value="{{$chart->id_chart_account}}" data-key="{{$chart->account_code}}">{{$chart->account_code}} - {{$chart->description}}</option>
									@endforeach
								</select>
							</td>
							<td><input class="col_entry_entry form-control w_border txt_description  p-0" value="" readonly></td>

							<td><input class="col_entry_entry form-control w_border class_amount txt_debit" value=""></td>
							<td><input class="col_entry_entry form-control w_border class_amount txt_credit" value=""></td>
							<td><input class="col_jv_entry form-control w_border txt_details" value=""></td>
							<td>
								@if($allow_post)
								<a class="btn btn-xs bg-gradient-danger2 btn_delete_entry" onclick="remove_entry(this)"><i class="fa fa-trash"></i></a>
								@endif
							</td>
						</tr>
					</tbody>
					<tbody id="paymode_body">
						
					</tbody>
					<tfoot>
						<tr>
							<th class="footer_fix font-total" colspan="3" style="text-align:center;background: #808080">T&nbsp;&nbsp;O&nbsp;&nbsp;T&nbsp;&nbsp;A&nbsp;&nbsp;L</th>
							<th class="footer_fix class_amount font-total" style="padding-right:12px;background: #808080" id="td_tot_debit">{{number_format($total_debit,2)}}</th>
							<th class="footer_fix class_amount font-total" style="padding-right:12px;background: #808080" id="td_tot_credit">{{number_format($total_credit,2)}}</th>
							<th class="footer_fix" colspan="2" style="background: #808080"></th>
						</tr>
					</tfoot>
				</table>    
			</div> 

		</div>
		@if($allow_post)
		<div class="card-footer">
			<button type="button" class="btn btn-xs bg-gradient-success2 col-md-12" onclick="add_entry()" style="margin-top:-15px"><i class="fa fa-plus"></i> Add {{$title_mod}}</button>
		</div>
		@endif
	</div>
</div>
@include('cash_disbursement.attachment')
@push('scripts')
<script type="text/javascript">
	$(document).on('keyup','.txt_debit,.txt_credit,#txt_amount',function(){
		set_entry_total()
	})
	$(document).on('change','.sel_account_code',function(e,wasTriggered){
		// if(!wasTriggered){
		// 	fill_description_v_code($(this));
		// }

		var id = $(this).attr("id");
		var val = parseInt($(this).val());
		$(this).closest('tr.entry_row').find('.txt_description').val(chart_option[val]['description'])
		$('span#select2-'+id+'-container').text(chart_option[val]['account_code'])
		set_entry_total()
	})
	function set_entry_total(){
		var $credit = 0;
		var $debit = 0;
		var in_amount = $('#txt_amount').val();
		if(in_amount == ""){
			in_amount = 0;
		}else{
			in_amount = ($.isNumeric(in_amount))?parseFloat(in_amount):decode_number_format(in_amount);
		}

		$('tr.entry_row').each(function(){
			var parent_row = $(this);
			var id_chart_account = $(this).find('.sel_account_code').val();

			if(id_chart_account != null){
				var deb = parent_row.find('.txt_debit').val();
				deb = (deb=='')?0:deb;
				deb = (!$.isNumeric(deb))?decode_number_format(deb):parseFloat(deb);
				$debit+= deb;

				var cred = parent_row.find('.txt_credit').val();
				cred = (cred=='')?0:cred;
				cred = (!$.isNumeric(cred))?decode_number_format(cred):parseFloat(cred);
				$credit+= cred;
			}
			
		})
		$debit = (isNaN($debit))?0:$debit;
		$credit = (isNaN($credit))?0:$credit;

		var $valid = true;
		var $message = "";

		$('#td_tot_debit').text(number_format($debit,2));
		$('#td_tot_credit').text(number_format($credit,2));


		$credit = parseFloat($credit.toFixed(2));
		$debit = parseFloat($debit.toFixed(2));
		in_amount = parseFloat(in_amount.toFixed(2));

	

		if($credit != $debit || $credit != in_amount || $debit != in_amount){
			$('#td_tot_debit').addClass("not_balance_amount")
			$('#td_tot_credit').addClass("not_balance_amount")
			alert("PAK");
			$valid = false;
			$message = "Total amount is not balance";
		}else{
			$('#td_tot_debit').removeClass("not_balance_amount")
			$('#td_tot_credit').removeClass("not_balance_amount")
		}


		var output = {};
		output['debit'] = $debit;
		output['credit'] = $credit;
		output['in_amount'] = in_amount;
		output['valid'] = $valid;
		output['message'] = $message;
		console.log({$debit,$credit});

		return output;
	}
</script>

@if($allow_post)
<script type="text/javascript">

	$('#frm_post_entry').submit(function(e){
		e.preventDefault();
		var validation_row = [];
		$('.mandatory').removeClass('mandatory')
		$('tr.entry_row').each(function(){
			var parent_row = $(this);
			var id_chart_account = $(this).find('.sel_account_code').val();
			var temp = {};

			$deb = decode_number_format(parent_row.find('.txt_debit').val());
			$cred = decode_number_format(parent_row.find('.txt_credit').val());

			if(($deb == 0 && $cred == 0) || (id_chart_account == null) || ($deb > 0 && $cred > 0)){
				validation_row.push(parent_row);
			}
		})
		if(validation_row.length > 0 || $('tr.entry_row').length < 2){
			for(var $i=0;$i<validation_row.length;$i++){
				$(validation_row[$i]).find('input,.select2-selection').addClass('mandatory');
			}
			Swal.fire({
				title: "Invalid Entry",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 2500
			});
			return;
		}

		var totals = set_entry_total();
		if(totals['credit'] == 0 || totals['debit'] == 0){
			Swal.fire({
				title: "Invalid Amount",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 2500
			});

			return;
		}
		if(!totals['valid']){
			Swal.fire({
				title: "Total amount is not balance",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 2500
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
	function parseEntryRow(){
		var entry_account = [];
		$('tr.entry_row').each(function(){
			var temp ={};
			temp['id_chart_account'] = $(this).find('.sel_account_code').val();
			temp['credit'] = checkNan(decode_number_format($(this).find('.txt_credit').val()));
			temp['debit'] = checkNan(decode_number_format($(this).find('.txt_debit').val()));
			temp['remarks'] = $(this).find('.txt_details').val();
			entry_account.push(temp);
		})

		return entry_account;
	}
	function checkNan($in){
		return isNaN($in)?0:$in;
	}
</script>
@endif
@endpush