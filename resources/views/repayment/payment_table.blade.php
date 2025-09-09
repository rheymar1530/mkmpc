<h5>Loan Payments</h5>
<?php
	$input_mode = [
		2=>"Default",
		1=>"Manual",
		
	];

	$selected_in = $repayment_transaction->input_mode ?? 2;
?>
<select class="form-control col-md-3" id="sel_input_mode">
	@foreach($input_mode as $val=>$desc)
	<option value="{{$val}}" <?php echo($val==$selected_in)?"selected":""; ?>>{{$desc}}</option>
	@endforeach
</select>
<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
	<table class="table table-bordered table-stripped tbl-inputs tbl_loans" style="white-space: nowrap;">
		<thead>
			<tr>
				<th class="table_header_dblue" width="5%"></th>
				<th class="table_header_dblue">Loan Dues</th>
				<!-- <th class="table_header_dblue">Principal Balance</th> -->
				<th class="table_header_dblue sec_amt_paid">Amount Paid</th>
				<th class="table_header_dblue sec_principal">Principal</th>
				<th class="table_header_dblue sec_interest">Interest</th>
				<th class="table_header_dblue sec_fees">Fees</th>
				<th class="table_header_dblue">Penalty/Surcharges</th>
				<th class="table_header_dblue">Rebates</th>
				<th class="table_header_dblue">Total Payment</th>
			</tr>
		</thead>
		<tbody id="loan_payment_body"></tbody>
		<tfoot>
			<tr class="foot_loan">
				<td class="tbl-inputs-text text_bold" colspan="2">TOTAL</td>
				<td class="class_amount text_bold tbl-inputs-text sec_amt_paid" id="txt_pay_amt_paid" style="padding-right:12px !important"></td>
				<td class="class_amount text_bold tbl-inputs-text sec_principal" id="txt_pay_principal" style="padding-right:12px !important">0.00</td>
				<td class="class_amount text_bold tbl-inputs-text sec_interest" id="txt_pay_interest" style="padding-right:12px !important"></td>
				<td class="class_amount text_bold tbl-inputs-text sec_fees" id="txt_pay_fees" style="padding-right:12px !important"></td>
				<td class="class_amount text_bold tbl-inputs-text" id="txt_pay_sur" style="padding-right:12px !important"></td>
				<td class="class_amount text_bold tbl-inputs-text" id="txt_rebates_total" style="padding-right:12px !important"></td>
				<td class="class_amount text_bold tbl-inputs-text" id="txt_pay_total" style="padding-right:12px !important"></td>
			</tr>
		</tfoot>
	</table>    
</div>  
@push('scripts')
<script type="text/javascript">
	function initialize_input_mode(load){
		var input_mode = $('#sel_input_mode').val();
		if(input_mode == 1){ //Manual
			$('.sec_principal,.sec_interest,.sec_fees').show();
			$('.sec_amt_paid').hide();
		}else{
			$('.sec_principal,.sec_interest,.sec_fees').hide();
			$('.sec_amt_paid').show();
		}

		$('tr.row_loan_dues').each(function(){
			compute_payment_row($(this),load);
		})
	}
	$('#sel_input_mode').change(function(){
		initialize_input_mode();
	})
	$(function() {
	    $.contextMenu({
	        selector: '.row_loan_dues',
	        callback: function(key, options) {
	            var m = "clicked: " + key;
				var repayment_token = $(this).attr('data-code');
				console.log({repayment_token})
				if(key == "loan_balance"){
					fill_loan_bal($(this),1);
				}else if(key == "current"){
					fill_due_row($(this),1);
				}else if(key == "half_payment"){
					fill_due_row($(this),2);
				}else if(key == "remove_pay"){
					fill_loan_bal($(this),2);
				}else if(key == "total_due_payment"){
					fill_due_row($(this),3);
				}
	        },
	        items: {
	        	"current": {name: "Pay Current Due"},
	        	"half_payment": {name: "Pay Current Due (Half Payment)"},
	        	"total_due_payment": {name: "Pay Total Due"},
	        	"loan_balance": {name: "Pay Loan Balance"},
	        	
	        	"sep1": "---------",
	        	"remove_pay": {name: "Remove Payment"},
	            "sep2": "---------",
	            "quit": {name: "Close", icon: "fas fa-times" }
	        }
	    });   
	});

	function fill_loan_bal(obj,type){
		var parent_row = $(obj);

		parent_row.find('.txt_loan_payment_amount').each(function(){
			if($(this).is(':visible')){
				if(!$(this).attr('disabled') && !$(this).hasClass('txt_sur')){
					var value ='';
					if(type == 1){
						value = number_format($(this).attr('loan-balance'),2);
					}
					$(this).val(value);
					compute_payment_row($(this));
					computeAll();
				}				
			}

		})

	}
	function fill_due_row(obj,type){
		var parent_row = $(obj);

		parent_row.find('.txt_loan_payment_amount').each(function(){
			if($(this).is(':visible')){
				if(!$(this).attr('disabled') && !$(this).hasClass('txt_sur')){
					// var value = parseFloat($(this).attr('total-due'))/type;
					if(type <= 2){
						var value = parseFloat($(this).attr('due'))/type;
					}else{
						var value = parseFloat($(this).attr('total-due'));
					}
					
					$(this).val(number_format(value,2));
					compute_payment_row($(this));
					computeAll();
				}				
			}

		})
	}
</script>
@endpush