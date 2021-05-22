<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddonsCategory extends Model
{
    //

    public function addons()
    {
        return $this->hasMany('App\Addon');
    }
}
