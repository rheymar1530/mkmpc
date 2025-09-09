@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.tbl_accounts tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.tbl_accounts tr>td{
		padding: 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 13px;
	}
	.class_amount{
		text-align: right;
		padding-right: 10px !important;
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
		margin-top: 5px;
	}
	.col_tot{
		font-weight: bold;
	}
	.row_total{
		/*background: #e6e6e6;*/
		/*color: black;*/
	}
	.acc_head{
		/*background: #b3ffff;*/
	}
	.fas {
		transition: .3s transform ease-in-out;
	}
	.collapsed .fas {
		transform: rotate(90deg);
		padding-right: 3px;
	}
	.row_total{
		/*background: #e6e6e6;*/
		/*color: black;*/
	}
	.table_header_dblue_cust{
		background: #203764 !important;
		color: white;
		text-align: center;
	}
	.tbl_head{
		border-top:  2px solid;
		border-bottom: 2px solid;
	}
	tr.head_tbl th {
		background-color: #fff;
		border-bottom: 0;
		box-shadow: inset 0 1px 0 #dee2e6, inset 0 -1px 0 #dee2e6; 
		position: sticky;
		top: 0;
		z-index: 10;
	}
</style>
<?php
$report_type = [
	1=>'Balance Sheet',
	2=>'Income Statement',
	3=>'Cash Flow',
	4=>'Changes in Equity'
];

$type_list = [
	1=>"Per Month",
	2=>"Per Year",
];

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

$start_month = $start_month ?? $current_month;
$end_month = $end_month ?? $current_month;
$year = $year ?? $current_year;

$start_year = $start_year ?? $current_year;
$end_year = $end_year ?? $current_year;

$comparative_type_list = [1=>'Last Month and Year',2=>'Select Date/Period'];
?>
<div class="row section_body">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<h4 class="head_lbl text-center mb-4">{{$mod_title}}</h4>
						
						<div class="form-group row mt-3">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Report Type&nbsp;</label>
							<div class="col-md-3">
								<select class="form-control p-0" id="sel_rpt">
									@foreach($report_type as $val=>$rt)
									<option value="{{$val}}" <?php echo ($val==$financial_report_type)?'selected':''; ?> >{{$rt}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Filter Type&nbsp;</label>
							<div class="col-md-3">
								<select class="form-control p-0" id="sel_type">
									@foreach($type_list as $val=>$tl)
									<option value="{{$val}}" <?php echo($type == $val)?"selected":""; ?> >{{$tl}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group row" id="div_monthly">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Period&nbsp;</label>
							<div class="col-md-2">
								<select class="form-control p-0" id="sel_month_end">
									@foreach($month_list as $val=>$md)
									<option value="{{$val}}" <?php echo ($val==$end_month)?"selected":""; ?>>{{$md}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-1">
								<select class="form-control" id="sel_year">
									@for($i=2022;$i<=2050;$i++)
									<option value="{{$i}}" <?php echo ($i==$year)?"selected":""; ?>>{{$i}}</option>
									@endfor
								</select>
							</div>
							<button class="btn bg-gradient-success2 btn-sm" onclick="submit_filter(1)">Generate</button>
							<div class="btn-group ml-1">
								<button type="button" class="btn bg-gradient-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Export
								</button>
								<div class="dropdown-menu">
			
									<a class="dropdown-item" onclick="submit_filter(2,1)">PDF</a>
									<a class="dropdown-item" onclick="submit_filter(2,2)">Excel</a>
								</div>
							</div>

							<!-- <button class="btn bg-gradient-danger2 btn-sm" onclick="submit_filter(2)" style="margin-left:10px">Export to PDF</button> -->
						</div>
						
						<div class="form-group row" id="div_yearly">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Year&nbsp;</label>
							<div class="col-md-2">
								<select class="form-control" id="sel_year_end">
									@for($i=2022;$i<=2050;$i++)
									<option value="{{$i}}"  <?php echo ($i==$end_year)?"selected":""; ?>>{{$i}}</option>
									@endfor
								</select>
							</div>
						
							<button class="btn bg-gradient-success2 btn-sm" onclick="submit_filter(1)">Generate</button>
							<div class="btn-group ml-1">
								<button type="button" class="btn bg-gradient-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Export
								</button>
								<div class="dropdown-menu">
			
									<a class="dropdown-item" onclick="submit_filter(2,1)">PDF</a>
									<a class="dropdown-item" onclick="submit_filter(2,2)">Excel</a>
								</div>
							</div>
						</div>
						<div id="div_fil"></div>
						@if($act_fs)
						@if(count($financial_statement['data']) > 0)
						@include('financial_statement.table')
						@else
						@if($show_no_record)
						<h4 style="margin-top:40px">No Record Found</h4>
						@endif
						@endif
						@else
						@include($view)
						@endif
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	var div_monthly =$('#div_monthly').detach();
	var div_yearly =$('#div_yearly').detach();

	$(document).ready(function(){
		initialize_filters();
	})
	$(document).on('change','#sel_type,#sel_rpt',function(){
		initialize_filters();
	})

	function initialize_filters(){
		var rpt_type = $('#sel_rpt').val();
		$("#sel_type option[value='1']").attr('disabled', false); 
		if(rpt_type > 2){
			$("#sel_type option[value='1']").attr('disabled', true); 
			$('#sel_type').val(2)
		}	
		var val = $('#sel_type').val();

		if(val == 1){
			$('#div_fil').html(div_monthly);
		}else{
			$('#div_fil').html(div_yearly);
		}
	}

	function submit_filter(type,export_type=1){
		var $rpt = $('#sel_rpt').val();
		if($rpt <= 2 || type == 1){
			var parameter = {
				'financial_report_type' : $('#sel_rpt').val(),
				'type' : $('#sel_type').val(),
				'export_type' : export_type
			};

			if($('#sel_type').val() == 1){
				parameter['month_end'] =  $('#sel_month_end').val();
				parameter['year'] = $('#sel_year').val() ;
			}else{
				parameter['year_end'] = $('#sel_year_end').val() ;
			}

			var link_param = $.param(parameter);

			if(type == 1){
				window.location = '/financial_statement/'+'<?php echo ($gen_type==1)?"index":"comparative"; ?>'+'?'+link_param;
			}else{
				window.open('/financial_statement/'+('<?php echo ($gen_type==1)?"index":"comparative"; ?>')+'/export'+'?'+link_param,'_blank')
			}
			console.log({link_param});
		// console.log({parameter});		
	}else{

		var parameter = {
			'year' : $('#sel_year_end').val(),
			'export_type' : export_type
		};
		var link_param = $.param(parameter);

		var link = ($rpt == 3)?"/cash_flow/export":"/changes_equity/export";
		window.open(link+'?'+link_param,'_blank')
	}

}
</script>
@endpush




