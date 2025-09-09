<style type="text/css">    
    #payroll_employee_modal{
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
    }
    legend.w-auto{
        font-size: 20px;
    }
    .tbl_ca tr>td{
        padding: 0px;

      /*  padding-left: 5px;
        padding-right: 5px;*/
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 14px;
        /* text-align: center;*/
    }
    td.ca_text{
        padding-top: 3px !important;
    }
    td.pad_right{
        padding-right: 5px !important;
    }
    .tbl_ca tr>th{
        padding: 3px !important;
      /*  padding-left: 5px;
        padding-right: 5px;*/
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 14px;
        text-align: center;
    }
    .ca_input{
        height: 24px;
    }
    .tbl_fields_style{
        margin-top:15px;margin-bottom: 20px;
    }
</style>
<div class="modal fade" id="payroll_employee_modal"  role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-conf" >
        <div class="modal-content">
            <form id="frm_submit_payroll_employee">
                <div class="modal-body">
                    <div class="row">
                        <!-- NET PAY -->
                        <div class="col-md-12" style="margin-top:30px">
                            <div class="form-group row p-0">
                                <label class="col-md-2 control-label col-form-label col-md-cus" style="text-align: left;font-size: 15px !important;">Employee</label>
                                <div class="col-md-7">
                                    <select class="form-control select2 sel_employee" id="sel_employee"></select>
                                </div>
                            </div>
                            <div id="div_daily_rate_holder"></div>
                            <div class="form-group row p-0" id="div_daily_rate">
                                <label class="col-md-2 control-label col-form-label col-md-cus" style="text-align: left;font-size: 15px !important;" >Daily Rate</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control in_payroll" id="txt_daily_rate" value="0.00" disabled>
                                </div>
                                <div class="col-md-2" id="label_no_days">
                                   <!--  <i>(Semi-monthly)</i> -->
                               </div>
                           </div>
                           <div class="form-group row p-0">
                            <label class="col-md-2 control-label col-form-label col-md-cus" style="text-align: left;font-size: 15px !important;" >Basic Pay</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control in_payroll" id="txt_basic_pay" value="0.00" disabled>
                            </div>
                            <div class="col-md-2">
                               <!--  <i>(Semi-monthly)</i> -->
                           </div>
                       </div>
                       <div class="form-group row p-0">
                        <label class="col-md-2 control-label col-form-label col-md-cus" style="text-align: left;font-size: 15px !important;" >Remarks</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control in_payroll" id="txt_remarks_employee" value="">
                        </div>

                    </div>
                </div>
                <div class="col-md-5">
                    <?php
                    $additional_compensation = [
                     'ot'=> ['label'=>'Overtime','key'=>'ot'],
                     'night_shift_dif' => ['label'=>'Night Shift Differential','key'=>'night_shift_dif'],
                     'holiday'=>['label'=>'Holiday','key'=>'holiday'],
                     'paid_leaves'=>['label'=>'Paid Leaves','key'=>'paid_leaves'],
                     'salary_adjustment'=>['label'=>'Salary Adjustment','key'=>'salary_adjustment'],
                     '13th_month'=>['label'=>'13th Month','key'=>'13th_month'],
                     'others'=>['label'=>'Others','key'=>'others'],
                 ];
                 ?>
                 <div class="col-md-12 p-0">
                    <div class="card c-border">
                        <div class="card-body">
                            <h5 class="lbl_color text-center"><u>Additional Compensation</u></h5>
                            <table class="tbl_fields_style lbl_color">
                                @foreach($additional_compensation as $ac)
                                <tr>
                                    <th width="50%">{{$ac['label']}}</th>
                                    <td><input type="text" class="form-control class_amount in_add_compensation in_employee_amounts" value="0.00" id="txt_{{$ac['key']}}" key="{{$ac['key']}}"  parent-class="in_add_compensation"></td>
                                </tr>
                                @endforeach
                                <tr style="border-top: 2px solid;">
                                    <th width="50%">Sub Total</th>
                                    <td><input type="text" class="form-control class_amount" value="0.00" id="txt_total_add_comp" disabled></td>
                                </tr>
                            </table>

                            <h5 class="lbl_color text-center"><u>Allowances</u></h5>
                            <table class="tbl_fields_style lbl_color">
                                <tbody id="allowance_body">
                                    <tr id="row_no_allowance"><td colspan="2" style="text-align:center">No Allowance(s)</td></tr>
                                </tbody>

                            </table> 
                            <table class="tbl_fields_style lbl_color" style="border-top:2px solid;font-size: 18px;">
                                <tbody id="allowance_body">
                                    <th width="50%">GROSS PAY</th>
                                    <td><input type="text" class="form-control class_amount" value="0.00" id="txt_gross_income" disabled></td>
                                </tbody>

                            </table>                                     
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-7">
                <div class="col-md-12 p-0">
                    <?php
                    $employee_deductions = [
                        'absences'=>['label'=>'Absences','key'=>'absences'],
                        'late'=>['label'=>'Late','key'=>'late'],
                        'sss_loan'=>['label'=>'SSS Loan','key'=>'sss_loan'],
                        'hdmf_loan'=>['label'=>'HDMF Loan','key'=>'hdmf_loan'],

                    ];
                    ?>
                    <div class="card c-border">
                        <div class="card-body">
                            <h5 class="lbl_color text-center"><u>Employee Deductions</u></h5>
                            <table class="tbl_fields_style lbl_color">
                                @foreach($employee_deductions as $ad)
                                <tr>
                                    <th width="50%">{{$ad['label']}}</th>
                                    <td><input type="text" class="form-control class_amount in_employee_deduction in_employee_amounts" value="0.00" id="txt_{{$ad['key']}}" key="{{$ad['key']}}" parent-class="in_employee_deduction"></td>
                                </tr>
                                @endforeach
                                <tr style="border-top: 2px solid;">
                                    <th width="50%">Sub Total</th>
                                    <td><input type="text" class="form-control class_amount" value="0.00" id="txt_total_employee_deduction" disabled></td>
                                </tr>
                            </table>
                            <h5><u>Cash Advance</u></h5>
                            <table class="table table-bordered tbl_ca tbl_fields_style">
                                <thead>
                                    <tr>
                                        <th class="table_header_dblue">CDV #</th>
                                        <th class="table_header_dblue">Date</th>
                                        <th width="40%" class="table_header_dblue">Description</th>
                                        <th class="table_header_dblue">Balance</th>
                                        <th width="25%" class="table_header_dblue">Amount to Deduct</th>
                                    </tr>
                                </thead>
                                <tbody id="ca_body">
                                    <tr id="row_no_ca"><td colspan="5" style="text-align:center">No Cash Advance(s)</td></tr>

                                </tbody>                          
                            </table>
                            <table width="70%" class="tbl_fields_style lbl_color">
                                <tr>
                                    <th colspan="2">Total CA</th>
                                    <td><input type="text" class="form-control class_amount" value="0.00" id="txt_total_ca_deduct" disabled></td>
                                </tr>
                            </table>
                            <h5 class="lbl_color text-center"><u>Deducted Benefits</u></h5>
                            <?php
                            $deducted_benefits = [
                                'sss'=>['label'=>'SSS','key'=>'sss'],
                                'philhealth'=>['label'=>'PhilHealth','key'=>'philhealth'],
                                'hdmf'=>['label'=>'HDMF','key'=>'hdmf'],
                                'wt'=>['label'=>'Withholding Tax','key'=>'wt'],
                                'insurance'=>['label'=>'Insurance','key'=>'insurance'],
                            ];
                            ?>
                            <table class="tbl_fields_style lbl_color"> 
                                @foreach($deducted_benefits as $db)
                                <tr>
                                    <th width="50%">{{$db['label']}}</th>
                                    <td><input type="text" class="form-control class_amount  in_deducted_benefits in_employee_amounts" value="0.00" id="txt_{{$db['key']}}" key="{{$db['key']}}" parent-class="in_deducted_benefits"></td>
                                </tr>
                                @endforeach
                                <tr style="border-top: 2px solid;">
                                    <th width="50%">Sub Total</th>
                                    <td><input type="text" class="form-control class_amount" value="0.00" id="txt_total_deducted_benefits" disabled></td>
                                </tr>
                            </table> 
                            <table class="tbl_fields_style lbl_color" style="border-top:2px solid;font-size: 18px;">
                                <tbody id="allowance_body">
                                    <tr>
                                        <th width="50%">TOTAL DEDUCTIONS</th>
                                        <td><input type="text" class="form-control class_amount" value="0.00" id="txt_total_deduction" disabled></td>
                                    </tr>
                                    <tr>
                                        <th width="50%">NET PAY</th>
                                        <td><input type="text" class="form-control class_amount" value="0.00" id="txt_net_income" disabled></td>
                                    </tr>
                                </tbody>

                            </table>                         
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer" style="padding:5px;padding-left: 10px;">
        @if($allow_post)
        <button class="btn bg-gradient-success2" id="btn_save">Save</button>
        @endif

        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</form>
