@extends('adminLTE.admin_template_frame')
@section('content')
	@include('investment.frame_content')
@endsection


@push('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		parent.set_amount_range('<?php echo $investment_data['amt_range'] ?? "0.00";?>');
	})
</script>
@endpush