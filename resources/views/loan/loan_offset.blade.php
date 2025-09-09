<div class="col-md-12 p-0" style="margin-top:15px">
    <div class="card">
        <div class="card-header bg-gradient-primary custom_card_header">
            <h5>Active Loans</h5>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                    <table class="table table-bordered table-stripped table-head-fixed tbl_loan_req" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th class="table_header_dblue">Loan Service</th>
                                <th class="table_header_dblue">Balance</th>
                                <th class="table_header_dblue">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $existing_loan = $existing_loan['active_loan']??[];
                            ?>
                        	@foreach($existing_loan as $ex)
                        		<tr class="row_active_loan" data-token="{{$ex->loan_token}}">
                        			<td><input type="text" name="" class="form-control frm-requirements" value="{{$ex->loan}}" disabled></td>
                        			<td><input type="text" name="" class="form-control frm-requirements class_amount" value="{{number_format($ex->balance,2)}}" disabled></td>
                        			<td><input type="text" name="" class="form-control frm-requirements class_amount loan_paid_amt" value="{{number_format($ex->payment,2)}}"></td>
                        		</tr>

                        	@endforeach
                        </tbody>
                    </table>    
                </div>  
            </div>
        </div>
    </div>
</div>
