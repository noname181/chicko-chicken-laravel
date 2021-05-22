@extends('adminlte::page')

@section('title', 'List of Coupons')

@section('content_header')
<h1 class="m-0 text-dark">Coupons - Total ({{count($coupons)}})</h1>
@stop

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h1 class="card-title mt-2">List of Coupons</h1>
        <div class="card-tools">
          <a href={{url("/admin/coupons/create")}}>
            <button type="button" class="btn btn-block bg-gradient-primary btn-sm	">Add a Coupons</button>
          </a>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <table id="table-data" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Name</th>
              <th>Restaurant</th>
              <th>Code</th>
              <th>Type</th>
              <th>Discount</th>
              <th>Usage</th>
              <th>Expiry Date</th>
              <th>Active</th>
              <th>Action</th>
              {{-- <th>Created at</th>
              <th>Action</th> --}}
            </tr>
          </thead>
          <tbody>
            @foreach ($coupons as $coupon)
            <tr>
              <td>{{ $coupon->name }}</td>
              <td>{{ $coupon->restaurant->name ?? 'All Restaurant' }}</td>
              <td>{{ $coupon->coupon_code }}</td>
              <td>{{ $coupon->discount_type }}</td>
              <td>{{ $coupon->discount }}</td>
              <td>{{ $coupon->max_usage }}</td>
              <td>{{ $coupon->expiry_date }}</td>
              <td class="text-center">
                @if ($coupon->active === 1)
                <span class="badge badge-pill badge-primary">Active</span>
                @else
                <span class="badge badge-pill badge-danger">Inactive</span>
                @endif
              </td>
              <td>
                <a href={{url("/admin/coupons/".$coupon->id."/edit")}}>
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
              <th>Restaurant</th>
              <th>Code</th>
              <th>Type</th>
              <th>Discount</th>
              <th>Usage</th>
              <th>Expiry Date</th>
              <th>Active</th>
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