</div>
</div>
</div>

@push('scripts')

@if($allow_post)
<script type="text/javascript">
    function post_payroll_employee(){
        var $payroll_employee_data = {};
        //CA

        $('.txt_ca_deduct').removeClass('mandatory');

        if($current_employee_key == ""){
            Swal.fire({
              position: 'center',
              icon: 'warning',
              title: "Please select valid employee",
              showConfirmButton: false,
              showCancelButton: true,
              cancelButtonText : "Close"
          })
            return;
        }

        //Amount inputs
        $.each(class_mapping_amounts,function(obj_key,input_class){
            $payroll_employee_data[obj_key] = {};
            $(input_class).each(function(){
                var key = $(this).attr('key');
                var val = $(this).val();
                if($(this).hasClass('class_amount')){
                    val = decode_number_format(val);
                }
                $payroll_employee_data[obj_key][key] = val;
            })
        })
        $payroll_employee_data['employee_details'] = current_employee_details;



        //Allowance Labels
        var allowances_obj = [];
        $('.in_employee_allowance').each(function(){
            var temp = {};
            temp['description'] = $(this).attr('label');
            temp['id_allowance_name'] = $(this).attr('key');
            temp['amount'] = decode_number_format($(this).val());
            temp['type'] = $(this).attr('allowance_type');

            allowances_obj.push(temp);
            // allowance_name[$(this).attr('key')] = $(this).attr('label');
        })
        $payroll_employee_data['allowances'] = allowances_obj;
        $payroll_employee_data['totals'] = sub_totals();
        $payroll_employee_data['basic_pay'] = parseBasicPay(parseFloat(current_employee_details.monthly_rate),parseFloat(current_employee_details.daily_rate));

        // id-ref,col_date,col_description,col_balance,txt_ca_deduct
        var valid_ca_amount = true;
        var ca_obj = [];
        $('tr.ca_row').each(function(){
            var temp = {};
            temp['id_ref'] = $(this).attr('id-ref');
            temp['date'] = $(this).find('td.col_date').text();
            temp['description'] = $(this).find('td.col_description').text();
            temp['balance'] = decode_number_format($(this).find('td.col_balance').text());
            temp['deducted_amt'] = decode_number_format($(this).find('input.txt_ca_deduct').val());

            var balance =parseFloat($(this).find('input.txt_ca_deduct').attr('balance'));

            
            if(temp['deducted_amt'] > balance){
                $(this).find('input.txt_ca_deduct').addClass('mandatory')
                valid_ca_amount = false;
            }

            ca_obj.push(temp);
        })

        if(!valid_ca_amount){
            Swal.fire({
              position: 'center',
              icon: 'warning',
              title: "CA Deducted amount must be less than or equal to balance",
              showConfirmButton: false,
              showCancelButton: true,
              cancelButtonText : "Close"
          })
            return;
        }

        $payroll_employee_data['cash_advances'] = ca_obj;

        $income = {};
        $income['gross'] = $payroll_employee_data['basic_pay'] + $payroll_employee_data['totals']['total_employee_allowance']+$payroll_employee_data['totals']['total_additional_compensation'];
        $income['net'] = $income['gross'] - $payroll_employee_data['totals']['total_deducted_benefits']-$payroll_employee_data['totals']['total_deducted_cash_advance']-$payroll_employee_data['totals']['total_employee_deduction'];



        $payroll_employee_data['income'] =$income;
        $payroll_employee_data['remarks'] = $('#txt_remarks_employee').val();

        var employee_key = ($current_employee_key == "")?makeid(5):$current_employee_key;
        $payroll_employee[employee_key] = $payroll_employee_data;

        table_append($payroll_employee[employee_key]);

        clear_modal();
        set_totals();
        $('#payroll_employee_modal').modal('hide')

        console.log({$payroll_employee});
    }
    $(document).on('change','#sel_employee',function(){
        var id_employee = $(this).val();
        if(id_employee == null){
            return;
        }
        //validate if employee has payroll record
        if($payroll_employee[id_employee] != undefined){
            Swal.fire({
              position: 'center',
              icon: 'warning',
              title: "Employee has already payroll record",
              showConfirmButton: false,
              showCancelButton: true,
              cancelButtonText : "Close"
          })
            $(this).val(0).trigger("change");
            return;
        }
        get_employee_details(id_employee);
    });
