@extends('adminlte::page')

@section('title', 'List of Addons')

@section('content_header')
<h1 class="m-0 text-dark">Addons Categories - Total ({{count($addons)}})</h1>
@stop

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h1 class="card-title mt-2">List of Addons Categories</h1>
        <div class="card-tools">
          <a href={{url("/admin/dish_addons_categories/create")}}>
            <button type="button" class="btn btn-block bg-gradient-primary btn-sm	">Add a Addon Category</button>
          </a>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <table id="table-data" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>type</th>
              <th>Action</th>
              {{-- <th>Created at</th>
              <th>Action</th> --}}
            </tr>
          </thead>
          <tbody>
            @foreach ($addons as $addon)
            <tr>
              <td>#{{ $addon->id }}</td>
              <td>{{ $addon->name }}</td>
              <td>{{ $addon->type}}</td>
              <td>
                <a href={{url("/admin/dish_addons_categories/".$addon->id."/edit")}}>
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
              <th>ID</th>
              <th>Name</th>
              <th>type</th>
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