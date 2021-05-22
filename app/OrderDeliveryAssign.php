<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDeliveryAssign extends Model
{
    public $timestamps = false;

    protected $fillable = ['order_id'];    

    public function order()
    {
        return $this->belongsTo('App\Order');
    }
}
