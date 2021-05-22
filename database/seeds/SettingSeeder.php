<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Setting::set('app_name', 'Foodlicious');
        Setting::set('app_timezone', 'America/New_York');

        Setting::set('currency_symbol', '$');
        Setting::set('currency_code', 'USD');
        
        Setting::set('fcm_app_id', '');
        Setting::set('fcm_project_id', '');
        Setting::set('fcm_sender_id', '');
        Setting::set('fcm_web_certificate', '');
        Setting::set('fcm_web_api_key', '');
        Setting::set('fcm_server_key', '');
        Setting::set('fcm_active', true);
        
        Setting::set('pg_cod_active', true);
        Setting::set('pg_paypal_active', true);
        Setting::set('pg_razorpay_active', true);

        Setting::set('pg_paypal_key', '');
        Setting::set('pg_razorpay_client_key', '');
        Setting::set('pg_razorpay_secret_key', '');

        Setting::set('twilio_sid', '');
        Setting::set('twilio_auth_token', '');
        Setting::set('twilio_number', '');
        Setting::set('default_sms_gateway', 2);
        
        Setting::set('google_map_api_key', '');

        Setting::set('delivery_radius', '5');

        Setting::set('tax_applicable', true);
        Setting::set('tax_percentage', 5);

        Setting::set('delivery_charge_applicable', true);
        Setting::set('delivery_charge', 40);

        Setting::set('verification_required', false);
        Setting::save();
    }
}
