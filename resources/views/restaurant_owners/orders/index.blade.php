@extends('adminlte::page')

@section('title', 'Orders')

@section('content_header')
<h1 class="m-0 text-dark">Orders - Total ({{count($orders)}})</h1>
@stop

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <!-- <div class="card-header">
              <h3 class="card-title">DataTable with default features</h3>
            </div> -->
      <!-- /.card-header -->
      <div class="card-body">
        <table id="orders-data" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Restaurant</th>
              <th>Status</th>
              <th>Total Amount</th>
              <th>Ordered at</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($orders as $order)
            <tr>
              <td>{{ $order->unique_id }}</td>
              <td>{{ $order->restaurant->name }}
              </td>
              <td class="text-center">
                <button type="button" class="btn btn-success btn-xs">{{ $order->status }}</button>
              </td>
              <td>{{ Setting::get('currency_symbol').$order->total }}</td>
              <td>{{ $order->created_at->diffForHumans() }}</td>
              <td>
                <a href={{url("/restaurant-owner/orders/".$order->id)}}>
                  <button type="button" class="btn btn-info btn-sm">
                    {{-- <i class="fas fa-pencil-alt"></i> --}}
                    View</button>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th>Order ID</th>
              <th>Restaurant</th>
              <th>Status</th>
              <th>Total</th>
              <th>Ordered at</th>
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
    $("#orders-data").DataTable({
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