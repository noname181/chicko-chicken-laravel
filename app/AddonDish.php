<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddonDish extends Model
{
    protected $fillable = ['addons_category_id', 'dish_id'];
    //

    public function addons_category()
    {
        return $this->belongsTo('App\AddonsCategory');
    }
}
