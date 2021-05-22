{{-- {{dd($settings)}} --}}
<div class="row">
    <div class="col-12">
        <form role="form" id="createForm" method="POST" action="{{url('/admin/settings/update')}}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">

                <x-form-elements name="pg_cod_active" type="switch" label="COD Active"
                    value="{{old('pg_cod_active',$settings->pg_cod_active)}}" />

                <hr />
                
                <x-form-elements name="pg_paypal_active" type="switch" label="Paypal Active"
                    value="{{old('pg_paypal_active',$settings->pg_paypal_active)}}" />

                <x-form-elements name="pg_paypal_key" type="text" label="Paypal API Key"
                    value="{{old('pg_paypal_key',$settings->pg_paypal_key)}}"  />

                <hr />
                <x-form-elements name="pg_razorpay_active" type="switch" label="Razorpay Active"
                    value="{{old('pg_razorpay_active',$settings->pg_razorpay_active)}}" />

                <x-form-elements name="pg_razorpay_client_key" type="text" label="Razorpay Client Key"
                    value="{{old('pg_razorpay_client_key',$settings->pg_razorpay_client_key)}}"  />

                <x-form-elements name="pg_razorpay_secret_key" type="text" label="Razorpay Secret Key"
                    value="{{old('pg_razorpay_secret_key',$settings->pg_razorpay_secret_key)}}"  />

                <input type="hidden" value="payment_gateway" name="setting_tab" />

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <!-- ./col -->
</div>