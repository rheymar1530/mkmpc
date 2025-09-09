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
		padding: 1px 3px 1px 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 1rem;
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
$types = [
	1=>'Monthly',
	2=>'Daily'
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


?>
<div class="row section_body">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<h4 class="head_lbl text-center mb-4">Treasurer's Report</h4>
					</div>
				</div>
				<form>
					<div class="row d-flex align-items-end">
						<div class="form-group col-md-2">
							<label class="lbl_color">Type</label>
							<select class="form-control form-control-border p-0" name="type" id="sel-type">
								@foreach($types as $val=>$type)
								<option value="{{$val}}" <?php echo($val==$selected_type)?'selected':''; ?>>{{$type}}</option>
								@endforeach
							</select>
						</div>
						
						<div class="form-group col-md-2 div-monthly">
							<label class="lbl_color">Month</label>
							<select class="form-control form-control-border p-0" name="month" id="sel-month">
								@foreach($month_list as $key=>$month)
								<option value="{{$key}}" <?php echo($key==$selected_month)?'selected':''; ?> >{{$month}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-1 div-monthly">
							<label class="lbl_color">Year</label>
							<select class="form-control form-control-border p-0" name="year" id="sel-year">
								@for($i=$year_start;$i<=$current_year;$i++)
								<option value="{{$i}}" <?php echo($i==$selected_year)?'selected':''; ?>>{{$i}}</option>
								@endfor
							</select>
						</div>

						<div class="form-group col-md-2 div-daily">
							<label class="lbl_color">Date</label>
							<input type="date" class="form-control form-control-border" id="txt-date" name="date" value="{{$selected_day}}">
						</div>


						<div class="form-group col-md-2">
							<button type="button" class="btn btn-sm bg-gradient-success2 w-100" onclick="Generate()">Generate</button>
						</div>
						<div class="form-group col-md-2">
							<div class="dropdown show">
								<button type="button" class="btn btn-sm bg-gradient-primary2 col-md-12 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
									Export
								</button>
								<div class="dropdown-menu">
									<button type="button" class="dropdown-item" onclick="ExportData(1)">PDF</button>
									<button type="button" class="dropdown-item" onclick="ExportData(2)">Excel</button>
								</div>
							</div>
						</div>

					</div>
				</form>

				<div class="row">
					<div class="col-md-12">
						
						<div id="div_fil"></div>

						@include('treasurer_report.table')
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
	const ExportData = (type) =>{
		$t = (type==1)?'pdf':'excel';
		var v = $('#sel-type').val();
		$query = {
			'month' : {{$selected_month}},
			'year' : {{$selected_year}},
			'type' : {{$selected_type}},
			'date' : '{{$selected_day}}'
		};
		
		window.open(`/treasurer-report/export/${$t}?`+$.param($query),'_blank');
	}
	
	const Generate = ()=>{
		$query = {
			'month' : $('#sel-month').val(),
			'year' : $('#sel-year').val(),
			'type' : $('#sel-type').val(),
			'date' : $('#txt-date').val()
		};
		window.location = `/treasurer-report?`+$.param($query);
	}
	$(document).ready(function () {
	    let $divDaily = $(".div-daily").detach();    // store daily field
	    let $divMonthly = $(".div-monthly").detach(); // store monthly field

	    function toggleDateFields() {
	        let selectedType = $("#sel-type").val();


	        if (selectedType == 1) {
	            // remove daily if exists
	            $(".div-daily").remove();
	            // attach monthly fields before Generate button
	            $(".btn.bg-gradient-success2").closest(".form-group").before($divMonthly);
	        } 
	        else if (selectedType == 2) {
	            // remove monthly if exists
	            $(".div-monthly").remove();
	            // attach daily field before Generate button
	            $(".btn.bg-gradient-success2").closest(".form-group").before($divDaily);
	        }
	    }

	    // Run on page load
	    toggleDateFields();

	    // Run on change
	    $("#sel-type").on("change", toggleDateFields);
	});
</script>
@endpush