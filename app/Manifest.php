<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manifest extends Model
{
    protected $table = 'manifest';
    protected $fillable = ['number', 'tracking', 'sender_country', 'sender_name', 'recipient_name', 'recipient_city', 'recipient_address', 'content', 'quantity', 'weight', 'cost', 'work_sheet_id','batch_number','in_trash'];
}
