<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Dish extends Model
{
    public function dish_category()
    {
        return $this->belongsTo('App\DishCategory');
    }

    public function restaurant()
    {
        return $this->belongsTo('App\Restaurant');
    }

    public function addons_dish()
    {
        return $this->hasMany('App\AddonDish');
    }
    
    public function addons_category()
    {
        return $this->belongsTo('App\AddonsCategory');
    }

    public function getImageAttribute($value)
    {
        return Storage::url(config('path.dishes'). $value);
    }

    // public function getPriceAttribute($value)
    // {
    //     return '$'. round($value, 2);
    // }

    // public function amount()
    // {
    //     return substr($this->price, 1);
    // }

    public function amount()
    {
        return $this->price;
    }
}
