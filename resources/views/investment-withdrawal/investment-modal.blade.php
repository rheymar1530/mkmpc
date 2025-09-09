<style type="text/css">
    .mandatory{
        border-color: rgba(232, 63, 82, 0.8) !important;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
        outline: 0 none;
    }
    .form-row  label{
        margin-bottom: unset !important;
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 13px;
    }
    .form-row{
        margin-top: -7px;
    }

</style>
<?php
$STATUS_LISTS = [
    1=>'Released',
    10=>'Cancel'
];
?>
<div class="modal fade" id="withdrawable_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content">
            <form id="frm_select_withdrawables">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4 lbl_color">Withdrawable(s)
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 col-12 px-4">
                            <select class="form-control form-control-border col-8" id="sel_filter_member">
                                <option value="ALL">** ALL **</option>
                                @foreach($withdrawables as $id_m=>$d)
                                <option value="{{$id_m}}">{{$d[0]->investor}}</option>
                                @endforeach
                            </select>
                            <table class="table table-head-fixed table-hover table-bordered mt-3 inv-table">
                                <thead>
                                    <tr>
                                        <th colspan="5">Investment Details</th>
                                        <th colspan="4">Withdrawables</th>
                                    </tr>
                                    <tr>
                                        <th class="table_header_dblue">Investor</th>
                                        <th class="table_header_dblue">Investment ID</th>
                                        <th class="table_header_dblue">Investment Product</th>
                                        <th class="table_header_dblue">Maturity Date</th>
                                        <th class="table_header_dblue">Principal Amount</th>

                                        <th class="table_header_dblue">Principal</th>
                                        <th class="table_header_dblue">Interest</th>
                                        <th class="table_header_dblue">Withdrawable Amount</th>
                                        <th class="table_header_dblue" width="5%"><input type="checkbox" id="sel_all"></th>
                                    </tr>
                                </thead>
                                @foreach($withdrawables as $id_m=>$invs)    
                                <?php
                                $investor_key[$id_m] = $invs[0]->investor;
                                ?>
                                <tbody class="body_investment" id="member_{{$id_m}}">
                                    @foreach($invs as $c=>$in)
                                    <tr class="with_row {{($c==0)?'b-top':''}}" data-id-investor="{{$id_m}}" data-id="{{$in->id_investment}}" >
                                        @if($c == 0)
                                        <td class="v-center font-weight-bold" rowspan="{{count($invs)}}">{{$in->investor}}</td>
                                        @endif
                                        <td class="text-center v-center"><a href="/investment/view/{{$in->id_investment}}" target="_blank">{{$in->id_investment}}</a></td>
                                        <td>{{$in->product_name}}</td>
                                        <td>{{$in->maturity_date}}</td>
                                        <td class="text-right pr-3">{{number_format($in->investment_amount,2)}}</td>

                                        <td class="text-right pr-3">{{number_format($in->withdrawable_principal,2)}}</td>
                                        <td class="text-right pr-3">{{number_format($in->withdrawable_interest,2)}}</td>
                                        <td class="text-right pr-3">{{number_format($in->withdrawable,2)}}</td>
                                        <td class="text-center v-center"><input type="checkbox" class="chk_investment"></td>
                                    </tr>
                                    <?php
                                    $temp = array();
                                    $temp['PRODUCT'] = $in->product_name;
                                    $temp['ID_MEMBER'] = $id_m;
                                    $temp['MAX_AMOUNT'] = $in->withdrawable;
                                    $temp['PRINCIPAL'] = $in->withdrawable_principal;
                                    $temp['INTEREST'] = $in->withdrawable_interest;

                                    $withdrawables_validation[$in->id_investment] = $temp;
                                    ?>
                                    @endforeach
                                </tbody>                                @endforeach
                            </table>
                        </div>
                        <!-- <div class="col-md-4 col-12">
                            <div id="withdrawal_cards">

                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer" style="padding:5px;padding-left: 10px;">
                    <button class="btn bg-gradient-success2">Select</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    const INVESTOR_KEY = jQuery.parseJSON('<?php echo json_encode($investor_key ?? []); ?>');
    const WITHDRAWABLE_KEY = jQuery.parseJSON('<?php echo json_encode($withdrawables_validation ?? []); ?>');

    const FORM_ROW = `<?php echo withdrawal_form_row([]); ?>`;

    const WITHDRAWALS = jQuery.parseJSON('<?php echo json_encode($withdrawals ?? []); ?>');
    const opcode = {{$opcode}};
    
    const ID_INVESTMENT_WITHDRAWAL_BATCH = {{$batch_details->id_investment_withdrawal_batch ?? 0}};
