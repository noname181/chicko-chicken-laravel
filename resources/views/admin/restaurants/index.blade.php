@extends('adminlte::page')

@section('title', 'Restaurants')

@section('content_header')
<h1 class="m-0 text-dark">Restaurants - Total ({{count($restaurants)}})</h1>
@stop

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h1 class="card-title mt-2">List of Restaurants</h1>
        <div class="card-tools">
          <a href={{url("/admin/restaurants/create")}}>
            <button type="button" class="btn btn-block bg-gradient-primary btn-sm	">Add a Restaurant</button>
          </a>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <table id="table-data" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Restaurant</th>
              <th>Owner Name</th>
              <th>Active</th>
              <th>Action</th>
              {{-- <th>Created at</th>
              <th>Action</th> --}}
            </tr>
          </thead>
          <tbody>
            @foreach ($restaurants as $restaurant)
            <tr>
              <td>#{{ $restaurant->id }}</td>
              <td><img src="{{ $restaurant->image }}" height="60" width="100"
                  class="text-center" /> </td>
              <td>{{ $restaurant->name }}</td>
              <td>{{ $restaurant->user->name }}</td>
              <td class="text-center">
                @if ($restaurant->active === 1)
                <span class="badge badge-pill badge-primary">Active</span>
                @else
                <span class="badge badge-pill badge-danger">Inactive</span>
                @endif
              </td>
              <td>
                <a href={{url("/admin/restaurants/".$restaurant->id."/edit")}}>
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
              <th>#ID</th>
              <th>Image</th>
              <th>Name</th>
              <th>Restaurant</th>
              <th>Category</th>
              <th>Price</th>
              {{-- <th>Created at</th>
              <th>Action</th> --}}
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
      "order": []
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