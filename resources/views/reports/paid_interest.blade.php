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

?>
<div class="container-fluid section_body">
<div class="row ">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<h2 class="head_lbl text-center">Paid Interest Summary</h2>
				<div class="row mt-3">
					
					<div class="col-md-12">
						<div class="form-group row mt-3">
							<div class="col-md-1">
								<label>Year</label>
							</div>
							<div class="col-md-2">

								<select class="form-control" id="sel_year_end">
									@for($i=2022;$i<=2050;$i++)
									<option value="{{$i}}" <?php echo ($selected_year == $i)?"selected":""; ?> >{{$i}}</option>
									@endfor
								</select>
							</div>
							<button class="btn bg-gradient-primary2 btn-sm" onclick="submit_filter(1)">Generate</button>
							<div class="btn-group ml-1">
								<button type="button" class="btn bg-gradient-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Export
								</button>
								<div class="dropdown-menu">
									
									<a class="dropdown-item" onclick="submit_filter(2)">PDF</a>
									<a class="dropdown-item" onclick="submit_filter(3)">Excel</a>
								</div>
							</div>
							
				
						</div>
						<div class="table-responsive" style="overflow-x:auto;max-height: calc(100vh - 100px);">
							@include('reports.interest_table')
						</div>
						
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>
</div>
@include('global.print_modal')
@endsection

@push('scripts')
<script type="text/javascript">





	function submit_filter(type){


		var parameter = {
			'year' : $('#sel_year_end').val()
		};
		var link_param = $.param(parameter);
		if(type == 1){
			
			window.location = '/summary/paid_interest?'+link_param;			
		}else if(type == 2){
			window.open('/summary/paid_interest/export?'+link_param,'_blank');
		}else if(type == 3){
			window.open('/summary/paid_interest/export-excel?'+link_param,'_blank');
		}



	
	// console.log({parameter});
}
</script>
@endpush




