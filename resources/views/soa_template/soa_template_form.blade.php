@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
    .lbl_form{
        margin-top: -5px !important;
        margin-bottom: 0px;
    }
</style>
<div class="container-fluid">
	<div class="row">
        @if($op == 1)
            <div class="col-sm-12" style="margin-top: -20px !important;margin-bottom: 20px;">
                 <span class="text-muted">&nbsp;&nbsp;Description : {{$details->description}}</span>
                 <br>
                 <span class="text-muted">&nbsp;&nbsp;Remarks  : {{ $details->remarks }}</span>
                 <br>
                 <span class="text-muted">&nbsp;&nbsp;Orientation  : {{ ($details->orientation==0)?'Portrait':'Landscape' }}</span>
            </div>
        @endif
       
        <div class="col-sm-12">
            <div class="form-group" role="group" aria-label="Basic example">
              <a  class="btn btn-default" href="/admin/soa_template/index"><i class="fas fa-chevron-circle-left"></i> &nbsp;Template List</a>
               @if($credential->is_edit)
              <button type="button" class="btn bg-gradient-primary" onclick="show_save_modal(0)">Save As</button>
                  @if($op == 1)
                    <button type="button" class="btn bg-gradient-success" onclick="show_save_modal(1)">Save</button>
                  @endif
              @endif
            </div>
        </div>
        
    	<div class="col-sm-3">
    	    <div class="card card-success">
                <div class="card-header bg-gradient-success" style="padding: 5px 5px 5px 5px !important;">
                    <!-- <a class="btn btn-danger btn-xs float-right" onclick="order_fields()">&nbsp;Re-order</a> -->
                    <strong>Fields</strong>
                </div>
                <div class="card-body clearfix" >
                    <?php $order = array(); $summarize_fields = array()?>
                    <div class="form-group">
                        <input type="text" name="" class="form-control" placeholder="Search Field" onkeyup="search_fields(this)">
                    </div>
                    <div style="max-height: calc(100vh - 150px);overflow-y: auto;" id="div_menu_fields">
                    	<ul class='draggable-menu draggable-menu-fields' id="id_menu_fields">
                    		@foreach($fields as $count=>$field)
                                <?php if($field->is_summarize == 1)array_push($summarize_fields,$field->id_field);?>
                                @if($field->selected == 0 && $field->groupings == 0)
                        			<li data-id='{{$field->id_field}}' data-name='{{$field->alias}}' data-order={{$count}} class="static" title="{{$field->alias}}" data-custom="{{$field->alias}}" data-col_order="0" data-sum="0">
                                        <div style="color:black">
                                            <span class="spn_alias">{{$field->alias}}</span>
                                            <span class='float-right spn_buttons'></span>
                                            <section class="sec_others"></section>
                                        </div>
                        			</li>
                                @else
                                    <?php $order[$field->id_field] = $count; ?>
                                @endif
                    		@endforeach
                    	</ul>
                    </div>
                </div>
            </div>	
    	</div>
        <div class="col-sm-5">
            <div class="card card-primary">
                <div class="card-header bg-gradient-primary" style="padding: 5px 5px 5px 5px !important;"><strong>Selected Fields (Max of 10)</strong></div>
                <div class="card-body" style="max-height: calc(100vh - 150px);overflow-y: auto;">
                	<ul class='draggable-menu draggable-menu-sel_fields'>
                        @if(isset($selected))
                            @foreach($selected as $count=>$field)
                                <?php $or = $order["$field->id_field"];?>
                                <li data-id='{{$field->id_field}}' data-name='{{$field->alias}}' data-order={{$or}} class="static unsortable" title="{{$field->alias}}" data-custom="{{$field->custom_col}}" data-col_order="{{$field->order}}" data-sum="{{$field->is_sum}}">
                                    <div style="color:black">
                                        <span class="spn_alias">{{$field->custom_col}}</span>
                                        <span class='float-right spn_buttons'>
                                            <a  title='Options' class='fas fa-exchange-alt' style='margin-right:10px'
                                href='javascript:void(0)' onclick="options('{{$field->id_field}}',1)"></a>
                                <a  title='Delete' class='fa fa-trash'
                                href='javascript:void(0)' onclick="remove_field(this)"></a>
                                        </span>
                                        <section class="sec_others"></section>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card card-warning">
                <div class="card-header bg-gradient-warning" style="padding: 5px 5px 5px 5px !important;color:white"><strong>Group Fields  (Max of 2)</strong></div>
                <div class="card-body">
                	<ul class='draggable-menu draggable-menu-sel_group'>
                        @if(isset($groupings))
                            @foreach($groupings as $field)
                                <?php $or = $order["$field->id_field"];?>
                                <li data-id='{{$field->id_field}}' data-name='{{$field->alias}}' data-order={{ $or }} class="static" title="{{$field->alias}}" data-custom="{{$field->alias}}" data-col_order="{{$field->order}}" data-sum="0">
                                    <div style="color:black">
                                        <span class="spn_alias">{{$field->alias}}</span>
                                        <span class='float-right spn_buttons'>
                                            <a  title='Options' class='fas fa-exchange-alt' style='margin-right:10px'
                                            href='javascript:void(0)' onclick="options('{{$field->id_field}}',0)"></a>
                                            <a  title='Delete' class='fa fa-trash'
                                            href='javascript:void(0)' onclick="remove_field(this)"></a>
                                        </span>
                                        <section class="sec_others"></section>
                                        <!--  -->
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    <!--     <div class="col-sm-3">
            <div class="card card-primary">
                <div class="card-header"><strong>Group Fields</strong></div>
                <div class="card-body">
                	<ul class='draggable-menu draggable-menu-sel_fields'>

                    </ul>
                </div>
            </div>
        </div> -->
        </div>
    </div>

    <div class="modal modalfade" id="modal_settings" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <form id="frm_col_options">
              <div class="modal-body">
                    <div class="form-group">
                        <label for="txt_column" class="col-form-label">Custom column name</label>
                        <div class="input-group input-group-sm">
                          <input type="text" class="form-control" id="txt_column"  style="margin-top: -5px">
                          <span class="input-group-append">
                            <button type="button" class="btn btn-info btn-flat"  style="margin-top: -5px" id="btn_back_col"><i class="fas fa-long-arrow-alt-left" ></i></button>
                          </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sel_order" class="col-form-label">Order</label>
                        <select class="form-control" style="margin-top: -5px" id="sel_order">
                            <option value='0'>-</option>
                            <option value='1'>Ascending</option>
                            <option value='2'>Descending</option>
                        </select>
                    </div>
                    <div id="div_sum">
                        <div class="form-group" id="div_sum_in">
                            <label for="sel_summarize" class="col-form-label">Field Summarize</label>
                            <select class="form-control" style="margin-top: -5px" id="sel_summarize">
                                 <option value='0'>-</option>
                                 <option value='1'>SUM</option>
                            </select>
                        </div>   
                    </div>       
              </div>
              <div class="modal-footer">
                 <button type="submit" class="btn bg-gradient-primary">Save changes</button>
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
               
              </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal modalfade" id="modal_save" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="frm_post_rpt">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="txt_rpt_name">Name</label>
                                <input type="text" class="form-control" id="txt_rpt_name" required="">
                            </div>             
                        </div>
                       <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="txt_desc">Description</label>
                                <input type="text" class="form-control" id="txt_desc" required="">
                            </div>             
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="txt_remarks">Remarks</label>
                                <input type="text" class="form-control" id="txt_remarks">
                            </div>             
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="txt_remarks">Orientation</label>
                                <select class="form-control p-0" id="sel_orientation">
                                    <option value="0">Portrait</option>
                                    <option value="1">Landscape</option>
                                </select>
                            </div>             
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('head')
	    <style type="text/css">
