<?php

namespace App\Traits;

use Illuminate\Http\Request;

use Setting;

trait Utility
{
    public function sendFCM($message, $ids, $title = '', $multiple = false)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        if ($multiple) {
            $fields = array(
                'registration_ids' => $ids,
                'data' => array(
                        "message" => $message,
                        "title" => $title,
                        // "icon" => "myicon"
                )
            );
        } else {
            $fields = array(
                'to' => $ids,
                'data' => array(
                        "message" => $message,
                        "title" => Setting::get('app_name'),
                        // "icon" => "myicon"
                )
        );
        }
        $fields = json_encode($fields);
        $headers = array(
                'Authorization: key=' . Setting::get('fcm_server_key'),
                'Content-Type: application/json'
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function readable_random_string($length = 6)
    {  
        $string     = '';
        $vowels     = array("a","e","i","o","u");  
        $consonants = array(
            'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 
            'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'
        );  

        // Seed it
        srand((double) microtime() * 1000000);

        $max = $length/2;
        for ($i = 1; $i <= $max; $i++)
        {
            $string .= $consonants[rand(0,19)];
            $string .= $vowels[rand(0,4)];
        }

        return $string;
    }
}
