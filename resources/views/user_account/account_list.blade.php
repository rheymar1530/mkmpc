@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
#tbl_account_list  tr>td{
	padding:3px !important;
	vertical-align:top;
	font-family: Arial !important;
	font-size: 14px !important;
}  
</style>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Account List</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="tbl_account_list" class="table table-bordered table-hover">
                  <thead>
                  <tr class="table_header">
                    <th>Account ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Privilege</th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($users as $user)
                      <tr>
                        <td>{{$user->id}}</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{ $user->priv_name }}</td>
                        <td>
                          <a class="btn btn-xs btn-primary" href="/user/edit?id={{$user->id}}">Edit</a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>

                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
@endsection
@push('scripts')
<script>
  $(function () {
    $("#tbl_account_list").DataTable({
      "responsive": true, 
      "lengthChange": true, 
      "autoWidth": false,
      "pageLength": 20,
       scrollCollapse: true,
       scrollY: '70vh',
       "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

  });
</script>	
@endpush