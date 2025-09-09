<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>

</head>
<style type="text/css">
	@page {      
		margin-left: 1cm;
		margin-right: 1cm;
		margin-top: 1cm;
		margin-bottom: 0.5cm; 

		size: legal portrait; 
	}
	div.a {
		line-height: 50%;
	}

	* {
		box-sizing: border-box;

		font-family: Calibri, sans-serif;
	}
	
	.row:after {
		line-height: 10%;
		content: "";
		display: table;
		clear: both;
		height: 1px;
	}
	table, th, td {
		border-collapse: collapse;
		padding-left: 1mm;
	}
	.with_border{
		border: 1px solid black;
	}
	.class_amount{
		text-align: right;
		padding-right: 2mm;
	}

	.sticker_header{
		font-size:18px;
		margin-top: 0px !important;
	}
	.bold_txt{
		font-weight: bold;
	}
	/*.tbl_assets tr{
		page-break-inside: avoid !important;
	}*/
/*	.tbl_assets  tr>td{
		page-break-inside: avoid !important;
	}*/
	.tbl_assets{
		page-break-inside: always !important;
		/*page-break-after: always;*/
	}
</style>
<body>
	<div class="row">
		
		<table style="width:100%;" class="tbl_assets">

			@foreach($assets as $asset)
			<tr>
				@foreach($asset as $counter=>$column)
				<td class="with_border" width="33%">
					<div class="row">
						<h3 style="" class="head sticker_header"><center>Property of {{config('variables.coop_abbr')}}</center></h3>
					</div>
					<div class="row" style="margin-top:-15px">
						<table>
							<tr>
								<td>{!! DNS2D::getBarcodeHTML($column['asset_code'],'QRCODE',3,3)!!}<span style="font-size:13px;margin-left: 10px">{{$column['counter']}} of {{$column['quantity']}}</span>
								<td style="vertical-align: top;font-size: 14px;padding-left: 10px;">
									<span><label class="bold_txt">Asset Code:</label> {{$column['asset_code']}}</span><br>
									<span><label class="bold_txt">Description:</label> {{$column['description']}}</span><br>
									<span><label class="bold_txt">Serial No:</label> {{$column['serial_no']}}</span><br>
								</td>


								</tr>
							</table>
						</div>
					
					@if($counter == count($asset)-1)
						@for($i=0;$i<$fill_td;$i++)
						<td class="" width="33%"></td>
						
						@endfor
					@endif
					@endforeach

				</tr>

				@endforeach




			</table>
		</div>

	</body>
	</html>