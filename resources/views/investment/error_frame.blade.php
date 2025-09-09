@extends('adminLTE.admin_template_frame')
@section('content')

<div class="col-md-12">
	<div class="alert bg-gradient-danger" role="alert">
		<h4 class="alert-heading">Error Message</h4>
		<hr>
		<ul>
			<li>{{$message}}</li>
		</ul>
		
	</div>
</div>

@endsection