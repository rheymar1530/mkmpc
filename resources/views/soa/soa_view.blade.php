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
	.col_amount, .col_number{
		text-align: right;
		padding-right: 5px !important;
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
	.pad_2{
    	padding-left: 15px !important;
 	}
  	.pad_3{
    	padding-left: 30px !important;
    }
    .xlText {
    	mso-number-format: "\@";
	}
</style>

<div class="container-fluid">
	<?php $back_link = (request()->get('href') == '')?'/admin/soa/index':request()->get('href'); ?>
	<a class="btn btn-default btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to SOA Control List</a>
	@if($credential->is_confirm && $details->status == "0")
		<a class="btn btn-info btn-sm" onclick="change_status()" style="margin-bottom:10px">Change Status</a>
	@endif
	@if($details->status == "10")
		<p class="text-muted" style="margin-bottom:-3px"><?php echo ($details->status == "10")?"Reason : $details->reason (".date('m/d/Y h:i:s A', strtotime($details->date_updated)).")":"" ?></p>
	@else
		<p class="text-muted" style="margin-bottom:-3px">
			@if($details->status == 1)
				Date Sent : {{$details->date_updated}}
			@elseif($details->status == 2)
				Date Sent : {{date('m/d/Y h:i:s A', strtotime($details->date_updated))}} || Date Viewed :  {{date('m/d/Y h:i:s A', strtotime($details->date_viewed))}}
			@endif
		</p>
	@endif

	<div class="card">
		<div class="card-body">
		@if($details->status > 0)
				@if($details->status == "10")
					<div class="ribbon-wrapper ribbon-lg">
						<div class="ribbon bg-danger">
							CANCELLED	
						</div>
					</div>
				@else
					<div class="ribbon-wrapper ribbon-lg">
						<div class="ribbon bg-success">
							{{$details->status_text}}	
						</div>
					</div>					
				@endif
			@endif
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
				  <?php
				    $g_total = array();
				    if(count($sum_fields) > 0){
				      foreach($sum_fields as $f){
				        $g_total[$f] = 0;
				      }
				    }
				  ?>
				    @if($credential->is_print && $details->status != "10")
					<button class="btn bg-gradient-success" onclick="export_excel()">Export to Excel</button>
					<button class="btn btn-danger" onclick="export_soa()">Export & Print</button>
					@endif
					@if($credential->is_print && $details->status == "0")
					<button class="btn bg-gradient-info" onclick="preview_attachment()">SOA Attachment Preview</button>
					@endif
					@if($details->attachment_status > 0)
					<div class="btn-group">
						<button type="button" class="btn bg-gradient-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							SOA Attachment @if($details->attachment_status ==1) (PROCESSING)@endif
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="/billing/attachment/CTRL_{{$details->control_number}}_{{$details->access_token}}" target="_blank">View/Download</a>
							<a class="dropdown-item" onclick="refresh_soa()">Refresh</a>
						</div>
					</div>
					@endif
					<div class="table-responsive" style="max-height: calc(100vh - 150px);overflow-y: auto;margin-top: 10px">
						<table class="table tbl_transactions table-striped" style="white-space:nowrap;">
							<thead>
								<tr class="table_header" style="border-top: 10px;">
									@foreach($headers as $header)
										<th><b>{{ $header }}</b></th>
									@endforeach
								</tr>
							</thead>
					        <tbody>
					          @if($group_count == 0)
					            @foreach($data_list as $item)
					              <tr>
					                @foreach($fields as $field)
					                  <?php
					                    $dt = $data_types[$field];
					                    if($dt == "amount"){
					                      $val = number_format($item->{$field},2);
					                    }else{
					                      $val = $item->{$field};
					                    }

					                    if($field == "hawb_no"){
					                    	$cc = "xlText";
					                    }else{
					                    	$cc = "";
					                    }
					                  ?>
					                  <td class="col_{{$dt}} {{$cc}}">{{ $val }}</td>
					                @endforeach
					              </tr>
					            @endforeach
					            @elseif($group_count == 1)
					              @foreach($data_list as $first_key => $first_item)
					                <tr>
					                  <th colspan="{{count($fields)}}">{{$first_key}}</th>
					                </tr>
					                @foreach($first_item as $item)
					                  <tr>
					                    @foreach($fields as $c=>$field)
					                    <?php
					                      $dt = $data_types[$field];
					                      if($dt == "amount"){
					                        $val = number_format($item->{$field},2);
					                      }else{
					                        $val = $item->{$field};
					                      }
					                      $pad = ($c==0)?"pad_2":"";
					                    ?>
					                    <td class="col_{{$dt}} {{ $pad }}">{{ $val }}</td>
					                    @endforeach
					                  </tr>
					                @endforeach
					                @if(count($sum_fields) > 0)
					                  <tr>
					                    @foreach($fields as $c=>$field)
					                      @if($c == 0)
					                        <th class="subtotal_border">Sub total</th>
					                      @else
					                        <th class="col_amount subtotal_border">{{ isset($group_total[$first_key][$field])?number_format($group_total[$first_key][$field],2):'' }}</th>
					                      @endif

					                    @endforeach
					                  </tr>
					                @endif
					              @endforeach
					            @else
					              @foreach($data_list as $first_key => $first_item)
					                <tr>
					                  <th colspan="{{count($fields)}}">{{$first_key}}</th>
					                </tr>
					                @foreach($first_item as $second_key => $second_item)
					                  <tr>
					                    <th colspan="{{count($fields)}}" class="pad_2">{{$second_key}}</th>
					                  </tr>
					                   @foreach($second_item as $item)
					                    <tr>
					                      @foreach($fields as $c=>$field)
					                      <?php
					                        $dt = $data_types[$field];
					                        if($dt == "amount"){
					                          $val = number_format($item->{$field},2);
					                        }else{
					                          $val = $item->{$field};
					                        }
					                        $pad = ($c==0)?"pad_3":"";
					                      ?>
					                      <td class="col_{{$dt}} {{ $pad }}">{{ $val }}</td>
					                      @endforeach
					                    </tr>
					                   @endforeach
					                  @if(count($sum_fields) > 0)
					                  <tr>
					                    @foreach($fields as $c=>$field)
					                      @if($c == 0)
					                        <th>Sub total</th>
					                      @else
					                        <th class="col_amount">{{ isset($group_total[$first_key][$second_key][$field])?number_format($group_total[$first_key][$second_key][$field],2):'' }}</th>
					                      @endif

					                    @endforeach
					                  </tr>
					                @endif
					                @endforeach
					                @if(count($sum_fields) > 0)
					                  <tr>
					                    @foreach($fields as $c=>$field)
					                      @if($c == 0)
					                        <th>Sub total</th>
					                      @else
					                        <th class="col_amount">{{ isset($group_total[$first_key][$field])?number_format($group_total[$first_key][$field],2):'' }}</th>
					                      @endif

					                    @endforeach
					                  </tr>
					                @endif
					              @endforeach
					          @endif
					      </tbody>
					      @if(count($sum_fields) > 0)
					      <tfoot>
					          <tr>
					            @foreach($fields as $field)
					              <td class="col_amount">{{ isset($grand_sum[$field])?number_format($grand_sum[$field],2):'' }}</td>
					            @endforeach
					          </tr>
					      </tfoot>
					      @endif
						  </table>
					</div>
				</div>
				<div class="col-md-12" style="margin-top: 20px;">
					<?php
						$current_total = $grand_sum['total'];
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
@include('soa.soa_update_status')
@endsection
@push('scripts')
<script type="text/javascript">
	const token = '<?php echo $details->access_token; ?>';
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
	function change_status(){
		$('#modal_status').modal('show');
	}
	function generate_soa_attachment(){
		$.ajax({
			type             :               'POST',
			url              :               '/billing/generate_attachment',
			data             :               {'token' : token,
											  'ctrl_no' : '{{$details->control_number}}'},
			beforeSend       :               function(){
												Swal.fire({
													title: 'Generating SOA Attachment',
													text : 'Please wait ..',
													allowOutsideClick: false,
													showConfirmButton: false,
													allowEscapeKey: false,
													onBeforeOpen: () => {
														Swal.showLoading()
													},
												})
			},
			success          :              function(response){
												console.log({response});
												if(response.RESPONSE_CODE == "SUCCESS"){
													Swal.fire({
														position: 'center',
														icon: 'success',
														title: 'SOA Attachment Generated !',
														showCancelButton: false,
														cancelButtonText : 'Close',
														showConfirmButton: false,   
														timer: 2000
													}).then(function() {
														location.reload();
													}) 													
												}
			},error: function(xhr, status, error){
	            hide_loader();
	            var errorMessage = xhr.status + ': ' + xhr.statusText;
	            Swal.fire({
	              position: 'center',
	              icon: 'warning',
	              title: "Error-"+errorMessage,
	              showConfirmButton: false,
	              showCancelButton: true,
	              cancelButtonText : "Close"
	            })
	        }  
		})
	}
	function preview_attachment(){
		window.open('/admin/soa/soa_attachment/'+'{{$details->access_token}}'+'?load=1','_blank');
	}
	function export_excel(){
		var file_name = '{{$details->control_number}}'+"_"+'{{$details->account_no}}'+"_"+'{{$details->name}}';
		$(".tbl_transactions").table2excel({
		    name: '{{$details->control_number}}',
		    filename: file_name
		});
	}
	function refresh_soa(){
		Swal.fire({
			title: 'Confirmation',
			text : 'Refreshing the SOA attachment will override the previous attachment',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Refresh`,
		}).then((result) => {
			if (result.isConfirmed) {
			generate_soa_attachment()
			} 
		}) 
	}
</script>
@endpush