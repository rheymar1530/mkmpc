<style type="text/css">
    .mandatory{
        border-color: rgba(232, 63, 82, 0.8) !important;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
        outline: 0 none;
    }
</style>
<?php
// $loan_status = [
    $loan_fees = DB::table('loan_fees')
                 ->where('visible',1)
                 ->orderBy('name','ASC')
                 ->get();

?>
<div class="modal fade" id="other_deduction_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <form id="frm_submit_deduction">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Other Deductions
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
                                        <th class="table_header_dblue" width="30%">Fee</th>
                                        <th class="table_header_dblue">Amount</th>
                                        <th class="table_header_dblue" width="50%">Remarks</th>
                                        <th class="table_header_dblue" width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="deduction-body">
                                    <tr class="row_deduction">
                                        <td>
                                            <select class="form-control form-control-border frm-requirements p-0 fee-type">
                                                @foreach($loan_fees as $lf)
                                                <option value="{{$lf->id_loan_fees}}">{{$lf->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-border frm-requirements class_amount deduction-amount" name="" value="0.00">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-border frm-requirements deduction-remarks" name="">
                                        </td>
                                        <td><a class="btn btn-xs bg-gradient-danger2" onclick="remove_deduction(this)"><i class="fa fa-trash"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>    
                        </div>  
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn bg-gradient-primary" onclick="AddFee()"><i class="fa fa-plus"></i>&nbsp;Add Fee</button>
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
@push('scripts')
<script type="text/javascript">
    let RowDeduction = `<tr class="row_deduction">${$('tr.row_deduction').html()}</tr>`;
    let DeductionData = [];

    let DeductionOBJ = jQuery.parseJSON(`<?php echo json_encode($other_deductions ?? []) ?>`);

    DeductionData=DeductionOBJ;

    function showDeductionModal(){
        ReDrawDeductions();
        $('#other_deduction_modal').modal('show');
    }

    const AddFee = ()=>{
        $('#deduction-body').append(RowDeduction);
    }

    const remove_deduction=(obj)=>{
        $(obj).closest('tr').remove();
    }
    $('#frm_submit_deduction').submit(function(e){
        e.preventDefault();
        let outDeductionData = [];
        $('tr.row_deduction').each(function(){
            
            var amount = decode_number_format($(this).find('.deduction-amount').val());
            if(amount > 0){
                var temp = {};

                temp['amount'] = amount;
                temp['id_loan_fees'] = $(this).find('.fee-type').val();
                temp['remarks'] = $(this).find('.deduction-remarks').val();

                outDeductionData.push(temp);
            }
        });
        DeductionData = outDeductionData;
        calculate_loan();
         $('#other_deduction_modal').modal('hide');
    });

    const ReDrawDeductions = ()=>{
        $('#deduction-body').html('');

        if(DeductionData.length == 0){
            AddFee();
            return;
        }
        $.each(DeductionData,function(i,item){
            AddFee();
            var lrow = $('tr.row_deduction').last();
            lrow.find('select.fee-type').val(item.id_loan_fees);
            lrow.find('input.deduction-amount').val(number_format(item.amount,2));
            lrow.find('input.deduction-remarks').val(item.remarks);
        })
    }

</script>
@endpush