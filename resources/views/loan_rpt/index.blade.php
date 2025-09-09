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
	$current_month =intval(MySession::current_month());
	// $current_month = intval("02");
?>
<div class="container-fluid section_body">
<div class="row ">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<h2 class="head_lbl text-center">Lending Summary Update</h2>
				<form id="frm-generate-rpt">
					<div class="row d-flex align-items-end mt-3">
						<div class="form-group col-md-2">
							<label class="lbl_color mb-0">Month</label>
							<select class="form-control" name="month">
								@foreach($month_list as $m=>$month)
								<option value="{{$m}}" <?php echo ($m == $current_month)?'selected':''; ?>>{{$month}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-2">
							<label class="lbl_color mb-0">Year</label>
							<select class="form-control" name="year">
								@for($i=2024;$i<=2050;$i++)
								<option value="{{$i}}" <?php echo ($i == MySession::current_year())?'selected':''; ?>  >{{$i}}</option>
								@endfor
							</select>
						</div>
						<div class="form-group col-md-4">
							<label class="lbl_color mb-0">Loan Service</label>
							<select class="form-control" name="id_loan_service">
								@foreach($loan_services as $ls)
								<option value="{{$ls->id_loan_service}}">{{$ls->name}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-1">
							<button class="btn bg-gradient-primary2 btn-sm">Generate</button>
						</div>
					</div>
				</form>
				<div id="print_div" class="hide">
					<button class="btn btn-sm bg-gradient-danger2 float-right round_button mb-2" onclick="open_new_tab()">Open in new tab</button>
					<iframe id="print_frame" class="embed-responsive-item" frameborder="0" style="border:0;height:700px;width: 100%;" src=""></iframe>
				</div>
			</div>
		</div>
		
	</div>
</div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">


	$('#frm-generate-rpt').submit(function(e){
		e.preventDefault();
		let data = $(this).serialize();
		let link = `/loan/released-rpt/export?${data}`;

		$('#print_frame').attr('src',link);
		$('#print_div').show();
		console.log({data});


		// if(EXPORT_OPCODE == 1){
		// 	$('#print_frame').attr('src',link);
		// 	$('#print_div').show();
		// 	console.log({data});			
		// }else{
		// 	window.open(link,'_blank');
		// }

	})

	const open_new_tab = ()=>{
		let data = $('#frm-generate-rpt').serialize();
		let link = `/loan/released-rpt/export?${data}`;

		window.open(`${link}`,'_blank');
	}
</script>
@endpush




