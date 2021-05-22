@extends('adminlte::page')

@section('title', $addon->name.' Addon')

@section('content_header')
<h1 class="m-0 text-dark">{{$addon->name}} Addon</h1>
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
				<h3 class="card-title">Edit Addon</h3>
			</div>
			<!-- /.card-header -->
			<!-- form start -->
			<form role="form" id="createForm" method="POST" action="{{url('/restaurant-owner/dish_addons/'.$addon->id.'/update')}}"
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

					<x-form-elements name="name" type="text" label="Addon Name" value="{{old('name',$addon->name)}}"
						required />

					<x-form-elements name="addons_category_id" type="select2" label="Addon Category"
						value="{{old('addons_category_id',$addon->addons_category_id)}}" :options="$categories" />

					<x-form-elements name="price" type="text" subtype="number" label="Price"
						value="{{old('price',$addon->price)}}" required />

					<x-form-elements name="active" checked type="switch" label="Active"
						value="{{old('active',$addon->active)}}" />

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