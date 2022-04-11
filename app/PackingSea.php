<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackingSea extends Model
{
    protected $table = 'packing_sea';
    protected $fillable = ['track_code', 'work_sheet_id', 'attachment_number', 'attachment_name', 'amount_3','in_trash'];
}
