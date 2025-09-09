<style type="text/css">
    .tbl_loan_req tr>th{
        padding: 5px;
        padding-left: 5px;
        padding-right: 5px;
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 12px;
    }
    .tbl_loan_req tr>td,.tbl_requirements tr>td{
        padding: 1px;
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 12px;
    }
    .cus-font{
       font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
       font-size: 15px !important;       
   }
   .frm-requirements{
    height: 24px !important;
    width: 100%;    
    font-size: 13px;
}
.modal-conf {
    max-width:60% !important;
    min-width:60% !important;
    /*margin: auto;*/
}
</style>

<!-- NET PAY -->
<div class="col-md-12 p-0" style="margin-top:15px">
    <div class="card c-border">
        <div class="card-header bg-gradient-primary2 py-1">
            <h5 class="text-center mb-0">Latest Payslip</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php for($i=0;$i<2;$i++): ?>
                <div class="col-md-6 col-12">
                    <div class="card c-border row_net">
                        <div class="card-body">
                            <h6 class="lbl_color"><u>Payslip <?php echo e($i+1); ?></u></h6>
                            <div class="form-row d-flex align-items-end mt-3">
                                <div class="form-group col-md-6 col-6">
                                    <label class="lbl_color">Date Period</label>
                                    <input type="date" name="" required class="form-control frm-net frm-requirements" key="period_start" value="<?php echo e($net_pays[$i]->period_start ?? ''); ?>">
                                </div>
                                <div class="form-group col-md-6 col-6">
                                    <label class="lbl_color">&nbsp;</label>
                                    <input type="date" name="" required class="form-control frm-net frm-requirements" key="period_end" value="<?php echo e($net_pays[$i]->period_end ?? ''); ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="lbl_color">Amount</label>
                                    <input type="text" name="" required class="form-control frm-net class_amount frm-requirements" value="<?php echo e($net_pays[$i]->amount ?? '0.00'); ?>" key="amount">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>


            </div>
        </div>
    </div>
</div>

<!-- OTHER LENDER -->

<div class="col-md-12 p-0" style="margin-top:10px">
    <div class="card c-border">
        <div class="card-header bg-gradient-primary2 py-1">
            <h5 class="text-center mb-0">Other Lending(s)</h5>
        </div>
        <div class="card-body">
            <div class="form-group col-md-12 p-0" style="margin-bottom:unset;">
                <div class="custom-control custom-checkbox" style="margin-top:20px">
                    <input type="checkbox" class="custom-control-input" id="chk_has_other_loan" key="has_other_loans" <?php echo (isset($other_lendings) && count($other_lendings) > 0)?'checked':''; ?>>
                    <label class="custom-control-label" for="chk_has_other_loan">Do you have any outstanding loans with other lenders?</label>
                </div>                                  
            </div>
            <div class="col-md-12 mt-2">
                <div id="other-lending-body">
                    <div class="card c-border row_other_lending">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-xs bg-gradient-danger2 float-right" onclick="remove_other_lending(this)"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="form-row d-flex align-items-end mt-2">
                                <div class="form-group col-md-5 col-12">
                                    <label class="lbl_color">Lending Institution</label>
                                    <input type="text" name="" required class="form-control frm-other-lending frm-requirements" key="name">
                                </div>
                                <div class="form-group col-md-2 col-6">
                                    <label class="lbl_color">Date Started</label>
                                    <input type="date" name="" required class="form-control frm-other-lending frm-requirements" key="date_started">
                                </div>
                                <div class="form-group col-md-2 col-6">
                                    <label class="lbl_color">Maturity Date</label>
                                    <input type="date" name="" required class="form-control frm-other-lending frm-requirements" key="date_ended">
                                </div>
                                <div class="form-group col-md-3 col-6">
                                    <label class="lbl_color">Amount Loaned</label>
                                    <input type="text" name="" required class="form-control frm-other-lending class_amount frm-requirements" value="0.00" key="amount">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer custom_card_footer">
            <div class="col-md-12">
                <button type="button" class="btn btn-sm bg-gradient-success2 frm-other-lending col-md-12" style="margin-left:10px" onclick="append_lender()"><i class="fa fa-plus"></i>&nbsp;Add</button>

            </div>
        </div>
    </div>
</div>

<!-- COMAKERS -->


