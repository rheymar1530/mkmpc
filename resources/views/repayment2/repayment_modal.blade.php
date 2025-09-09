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
    #repayment_modal{
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
    }
</style>
<div class="modal fade" id="repayment_modal"  role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-conf" >
        <div class="modal-content">
            <form id="frm_repayment">
                <div class="modal-body">
                    <div class="row">
                        <!-- NET PAY -->
                        <div class="col-md-12">
                            <div class="form-row">
                                <div class="form-group col-md-5" style="margin-top:20px">
                                    <label for="txt_description">Select Borrower</label>
                                    <select class="form-control select2" id="sel_borrower"></select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12" style="margin-bottom:unset;">
                            <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                                <table class="table table-bordered table-stripped table-head-fixed tbl_loans" style="white-space: nowrap;">
                                    <thead>
                                        <tr>
                                            <th class="table_header_dblue" width="2%"></th>
                                            <th class="table_header_dblue">Loan Service</th>
                                            <th class="table_header_dblue">Principal</th>
                                            <th class="table_header_dblue">Interest Rate</th>
                                            <th class="table_header_dblue">Terms</th>
                                            <!-- <th class="table_header_dblue">Loan Fees</th> -->
                                            <th class="table_header_dblue">Loan Amount</th>
                                            <th class="table_header_dblue">No of Loan Payment made</th>
                                            <th class="table_header_dblue">Total Amount Paid</th>
                                            <th class="table_header_dblue">Loan Balance </th>
                                            <th class="table_header_dblue">Amount Due</th>
                                            <th class="table_header_dblue">Payable Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loan_dues_body">

                                    </tbody>
