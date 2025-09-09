@extends('adminLTE.admin_template_frame')
@section('content')
<style type="text/css">
  
  html {
  box-sizing: border-box;
}
*,
*:before,
*:after {
  box-sizing: inherit;
}
.table-scroll {
  position: relative;
  width:100%;
  z-index: 1;
  margin: auto;
  overflow: auto;
/*  min-height: 300px;
  max-height: 600px;*/
  /*height: 600px;*/
}
.table-scroll table {
  width: 100%;
  min-width: 1280px;
  margin: auto;
  border-spacing: 0;
}
.table-wrap {
  position: relative;
}
.table-scroll th,
.table-scroll td {
  /*border:1px solid #000 !important;*/
  /*background: #fff;*/
  vertical-align: top;
}
.table-scroll th{
  font-size: 13px !important;
  font-family: Arial !important;
  padding: 2px;
}
.table-scroll td{
  padding: 2px;
  font-size: 13px !important;
  font-family: Arial !important;
}
.tbl_tran_summary td{
  padding: 2px;
  font-size: 13px !important;
  font-family: Arial !important;
}
.table-scroll thead th {
  background: #00264d;
  color: #fff;
  position: -webkit-sticky;
  position: sticky;
  top: 0;
}
.dark-mode .table-scroll thead th {
  background: #0047b3;
  color: #fff;
  position: -webkit-sticky;
  position: sticky;
  top: 0;
}

.subtotal_border{
  border-bottom: 2px solid;
}

/* safari and ios need the tfoot itself to be position:sticky also */
.table-scroll tfoot,
.table-scroll tfoot th,
.table-scroll tfoot td {
  position: -webkit-sticky;
  position: sticky;
  bottom: 0;
  background: #666;
  color: #fff;
  z-index:4;
}
.group_padding{
  padding-left: 12px !important;
}

  thead th:first-child,
  tfoot th:first-child {
    z-index: 5;
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
  .pad_2{
    padding-left: 15px !important;
  }
  .pad_3{
    padding-left: 30px !important;
  }
</style>
<div class="container-fluid">
    <h3>{{$client_details->name}} <span style="font-size: 15px;">(Acct. # {{ $client_details->account_no }})</span></h3>
    <p style="margin-top: -10px;">Cost Center(s): {{ ($cost_center == null)?"-":$cost_center->cost_center }}</p>
    <p style="margin-top: -10px;">{{ $client_details->address }}</p>
    <p style="margin-top: -10px;"><b>Billing Period:</b> {{$billing_period}}</p>
    <p style="margin-top: -10px;"><b>Total Amount Due: <span id="spn_amt"></span></b></p>
    <button class="btn bg-gradient-primary" type="button" id="btn_save_soa" disabled="">Generate SOA</button>
  <button class="btn bg-gradient-danger" type="button" id="btn_attachment" disabled="">View SOA Attachment</button>
  <button class="btn bg-gradient-success" type="button" id="btn_export_excel" disabled="">Export to Excel</button>
  <div class="col-md-12">
  <?php
    $g_total = array();
    if(count($sum_fields) > 0){
      foreach($sum_fields as $f){
        $g_total[$f] = 0;
      }
    }
  ?>
  <!-- <div class="table-responsive" > -->
  <div id="table-scroll" class="table-scroll" style="max-height: calc(100vh - 150px);overflow-y: auto;margin-top: 10px">
    <table class="table table-striped main-table tbl_transactions" style="white-space: nowrap;">
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
                  ?>
                  <td class="col_{{$dt}}">{{ $val }}</td>
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
    <?php
      $current_total = $grand_sum['total'];
      $taxable_amt = $current_total/1.12;
      $vat_amount = $current_total - $taxable_amt;
      // $prev_amount_due = $previous_amt_due;
    ?>
<div class="col-md-12">
  <table class="borderless tbl_tran_summary col-sm-10">
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
      <th class="group_padding" style="vertical-align: top;">Less: Payment  </th>
      <td>
        <?php $less_payment = 0;?>
        @if(count($payments) > 0)
          <table style="white-space: nowrap;width: 80%">
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
        <?php $subtotal = $previous_amt_due-$less_payment+$adjustments; ?>
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
        <th class="col_amount dbl_undline">{{number_format($total_amount_due,2)}}</th>
      </tr>
  </table>
</div>



</div>

@endsection
@push('scripts')
  <script type="text/javascript">
    $(document).ready(function(){
      if('<?php echo count($data_list) ?>' > 0){
        $('#btn_save_soa').attr('disabled',false);
        $('#btn_attachment').attr('disabled',false);
        $('#btn_export_excel').attr('disabled',false);
      }
      $('#spn_amt').text('<?php echo number_format($amount_due,2) ?? "0.00" ?>');
    })
    $('#btn_save_soa').click(function(){
      Swal.fire({
        title: 'Do you want to save the changes?',
        icon: 'warning',
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: `Save`,
      }).then((result) => {
        if (result.isConfirmed) {
          post_soa();
        } 
      // else if (result.isDenied) {
      //   Swal.fire('Changes are not saved', '', 'info')
      // }
      })  
    })
    function post_soa(){
      var form_data = jQuery.parseJSON('<?php echo $form_request;?>');
      console.log({form_data});
      $.ajax({
          type             :             'POST',
          url              :             '/admin/generate_soa/post',
          data             :             {'form_data' : form_data,  
                                          'actual_account_no' : '<?php echo $client_details->account_no; ?>'},
          beforeSend       :             function(){
                                          show_loader();
          },
          success          :             function(response){
                                          setTimeout(
                                          function() {
                                            hide_loader();
                                            if(response.message == "success"){
                                              Swal.fire({
                                                position: 'center',
                                                icon: 'success',
                                                title: 'SOA successfully saved !',
                                                showConfirmButton: false,
                                                timer: 1500
                                              }).then(function() {
                                                   parent.soa_saved(response.control_number);
                                              })
                                            }
                                          }, 1500);
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
    $('#btn_attachment').click(function(){
      // parent.show_option_attachment();
      parent.redirect_soa_attachment_preview();
    })
    $('#btn_export_excel').click(function(){
      var file_name = '{{$billing_period}}'+" "+'{{$client_details->account_no}}'+"_"+'{{$client_details->name}}';
          $(".tbl_transactions").table2excel({
   
        name: '{{$client_details->account_no}}',
        filename: file_name
      });
    })

  </script>
@endpush