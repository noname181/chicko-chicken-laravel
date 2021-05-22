@extends('adminlte::page')

@section('title', 'Create a Coupon')

@section('content_header')
<h1 class="m-0 text-dark">Coupons</h1>
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
				<h3 class="card-title">Create a Coupon</h3>
			</div>
			<!-- /.card-header -->
			<!-- form start -->
			<form role="form" id="createForm" method="POST" action="{{url('/admin/coupons/store')}}">
				@csrf
				<div class="card-body">
					@include('errors.errors-forms')

					<x-form-elements name="name" type="text" label="Coupon Name" value="{{old('name')}}" required />

					<x-form-elements name="description" type="text" label="Coupon Description"
						value="{{old('description')}}" required />

					<x-form-elements name="restaurant_id" type="select2" label="Restaurant"
						value="{{old('restaurant_id')}}" :options="$restaurants" required />

					<x-form-elements name="coupon_code" type="text" label="Coupon Code" value="{{old('coupon_code')}}"
						required />

					<x-form-elements name="discount_type" type="select2" label="Discount Type"
						value="{{old('discount_type')}}" :options="$discountType" required />

					<x-form-elements name="discount" type="text" subtype="number" label="Dicount"
						value="{{old('discount')}}" required />

					<x-form-elements name="expire_date" type="datepicker" label="Expiry Date"
						value="{{old('expire_date')}}" required />

					<x-form-elements name="max_usage" type="text" subtype="number" label="Max. usage"
						value="{{old('max_usage')}}" />

					<x-form-elements name="active" checked type="switch" label="Active" value="{{old('active')}}" />


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