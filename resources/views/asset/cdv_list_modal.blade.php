<style type="text/css">    
    #payroll_employee_modal{
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
    }
    legend.w-auto{
        font-size: 20px;
    }
    .modal-master-list{
        max-width: 50% !important;
        min-width: 50% !important;
    }
    .modal { overflow: auto !important; }
</style>
<div class="modal fade" id="modal_cdv_list"  role="dialog" aria-labelledby="booking" aria-hidden="false">
    <div class="modal-dialog modal-master-list" >
        <div class="modal-content">

            <div class="modal-body">
                <div class="row">

                   <div class="col-md-12"><h4>Cash Disbursement List</h4></div>  
                   <div class="col-md-12"><input type="text" name="" class="form-control" placeholder="Search" id="txt_search_cdv"></div>  
                   <div class="col-md-12">


                    <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                        <table class="table table-bordered table-striped table-head-fixed tbl-inputs" style="white-space: nowrap;" id="tbl_cdv_list">
                            <thead>
                                <tr>
                                    <th>CDV# </th>
                                    <th>Date</th>
                                    <th>Payee</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cdv_list_body">

                            </tbody>
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
    $(document).ready(function(){
      $("#txt_search_cdv").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tbl_cdv_list tr.cdv_list_row").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });
    function parse_cdv_list(){
        $.ajax({
            type        :       'GET',
            url         :       '/asset/parse/cdv_list',
            beforeSend  :       function(){
                show_loader();
            },
            success     :      function(response){
                hide_loader();
                console.log({response});
             
                var out = '';
                $.each(response.cdv,function(i,item){
                    out += '<tr class="cdv_list_row">';
                    out += '    <td style="text-align:center">'+item.id_cash_disbursement+'</td>';
                    out += '    <td>'+item.date+'</td>';
                    out += '    <td>'+item.payee+'</td>';
                    out += '    <td>'+item.description+'</td>';
                    out += '    <td><a class="btn btn-xs bg-gradient-success2" style="width:100%" onclick="parseCDV('+item.id_cash_disbursement+')">Select</a></td>';
                    out += '</tr>';
                })
                $('#cdv_list_body').html(out);
                $('#modal_cdv_list').modal('show');
                
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
    function redirect_create(id,name){
        console.log({id,name})
        $('#sel_employee').select2('destroy');
        $('#sel_employee').html('<option value="'+id+'">'+id+' || '+name+'</option>');
        intialize_select2();

        $('#sel_employee').trigger("change")
        $('#modal_cdv_list').modal('hide')
        $('#payroll_employee_modal').modal('show');

    }
</script>
@endpush


