@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
<h1 class="m-0 text-dark">Settings</h1>

@include('errors.errors-forms')
@if (\Session::has('success'))
<div class="mt-2 alert alert-success">
    {!! \Session::get('success') !!}
</div>
@endif

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
    <div class="card card-primary card-outline card-outline-tabs">
      <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="general-settings-tab" data-toggle="pill" href="#general-settings" role="tab"
              aria-controls="general-settings" aria-selected="true">General</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="push-notification-settings-tab" data-toggle="pill"
              href="#push-notification-settings" role="tab" aria-controls="push-notification-settings"
              aria-selected="false">Push Notifications</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="sms-gateway-settings-tab" data-toggle="pill" href="#sms-gateway-settings" role="tab"
              aria-controls="sms-gateway-settings" aria-selected="false">SMS Gateway</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="google-map-settings-tab" data-toggle="pill" href="#google-map-settings" role="tab"
              aria-controls="google-map-settings" aria-selected="false">Google Maps</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="payment-gateway-settings-tab" data-toggle="pill" href="#payment-gateway-settings"
              role="tab" aria-controls="payment-gateway-settings" aria-selected="false">Payment Gateway</a>
          </li>
        </ul>
      </div>
      <div class="card-body p-2">
        <div class="tab-content" id="custom-tabs-four-tabContent">
          <div class="tab-pane fade show active" id="general-settings" role="tabpanel"
            aria-labelledby="general-settings-tab">
            @include('admin.settings.general', ['settings' => $settings])
          </div>
          <div class="tab-pane fade" id="push-notification-settings" role="tabpanel"
            aria-labelledby="push-notification-settings-tab">
            @include('admin.settings.push_notification', ['settings' => $settings])
          </div>
          <div class="tab-pane fade" id="sms-gateway-settings" role="tabpanel"
            aria-labelledby="sms-gateway-settings-tab">
            @include('admin.settings.sms_gateway', ['settings' => $settings])
          </div>
          <div class="tab-pane fade" id="google-map-settings" role="tabpanel" aria-labelledby="google-map-settings-tab">
            @include('admin.settings.google_map', ['settings' => $settings])
          </div>
          <div class="tab-pane fade" id="payment-gateway-settings" role="tabpanel"
            aria-labelledby="payment-gateway-settings-tab">
            @include('admin.settings.payment_gateway', ['settings' => $settings])
          </div>
        </div>
      </div>
      <!-- /.card -->
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