/*            #modal_settings {
               position:  fixed;
               width: 600px;
               top: 40px;
               left: calc(50% - 300px);
               bottom: 40px;
               z-index: 100;
            }*/
            .modalfade {
                animation: fadeIn 2s;
                -moz-animation: fadeIn 2s; /* Firefox */
                -webkit-animation: fadeIn 2s; /* Safari and Chrome */
                -o-animation: fadeIn 2s; /* Opera */
            }
            @keyframes fadeIn {
                from {
                    opacity:0;
                }
                to {
                    opacity:1;
                }
            }
            @-moz-keyframes fadeIn { /* Firefox */
                from {
                    opacity:0;
                }
                to {
                    opacity:1;
                }
            }
            @-webkit-keyframes fadeIn { /* Safari and Chrome */
                from {
                    opacity:0;
                }
                to {
                    opacity:1;
                }
            }
            @-o-keyframes fadeIn { /* Opera */
                from {
                    opacity:0;
                }
                to {
                    opacity: 1;
                }
            }
            body.dragging, body.dragging * {
                cursor: move !important;
            }

            .dragged {
                position: absolute;
                opacity: 0.7;
                z-index: 2000;
            }

            .draggable-menu {
                padding: 0 0 0 0;
                margin: 0 0 0 0;
            }

            .draggable-menu li ul {
                margin-top: 6px;
            }

            .draggable-menu li div {
                padding: 5px;
                border: 1px solid #cccccc;
                background: white;
                cursor: move;
            }
            .draggable-menu li {
                list-style-type: none;
                margin-bottom: 4px;
                min-height: 35px;
            }

            .draggable-menu li.placeholder {
                position: relative;
                border: 1px dashed #b7042c;
                background: #ffffff;
                /** More li styles **/
            }

            .draggable-menu li.placeholder:before {
                position: absolute;
                /** Define arrowhead **/
            }
            .select2-selection__choice{
            	background: #4d94ff !important;
            }
            .select2-search__field{
                color :black !important;
            }
        </style>
