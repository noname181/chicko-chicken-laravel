<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use Rinvex\Addresses\Traits\Addressable;

class Restaurant extends Model
{
    use Addressable;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function dishes()
    {
        return $this->hasMany('App\Dish');
    }

    public function getImageAttribute($value)
    {
        return Storage::url(config('path.restaurant'). $value);
    }

    public function getForTwoAttribute($value)
    {
        return setting('currency_symbol'). $value;
    }

    public function getPriceRangeAttribute()
    {
        return substr($this->for_two, 1);
    }
}
