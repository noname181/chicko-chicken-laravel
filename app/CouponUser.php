<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CouponUser extends Model
{
    //
    public function coupon()
    {
        return $this->belongsTo('App\Coupon');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}