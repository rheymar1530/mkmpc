<style type="text/css">    
    legend.w-auto{
        font-size: 20px;
    }
    .modal-monthly-dep{
        max-width: 60% !important;
        min-width: 60% !important;
    }
    .modal { overflow: auto !important; }
</style>
<div class="modal fade" id="modal_monthly_dep"  role="dialog" aria-labelledby="booking" aria-hidden="false">
    <div class="modal-dialog modal-monthly-dep" >
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                   <div class="col-md-12"><h4 id="head_asset_code" class="head_lbl"></h4></div>
                   <div class="col-md-12">
                    <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                        <table class="table table-bordered table-striped table-head-fixed tbl-inputs table-hover" style="white-space: nowrap;" id="tbl_cdv_list">
                            <thead>
                                <tr>
                                    <th>Month </th>
                                    <th>Start Value</th>
                                    <th>Depreciation Amount</th>
                                    <th>Accumulated Depreciation</th>
                                    <th>End Value</th>
                                </tr>
                            </thead>
                            <tbody id="monthly_dep_body"></tbody>
                        </table>    
                    </div> 
                </div>
            </div>
        </div>
        <div class="modal-footer" style="padding:5px;padding-left: 10px;">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
  </div>
</div>
</div>

@push('scripts')
<script type="text/javascript">
    function parseMonthlyDep($id_asset_item,$year){
        $.ajax({
            type        :       'GET',
            url         :       '/asset/parse/monthly_dep',
            data        :       {'id_asset_item' : $id_asset_item,'year' : $year},
            beforeSend  :       function(){
                show_loader();
            },
            success     :      function(response){
                hide_loader();
                console.log({response});
                
             
                var out = '';
                $.each(response.dep,function(i,item){
                    out += '<tr class="dep_list_row">';
                    out += '    <td>'+item.month+'</td>';
                    out += '    <td class="class_amount">'+number_format(item.start_book_value,2)+'</td>';
                    out += '    <td class="class_amount">'+number_format(item.depreciation_amount,2)+'</td>';
                    out += '    <td class="class_amount">'+number_format(item.accumulated_depreciation,2)+'</td>';
                    out += '    <td class="class_amount">'+number_format(item.end_book_value,2)+'</td>';
                    
                    out += '</tr>';
                })

                $('#monthly_dep_body').html(out);
                $('#head_asset_code').text(response.details.asset_dec)
                $('#modal_monthly_dep').modal('show');
                // $('#cdv_list_body').html(out);
                // $('#modal_cdv_list').modal('show');
                
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

</script>
@endpush


