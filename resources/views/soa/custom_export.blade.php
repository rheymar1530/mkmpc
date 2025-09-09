<!DOCTYPE html>
<html lang="en">   
	<head>
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	        <style>

		        @page {      
		            margin-left: 5%;
		            margin-right: 5%;
		            margin-top:6%;
		        }
			    div.header{
			    	text-align: center;
			    	line-height: 1px;
			    	font-size: 10px;
			    	 font-family: Arial, Helvetica, sans-serif;
			    }
	/*		    	        	@page :first{
	        		margin-top: 100px !important;
	        		margin-bottom: 300px;
	        	}*/
		        * {
		            box-sizing: border-box;
		        }
		        .columnLeft {         
		            float: left;
		            width: 50%;
		         }
		        .columnRight {         
		            float: right;
		            width: 50%;
		        }
				.tbl_acct_details  tr>td,.tbl_acct_details  tr>th,
				.tbl_transactions  tr>td,.tbl_transactions  tr>th{
					padding:0px;
					vertical-align:top;
					font-family: Arial, Helvetica, sans-serif;
					font-size: 10px !important;
				}
				.bold_lbl{
					font-weight: bold;
				}
		        .details_tbl_lbl{
					text-align: right !important;
					width: 40% !important;
				}	
				.pd_left_10{
					/*padding-left: 10px !important;*/
				}
				.row:after {
		            line-height: 10%;
		            content: "";
		            display: table;
		            clear: both;
		            height: 1px; 
		        }

				.col_amount{
					text-align: right;
				}
				.group_padding{
		 			 padding-left: 20px !important;
				}
				.tbl_transactions{
					page-break-inside: always !important;
					/*page-break-after: always;*/
				}
				.page-break {
				    page-break-after: always;
				}
				div.absolute {
					position: absolute;
					bottom: 10px;
					width: 50%;
					/*border: 3px solid #8AC007;*/
				}
				.text_notes{
					font-family: Arial, Helvetica, sans-serif;
					font-size: 10px;
				}
				.table_header th{
					text-align: left;
				}
				.align-left{
					text-align: left;
				}
				.pad_2{
				  margin-left: 15px !important;
				}
				.pad_3{
				  margin-left: 30px !important;
				}
				.border-top{
					border-top: 1px solid black !important
				}
				.border-bottom{
					border-bottom: 1px solid black !important
				}
	</style>
	</head>
	<!-- <body style="margin-bottom: 1% !important;margin-top: -0.5%;margin-left: -10px;margin-right: : -10px"> -->
	<body>


	<div class="row">
		<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black" class="tbl_transactions">
			<thead>
				<tr class="table_header">
				@foreach($headers as $header)
					<th>{{ $header }}</th>
				@endforeach
				</tr>
				<tr>
					@foreach($headers as $header)
						<th>&nbsp;</th>
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
									?>
								<td class="col_{{$dt}}">{{ $val }}</td>
							@endforeach
						</tr>
					@endforeach
				@elseif($group_count == 1)
					@foreach($data_list as $first_key => $first_item)
		                <tr>
		                	<th colspan="{{count($fields)}}" class="align-left">{{$first_key}}</th>
		                </tr>
						@foreach($first_item as $item)
							<tr>
								@foreach($fields as $field)
									<?php
										$dt = $data_types[$field];
										if($dt == "amount"){
											$val = number_format($item->{$field},2);
										}else{
											$val = $item->{$field};
										}
									?>
									<td class="col_{{$dt}} pad_2">{{ $val }}</td>
								@endforeach
							</tr>
						@endforeach
						@if(count($sum_fields) > 0)
							<tr>
								@foreach($fields as $c=>$field)
									@if($c == 0)
										<th class="align-left border-bottom">Sub total</th>
									@else
										<th class="col_amount border-bottom">{{ isset($group_total[$first_key][$field])?number_format($group_total[$first_key][$field],2):'' }}</th>
									@endif
								@endforeach
							</tr>
						@endif
					@endforeach
				@else
					@foreach($data_list as $first_key => $first_item)
						<tr>
							<th colspan="{{count($fields)}}" class="align-left">{{$first_key}}</th>
						</tr>
						@foreach($first_item as $second_key => $second_item)
							<tr>
								<th colspan="{{count($fields)}}" class="pad_2 align-left">{{$second_key}}</th>
							</tr>
							@foreach($second_item as $item)
								<tr>
									@foreach($fields as $field)
										<?php
											$dt = $data_types[$field];
											if($dt == "amount"){
												$val = number_format($item->{$field},2);
											}else{
												$val = $item->{$field};
											}
										?>
										<td class="col_{{$dt}} pad_3">{{ $val }}</td>
									@endforeach
								</tr>
							@endforeach
							<!-- SUM FIRST GROUP -->
							@if(count($sum_fields) > 0)
								<tr>
									@foreach($fields as $c=>$field)
										@if($c == 0)
											<th class="align-left border-bottom">Sub total</th>
										@else
											<th class="col_amount border-bottom">{{ isset($group_total[$first_key][$second_key][$field])?number_format($group_total[$first_key][$second_key][$field],2):'' }}</th>
										@endif
									@endforeach
								</tr>
							@endif
							<!-- END SUM FIRST GROUP -->
						@endforeach
						<!-- SUM SECOND GROUP -->
						@if(count($sum_fields) > 0)
							<tr>
								@foreach($fields as $c=>$field)
									@if($c == 0)
										<th class="align-left border-bottom">Sub total</th>
									@else
										<th class="col_amount border-bottom">{{ isset($group_total[$first_key][$field])?number_format($group_total[$first_key][$field],2):'' }}</th>
									@endif

								@endforeach
							</tr>
						@endif
						<!--END SUM SECOND GROUP  -->
					@endforeach
				@endif
			</tbody>
			@if(count($sum_fields) > 0)
				<tfoot>
					<tr>
						@foreach($fields as $c=>$field)
							@if($c == 0)
								<th class="align-left border-top">Grand Total</th>
							@else
								<td class="col_amount border-top">{{ isset($grand_sum[$field])?number_format($grand_sum[$field],2):'' }}</td>
							@endif
						@endforeach
					</tr>
				</tfoot>
			@endif
		</table>
	</div>

	<div class="row" style="page-break-inside: avoid;height: 200px;margin-bottom: 80px">
		<table class="tbl_acct_details" style="border-collapse: collapse" width="100%">
			<tr>
				<td width="100%" colspan="2">Please make your check payable and crossed to <b>LIBCAP SUPER EXPRESS CORPORATION</td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td width="100%" colspan="2">Your usual prompt payment will be highly appreciated. However, a 2% interest per month will be charged to your account if Total Amount Due is not paid within fifteen (15) days of Payment Due Date. Please Notify LIBCAP SUPER EXPRESS CORPORATION within three (3) days from receipt thereof, of any correction or adjustment in writing otherwise, you agree that this Statement of Account is true and correct as to all matters contained herein, including of its attachments. Thank you for giving us the opportunity to be of service to you.</td>
			</tr>
		</table>
		<table class="tbl_acct_details" style="border-collapse: collapse;float: right;margin-top: 10px;" width="30%">
			<tr>
				<td>Received statement and supporting original copies of Tracking Summary and PODs</td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td>_______________________________________</td>
			</tr>
			<tr>
				<td style="text-align:center;">Please print name and sign</td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td>Date:___________________________________</td>
			</tr>
		</table>
	</div>


	</body>
</html>