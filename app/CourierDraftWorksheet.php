<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CourierTask;
use App\UpdatesArchive;
use App\SignedDocument;
use App\BaseModel;


class CourierDraftWorksheet extends BaseModel
{   
    public $table = 'courier_draft_worksheet';
    protected $fillable = ['order_number', 'tracking_main', 'status', 'status_en', 'status_he', 'status_ua', 'update_status_date', 'date', 'standard_phone', 'site_name', 'package_content', 'direction', 'tariff', 'comment_2','comments','sender_name','sender_country','sender_city','sender_postcode','sender_address','sender_phone','sender_passport','recipient_name','recipient_country','region','district','recipient_city','recipient_postcode','recipient_street','recipient_house','body','recipient_room','recipient_phone','recipient_passport','recipient_email','courier','pick_up_date','weight','width','height','length','volume_weight','quantity_things','status_date','parcels_qty','in_trash','shipper_region','order_date'];


    /**
    * Get the courier task associated with the draft.
    */
    public function courierTask()
    {
        return $this->hasOne('App\CourierTask','draft_id');
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
            $new_task->draft_id = $this->id;
            $new_task->direction = $this->direction;
            $new_task->site_name = $this->site_name;
            $new_task->status = $this->status;
            $new_task->parcels_qty = $this->parcels_qty;
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
            $this->courierTask->parcels_qty = $this->parcels_qty;
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
    * Get the updates archive associated with the draft.
    */
    public function updatesArchive()
    {
        return $this->hasOne('App\UpdatesArchive','draft_id');
    }


    /**
    * Get the signed documents associated with the draft.
    */
    public function signedDocuments()
    {
        return $this->hasMany('App\SignedDocument','draft_id');
    }


    public function getLastDoc()
    {
        $documents = $this->signedDocuments;
        if ($documents->count() > 1) {
            foreach ($documents as $document) {
                if ($document->id == $documents->max('id')) {
                    return $document;
                }
            }
        }
        elseif($documents->count() == 1){
            foreach ($documents as $document) {
                if ($document->first_file) {
                    return $document;
                }
            }
        }
        else return null;
    }


    public function getLastDocUniq()
    {
        $document = $this->getLastDoc();
        if ($document) return $document->uniq_id;
        else return null;
    }
}
