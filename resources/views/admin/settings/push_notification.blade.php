{{-- {{dd($settings)}} --}}
<div class="row">
    <div class="col-12">
        <form role="form" id="createForm" method="POST" action="{{url('/admin/settings/update')}}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">

                <x-form-elements name="fcm_project_id" type="text" label="FCM Project ID"
                    value="{{old('fcm_project_id',$settings->fcm_project_id)}}" />

                <x-form-elements name="fcm_app_id" type="text" label="FCM App Id"
                    value="{{old('fcm_app_id',$settings->fcm_app_id)}}" />

                <x-form-elements name="fcm_sender_id" type="text" label="FCM Sender ID"
                    value="{{old('fcm_sender_id',$settings->fcm_sender_id)}}" />

                <x-form-elements name="fcm_web_certificate" type="text" label="FCM Web Certificate"
                    value="{{old('fcm_web_certificate',$settings->fcm_web_certificate)}}" />

                    <x-form-elements name="fcm_web_api_key" type="text" label="FCM Web API Key"
                        value="{{old('fcm_web_api_key',$settings->fcm_web_api_key)}}" />

                <x-form-elements name="fcm_server_key" type="text" label="FCM Server Key"
                    value="{{old('fcm_server_key',$settings->fcm_server_key)}}" />

                <x-form-elements name="fcm_active" type="switch" label="Active"
                    value="{{old('fcm_active',$settings->fcm_active)}}" />

                <input type="hidden" value="fcm" name="setting_tab" />

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <!-- ./col -->
</div>