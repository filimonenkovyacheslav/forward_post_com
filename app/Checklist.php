<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    public $table = 'checklist';
    protected $fillable = [
        'tracking_main', 'value'
    ];

    public static function getAttr()
    {
        return [
            'tracking_main', 'value'
        ];
    }
}
