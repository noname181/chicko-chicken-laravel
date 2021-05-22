@component('mail::message')
# Reset Password

Your new password is: <b>{{ $new_password }}</b>

{{-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
