<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use App\CourierTask;
use App\UpdatesArchive;
use App\BaseModel;
use App\SignedDocument;
use App\CourierDraftWorksheet;
use App\PackingSea;
use App\NewPacking;


class NewWorksheet extends BaseModel
{   
    public $table = 'new_worksheet';
    protected $fillable = ['site_name','direction','order_number', 'tracking_main', 'status', 'status_date', 'status_en', 'status_he', 'status_ua', 'update_status_date','tariff','partner','tracking_local','tracking_transit','pallet_number','comment_2','comments','sender_name','sender_country','sender_city','sender_postcode','sender_address','sender_phone','sender_passport','recipient_name','recipient_country','region','district','recipient_city','recipient_postcode','recipient_street','recipient_house','body','recipient_room','recipient_phone','recipient_passport','recipient_email','package_cost','courier','pick_up_date','weight','width','height','length','volume_weight','quantity_things','batch_number','pay_date','pay_sum','background','in_trash','shipper_region','order_date','parcels_qty','packing_number','index_number'];   


    /**
    * Get the courier task associated with the worksheet.
    */
    public function courierTask()
    {
        return $this->hasOne('App\CourierTask','worksheet_id');
    }

    /**
    * Get the new packing lists associated with the worksheet.
    */
    public function newPackinglists()
    {
        return $this->hasMany('App\NewPacking','work_sheet_id');
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
            $new_task->packing_num = $this->getLastDocUniq();
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
            $new_task->weight = $this->weight;
            $new_task->shipped_items = $this->package_content;
            $new_task->save();
            $result = $new_task;
        }
        elseif ($this->courierTask) {
            $this->courierTask->direction = $this->direction;
            $this->courierTask->site_name = $this->site_name;
            $this->courierTask->status = $this->status;
            $this->courierTask->parcels_qty = 1;
            $this->courierTask->order_number = $this->order_number;
            $this->courierTask->packing_num = $this->getLastDocUniq();
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
            $this->courierTask->weight = $this->weight;
            $this->courierTask->shipped_items = $this->package_content;
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
    

    /**
    * Get the signed documents associated with the worksheet.
    */
    public function signedDocuments()
    {
        return $this->hasMany('App\SignedDocument','worksheet_id');
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


    public function deactivateWorksheet()
    {
        $fields = Schema::getColumnListing('courier_draft_worksheet'); 
        $draft = new CourierDraftWorksheet();         

        foreach($fields as $field){
            if ($field !== 'created_at' && $field !== 'id'){
                $draft->$field = $this->$field;
            }           
        }
        $draft->in_trash = false;       
        $draft->setIndexNumber();            

        if ($draft->save())
        {           
            $work_sheet_id = $draft->id;       
            $draft = CourierDraftWorksheet::find($work_sheet_id);

            // Packing
            $fields_packing = ['payer', 'contract', 'type', 'track_code', 'full_shipper', 'full_consignee', 'country_code', 'postcode', 'region', 'district', 'city', 'street', 'house', 'body', 'room', 'phone', 'tariff', 'tariff_cent', 'weight_kg', 'weight_g', 'service_code', 'amount_1', 'amount_2', 'attachment_number', 'attachment_name', 'amount_3', 'weight_enclosures_kg', 'weight_enclosures_g', 'value_euro', 'value_cent', 'work_sheet_id'];
            
            $j=1;
            $items = explode(";", $this->package_content);          
            if (count($items)) {
                $temp = '';
                for ($i=0; $i < count($items); $i++) {  

                    if (strripos($items[$i], '-') !== false) {
                        $temp = explode("-", $items[$i]);
                        $content = trim($temp[0]);
                        $quantity = trim($temp[1]);
                    }
                    elseif (strripos($items[$i], ':') !== false) {
                        $temp = explode(":", $items[$i]);
                        $content = trim($temp[0]);
                        $quantity = trim($temp[1]);
                    }

                    if ($items[$i]) {
                        $packing_sea = new PackingSea();
                        foreach($fields_packing as $field){
                            if ($field === 'type') {
                                $packing_sea->$field = $this->tariff;
                            }
                            else if ($field === 'full_shipper') {
                                $packing_sea->$field = $this->sender_name;
                            }
                            else if ($field === 'full_consignee') {
                                $packing_sea->$field = $this->recipient__name;
                            }
                            else if ($field === 'country_code') {
                                $packing_sea->$field = $this->recipient_country;
                            }
                            else if ($field === 'postcode') {
                                $packing_sea->$field = $this->recipient_postcode;
                            }
                            else if ($field === 'city') {
                                $packing_sea->$field = $this->recipient_city;
                            }
                            else if ($field === 'street') {
                                $packing_sea->$field = $this->recipient_street;
                            }
                            else if ($field === 'house') {
                                $packing_sea->$field = $this->recipient_house;
                            }
                            else if ($field === 'room') {
                                $packing_sea->$field = $this->recipient_room;
                            }
                            else if ($field === 'phone') {
                                $packing_sea->$field = $this->recipient_phone;
                            }
                            else if ($field === 'tariff') {
                                $packing_sea->$field = null;
                            }
                            else if ($field === 'work_sheet_id') {
                                $packing_sea->$field = $work_sheet_id;
                            }
                            else if ($field === 'attachment_number') {
                                $packing_sea->$field = $j;
                            }
                            else if ($field === 'attachment_name') {
                                $packing_sea->$field = $content;
                            }
                            else if ($field === 'amount_3') {
                                $packing_sea->$field = $quantity;
                            }
                            else if ($field === 'track_code') {
                                $packing_sea->$field = $this->tracking_main;
                            }
                            else{
                                $packing_sea->$field = $this->$field;
                            }
                        }
                        $j++;
                        $packing_sea->save(); 
                    }                      
                }
            }
        }

        if ($draft) {
            return $draft;
        }
        else return null;
    }


    public function setIndexNumber()
    {
        $max = 0;
        if(!$this->index_number){
            $max = NewWorksheet::max('index_number');
            $max++;
            $this->index_number = $max;
            $this->save();
        }
        return $max;
    } 


    public function reIndex($index, $courier = false)
    {
        $number = 1;       
        $old = NewWorksheet::where('index_number', $index)->first();
        if (!$courier) {
            if ($old) {
                if ($this->index_number < $index) {
                    $old->index_number = $index-1;
                }
                elseif ($this->index_number > $index) {
                    $old->index_number = $index+1;
                }
                elseif ($this->index_number === $index) {
                    return false;
                }
                $old->save();
            } 
        }
        elseif ($old){
            $old->index_number = $index+1;
            $old->save();
        }

        $this->index_number = $index;
        $this->save();        
        $worksheets = NewWorksheet::orderBy('index_number')->get();
        
        foreach ($worksheets as $item) {
            if ($item->index_number !== $index) {
                $item->index_number = $number;
                $item->save();               
            } 
            $number++;
        }
    }
    
}
