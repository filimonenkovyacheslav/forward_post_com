<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoice';
    protected $fillable = ['number', 'tracking', 'box', 'shipper_name', 'shipper_address_phone', 'consignee_name', 'consignee_address', 'shipped_items', 'weight', 'height', 'length', 'width', 'declared_value', 'work_sheet_id','batch_number','in_trash'];
}
