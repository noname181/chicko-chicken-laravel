@extends('adminlte::page')

@section('title', 'Create a User')

@section('content_header')
<h1 class="m-0 text-dark">Users</h1>
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
				<h3 class="card-title">Create a User</h3>
			</div>
			<!-- /.card-header -->
			<!-- form start -->
			<form role="form" id="createForm" method="POST" action="{{url('/admin/users/store')}}" enctype="multipart/form-data">
				@csrf
				<div class="card-body">
					@include('errors.errors-forms')

					<x-form-elements name="role" type="select2" label="Role" value="{{old('role')}}" :options="$roles"
						required />

					<x-form-elements name="name" type="text" label="Full Name" value="{{old('name')}}" required />

					<x-form-elements name="avatar" type="file" label="Profile Image" value="{{old('avatar')}}"
						info="File Size should be less than 2mb" accept="image/*" />

					<x-form-elements name="email" type="text" label="Email" value="{{old('email')}}" required />

					<x-form-elements name="phone" type="text" label="Phone" value="{{old('phone')}}" required />

					<x-form-elements name="password" type="text" subtype="password" label="Password"
						value="{{old('password')}}" required />


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
<script src="/vendor/inputmask/min/jquery.inputmask.bundle.min.js"></script>

<script>
	$(document).ready(function () {

	$('.select2').select2({
		theme: 'bootstrap4'
	});

	$("input[data-bootstrap-switch]").each(function(){
			$(this).bootstrapSwitch('state', $(this).prop('checked'));
	});

	$('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
	$('[data-mask]').inputmask();

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
