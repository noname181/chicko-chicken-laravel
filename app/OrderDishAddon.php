<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDishAddon extends Model
{
    protected $table = 'order_dishes_addons';

    public function order_dish()
    {
        return $this->belongsTo('App\OrderDish');
    }
}