<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackingEngNew extends Model
{
    protected $table = 'packing_eng_new';
    protected $fillable = ['tracking', 'country', 'shipper_name', 'shipper_address', 'shipper_phone', 'shipper_id', 'consignee_name', 'consignee_address', 'consignee_phone', 'consignee_id', 'length', 'width', 'height', 'weight', 'items', 'shipment_val', 'work_sheet_id', 'lot','in_trash'];
}
