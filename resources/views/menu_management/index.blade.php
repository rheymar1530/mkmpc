@extends('adminLTE.admin_template')
@section('content')

<div class="row">
	<div class="col-sm-5">
	    <div class="card card-success">
            <div class="card-header">
                <strong>Menu Order (Active)</strong> <span id='menu-saved-info' style="display:none" class='pull-right text-success'><i class='fa fa-check'></i> Menu Saved !</span>                   
            </div>
                <div class="card-body clearfix">
                    <ul class='draggable-menu draggable-menu-active'>
                    	@foreach($menu_active as $menu)
                    		@php
                                $privileges = DB::table('cms_menus_privileges')
                                ->join('cms_privileges','cms_privileges.id','=','cms_menus_privileges.id_cms_privileges')
                                ->where('id_cms_menus',$menu->id)->pluck('cms_privileges.name')->toArray();
                            @endphp
                            <!-- href='javascript:void(0)' -->
                             <li data-id='{{$menu->id}}' data-name='{{$menu->name}}'>
                             	<div class='{{$menu->is_dashboard?"is-dashboard":""}}' title="{{$menu->is_dashboard?'This is setted as Dashboard':''}}" style="color:black">
                             		<i class='{{($menu->is_dashboard)?"icon-is-dashboard fa fa-dashboard":$menu->icon}}'></i>{{$menu->name}}
									<span class='float-right'>
										<a class='fas fa-pencil-alt' title='Edit' href='javascript:void(0)' onclick="window.location = '/admin/menu_management?id_menu='+{{$menu->id}}"></a>&nbsp;&nbsp;
                                        <a  title='Delete' class='fa fa-trash'
                                                href='javascript:void(0)' onclick="delete_menu({{$menu->id}})"></a>
                                    </span>
                                    <br/><em class="text-muted">
                                        <small><i class="fa fa-users"></i> &nbsp; {{implode(', ',$privileges)}}</small>
                                    </em>             
                             	</div>
                             	<ul>
                             		@if(isset($menu->children))
                             			@foreach($menu->children as $child)
                             				@php
                                                $privileges = DB::table('cms_menus_privileges')
                                                ->join('cms_privileges','cms_privileges.id','=','cms_menus_privileges.id_cms_privileges')
                                                ->where('id_cms_menus',$child->id)->pluck('cms_privileges.name')->toArray();
                                            @endphp
                                            <li data-id='{{$child->id}}' data-name='{{$child->name}}'>
												<div class='{{$child->is_dashboard?"is-dashboard":""}}' title="{{$child->is_dashboard?'This is setted as Dashboard':''}}" style="color:black">
													<i class='{{($child->is_dashboard)?"icon-is-dashboard fa fa-dashboard":$child->icon}}'></i>
													{{$child->name}}
													<span class='float-right'>
														<a class='fas fa-pencil-alt' title='Edit' href='javascript:void(0)' onclick="window.location = '/admin/menu_management?id_menu='+{{$child->id}}"></a>&nbsp;&nbsp;
														<a title="Delete" class='fa fa-trash'  href='javascript:void(0)' onclick="delete_menu({{$child->id}})"></a>                 
													</span>
													<br/><em class="text-muted">
                                                        <small><i class="fa fa-users"></i> &nbsp; {{implode(', ',$privileges)}}</small>
                                                    </em>     
												</div>
                                            </li>
                             			@endforeach
                             		@endif
                             	</ul>
                             </li>
                        @endforeach
                    </ul>
                </div>
        </div>	

        <div class="card card-danger">
            <div class="card-header">
                <strong>Menu Order (Inactive)</strong> <span id='menu-saved-info' style="display:none" class='pull-right text-success'><i
                            class='fa fa-check'></i> Menu Saved !</span>
            </div>
            <div class="card-body clearfix">
                <ul class='draggable-menu draggable-menu-inactive'>
                    @foreach($menu_inactive as $menu)
                         <li data-id='{{$menu->id}}' data-name='{{$menu->name}}'>
                            <div style="color:black">
                                <i class='{{$menu->icon}}'></i> {{$menu->name}} 
                                    <span class='float-right'>
                                        <a class='fas fa-pencil-alt' title='Edit' href='javascript:void(0)' onclick="window.location = '/admin/menu_management?id_menu='+{{$menu->id}}"></a>&nbsp;&nbsp;
                                        <a title="Delete" class='fa fa-trash'  href='javascript:void(0)' onclick="delete_menu({{$menu->id}})"></a>                 
                                    </span>
                            </div>
                           <ul>
                                @if(isset($menu->children))
                                    @foreach($menu->children as $child)
                                        <li data-id='{{$child->id}}' data-name='{{$child->name}}'>
                                             <div style="color:black">
                                                <i class='{{$child->icon}}'></i> {{$child->name}}
                                                <span class='float-right'><a class='fas fa-pencil-alt' title='Edit'></a>&nbsp;&nbsp;
                                                <a title="Delete" class='fa fa-trash' onclick='' href='javascript:void(0)'></a></span>                 
                                             </div>

                                        </li>
                                    @endforeach
                                @endif
                           </ul>
                         </li>
                    @endforeach
                </ul>
            </div>
        </div>
	</div>
	    <div class="col-sm-7">
            <div class="card card-primary">
                <div class="card-header">
                    {{ ($opcode == 0)? 'Add Menu' : 'Edit Menu' }}
                    
                    
                </div>
                <div class="card-body">
                	@include('menu_management.menu_form')
                </div>
            </div>
        </div>
