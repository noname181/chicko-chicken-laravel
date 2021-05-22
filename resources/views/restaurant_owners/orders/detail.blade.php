@extends('adminlte::page')

@section('title', 'Order '.$order->unique_id)

@section('content_header')
<h1 class="m-0 text-dark">Order {{$order->unique_id}}</h1>
@stop

@section('css')
<link rel="stylesheet" href="/vendor/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css">
<link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
<link rel="stylesheet" href="/vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@stop

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
	<div class="col-12">

		@include('errors.errors-forms')

		@if (\Session::has('success'))
		<div class="alert alert-success">
			{!! \Session::get('success') !!}
		</div>
		@endif

		<div class="card card-primary card-tabs">
			<div class="card-header p-0 pt-1 border-bottom-0">
				<ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill"
							href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home"
							aria-selected="true">Order Detail</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tabs-dishes-tab" data-toggle="pill" href="#tabs-dishes" role="tab"
							aria-controls="tabs-dishes" aria-selected="false">
							<i class="fas fa-pencil-alt"></i> Update Order Status</a>
					</li>
				</ul>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="tab-content" id="custom-tabs-three-tabContent">
					<div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel"
						aria-labelledby="custom-tabs-three-home-tab">
						<!-- Main content -->
						<div class="invoice p-3 mb-3" id="invoice">
							<div class="row border-bottom">
								<div class="col-12 pb-2">
									<h4 class="text-muted">
										<i class="far fas fa-store"></i> {{$order->restaurant->name}}
										{{-- <button type="button" class="btn btn-success text-uppercase float-right"><i
												class="fas fa-biking"></i>
											Accept the Order
										</button> --}}
									</h4>
								</div>
								<!-- /.col -->
							</div>
							<!-- info row -->
							<div class="row invoice-info pt-2">
								<div class="col-sm-4 invoice-col">
									From
									<address>
										<strong>{{$order->user->name}}</strong><br>
										{{$order->user->addresses[0]->street}}<br>
										Phone: {{$order->user->phone}}<br>
										Email: {{$order->user->email}}
									</address>
								</div>
								<!-- /.col -->
								<div class="col-sm-4 invoice-col">
									To
									<address>
										<strong>{{$order->restaurant->name}}</strong><br>
										{{$order->restaurant->addresses[0]->street}}<br>
										{{$order->restaurant->addresses[0]->city}}<br>
										{{$order->restaurant->addresses[0]->postal_code}}<br>
										Phone: {{$order->restaurant->phone}}<br>
										Email: {{$order->restaurant->email}}
									</address>
								</div>
								<!-- /.col -->
								<div class="col-sm-4 invoice-col">
									<b>Invoice {{$order->unique_id}}</b><br>
									<b>{{$order->created_at}}</b><br>
									<br>
									<b>Order ID:</b> {{$order->id}}<br>
									<b>Order placed:</b> {{$order->created_at->diffForHumans()}}<br>
									<b>Status:</b> <button type="button"
										class="btn btn-success btn-xs">{{ $order->status }}</button>
								</div>
								<!-- /.col -->
							</div>
							<!-- /.row -->

							<!-- Table row -->
							<div class="row">
								<div class="col-12 table-responsive">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>Dish Name</th>
												<th>Per Price</th>
												<th>Qty</th>
												<th>Subtotal</th>
											</tr>
										</thead>
										<tbody>
											@php $sub_total = 0;$addons = 0;  @endphp
											@foreach ($order->order_dishes as $order_dishes)
											<tr>
												<td>{{$order_dishes->name}} <br>
													@if (count($order_dishes->order_adddons) > 0)
														Addons: 
														@foreach ($order_dishes->order_adddons as $order_adddons)
															{{ $order_adddons->name . '-' . Setting::get('currency_symbol').($order_adddons->price)}},
															@php $addons+=(int)$order_adddons->price @endphp
														@endforeach
													@endif
												</td>
												<td>{{Setting::get('currency_symbol').$order_dishes->price}}</td>
												<td>x {{$order_dishes->quantity}}</td>
												<td>{{(Setting::get('currency_symbol').(($order_dishes->price * $order_dishes->quantity)+$addons))}}
												</td>
											</tr>
											@php $sub_total += ($order_dishes->quantity*$order_dishes->price)+$addons @endphp
											@endforeach
										</tbody>
									</table>
								</div>
								<!-- /.col -->
							</div>
							<!-- /.row -->

							<div class="row">
								<!-- accepted payments column -->
								<div class="col-6">
									<p class="lead">Payment Methods:</p>

									<p class="text-muted well well-sm text-uppercase shadow-none font-weight-bold"
										style="margin-top: 10px;">
										{{$order->payment_mode}}
									</p>
								</div>
								<!-- /.col -->
								<div class="col-6">

									<div class="table-responsive">
										<table class="table">
											<tr>
												<th style="width:50%">Subtotal:</th>
												<td>{{Setting::get('currency_symbol').$sub_total}}</td>
											</tr>
											<tr>
												<th>Tax ({{Setting::get('tax_percentage')}}%)</th>
												<td>{{Setting::get('currency_symbol').$order->tax}}</td>
											</tr>
											<tr>
												<th>Delivery Charges:</th>
												<td>{{Setting::get('currency_symbol').$order->delivery_charge}}</td>
											</tr>
											<tr>
												<th>Coupon Discount (-):</th>
												<td>{{Setting::get('currency_symbol').$order->coupon_discount}}</td>
											</tr>
											<tr>
												<th>Total:</th>
												<td>{{Setting::get('currency_symbol').$order->total}}</td>
											</tr>
										</table>
									</div>
								</div>
								<!-- /.col -->
							</div>
							<!-- /.row -->

							<!-- this row will not appear when printing -->
							<div class="row no-print">
								<div class="col-12">
									<a href="javascript:void()" onclick="printDiv('invoice')" target="_blank" class="btn btn-default"><i
											class="fas fa-print"></i>
										Print</a>
								</div>
							</div>
						</div>
						<!-- /.invoice -->
					</div>

					<div class="tab-pane fade" id="tabs-dishes" role="tabpanel" aria-labelledby="tabs-dishes-tab">
						<!-- form start -->
						<form role="form" id="createForm" method="POST"
							action="{{url('/restaurant-owner/orders/'.$order->id.'/update')}}"
							enctype="multipart/form-data">
							@csrf
							@method('PUT')
							<div class="card-body">

								<div class="form-group row">
									<label for="order_status" class="col-sm-3 col-form-label">Order Status
									</label>
									<select name="order_status" class="col-sm-9 form-control select2">
										@foreach ($orderStatus as $option)
										<option value="{{ $option }}"
											{{ ($option == old('order_status',$order->status) ) ? "selected":"" }}>
											{{ $option }}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group row">
									<label for="payment_status" class="col-sm-3 col-form-label">Payment Status
									</label>
									<select name="payment_status" class="col-sm-9 form-control select2">
										@foreach ($paymentStatus as $option)
										<option value="{{ $option }}"
											{{ ($option == old('payment_status',$order->status) ) ? "selected":"" }}>
											{{ $option }}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group row">
									<label for="delivery_user_id" class="col-sm-3 col-form-label">Delivery Scout Assign
									</label>
									<select name="delivery_user_id" class="col-sm-9 form-control select2">
										<option value=""></option>
										@foreach ($deliveryScout as $option)
										<option value="{{ $option->id }}"
											{{ ($option->id == old('delivery_user_id',$order->order_delivery_assign->user_id) ) ? "selected":"" }}>
											{{ $option->name }}</option>
										@endforeach
									</select>
								</div>

							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<button type="submit" class="btn btn-primary">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>

		</div>


	</div>
	<!-- ./col -->
</div>
<!-- /.row -->
@stop

@section('js')
<script src="/vendor/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="/vendor/jquery-validation/jquery.validate.min.js"></script>
<script src="/vendor/select2/js/select2.full.min.js"></script>

<script>
	$(function () {
    
	$('.select2').select2({
		theme: 'bootstrap4',
		placeholder: {
			id: '', // the value of the option
			text: 'None Selected'
		}
	});

	$("input[data-bootstrap-switch]").each(function(){
			$(this).bootstrapSwitch('state', $(this).prop('checked'));
	});

	function printDiv(divName) {
		// var printContents = document.getElementById(divName).innerHTML;
		// var originalContents = document.body.innerHTML;
		// document.body.innerHTML = printContents;
		window.print();
		// document.body.innerHTML = originalContents;
	}

  });
</script>
@stop