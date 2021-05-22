@extends('adminlte::page')

@section('title', 'List of Dishes')

@section('content_header')
<h1 class="m-0 text-dark">Dishes - Total ({{count($dishes)}})</h1>
@stop

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h1 class="card-title mt-2">List of Dishes</h1>
        <div class="card-tools">
          <a href={{url("/admin/dishes/create")}}>
            <button type="button" class="btn btn-block bg-gradient-primary btn-sm	">Add a Dishes</button>
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
              <th>Name</th>
              <th>Restaurant</th>
              <th>Item Category</th>
              <th>Price</th>
              <th>Active</th>
              <th>Action</th>
              {{-- <th>Created at</th>
              <th>Action</th> --}}
            </tr>
          </thead>
          <tbody>
            @foreach ($dishes as $dish)
            <tr>
              <td>#{{ $dish->id }}</td>
              <td>@empty(!$dish->image)<img src="{{ $dish->image }}" height="60"
                  width="100" class="text-center" /> @endempty </td>
              <td>{{ $dish->name }}</td>
              <td>{{ $dish->restaurant->name }}</td>
              <td>{{ $dish->dish_category->name ?? 'NONE'}}</td>
              <td>{{ setting('currency_symbol').$dish->price }}</td>
              <td class="text-center">
                @if ($dish->active === 1)
                <span class="badge badge-pill badge-primary">Active</span>
                @else
                <span class="badge badge-pill badge-danger">Inactive</span>
                @endif
              </td>
              <td>
                <a href={{url("/admin/dishes/".$dish->id."/edit")}}>
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
              <th>Image</th>
              <th>Name</th>
              <th>Restaurant</th>
              <th>Item Category</th>
              <th>Price</th>
              <th>Active</th>
              <th>Action</th>
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