</script>
<script type="text/javascript">
    function show_withrawables(){
        $('#withdrawable_modal').modal('show');
    }
    $(document).ready(function(){
        $.each(WITHDRAWALS,function(i,item){
            $(`tr.with_row[data-id="${item.id_investment}"]`).find('.chk_investment').trigger('click',parseFloat(item.amount));
            $(`tr.with_row[data-id="${item.id_investment}"]`).attr('def-amount',parseFloat(item.amount));
        });
        $('#frm_select_withdrawables').trigger("submit",true);      
    });



    function remove_inv(obj){
        var parent_card = $(obj).closest('.c-withdrawal-form');
        var data_id = parent_card.attr('data-id');
        parent_card.remove();
        $(`tr.with_row[data-id="${data_id}"]`).find('.chk_investment').prop('checked',false);
        set_no_transaction();
    }

    $(document).on('change','#sel_filter_member',function(){
        var val = $(this).val();
        if(val == "ALL"){
            $('.body_investment').removeClass('hidden');
        }else{
            $('.body_investment').addClass('hidden');
            $(`#member_${val}`).removeClass('hidden');
        }
    });

    $(document).on('click','#sel_all',function(){
        var checked = $(this).prop('checked');
        $('.body_investment:visible').each(function(){
            $(this).find('.chk_investment').each(function(){
                $(this).prop('checked',!checked).trigger('click');
            })
        })
    });

    $('#frm_select_withdrawables').submit(function(e,onload=false){
        e.preventDefault();
        $('tr.with_row').each(function(){

            var parent_row = $(this);
            var id_investment = parent_row.attr('data-id');
            var checked = parent_row.find(".chk_investment").prop('checked');
            var def_amount = $(this).attr('def-amount');
            if(checked){
                if($(`.c-withdrawal-form[data-id=${id_investment}]`).length == 0){
                    $('#with_form_body').append(FORM_ROW);
                    var last_row = $('.c-withdrawal-form').last();
                    var inv_d = WITHDRAWABLE_KEY[id_investment];
                    let as_amount = (onload)?def_amount:inv_d['MAX_AMOUNT'];
                    last_row.attr('data-id',id_investment);
                    last_row.find('.col-investor').text(INVESTOR_KEY[inv_d['ID_MEMBER']]);
                    last_row.find('.col-inv-prod').text(inv_d['PRODUCT']+` [ID# ${id_investment}]`);
                    last_row.find('.col-principal').text(number_format(inv_d['PRINCIPAL'],2));
                    last_row.find('.col-interest').text(number_format(inv_d['INTEREST'],2));
                    last_row.find('.col-total').text(number_format(inv_d['MAX_AMOUNT'],2));
                    last_row.find('input.text-with-amt').val(number_format(as_amount,2));                    
                }
            }else{
                $(`.c-withdrawal-form[data-id="${id_investment}"]`).remove();
            }
        });
        set_no_transaction();
        $('#withdrawable_modal').modal('hide');
    })

    function set_no_transaction(){
        if($('tr.c-withdrawal-form').length > 0){
            $('tr#no_record').remove();
        }else{
            $('#with_form_body').html(`<tr id="no_record"><td colspan="7" class="text-center">No Transaction</td></tr>`)
        }
    }
</script>
@endpush