@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">


	#myBtn {
		display: none;
		position: fixed;
		bottom: 20px;
		left: 260px;
		z-index: 99;
		font-size: 18px;
		border: solid #006bb3;
		border-radius: 50%;
		outline: none;
		background-color: #4db8ff;
		color: white;
		cursor: pointer;
		height: 70px;
		width: 70px;
		/*padding: 20px 20px 20px 20px;*/
		/*border-radius: 4px;*/
	}

	#myBtn:hover {
	  background-color: #006bb366;
	}
	.borderless td, .borderless th {
	    border: none;
	}
	.tbl_soa_details  tr>td,.tbl_soa_details  tr>th,
	.tbl_transactions  tr>td,.tbl_transactions  tr>th{
		padding:0px;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 14px !important;
	}
	#tbl_summary_amount  tr>td,#tbl_summary_amount  tr>th{
		padding:0.5px;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 14px !important;
	}

	.header_color{
		background: #00264d ;
		color :white;
	}
	.dark-mode .header_color{
		background: #3498db ;
		color :white;
	}
	
	.details_tbl_lbl{
		/*text-align: right !important;*/
		width: 30% !important;
	}


	.tbl_transactions {
 		 border: 1px solid #bfbfbf;
	}

	.tbl_transactions thead th {
	  border-top: 1px solid #bfbfbf!important;
	  border-bottom: 1px solid #bfbfbf!important;
	  border-left: 1px solid #bfbfbf;
	  border-right: 1px solid #bfbfbf;
	}

	.tbl_transactions td {
	  border-left: 1px solid #bfbfbf;
	  border-right: 1px solid #bfbfbf;
	  border-top: none!important;
	}
	.col_amount{
		text-align: right;
	}
	.tbl_transactions thead th {
		background: #00264d ;
		color :white;
		position: -webkit-sticky;
		position: sticky;
		top: 0;
	}
	.dark-mode .tbl_transactions thead th {
		background: #3498db ;
		color :white;
		position: -webkit-sticky;
		position: sticky;
		top: 0;
	}
	.tbl_transactions tfoot,
	.tbl_transactions tfoot th,
	.tbl_transactions tfoot td {
		position: -webkit-sticky;
		position: sticky;
		bottom: 0;
		background: #666;
		color: #fff;
		z-index:4;
	}
	.group_padding{
	  padding-left: 20px !important;
	}
	.bold_lbl{
		font-weight: bold;
	}
	.dbl_undline{

		text-decoration-line: underline;
		text-decoration-style: double;

	}
	.subtotal_border{
  		border-bottom: 2px solid;
	}
