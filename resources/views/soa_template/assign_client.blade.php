@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
.list-group{
	padding: 0px;
	border: none;
	font-size: 14px;
    margin-right: 10px;
}
.list-group-item{
	padding: 5px;
    font-size: 12px;
	font-weight: bold;
    font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
.form-row{
    margin-top: -5px !important;
}
label.lbl_gen{
    margin-bottom: -10px !important;
    font-size: 12px;
    font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
.select2 {
    width:100%!important;
}
.list-client-item{
    font-weight: normal;
}
</style>
<div class="container-fluid" style="margin-top:-15px">
	<h4>{{$report_details->name}}&nbsp; <span class="spn_report_details" style="font-size:12px">({{$report_details->description}})</span></h4>
	<div class="row">
		<div class="col-sm-3">
            <div class="card card-primary">
                <div class="card-header bg-gradient-primary" style="padding: 5px 5px 5px 5px !important;"><strong>Selected Fields</strong></div>
                <div class="card-body" style="max-height: calc(100vh - 150px);overflow-y: auto;">
                	<ul class="list-group">
                		@if(isset($selected))
                			@foreach($selected as $count=>$field)
                				<li class="list-group-item">{{$field->custom_col}}</li>
                			@endforeach
                		@endif					
					</ul>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card card-warning">
                <div class="card-header bg-gradient-warning" style="padding: 5px 5px 5px 5px !important;"><strong>Group Fields</strong></div>
                <div class="card-body" style="max-height: calc(100vh - 150px);overflow-y: auto;">
                	<ul class="list-group">
                		@if(isset($groupings))
                			@foreach($groupings as $count=>$field)
                				<li class="list-group-item">{{$field->alias}}</li>
                			@endforeach
                		@endif					
					</ul>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card card-danger">
                <div class="card-header bg-gradient-danger" style="padding: 5px 5px 5px 5px !important;"><strong>Assigned Client</strong></div>
                <div class="card-body">
                    <form id="frm_add_client">
                        <div class="form-row">
                            <div class="col-md-10 form-group ">
                                <label for="sel_account" class="lbl_gen">Choose Client</label>
                                <select class="form-control select2" id="sel_account" required>

                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <label class="lbl_gen " >&nbsp;</label>
                                <button type="submit" class="btn btn-sm bg-gradient-success form-control" id="btn_add_client">Add</button>
                                
                            </div>
                        </div>
                    </form>
                    <hr style="margin-top:0px">
                	<div class="form-group" style="margin-bottom:0px">
                        <input type="text" name="" class="form-control" placeholder="Search client from this template" onkeyup="search_fields(this)">
                    </div>
                    <span id="spn_client_counter">0 Client</span>
                    <div  style="max-height: calc(100vh - 150px);overflow-y: auto;">
                    	<ul class="list-group" id="list-client">
                            @if(isset($client_list))
                                @if(count($client_list) > 0)
                                    @foreach($client_list as $list)
                                        <li data-id="{{$list->id_client_profile}}" class="list-group-item list-client-item">{{$list->account_no}} - {{$list->name}} <span style="float: right"><a class="fa fa-times" title="Remove Client from this template" style="cursor: pointer;" onclick="remove_client(<?php echo $list->id_client_profile; ?>)"></a></span></li>
                                    @endforeach
                                @else
                                    <h4 class="h_no_client_record">No Client Found</h4>
                                @endif
                            @endif
                    		
    					</ul>
					</div>
                </div>
            </div>
        </div>
	</div>
</div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
           $('#spn_client_counter').text($('#list-client li').length+" Client(s)");
        })
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
        $("#sel_account").select2({
            minimumInputLength: 2,
            createTag: function (params) {
                return null;
            },
            ajax: {
                tags: true,
                url: '/admin/serch_account',
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
        $('#frm_add_client').submit(function(e){
            e.preventDefault();
            var account = $('#sel_account').val();
            post_validate(0,account);
        })
        function post_validate(opcode,account){
            $.ajax({
                type            :   'POST',
                url             :   '/admin/soa_template/post_validate',
                data            :   {'opcode'  : opcode,
                                     'account' : account,
                                     'id_report' : '<?php echo $report_details->id_report ?? "" ?>'},
                beforeSend      :   function(){
                                        change_loader_button(true);
                },
                success         :   function(response){
                                        console.log({response});
                                        change_loader_button(false);
                                        if(response.STATUS_CODE == "POST_DIRECTLY" || response.STATUS_CODE == "POST_UPDATE"){
                                            $('.h_no_client_record').remove();
                                            toastr.success(response.account_name+' Successfully added on this template');
                                            $('#list-client').append('<li class="list-group-item list-client-item" data-id="'+account+'">'+response.account_name+'&nbsp;&nbsp;<span class="badge badge-success">New</span> <span style="float: right"><a class="fa fa-times" title="Remove Client from this template" style="cursor: pointer;"  onclick="remove_client('+account+')"></a></span></li>')
                                            $('#sel_account').val(null).trigger('change');
                                        }else if(response.STATUS_CODE == "NO_CHANGES"){
                                            toastr.info(response.account_name+' is already exist on this template');
                                        }else if(response.STATUS_CODE == "OTHER_REPORTS"){
                                            Swal.fire({
                                                title: "Existing Template",
                                                html:response.account_name+" has already a assigned template (<a href='/admin/soa_template/edit?id_report="+response.id_report+"' title='Click to view template' target='_blank'>"+response.REPORT_NAME+"</a>). If you want to proceed please click <b>Change to this template</b> button",
                                                
                                                icon: 'warning',
                                                showDenyButton: false,
                                                showCancelButton: true,
                                                confirmButtonText: `Change to this template`,
                                            }).then((result) => {
                                            if (result.isConfirmed) {
                                                post_validate(1,account)
                                            }

                                            })  
                                        }else if(response.STATUS_CODE == "REMOVE_CLIENT_REPORT"){
                                            toastr.success(response.account_name+' Successfully removed on this template');
                                            $("[data-id="+account+"]").remove();
                                        }
                                        $('#spn_client_counter').text($('#list-client li').length+" Client(s)");
                                    
                },
                error: function(xhr, status, error){
                    change_loader_button(false);
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    Swal.fire({
                        position: 'center',
                        icon: 'warning',
                        title: "Error-"+errorMessage,
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText : "Close"
                    })
                }  
            })
        }
        function change_loader_button(state){
            if(state){
                $('#btn_add_client').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>')
                $('#btn_add_client').append("&nbsp;&nbsp;Saving");
                $('#btn_add_client').attr('disabled',true);
            }else{
                $('#btn_add_client').html("Add");
                $('#btn_add_client').attr('disabled',false);
            }

        }
        function search_fields(obj){
            var value = $(obj).val().toLowerCase();
            $("#list-client li").filter(function() {
              $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        }
        function remove_client(id_client_profile){
            Swal.fire({
                title: 'Confirmation',
                text  : 'Do you want to remove this client account from this template ?',
                icon: 'warning',
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    post_validate(3,id_client_profile);
                } 
            })  
           
        }
    </script>
@endpush

