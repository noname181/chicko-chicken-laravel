@extends('adminlte::page')

@section('title', $dish->name.' Dish')

@section('content_header')
<h1 class="m-0 text-dark">{{$dish->name}} Dish</h1>
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
				<h3 class="card-title">Edit Dish</h3>
			</div>
			<!-- /.card-header -->
			<!-- form start -->
			<form role="form" id="createForm" method="POST" action="{{url('/restaurant-owner/dishes/'.$dish->id.'/update')}}"
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
					<x-form-elements name="restaurant_id" type="select2" label="Restaurant"
						value="{{old('restaurant_id',$dish->restaurant_id)}}" :options="$restaurants" required />

					<x-form-elements name="name" type="text" label="Dish Name" value="{{old('name',$dish->name)}}"
						required />

					<x-form-elements name="description" type="text" label="Dish Description"
						value="{{old('description',$dish->description)}}" required />

					<x-form-elements name="image" type="file" label="Dish Image" value="{{old('image', $dish->image)}}"
						info="File Size should be less than 2mb" required accept="image/*" />

					<x-form-elements name="dish_category_id" type="select2" label="Dish Category"
						value="{{old('dish_category_id',$dish->dish_category_id)}}" :options="$categories" />

					<div class="form-group row">
						<label for="addon_id[]" class="col-sm-3 col-form-label">Select Addons
							@isset($required)<span class="text-danger">*</span>
							@endisset
						</label>
						<select name="addon_id[]" class="col-sm-9 form-control select2" multiple="multiple">
							@foreach ($addons as $option)
							{{-- {{dd( in_array($option->id, old('addon_id[]', $dish->dish_addons->pluck('addons_category_id')->toArray())) )}} --}}
							<option value="{{ $option->id }}" {{ in_array($option->id, old('addon_id[]', $dish->addons_dish->pluck('addons_category_id')->toArray())) ? "selected":"" }}>
								{{ $option->name }}</option>
							@endforeach
						</select>
					</div>
					
					<x-form-elements name="price" type="text" subtype="number" label="Price"
						value="{{old('price',$dish->price)}}" required />

					<x-form-elements name="discount_price" type="text" subtype="number" label="Dicounted Price"
						value="{{old('discount_price',$dish->discount_price)}}" />

					<x-form-elements name="is_veg" type="switch" label="Is Pure Veg"
						value="{{old('is_veg',$dish->is_veg)}}" />

					<x-form-elements name="featured" type="switch" label="Featured"
						value="{{old('featured',$dish->featured)}}" />

					<x-form-elements name="active" checked type="switch" label="Active"
						value="{{old('active',$dish->active)}}" />

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