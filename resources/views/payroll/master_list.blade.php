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
<div class="modal fade" id="modal_master_list"  role="dialog" aria-labelledby="booking" aria-hidden="false">
    <div class="modal-dialog modal-master-list" >
        <div class="modal-content">
            <?php 
                $employeeType = DB::table('employee_type')->get();
            ?> 
            <div class="modal-body">
                <div class="row">

                   <div class="col-md-12"><h4 class="head_lbl text-center">Employee Master List</h4></div>  
                   <div class="col-md-6 col-12">
                       <label class="lbl_color mb-0">Employee Type</label>
                       <select class="form-control form-control-border" id="sel-employee-type">
                           <option value="0">** ALL **</option>
                           @foreach($employeeType as $et)
                           <option value="{{$et->id_employee_type}}">{{$et->description}}</option>
                           @endforeach
                       </select>
                   </div>
                   <div class="col-md-12 mt-2"><input type="text" name="" class="form-control" placeholder="Search" id="txt_search_employee"></div>  
                   <div class="col-md-12">
                    <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                        <table class="table table-bordered table-striped table-head-fixed tbl_payroll_employee" style="white-space: nowrap;" id="tbl_employee_master_list">
                            <thead>
                                <tr>
                                    <th>ID Employee</th>
                                    <th>Employee</th>
                                    <th>Remarks</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="employee_list_body">

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
      $("#txt_search_employee").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tbl_employee_master_list tr.row_employee_list").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });
    function parse_master_list(){
        $.ajax({
            type        :       'GET',
            url         :       '/payroll/master_list',
            beforeSend  :       function(){
                show_loader();
            },
            success     :      function(response){
                hide_loader();
                console.log({response});
                var out = '';
                $.each(response.employee_list,function(i,item){
                    out += '<tr class="row_employee_list" type="'+item.id_employee_type+'">';
                    out += '    <td>'+item.id_employee+'</td>';
                    out +=     `<td>${item.name} <i class="text-xs">[${item.description}]</i></td>`;

                    out += '    <td>'+(($payroll_employee[parseInt(item.id_employee)] != undefined)?'<span class="badge badge-success">Done</span>':'')+'</td>';
                    out += '    <td>'+(($payroll_employee[parseInt(item.id_employee)] == undefined)?'<a class="btn btn-xs bg-gradient-success2" onclick="redirect_create(`'+item.id_employee+'`,`'+item.name+'`)">Create Payroll</a>':'')+'</td>';
                    out += '</tr>';
                })
                $('#employee_list_body').html(out);
                $('#modal_master_list').modal('show')
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
        $('#modal_master_list').modal('hide')
        $('#payroll_employee_modal').modal('show');

    }
    $(document).on('change','#sel-employee-type',function(){
        var val = $(this).val();
        if(val == 0){
            $('.row_employee_list').show();
        }else if(val >= 1){
            $('.row_employee_list').hide();
            $('.row_employee_list[type="'+val+'"]').show();
        }
    })
</script>
@endpush


