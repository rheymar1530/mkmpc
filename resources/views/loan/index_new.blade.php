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
	.loan_card:hover{
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

// tabs
$tabs = [
	0 => 'Submitted',
	1 => 'Processing',
	2 => 'Approved',
	3 => 'Active',
	6 => 'Closed',
	4 => 'Cancelled/Disapproved',
	"All" => "All",
];

$status_badge = [
	0=>"info",
	1=>"primary",
	2=>"success",
	3=>"success",
	4=>"warning",
	5=>"danger",
	6=>"primary",
];

$total = 0;
?>

<div class="container main_form">
	<div class="btn-tools">
		@if($credential->is_create)
		<button class="btn bg-gradient-success2 round_button" onclick="redirect_add()"><i class="fa fa-plus"></i>&nbsp;Create Loan Application</button>
		@endif
		@if(MySession::isAdmin())
		<button class="btn bg-gradient-primary2 round_button" onclick="show_filter()"><i class="fa fa-eye"></i>&nbsp;View Options</button>
		


		<div class="btn-group">
			<button type="button" class="btn bg-gradient-danger  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Generate Active Loan Summary
			</button>
			@if($credential->is_read)
			<div class="dropdown-menu" style="">
				<a class="dropdown-item" href="javascript:void(0)" onclick="show_active_loan_summary(1)">Per Borrower</a>
				<a class="dropdown-item" href="javascript:void(0)" onclick="show_active_loan_summary(2)">Per Loan Service</a>
			</div>

			@endif
		</div>
		@endif
		<div class="btn-group d-md-none">
			<button type="button" class="btn bg-gradient-primary2  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Status <small style="font-size:0.75rem" id="spn_status"></small>
			</button>
			<div class="dropdown-menu" style="">
				
				
				@foreach($tabs as $val=>$tab)
				<?php
				$total += $loan_counts[$val][0]->count ?? 0;
				if($tab != "All"){
					$count = "(".($loan_counts[$val][0]->count ?? 0).")";
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

			$total += $loan_counts[$val][0]->count ?? 0;
			if($tab != "All"){
				$count = "(".($loan_counts[$val][0]->count ?? 0).")";
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
			<input type="text" id="search_loan" class="form-control form-control-border" placeholder="Search from loans .....">
			@if(count($loan_lists) > 0)
			<p class="my-0 mt-3">{{number_format(count($loan_lists),0)}} Records</p>
			<div style="max-height:calc(100vh - 100px);overflow-y:auto;" id="loan-card-body">

				
				@foreach($loan_lists as $ls)
				<div class="card c-border loan_card" data-code="{{$ls->loan_token}}">
					<div class="card-body p-3">
						<div class="row">
							<div class="col-md-6 col-6">
								<a class="badge bg-gradient-dark text-sm" href="/loan/application/view/{{$ls->loan_token}}" target="_blank">Loan ID# {{$ls->id_loan}}</a>
							</div>
							<div class="col-md-6 col-6">
								<span class="float-right badge badge-{{$status_badge[$ls->status_code]}} text-xs">{{$ls->loan_status}}</span>
							</div>
						</div>
						<div class="row mt-2">
							<div class="col-md-6 col-12">
								@if(MySession::isAdmin())
								<p class="font-weight-bold my-0 lbl_color">{{$ls->member_name}}</p>
								@endif
								<p class="my-0 lbl_color">{{$ls->loan_service_name}} ({{$ls->interest_rate}}%)</p>
								<p class="my-0 lbl_color"><b>Amount: </b>{{number_format($ls->principal_amount,2)}}</p>
							</div>

							@if(in_array($ls->status_code,[3,6]))
							<div class="col-md-6 col-12">
								<p class="my-0 lbl_color"><b>Paid Principal: </b>{{number_format($ls->paid_principal,2)}}</p>
								<p class="my-0 lbl_color"><b>Balance: </b><span class="{{($ls->principal_balance==$ls->principal_amount)?'text-danger':''}}">{{number_format($ls->principal_balance,2)}} </span></p>
								@if(isset($ls->date_released))
								<p class="my-0 lbl_color"><i>Date Released: {{$ls->date_released}}</i></p>
								@endif
							</div>
							@endif
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
@include('loan.filter_option_modal')
@endif
@include('global.print_modal')
@endsection

@push('scripts')
<script type="text/javascript">
	$(document).on('click','.nav-status',function(){
		// var $status = $(this).attr('status-id');
		// $param = {'status' : $status};

		// window.location = '/loan?'+$.param($param);
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

	$(document).ready(function(){
		$('#spn_status').text(`(${$('.dp-status.active').find('.spn_status_desc').text()})`);
		
		$("#search_loan").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$("#loan-card-body div.card").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
	});
	$(function() {
		$.contextMenu({
			selector: '.loan_card',
			callback: function(key, options) {
				var m = "clicked: " + key;
				var loan_token = $(this).attr('data-code');
				var viewing_route = '<?php echo $viewing_route;?>';

				console.log({loan_token});
				if(key == "view"){
					window.location = viewing_route+loan_token +'?href='+'{{urlencode(url()->full())}}';
				}

			},
			items: {
				"view": {name: "View Loan", icon: "fas fa-eye"},
				"sep1": "---------",
				"quit": {name: "Close", icon: "fas fa-times" }
			}
		});   
	});
	function redirect_add(){
		window.location = '/loan/application/create'+'?href='+'{{urlencode(url()->full())}}';
	}
	function show_filter(){
		$('#view_options').modal('show');
	}
</script>

@if($credential->is_read)
<script type="text/javascript">
	function show_active_loan_summary(type){
		$.ajax({
			type           :         'GET',
			url              :         '/loan/validate/active',
			data             :       {},
			beforeSend       :       function(){
				show_loader();
			},
			success         :        function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showConfirmButton : false,
						timer  : 2500
					})
				}else if(response.RESPONSE_CODE == "SUCCESS"){
					print_page('/loan/active/'+type)
				}
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
@endif
@endpush