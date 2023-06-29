<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\NewWorksheet;

class NewPacking extends Model
{
    protected $table = 'new_packing';
    protected $fillable = ['payer', 'contract', 'type', 'track_code', 'full_shipper', 'full_consignee', 'country_code', 'postcode', 'region', 'district', 'city', 'street', 'house', 'body', 'room', 'phone', 'tariff', 'tariff_cent', 'weight_kg', 'weight_g', 'service_code', 'amount_1', 'amount_2', 'attachment_number', 'attachment_name', 'amount_3', 'weight_enclosures_kg', 'weight_enclosures_g', 'value_euro', 'value_cent', 'work_sheet_id','batch_number','in_trash'];



    /**
    * Get the worksheet record that owns the packing list.
    */
    public function worksheet()
    {
        return $this->belongsTo('App\NewWorksheet','work_sheet_id');
    }
}



