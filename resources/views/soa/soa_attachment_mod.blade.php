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
/*
		* {
			box-sizing: border-box;
		}*/
		.columnLeft {         
			float: left;
			width: 50%;
		}
		.columnRight {         
			float: right;
			width: 50%;
		}
		.tbl_acct_details  tr>td,.tbl_acct_details  tr>th,.ins_table  tr>td,.ins_table  tr>th
		{
			padding:1px !important;
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
	<?php
		$class=[
			'id_image'=>[
				'label' =>'RECEIVER ID',
				'properties' =>" height='150px' width='200px'" 
			],
			'image'=>[
				'label' =>'POD',
				'properties' =>" height='70px' width='200px'" 
			],
			'signature'=>[
				'label'=>'SIGNATURE',
				'properties' =>" height='70px' width='200px'" 
			]
		];
	?>
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
	<?php $no_attachment = array();?>
	<div class="row" style="page-break-inside: auto;border-top: 1px solid;">
			@if($type_count >= 2)
					@foreach($attachments as $hawb_key=>$att)
						<table style="page-break-inside: avoid !important;" class="ins_table">
							@if($att['w_attachment'] == 1)
								<tr>
									<td colspan="{{$type_count}}"><strong>{{$att['remarks']}}</strong></td>
								</tr>
								<tr>
									@foreach($image_keys as $c=> $k)
										<?php
											$type= "jpeg";
											if($att['attachments'][$k] != ""){
												$path = Storage::disk('tat')->path($att['attachments'][$k]);
												
											    if (!File::exists($path)) {
										           $base64 = "";
										        }else{
										        	$base64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));	
										        }			
											}
										?>
										<td style="width:33% !important">
											<div style="text-align:center;">
												<strong>{{$class[$k]['label']}}</strong>
											</div>
											@if($att['attachments'][$k] != "")
												<div style="margin-top:5px">
													<img src="{{$base64}}" alt="image" height="150px" width="230px">
												</div>
											@else
												<div style="margin-top:5px;width: 230px;">
												</div>
											@endif
											
										</td>
									@endforeach
								</tr>
							<!-- NO ATTACHMENT -->
							@else 
								<?php
									array_push($no_attachment,$att['remarks']);
								?>
							@endif
						</table>
					@endforeach
					<?php
						// $count = count($no_attachment);
						// $size = floor($count/2) + (($count%2 == 0)?0:1);
						// $no_attachment = array_chunk($no_attachment, $size);
						$size =  count($no_attachment);
					?>
			@else
				<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black;" class="tbl_attachment">
				<?php $cc = 0; $start_tr = true;?>
				<tbody>
					@foreach($attachments as $hawb_key=>$att)
						@if($att['w_attachment'] == 1)
							@if($start_tr)
								<tr>
						
							@endif
							<?php
								$prev_cc = $cc;
								$type= "jpeg";
								if($att['attachments'][$image_keys[0]] != ""){
									$path = Storage::disk('tat')->path($att['attachments'][$image_keys[0]]);
								    if (!File::exists($path)) {
							           $base64 = "";
							        }else{

							        	$base64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));	
							        }	
							        $cc++;
								}
								if($prev_cc != $cc){
									$start_tr = false;
								}	
							?>
							<td style="width:33% !important">
								<strong>{{$att['remarks']}}</strong>
								<div style="text-align:center;">
									<strong>{{$class[$image_keys[0]]['label']}}</strong>
									
								</div>
								@if($att['attachments'][$image_keys[0]] != "")
									<div style="margin-top:5px">
										<img src="{{$base64}}" alt="image" <?php echo $class[$image_keys[0]]['properties']; ?> >
									</div>
								@else
									<div style="margin-top:5px;width: 230px;">
									</div>
								@endif
							</td>
						
							@if($cc == 3)
								</tr>
								<?php $cc = 0; $start_tr = true;?>
							@endif
						@else
							<?php
								array_push($no_attachment,$att['remarks']);
							?>
						@endif
					@endforeach
						<?php
							// $count = count($no_attachment);
							// $size = floor($count/2) + (($count%2 == 0)?0:1);
							// $no_attachment = array_chunk($no_attachment, $size);
							$size =  count($no_attachment);
						?>
				</tbody>
				</table>

			@endif
		

	</div>
	<br>
	<div class="row">
		
		@if(count($no_attachment) > 0)
			<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black;margin-top: 20px;" class="tbl_acct_details">	
				<thead>
					<tr>
						<th style="font-size: 15px !important;text-align: left;">No Attachments</th>
					</tr>
				</thead>	
				<tbody>
					@for($i=0;$i<$size;$i++)

						<tr>
							<td style="font-weight:bold;border: black;">{{$no_attachment[$i] ?? ''}}</td>
						</tr>
					@endfor
				</tbody>
					
			</table>
		@endif
	</div>

</body>
</html>

