<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDish extends Model
{
    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function order_adddons()
    {
        return $this->hasMany('App\OrderDishAddon','order_dishes_id');
    }
}