</style>
<div class="container-fluid">
	<div class="card">
		<div class="card-body">
	<div class="row">
		<div class="col-md-12">
			<h3><u>Statement of Account</u></h3>
			<div class="form-row">
				<div class="col-md-6">
					<div class="right-left">
						<table class="table borderless tbl_soa_details" style="width: 70%">
						    <thead>
						      <tr class="header_color">
						        <th>Bill to</th>
						      </tr>
						    </thead>
						    <tbody>
								<tr>
									<td class="bold_lbl">{{ $details->name }}</td>
								</tr>
						      <tr>
						        <td>{{ $details->address }}</td>
						      </tr>
						      	<tr>
									<td>TIN - {{ $details->tin }}</td>
								</tr>
						      	<tr>
									<td></td>
								</tr>
						      	<tr>
									<td></td>
								</tr>
						      	<tr>
									<td></td>
								</tr>

						    </tbody>
					 	</table>
					</div>
				</div>
				<div class="col-md-6 ">
					<div class="float-right" style="width: 60% !important">
						<table class="table borderless tbl_soa_details" style="white-space: nowrap" >
						    <thead>
						      <tr class="header_color">
						        <th colspan="2">SOA Details</th>
						      </tr>
						    </thead>
						    <tbody>
								<tr>
									<th class="details_tbl_lbl">Control No.:</th>
									<td>&nbsp;&nbsp;{{ $details->control_number }}</td>
								</tr>
								<tr>
									<th class="details_tbl_lbl">Account No.:</th>
									<td>&nbsp;&nbsp;{{ $details->account_no }}</td>
								</tr>
								<tr>
									<th class="details_tbl_lbl">Billing Period:</th>
									<td>&nbsp;&nbsp;{{ $details->billing_period }}</td>
								</tr>
								<tr>
									<th class="details_tbl_lbl">Statement Date:</th>
									<td>&nbsp;&nbsp;{{ $details->statement_date }} </td>
								</tr>
								<tr>
									<th class="details_tbl_lbl">Amount Due:</th>
									<td id="td_amount_due">&nbsp;&nbsp;{{ number_format($amount_due,2) }}</td>
								</tr>
								<tr>
									<th class="details_tbl_lbl">Payment Due Date</th>
									<td>&nbsp;&nbsp;{{ $details->due_date }}</td>
								</tr>
								<!-- <tr>
									<td>Account No :42157</td>
								</tr>
						      <tr>
						        <td>Km. 18 West Service Road, South Luzon Expressway</td>
						      </tr> -->
						    </tbody>
					 	</table>
					</div>
				</div>
				<div class="col-md-12">
					<button class="btn btn-danger" onclick="export_soa()">Export & Print</button>
					<div class="table-responsive" style="max-height: calc(100vh - 150px);overflow-y: auto;margin-top: 10px">
						  <table class="table tbl_transactions table-striped">
						    <thead>
						    	<?php
						    		$current_total = 0;
						    	?>
						      <tr class="header_color">
						        <th scope="col">Date</th>
						        <th scope="col">Tracking No.</th>
						        <th scope="col">Destination</th>
						        <th scope="col">Description</th>
						        <th scope="col">Date & Time Received</th>
						        <th scope="col">Received By</th>
						        <th scope="col">Amount</th>
						      </tr>
						    </thead>
						    <tbody>
						      @foreach($transactions as $cc=>$group)
								@if(count($transactions) >1 || (count($transactions) ==1 && $cc != ""))
									<?php $group_padding = "group_padding" ?>
									<tr>
									<th colspan="7">{{ $cc }}</th>
									</tr>
								@endif
									<?php
             							$sub_total = 0;
            						?>
							      @foreach($group as $item)

								      <tr>
								      	<td class="{{$group_padding ?? ''}}">{{$item->transaction_date}}</td>
								      	<td>{{$item->hawb_no}}</td>
								      	<td>{{$item->destination}}</td>
								      	<td>{{$item->description}}</td>
								      	<td>{{$item->date_time_received}}</td>
								      	<td>{{$item->received_by}}</td>
								      	<td class="col_amount">{{number_format($item->total,2)}}</td>
								      </tr>
							      	<?php $current_total += $item->total; $sub_total += $item->total;?>
							      @endforeach
									@if(count($transactions) > 1)
						            <tr>
						              <th class="subtotal_border">Subtotal</th>
						              <th colspan="6" style="text-align: right" class="subtotal_border">{{number_format($sub_total,2)}}</th>
						            </tr>
						            @endif
						      @endforeach

						    </tbody>
						    <tfoot>
						    	<tr>
						    		<th colspan="6">Current Total</th>
						    		<th class="col_amount">{{number_format($current_total,2)}}</th>
						    	</tr>
						    </tfoot>
						  </table>
					</div>
				</div>
				<div class="col-md-12" style="margin-top: 20px;">
					<?php
						$taxable_amt = $current_total/1.12;
						$vat_amount = $current_total - $taxable_amt;
						$prev_amount_due = $previous_amt_due;
					?>
					<table class="table borderless col-sm-10" id="tbl_summary_amount">
					    <tbody>
							<tr>
								<th class="group_padding" width="35%">Current Charges:</th>
								<td></td>
								<th class="col_amount">{{number_format($current_total,2)}}</th>

							</tr>
							<tr>
								<td class="group_padding" colspan="3">Taxable Amount  : {{number_format($taxable_amt,2)}}</td>
							</tr>
							<tr>
								<td class="group_padding" colspan="3">VAT Amount : {{ number_format($vat_amount,2) }}</td>
								
							</tr>
