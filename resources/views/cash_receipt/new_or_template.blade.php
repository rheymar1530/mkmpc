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
      /*      min-height: 7.4cm;
            max-height: 7.4cm;*/
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
	        font-size: 12pt;
	        padding-top: 1mm !important;
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
	        font-size: 7pt;
	        
	        padding-top: 0.5mm;
	        
	    }
	    .item_3{
	    	height: 0.3cm !important;
	    }
		.item_4{
			height: 0.4cm !important;
			padding-top: 0mm !important;
		
		}
		.item_55{
			height: 0.55cm !important;
/*			padding-top: 0mm !important;*/
		
		}
		.item_7{
			height: 0.7cm !important;
			padding-top: 2mm !important;
		}
		.item_7_np{
			height: 0.7cm !important;
			padding-top: -5mm;
		
		}
		.item_6{
			height: 0.6cm !important;
			padding-top: 1mm !important;
		}

		.item_8{
			height: 0.8cm !important;
			padding-top: 2mm !important;
		}
		.amt{
			padding-right: 1mm !important;
		}
		.pmode{
			height: 1.5cm;
		}

		.hp4{
			height: 0.4cm !important;
			padding-top: 1mm !important;
		}
		.hp45{
			height: 0.45cm !important;
			padding-top: 1mm !important;
		}
		.hp5{
			height: 0.5cm !important;
			padding-top: 1mm !important;
		}
		.hp55{
			height: 0.55cm !important;
			padding-top: 1mm !important;
		}
	</style>
	<style type="text/css">
		.or_details{
			width: 14.3cm;
		}
		.header_height2{
			height: 1.3cm;

		}
	</style>
	<style type="text/css">
		.add_border_main {
	        box-shadow:-1px 0 1px 1px rgba(0, 0, 0, 0.75), inset -1px 0 0 1px rgba(0, 0, 0, 0.75);
	    }
	    .add_border {
	        box-shadow:-1px 0 1px 1px rgba(0, 0, 0, 0.75), inset -1px 0 0 1px rgba(0, 0, 0, 0.75);
	    }
/*	    #col-grp-2 .add_border{
	    	box-shadow:  none !important;*/
	    }*/
	</style>
