<style type="text/css">
    .tbl_loans tr>th ,.tbl_fees tr>th,.tbl_repayment_display tr>th{
        padding: 5px;
        padding-left: 5px;
        padding-right: 5px;
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 14px;
        text-align: center;
    }
    .tbl_loans tr>td,.tbl_fees tr>td,.tbl_repayment_display tr>td{
        padding: 0px 2px 0px 2px;
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 15px;
    }
    .frm_loans,.frm-requirements{
        height: 27px !important;
        width: 100%;    
        font-size: 13px;
    }
    .class_amount{
        text-align: right;
    }
    .cus-font{
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 15px !important;       
    }
    .form-row  label{
        margin-bottom: unset !important;
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 15px;
    }
    .form-label{
        margin-bottom: 4px !important;
    }

    .modal-conf {
        max-width:98% !important;
        min-width:98% !important;

    }
    .text_center{
        text-align: center;
    }
    .text_bold{
        font-weight: bold;
    }
    .spn_t{
        font-weight: bold;
        font-size: 16px;
    }
    .spn_txt{
        word-wrap:break-word;
        overflow: hidden;
        text-align: right;
    }
    .label_totals{
        margin-top: -13px !important;
    }
    #change_modal{
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
    }
</style>
<div class="modal fade" id="change_modal"  role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-conf" >
        <div class="modal-content">
            <form id="frm_repayment">
                <div class="modal-body">
                    <div class="row">
                        <h3>Change Release</h3>
                        <div class="form-group col-md-12" style="margin-bottom:unset;">
                            <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                                <table class="table table-bordered table-stripped table-head-fixed tbl_loans" style="white-space: nowrap;" id="tbl_change_post">
                                    <thead>
                                        <tr>
                                            <th class="table_header_dblue">ID Loan Payment</th>
                                            <th class="table_header_dblue">Transaction Date</th>
                                            <th class="table_header_dblue">Payee</th>
                                            <th class="table_header_dblue">Loan Reference</th>
                                            <th class="table_header_dblue">Swiping Amount</th>
                                            <th class="table_header_dblue">Total Amount Paid</th>
                                            <th class="table_header_dblue">Change</th>
                                            <th class="table_header_dblue">Change Released</th>
                                            <th class="table_header_dblue">Remaining Change</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loan_dues_body">

                                    </tbody>
                                </table>    
                            </div>  
                        </div>

  
              
                    </div>
                </div>
                <div class="modal-footer" style="padding:5px;padding-left: 10px;">
                  <button class="btn bg-gradient-success" id="btn_save">Save</button>

                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
          </form>
      </div>
  </div>
</div>
@push('scripts')
<script type="text/javascript">

</script>
@endpush

