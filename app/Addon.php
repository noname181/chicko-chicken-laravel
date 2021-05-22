<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    public function addons_category()
    {
        return $this->belongsTo('App\AddonsCategory');
    }
}
