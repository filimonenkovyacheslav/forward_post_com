<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $table = 'receipts';
    protected $fillable = ['legal_entity', 'receipt_number', 'range_number', 'sum', 'date', 'tracking_main', 'courier_name','comment','double'];
}
