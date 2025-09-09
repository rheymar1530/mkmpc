<div class="modal fade" id="member-modal" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <form id="frm-add-member">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4 lbl_color">Select Member</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-row" style="margin-top:10px">
                            <div class="form-group col-md-12">
                                <label for="sel_status">Member</label>
                                <select class="form-control p-0" id="sel-member" required>
                                    
                                </select>        
                            </div>
                        </div>
                        <div id="div_reason_cancel"></div>
                    </div>
                </div>
                <div class="modal-footer" style="padding:5px;padding-left: 10px;">
                    <button class="btn bg-gradient-success2">Add</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        initMemberSelect();
      $(document).on('select2:opening select2:closing', function (event) {
        event.stopPropagation();
      });
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        }); 
    })
    
    
    function initMemberSelect(){        
        $("#sel-member").select2({
            minimumInputLength: 2,
            width: '100%',
            dropdownParent: $('#member-modal'),
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


    $('#frm-add-member').submit(function(e){
        e.preventDefault();
        var val = $('#sel-member').val();
        var text = $('#sel-member').select2('data')[0].text;
        var q = text.split(" || ");
        var MemName = $.trim(q[1]);

        
        if($(`tr.row-member[data-id='${val}']`).length > 0){
            toastr.success(`${MemName} already exists`);
            $(`tr.row-member[data-id='${val}']`).addClass('text-success');

            setTimeout(function() {
              $(`tr.row-member[data-id='${val}']`).removeClass('text-success');
            }, 5000);
        }else{
            membOut = `<tr class="row-member" data-id="${val}" data-add="1">
            <td class="text-center tc"></td>
            <td class="">${MemName}</td>
            <td class="p-0"><input class="form-control form-control-border txt-input-amount text-right txt-change-payable" value="0.00"></td>
            <td class="p-0"><a class="btn btn-xs bg-gradient-danger w-100" onclick="removeMember(this)"><i class="fa fa-times"></i></a></td>
            </tr>`;

            $('#memberBody').append(membOut);

            $('#sel-member').val(null).trigger('change');
            $('#member-modal').modal('hide');
            refreshCounter();
        }
    })

    const refreshCounter = ()=>{
        $('.tc').each(function(i){
            $(this).text(i+1);
        })
    }
    const removeMember = (obj)=>{
        $(obj).closest('tr').remove();
        refreshCounter();
        ComputeAll();
    }
</script>
@endpush