@endpush
@push('scripts')
<!-- $("ul").find(`[data-slide='${current}']`) -->
<script src="{{URL::asset('plugins/sortable/jquery-sortable-min.js')}}"></script>
        <script type="text/javascript">
            var sum_fields = jQuery.parseJSON('<?php echo json_encode($summarize_fields); ?>');
            var $div_sum_in = $('#div_sum_in').detach();
            var fix_field = [506];
            $(document).ready(function(){
                $('.draggable-menu-sel_fields li').each(function(){
                    set_selected_details($(this),1);
                })
                $('.draggable-menu-sel_group li').each(function(){
                    set_selected_details($(this),0);
                })
                set_select_field_auto(fix_field);
            })
        	var selected_data = {};
            var current_data_id = 0;
            var current_panel = 0; // 0 Group , 1 - select field
            var opcode = 0 ; // 0 - save as // 1- save
            $(function () {
                var sortactive = $(".draggable-menu").sortable({
                    group: '.draggable-menu',
                    delay: 200,
                    items: "li:not(.unsortable)",
                    pullPlaceholder : true,
                      tolerance: 6,
                      distance: 10,
                    // accept: '.draggable-menu-sel_fields' ,
                    isValidTarget: function ($item, container) {
                    	var cc = container.target; // target
                        var dd = $item.parents('ul'); //current

                        var container_class= $item.parents('ul').attr('class');
                        var target_class = cc.attr('class');
                        // console.log({container_class,target_class});

                        if(cc.hasClass('draggable-menu-fields') && (dd.hasClass('draggable-menu-sel_fields') || dd.hasClass('draggable-menu-sel_group'))){
                            return false;
                        }
                        if(cc.hasClass('draggable-menu-fields')){
                            return true;
                        }
                        if(cc.hasClass('draggable-menu-sel_fields')){
                            var length = $('.draggable-menu-sel_fields li').length;
                            if(length  == 11){
                                return false;
                            }
                            console.log({length});
                        }
                        if(cc.hasClass('draggable-menu-sel_group')){
                            var length = $('.draggable-menu-sel_group li').length;
                            if(length  == 2){
                                return false;
                            }
                            console.log({length});
                        }

                        var depth = 1, // Start with a depth of one (the element itself)
                            maxDepth = 2,
                            children = $item.find('ul').first().find('li');

                        // Add the amount of parents to the depth
                        depth += container.el.parents('ul').length;

                        // Increment the depth for each time a child
                        while (children.length) {
                            depth++;
                            children = children.find('ul').first().find('li');
                        }
                        return depth <= maxDepth;
                    },
                    onCancel : function ($item, container, _super, event) {
                        console.log("QWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW");
                    },
					// onDragStart: function($item, container, _super) {
					// 	if($item.parents('ul').hasClass('draggable-menu-sel_fields')){
					// 		alert(123);
					// 	}
					// },
                    onDrop: function ($item, container, _super) {
                        if ($item.parents('ul').hasClass('draggable-menu-fields')) {
                            $item.find('.spn_buttons').html('')
                            $item.find('.sec_others').html('');
                            sortList();
                        }else if($item.parents('ul').hasClass('draggable-menu-sel_group')){
                            var data_id = $item.attr('data-id');
                            $item.find('span.spn_alias').text($item.attr('data-name'));
                            $item.find('.sec_others').html('');
                            $item.find('.spn_buttons').html(`
                            <a  title='Options' class='fas fa-exchange-alt' style='margin-right:10px'
                            href='javascript:void(0)' onclick="options('`+data_id+`',0)"></a>
                            <a  title='Delete' class='fa fa-trash'
                            href='javascript:void(0)' onclick="remove_field(this)"></a>`)
                            set_selected_details($item,0);
                        }else{
                            var data = $('.draggable-menu-sel_fields').sortable("serialize").get();
                            selected_data = data;
                            var jsonString = JSON.stringify(data, null, ' ');
                            var data_id = $item.attr('data-id');
                            console.log({data_id});

                            $item.find('span.spn_alias').text($item.attr('data-custom'));
                            set_selected_details($item,1);
                            $item.find('.spn_buttons').html(`
                            <a  title='Options' class='fas fa-exchange-alt' style='margin-right:10px'
                            href='javascript:void(0)' onclick="options('`+data_id+`',1)"></a>
                            <a  title='Delete' class='fa fa-trash'
                            href='javascript:void(0)' onclick="remove_field(this)"></a>`)


                            console.log({data});
                        }
                        _super($item, container);
                    },
                    onCancel : function ($item, position, _super, event) {
					  $item.css(position)
					}
                });
            });
            function set_selected_details(obj,type){ //1 Selected Field ; 0 group
                var order = ["-","Ascending","Descending"];
                var data_id = obj.attr('data-id');
                var current_col = (obj.attr('data-custom') == obj.attr('data-name')) ?'':"("+obj.attr('data-name')+")&nbsp&nbsp;&nbsp;&nbsp";
                var ordering_text =order[obj.attr('data-col_order')];
                var data_sum = obj.attr('data-sum');
                var summarize_text="";
                 var sum_select = ['-','SUM','AVERAGE'];
                if(jQuery.inArray(parseInt(data_id) , sum_fields ) >= 0){
                    summarize_text = (data_sum == 0)?"":"&nbsp&nbsp;&nbsp;&nbspSummarize::&nbsp;&nbsp"+sum_select[data_sum];
                }
                if(type == 0){
                    current_col = '';
                    summarize_text='';
                }

                console.log({ordering_text});
                obj.find('section.sec_others').html(`<em class="text-muted">
                <small>`+current_col+`Ordering:&nbsp;&nbsp`+ordering_text+``+summarize_text+`</small></em>`);
            }
            function options(data_id,type){
                current_panel = type;
                $('#txt_column, #btn_back_col').attr('disabled',!type)
                var parent_li =  $("li[data-id='"+data_id+"']");
                var custom_col_name =  (type == 1)?parent_li.attr('data-custom'):parent_li.attr('data-name');
                $('#txt_column').val(custom_col_name);


              
                $('#sel_order').val(parent_li.attr('data-col_order'))
                $('#btn_back_col').click(function(){
                    $('#txt_column').val(parent_li.attr('data-name'));
                })
                $('#div_sum_in').remove();
                if(jQuery.inArray(parseInt(data_id) , sum_fields ) >= 0 && current_panel == 1){
                    $('#div_sum').html($div_sum_in);
                }
                 $('#sel_summarize').val(parent_li.attr('data-sum'))
                current_data_id = data_id;
                $('#modal_settings').modal('show');
            }
            $('#frm_col_options').submit(function(e){
                e.preventDefault();
                var custom_name = $('#txt_column').val();
                var order = $('#sel_order').val();
                var parent_li =  $("li[data-id='"+current_data_id+"']");
                var name = parent_li.attr('data-name');
                var summarize = $('#sel_summarize').val();
                console.log({summarize});
                if(current_panel == 1){
                     parent_li.attr('data-custom',custom_name);
                  
                     parent_li.attr('data-sum',summarize)
                }
                parent_li.find('span.spn_alias').text(custom_name);
                parent_li.attr('data-col_order',order);
                set_selected_details(parent_li,current_panel);
                $('#modal_settings').modal('hide');
                console.log({name});
            })
            function remove_field(obj){
                var parent_li = $(obj).closest('li');
                var order = parent_li.attr('data-order');
                var data_id = parseInt(parent_li.attr('data-id'));
                console.log({fix_field,data_id})
                if(jQuery.inArray(data_id,fix_field) >=0){
                    return;
                }
                Swal.fire({
                  title: 'Do you want to remove this selected field ?',
                  icon: 'warning',
                  showDenyButton: false,
                  showCancelButton: true,
                  confirmButtonColor: '#ff3333',
                  confirmButtonText: `Remove`,
                }).then((result) => {
                    if (result.isConfirmed) {

                        parent_li.find('span.spn_buttons').html('');
                        parent_li.find('section.sec_others').html('');
                        parent_li.find('span.spn_alias').text(parent_li.attr('data-name'))
                        $(parent_li).remove(); 
                        insertAtIndex(parseInt(order),parent_li);
                        sortList();
                    }
 
                })

                // $(parent_li).appendToWithIndex($('li.draggable-menu-fields'),4)
                // $('.draggable-menu-fields').append(parent_li);
            }

            function insertAtIndex(i,parent_li) {
                console.log({i});
                if(i == 0) {
                    console.log({i});
                 // $("#controller").prepend("<div>okay things</div>");      
                  $('.draggable-menu-fields').prepend(parent_li);  
                   return;
                }

                $(".draggable-menu-fields > li:nth-child(" + (i) + ")").after(parent_li);
            }
            function order_fields(){
                sortList();
            }

            function sortList() {
              var list, i, switching, b, shouldSwitch;
              list = document.getElementById("id_menu_fields");
              switching = true;
              /* Make a loop that will continue until
              no switching has been done: */
              while (switching) {
                // start by saying: no switching is done:
                switching = false;
                b = list.getElementsByTagName("LI");
                // Loop through all list-items:
                for (i = 0; i < (b.length - 1); i++) {
                  // start by saying there should be no switching:
                  shouldSwitch = false;
                  /* check if the next item should
                  switch place with the current item: */
                 //alert(b[i].getAttribute("order"));
                  if (parseInt(b[i].getAttribute("data-order").toLowerCase()) > parseInt(b[i + 1].getAttribute("data-order").toLowerCase())) {
                    /* if next item is alphabetically
                    lower than current item, mark as a switch
                    and break the loop: */
                    shouldSwitch = true;
                    break;
                  }
                }
                if (shouldSwitch) {
                  /* If a switch has been marked, make the switch
                  and mark the switch as done: */
                  b[i].parentNode.insertBefore(b[i + 1], b[i]);
                  switching = true;       
                }
              }
            }
            function save_template(){
                var  select_fields = [], group_fields = [];
                $('.draggable-menu-sel_fields li').each(function(){
                    var  json = {};
                    json['id_field'] = $(this).attr('data-id');
                    json['custom_col'] = $(this).attr('data-custom');
                    json['order'] = $(this).attr('data-col_order');
                    json['summarize'] = $(this).attr('data-sum');
                    select_fields.push(json);
                });

                if(select_fields.length == 0){
                    Swal.fire({
                        position: 'center',
                        icon: 'warning',
                        title: 'Please select atleast 1 fields',
                        showConfirmButton: false,
                        cancelButtonText: `Close`,
                        showCancelButton: true,
                    });

                    return;
                }
                $('.draggable-menu-sel_group li').each(function(){
                    var  json = {};
                    json['id_field'] = $(this).attr('data-id');
                    json['order'] = $(this).attr('data-col_order');
                    group_fields.push(json);
                });

                Swal.fire({
                  title: 'Do you want to save this template ?',
                  icon: 'warning',
                  showDenyButton: false,
                  showCancelButton: true,
                  confirmButtonColor: '#ff3333',
                  confirmButtonText: `Save`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        post(select_fields,group_fields);
                    }
                })
            }
            function post(select_fields,group_fields){
                $.ajax({
                    type                  :              'POST',
                    url                   :              '/admin/post_template',
                    data                  :              {'select_fields' : select_fields,
                                                          'group_fields' : group_fields,
                                                          'rpt_name' : $('#txt_rpt_name').val(),
                                                          'rpt_desc' : $('#txt_desc').val(),
                                                          'rpt_remarks' : $('#txt_remarks').val(),
                                                          'id_report' : '<?php echo $details->id_report ?? 0 ?>',
                                                          'orientation' : $('#sel_orientation').val(),
                                                          'opcode' : opcode},
                    beforeSend            :              function(){
                                                            show_loader();
                    },
                    success               :              function(response){
                                                            setTimeout(
                                                            function() {
                                                                hide_loader();
                                                                if(response.message == "success"){
                                                                    console.log({response});
                                                                    $('#modal_save').modal('hide');
                                                                    Swal.fire({
                                                                        position: 'center',
                                                                        icon: 'success',
                                                                        title: 'Report Successfully Saved ',
                                                                        showConfirmButton: false,
                                                                        timer: 1500
                                                                    }).then(function() {
                                                                       window.location = '/admin/soa_template/edit?id_report='+response.id_report
                                                                        
                                                                    })                                                                    
                                                                }
                                                            }, 1500);
                    },error: function(xhr, status, error){
                            hide_loader();
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
                console.log({select_fields,group_fields});
            }
            function show_save_modal(op){
                var length = $('.draggable-menu-sel_fields li').length;
                opcode = op;

                if(length == 0){
                    Swal.fire({
                        position: 'center',
                        icon: 'warning',
                        title: 'Please select atleast 1 fields',
                        showConfirmButton: false,
                        cancelButtonText: `Close`,
                        showCancelButton: true,
                    });
                    return;
                }
                if(op == 0){
                    $('#txt_rpt_name').val('');
                    $('#txt_desc').val('');
                    $('#txt_remarks').val('');
                    $('#sel_orientation').val(0);
                }else{
                    $('#txt_rpt_name').val('<?php echo  $details->name ?? ""?>');
                    $('#txt_desc').val('<?php echo  $details->description ?? ""?>');
                    $('#txt_remarks').val('<?php echo  $details->remarks ?? ""?>');          
                    $('#sel_orientation').val('<?php echo $details->orientation ?>');             
                }
                $('#modal_save').modal('show');
            }
            $('#frm_post_rpt').submit(function(e){
                e.preventDefault();
                save_template();
            })
            function search_fields(obj){
                var val = $(obj).val();
                console.log({val});
                $('#id_menu_fields li').each(function(){

                })
                // $('#yourUL').animate({
                //      scrollTop: $('#yourUL li:nth-child(14)').position().top
                // }, 'slow');
            }
            function set_select_field_auto(data_id){
                for(var i=0;i<data_id.length;i++){
                   var $item =  $("li[data-id='"+data_id[i]+"']");
                    $item.find('span.spn_alias').text($item.attr('data-custom'));
                    set_selected_details($item,1);
                    $item.find('.spn_buttons').html(`
                    <a  title='Options' class='fas fa-exchange-alt' style='margin-right:10px'
                    href='javascript:void(0)' onclick="options('`+data_id[i]+`',1)"></a>
                    <a  title='Delete' class='fa fa-trash'
                    href='javascript:void(0)' onclick="remove_field(this)"></a>`)
                    var q = $item.detach();
                    $('.draggable-menu-sel_fields').append(q);
                }
            }
        </script>
@endpush