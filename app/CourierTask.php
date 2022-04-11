<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;


class CourierTask extends Model
{   
    protected $table = 'couriers_tasks';
    protected $fillable = ['worksheet_id','eng_worksheet_id','draft_id', 'eng_draft_id', 'direction', 'site_name', 'status', 'parcels_qty', 'comments_1', 'comments_2', 'shipper_name', 'shipper_country','shipper_city','shipper_address','standard_phone','courier','pick_up_date_comments','shipper_region','order_number'];


    /**
    * Import couriers tasks.
    */
    public function importWorksheet()
    {
        $worksheet = NewWorksheet::where('status','Забрать')
        ->orWhere('status','Коробка')->get();
        $worksheet_eng = PhilIndWorksheet::where('status','Pick up')
        ->orWhere('status','Box')->get();
        $draft = CourierDraftWorksheet::where('status','Забрать')
        ->orWhere('status','Коробка')->get();
        $draft_eng = CourierEngDraftWorksheet::where('status','Pick up')
        ->orWhere('status','Box')->get();

        foreach ($worksheet as $row) {
            $new_task = new CourierTask();
            $new_task->worksheet_id = $row->id;
            $new_task->direction = $row->direction;
            $new_task->site_name = $row->site_name;
            $new_task->status = $row->status;
            $new_task->parcels_qty = 1;
            $new_task->order_number = $row->order_number;
            $new_task->comments_1 = $row->comment_2;
            $new_task->comments_2 = $row->comments;
            $new_task->shipper_name = $row->sender_name;
            $new_task->shipper_country = $row->sender_country;
            $new_task->shipper_region = $row->shipper_region;
            $new_task->shipper_city = $row->sender_city;
            $new_task->shipper_address = $row->sender_address;
            $new_task->standard_phone = $row->standard_phone;
            $new_task->courier = $row->courier;
            $new_task->pick_up_date_comments = $row->pick_up_date;
            $new_task->save();
        }
        foreach ($worksheet_eng as $row) {
            $new_task = new CourierTask();
            $new_task->eng_worksheet_id = $row->id;
            $new_task->direction = $row->direction;
            $new_task->site_name = 'ORE';
            $new_task->status = $row->status;
            $new_task->parcels_qty = 1;
            $new_task->order_number = $row->order_number;
            $new_task->comments_1 = $row->comments_1;
            $new_task->comments_2 = $row->comments_2;
            $new_task->shipper_name = $row->shipper_name;
            $new_task->shipper_country = $row->shipper_country;
            $new_task->shipper_region = $row->shipper_region;
            $new_task->shipper_city = $row->shipper_city;
            $new_task->shipper_address = $row->shipper_address;
            $new_task->standard_phone = $row->standard_phone;
            $new_task->courier = $row->courier;
            $new_task->pick_up_date_comments = $row->delivery_date_comments;
            $new_task->save();
        }
        foreach ($draft as $row) {
            $new_task = new CourierTask();
            $new_task->draft_id = $row->id;
            $new_task->direction = $row->direction;
            $new_task->site_name = $row->site_name;
            $new_task->status = $row->status;
            $new_task->parcels_qty = $row->parcels_qty;
            $new_task->order_number = $row->order_number;
            $new_task->comments_1 = $row->comment_2;
            $new_task->comments_2 = $row->comments;
            $new_task->shipper_name = $row->sender_name;
            $new_task->shipper_country = $row->sender_country;
            $new_task->shipper_region = $row->shipper_region;
            $new_task->shipper_city = $row->sender_city;
            $new_task->shipper_address = $row->sender_address;
            $new_task->standard_phone = $row->standard_phone;
            $new_task->courier = $row->courier;
            $new_task->pick_up_date_comments = $row->pick_up_date;
            $new_task->save();
        }
        foreach ($draft_eng as $row) {
            $new_task = new CourierTask();
            $new_task->eng_draft_id = $row->id;
            $new_task->direction = $row->direction;
            $new_task->site_name = 'ORE';
            $new_task->status = $row->status;
            $new_task->parcels_qty = $row->parcels_qty;
            $new_task->order_number = $row->order_number;
            $new_task->comments_1 = $row->comments_1;
            $new_task->comments_2 = $row->comments_2;
            $new_task->shipper_name = $row->shipper_name;
            $new_task->shipper_country = $row->shipper_country;
            $new_task->shipper_region = $row->shipper_region;
            $new_task->shipper_city = $row->shipper_city;
            $new_task->shipper_address = $row->shipper_address;
            $new_task->standard_phone = $row->standard_phone;
            $new_task->courier = $row->courier;
            $new_task->pick_up_date_comments = $row->delivery_date_comments;
            $new_task->save();
        }
        
        return 'importWorksheet';
    }

    
    /**
    * Get the worksheet record that owns the courier task.
    */
    public function worksheet()
    {
        return $this->belongsTo('App\NewWorksheet','worksheet_id');
    }

    
    /**
    * Get the eng worksheet record that owns the courier task.
    */
    public function worksheetEng()
    {
        return $this->belongsTo('App\PhilIndWorksheet','eng_worksheet_id');
    }


    /**
    * Get the draft record that owns the courier task.
    */
    public function draft()
    {
        return $this->belongsTo('App\CourierDraftWorksheet','draft_id');
    }

    
    /**
    * Get the eng draft record that owns the courier task.
    */
    public function draftEng()
    {
        return $this->belongsTo('App\CourierEngDraftWorksheet','eng_draft_id');
    }


    /**
    * Update the courier task.
    */
    public function taskDone()
    {
        $status = $this->status;
        $this_worksheet = null;

        if ($this->worksheet) {
            $this_worksheet = $this->worksheet;
        }
        elseif ($this->worksheetEng) {
            $this_worksheet = $this->worksheetEng;
        }
        elseif ($this->draft) {
            $this_worksheet = $this->draft;
        }
        elseif ($this->draftEng) {
            $this_worksheet = $this->draftEng;
        }

        if ($this_worksheet) {
            switch($status) {
                case 'Коробка';
                    $this_worksheet->status = 'Подготовка';
                    break;
                case 'Box';
                    $this_worksheet->status = 'Pending';
                    break;
                case 'Забрать';
                    $this_worksheet->status = 'На складе в стране отправителя';
                    $this_worksheet->status_en = 'At the warehouse in the sender country';
                    $this_worksheet->status_he = "במחסן במדינת השולח";
                    $this_worksheet->status_ua = 'На складі в країні відправника';
                    break;
                case 'Pick up';
                    $this_worksheet->status = 'At the warehouse in the sender country';
                    $this_worksheet->status_ru = 'На складе в стране отправителя';
                    $this_worksheet->status_he = "במחסן במדינת השולח";
                    break;       
                default:
                    break;
            } 
            $this_worksheet->save();
            $this->delete();
        }                        

        return true;
    }
}
