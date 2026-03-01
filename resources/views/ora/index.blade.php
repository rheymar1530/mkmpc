@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	#tbl-or td, #tbl-or th{
		font-size: 0.9rem;
		padding: 4px;
	}
	.bold{
		font-weight: bold;
	}
	.series-row td{
		font-weight: bold;
		background: #939cab;
	}
	.missing{
		background: #ffb3b3;
		font-weight: bold;
	}
</style>
<div class="container">
	<div class="card">
		<div class="card-body">
			<div class="text-center">
				<h5 class="head_lbl">ORA</h5>
			</div>
			<form id="frm-generate-rpt">
				<div class="row d-flex align-items-end">
					<div class="form-group col-md-3">
						<label class="lbl_color">Date</label>
						<input type="date" class="form-control form-control-border" name="start" value="{{$selected_start}}">
					</div>
					<div class="form-group col-md-3">
						<input type="date" class="form-control form-control-border" name="end" value="{{$selected_end}}">
					</div>
					<div class="form-group col-md-2">
						<button class="btn btn-sm bg-gradient-success w-100" onclick="generate_matchup(0)">Generate</button>
					</div>
					<div class="form-group col-md-2">
						<div class="dropdown">
							<button class="btn btn-sm bg-gradient-primary col-md-12 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
							Export
							</button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
								<button type="submit" class="dropdown-item" onclick="generate_matchup(1)">PDF</button>
								<button type="submit" class="dropdown-item" onclick="generate_matchup(2)">Excel</button>
							</div>
						</div>
					</div>
					
				</div>
			</form>
			<div class="row">
				<div class="col-md-12">
					@include('ora.table')
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
const generate_matchup=(type)=>{
	EXPORT_MODE = type;
}
$('#frm-generate-rpt').submit(function(e){
	e.preventDefault();
	let data = $(this).serialize();


	if(EXPORT_MODE == 0){
		window.location =  `/ora?${data}`
	}

	let x = (EXPORT_MODE ==1) ? 'pdf' : 'excel';
	let link = `/ora/export/${x}?${data}`;
	window.open(link,'_blank');
	// if(EXPORT_MODE == 1){
	// 	$('#print_frame').attr('src',link);
	// 	$('#print_div').show();
	// 	console.log({data});			
	// }else{
	// 	window.open(link,'_blank');
	// }
})
</script>
@endpush