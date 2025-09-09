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
	.investment-card:hover{
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
	0 => 'Draft',
	1 => 'Processing',
	2 => 'Active',
	3 => 'Rejected/Cancelled',
	5 => 'Closed' ,
	6 => 'Renewal/Withdrawal Processing',
	"All" => "All",
];

function status_class($status){
	$class = "";
	switch($status){
		case 0:
		$class="info";
		break;
		case 1:
		$class ="info";
		break;
		case 2:
		$class = "success";
		break;
		case 5:
		$class="primary";
		break;
		case 8:
		$class="warning";
		break;
		default:
		$class="danger";
		break;
	}

	return $class;
}

$total = 0;
?>

<div class="container main_form">
	<div class="btn-tools">
		@if($credential->is_create ?? false)
		<button class="btn bg-gradient-success2 round_button" onclick="redirect_add()"><i class="fa fa-plus"></i>&nbsp;Create Investment</button>
		@endif
		@if(MySession::isAdmin())
		<button class="btn bg-gradient-primary2 round_button" onclick="show_filter()"><i class="fa fa-eye"></i>&nbsp;View Options</button>
		@endif
		<div class="btn-group d-md-none">
			<button type="button" class="btn bg-gradient-primary2  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Status <small style="font-size:0.75rem" id="spn_status"></small>
			</button>
			<div class="dropdown-menu" style="">
				
				
				@foreach($tabs as $val=>$tab)
				<?php
				$total += $investment_counts[$val][0]->count ?? 0;
				if($tab != "All"){
					$count = "(".($investment_counts[$val][0]->count ?? 0).")";
				}else{
					$count = "($total)";
				}
				?>
				<a class="dropdown-item {{$current_tab === $val ?'active' : ''}} nav-status dp-status" status-id="{{$val}}" href="javascript:void(0)"><span class="spn_status_desc">{{$tab}}</span> {{$count}}</a>
				
				@endforeach
			</div>
		</div>
	</div>
	<div class="nav-pills-container mt-2 d-none d-md-flex" style="width: 100%; overflow-x: auto;">
		<ul class="nav nav-pills flex-nowrap px-0" id="ul_tabs">
			<?php $total=0; ?>
			@foreach($tabs as $val=>$tab)
			<?php

			$total += $investment_counts[$val][0]->count ?? 0;
			if($tab != "All"){
				$count = "(".($investment_counts[$val][0]->count ?? 0).")";
			}else{
				$count = "($total)";
			}
			?>

			<li class="nav-item mr-3"><a class="nav-link nav_sel nav-status {{$current_tab === $val ?'active' : ''}}" status-id="{{$val}}" style="cursor: pointer;" data-toggle="tab">{{$tab}} {{$count}}</a></li>
			@endforeach
		</ul>
	</div>
	<div class="row mt-2">
		<div class="col-md-12">
			<input type="text" id="search_investment" class="form-control form-control-border" placeholder="Search from Investments .....">
			@if(count($investments) > 0)
			<p class="my-0 mt-3">{{number_format(count($investments),0)}} Records</p>
			<div style="max-height:calc(100vh - 100px);overflow-y:auto;" id="investment-card-body">
				@foreach($investments as $inv)
				<div class="card c-border investment-card" data-code="{{$inv->id_investment}}">
					<div class="card-body p-3">
						<div class="row">
							<div class="col-md-6 col-6">
								<a class="badge bg-gradient-dark text-sm" href="/investment/view/{{$inv->id_investment}}" target="_blank">Investment ID# {{$inv->id_investment}}</a>
							</div>
							<div class="col-md-6 col-6">
								<span class="float-right badge badge-{{status_class($inv->status_code)}} text-xs">{{$inv->status}}</span>
								
							</div>
						</div>
						<div class="row mt-2">
							<div class="col-md-6 col-12">
								@if(MySession::isAdmin())
								<p class="font-weight-bold my-0 lbl_color">{{$inv->investor}}</p>
								@endif
								<p class="my-0 lbl_color">{{$inv->product_name}} | {{$inv->terms}}</p>
								<p class="my-0 lbl_color"><b>Amount: </b>{{number_format($inv->amount,2)}}</p>
							</div>

							@if(in_array($inv->status_code,[2,5,8]))
							<div class="col-md-6 col-12">
								<p class="my-0 lbl_color"><b>Investment Date: </b>{{$inv->investment_date}}</p>
								<p class="my-0 lbl_color"><b>Maturity Date: </b>{{$inv->maturity_date}}</p>
								@if($inv->status_code == 2 && $inv->is_withdrawable == 1 && $inv->withdrawable > 0)
								<p class="my-0 lbl_color"><b>Withdrawables: </b><span class="text-success">{{number_format($inv->withdrawable ?? 0,2)}}</span></p>
								@endif
							</div>
							@endif
						</div>
						<div class="row mt-1">
							<div class="col-md-12 col-12">
								<p class="my-0 mb-0 lbl_color float-right text-muted"><i>Date Created: {{$inv->date_created}}</i></p>
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

@if(MySession::isAdmin())
@include('investment.filter_option_modal')
@endif
@endsection

@push('scripts')
<script type="text/javascript">
	$(document).on('click','.nav-status',function(){
		var $status = $(this).attr('status-id');
		// $param = {'status' : $status};

		// window.location = '/investment?'+$.param($param);
	     
	      var params = new URLSearchParams(window.location.search);

	      var newParameter = "status";
	      var newValue = $status;
	      params.set(newParameter, newValue);
	      var newURL = window.location.pathname + '?' + params.toString();

	      window.location.href = newURL;
	})
	function redirect_add(){
		window.location = '/investment/create'+'?href='+'{{urlencode(url()->full())}}';
	}
	$(document).ready(function(){
		$('#spn_status').text(`(${$('.dp-status.active').find('.spn_status_desc').text()})`);
		
		$("#search_investment").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$("#investment-card-body div.card").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
	});
	$(function() {
		$.contextMenu({
			selector: '.investment-card',
			callback: function(key, options) {
				var m = "clicked: " + key;
				var investment_id= $(this).attr('data-code');
				if(key == "view"){
					window.location ='/investment/view/'+investment_id +'?href='+'{{urlencode(url()->full())}}';
				}

			},
			items: {
				"view": {name: "View Investment", icon: "fas fa-eye"},
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