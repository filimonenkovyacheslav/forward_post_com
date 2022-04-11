<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiptArchive extends Model
{
    protected $table = 'receipts_archive';
    protected $fillable = ['receipt_id', 'worksheet_id', 'which_admin','receipt_number','tracking_main','description','update_date','status','comment','in_trash'];
}