<div class="col-md-12 p-0" style="margin-top: 20px">
    <div class="card c-border">
        <div class="card-header bg-gradient-primary2 py-1">
            <h5 class="text-center mb-0">Comaker(s)</h5>
        </div>
        <div class="card-body">
            <div id="cbu-div">

            </div>
            <div class="col-md-12">
                <table class="table table-bordered table-stripped table-head-fixed tbl_loan_req tbl-comakers" style="">
                    <thead>
                        <tr >
                            <th class="table_header_dblue" width="2%">No.</th>
                            <th class="table_header_dblue">Name</th>
                        </tr>
                    </thead>  
                    <tbody>
                        <?php for($i=0;$i<$loan_service->no_comakers;$i++): ?>
                            <tr class="row_comaker">
                                <td class="cus-font" style="text-align:center;"><?php echo e($i+1); ?></td>
                                <td>
                                    <select id="sel_comaker_<?php echo e($i+1); ?>" class="form-control frm-comakers frm-requirements p-0 select2 sel_comaker cus-font"  key='id_member' required onchange="check_comaker_duplicate(this,'<?php echo $i;?>')">
                                        <?php
                                        if($opcode == 1 && $CHANGE_SERVICE){
                                         if(isset($comakers) && count($comakers) > 0){
                                            echo "<option value='".$comakers[$i]->tag_id."'>".$comakers[$i]->tag_value."</option>";
                                        }                                       
                                    }

                                    ?>
                                </select>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>                                      
                </table>

            </div>
        </div>
    </div>
</div>

<!-- LOAN REMARKS -->


