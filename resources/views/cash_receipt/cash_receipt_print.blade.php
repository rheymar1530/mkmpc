<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @page {
           /* margin-top: 0;
            padding: 0;*/
            /*margin-top: 1cm;*/
            /* margin-right: 0.3cm; */
           /* margin-left: 0.8cm;
            width: auto;*/
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
            width: 11.9cm;
            /*border: 1px solid black; */
            position: relative;
            /*background: blue;*/
            overflow: hidden;
            padding: 0;
            margin: 0;
        }
        .table_width{
            width: 11.9cm;
        }
        .table_column_1_width_item{
            width: 2.1cm;
        }
        .table_column_2_width_item{
            width: 7.2cm;
        }
        .table_column_3_width_item{
            width: 2.6cm;
        }

        .header_height{
            height: 0.6cm;
        }
        td, th {
          padding: 0px !important;
          /*border-collapse: collapse;*/

          /*border-bottom: 1px solid;*/
      }
      .add_border {
            /*box-shadow:-1px 0 1px 1px rgba(0, 0, 0, 0.75), inset -1px 0 0 1px rgba(0, 0, 0, 0.75);*/
     }
           .add_border_main {
            /*box-shadow:-1px 0 1px 1px rgba(0, 0, 0, 0.75), inset -1px 0 0 1px rgba(0, 0, 0, 0.75);*/
     }
      table {
          width: 100%;
          border-collapse: collapse !important;
          /*border-spacing: 0 !important;*/
      }
      td.table-column > div.item {
        height: 0.5cm;
        box-sizing: border-box;
        /*background-color: lightpink;*/
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        font-size: 12px;
         
        /*display: table-cell;
        vertical-align: bottom;
        padding-bottom: 1mm;*/

        /*outline: 1px;*/
      }
      .item_2{
        height: 0.6cm !important;
        /*background-color: red;*/
      }
      .item_description{
        height: 0.5cm ;
        /*background-color: lightpink;*/
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        box-sizing: border-box;
        font-size: 12px;
        padding-top: 1mm !important;
        padding-left: 1mm;
       /* display: table-cell !important;
        vertical-align: bottom !important;
        padding-bottom: 0.5mm;
        padding-left: 1mm;*/
      }
    .item_2_description{
        height: 0.4cm !important;
        /*background-color: blue;*/
      }

      /*DATE*/
      .date_space{
         width: 9.2cm;
      }
      .date_text{
        width: 2.7cm;
      }
      /*RECEIVED FROM*/
      .received_from_label{
        width: 2.6cm;
      }
      .receved_from_text{
        width: 9.3cm;
      }

      /*ADDRESS AND TIN*/
      .address_label{
        width: 2.6cm;
      }
      .address_fill{
        width: 6.1cm;
      }
      .tin_label{
        width: 0.9cm;
      }
      .tin_text{
        width: 2.3cm;
      }

      /*Engaged in*/
      .engaged_in_label{
        width: 2cm;
      }
      .engaged_in_text{
        width: 9.9cm;
      }

      /*SUM OF */
      .sum_of_label{
        width: 1.9cm;
      }
      .sum_of_text{
        width: 10cm;
      }
      /*AMOUNTS*/
      .sec_amount{
        width: 3.6cm;
        padding-left: 5mm;
      }
      .as_payment_label{
        width: 5.1cm;
      }
      .as_payment_text{
        width: 3.2cm;
      }
      .class_amount{
        text-align: right;
        padding-right: 2mm !important;
      }
      .text-center{
        text-align: center;
      }

        /*AMOUNT IN WORDS*/
      .amount_words_space{
         width: 3.4cm;
      }
      .amount_words_text{
        width: 8.5cm;
      }

      /*FORM OF PAYMENT*/
      .form_payment_space{
        width: 4cm;
      }
      .form_payment_text{
        width: 3.2cm;
      }
      .form_payment_space2{
        width: 4.7cm;
      }
      /*AMOUNT RECEIVED*/
      .amt_rec_space{
        width: 8cm;
      }
      .amt_rec_text{
        width: 3.9cm;
      }
      .green{
        background-color: green !important;
      }
      .blue{
        background-color: blue !important;
      }
