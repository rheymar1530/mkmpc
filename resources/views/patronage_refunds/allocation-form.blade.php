@extends('adminLTE.admin_template')
@section('content')
<style>
.table-allocation th,td{
    font-size: 0.9rem;

}
.table-allocation th{
    vertical-align: middle !important;
    padding: 3px;
}
.table-allocation td{
    padding: 3px;
}

.footer td{
    font-weight: bold;
    text-align: right;
}
.b{
    font-weight: bold;
}
</style>
<div class="row">
    <!-- <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <input type="text" class="form-control" id="txt_search" placeholder="Search...">
                <div class="mt-2" style="max-height: calc(100vh - 50px);overflow-y: auto;">
                    <table class="table table-bordered">
                        @foreach($Groups as $g)
                        <tr>
                            <td>{{$g->groupings}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div> -->
    <div class="col-md-12">
         <div class="card">
            <div class="card-body mx-4">
                <div class="text-center">
                    <h5 class="head_lbl">Patronage Refund and Capital Share Allocation</h5>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="card c-border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="d-flex flex-column">

                                            <span class="text-md  font-weight-bold lbl_color">Year: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->year}}</span></span>
                                            <span class="text-md  font-weight-bold lbl_color">Interest on Capital Share Payable: <span class="ms-sm-2 font-weight-normal ml-2"> {{number_format($details->capital_share_p,2)}} <i>(Interest Rate @ {{round($details->capital_share_rate,2)}} %)</i></span></span>
                                            <span class="text-md  font-weight-bold lbl_color">Patronage Refund Payables: <span class="ms-sm-2 font-weight-normal ml-2"> {{number_format($details->patronage_refund_p,2)}} <i>(Interest Rate @ {{round($details->patronage_refund_rate,2)}} %)</i></span></span>

                                            <span class="text-md  font-weight-bold lbl_color">Remarks: <span class="ms-sm-2 font-weight-normal ml-2"> {{$details->remarks}}</span></span>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="form-group col-md-3">
                        <label class="lbl_color mb-2">Brgy/LGU/Regular</label>
                        <select class="form-control form-control-border" id="sel-groupings">
                            @foreach($Groups as $g)
                            <option value="{{$g->group_ref}}">{{$g->groupings}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <table class="table table-bordered table-allocation table-head-fixed mt-3">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Member</th>
                            <th>Total<br>Capital Share</th>
                            <th>Ave<br>Monthly CBU</th>
                            <th>Interest<br>on Capital Share</th>
                            <th>Total<br>Interest on Loan</th>
                            <th>Patronage <br> Refund</th>
                            <th>Total</th>
                            <th>Withdraw</th>
                            <th>Add on CBU</th>
                        </tr>
                    </thead>
                    <tbody id="body-allocation">

                    </tbody>

                    <tfoot id="footer-allocation">

                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <button class="btn btn-md float-right bg-gradient-success" id="btn-post">Save</button>
            </div>
         </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    const ID_PATRONAGE_CAPITAL_ALLOCATION = '{{$details->id_patronage_capital_allocation ?? 0}}';
    const AllocationTable = @json($AllocationTable);
    $(document).ready(function(){
        DrawTable(AllocationTable);
    })
    $(document).on('change','#sel-groupings',function(){
         let type = $(this).val();
        fetchAllocations(type);
    });

    const fetchAllocations = (type)=>{
        $.ajax({
            type        :          'GET',
            url         :          '/patronage-refund/fetch-member-allocations',
            beforeSend  :          function(){
                                   show_loader();
            },
            data        :          {'id_patronage_capital_allocation' : ID_PATRONAGE_CAPITAL_ALLOCATION , 'type' : type},
            success     :          function(response){
                                   hide_loader();
                                   DrawTable(response.allocations);
                                   console.log({response});
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

    const DrawTable = (allocations)=>{
        $('#body-allocation').html('');

        // Initialize totals
        let total_capital_share = 0;
        let total_ave_monthly_cbu = 0;
        let total_interest_capital_share = 0;
        let total_loan_interest = 0;
        let total_patronage_refund = 0;
        let total_total = 0;
        let total_def_val = 0;
        let total_w_cbu = 0;

        let out = '';
        $.each(allocations,function(i,item){
            total_capital_share += parseFloat(item.capital_share) || 0;
            total_ave_monthly_cbu += parseFloat(item.ave_monthly_cbu) || 0;
            total_interest_capital_share += parseFloat(item.interest_capital_share) || 0;
            total_loan_interest += parseFloat(item.loan_interest) || 0;
            total_patronage_refund += parseFloat(item.patronage_refund) || 0;
            total_total += parseFloat(item.total) || 0;
            total_def_val += parseFloat(item.def_val) || 0;
            total_w_cbu += parseFloat(item.w_cbu) || 0;

            out += `<tr class="row-member" data-id-member="${item.id_member}">`;
            out += `<td class="text-center">${i+1}</td>`;
            out += `<td>${item.Name}</td>`;
            out += `<td class="text-right">${number_format(item.capital_share,2)}</td>`;
            out += `<td class="text-right">${number_format(item.ave_monthly_cbu,2)}</td>`;
            out += `<td class="text-right">${number_format(item.interest_capital_share,2)}</td>`;
            out += `<td class="text-right">${number_format(item.loan_interest,2)}</td>`;
            out += `<td class="text-right">${number_format(item.patronage_refund,2)}</td>`;
            out += `<td class="text-right">${number_format(item.total,2)}</td>`;
            out += `<td class="text-right p-0"><input type="text" class="form-control w-100 class_amount text-right text-cash ff" name="cash" value="${number_format(item.def_val,2)}"></td>`;
            out += `<td class="text-right p-0"><input type="text" class="form-control w-100 class_amount text-right text-cbu ff" name="cbu" value="${number_format(item.w_cbu,2)}"></td>`;
            out += `</tr>`;
        });
        $('#body-allocation').html(out);
        let grand = '';
        grand += `<tr class="font-weight-bold bg-light">`;
        grand += `<td></td>`;
        grand += `<td class="text-right">GRAND TOTAL</td>`;
        grand += `<td class="text-right">${number_format(total_capital_share,2)}</td>`;
        grand += `<td class="text-right">${number_format(total_ave_monthly_cbu,2)}</td>`;
        grand += `<td class="text-right">${number_format(total_interest_capital_share,2)}</td>`;
        grand += `<td class="text-right">${number_format(total_loan_interest,2)}</td>`;
        grand += `<td class="text-right">${number_format(total_patronage_refund,2)}</td>`;
        grand += `<td class="text-right">${number_format(total_total,2)}</td>`;
        grand += `<td class="text-right pr-3" id="total-cash">${number_format(total_def_val,2)}</td>`;
        grand += `<td class="text-right pr-3" id="total-cbu">${number_format(total_w_cbu,2)}</td>`;
        grand += `</tr>`;

        $('#footer-allocation').html(grand);
    }

    function recomputeTotals() {

        let totalCash = 0;
        let totalCbu = 0;


        $('.text-cash').each(function () {
            let val = $(this).val().replace(/,/g, '');
            totalCash += parseFloat(val) || 0;
        });


        $('.text-cbu').each(function () {
            let val = $(this).val().replace(/,/g, '');
            totalCbu += parseFloat(val) || 0;
        });

        console.log({totalCash,totalCbu})

        $('#total-cash').text(number_format(totalCash, 2));
        $('#total-cbu').text(number_format(totalCbu, 2));
    }

    $(document).on('blur', '.ff', function () {
        recomputeTotals();
    });

    $('#btn-post').on('click', () => {

        const allocation = $('.row-member').map(function () {
            const temp = {};
            temp['id_member'] = $(this).attr('data-id-member');
            $(this).find('.ff').each(function () {
                temp[this.name] = decode_number_format(this.value);
            });
            return temp;
        }).get();

        const ajaxParam = {
            'allocations' : allocation,
            'id_patronage_capital_allocation' : ID_PATRONAGE_CAPITAL_ALLOCATION
        };


        $.ajax({
            type       :    'POST',
            url        :    '/patronage-refund/post-allocation',
            data       :    ajaxParam,
            beforeSend  :   function(){
                            $('.table-danger').removeClass('table-danger');
                            show_loader();
            },
            success    :    function(response){
                            console.log({response});
                            hide_loader();
                            if(response.RESPONSE_CODE == "SUCCESS"){
                                Swal.fire({
                                    title: response.message,
                                    text: '',
                                    icon: 'success',
                                    showCancelButton : false,
                                    showConfirmButton : false,
                                    timer : 2000
                                }).then(()=>{

                                });
                            }else if(response.RESPONSE_CODE == "ERROR"){
                                const InvalidRows = response.invalidRows;
                                Swal.fire({
                                    title: response.message,
                                    text: '',
                                    icon: 'warning',
                                    showCancelButton : false,
                                    showConfirmButton : false,
                                    timer : 2500
                                }).then(()=>{
                                    $.each(InvalidRows,function(i,id){
                                        console.log({id})
                                        $(`tr.row-member[data-id-member="${id}"]`).addClass('table-danger');
                                    })
                                });
                            }
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
        });

        console.log({ajaxParam});



    });
</script>
@endpush