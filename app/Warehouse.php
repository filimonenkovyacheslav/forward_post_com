<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = 'warehouse';
    protected $fillable = ['tracking_numbers', 'pallet', 'cell', 'arrived', 'left', 'lot', 'notifications', 'which_admin'];
}