</div>

@endsection

@push('head')
	    <style type="text/css">
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
                background: #eeeeee;
                cursor: move;
            }

            .draggable-menu li .is-dashboard {
                background: #fff6e0;
            }

            .draggable-menu li .icon-is-dashboard {
                color: #ffb600;
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

    <script type="text/javascript">
        $(function () {
            function format(icon) {
                var originalOption = icon.element;
                var label = $(originalOption).text();
                var val = $(originalOption).val();
                if (!val) return label;
                var $resp = $('<span><i style="margin-top:5px" class="pull-right ' + $(originalOption).val() + '"></i> ' + $(originalOption).data('label') + '</span>');
                return $resp;
            }

            $('#list-icon').select2({
                width: "100%",
                templateResult: format,
                templateSelection: format
            });
        })
    </script>
@endpush
@push('scripts')
<script src="{{URL::asset('plugins/sortable/jquery-sortable-min.js')}}"></script>
<script type="text/javascript">
	function delete_menu(id){
    	Swal.fire({
          title: 'Do you want to remove this menu ?',
          icon: 'warning',
          showDenyButton: false,
          showCancelButton: true,
          confirmButtonColor: '#ff3333',
          confirmButtonText: `Delete`,
        }).then((result) => {
            if (result.isConfirmed) {
	            $.ajax({
        			type          :        'POST',
        			url           :        '/admin/menu/delete',
        			data          :        {'id_menu' : id},
        			success       :        function(response){
        									console.log({response});
        									if(response.message == 'success'){
        										window.location = '/admin/menu_management';
        									}
        			}
        		})
            } 
        })
	}
    $(function () {
        var id_cms_privileges = '{{$id_cms_privileges}}';
        var sortactive = $(".draggable-menu").sortable({
            group: '.draggable-menu',
            delay: 200,
            nested : true,
            isValidTarget: function ($item, container) {
                var depth = 1, // Start with a depth of one (the element itself)
                    maxDepth = 3,
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
            onDrop: function ($item, container, _super) {
                if ($item.parents('ul').hasClass('draggable-menu-active')) {
                    var isActive = 1;
                    var data = $('.draggable-menu-active').sortable("serialize").get();
                    var jsonString = JSON.stringify(data, null, ' ');
                } else {
                    var isActive = 0;
                    var data = $('.draggable-menu-inactive').sortable("serialize").get();
                    var jsonString = JSON.stringify(data, null, ' ');
                    $('#inactive_text').remove();
                }
                
                $.post('/admin/menu_management/arrange', {menus: jsonString, isActive: isActive}, function (resp) {
                    $('#menu-saved-info').fadeIn('fast').delay(1500).fadeOut('fast');
                });
                console.log({jsonString,isActive});

                _super($item, container);
            }
        });
    });
</script>
@endpush