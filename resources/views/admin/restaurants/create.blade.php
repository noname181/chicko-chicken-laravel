@extends('adminlte::page')

@section('title', 'Add Restaurant')

@section('content_header')
<h1 class="m-0 text-dark">Restaurant</h1>
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
		<div class="card card-primary">
			<div class="card-header">
				<h3 class="card-title">Add New Restaurant</h3>
			</div>
			<!-- /.card-header -->
			<!-- form start -->
			<form role="form" id="createForm" method="POST" action="{{url('/admin/restaurants/store')}}"
				enctype="multipart/form-data">
				@csrf
				<div class="card-body">
					@include('errors.errors-forms')

					@if (\Session::has('success'))
					<div class="alert alert-success">
						{!! \Session::get('success') !!}
					</div>
					@endif


					<x-form-elements name="name" type="text" label="Name" value="{{old('name')}}" required />

					<x-form-elements name="description" type="text" label="Description" value="{{old('description')}}"
						required />

					<x-form-elements name="user_id" type="select2" label="Restaurant Owner"
						value="{{old('user_id')}}" :options="$owners" required />

					<x-form-elements name="image" type="file" label="Image" value="{{old('image')}}"
						info="File Size should be less than 2mb" required accept="image/*" />

					<x-form-elements name="phone" type="text" label="Phone" value="{{old('phone')}}" required />

					<x-form-elements name="email" type="text" subtype="email" label="Email" value="{{old('email')}}"
						required />

					<hr />

					<x-form-elements name="rating" type="text" subtype="number" label="Rating" value="{{old('rating')}}"
						required />

					<x-form-elements name="delivery_time" type="text" subtype="number"
						label="Approx. Delivery Time (in mins)" value="{{old('delivery_time')}}" required />

					<x-form-elements name="for_two" type="text" subtype="number" label="Approx. price for two people"
						value="{{old('for_two')}}" required />

					<hr />

					<x-form-elements name="address" type="text" label="Full Address" value="{{old('address')}}"
						required />

					<x-form-elements name="pincode" type="text" label="Pincode" value="{{old('pincode')}}" />

					<x-form-elements name="city" type="text" label="City" value="{{old('city')}}" />

					<x-form-elements name="lat" type="text" subtype="number" label="Latitude" value="{{old('lat')}}"
						required />

					<x-form-elements name="long" type="text" subtype="number" label="Longitude" value="{{old('long')}}"
						required />

					<hr />

					<x-form-elements name="commission_rate" type="text" subtype="number" label="Commission Rate %"
						value="{{old('commission_rate')}}" required />

					<x-form-elements name="license_code" type="text" label="License Code"
						value="{{old('license_code')}}" required />

					<x-form-elements name="restaurant_charges" type="text" subtype="number" label="Restaurant Charges"
						value="{{old('restaurant_charges')}}" />

					<x-form-elements name="delivery_radius" type="text" subtype="number" label="Delivery Radius (in km)"
						value="{{old('delivery_radius')}}" required />

					<hr />

					<x-form-elements name="is_veg" type="switch" label="Is Pure Veg" value="{{old('is_veg')}}" />

					<x-form-elements name="featured" type="switch" label="Featured" value="{{old('featured')}}" />

					{{-- <x-form-elements name="active" type="switch" label="Active" value="{{old('active')}}" /> --}}


				</div>
				<!-- /.card-body -->
				<div class="card-footer">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
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
