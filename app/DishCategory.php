<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DishCategory extends Model
{
    public function dishes()
    {
        return $this->hasMany('App\Dish');
    }


    public function getImageAttribute($value)
    {
        return Storage::url(config('path.categories'). $value);
    }
}
