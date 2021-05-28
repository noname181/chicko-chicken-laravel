@extends('adminlte::page')

@section('title', $restaurant->name.' Restaurant')

@section('content_header')
<h1 class="m-0 text-dark">{{$restaurant->name}} Restaurant</h1>
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
		<!-- jquery validation -->
		<div class="card card-primary card-tabs">
			<div class="card-header p-0 pt-1 border-bottom-0">
				<ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill"
							href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home"
							aria-selected="true"><i class="fas fa-pencil-alt"></i>Edit Restaurant</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tabs-dishes-tab" data-toggle="pill" href="#tabs-dishes" role="tab"
							aria-controls="tabs-dishes" aria-selected="false"><i
								class="far fas fa-fw fa-utensils "></i>Restaurant's Dishes</a>
					</li>
				</ul>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="tab-content" id="custom-tabs-three-tabContent">
					<div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel"
						aria-labelledby="custom-tabs-three-home-tab">
						<!-- form start -->
						<form role="form" id="createForm" method="POST"
							action="{{url('/admin/restaurants/'.$restaurant->id.'/update')}}"
							enctype="multipart/form-data">
							@csrf
							@method('PUT')
							<div class="card-body">
								@include('errors.errors-forms')

								@if (\Session::has('success'))
								<div class="alert alert-success">
									{!! \Session::get('success') !!}
								</div>
								@endif


								<x-form-elements name="name" type="text" label="Name"
									value="{{old('name', $restaurant->name) }}" required />

								<x-form-elements name="description" type="text" label="Description"
									value="{{old('description', $restaurant->description)}}" required />

								<x-form-elements name="user_id" type="select2" label="Restaurant Owner"
									value="{{old('user_id', $restaurant->user_id)}}" :options="$owners" required />

								<x-form-elements name="image" type="file" label="Image"
									value="{{old('image', $restaurant->image )}}"
									info="File Size should be less than 2mb" required accept="image/*" />

								<x-form-elements name="phone" type="text" label="Phone"
									value="{{old('phone', $restaurant->phone)}}" required />

								<x-form-elements name="email" type="text" subtype="email" label="Email"
									value="{{old('email', $restaurant->email)}}" required />

								<hr />

								<x-form-elements name="rating" type="text" subtype="number" label="Rating"
									value="{{old('rating', $restaurant->rating)}}" required />

								<x-form-elements name="delivery_time" type="text" subtype="number"
									label="Approx. Delivery Time (in mins)"
									value="{{old('delivery_time', $restaurant->delivery_time)}}" required />

								<x-form-elements name="for_two" type="text" subtype="number"
									label="Approx. price for two people"
									value="{{old('for_two', $restaurant->price_range)}}" required />

								<hr />
								<x-form-elements name="address" type="text" label="Full Address"
									value="{{old('address', $addresses->street)}}" required />

								<x-form-elements name="pincode" type="text" label="Pincode"
									value="{{old('pincode', $addresses->postal_code)}}" />

								<x-form-elements name="city" type="text" label="City"
									value="{{old('city', $addresses->city)}}" />

								<x-form-elements name="lat" type="text" subtype="number" label="Latitude"
									value="{{old('lat', $addresses->latitude)}}" required />

								<x-form-elements name="long" type="text" subtype="number" label="Longitude"
									value="{{old('long', $addresses->longitude)}}" required />

								<hr />

								<x-form-elements name="commission_rate" type="text" subtype="number"
									label="Commission Rate %"
									value="{{old('commission_rate', $restaurant->commission_rate)}}" required />

								<x-form-elements name="license_code" type="text" label="License Code"
									value="{{old('license_code', $restaurant->license_code)}}" required />

								<x-form-elements name="restaurant_charges" type="text" subtype="number"
									label="Restaurant Charges"
									value="{{old('restaurant_charges', $restaurant->restaurant_charges)}}" />

								<x-form-elements name="delivery_radius" type="text" subtype="number"
									label="Delivery Radius (in km)"
									value="{{old('delivery_radius', $restaurant->delivery_radius)}}" required />

								<hr />

								<x-form-elements name="is_veg" type="switch" label="Is Pure Veg"
									value="{{old('is_veg', $restaurant->is_veg)}}" />

								<x-form-elements name="featured" type="switch" label="Featured"
									value="{{old('featured', $restaurant->featured)}}" />

								<x-form-elements name="active" type="switch" label="Active"
									value="{{old('active', $restaurant->active)}}" />


							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<button type="submit" class="btn btn-primary">Submit</button>
							</div>
						</form>
					</div>
					<div class="tab-pane fade" id="tabs-dishes" role="tabpanel" aria-labelledby="tabs-dishes-tab">
						<div class="row">
							<div class="col-12">
								<div class="card">
									<div class="card-header">
										<h1 class="card-title mt-2">List of Dishes</h1>
										<div class="card-tools">
											<a href={{url("/admin/dishes/create")}}>
												<button type="button"
													class="btn btn-block bg-gradient-primary btn-sm	">Add a
													Dish</button>
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
													<td><img src="{{ $dish->image }}"
															height="60" width="100" class="text-center" /> </td>
													<td>{{ $dish->name }}</td>
													<td>{{ $dish->dish_category->name }}</td>
													<td>{{ $dish->price }}</td>
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
					</div>
				</div>
			</div>

		</div>
		<!-- /.card -->
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
	$(document).ready(function () {

	$('.select2').select2({
		theme: 'bootstrap4'
	});

	$("input[data-bootstrap-switch]").each(function(){
			$(this).bootstrapSwitch('state', $(this).prop('checked'));
	});

	$("#table-data").DataTable({
	"responsive": true,
	"autoWidth": false,
	"order": []
	});

	$(".custom-file-input").change(function() {
		var _this = $(this);
		if (this.files && this.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				console.log(_this.parent().parent().find('.img-preview'));
				_this.parent().parent().find('.img-preview').attr('src', e.target.result);
			}
			reader.readAsDataURL(this.files[0]);
		}
	});

});
</script>
@stop
