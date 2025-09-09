@extends('adminLTE.admin_template_frame')
@section('content')

@include('loan.loan_table')

<button class="btn btn-sm bg-gradient-danger2 col-md-12" onclick="parent.load_loan_offset()" style="margin-top:15px"><i class="fas fa-mouse-pointer"></i>&nbsp;&nbsp;Click to View and Pay Active Loan</button>



<button class="btn btn-sm bg-gradient-warning col-md-12" onclick="parent.showDeductionModal()" style="margin-top:15px"><i class="fas fa-mouse-pointer"></i>&nbsp;&nbsp;Click to Add Other Deductions</button>

@push('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		parent.set_terms_selected('<?php echo $service_details->terms_token ?? '' ?>');
		if(parent.hide_proceed == 0){
			parent.show_proceed_button();
		}
	})

	function get_loan_proceed(){
		var $loan_proceed = '<?php echo $loan['TOTAL_LOAN_PROCEED']?>';
		if($loan_proceed < 0){
			return false;
		}

		return true;
	}
</script>
@endpush
@endsection

<!-- <i class="fa-solid fa-arrow-pointer"></i> -->