<div class="col-md-12 p-0" style="margin-top:15px">
    <div class="card c-border">

        <div class="card-body">
       <!--      <div class="">
                <h5 class="lbl_color badge bg-light text-lg">Loan Purpose</h5>
            </div> -->
            <div class="col-md-12">
                <p class="lbl_color text-md font-weight-bold mb-0">Loan Purpose</p>
                <textarea class="form-control" rows="3" style="resize:none;" key="loan_remarks" id="txt_loan_remarks"><?php echo e($loan_service->loan_remarks ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
    // $lender_row = '<tr class="row_other_lending">'+$('tr.row_other_lending').html()+'</tr>';
    $lender_row = ' <div class="card c-border row_other_lending">'+$('div.row_other_lending').html()+'</div>';

    $(document).ready(function(){
        initmaker_error();
    })

        // $('.frm-requirements').attr('required',false);
    function append_lender(){
        $('#other-lending-body').append($lender_row);
        animate_element($('div.row_other_lending').last(),1);
    }
    function remove_other_lending(obj){
        var parent_row = $(obj).closest('div.row_other_lending');
        animate_element(parent_row,2);

    }

    $('.frm-other-lending').attr('disabled',true)
    intialize_select2();

    function intialize_select2(){       
        $(".sel_comaker").select2({
            minimumInputLength: 2,
            width: '100%',
            createTag: function (params) {
                return null;
            },
            ajax: {
                tags: true,
                url: '/search_comakers',
                dataType: 'json',
                type: "GET",
                quietMillis: 1000,
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        cm : '<?php echo $id_member ?>'
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

    $('#chk_has_other_loan').click(function(){
        var checked = $(this).prop('checked');
        $('.frm-other-lending').attr('disabled',!checked)
    })
    $('#frm_loan_application_details').submit(function(e){
        e.preventDefault();
        set_loan_application_details();
        $('#loan_app_modal').modal('hide');

        $('#div_loan_application_details_display').show();

    })

    function set_loan_application_details(){
            //net pay
        $net_pay = parseNetPays();
        var out = '';
        for(var $i=0;$i<$net_pay.length;$i++){
            out += '<tr>'
            $.each($net_pay[$i],function(i,val){
             out += '<td>'+((i=='amount')?number_format(val):val)+'</td>'
         })
            out += '</tr>';
        }
        $('#net_body_summary').html(out);

            //other lendings
        var out_other_lendings = '';
        if($('#chk_has_other_loan').prop('checked')){
            $other_lendings = parseOtherLendings();
            for(var $i=0;$i<$other_lendings.length;$i++){
                out_other_lendings += '<tr>'
                $.each($other_lendings[$i],function(i,val){
                 out_other_lendings += '<td>'+((i=='amount')?number_format(val):val)+'</td>'
             })
                out_other_lendings += '</tr>';
            }
            $('#other_lending_body_summary').html(out_other_lendings);  
            $('#div_other_lendings').show();             
        }else{
            out_other_lendings = '';
            $('#div_other_lendings').hide();
        }


        var out_comaker = ''
            //comakers
        $('.sel_comaker').each(function(i){
            var text = $(this).select2('data')[0]['text'];
            out_comaker += '<tr>';
            out_comaker += '<td>'+(i+1)+'</td>'
            out_comaker += '<td>'+text+'</td>'
            out_comaker += '</tr>';
            console.log({text});    
        })

        $('#comaker_body_summary').html(out_comaker);
    }

    function parseNetPays(){
        $net_pay = [];

        $('.row_net').each(function(){
            var inputs = $(this).find('input.frm-requirements');
            var temp = {};
            inputs.each(function(){
                var key = $(this).attr('key');
                temp[key] = ($(this).hasClass('class_amount'))?decode_number_format($(this).val()):$(this).val();
            })
            $net_pay.push(temp);
        });
        return $net_pay;
    }
    function parseOtherLendings(){
        $other_lendings = [];
        $('.row_other_lending').each(function(){
            var inputs = $(this).find('input.frm-requirements');
            var temp = {};
            inputs.each(function(){
                var key = $(this).attr('key');
                temp[key] = ($(this).hasClass('class_amount'))?decode_number_format($(this).val()):$(this).val();
            })
            $other_lendings.push(temp);
        });
        return $other_lendings;
    }
    function parseComakers(){
        $comakers = [];
        $('.sel_comaker').each(function(i){
            var temp  = {};
            temp['id_member'] = $(this).val();
            $comakers.push(temp);
        })

        return $comakers;
    }

    
    function check_comaker_duplicate(obj,index){
        CURRENT_MAKER_CBU[index] = 0;
        var comakers =[];
        var id_com = $(obj).val();
        $('.sel_comaker').each(function(i){
            if(parseInt(index) != i){
                var val = $(this).val();
                if(val != ""){
                    comakers.push(val);
                }                      
            }
        });

        var valid_comaker = true;
        if(jQuery.inArray(id_com,comakers) >=0 ){
            var selected_com_text = $(obj).select2('data')[0]['text'];
            toastr.error('Duplicate Comaker. ('+selected_com_text+')');
            $(obj).empty();
            valid_comaker = false;
        }

        if(valid_comaker && LOAN_SERVICE_DETAILS.with_maker_cbu == 1){
            checkComakerCBU(index,id_com);
        }

    }

    const checkComakerCBU = (index,id_member) =>{
        $.ajax({
            type      :        'GET',
            url       :        '/parse-member-cbu',
            data      :        {'id_member' : id_member,'id_loan_service' : id_loan_service},
            success   :        function(response){
                               console.log({response});
                               if(response.WITH_CBU){
                                    CURRENT_MAKER_CBU[index] = response.cbu;
                                    initmaker_error();   
    
                               }
            }
        });
    }

    const initmaker_error = ()=>{
        var MAKER_OBJ = [];
        $('select.sel_comaker').each(function(i){
            let mcbu = CURRENT_MAKER_CBU[i];
            if(parseFloat(CURRENT_MAKER_CBU[i]) < parseFloat(LOAN_SERVICE_DETAILS.maker_min_cbu)){
                let mname= $(this).select2('data')[0]['text'];
                MAKER_OBJ.push({mcbu,mname});
            }
        });

        let cbu_div = ` <div class="alert alert-warning p-3" id="cbu-alert">
                            <h6 class="font-weight-bold">Minimum CBU for comaker (₱<span>${number_format(LOAN_SERVICE_DETAILS.maker_min_cbu,2)}</span>) not met</h6>
                            <ul id="ul_comaker_error">
                            
                            </ul>
                        </div>`;

        if(MAKER_OBJ.length > 0){
            if($('#div.cbu-alert').length == 0){
                $('#cbu-div').html(cbu_div);
            }
            $('#ul_comaker_error').html('');

            $.each(MAKER_OBJ,function(i,item){
                $('#ul_comaker_error').append(`<li>${item.mname} - ₱${number_format(item.mcbu,2)}</li>`);
            });
        }else{
            $('#cbu-div').html('');
        }



    }
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/loan_application_details_form.blade.php ENDPATH**/ ?>