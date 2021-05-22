@extends('adminlte::page')

@section('title', 'Dishes Categories')

@section('content_header')
<h1 class="m-0 text-dark">Dishes Categories</h1>
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
				<h3 class="card-title">Create a Dishes Categories</h3>
			</div>
			<!-- /.card-header -->
			<!-- form start -->
			<form role="form" id="createForm" method="POST"
				action="{{url('/restaurant-owner/dish_categories/'.$category->id.'/update')}}" enctype="multipart/form-data">
				@csrf
				@method('PUT')
				<div class="card-body">
					@include('errors.errors-forms')

					@if (\Session::has('success'))
					<div class="alert alert-success">
						{!! \Session::get('success') !!}
					</div>
					@endif

					<x-form-elements name="name" type="text" label="Category Name"
						value="{{old('name',$category->name)}}" required />

					<x-form-elements name="image" type="file" label="Image"
						value="{{old('image', $category->image)}}"
						info="File Size should be less than 2mb" required accept="image/*" />

					<x-form-elements name="active" type="switch" label="Active"
						value="{{old('active',$category->active)}}" />
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