</head>
<body>
	<?php
		$item_height = ['hp5','hp5','hp45','hp5','hp45','hp5','hp45','hp5','hp5','hp55'];
	?>
	<div class="row add_border_main" id="tn_box">
		<div class="column border-right" id="col-grp-1" style="padding:0px !important;">
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
		 				$add_class = $item_height[$i-1];
		 			?>
		 			<tr>
		 				 <td class="table-column"><div class="item {{$add_class}} table_column_1_width_item add_border text-center">Particular {{$i}}</div></td>
		 				 <td class="table-column"><div class="item {{$add_class}} table_column_2_width_item add_border text-right amt">{{number_format($i*100,2)}}</div></td>
		 			</tr>
		 			@endfor	
		 		</tbody>
	<!-- 		        <tr>
			            <th class="table_column_1_width_item add_border"><div class="pmode"></div></th>
			            <th class="table_column_2_width_item add_border"><div class="pmode"></div></th>
			        </tr> -->
		 	</table>
		 	<table class="table_width add_border" style="" border="0" cellspacing="0" cellpadding="0">
		 		<tr>
		 				<!-- cash -->
		 				<td class="table-column" style="width:1.5cm !important;"><div class="item add_border" style="width:1.5cm !important;padding-left: 1mm;padding-top: 2mm;">x</div></td>
		 				<!-- bank -->
		 				<td class="table-column"><div class="item add_border" style="padding-left:0.5mm;padding-top: 2mm;">x</div></td>
		 		</tr>
		 		<tr>
		 				<!-- cash -->
		 				<td class="table-column" style="width:1.5cm !important;"><div class="item item_3 add_border"></div></td>
		 				<!-- bank -->
		 				<td class="table-column"><div class="item add_border item_3"></div></td>
		 		</tr>
		 		<tr>
		 				<!-- cash -->
		 				<td class="table-column" style="width:1.5cm !important;"><div class="item item_7_np add_border" style="width:1.5cm !important;padding-left: 1mm;">x</div></td>
		 				<!-- bank -->
		 				<td class="table-column"><div class="item add_border item_7_np" style="padding-left:0.5mm;padding-top: -10mm !important;">x</div></td>
		 		</tr>
		 	</table>
		</div>
		<div class="column border-right add_border" id="col-grp-2" style="padding:0px !important">
			<table class="or_details add_border" style="" border="0" cellspacing="0" cellpadding="0">
					<tr>
			            <th class="add_border" colspan="3"><div class="header_height2"></div></th>
			        </tr>
			        <!-- Date -->
			        <tr>
		 				 <td class="table-column" style="width:10.5cm" colspan="2"><div class="item item_7 add_border"></div></td>
		 				 <td class="table-column" style="width:3.8cm"><div class="item add_border item_7">[Date]</div></td>
		 			</tr>

		 			<!-- Received From -->
		 			<tr>
		 				 <!-- <td class="table-column" style="width:2.2cm"><div class="item item_6 add_border"></div></td> -->
		 				 <td class="table-column" colspan="3"><div class="item add_border item_6" style="padding-left: 2.2cm;">[Received From]</div></td>
		 			</tr>

		 			<!-- With TIN -->
		 			<tr>
		 				 <td class="table-column" colspan="3"><div class="item add_border" style="padding-left: 1.3cm;">[TIN]</div></td>
		 			</tr>	

		 			<!-- Address   -->
		 			<tr>
		 				 <td class="table-column" colspan="3"><div class="item item_55 add_border" style="padding-left: 2.5cm;">[Address]</div></td>
		 			</tr>	 			

		 			<!-- Engaged in -->
		 			<tr>
		 				 <td class="table-column" colspan="3"><div class="item item_55 add_border" style="padding-left: 1.8cm;">[Engaged in]</div></td>
		 			</tr>	

		 			<!-- Sum of -->
		 			<tr>
		 				 <td class="table-column" colspan="3"><div class="item add_border" style="padding-left: 1.8cm;">[Sum of (word)]</div></td>
		 			</tr>	

		 			<!-- Sum of word and amount-->
		 			<tr>
		 				 <td class="table-column" colspan="2" style="width:10cm !important"><div class="item item_6 add_border"></div></td>
		 				 <td class="table-column" ><div class="item item_6 add_border">[Amount in number]</div></td>
		 			</tr>	

		 			<!-- In partial -->
		 			<tr>
		 				 <td class="table-column" colspan="3"><div class="item add_border item_6" style="padding-left: 4cm;">[In Partial/full]</div></td>
		 			</tr>

			</table>

			<table class="or_details add_border" style="margin-top: 0.2cm;" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="table-column" style="width:1.8cm !important"><div class="add_border item"></div></td>
					<td class="table-column" style="width:2.6cm !important"><div class="add_border item">[Bank]</div></td>
					<td class="table-column" style="width:4.6cm !important"><div class="add_border item"> </div></td>
					<td class="table-column" style="width:5.3cm !important"><div class="add_border item"></div></td>
				</tr>
			<tr>
					<td class="table-column" style="width:1.8cm !important"><div class="add_border item item_4"></div></td>
					<td class="table-column" style="width:2.6cm !important"><div class="add_border item item_4"> [Date]</div></td>
					<td class="table-column" style="width:4.6cm !important"><div class="add_border item item_4"> </div></td>
					<td class="table-column" style="width:5.3cm !important"><div class="add_border item item_4"></div></td>
				</tr>
				<tr>
					<td class="table-column" style="width:1.8cm !important"><div class="add_border item item_4"></div></td>
					<td class="table-column" style="width:2.6cm !important"><div class="add_border item item_4"> [Check no]</div></td>
					<td class="table-column" style="width:4.6cm !important"><div class="add_border item item_4"> </div></td>
					<td class="table-column" style="width:5.3cm !important"><div class="add_border item item_4">[Received by] </div></td>
				</tr>

		

		</div>
	</div>
</body>
</html>