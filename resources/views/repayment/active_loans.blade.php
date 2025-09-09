
<style type="text/css">
	.row_active_loans td{
		padding: 2px !important;
	}
	.border_r{
		/*border-right: 1px solid black !important;*/
		font-weight: bold;
	}
	.highlightBg, .highlightBg input{
		background: #99ff6699;
	}
	.bg-gradient-warning2 {
	    background: #203764 !important;
	    color: white;
	}
	.bg-gradient-success2 {
	    background: #33cc33 !important;
	    color: white;
	}
	
</style>
<h5>Loan Summary</h5>
<!-- <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto"> -->

<div class="table-responsive" style="margin-top: 5px !important">
	<table class="table table-bordered table-stripped table-head-fixed tbl-inputs tbl_loans" style="white-space: nowrap;">
		<thead>
			<tr>
				<th colspan="3" style="text-align:center;" class="bg-gradient-primary2">Loan Details</th>
				<th colspan="3" style="text-align:center;" class="bg-gradient-primary2">Amortization Sched</th>
				<th colspan="3" style="text-align:center;" class="bg-gradient-success2">Loan Balance</th>
				<th colspan="4" style="text-align:center;" class="bg-gradient-warning2">Total Dues</th>
			</tr>

			<tr>
				<th class="bg-gradient-primary2" width="5%"></th>
				<th class="bg-gradient-primary2">Loan Service</th>
				<th class="bg-gradient-primary2">Date Granted</th>

				<th class="bg-gradient-primary2">Principal</th>
				<th class="bg-gradient-primary2">Interest</th>
				<th class="bg-gradient-primary2">Total</th>



				
				
				<th class="bg-gradient-success2">Principal </th>
				<th class="bg-gradient-success2">Interest</th>
				<th class="bg-gradient-success2">Total</th>

				<th class="bg-gradient-warning2">Principal</th>
				<th class="bg-gradient-warning2">Interest</th>
				<th class="bg-gradient-warning2">Fees</th>
				<th class="bg-gradient-warning2">Total</th>				
			</tr>
		</thead>
		<tbody id="active_loans_body">


		</tbody>
	</table>    
</div>  


