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

<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">

				<div class="row">
					
					<div class="col-md-12">
						<h4>Cash Flow</h4>
						
						<div class="form-group row mt-4">
							<label class="col-md-1 control-label col-form-label col-md-cus" id="lbl_period" style="text-align: left">Year&nbsp;</label>
							
							<div class="col-md-2">
								<select class="form-control" id="sel_year">
									@for($i=2022;$i<=2050;$i++)
									<option value="{{$i}}" <?php echo ($i==$year)?'selected':''; ?>>{{$i}}</option>
									@endfor
								</select>
							</div>
							<button class="btn bg-gradient-success btn-sm" onclick="submit_filter(1)">Generate</button>
							<button class="btn bg-gradient-danger btn-sm" onclick="submit_filter(2)" style="margin-left:10px">Export to PDF</button>
						</div>
						
						
						<div id="div_fil"></div>
							
					</div>
					<div class="col-md-12">
						@include('cash_flow.table')
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">







function submit_filter(type){

	
	var parameter = {
		'year' : $('#sel_year').val(),
	};


	var link_param = $.param(parameter);
	console.log({link_param});


	var link = window.location.pathname;

	if(type == 1){
		window.location = link+'?'+link_param;
	}else{
		window.open(link+'/export?'+link_param,'_blank')
		// window.location = link+'/export?'+link_param;
	}



	
	// console.log({parameter});
}
</script>
@endpush




