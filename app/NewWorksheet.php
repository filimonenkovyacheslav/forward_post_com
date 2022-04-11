<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CourierTask;
use App\UpdatesArchive;


class NewWorksheet extends BaseModel
{   
    public $table = 'new_worksheet';
    protected $fillable = ['site_name','direction','order_number', 'tracking_main', 'status', 'status_date', 'status_en', 'status_he', 'status_ua', 'update_status_date','tariff','partner','tracking_local','tracking_transit','pallet_number','comment_2','comments','sender_name','sender_country','sender_city','sender_postcode','sender_address','sender_phone','sender_passport','recipient_name','recipient_country','region','district','recipient_city','recipient_postcode','recipient_street','recipient_house','body','recipient_room','recipient_phone','recipient_passport','recipient_email','package_cost','courier','pick_up_date','weight','width','height','length','volume_weight','quantity_things','batch_number','pay_date','pay_sum','background','in_trash','shipper_region'];   


    /**
    * Get the courier task associated with the worksheet.
    */
    public function courierTask()
    {
        return $this->hasOne('App\CourierTask','worksheet_id');
    }


    /**
    * Check the courier task.
    */
    public function checkCourierTask($status)
    {
        $result = static::isNecessaryCourierTask($status,$this->table);
        if (!$result) {
            if ($this->courierTask) $this->courierTask->delete();            
        }
        elseif(!$this->courierTask){           
            $new_task = new CourierTask();
            $new_task->worksheet_id = $this->id;
            $new_task->direction = $this->direction;
            $new_task->site_name = $this->site_name;
            $new_task->status = $this->status;
            $new_task->parcels_qty = 1;
            $new_task->order_number = $this->order_number;
            $new_task->comments_1 = $this->comment_2;
            $new_task->comments_2 = $this->comments;
            $new_task->shipper_name = $this->sender_name;
            $new_task->shipper_country = $this->sender_country;
            $new_task->shipper_region = $this->shipper_region;
            $new_task->shipper_city = $this->sender_city;
            $new_task->shipper_address = $this->sender_address;
            $new_task->standard_phone = $this->standard_phone;
            $new_task->courier = $this->courier;
            $new_task->pick_up_date_comments = $this->pick_up_date;
            $new_task->save();
            $result = $new_task;
        }
        elseif ($this->courierTask) {
            $this->courierTask->direction = $this->direction;
            $this->courierTask->site_name = $this->site_name;
            $this->courierTask->status = $this->status;
            $this->courierTask->parcels_qty = 1;
            $this->courierTask->order_number = $this->order_number;
            $this->courierTask->comments_1 = $this->comment_2;
            $this->courierTask->comments_2 = $this->comments;
            $this->courierTask->shipper_name = $this->sender_name;
            $this->courierTask->shipper_country = $this->sender_country;
            $this->courierTask->shipper_region = $this->shipper_region;
            $this->courierTask->shipper_city = $this->sender_city;
            $this->courierTask->shipper_address = $this->sender_address;
            $this->courierTask->standard_phone = $this->standard_phone;
            $this->courierTask->courier = $this->courier;
            $this->courierTask->pick_up_date_comments = $this->pick_up_date;
            $this->courierTask->save();
            $result = $this->courierTask;
        }
        return $result;
    }
    

    /**
    * Get the updates archive associated with the worksheet.
    */
    public function updatesArchive()
    {
        return $this->hasOne('App\UpdatesArchive','worksheet_id');
    }
    
}
