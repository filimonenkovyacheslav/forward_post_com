<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingList extends Model
{
    protected $table = 'tracking_lists';
    protected $fillable = [
        'tracking','list_name'
    ];
}