@push('scripts')
<script type="text/javascript">
	let loan_balances = {};
	function draw_active_loan($active_loans,load){
		loan_balances = {};
		var out = '';
		var out_payments = '';
		var show_fees = false;
		var total_prin_bal = 0,total_prin=0,total_in=0,total_fees=0,total_loan_bal=0,total_in_bal=0,total_prin_amt=0,total_int_amt=0;
		var total_loan_due_t = 0;
		$.each($active_loans,function(i,item){
			var principal = parseFloat(item.previous_p)+parseFloat(item.current_p);
			var interest = parseFloat(item.previous_i)+parseFloat(item.current_i);

			var interest_g = interest + parseFloat(item.onwards_i);
			var fees = parseFloat(item.previous_f)+parseFloat(item.current_f);
			var total = principal+interest+fees;

			var loan_bal = parseFloat(item.principal_balance)+interest_g+fees;

			loan_balances[item.loan_token] = loan_bal;

			total_prin += principal;
			total_in += interest;
			total_fees += fees;

			total_prin_bal += (parseFloat(item.principal_balance));
			total_loan_due_t += total;
			total_loan_bal += loan_bal;
			total_in_bal += interest_g;

			total_prin_amt += parseFloat(item.principal_amt);
			total_int_amt += parseFloat(item.interest_amt);

			out += '<tr data-token="'+item.loan_token+'" class="row_active_loans row_loans">';
			out += '	<td style="text-align:center">'+(i+1)+'</td>';
			
			out += '	<td class="tbl-inputs-text">ID#<a href="/loan/application/approval/'+item.loan_token+'" target="_blank">'+item.id_loan+'</a> '+item.loan_name+' '+((item.overdue > 0)?('<span class="badge badge-danger">LAPSED MD - '+item.overdue+' Month(s)</span>'):'')+'</td>';

			out += '	<td class="">'+item.date_released+'</td>';
			out += '	<td class="class_amount border_r">'+number_format(item.principal_amt,2)	+'</td>';
			out += '	<td class="class_amount border_r">'+number_format(item.interest_amt,2)	+'</td>';
			out += '	<td class="class_amount border_r">'+number_format((parseFloat(item.interest_amt)+parseFloat(item.principal_amt)),2)	+'</td>';

			out += '	<td class="class_amount border_r">'+number_format(parseFloat(item.principal_balance),2)	+'</td>';

			out += '	<td class="class_amount border_r">'+number_format(interest_g,2)	+'</td>';
			out += '	<td class="class_amount border_r">'+number_format(loan_bal,2)	+'</td>';
			
			out += '	<td class="class_amount" title="Past : '+number_format(parseFloat(item.previous_p,2))+' || Current : '+number_format(parseFloat(item.current_p,2))+'">'+number_format(principal,2)	+'</td>';
			out += '	<td class="class_amount" title="Past : '+number_format(parseFloat(item.previous_i,2))+' || Current : '+number_format(parseFloat(item.current_i,2))+'">'+number_format(interest,2)	+'</td>';
			out += '	<td class="class_amount" title="Past : '+number_format(parseFloat(item.previous_f,2))+' || Current : '+number_format(parseFloat(item.current_f,2))+'">'+number_format(fees,2)	+'</td>';
			out += '	<td class="class_amount">'+number_format(total,2)+'</td>';
			out += '</tr>';


			/**********************PAYMENTS**************************/
			out_payments += '<tr class="row_loans row_loan_dues" data-token="'+item.loan_token+'">';
			out_payments += '	<td style="text-align:center">'+(i+1)+'</td>';
			// out_payments += '<td class="tbl-inputs-text">ID#<a href="/loan/application/approval/'+item.loan_token+'" target="_blank">'+item.id_loan+'</a> '+item.loan_name+'</td>';
			out_payments += '	<td class="tbl-inputs-text">ID#<a href="/loan/application/approval/'+item.loan_token+'" target="_blank">'+item.id_loan+'</a> '+item.loan_name+' '+((item.overdue > 0)?('<span class="badge badge-danger">LAPSED MD - '+item.overdue+' Month(s)</span>'):'')+'</td>';
			// out_payments += '<td><input type="text" class="form-control class_amount frm_loans" disabled value="'+number_format(item.principal_balance,2)+'"></td>';

			var amt_paid = parseFloat(item.current_p)+parseFloat(item.current_i)+parseFloat(item.current_f);
			var paid_amt = parseFloat(item.paid_principal)+parseFloat(item.paid_interest)+parseFloat(item.paid_fees);
			paid_amt = (paid_amt > 0)?number_format(paid_amt,2):'';

			/*************AMOUNT PAID SINGLE TEXTBOX*************/
			out_payments +=  '<td class="sec_amt_paid"><input type="text" name="" required class="form-control frm_loans txt_loan_payment_amount txt_amt_paid" key="amount" value="'+paid_amt+'" loan-token="'+item.loan_token+'" due="'+amt_paid+'" amount-type="amt_paid" placeholder="('+number_format(amt_paid,2)+')" title="'+number_format(amt_paid,2)+'" loan-balance="'+loan_bal+'" total-due="'+(principal+interest+fees)+'"></td>';

			/****************************************************/

			// PRINCIPAL
			var paid_principal = parseFloat(item.paid_principal);
			paid_principal = (paid_principal > 0)?number_format(paid_principal):'';
			out_payments +=  '<td class="sec_principal"><input type="text" name="" required class="form-control frm_loans txt_loan_payment_amount txt_prin" key="amount" value="'+paid_principal+'" loan-token="'+item.loan_token+'" due="'+item.current_p+'" amount-type="principal" placeholder="('+number_format(parseFloat(item.current_p),2)+')" title="'+number_format(parseFloat(item.current_p),2)+'" loan-balance="'+parseFloat(item.principal_balance)+'" total-due="'+(principal)+'"></td>';
			// INTEREST
			var disabled = (item.act_interest > 0)?"":"disabled";
			var place = (item.act_interest > 0)?(number_format(parseFloat(item.current_i),2)):"";
			var paid_interest = parseFloat(item.paid_interest);
			paid_interest = (paid_interest > 0)?number_format(paid_interest):'';
			out_payments +=  '<td class="sec_interest"><input type="text" name="" required class="form-control frm_loans txt_loan_payment_amount txt_in" key="amount" value="'+paid_interest+'" loan-token="'+item.loan_token+'" due="'+item.current_i+'" amount-type="interest" placeholder="'+place+'" '+disabled+' title="'+place+'" loan-balance="'+interest+'" total-due="'+(interest)+'"></td>';

			// FEES
			var paid_fees = parseFloat(item.paid_fees);
			paid_fees = (paid_fees > 0)?number_format(paid_fees):'';
			var disabled = (item.act_fees > 0)?"":"disabled";
			var place = (item.act_fees > 0)?(number_format(parseFloat(item.current_f),2)):"";

			show_fees = (item.act_fees > 0)?true:false;
			out_payments +=  '<td class="sec_fees"><input type="text" name="" required class="form-control frm_loans txt_loan_payment_amount txt_fees" key="amount" value="'+paid_fees+'" loan-token="'+item.loan_token+'" due="'+item.current_f+'" amount-type="txt_fees" placeholder="'+place+'" '+disabled+' title="'+place+'" loan-balance="'+fees+'" total-due="'+(fees)+'"></td>';
			//SURCHARGES

			var sur_amt = number_format(parseFloat(item.surcharges),2);
			out_payments += '<td><input type="text" class="form-control class_amount txt_loan_payment_amount frm_loans txt_sur" amount-type="surcharges" value="'+sur_amt+'" key="amount" loan-token="'+item.loan_token+'"></td>'

			//Rebates
			var sur_amt = number_format(parseFloat(item.surcharges),2);
			// out_payments += '<td><input type="text" class="form-control class_amount frm_loans txt_rebates" value="'+number_format(parseFloat(item.rebate_amount),2)+'" rebates-value="'+item.rebate_amount*-1+'" loan-token="'+item.loan_token+'" rebates-def="'+item.rebates+'" disabled></td>'
			out_payments += '<td><input type="text" class="form-control class_amount frm_loans txt_rebates" value="'+number_format(parseFloat((item.rebate_amount ?? 0)),2)+'" rebates-value="'+(item.rebate_amount ?? 0)*-1+'" loan-token="'+item.loan_token+'" rebates-def="'+(item.rebates ?? 0)+'" disabled></td>';


			//TOTAL
			out_payments += '<td><input type="text" class="form-control class_amount frm_loans total_loan_paid" disabled value=""></td>'		
		});

		out += 
			`<tr class="foot_loan">
			
				<td class="tbl-inputs-text text_bold" colspan="3">TOTAL</td>
				<td class="class_amount text_bold tbl-inputs-text" style="padding-right:5px !important">`+number_format(total_prin_amt,2)+`</td>
				<td class="class_amount text_bold tbl-inputs-text" style="padding-right:5px !important">`+number_format(total_int_amt,2)+`</td>
				<td class="class_amount text_bold tbl-inputs-text" style="padding-right:5px !important">`+number_format(total_prin_amt+total_int_amt,2)+`</td>
				<td class="class_amount text_bold tbl-inputs-text" style="padding-right:5px !important">`+number_format(total_prin_bal,2)+`</td>
				<td class="class_amount text_bold tbl-inputs-text" style="padding-right:5px !important">`+number_format(total_in_bal,2)+`</td>
				<td class="class_amount text_bold tbl-inputs-text" style="padding-right:5px !important">`+number_format(total_loan_bal,2)+`</td>
				
				<td class="class_amount text_bold tbl-inputs-text" style="padding-right:5px !important">`+number_format(total_prin,2)+`</td>
				<td class="class_amount text_bold tbl-inputs-text" style="padding-right:5px !important">`+number_format(total_in,2)+`</td>
				<td class="class_amount text_bold tbl-inputs-text" style="padding-right:5px !important">`+number_format(total_fees,2)+`</td>
				<td class="class_amount text_bold tbl-inputs-text" style="padding-right:5px !important">`+number_format(total_loan_due_t,2)+`</td>
			</tr>`;

		total_loan_due = total_loan_due_t;

		$('#active_loans_body').html(out);
		$('#loan_payment_body').html(out_payments);


		initialize_input_mode(load);

		if(!show_fees){
			$('.sec_fees').hide();
		}
	}
	var tr_class;

	// $(document).on('hover','tr.row_loans',	  function(){

	//     tr_class = $('this').attr('class');
	//     $(this).addClass('highlightBg');
	//     $('.table2 ' + tr_class).addClass('highlightBg');
	// }, 
	//   function(){
	//   	alert(321);
	//    $(this).removeClass('highlightBg');
	//    $('table2 ' + tr_class).removeClass('highlightBg');
	// })

	$(document).on('mouseenter','tr.row_loans',function(){
		var token = $(this).attr('data-token');
		$('tr[data-token="'+token+'"]').addClass('highlightBg');
		// $(this).addClass('highlightBg');
	}).on('mouseleave','tr.row_loans',function(){
		// $(this).removeClass('highlightBg');
		var token = $(this).attr('data-token');
		$('tr[data-token="'+token+'"]').removeClass('highlightBg');
	})


</script>
@endpush