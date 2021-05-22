<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $casts = [
        'created_at' => 'datetime:d M Y',
    ];

    public function order_dishes()
    {
        return $this->hasMany('App\OrderDish');
    }

    public function order_delivery_assign()
    {
        return $this->hasOne('App\OrderDeliveryAssign');
    }

    public function restaurant()
    {
        return $this->belongsTo('App\Restaurant');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
