<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackingEng extends Model
{
    protected $table = 'packing_eng';
    protected $fillable = ['tracking', 'country', 'shipper_name', 'shipper_address', 'shipper_phone', 'shipper_id', 'consignee_name', 'consignee_address', 'consignee_phone', 'consignee_id', 'length', 'width', 'height', 'weight', 'items', 'shipment_val', 'work_sheet_id','in_trash'];
}
