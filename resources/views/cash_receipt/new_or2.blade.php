<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Print OR</title>
	<style type="text/css">
		@page {
            margin:  0;
            size: portrait;
        }
        @media print {
            @page {
                size: portrait;
                margin-top: 1.7cm;
                margin-left: 0.8cm;
                padding: 0;
            }
        }
        #tn_box{
            width: 18.7cm;
            /*border: 1px solid black; */
            position: relative;
            /*background: blue;*/
            overflow: hidden;
            padding: 0;
            margin: 0;
            height: 7.4cm;
            min-height: 7.4cm;
            max-height: 7.4cm;
        }
	    .column {
	        float: left;
	    }
	    #col-grp-1{
	        width: 4.2cm;
	        min-width: 4.2cm;
	        max-width: 4.2cm;
	        padding: 0;

	        margin: 0;
	        margin-right: 0.2cm;

	    }
	    #col-grp-2{
	        width: 14.3cm;
	        min-width: 14.3cm;
	        max-width: 14.3cm;
	        padding: 0;
	        margin: 0;
	        margin-top: 1.3cm !important;
/*	        margin-left: 1.5mm !important;*/

	        height: 6.1cm;
/*	        background: red;*/

	    }
	    .text-center{
	    	text-align: center !important;
	    }
	    .text-right{
	    	text-align: right !important;
	    }
      .item_description{
	        height: 0.5cm ;
	        white-space: nowrap;
	        text-overflow: ellipsis;
	        overflow: hidden;
	        box-sizing: border-box;
	        font-size: 12px;
/*	        padding-top: 1mm !important;*/
	        padding-left: 1mm;
      }
	</style>
	<style type="text/css">
     	table {
          width: 100%;
          border-collapse: collapse !important;
          
      	}
		.table_width{
            width: 4.2cm;
        }
        .table_column_1_width_item{
            width: 2.2cm;
        }
        .table_column_2_width_item{
            width: 2cm;
        }
        .header_height{
            height: 1cm;
        }
	    td.table-column > div.item {
	        height: 0.5cm;
	        box-sizing: border-box;
	        white-space: nowrap;
	        text-overflow: ellipsis;
	        overflow: hidden;
	        font-size: 12px;
	        
/*	        padding-top: 0.5mm;*/
	        
	    }
		.item_4{
			height: 0.4cm !important;
		
		}
		.item_8{
			height: 0.8cm !important;
			
		}
		div.item_8{
/*			padding-top: 4mm !important;*/
		}

		.item_7{
			height: 0.7cm !important;
			
		}
		div.item_7{
/*			padding-top: 3mm !important;*/
		}

		.item_6{
			height: 0.6cm !important;
			
		}

		div.item_6{
/*			padding-top: 1.5mm !important;*/
		}

		tr.i4 .table-column{
			height: 0.4cm !important;
		}

		tr.i4 div.item_description{
/*			padding-top: 1mm !important;*/
		}

		.amt{
			padding-right: 1mm !important;
		}
		.pmode{
			height: 1.5cm;
		}
	</style>
	<style type="text/css">
		.or_details{
			width: 14.3cm;
		}
		.or_details{

		}
	</style>
	<style type="text/css">
		.add_border_main {
	        box-shadow:-1px 0 1px 1px rgba(0, 0, 0, 0.75), inset -1px 0 0 1px rgba(0, 0, 0, 0.75);
	    }
	    .add_border {
	        box-shadow:-1px 0 1px 1px rgba(0, 0, 0, 0.75), inset -1px 0 0 1px rgba(0, 0, 0, 0.75);
	    }
	    #col-grp-2 .add_border{
/*	    	box-shadow:  none !important;*/
	    }
	</style>
