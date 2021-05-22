<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'message', 'user_id',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::create($value)->diffForHumans();
    }
}
