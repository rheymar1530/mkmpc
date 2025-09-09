<style type="text/css">    

    .modal { overflow: auto !important; }
</style>
<div class="modal fade" id="modal-employee"  role="dialog" aria-labelledby="booking" aria-hidden="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-body">
                <div class="row">

                 <div class="col-md-12"><h4 class="head_lbl text-center">Member List</h4></div>  
                 <div class="col-md-12"><input type="text" name="" class="form-control" placeholder="Search" id="txt_search_employee"></div>  
                 <div class="col-md-12">

                    <div class="form-group my-0 mt-3" id="div_books">
                        <div class="form-check">
                            <input class="form-check-input" id="chk-sel-member" type="checkbox">
                            <label class="form-check-label" for="chk_cancel">Select all member</label>
                        </div>                      
                    </div>
                    <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;overflow-x: auto">
                        <table class="table table-bordered table-striped table-head-fixed tbl_payroll_employee" style="white-space: nowrap;" id="tbl-member-list">
                            <colgroup width="5%"></colgroup>
                            <tbody>
                                @foreach($loans as $id_member=>$loan)
                                <tr class="row-member" data-id="{{$id_member}}">
                                    <td><input type="checkbox" class="chk-member"></td>
                                    <td>{{$loan[0]->member}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>    
                    </div> 
                </div>
            </div>
        </div>
        <div class="modal-footer" style="padding:5px;padding-left: 10px;">
            <button class="btn bg-gradient-success2" onclick="ChooseMember()">Select</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
  </div>
</div>
</div>


@push('scripts')
<script type="text/javascript">
    const LOANS = jQuery.parseJSON('<?php echo json_encode($loans ?? [])?>');

    let SELECTED_MEMBER = [];
    $(document).ready(function(){
        $("#txt_search_employee").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tbl-member-list tr.row-member").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
    const ChooseMember = ()=>{
        let t = [];
        $('#r-no-member').remove();
        $('.chk-member').each(function(){
            var prow = $(this).closest('tr');
            var member_id = prow.attr('data-id');
            var checked = $(this).prop('checked');

            if(checked){
                if($.inArray(member_id,SELECTED_MEMBER) < 0){
                    $('#tbl_loan').children('thead').after(DrawMemberRow(member_id,LOANS[member_id]));
                }
                t.push(member_id);                
            }else{
                if($.inArray(member_id,SELECTED_MEMBER) >= 0){
                    $(`tbody.bmember[data-member-id="${member_id}"]`).remove();
                }
            }

            console.log({member_id});
        });
        if(t.length > 0){
           
        }else{
            $('#tbl_loan').children('thead').after(`                     <tr id='r-no-member'>
                        <td class="text-center" colspan="5">Select at least 1 member</td>
                    </tr>`);
        }
        SELECTED_MEMBER = t;

        ComputeAll();
        $('#modal-employee').modal('hide');
    }
    const DrawMemberRow=($id_member,$loan)=>{
        let MemberRowHTML = `<tbody class="borders bmember" data-member-id="${$id_member}" repayment-id="${REP_REFERENCE[$id_member] ?? 0}">`  
        $.each($loan,function($c,$lo){
            MemberRowHTML += `<tr class="rloan" data-loan="${$lo['loan_token']}">`;
            if($c == 0){
                MemberRowHTML += `<td class="font-weight-bold nowrap" rowspan="${$loan.length}"><i>${$lo['member']}</i></td>`;
            }   
            MemberRowHTML += `<td class="nowrap"><sup><a href="/loan/application/approval/${$lo['loan_token']}" target="_blank">[${$lo['id_loan']}] </a></sup>${$lo['loan_name']}</td>
                                    <td class="text-right">${number_format($lo['balance'],2)}</td>
                                    <td class="text-right">${number_format($lo['current_due'],2)}</td>
                                    <td class="in"><input class="form-control p-2 text-right txt-input-amount in-loan-payment" value="${number_format($lo['payment'],2)}"></td>`;
            MemberRowHTML += `</tr>`;
        });
        
        MemberRowHTML += `</tbody>`;

        return MemberRowHTML;
    }
    $('#chk-sel-member').click(function(){
        $('input.chk-member:visible').prop('checked',$(this).prop('checked'))
    })
</script>
@endpush


