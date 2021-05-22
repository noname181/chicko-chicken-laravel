{{-- {{dd($settings)}} --}}
<div class="row">
    <div class="col-12">
        <form role="form" id="createForm" method="POST" action="{{url('/admin/settings/update')}}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                
                <x-form-elements name="google_map_api_key" type="text" label="Google API Key"
                    value="{{old('google_map_api_key',$settings->google_map_api_key)}}"  />

                <input type="hidden" value="google_map" name="setting_tab" />

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <!-- ./col -->
</div>