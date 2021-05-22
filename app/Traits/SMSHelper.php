<?php

namespace App\Traits;

use Illuminate\Http\Request;

use App\SmsOtp;
use App\User;
use Twilio\Rest\Client;

use Setting;

trait SMSHelper
{
    public function processSMS($action, $phone, $message = null, $user = null)
    {
        // Selects Default Gateway
        $gateway = Setting::get('default_sms_gateway');

        // switch ($gateway) {

        //     case '1':
        //         $response = $this->msg91($action, $phone, $message, $user);
        //         break;

        //     case '2':
                $response = $this->twilio($action, $phone, $message, $user);
        //         break;

        // }
        return $response;
    }

    /**
     * @param $action
     * @param $phone
     * @param $otp
     * @param $message
     * @return mixed
     */
    private function msg91($action, $phone, $message, $user)
    {
        $authkey = config('settings.msg91AuthKey');
        $sender_id = config('settings.msg91SenderId');

        if ($action === 'OTP') {
            $otp = rand(111111, 999999);
            $message = 'Your Verification code is: ' . $otp;

            $user->verification_code = $otp;
            $user->save();
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.msg91.com/api/v2/sendsms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "{ \"sender\": \"$sender_id\", \"route\": \"4\", \"sms\": [ { \"message\": \"$message\", \"to\": [ \"$phone\" ] } ] }",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "authkey: $authkey",
                'content-type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $action
     * @param $phone
     * @param $otp
     * @param $message
     * @return mixed
     */
    private function twilio($action, $phone, $message, $user)
    {
        $account_sid = Setting::get('twilio_sid');
        $auth_token = Setting::get('twilio_auth_token');
        $twilio_number = Setting::get('twilio_number');

        if ($action === 'OTP') {
            $otp = rand(111111, 999999);
            $message = 'Your Verification code is: ' . $otp;

            $user->verification_code = $otp;
            $user->save();
        }

        $twilio = new Client($account_sid, $auth_token);

        try {
            $twilio->messages->create(
                $phone,
                array(
                    'From' => $twilio_number,
                    'body' => $message,
                )
            );
            return true;
        } catch (Exception $e) {
        throw new Exception($e);
        }
    }
}
