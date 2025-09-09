@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.chart_pie tr >td{
		padding: 4px !important;
	}
	.chart_container{
		zoom : 1.11 !important;
	}
	.vertical-center,.vertical-center th,.vertical-center td{
		vertical-align: middle;
		
	}
	.dashboard-table td,.dashboard-table th{
		padding-top: 10px;
		padding-bottom: 10px;
	}
	.font-up{
		color: #00cc00 !important;
	}
	.font-down{
		color: #ff1a1a !important;
	}
	.c_font{
		font-size: 1rem !important;
	}
	#div_main_dashboard table{
		border: 1px #dee2e6 solid;
	} 
	.border-right{
		border-right: 1px #dee2e6 solid;
	}
</style>
<?php
$month_list = [
	1=>"January",
	2=>"February",
	3=>"March",
	4=>"April",
	5=>"May",
	6=>"June",
	7=>"July",
	8=>"August",
	9=>"September",
	10=>"October",
	11=>"November",
	12=>"December"
];
function check_negative($val){
	$col_val = number_format(abs($val),2);
	// if($val == 0){
	// 	return '0';
	// }
	if($val < 0){
		$col_val = "($col_val)";
	}
	return $col_val;
}

function perc_difference($previous,$current){
	$diff = $current - $previous;

	if($previous != 0){
		$percentage = ROUND(($diff/$previous)*100,2);
	}else{
		$percentage= 0;
	}
	$output['percentage'] = $percentage;
	$output['change'] = ($previous > $current)?'down':'up';
	$output['color'] = ($previous > $current)?'danger':'success';

	return $output;
}

function expand_button($div_id){
	return '		<div class="row mb-1">
						<div class="col-lg-12 order-last float-right  pr-n1">
							<button class="btn btn-default btn-sm float-right mt-n3 btn-full-screen round_button bg-gradient-success2"  title="Expand" onclick="goFullScreen(this,`'.$div_id.'`)"><i class="fas fa-expand"></i></button>
						</div>
					</div>';
}
?>
<div class="container-fluid" style="font-family: Roboto,Helvetica,Arial,sans-serif !important" id="div_main_dashboard">
	<form>
		<h3 class="lbl_color mt-n2 mb-3">{{$DASHBOARD_TITLE}}</h3>
		<div class="form-row d-flex align-items-end">
			<div class="form-group col-md-2">
				<label class="lbl_color mb-0">Period</label>
				<select class="form-control form-control-border p-0" name="month">
					@foreach($month_list as $m=>$m_description)
					<option value="{{$m}}" <?php echo ($m == $selected_month)?'selected':''; ?> >{{$m_description}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-md-1">
				<select class="form-control form-control-border p-0" name="year">
					@for($i=2022;$i<=2050;$i++)
					<option value="{{$i}}" <?php echo ($i == $selected_year)?'selected':''; ?>>{{$i}}</option>
					@endfor
				</select>
			</div>
			<div class="form-group col-md-1">
				<button  type="submit" class="btn btn-sm bg-gradient-success2 w-100 round_button" style="height: 31px !important"><i class="fa fa-search"></i>&nbsp;Filter</button>
			</div>

		</div>
	</form>
	@if($type == "revenue_expenses")
	@include('admin_dashboard.dashboard_stats')
	@include('admin_dashboard.revenue_expenses')	
	@else
	@include('admin_dashboard.dashboard_stats_financial')
	@include('admin_dashboard.financial')
	@endif
</div>
@include('admin_dashboard.chart_config')

@include('admin_dashboard.test_modal')
@endsection
@push('scripts')


@endpush