<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingEng extends Model
{
    protected $table = 'trackings_eng';
    protected $fillable = [
        'tracking_main'
    ];
}
