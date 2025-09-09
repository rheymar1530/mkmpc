<style type="text/css">
    .tbl_loan_req tr>th{
        padding: 5px;
        padding-left: 5px;
        padding-right: 5px;
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 12px;
    }
    .tbl_loan_req tr>td,.tbl_requirements tr>td{
        padding: 0px;
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
<div class="modal fade" id="loan_app_modal"  role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-conf" >
        <div class="modal-content">
            <form id="frm_loan_application_details">
                <div class="modal-body">
                    <!-- NET PAY -->
                    <div class="col-md-12">
                        <fieldset class="border" style="padding-left: 10px;padding-right: 10px;">
                            <legend class="w-auto p-1">Net Pay</legend>
                            <div class="col-md-8">
                                <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                                    <span>Enter 2 Latest Net Pay</span>
                                    <table class="table table-bordered table-stripped table-head-fixed tbl_loan_req tbl-net-pays" style="white-space: nowrap;">
                                        <thead>
                                            <tr>
                                                <th class="table_header_dblue">Period Start</th>
                                                <th class="table_header_dblue">Period End</th>
                                                <th class="table_header_dblue">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="net_body">
                                            @for($i=0;$i<2;$i++)
                                            <tr class="row_net">
                                                <td><input type="date" name="" required class="form-control frm-net frm-requirements" key="period_start" value="{{$net_pays[$i]->period_start ?? ''}}"></td>
                                                <td><input type="date" name="" required class="form-control frm-net frm-requirements" key="period_end" value="{{$net_pays[$i]->period_end ?? ''}}"></td>
                                                <td><input type="text" name="" required class="form-control frm-net class_amount frm-requirements" value="{{$net_pays[$i]->amount ?? '0.00'}}" key="amount"></td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>    
                                </div>  
                            </div>
                        </fieldset>
                    </div>
                    <div class="form-group col-md-12" style="margin-bottom:unset;">
                        <div class="custom-control custom-checkbox" style="margin-top:20px">
                            <input type="checkbox" class="custom-control-input" id="chk_has_other_loan" key="has_other_loans" <?php echo (isset($other_lendings) && count($other_lendings) > 0)?'checked':''; ?>>
                            <label class="custom-control-label" for="chk_has_other_loan">Do you have any outstanding loans with other lenders?</label>
                        </div>                                  
                    </div>
                    <!-- OTHER LENDER -->
                    <div class="col-md-12">
                        <fieldset class="border" style="padding-left: 10px;padding-right: 10px;">
                            <legend class="w-auto p-1">Other Lending <button type="button" class="btn btn-sm bg-gradient-primary frm-other-lending" style="margin-left:10px" onclick="append_lender()"><i class="fa fa-plus"></i>&nbsp;Add</button></legend>

                            <div class="col-md-12">
                                <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                                    <table class="table table-bordered table-stripped table-head-fixed tbl_loan_req tbl-other-lending" style="white-space: nowrap;">
                                        <thead>
                                            <tr>
                                                <th class="table_header_dblue" style="min-width:140px;max-width:140px">Name</th>
                                                <th class="table_header_dblue">Date Started</th>
                                                <th class="table_header_dblue">Date of Maturity</th>
                                                <th class="table_header_dblue">Amount</th>
                                                <th class="table_header_dblue" style="min-width:15px;max-width:15px"></th>
                                            </tr>
                                        </thead>  
                                        <tbody id="other-lending-body">
                                            <tr class="row_other_lending">
                                                <td><input type="text" name="" required class="form-control frm-other-lending frm-requirements" key="name"></td>
                                                <td><input type="date" name="" required class="form-control frm-other-lending frm-requirements" key="date_started"></td>
                                                <td><input type="date" name="" required class="form-control frm-other-lending frm-requirements" key="date_ended"></td>
                                                <td><input type="text" name="" required class="form-control frm-other-lending class_amount frm-requirements" value="0.00" key="amount"></td>
                                                <td><a onclick="remove_other_lending(this)" style="margin-left:5px;margin-top: 10px !important;" class="frm-other-lending"><i class="fa fa-times"></i></a></td>
                                            </tr>
                                        </tbody>                                      
                                    </table>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <!-- COMAKERS -->
                    <div class="col-md-12">
                        <fieldset class="border" style="padding-left: 10px;padding-right: 10px;">
                            <legend class="w-auto p-1">Comaker(s)</legend>
                            <div class="col-md-12">

                                <table class="table table-bordered table-stripped table-head-fixed tbl_loan_req tbl-comakers" style="">
                                    <thead>
                                        <tr >
                                            <th class="table_header_dblue" width="2%">No.</th>
                                            <th class="table_header_dblue">Name</th>
                                        </tr>
                                    </thead>  
                                    <tbody>
                                        @for($i=0;$i<$loan_service->no_comakers;$i++)
                                            <tr class="row_comaker">
                                                <td class="cus-font" style="text-align:center;">{{$i+1}}</td>
                                                <td>
                                                    <select id="sel_comaker_{{$i+1}}" class="form-control frm-comakers frm-requirements p-0 select2 sel_comaker cus-font"  key='id_member' required>
                                                        <?php
                                                            if(isset($comakers) && count($comakers) > 0){
                                                                echo "<option value='".$comakers[$i]->tag_id."'>".$comakers[$i]->tag_value."</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            @endfor
                                        </tbody>                                      
                                    </table>

                                </div>
                            </fieldset>
                        </div>                   
                    </div>
                    <div class="modal-footer" style="padding:5px;padding-left: 10px;">
                        <span id="spn_btn_save"> <button class="btn bg-gradient-success" id="btn_save">Save</button></span>

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script type="text/javascript">
        $lender_row = '<tr class="row_other_lending">'+$('tr.row_other_lending').html()+'</tr>';

        // $('.frm-requirements').attr('required',false);

        function append_lender(){
            $('#other-lending-body').append($lender_row);
        }
        function remove_other_lending(obj){
            var parent_row = $(obj).closest('tr.row_other_lending');
            parent_row.remove()
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
    </script>
    @endpush