<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $address = config('settings.MAIL_FROM_ADDRESS');
        $subject = 'Password Reset';
        $name = setting('app_name');

        return $this->markdown('email.reset-password')
                    ->from($address, $name)
                    ->replyTo($address, $name)
                    ->subject($subject)
                    ->with([ 'new_password' => $this->data['password'] ]);
    }
    // public function build()
    // {
    //     return $this->markdown('email.reset-password');
    // }
}