</style>
</head>
<body onload="window.print();">
<!-- <body> -->
    <div class="row add_border_main" id="tn_box">
        <!-- DATE -->
        <table class="table_width">
            <tr>
                <td class="date_space"><div class="item_description date_space add_border"></div></td>
                <td class="date_text"><div class="date_text item_description add_border">{{$cash_receipt->transaction_date}}</div></td>
            </tr>
        </table>
        <!-- RECEIVED FROM -->
        <table class="table_width" style="margin-top:4mm">
            <tr>
                <td class="received_from_label"><div class="item_description received_from_label add_border"></div></td>
                <td class="receved_from_text"><div class="receved_from_text item_description add_border">{{$cash_receipt->member_name}}</div></td>
            </tr>
        </table>
        <!-- ADDRESS AND TIN -->
        <table class="table_width">
            <tr>
                <td class="address_label"><div class="item_description address_label add_border"></div></td>
                <td class="address_fill"><div class="address_fill item_description add_border">{{$cash_receipt->address}}</div></td>
                <td class="tin_label"><div class="tin_label item_description add_border"></div></td>
                <td class="tin_text"><div class="tin_text item_description add_border">{{$cash_receipt->tin}}</div></td>
            </tr>
        </table>
        <!-- ENGAGED IN -->
        <table class="table_width">
            <tr>
                <td class="engaged_in_label"><div class="item_description item_2_description engaged_in_label add_border"></div></td>
                <td class="engaged_in_text"><div class="engaged_in_text item_2_description item_description add_border"></div></td>
            </tr>
        </table>

        <!-- The sum of amount (worded amount) -->
        <table class="table_width">
            <tr>
                <td class="sum_of_label"><div class="item_description sum_of_label add_border"></div></td>
                <td class="sum_of_text"><div class="sum_of_text item_description add_border" id="sum_text_worded"></div></td>
            </tr>
        </table>
        <!-- AMOUNT / PAYMENT FOLLOWING -->
        <table class="table_width">
            <tr>
                <td class="sec_amount"><div class="item_description sec_amount add_border" id="sum_number_text">123</div></td>
                <td class="as_payment_label"><div class="as_payment_label item_description add_border"></div></td>
                <td class="as_payment_text"><div class="as_payment_text item_description add_border">{{$cash_receipt->payment_remarks}}</div></td>
            </tr>
        </table>
        <!-- Items -->
        <table class="table_width add_border" style="margin-top:1.5mm;height: 7.6cm;" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <th class="table_column_1_width_item add_border"><div class="header_height"></div></th>
            <th class="table_column_2_width_item add_border"><div class="header_height"></div></th>
            <th class="table_column_3_width_item add_border"><div class="header_height"></div></th>
        </tr>
        @for($i=0;$i<12;$i++)
        <?php
            $add_class = ($i >=9)?"item_2":"";
            $color = ($i%2 == 0)?"green":"blue";
        ?>

        <tr class="">
            <td class="table-column"><div class="item {{$add_class}} table_column_1_width_item add_border text-center"><?php echo isset($cash_receipt_details[$i])?($i+1):'' ?></div></td>
            <td class="table-column"><div class="item {{$add_class}} table_column_2_width_item add_border">{{$cash_receipt_details[$i]->payment_description ?? ''}}</div></td>
            <td class="table-column"><div class="item {{$add_class}} table_column_3_width_item add_border class_amount">
                <?php echo isset($cash_receipt_details[$i]->amount)?number_format($cash_receipt_details[$i]->amount,2):''; ?>
            </div></td>
        </tr>
        @endfor
        <tr>
            <td class="table-column"><div class="item table_column_1_width_item add_border text-center" style="height:0.7cm"></div></td>
            <td class="table-column"><div class="item table_column_2_width_item add_border" style="height:0.7cm"></div></td>
            <td class="table-column"><div class="item table_column_3_width_item add_border class_amount" style="height:0.7cm;font-weight: bold;">
                {{number_format($cash_receipt->total_payment,2)}}
            </div></td>
        </tr>
    </table>

    <!-- AMOUNT IN WORDS -->
    <table class="table_width" style="margin-top:2mm">
        <tr>
            <td class="amount_words_space"><div class="item_description amount_words_space add_border"></div></td>
            <td class="amount_words_text"><div class="amount_words_text item_description add_border" id="amt_words"></div></td>
        </tr>
    </table>
    <!-- FORM PAYMENT -->
    <table class="table_width" style="margin-top:0.85cm">
        <tr>
            <td class="form_payment_space"><div class="item_description form_payment_space add_border"></div></td>
            <td class="form_payment_text"><div class="form_payment_text item_description add_border">{{($cash_receipt->id_paymode == 1?'x':'')}}</div></td>
            <td class="form_payment_space2"><div class="form_payment_space2 item_description add_border"></div></td>
        </tr>
        <tr>
            <td class="form_payment_space"><div class="item_description form_payment_space add_border"></div></td>
            <td class="form_payment_text"><div class="form_payment_text item_description add_border">{{($cash_receipt->id_paymode == 2?'x':'')}}</div></td>
            <td class="form_payment_space2"><div class="form_payment_space2 item_description add_border"></div></td>
        </tr>
        <tr>
            <td class="form_payment_space"><div class="item_description form_payment_space add_border"></div></td>
            <td class="form_payment_text"><div class="form_payment_text item_description add_border">{{($cash_receipt->id_paymode > 2?'x':'')}}</div></td>
            <td class="form_payment_space2"><div class="form_payment_space2 item_description add_border"></div></td>
        </tr>
    </table>

    <!-- AMOUNT RECEIVED -->
        <table class="table_width">
        <tr>
            <td class="amt_rec_space"><div class="item_description amt_rec_space add_border"></div></td>
            <td class="amt_rec_text"><div class="amt_rec_text item_description add_border">{{number_format($cash_receipt->total_payment,2)}}</div></td>
        </tr>
    </table>






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
document.getElementById("amt_words").innerHTML = Inwords;
document.getElementById("sum_number_text").innerHTML = '<?php echo number_format($cash_receipt->total_payment,2); ?>';



// alert(Inwords);
</script>
</body>
</html>