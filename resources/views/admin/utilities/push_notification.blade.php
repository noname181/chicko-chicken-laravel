@extends('adminlte::page')

@section('title', 'Send a Push Notification')

@section('content_header')
<h1 class="m-0 text-dark">Send a Push Notification</h1>
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
				<h3 class="card-title">FCM Push Notification</h3>
			</div>
			<!-- /.card-header -->
			<!-- form start -->
			<form role="form" id="createForm" method="POST" action="{{url('/admin/push_notification/update')}}"
				enctype="multipart/form-data">
				@csrf
				<div class="card-body">
					@include('errors.errors-forms')
					@if (\Session::has('success'))
					<div class="alert alert-success">
						{!! \Session::get('success') !!}
					</div>
					@endif

					<x-form-elements name="user_id[]" multiple type="select2" label="Select Users"
						value="{{old('user_id[]')}}" :options="$users" required />

					<x-form-elements name="title" type="text" label="Notification Title" value="{{old('title')}}" required />

					<x-form-elements name="message" type="text" label="Notification Message"
						value="{{old('message')}}" required />

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