</head>
<body>
	<div class="row add_border_main" id="tn_box">
		<div class="column border-right" id="col-grp-1" style="padding:0px !important">
		 	<table class="table_width add_border" style="" border="0" cellspacing="0" cellpadding="0">
		 		<thead>
			        <tr>
			            <th class="table_column_1_width_item add_border"><div class="header_height"></div></th>
			            <th class="table_column_2_width_item add_border"><div class="header_height"></div></th>
			        </tr>
		 		</thead>
		 		<tbody>
		 			@for($i=1;$i<=10;$i++)
		 			<?php
		 				$add_class = ($i ==5)?"item_4":"";
		 			?>
		 			<tr>
		 				 <td class="table-column"><div class="item {{$add_class}} table_column_1_width_item add_border text-center">Particular {{$i}}</div></td>
		 				 <td class="table-column"><div class="item {{$add_class}} table_column_2_width_item add_border text-right amt">{{number_format($i*100,2)}}</div></td>
		 			</tr>
		 			@endfor	
		 		</tbody>
			        <tr>
			            <th class="table_column_1_width_item add_border"><div class="pmode"></div></th>
			            <th class="table_column_2_width_item add_border"><div class="pmode"></div></th>
			        </tr>
		 	</table>
		</div>
		<div class="column border-right add_border" id="col-grp-2" style="padding:0px !important">
			<!-- Date -->
			<table class="or_details">
				<tr>
					<td class="table-column item_7" style="width:10.5cm"><div  class="item_description item_7 add_border"></div></td>
					<td class="table-column item_7" style="width:3.8cm !important"><div class="item_description item_7 add_border">[Date]</div></td>
				</tr>
			</table>

			<!-- Received from -->
			<table class="or_details">
				<tr>
					<td class="table-column item_6" style="width:2.2cm"><div  class="item_description add_border item_6"></div></td>
					<td class="table-column item_6" style="width:12.1cm !important"><div class="item_description add_border item_6">[Received from name]</div></td>
				</tr>
			</table>

			<!-- TIN No -->
<!-- 			<table class="or_details">
				<tr>
					<td class="table-column" style="width:1.3cm"><div  class="item_description add_border"></div></td>
					<td class="table-column" style="width:13cm !important"><div class="item_description add_border"> [TIN Number]</div></td>
				</tr>
			</table> -->

			<!-- Address -->
<!-- 			<table class="or_details">
				<tr>
					<td class="table-column" style="width:2.5cm"><div  class="item_description add_border"></div></td>
					<td class="table-column" style="width:11.8cm !important"><div class="item_description add_border"> [Address]</div></td>
				</tr>
			</table> -->

			<!-- Engaged in -->
<!-- 			<table class="or_details">
				<tr>
					<td class="table-column" style="width:1.8cm"><div  class="item_description add_border"></div></td>
					<td class="table-column" style="width:12.5cm !important"><div class="item_description add_border"> [Engaged in]</div></td>
				</tr>
			</table> -->

			<!-- The sum of -->
<!-- 			<table class="or_details">
				<tr>
					<td class="table-column" style="width:1.8cm"><div  class="item_description add_border"></div></td>
					<td class="table-column" style="width:12.5cm !important"><div class="item_description add_border"> [Sum of]</div></td>
				</tr>
			</table> -->

			<!-- Amount in numbers -->
<!-- 			<table class="or_details">
				<tr>
					<td class="table-column" style="width:10cm"><div  class="item_description add_border"></div></td>
					<td class="table-column" style="width:4.3cm !important"><div class="item_description add_border"> 3,456.00</div></td>
				</tr>
			</table> -->

			<!-- Partial/Full payment -->
<!-- 			<table class="or_details">
				<tr>
					<td class="table-column item_7" style="width:4cm"><div  class="item_description item_7 add_border"></div></td>
					<td class="table-column item_7" style="width:10.3cm !important"><div class="item_description item_7 add_border"> [Partial/Full payment]</div></td>
				</tr>
			</table> -->

			<!-- BANK, DATE, CHECK AND RECEIVED BY -->
<!-- 			<table class="or_details" style="margin-top:0">
				<tr>
					<td class="table-column" style="width:1.8cm !important"><div class="item_description add_border"></div></td>
					<td class="table-column" style="width:2.6cm !important"><div class="item_description add_border"> [Bank]</div></td>
					<td class="table-column" style="width:4.6cm !important"><div class="item_description add_border"> </div></td>
					<td class="table-column" style="width:5.3cm !important"><div class="item_description add_border"> </div></td>
				</tr>


			</table> -->
		</div>
	</div>
</body>
</html>