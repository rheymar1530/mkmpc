<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		@page {      
			margin-left: 5%;
			margin-right: 5%;
			margin-top:6%;
			size : letter portrait;
		}
		div.header{
			text-align: center;
			line-height: 1px;
			font-size: 10px;
			font-family: Arial, Helvetica, sans-serif;
		}

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
		.tbl_acct_details  tr>td,.tbl_acct_details  tr>th{
			padding:1px;
			vertical-align:top;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 10px !important;
		}
		.tbl_attachment  tr>td,.tbl_attachment  tr>th{
			padding:4px;
			padding-top: 8px;
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
		.col_amount, .col_number{
			text-align: right;
			margin-right: 5px !important;
		}
		.group_padding{
			padding-left: 20px !important;
		}
		.tbl_attachment{
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
		.border-bottom{
			border-bottom: 1px solid;
		}
		.pad_right{
			padding-right: 5px;
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
		.img_type{
			font-weight: bold;
		}
	</style>
</head>
<body>
	<header>
		<div class="header" style="margin-top: -36px">
			<p><b>LIBCAP SUPER EXPRESS CORPORATION <b></p>
			<p>{{ $details->branch_address }}</p>
			<p>Tel. No. {{ $details->contact }}</p>
			<p>TIN # 005-984-515-000</p> 
		</div> 
		<div class="header">
			<p><b>STATEMENT OF ACCOUNT ATTACHMENT</b></p>
		</div> 
	</header>
	<div class="row" style="width:100%">
		<div class="columnLeft">
			<table style=" border-collapse: collapse;white-space: nowrap;" class="tbl_acct_details">
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
		<div class="columnRight">
			<table style="float: right; border-collapse: collapse;" class="tbl_acct_details">
				<tbody>
					<tr>
						<td class="details_tbl_lbl">Control No.:</td>
						<td class="bold_lbl">&nbsp;&nbsp;&nbsp;&nbsp; {{ $details->control_number }}</td>
					</tr>
					<tr>
						<td class="details_tbl_lbl">Account No.:</td>
						<td class="">&nbsp;&nbsp;&nbsp;&nbsp;{{ $details->account_no }}</td>
					</tr>
					<tr>
						<td class="details_tbl_lbl">Billing Period:</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $details->billing_period }}</td>
					</tr>
					<tr>
						<td class="details_tbl_lbl">Statement Date:</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $details->statement_date }} </td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black" class="tbl_attachment">
			<thead>
				<tr>	
					@if($type_count == 2)
						<td width="50%"></td>	
						<td width="50%"></td>	
					@else
						<td width="33%"></td>			
						<td width="33%"></td>
						<td width="33%"></td>
					@endif			
					<?php
						
					?>	
				</tr>
			</thead>
			<tbody>
				<?php $counter = 0; $found = true; $no_attachment = array();?>
				@foreach($attachments as $att)
					@if($att->w_attachment == 1)
					@if(($type_count == 1 && $counter==0 && $found) || $type_count >= 2)
						<tr>
					@endif
						<?php
							$found = false;
							$type= "jpeg";
							$dd  = Storage::disk('tat')->path("/uploads/POD/00720860b730b00faa9.jpeg");
							if($id_receiver && $att->id_image != ""){
								$path_id = Storage::disk('tat')->path($att->id_image);
								$base64_id = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path_id));
								$counter++;
								$found = true;
							}
							if($pod && $att->image != ""){
								$path_pod = Storage::disk('tat')->path($att->image);
								$base64_pod = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path_pod));
								$counter++;
								$found = true;
							}
							if($signature == 1 && $att->signature != ""){
								$path_signature =  Storage::disk('tat')->path($att->signature);
								$base64_signature = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path_signature));
								$counter++;
								$found = true;
							}
						?>
						@if($id_receiver)
							@if($type_count == 1 && $att->id_image == "")

							@else
							<td> 
								

									<strong>@if($first_col == 'id_receiver')TN No. {{$att->hawb_no}} - {{$att->status}} {{$att->date_received}} {{$att->received_by}}@else&nbsp;@endif</strong>
									<div style="text-align:center;">
										<strong>ID</strong>
									</div>
									@if($att->id_image != "")
									<div style="margin-top:5px">
										<img src="{{$base64_id}}" alt="image" height="150px" width="230px">
									</div>
								@endif
							</td>
							@endif
						@endif
						@if($pod)
							<td> 
								
									<strong>@if($first_col == 'pod')TN No. {{$att->hawb_no}} - {{$att->status}} {{$att->date_received}} {{$att->received_by}}@else&nbsp;@endif</strong>
									<div style="text-align:center;">
										<strong>POD</strong>
									</div>
									@if($att->image != "")
									<div style="margin-top:5px">
										<img src="{{$base64_pod}}" alt="image"  height="70px" width="220px">
									</div>
								@endif
							</td>
						@endif
						@if($signature == 1)
							@if($type_count == 1 && $att->signature == "")
								
							@else
								<td> 
									
									<strong>@if($first_col == 'signature')TN No. {{$att->hawb_no}} - {{$att->status}} {{$att->date_received}} {{$att->received_by}}@else&nbsp;@endif</strong>
										<strong>&nbsp;</strong>
										<div style="text-align:center;">
											<strong>Signature</strong>
										</div>
										@if($att->signature != "")
										<div style="margin-top:5px">
											<img src="{{$base64_signature}}" alt="image" height="70px" width="200px">
										</div>
										@endif
								</td>
							@endif
						@endif
						@if(($type_count == 1 && $counter==3) || $type_count >= 2)
							<?php $counter = 0; ?>
							</tr>
						@endif
					@else
							<?php
								$at = "TN No. ".$att->hawb_no." - ".$att->status." ".$att->date_received."  ".$att->received_by;
								array_push($no_attachment,$at);
								// no_attachment
							?>
						
					@endif
				@endforeach
				@if(count($no_attachment) > 0)
					<tr>
						<th colspan="{{$type_count}}" style="text-align:left;font-size:15px !important">No Attachments</th>
					</tr>
					@foreach($no_attachment as $n)
					<tr>
						<th colspan="{{$type_count}}" style="text-align:left">{{$n}}</th>
					</tr>
					@endforeach
				@endif
			</tbody>
		</table>
	</div>
</body>
</html>

