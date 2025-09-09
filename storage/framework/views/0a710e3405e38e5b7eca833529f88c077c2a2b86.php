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
// $loan_status = [

?>
<div class="modal fade" id="offset_loan_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <form id="frm_submit_offset">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Active Loans
                        <span id="spn_id_cash_receipt"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                            <table class="table table-bordered table-stripped table-head-fixed tbl_loan_req" style="white-space: nowrap;">
                                <thead>
                                    <tr>
                                        <th class="table_header_dblue" width="50%">Loan Service</th>
                                        <th class="table_header_dblue">Balance</th>
                                        <th class="table_header_dblue">Amount to Pay</th>
                                    </tr>
                                </thead>
                                <tbody id="active_loan_body">

                                </tbody>
                            </table>    
                        </div>  
                    </div>
                </div>
                <div class="modal-footer" style="padding:5px;padding-left: 10px;">
                    <button class="btn bg-gradient-success2">Compute</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">

    let paid_act_loan_holder = {};
    var act_h = jQuery.parseJSON('<?php echo json_encode($active_loan_paid_holder ?? []); ?>');

    if(Object.keys(act_h).length > 0){
        paid_act_loan_holder = act_h;
    }

    $(document).on('change','#sel_status',function(){

    })
    function load_loan_offset(){
        
        if(parseInt('<?php echo $loan_service->id_loan_payment_type?>') == 2){
            terms = '<?php echo $loan_service->terms_token ?? "" ?>';

        }else{
            terms = $terms_token_holder;

        }   
        var terms = $terms_token_holder;

        var data = {
            'id_loan_service' : id_loan_service,
            'id_member' : id_member,
            'terms_token' : terms
        };


        $.ajax({
            type          :     'GET',
            url           :     '/loan/parseActiveLoans',
            data          :     data,
            beforeSend    :     function(){
                                show_loader();
            },
            success       :     function(response){
             console.log({response});
             hide_loader()
             var active_loan = response.active_loan;
             var out = '';
             if(active_loan.length > 0){
                $.each(active_loan,function(i,item){
                    var amt_paid = paid_act_loan_holder[item.loan_token] ?? 0;
                    out += `<tr class="row_active_loan" data-token="`+item.loan_token+`">
                    <td><input type="text" name="" class="form-control frm-requirements" value="`+item.loan+`" disabled></td>
                    <td><input type="text" name="" class="form-control frm-requirements class_amount" value="`+number_format(item.balance,2)+`" disabled></td>
                    <td><input type="text" name="" class="form-control frm-requirements class_amount loan_paid_amt" value="`+number_format(amt_paid,2)+`"></td>
                    </tr>`;
                })
                console.log({out});

            }else{
                out += '<tr><th colspan="3" style="text-align:center">No Active Loan Found</th></tr>'
            }
            $('#active_loan_body').html(out);
            $('#offset_loan_modal').modal('show')
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
        console.log({data});
    }
    $('#frm_submit_offset').on('submit',function(e){
        e.preventDefault();
        paid_act_loan_holder = {};
        $('tr.row_active_loan').each(function(){
            var paid_amt = decode_number_format($(this).find('.loan_paid_amt').val());
        
            if(paid_amt > 0){
                paid_act_loan_holder[$(this).attr('data-token')] = paid_amt;
            }
        });     
        calculate_loan() ;
        $('#offset_loan_modal').modal('hide')
    })
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/offset_modal.blade.php ENDPATH**/ ?>