</script>
@endif

<script type="text/javascript">
    let current_employee_details = {};
    const allow_post = <?php echo $allow_post;?>;

    if(allow_post ==0){
        $('.in_employee_amounts,.in_payroll,#sel_employee').attr('disabled',true)
    }

    const $additional_compensation_obj = jQuery.parseJSON('<?php echo json_encode($additional_compensation)?>');
    const $employee_deductions_obj = jQuery.parseJSON('<?php echo json_encode($employee_deductions)?>');
    const $deducted_benefits_obj = jQuery.parseJSON('<?php echo json_encode($deducted_benefits)?>');
    const no_allowance_row = '<tr id="row_no_allowance"><td colspan="2" style="text-align:center">No Allowance(s)</td></tr>';
    const no_ca_row = '<tr id="row_no_ca"><td colspan="5" style="text-align:center">No Cash Advance(s)</td></tr>';

    const header_can_hide = ['ot', 'night_shift_dif', 'holiday', 'paid_leaves', 'salary_adjustment', '13th_month',  'sss_loan', 'hdmf_loan', 'wt', 'insurance'];


    var gg = [1,2,3];
    for(var $i=0;$i<gg.lenght;$i++){
        console.log({i})
    }

    const class_mapping_amounts = {
        'additional_compensation' : '.in_add_compensation',
        'employee_deductions' : '.in_employee_deduction',
        // 'cash_advance' : '.in_cash_advance',
        'deducted_benefits' : '.in_deducted_benefits',
        'employee_allowance' : '.in_employee_allowance'
    };
    const payroll_subtotal_mapping = {
        '.in_add_compensation' : {'textbox' : '#txt_total_add_comp','total_key' : 'total_additional_compensation'},
        '.in_employee_deduction':{'textbox' : '#txt_total_employee_deduction','total_key' : 'total_employee_deduction'},
        '.in_deducted_benefits' : {'textbox' : '#txt_total_deducted_benefits','total_key' : 'total_deducted_benefits'},
        // '.in_cash_advance' : {'textbox' : '','total_key' : 'total_deducted_cash_advance'},
        '.in_employee_allowance' : {'textbox' : '#txt_total_employee_allowance','total_key' : 'total_employee_allowance'},
    }
    intialize_select2();
    var $current_employee_key = '';
    $(document).on('select2:open', (e) => {
        const selectId = e.target.id
        $(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
            key,
            value,
            ) {
            value.focus()
        })
    }) 
    function intialize_select2(){       
        $(".sel_employee").select2({
            minimumInputLength: 2,
            width: '100%',
            createTag: function (params) {
                return null;
            },
            ajax: {
                tags: true,
                url: '/search_employee',
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


    $('#frm_submit_payroll_employee').submit(function(e){
        e.preventDefault();
        post_payroll_employee()
    })

    function makeid(length){
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for(var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function parseBasicPay(monthly_rate,daily_rate){
        var $id_payroll_mode = $('#sel_id_payroll_mode').val();
        if($id_payroll_mode == 1){ // Monthly
            return monthly_rate;
        }else if($id_payroll_mode == 2){ // Semi Monthly
            return monthly_rate/2;
        }else{ // Daily
            var $no_days = parseInt($('#txt_no_days').val())
            return daily_rate * $no_days;
        }
    }

    function view_payroll_employee(key){
        $current_employee_key = key;
        $payroll_employee_details = $payroll_employee[key];
        current_employee_details = $payroll_employee[key]['employee_details'];
        view_mode = 1;
        console.log({$payroll_employee_details});

        $('#sel_employee').select2('destroy');
        $('#sel_employee').html('<option value="'+$payroll_employee_details['employee_details']['id_employee']+'">'+$payroll_employee_details['employee_details']['id_employee']+' || '+$payroll_employee_details['employee_details']['name']+'</option>');
        intialize_select2()
        

        initialize_allowance($payroll_employee_details['allowances'])
        initialize_ca($payroll_employee_details['cash_advances'])

        $('#txt_basic_pay').val(number_format($payroll_employee_details['basic_pay']));
        $('#txt_daily_rate').val(number_format(parseFloat(current_employee_details['daily_rate'])));

        $.each(class_mapping_amounts,function(obj_key,input_class){
           $.each($payroll_employee_details[obj_key],function(key,value){
            var elem = $(input_class+'[key="'+key+'"]');
            var val = value;
            if($(elem).hasClass('class_amount')){
                val = number_format(parseFloat(value));
            }
            $(elem).val(val);
        })           
       })
        sub_totals();
        $('#txt_remarks_employee').val($payroll_employee_details['remarks'])
        $('#payroll_employee_modal').modal('show')
    }

    $(document).on('keyup','.in_employee_amounts',function(){
        var parent_class= $(this).attr('parent-class');
        sub_totals('.'+parent_class);
    })

    function sub_totals(input_class_name){
        var total = {};


        var basic_pay = 0;

        if(Object.keys(current_employee_details).length > 0){
            basic_pay = parseBasicPay(parseFloat(current_employee_details.monthly_rate),parseFloat(current_employee_details.daily_rate));
        }
        console.log({basic_pay})
        
        $.each(payroll_subtotal_mapping,function(input_class_name,item){
            var sub_total = 0;
            $(input_class_name+".class_amount.in_employee_amounts").each(function(){
                var val = $(this).val();
                val = (val == '')?0:decode_number_format(val);
                sub_total += val;
            });
            total[item['total_key']] = sub_total;
            $(item['textbox']).val(number_format(sub_total));            
        });


        var total_ca = 0;
        $('.txt_ca_deduct').each(function(i,item){
            total_ca += decode_number_format($(this).val());
        })
        $('#txt_total_ca_deduct').val(number_format(total_ca,2))
        total['total_deducted_cash_advance'] = total_ca;

        var add_income = total['total_additional_compensation'] + total['total_employee_allowance'];
        var total_deductions = total['total_employee_deduction']+total['total_deducted_benefits']+total['total_deducted_cash_advance'];

        $('#txt_gross_income').val(number_format(add_income+basic_pay));
        $('#txt_total_deduction').val(number_format(total_deductions));
        $('#txt_net_income').val(number_format(add_income+basic_pay-total_deductions));


        console.log({total});
        return total;
    }

    function get_employee_details($id_employee){
        $.ajax({
            type       :         'GET',
            url        :         '/employee/get_details',
            data       :         {'id_employee'  : $id_employee,
            'id_payroll' : '<?php echo $payroll->id_payroll ?? 0 ?>'},
            success    :         function(response){
               console.log({response});
               current_employee_details = response.details;
               $current_employee_key = response.details.id_employee;

               var id_payroll_mode = $('#sel_id_payroll_mode').val();
               var divide = 1;

               if(id_payroll_mode == 2){
                divide = 2;
            }

            $('#txt_basic_pay').val(number_format(parseBasicPay(parseFloat(current_employee_details.monthly_rate),parseFloat(current_employee_details.daily_rate))))

            $('#txt_daily_rate').val(number_format(parseFloat(current_employee_details.daily_rate)));

            $('#txt_sss').val(number_format(parseFloat(current_employee_details.sss_amount/divide)))
            $('#txt_philhealth').val(number_format(parseFloat(current_employee_details.philhealth_amount/divide)))
            $('#txt_hdmf').val(number_format(parseFloat(current_employee_details.hdmf_amount/divide)))
            $('#txt_wt').val(number_format(parseFloat(current_employee_details.withholding_tax/divide)))
            $('#txt_insurance').val(number_format(parseFloat(current_employee_details.insurance/divide)))

            $('#txt_balance,#txt_amt_deduct').val(number_format(parseFloat(current_employee_details.ca_balance)))


            initialize_allowance(response.allowances);
            initialize_ca(response.cash_advances);
            sub_totals();
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
    function initialize_allowance(allowances){
        var out = '';
        var found_allowance = false;
        if(allowances.length > 0){
            var allowance_type = ($('#sel_id_payroll_mode').val() == 3)?'1':'2';
            var divide = ($('#sel_id_payroll_mode').val() == 2)?2:1;

            $.each(allowances,function(i,item){
                console.log(item.type+"_______________"+allowance_type)
                if(item.type == allowance_type){
                    found_allowance =true;
                    var allowance_value = ($('#sel_id_payroll_mode').val() == 3)?(parseFloat(item.amount)*parseInt($('#txt_no_days').val())):parseFloat(item.amount)/divide;
                    out += '<tr>';        
                    out += '<th width="50%">'+item.description+'</th>'
                    out += '<td><input type="text" class="form-control class_amount in_employee_allowance in_employee_amounts" value="'+number_format(allowance_value)+'"parent-class="in_employee_allowance" key="'+item.id_allowance_name+'" label="'+item.description+'" allowance_type="'+item.type+'"></td>'
                    out += '</tr>';                   
                }

            })

            out += `                <tr style="border-top: 2px solid;">
            <th width="50%">Sub Total</th>
            <td><input type="text" class="form-control class_amount" value="0.00" id="txt_total_employee_allowance" disabled></td>
            </tr>`;
            if(!found_allowance){
                out = no_allowance_row;
            }
        }else{
            out = no_allowance_row;
        }
        $('#allowance_body').html(out)
    }
    function table_append($payroll_emp){
        var row_cols = '';
        var length  = $('.row_employee[row-key="'+$payroll_emp['employee_details']['id_employee']+'"]').length;


        console.log({length,$payroll_emp})

        row_cols += '<td>'+$payroll_emp['employee_details']['id_employee']+'</td>';
        row_cols += '<td>'+$payroll_emp['employee_details']['name']+'</td>';
        row_cols += '<td class="class_amount">'+number_format($payroll_emp['basic_pay'])+'</td>';
        var c = [];

        $.each($additional_compensation_obj,function(i,item){
            row_cols += '<td class="class_amount col_'+i+'">'+number_format(parseFloat($payroll_emp['additional_compensation'][i]))+'</td>';
            c.push(i);
        });

        row_cols += '<td class="class_amount col_allowance">'+number_format(parseFloat($payroll_emp['totals']['total_employee_allowance']))+'</td>';    
        row_cols += '<td class="class_amount">'+number_format(parseFloat($payroll_emp['income']['gross']))+'</td>';      

        $.each($employee_deductions_obj,function(i,item){
            row_cols += '<td class="class_amount col_'+i+'">'+number_format(parseFloat($payroll_emp['employee_deductions'][i]))+'</td>';
            c.push(i);
        })  

        row_cols += '<td class="class_amount col_ca_amount">'+number_format(parseFloat($payroll_emp['totals']['total_deducted_cash_advance']))+'</td>';
        $.each($deducted_benefits_obj,function(i,item){
            row_cols += '<td class="class_amount col_'+i+'">'+number_format(parseFloat($payroll_emp['deducted_benefits'][i]))+'</td>';
            c.push(i);
        });  


        var add_class = (parseFloat($payroll_emp['income']['net']) <= 0)?'text_danger':'';
        row_cols += '<td class="class_amount '+add_class+'">'+number_format(parseFloat($payroll_emp['income']['net']))+'</td>';  
        if(length == 0){ //Append
            $('#payroll_body').append('<tr class="row_employee" row-key="'+$payroll_emp['employee_details']['id_employee']+'">'+row_cols+'</tr>');
        }else{ // Update
            $('tr[row-key="'+$payroll_emp['employee_details']['id_employee']+'"]').html(row_cols);
        }

        
        hide_zero_columns();
        $('#row_no_employee').remove();




    }

    function hide_zero_columns(){

        for(var $i=0;$i<header_can_hide.length;$i++){
            var sum = 0;
            $('td.col_'+header_can_hide[$i]).each(function(){
                sum+=decode_number_format($(this).text());
            })

            if(sum == 0){
                $('.col_'+header_can_hide[$i]).hide();
                $('.col_'+header_can_hide[$i]+'_footer').hide();
            }else{
                var kk ='.col_'+header_can_hide[$i]+'_footer';

                console.log({kk,sum})
                $('.col_'+header_can_hide[$i]).show();
                $('.col_'+header_can_hide[$i]+'_footer').show();
                $('.col_'+header_can_hide[$i]+'_footer').text(number_format(sum))
            }
        }
    }

    function set_totals(){
        var totals = {};
        $.each($payroll_employee,function(i,emp){
            $.each(emp['additional_compensation'],function(key,val){
                var temp = totals[key] ?? 0;
                totals[key] = temp+val;
                console.log({key,val})
            })

            var temp = totals['basic_pay'] ?? 0;
            totals['basic_pay'] = temp + emp['basic_pay'];

            $.each(emp['deducted_benefits'],function(key,val){
                var temp = totals[key] ?? 0
                totals[key] = temp+val;
                console.log({key,val})
            })

            $.each(emp['employee_deductions'],function(key,val){
                var temp = totals[key] ?? 0
                totals[key] = temp+val;
                console.log({key,val})
            })

            
            var temp = totals['gross'] ?? 0;
            totals['gross'] = temp+emp['income']['gross'];

            var temp = totals['net'] ?? 0;
            totals['net'] = temp+emp['income']['net'];

            var temp = totals['allowance'] ?? 0;
            totals['allowance'] = temp+emp['totals']['total_employee_allowance'];

            var temp = totals['ca'] ?? 0;
            totals['ca'] = temp+emp['totals']['total_deducted_cash_advance'];

            

            

        });

        $.each(totals,function(key,value){
            $('.col_'+key+'_footer').text(number_format(value));
        })

        console.log({totals});

    }
    function check_with_value($in){
        return $in ?? 0;
    }
    function draw_table_header(){


        var out_footer = "";
        out_footer += '<td colspan="2">Grand Total</td>';
        out_footer += '<td class="col_basic_pay_footer class_amount"></td>';



        var out_header = '<tr >';
        out_header += '<th class="table_header_dblue">Emp ID</th>';
        out_header += '<th class="table_header_dblue">Emp Name</th>';
        out_header += '<th class="table_header_dblue">Basic Pay</th>';


        $.each($additional_compensation_obj,function(i,item){
            out_header += '<th class="table_header_dblue col_'+i+'">'+item['label']+'</th>';
            out_footer += '<td class="col_'+i+'_footer class_amount"></td>';
        })     
        out_header += '<th class="table_header_dblue">Allowance</th>';
        out_footer += '<td class="col_allowance_footer class_amount"></td>';

        out_header += '<th class="table_header_dblue">Gross Income</th>';
        out_footer += '<td class="col_gross_footer class_amount"></td>';

        $.each($employee_deductions_obj,function(i,item){
            out_header += '<th class="table_header_dblue col_'+i+'">'+item['label']+'</th>';
            out_footer += '<td class="col_'+i+'_footer class_amount"></td>';
        })
        out_header += '<th class="table_header_dblue col_ca_amount">Cash Advance</th>';
        out_footer += '<td class="col_ca_footer class_amount"></td>';
        $.each($deducted_benefits_obj,function(i,item){
            out_header += '<th class="table_header_dblue col_'+i+'">'+item['label']+'</th>';
            out_footer += '<td class="col_'+i+'_footer class_amount"></td>';
        })
        out_header += '<th class="table_header_dblue">Net Income</th>'; 
        out_footer += '<td class="col_net_footer class_amount"></td>';  
        out_header += '</tr>';


        $('#payroll_table_header').html(out_header);
        $('#payroll_footer').html(out_footer);






        hide_zero_columns();
    }



    function clear_modal(){
        $('.in_employee_amounts').val('0.00');
        $('#allowance_body').html(no_allowance_row)
        $('#ca_body').html(no_ca_row);
        $('#txt_basic_pay').val('0.00');
        $('#txt_daily_rate').val('0.00');
        $('#txt_balance').val('0.00')
        $('#txt_remarks_employee').val('')
        current_employee_details = {};
        $current_employee_key = '';

        $('#sel_employee').val(0).trigger("change");

        sub_totals();
    }

    function initialize_ca(ca){
        var out = '';
        if(ca.length ==0){
            $('#ca_body').html(no_ca_row);
            return;
        }
        $.each(ca,function(i,item){

            var balance = number_format(parseFloat(item.balance),2);
            var deducted_amt = (item.deducted_amt == undefined)?balance:number_format(parseFloat(item.deducted_amt),2);
            out += '<tr class="ca_row" id-ref="'+item.id_ref+'">';
            out += '    <td class="ca_text" style="text-align:center">'+item.ref_text+'</td>';
            out += '    <td class="ca_text col_date">'+item.date+'</td>';
            out += '    <td class="ca_text col_description">'+item.description+'</td>';
            out += '    <td class="ca_text class_amount pad_right col_balance">'+balance+'</td>';
            out += '    <td><input type="text" name="" class="form-control ca_input class_amount txt_ca_deduct in_employee_amounts" value="'+deducted_amt+'" balance="'+parseFloat(item.balance)+'"></td>';
            out += '</tr>';
        })
        $('#ca_body').html(out);
    }

</script>
@endpush


