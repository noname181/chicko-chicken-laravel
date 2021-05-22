@extends('adminlte::page')

@section('title', 'List of '.$role.'s')

@section('content_header')
<h1 class="m-0 text-dark">{{$role}}s - Total ({{count($users)}})</h1>
@stop

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h1 class="card-title mt-2">List of {{$role}}s</h1>
        <div class="card-tools">
          <a href={{url("/admin/users/create")}}>
            <button type="button" class="btn btn-block bg-gradient-primary btn-sm	">Add User</button>
          </a>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <table id="table-data" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Created</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
            <tr>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email}}</td>
              <td>{{ $user->phone }}</td>
              <td>{{ $user->created_at->diffForHumans() }}</td>
              <td class="text-center">
                @if ($user->phone_verified_at !== null)
                <span class="badge badge-pill badge-primary">Verified</span>
                @else
                <span class="badge badge-pill badge-danger">Unverified</span>
                @endif
              </td>
              <td>
                <a href={{url("/admin/users/".$user->id."/edit")}}>
                  <button type="button" class="btn btn-info btn-sm">
                  <i class="fas fa-pencil-alt"></i>
                  Edit</button>
                </a>
              </td>
            </tr>
            @endforeach

          </tbody>
          <tfoot>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Created</th>
              <th>Verification Status</th>
              <th>Action</th>
            </tr>
          </tfoot>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- ./col -->
</div>
<!-- /.row -->
@stop

@section('js')
<script>
  $(function () {
    $("#table-data").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
   //  $('#example2').DataTable({
   //    "paging": true,
   //    "lengthChange": false,
   //    "searching": false,
   //    "ordering": true,
   //    "info": true,
   //    "autoWidth": false,
   //    "responsive": true,
   //  });
  });
</script>
@stop