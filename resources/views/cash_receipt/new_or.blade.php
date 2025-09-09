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
                margin-top: 0.6cm;
                margin-left: 0.2cm;
                padding: 0;
            }
        }
        #tn_box{
            width: 14cm;
            /*border: 1px solid black; */
            position: relative;
            /*background: blue;*/
            overflow: hidden;
            padding: 0;
            margin: 0;
            height: 7cm;
      /*      min-height: 7.4cm;
            max-height: 7.4cm;*/
        }
	    .column {
	        float: left;
	    }
	    #col-grp-1{
	        width: 4cm;
	        min-width: 4cm;
	        max-width: 4cm;
	        padding: 0;

	        margin: 0;
	        margin-right: 0.3cm;

	    }
	    #col-grp-2{
	        width: 9.7cm;
	        min-width: 9.7cm;
	        max-width: 9.7cm;
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
	        font-size: 13pt;
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
            width: 4cm;
        }
        .table_column_1_width_item{
            width: 1.9cm;
        }
        .table_column_2_width_item{
            width: 2.1cm;
        }
        .header_height{
            height: 1.4cm;
        }
	    td.table-column > div.item {
	        height: 0.5cm;
	        box-sizing: border-box;
	        white-space: nowrap;
	        text-overflow: ellipsis;
	        overflow: hidden;
	        font-size: 10pt;
	        
	        padding-top: 0.5mm;
	        
	    }
	    .item-paymode{
	    	height: 1cm !important;
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
		.item_62{
			height: 0.7cm !important;
			padding-top: 2mm !important;
		}
		.item_6_np{
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
		.f-item > div.item{
			font-size: 8pt !important;
		}
	</style>
	<style type="text/css">
		.or_details{
			width: 9.7cm;
		}
		.header_height2{
			height: 2.4cm;

		}
	</style>
	<style type="text/css">
/*		.add_border_main {
	        box-shadow:-1px 0 1px 1px rgba(0, 0, 0, 0.75), inset -1px 0 0 1px rgba(0, 0, 0, 0.75);
	    }
	    .add_border {
	        box-shadow:-1px 0 1px 1px rgba(0, 0, 0, 0.75), inset -1px 0 0 1px rgba(0, 0, 0, 0.75);
	    }
	    #col-grp-2 .add_border{
	    	box-shadow:  none !important;
	    }*/
	</style>
</head>

<!--  -->
<body onload="window.print();">
	<?php
		$item_height = ['hp5','hp5','hp5','hp5','hp55','hp55','hp5'];
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
		 			@for($i=1;$i<=7;$i++)
		 			<?php
		 				$add_class = $item_height[$i-1];
		 			?>
		 			<tr>
		 				 <td class="table-column f-item"><div class="item {{$add_class}} table_column_1_width_item add_border"><?php echo isset($cash_receipt_details[$i-1])?$cash_receipt_details[$i-1]->payment_description:"" ?></div></td>
		 				 <td class="table-column f-item"><div class="item {{$add_class}} table_column_2_width_item add_border text-right amt"><?php echo isset($cash_receipt_details[$i-1])?number_format($cash_receipt_details[$i-1]->amount,2):"&nbsp;" ?></div></td>
		 			</tr>
		 			@endfor

					<tr>
		 				 <td class="table-column f-item"><div class="item hp5 table_column_1_width_item add_border"></div></td>
		 				 <td class="table-column f-item"><div class="item hp5 table_column_2_width_item add_border text-right amt">&nbsp;</div></td>
		 			</tr>	
					<tr>
		 				 <td class="table-column f-item"><div class="item hp5 table_column_1_width_item add_border"></div></td>
		 				 <td class="table-column f-item"><div class="item hp5 table_column_2_width_item add_border text-right amt">&nbsp;{{number_format($cash_receipt->total_payment,2)}}</div></td>
		 			</tr>
		 		</tbody>

		 	</table>
		 	<table class="table_width add_border" style="" border="0" cellspacing="0" cellpadding="0">
		 		<tr>
		 				<!-- cash -->
		 				<td class="table-column" style="width:3cm !important;"><div class="item add_border item-paymode" style="width:3cm !important;padding-left: 8.5mm;padding-top: 5mm;">{{($cash_receipt->id_paymode==1)?'x':''}}</div></td>
		 				<!-- bank -->
		 				<td class="table-column"><div class="item add_border item-paymode" style="padding-left:0.5mm;padding-top: 1.5mm;">{{($cash_receipt->id_paymode==3)?'x':''}}</div></td>
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
		 				 <td class="table-column" style="width:6.4cm" colspan="2"><div class="item item_62 add_border"></div></td>
		 				 <td class="table-column" style="width:3.3cm"><div class="item add_border item_62">{{$cash_receipt->transaction_date}}</div></td>
		 			</tr>

		 			<!-- Received From -->
		 			<tr>
		 				 <!-- <td class="table-column" style="width:2.2cm"><div class="item item_6 add_border"></div></td> -->
		 				 <td class="table-column" colspan="3"><div class="item add_border item_6" style="padding-left: 2.3cm;">{{$cash_receipt->member_name}}</div></td>
		 			</tr>

		 			<!-- With TIN -->
		 			<tr>
		 				 <td class="table-column" colspan="3"><div class="item add_border item_55" style="padding-left: 1.5cm;">{{$cash_receipt->tin}}</div></td>
		 			</tr>	

		 			<!-- Address   -->
		 			<tr>
		 				 <td class="table-column" colspan="3"><div class="item item_55 add_border" style="padding-left: 2.6cm;">{{$cash_receipt->address}}</div></td>
		 			</tr>	 			

		 			<!-- Engaged in -->
		 			<tr>
		 				 <td class="table-column" colspan="3"><div class="item item_6 add_border" style="padding-left: 5.2cm;">{{$cash_receipt->engaged_in}}</div></td>
		 			</tr>	

		 			<!-- Sum of -->
		 			<tr>
		 				 <td class="table-column" colspan="3"><div class="item add_border item_55" style="padding-left: 1.8cm;" id="sum_text_worded"></div></td>
		 			</tr>	

		 			<!-- Sum of word and amount-->
		 			<tr>
		 				 <td class="table-column" colspan="2" style="width:7.3cm !important"><div class="item item_55 add_border"></div></td>
		 				 <td class="table-column" ><div class="item item_55 add_border" id="sum_number_text"></div></td>
		 			</tr>	

		 			<!-- In partial -->
		 			<tr>
		 				 <td class="table-column" colspan="3"><div class="item add_border item_6" style="padding-left: 4.6cm;"></div></td>
		 			</tr>

			</table>

			<table class="or_details add_border" style="margin-top: 0.2cm;display: none;" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="table-column" style="width:1.8cm !important"><div class="add_border item"></div></td>
					<td class="table-column" style="width:2.6cm !important">
						<div class="add_border item">
						<!-- [Bank] -->
						</div>
					</td>
					<td class="table-column" style="width:4.6cm !important"><div class="add_border item"> </div></td>
					<td class="table-column" style="width:5.3cm !important"><div class="add_border item"></div></td>
				</tr>
			<tr>
					<td class="table-column" style="width:1.8cm !important"><div class="add_border item item_4"></div></td>
					<td class="table-column" style="width:2.6cm !important">
						<div class="add_border item item_4">
							<!-- [Date] -->
						</div>
					</td>
					<td class="table-column" style="width:4.6cm !important"><div class="add_border item item_4"> </div></td>
					<td class="table-column" style="width:5.3cm !important"><div class="add_border item item_4"></div></td>
				</tr>
				<tr>
					<td class="table-column" style="width:1.8cm !important"><div class="add_border item item_4"></div></td>
					<td class="table-column" style="width:2.6cm !important">
						<div class="add_border item item_4">
							<!-- [Check no] -->
						</div>
					</td>
					<td class="table-column" style="width:4.6cm !important"><div class="add_border item item_4"> </div></td>
					<td class="table-column" style="width:5.3cm !important">
						<div class="add_border item item_4">
							<!-- [Received by]  -->
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
<script type="text/javascript">
// System for American Numbering 
var th_val = ['', 'thousand', 'million', 'billion', 'trillion'];
// System for uncomment this line for Number of English 
// var th_val = ['','thousand','million', 'milliard','billion'];
 
var dg_val = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
var tn_val = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
var tw_val = ['twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
function toWordsconver(s) {
  s = s.toString();
    s = s.replace(/[\, ]/g, '');
    if (s != parseFloat(s))
        return 'not a number ';
    var x_val = s.indexOf('.');
    if (x_val == -1)
        x_val = s.length;
    if (x_val > 15)
        return 'too big';
    var n_val = s.split('');
    var str_val = '';
    var sk_val = 0;
    for (var i = 0; i < x_val; i++) {
        if ((x_val - i) % 3 == 2) {
            if (n_val[i] == '1') {
                str_val += tn_val[Number(n_val[i + 1])] + ' ';
                i++;
                sk_val = 1;
            } else if (n_val[i] != 0) {
                str_val += tw_val[n_val[i] - 2] + ' ';
                sk_val = 1;
            }
        } else if (n_val[i] != 0) {
            str_val += dg_val[n_val[i]] + ' ';
            if ((x_val - i) % 3 == 0)
                str_val += 'hundred ';
            sk_val = 1;
        }
        if ((x_val - i) % 3 == 1) {
            if (sk_val)
                str_val += th_val[(x_val - i - 1) / 3] + ' ';
            sk_val = 0;
        }
    }
    if (x_val != s.length) {

        var y_val = s.length;
        console.log({y_val});
        str_val += 'and '+s.split('.')[1]+'/100';
        // alert()
        // for (var i = x_val + 1; i < y_val; i++)
        //     str_val += dg_val[n_val[i]] + ' ';
    }
    
    return str_val.replace(/\s+/g, ' ');
}

var amount = parseFloat('<?php echo $cash_receipt->total_payment; ?>');  

var Inwords = toWordsconver(amount);

document.getElementById("sum_text_worded").innerHTML = Inwords;
document.getElementById("sum_number_text").innerHTML = '<?php echo number_format($cash_receipt->total_payment,2); ?>';

// alert(Inwords);
</script>
</body>
</html>