<!-- 					        <tr>
					      		<th class="group_padding">Taxable Amount : </th>
					        	<td class="col_amount">{{ number_format($taxable_amt,2) }}</td>
					        </tr>
					      	<tr>
					      		<th class="group_padding">VAT Amount (12%) : </th>
					        	<td class="col_amount">{{ number_format($vat_amount,2) }}</td>
					        </tr> -->
					        <tr>
					        	<th colspan="3">&nbsp;</th>
					        </tr>
					        <tr>
					        	<th colspan="3">Balance Forwarded</th>
					        </tr>
					      	<tr>
								<th class="group_padding">Previous Amount Due</th>
								<td></td>
								<th class="col_amount">{{ number_format($previous_amt_due,2) }}</th>
							</tr>
					        <tr>
					      		<th class="group_padding">Less: Payment  </th>
					        	<!-- <td class="col_amount">0.00</td> -->
					        	<td class="col_amount">
					        		<?php $less_payment = 0;?>
					        		@if(count($payments) > 0)
						        		<table class="tbl_soa_details table  " style=" border-collapse: collapse;white-space: nowrap;width: 80%">
											<tr>
												<th>Date</th>
												<th>OR No.</th>
												<th >Amount</th>
											</tr>
											@foreach($payments as $pay)
												<tr>
													<td>{{$pay->transaction_date}}</td>
													<td>{{$pay->or_number }}</td>
													<td class="col_amount">{{number_format($pay->amount,2) }}</td>
												</tr>
											<?php $less_payment += $pay->amount;?>
											@endforeach
										</table>
									@endif
					        	</td>
					        	<th class="col_amount">{{(count($payments) == 0)?'0.00':''}}</th>
					        </tr>
					        <!-- if payment -->
							@if(count($payments) > 0)
								<tr>
									<th class="col_amount" colspan="3">-{{number_format($less_payment,2)}}</th>
								</tr>
							@endif
					      	<tr>
					      		<th class="group_padding">Adjustment(s)</th>
					      		<td></td>
					        	<th class="col_amount">{{number_format($adjustments,2)}}</th>
					        </tr>
					        <tr>
					        	<?php $subtotal = $prev_amount_due-$less_payment+$adjustments; ?>
					        	<th class="group_padding">Subtotal</th>
					        	<td></td>
					        	<th class="col_amount">{{ number_format($subtotal,2) }}</th>
					        </tr>
					        <tr>
					        	<th colspan="3">&nbsp;</th>
					        </tr>
							<?php
								$total_amount_due = $current_total+$subtotal;
							?>
					        <tr>
					        	<th>TOTAL AMOUNT DUE</th>
					        	<th></th>
					        	<th class="col_amount">{{number_format($total_amount_due,2)}}</th>
					        </tr>
					    </tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>

</div>
<button onclick="topFunction()" id="myBtn" title="Scroll to top"><i class="fa fa-chevron-up"></i></button>
@endsection
@push('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		
	})
	var mybutton = $('#myBtn');
// When the user scrolls down 20px from the top of the document, show the button
	window.onscroll = function() {

	  // if (document.body.scrollTop > 20 || document.documentElement.scrollTop > $( window ).height()-100) {
	  // if ($(window).scrollTop() + $(window).height() == $(document).height()) {
	  //   mybutton.fadeIn(700);
	  // } else {
	  //   mybutton.fadeOut(700);
	  // }
	  	var scrollHeight = $(document).height();
		var scrollPosition = $(window).height() + $(window).scrollTop();
		if ((scrollHeight - scrollPosition) / scrollHeight <= 0.0002) {
			 mybutton.fadeIn(700);
		}else{
			mybutton.fadeOut(700);
		}
	};
	function topFunction() {
		var body = $("html, body");
			body.stop().animate({scrollTop:0}, 500, 'swing', function() { 
		});
	}
	function export_soa(){
		window.open('/admin/export_soa?control_number='+ '{{$details->control_number}}');
	}
</script>
@endpush