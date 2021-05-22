{{-- {{dd($settings)}} --}}
<div class="row">
    <div class="col-12">
        <form role="form" id="createForm" method="POST" action="{{url('/admin/settings/update')}}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">

                <x-form-elements name="verification_required" type="switch"
                    label="OTP Verficiation on Registration"
                    value="{{old('verification_required',$settings->verification_required)}}" />

                <x-form-elements name="twilio_sid" type="text" label="Twilio SID"
                    value="{{old('twilio_sid',$settings->twilio_sid)}}" />

                <x-form-elements name="twilio_auth_token" type="text" label="Twilio Access Token"
                    value="{{old('twilio_auth_token',$settings->twilio_auth_token)}}" />

                <x-form-elements name="twilio_number" type="text" label="Twilio Service ID"
                    value="{{old('twilio_number',$settings->twilio_number)}}" />

                <input type="hidden" value="sms_gateway" name="setting_tab" />

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <!-- ./col -->
</div>