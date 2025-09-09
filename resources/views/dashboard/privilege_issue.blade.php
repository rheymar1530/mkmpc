@extends('adminLTE.admin_template')
@section('content')
  @if(Session::get('message') != "")
  <div class="alert alert-warning">
    <strong>Privilege Role Issue</strong> You dont have a permission to access this module.<br>
    	<a href="/admin"><< Back to main menu</a>
    
  </div>
  @endif

@endsection
