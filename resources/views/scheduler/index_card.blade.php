@extends('adminLTE.admin_template')
@section('content')

<style type="text/css">
	.nav-pills-container {
		width: 100%;
		overflow-x: scroll;
		padding: 10px 0;
	}

	.nav-pills {
		padding: 0 10px;
		white-space: nowrap;
	}
	.scheduler-card:hover{
		background: #f2f2f2 !important;
	}
	#ul_tabs .nav-item{
		border: 1px solid #e6e6e6;
		border-radius: 0.25rem;
		background: white;
	}
	.btn-tools button{
		margin-right: 0.75rem;
	}
	@media only screen and (max-width: 600px) {
		.btn-tools button{

			margin-bottom: 10px;
		}
	}
</style>
<?php

$tabs = [
	
	1 => 'Active',
	0 => 'Inactive',
	2 => "All",
];

$total = 0;
?>

<div class="container main_form">

	<div class="btn-tools">
		@if($credential->is_create ?? false)
		<button class="btn bg-gradient-success2 round_button" onclick="redirect_add()"><i class="fa fa-plus"></i>&nbsp;Create Journal Entry Scheduler</button>
		@endif
		
		<div class="btn-group">
			<button type="button" class="btn bg-gradient-primary2  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Status <small style="font-size:0.75rem" id="spn_status"></small>
			</button>
			<div class="dropdown-menu" style="">
				@foreach($tabs as $val=>$tab)

				<?php
				$total += $counts[$val][0]->count ?? 0;
				if($tab != "All"){
					$count = "(".($counts[$val][0]->count ?? 0).")";
				}else{
					$count = "($total)";
				}
				?>

				<a class="dropdown-item {{$current_tab == $val ?'active' : ''}} nav-status dp-status" status-id="{{$val}}" href="javascript:void(0)"><span class="spn_status_desc">{{$tab}}</span> {{$count}}</a>
				@endforeach

			</div>
		</div>
	</div>

	<div class="row mt-2">
		<div class="col-md-12">
			<input type="text" id="search_scheduler" class="form-control form-control-border" placeholder="Search from Schedulers .....">
			@if(count($schedules) > 0)
			<p class="my-0 mt-3">{{number_format(count($schedules),0)}} Records</p>
			<div style="max-height:calc(100vh - 100px);overflow-y:auto;" id="scheduler-card-body">
				@foreach($schedules as $schd)
				<div class="card c-border scheduler-card tbl-crd" data-code="{{$schd->id_scheduler}}">
					<div class="card-body p-3">
						<div class="row">
							<div class="col-md-6 col-6">
								<a class="badge bg-gradient-dark text-sm" href="/scheduler/view/{{$schd->id_scheduler}}" target="_blank">Scheduler ID# {{$schd->id_scheduler}}</a>
							</div>
							<div class="col-md-6 col-6">
								<span class="float-right badge badge-{{($schd->status=='Active')?'success':'danger'}} text-xs">{{$schd->status}}</span>
								
							</div>
						</div>
						<div class="row mt-2">
							<div class="col-md-12 col-12">
								<span class="badge text-md bg-gradient-primary">{{$schd->type}}</span>
								<!-- <p class="font-weight-bold my-0 lbl_color"></p> -->
							</div>
						</div>
						<div class="row mt-2">
							<div class="col-md-6 col-12">
								<p class="my-0 lbl_color"><b>Schedule: </b>{{$schd->schedule_type}}</p>
								<p class="my-0 lbl_color"><b>Date Start: </b>{{$schd->date}}</p>
								
								<p class="my-0 lbl_color"><b>Date End: </b>{{$schd->stop_date}}</p>
							</div>
							<div class="col-md-6 col-12">
		                	<?php
		                		$link_route = '#';

		                		switch($schd->books){
		                			case '1':
		                				$link_route = "/journal_voucher/view/".$schd->reference_no;
		                				break;
		                			case '2':
		                				if($schd->cdv_type == 2){
		                					$ct = 'expenses';
		                				}elseif($schd->cdv_type == 3){
		                					$ct= 'asset_purchase';
		                				}elseif($schd->cdv_type == 4){
		                					$ct = 'others';
		                				}
		                				$link_route = "/cdv/$ct/view/".$schd->reference_no;
		                				break;
		                		}
		                	?>
								<p class="my-0 lbl_color"><b>Reference No: </b>
                				@if($schd->reference_no != '')
                				<a href="{{$link_route}}" target="_blank">
                				{{$schd->book_type}}# {{$schd->reference_no}}
                				</a>
                				@endif
								</p>
								<p class="my-0 lbl_color"><b>Last Run: </b><span class="text-muted">{{$schd->last_run}}</span></p>
								<p class="my-0 lbl_color"><b>Next Run: </b>{{$schd->next_run}}</p>
								
					
							</div>

						</div>
						<div class="row mt-1">
							<div class="col-md-12 col-12">
								<p class="my-0 mb-0 lbl_color float-right text-muted"><i>Date Created: {{$schd->date_created}}</i></p>
							</div>
						</div>
					</div>
				</div>
				@endforeach
		
			</div>
			@else
			<div class="card mt-2">
				<div class="card-body">
					<div class="text-center">
						<h5 class="lbl_color">No Data</h5>
					</div>
				</div>
			</div>
			@endif
		</div>
	</div>
</div>


@endsection

@push('scripts')
<script type="text/javascript">
	$(document).on('click','.nav-status',function(){
		var $status = $(this).attr('status-id');
		// $param = {'status' : $status};

		// window.location = '/scheduler?'+$.param($param);
	     
	      var params = new URLSearchParams(window.location.search);

	      var newParameter = "status";
	      var newValue = $status;
	      params.set(newParameter, newValue);
	      var newURL = window.location.pathname + '?' + params.toString();

	      window.location.href = newURL;
	})
	function redirect_add(){
		window.location = '/scheduler/create'+'?href='+'{{urlencode(url()->full())}}';
	}
	$(document).ready(function(){
		$('#spn_status').text(`(${$('.dp-status.active').find('.spn_status_desc').text()})`);
		
		$("#search_scheduler").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$("#scheduler-card-body div.card").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
	});
	$(function() {
		$.contextMenu({
			selector: '.scheduler-card',
			callback: function(key, options) {
				var m = "clicked: " + key;
				var scheduler_id= $(this).attr('data-code');
				if(key == "view"){
					window.location ='/scheduler/view/'+scheduler_id +'?href='+'{{urlencode(url()->full())}}';
				}

			},
			items: {
				"view": {name: "View Scheduler", icon: "fas fa-eye"},
				"sep1": "---------",
				"quit": {name: "Close", icon: "fas fa-times" }
			}
		});   
	});
	function show_filter(){
		$('#view_options').modal('show');
	}
</script>

@endpush