<!--                                     <tfoot>
                                        <tr>
                                            <td class="text_bold" colspan="10" style="padding-top: 3px;">Total Loan Due Payment</td>
                                            <td><input type="text" name="" required class="form-control frm_loans class_amount" value="0.00" disabled></td>
                                        </tr>
                                    </tfoot> -->
                                </table>    
                            </div>  
                        </div>
                        <!-- Fees -->
                        <div class="col-md-5">
                            <fieldset class="border" style="padding-left: 10px;padding-right: 10px;">
                                <legend class="w-auto p-1">Loan Payment Fee(s) &nbsp;<button class="btn btn-sm bg-gradient-primary" type="button" onclick="add_fees()"><i class="fas fa-plus"></i>&nbsp;Add</button></legend>
                                <div class="col-md-12">
                                    <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                                        <table class="table table-bordered table-stripped table-head-fixed tbl_loan_req tbl_fees" style="white-space: nowrap;">
                                            <thead>
                                                <tr>
                                                    <th class="table_header_dblue">Fee</th>
                                                    <th class="table_header_dblue" width="200px">Amount</th>
                                                    <th class="table_header_dblue" width="25px"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="fee_body">
                                                <tr class="row_fee_fix repayment_fees">
                                                    <td><select class="form-control p-0 sel_fees frm_loans" disabled>
                                                        <option value="1">Swiping Fee</option>
                                                    </select></td>
                                                    <td><input type="text" name="" required class="form-control class_amount frm_loans txt_fee_amount" value="25.00" key="amount"></td>
                                                    <td class="text_center"></td>                                               
                                                </tr>
                                                <tr class="row_fee repayment_fees">
                                                    <td><select class="form-control p-0 sel_fees frm_loans">
                                                        @foreach($repayment_fee_type as $rf)
                                                        <option value="{{$rf->id_repayment_fee_type}}">{{$rf->description}}</option>
                                                        @endforeach
                                                    </select></td>
                                                    <td><input type="text" name="" required class="form-control class_amount frm_loans txt_fee_amount" value="0.00" key="amount"></td>
                                                    <td class="text_center"><a onclick="remove_fee(this)" style="margin-top: 10px !important;" class="frm-other-lending"><i class="fa fa-times"></i></a></td>
                                                </tr>
                                            </tbody>
                                        </table>    
                                    </div>  
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-12">
                            <div class="form-row">
                                <div class="form-group col-md-2" style="margin-top:20px">
                                    <label for="txt_description">Swiping Amount</label>
                                    <input type="text" name="" required class="form-control class_amount" id="swiping_amount" value="0.00">
                                </div>
                            </div>
                        </div>  
                        <div class="col-md-5">
                            <table style="width:100%">
                                <tr>
                                    <td width="34%" class="spn_t">Total Loan Amount Due:</td>
                                    <td class="class_amount" width="30%" id="spn_total_loan_amount_due"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td width="34%" class="spn_t">Total Loan Fees:</td>
                                    <td class="class_amount" width="30%" id="spn_total_loan_fees">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td width="34%" class="spn_t">Total Amount Due:</td>
                                    <td class="class_amount" width="30%" id="spn_total_amount_due">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td width="34%" class="spn_t">Total Paid Amount:</td>
                                    <td class="class_amount" width="30%" id="spn_total_paid_amount">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td width="34%" class="spn_t">Change:</td>
                                    <td class="class_amount" width="30%" id="spn_change">0.00</td>
                                    <td></td>
                                </tr>
                            </table>
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
    $fee_row = '<tr class="row_fee repayment_fees">'+$('tr.row_fee').html()+'</tr>';
    $fee_fix = '<tr class="row_fee_fix repayment_fees">'+$('tr.row_fee_fix').html()+'</tr>';
    $('tr.row_fee').remove();
    clear_loan_dues();
    let total_loan_dues = 0;
    let current_dues = [];
    let is_edit = 0;
    let temp_repayme
    // $('.frm-requirements').attr('required',false);
    compute_fee_total();
    compute_total_amount_due();
    var payable_amount = {};
    function append_lender(){
        $('#other-lending-body').append($lender_row);
    }
    function remove_other_lending(obj){
        var parent_row = $(obj).closest('tr.row_other_lending');
        parent_row.remove()
    }
    $(document).on('select2:open', (e) => {
        const selectId = e.target.id

        $(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
            key,
            value,
            ) {
            value.focus()
        })
    })  
    $('.frm-other-lending').attr('disabled',true)
    intialize_select2();
    function intialize_select2(){       
        $("#sel_borrower").select2({
            minimumInputLength: 2,
            width: '100%',
            createTag: function (params) {
                return null;
            },
            ajax: {
                tags: true,
                url: '/search_member',
                dataType: 'json',
                type: "GET",
                quietMillis: 1000,
                data: function (params) {
                    var queryParameters = {
                        term: params.term
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    console.log({data});
                    return {
                        results: $.map(data.accounts, function (item) {
                            return {
                                text: item.tag_value,
                                id: item.tag_id
                            }
                        })
                    };
                }
            }
        });
    }
    function add_fees(){
        $('#fee_body').append($fee_row);
    }
    function remove_fee(obj){
        var parent_row = $(obj).closest('tr.row_fee');
        parent_row.remove();
    }
    $(document).on("focus",".class_amount",function(){
        var val = $(this).val();
        if(val == '' || val == 'NaN'){
            val = '0.00';
        }
        $(this).val(decode_number_format(val)); 
    })
    $(document).on("blur",".class_amount",function(){
        var val = $(this).val();
        if(!$.isNumeric(val)){
            val = 0;
        }
        $(this).val(number_format(parseFloat(val)));
    })
    function parseMemberLoanDues($id_member){
        $.ajax({
            type          :         'GET',
            url           :         '/repayment/member/loan_dues',
            data          :          {'id_member':$id_member},
            beforeSend    :          function(){
                show_loader();
            },
            success       :          function(response){
                console.log({response});
                // current_repayment_token 
                clear_form();
                temp_repayment_token = response.member_info.member_code;
                display_loan_dues(response.loan_dues)

                hide_loader();
            },error: function(xhr, status, error) {
                hide_loader()
                var errorMessage = xhr.status + ': ' + xhr.statusText;
                Swal.fire({
                    title: "Error-" + errorMessage,
                    text: '',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: "#DD6B55"
                });
            }
        })
    }
    $(document).on('change','#sel_borrower',function(){
        var val = $(this).val();
        if(val != null){
            parseMemberLoanDues(val);
        }
    })
    function display_loan_dues(dues){
        current_dues = dues;
        total_loan_dues = 0;
        var out = "";
        $.each(dues,function(i,item){
            console.log({item})
            out += '<tr class="row_loan_dues" loan-token="'+item.loan_token+'">';
            out += '<td class="text_center text_bold">'+(i+1)+'</td>';
            out += '<td class="text_bold" key="loan_service_name">'+item.loan_service_name+'</td>';
            out += '<td class="class_amount" key="principal_amount">'+number_format(parseFloat(item.principal_amount))+'</td>';
            out += '<td class="text_center" key="interest_rate">'+item.interest_rate+'%</td>';
            out += '<td key="terms">'+item.terms+'</td>';
            out += '<td class="class_amount" key="loan_amount">'+number_format(parseFloat(item.loan_amount))+'</td>';
            out += '<td class="text_center" key="repayment_made">'+item.repayment_made+'</td>';
            out += '<td class="class_amount" key="total_amount_paid">'+number_format(parseFloat(item.total_amount_paid))+'</td>';
            
            out += '<td class="class_amount" key="loan_balance">'+number_format(parseFloat(item.loan_balance))+'</td>';
            out += '<td class="class_amount" key="amount_due">'+number_format(parseFloat(item.amount_due))+'</td>';
            out += '<td><input type="text" name="" required class="form-control frm_loans class_amount txt_amount_paid" value="'+number_format((item.payable_amount ?? 0))+'" key="amount_paid"></td>'
            out += '</tr>';
            total_loan_dues += parseFloat(item.amount_due);
            payable_amount[item.loan_token] = item.amount_due;
            // payable_amount
        })
        $('#loan_dues_body').html(out);
        $('#spn_total_loan_amount_due').text(number_format(total_loan_dues))
        
        compute_total_amount_due();
        compute_swiping_amount();
    }
    function clear_loan_dues(){
        $('#loan_dues_body').html('<tr><td style="text-align:center" colspan="11">Please Select Borrower<td></tr>')
    }

    //COMPUTATIONS
    $(document).on('keyup','.txt_fee_amount',function(){
        compute_fee_total();
        compute_total_amount_due();
        compute_swiping_amount();
    });

    function compute_fee_total(){
        var total = 0;
        $('.txt_fee_amount').each(function(){
            var amt;
            if($.isNumeric($(this).val())){
                amt = parseFloat($(this).val());
            }else{
                if($.isNumeric(decode_number_format($(this).val()))){
                    amt = decode_number_format($(this).val());
                }else{
                    amt = 0;
                }
            }
            total+=amt;
        })
        $('#spn_total_loan_fees').text(number_format(total))

        return total;
        // $('#txt_total_amount').val();
    }
    function compute_total_amount_due(){
        var total = compute_fee_total() + total_loan_dues;

        $('#spn_total_amount_due').text(number_format(total));
        return total;
    }

    $('#swiping_amount').keyup(function(){
        compute_swiping_amount()
    })
    function compute_swiping_amount(){
        var val = decode_number_format($('#swiping_amount').val());

        // if($.isNumeric(swipe_val)){
        //     val = swipe_val;
        // }else{
        //     val = decode_number_format(val)
        // }
        var total = compute_fee_total() + total_loan_dues;
        var change = 0;
        var total_paid_amount = val;
        if(isNaN(total_paid_amount)) {
            total_paid_amount = 0;
        }
        if(val >= total){
            change = val - total;
            total_paid_amount = total;
            fill_payable_amount();
        }else{
            change = 0;
            $('.txt_amount_paid').val(number_format(0))
        }
        $('#spn_change').text(number_format(change));
        $('#spn_total_paid_amount').text(number_format(total_paid_amount))

        var out = {};
        out['change'] = change;
        out['total_paid_amount'] = total_paid_amount;

        return out;       
    }

    function fill_payable_amount(){
        $('tr.row_loan_dues').each(function(){
            var token = $(this).attr('loan-token')
            $(this).find('.txt_amount_paid').val(number_format(parseFloat(payable_amount[token])));
        })
    }
    $('#frm_repayment').submit(function(e){
        e.preventDefault();
        var data = {};
        $.each(current_dues,function(i,item){
            item['payable_amount'] = decode_number_format($("tr[loan-token='"+item.loan_token+"']").find('input.txt_amount_paid').val())
        })
        data['loan_dues'] = current_dues;
        data['swiping_amount'] = decode_number_format($('#swiping_amount').val());
        data['repayment_fees'] = parseRepaymentFees();
        data['id_member'] = $('#sel_borrower').val();
        data['member_name'] = $('#sel_borrower').select2('data')[0]['text'];
        current_repayment_token = temp_repayment_token;
        generate_object(current_repayment_token,data);

        //Display on repayment main view
        display_repayment_to_main(current_repayment_token)
        clear_repayment_modal();
        $('#repayment_modal').modal('hide');
    });
    function display_repayment_to_main(token){
        current_repayment_token = token;
        if($('#crd_'+token).length == 0){
            append_repayment(token);
        }
    
        data = repayment_data[token];
        $('#spn_memname_'+token).text(data['member_name']);

        display_loan_dues_main(data['loan_dues']);
        display_fees_main(data['repayment_fees']);

        var totals = parseTotals(token);
        //Totals
        $('#crd_'+token).find('.spn_swiping_amount').text(number_format(totals['swiping_amount']));
        $('#crd_'+token).find('.spn_total_loan_amount_due').text(number_format(totals['total_loan_dues']));
        $('#crd_'+token).find('.spn_total_loan_fees').text(number_format(totals['total_repayment_fees']));
        $('#crd_'+token).find('.spn_total_amount_due').text(number_format(totals['total_amount_due']));
        $('#crd_'+token).find('.spn_total_paid_amount').text(number_format(totals['total_paid_amount']));
        $('#crd_'+token).find('.spn_change').text(number_format(totals['change'])+"");
        current_repayment_token = '';
    }
    function load_repayment_details(repayment_token){
        temp_repayment_token = repayment_token;
        $('#swiping_amount').val(number_format(repayment_data[repayment_token]['swiping_amount']))
        display_loan_dues(repayment_data[repayment_token]['loan_dues']);

        //Reinitialize select2
        $("#sel_borrower").select2("destroy").select2();
        $('#sel_borrower').html('<option value="'+repayment_data[repayment_token]['id_member']+'">'+repayment_data[repayment_token]['member_name']+'</option>')
        intialize_select2();

        //Display fees
        var fees = repayment_data[repayment_token]['repayment_fees'];
        $('#fee_body').html('');
        $.each(fees,function(i,item){
            if(item.id_repayment_fee_type == 1){
                $('#fee_body').append($fee_fix);
            }else{
                $('#fee_body').append($fee_row);
            }
            $('select.sel_fees').last().val(item.id_repayment_fee_type);
            $('input.txt_fee_amount').last().val(number_format(item.amount));
        })
        compute_fee_total();
        compute_total_amount_due();
        compute_swiping_amount();
    }
    function parseRepaymentFees(){
        var fees = [];
        $('tr.repayment_fees').each(function(){
            var temp = {};
            temp['id_repayment_fee_type'] = $(this).find('select.sel_fees').val();
            temp['amount'] = decode_number_format($(this).find('input.txt_fee_amount').val());
            temp['fee_description'] = $(this).find('select.sel_fees option:selected').text();
            fees.push(temp);
        })
        return fees;
    }

    function clear_repayment_modal(){
        $('#sel_borrower').val(null).trigger('change');
        clear_form();
    }
    function clear_form(){
        current_repayment_token = '';
        temp_repayment_token = '';
        total_loan_dues = 0;
        current_dues = [];
        payable_amount = {};
        // $('#sel_borrower').val(null).trigger('change')
        clear_loan_dues();
        $('.row_fee').remove();
        $('#spn_total_loan_amount_due').text(number_format(0))
        $('.txt_fee_amount').val("25.00");
        $('#swiping_amount').val("0.00");
        compute_fee_total();
        compute_total_amount_due();
        compute_swiping_amount();       
    }
    function parseTotals(token){
        var data = repayment_data[token];
        var out = {};
        out['swiping_amount'] = data['swiping_amount'];

        // loan_dues
        var total_loan_dues = 0;
        $.each(data['loan_dues'],function(i,item){
            total_loan_dues += parseFloat(item.amount_due)
        })
        out['total_loan_dues'] =total_loan_dues;
        
        //repayment_fees
        var total_repayment_fees = 0;
        $.each(data['repayment_fees'],function(i,item){
            total_repayment_fees += item.amount;
        })

        out['total_repayment_fees'] =total_repayment_fees;
        out['total_amount_due'] =  out['total_loan_dues'] + out['total_repayment_fees'];

        if(out['swiping_amount'] >= out['total_amount_due']){
            out['total_paid_amount'] = out['total_amount_due'];
            out['change'] = out['swiping_amount'] - out['total_amount_due'];
        }else{
            out['total_paid_amount'] = out['swiping_amount'];
            out['change'] = 0;
        }
        
        return out;
    }
</script>
@endpush

