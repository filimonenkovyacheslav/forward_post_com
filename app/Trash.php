<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use App\PackingSea;
use App\NewPacking;
use App\Invoice;
use App\Manifest;
use App\PackingEng;
use App\PackingEngNew;
use App\ReceiptArchive;
use App\CourierTask;


class Trash extends BaseModel
{   
    protected $table = 'trash';
    protected $fillable = ['tracking_main', 'status', 'status_date', 'status_ru', 'status_he', 'operator','date','direction','tracking_local','pallet_number','comments_1','comments_2','shipper_name', 'shipper_city','passport_number','return_date','shipper_address','standard_phone','shipper_phone','shipper_id','consignee_name','house_name','post_office','district','state_pincode','consignee_address','consignee_phone','consignee_id','shipped_items','shipment_val','courier','delivery_date_comments','weight','width','height','length','volume_weight','lot','payment_date_comments','amount_payment','status_date','shipper_country','consignee_country','parcels_qty','site_name','tariff','partner','region','body','recipient_street','quantity_things','status_ua','recipient_room','table_name','worksheet_id'];


    /**
    * Create trash.
    */
    public function createTrash($request)
    {
        $data = $request->all();
        $table = $data['table'];
        if (isset($data['action'])) {
            $id = $data['action'];
            $new_trash = new Trash();
            return $this->__createTrash($table, $new_trash, $id)->count();
        }
        elseif (isset($data['row_id'])) {
            $id_arr = $data['row_id'];
            for ($i=0; $i < count($id_arr); $i++) { 
                $new_trash = new Trash();
                $this->__createTrash($table, $new_trash, $id_arr[$i]);
            }
            return count($id_arr);
        }                                          
    }


    protected function __createTrash($table, $new_trash, $id)
    {
        switch($table) {
            case 'new_worksheet';
                $worksheet = NewWorksheet::find($id);
                $new_trash->table_name = $table;
                $new_trash->site_name = $worksheet->site_name;
                $new_trash->tariff = $worksheet->tariff;
                $new_trash->partner = $worksheet->partner;
                $new_trash->comments_1 = $worksheet->comment_2;
                $new_trash->comments_2 = $worksheet->comments;
                $new_trash->shipper_name = $worksheet->sender_name;
                $new_trash->shipper_country = $worksheet->sender_country;
                $new_trash->shipper_city = $worksheet->sender_city;
                $new_trash->passport_number = $worksheet->sender_passport;
                $new_trash->shipper_address = $worksheet->sender_address;
                $new_trash->shipper_phone = $worksheet->sender_phone;
                $new_trash->consignee_name = $worksheet->recipient_name;
                $new_trash->consignee_country = $worksheet->recipient_country;
                $new_trash->house_name = $worksheet->recipient_house;
                $new_trash->post_office = $worksheet->recipient_postcode;
                $new_trash->region = $worksheet->region;
                $new_trash->recipient_street = $worksheet->recipient_street;
                $new_trash->recipient_room = $worksheet->recipient_room;
                $new_trash->body = $worksheet->body;
                $new_trash->consignee_phone = $worksheet->recipient_phone;
                $new_trash->consignee_id = $worksheet->recipient_passport;
                $new_trash->shipped_items = $worksheet->package_content;
                $new_trash->shipment_val = $worksheet->package_cost;
                $new_trash->delivery_date_comments = $worksheet->pick_up_date;
                $new_trash->lot = $worksheet->batch_number;
                $new_trash->quantity_things = $worksheet->quantity_things;
                $new_trash->payment_date_comments = $worksheet->pay_date;
                $new_trash->amount_payment = $worksheet->pay_sum;
                $new_trash->status_ru = $worksheet->status_en;
                $new_trash->status_ua = $worksheet->status_ua;
                break;
            case 'phil_ind_worksheet';
                $worksheet = PhilIndWorksheet::find($id);
                $new_trash->table_name = $table;
                $new_trash->comments_1 = $worksheet->comments_1;
                $new_trash->comments_2 = $worksheet->comments_2;
                $new_trash->shipper_name = $worksheet->shipper_name;
                $new_trash->shipper_country = $worksheet->shipper_country;
                $new_trash->shipper_city = $worksheet->shipper_city;
                $new_trash->passport_number = $worksheet->passport_number;
                $new_trash->return_date = $worksheet->return_date;
                $new_trash->shipper_address = $worksheet->shipper_address;
                $new_trash->shipper_phone = $worksheet->shipper_phone;
                $new_trash->shipper_id = $worksheet->shipper_id;
                $new_trash->consignee_name = $worksheet->consignee_name;
                $new_trash->consignee_country = $worksheet->consignee_country;
                $new_trash->house_name = $worksheet->house_name;
                $new_trash->post_office = $worksheet->post_office;
                $new_trash->state_pincode = $worksheet->state_pincode;
                $new_trash->consignee_address = $worksheet->consignee_address;
                $new_trash->consignee_phone = $worksheet->consignee_phone;
                $new_trash->consignee_id = $worksheet->consignee_id;
                $new_trash->shipped_items = $worksheet->shipped_items;
                $new_trash->shipment_val = $worksheet->shipment_val;
                $new_trash->operator = $worksheet->operator;
                $new_trash->delivery_date_comments = $worksheet->delivery_date_comments;
                $new_trash->lot = $worksheet->lot;
                $new_trash->payment_date_comments = $worksheet->payment_date_comments;
                $new_trash->amount_payment = $worksheet->amount_payment;
                $new_trash->status_ru = $worksheet->status_ru;
                break;
            case 'courier_draft_worksheet';
                $worksheet = CourierDraftWorksheet::find($id);
                $new_trash->table_name = $table;
                $new_trash->parcels_qty = $worksheet->parcels_qty;
                $new_trash->site_name = $worksheet->site_name;
                $new_trash->tariff = $worksheet->tariff;
                $new_trash->partner = $worksheet->partner;
                $new_trash->comments_1 = $worksheet->comment_2;
                $new_trash->comments_2 = $worksheet->comments;
                $new_trash->shipper_name = $worksheet->sender_name;
                $new_trash->shipper_country = $worksheet->sender_country;
                $new_trash->shipper_city = $worksheet->sender_city;
                $new_trash->passport_number = $worksheet->sender_passport;
                $new_trash->shipper_address = $worksheet->sender_address;
                $new_trash->shipper_phone = $worksheet->sender_phone;
                $new_trash->consignee_name = $worksheet->recipient_name;
                $new_trash->consignee_country = $worksheet->recipient_country;
                $new_trash->house_name = $worksheet->recipient_house;
                $new_trash->post_office = $worksheet->recipient_postcode;
                $new_trash->region = $worksheet->region;
                $new_trash->recipient_street = $worksheet->recipient_street;
                $new_trash->recipient_room = $worksheet->recipient_room;
                $new_trash->body = $worksheet->body;
                $new_trash->consignee_phone = $worksheet->recipient_phone;
                $new_trash->consignee_id = $worksheet->recipient_passport;
                $new_trash->shipped_items = $worksheet->package_content;
                $new_trash->shipment_val = $worksheet->package_cost;
                $new_trash->delivery_date_comments = $worksheet->pick_up_date;
                $new_trash->lot = $worksheet->batch_number;
                $new_trash->quantity_things = $worksheet->quantity_things;
                $new_trash->payment_date_comments = $worksheet->pay_date;
                $new_trash->amount_payment = $worksheet->pay_sum;
                $new_trash->status_ru = $worksheet->status_en;
                $new_trash->status_ua = $worksheet->status_ua;
                break;
            case 'courier_eng_draft_worksheet';
                $worksheet = CourierEngDraftWorksheet::find($id);
                $new_trash->table_name = $table;
                $new_trash->parcels_qty = $worksheet->parcels_qty;
                $new_trash->comments_1 = $worksheet->comments_1;
                $new_trash->comments_2 = $worksheet->comments_2;
                $new_trash->shipper_name = $worksheet->shipper_name;
                $new_trash->shipper_country = $worksheet->shipper_country;
                $new_trash->shipper_city = $worksheet->shipper_city;
                $new_trash->passport_number = $worksheet->passport_number;
                $new_trash->return_date = $worksheet->return_date;
                $new_trash->shipper_address = $worksheet->shipper_address;
                $new_trash->shipper_phone = $worksheet->shipper_phone;
                $new_trash->shipper_id = $worksheet->shipper_id;
                $new_trash->consignee_name = $worksheet->consignee_name;
                $new_trash->consignee_country = $worksheet->consignee_country;
                $new_trash->house_name = $worksheet->house_name;
                $new_trash->post_office = $worksheet->post_office;
                $new_trash->state_pincode = $worksheet->state_pincode;
                $new_trash->consignee_address = $worksheet->consignee_address;
                $new_trash->consignee_phone = $worksheet->consignee_phone;
                $new_trash->consignee_id = $worksheet->consignee_id;
                $new_trash->shipped_items = $worksheet->shipped_items;
                $new_trash->shipment_val = $worksheet->shipment_val;
                $new_trash->operator = $worksheet->operator;
                $new_trash->delivery_date_comments = $worksheet->delivery_date_comments;
                $new_trash->lot = $worksheet->lot;
                $new_trash->payment_date_comments = $worksheet->payment_date_comments;
                $new_trash->amount_payment = $worksheet->amount_payment;
                $new_trash->status_ru = $worksheet->status_ru;
                break;       
            default:
                $new_trash->table = $table;
                break;
        } 

        $new_trash->worksheet_id = $id;
        $new_trash->date = $worksheet->date;
        $new_trash->direction = $worksheet->direction;
        $new_trash->status = $worksheet->status;
        $new_trash->status_date = $worksheet->status_date;
        $new_trash->tracking_main = $worksheet->tracking_main;
        $new_trash->tracking_local = $worksheet->tracking_local;
        $new_trash->pallet_number = $worksheet->pallet_number;
        $new_trash->standard_phone = $worksheet->standard_phone;
        $new_trash->district = $worksheet->district;
        $new_trash->courier = $worksheet->courier;
        $new_trash->weight = $worksheet->weight;
        $new_trash->width = $worksheet->width;
        $new_trash->height = $worksheet->height;
        $new_trash->length = $worksheet->length;
        $new_trash->volume_weight = $worksheet->volume_weight;
        $new_trash->status_he = $worksheet->status_he; 

        if ($new_trash->save()) $this->inTrashUpdate($worksheet,$table);
        
        return $new_trash;
    }


    protected function inTrashUpdate($worksheet,$table)
    {
        $id = $worksheet->id;
        $worksheet->update(['in_trash' => true]);        

        switch($table) {
            case 'new_worksheet';
                NewPacking::where('work_sheet_id', $id)->update(['in_trash' => true]);
                Invoice::where('work_sheet_id', $id)->update(['in_trash' => true]);
                Manifest::where('work_sheet_id', $id)->update(['in_trash' => true]);
                ReceiptArchive::where('worksheet_id', $id)->update(['in_trash' => true]);
                CourierTask::where('worksheet_id', $id)->delete();
                break;
            case 'phil_ind_worksheet';
                PackingEngNew::where('work_sheet_id', $id)->update(['in_trash' => true]);
                ReceiptArchive::where('worksheet_id', $id)->update(['in_trash' => true]);
                CourierTask::where('eng_worksheet_id', $id)->delete();
                break;
            case 'courier_draft_worksheet';
                PackingSea::where('work_sheet_id', $id)->update(['in_trash' => true]);
                ReceiptArchive::where('worksheet_id', $id)->update(['in_trash' => true]);
                CourierTask::where('draft_id', $id)->delete();
                break;
            case 'courier_eng_draft_worksheet';
                PackingEng::where('work_sheet_id', $id)->update(['in_trash' => true]);
                ReceiptArchive::where('worksheet_id', $id)->update(['in_trash' => true]);
                CourierTask::where('eng_draft_id', $id)->delete();
                break;       
            default:
                break;
        }
    }


    public function trashActivate()
    {
        $table = $this->table_name;
        $id = $this->worksheet_id;
        $this->delete();
        switch($table) {
            case 'new_worksheet';
                $worksheet = NewWorksheet::find($id);
                $worksheet->update(['in_trash' => false]);
                $worksheet->checkCourierTask($worksheet->status);
                NewPacking::where('work_sheet_id', $id)->update(['in_trash' => false]);
                Invoice::where('work_sheet_id', $id)->update(['in_trash' => false]);
                Manifest::where('work_sheet_id', $id)->update(['in_trash' => false]);
                ReceiptArchive::where('worksheet_id', $id)->update(['in_trash' => false]);
                break;
            case 'phil_ind_worksheet';
                $worksheet = PhilIndWorksheet::find($id);
                $worksheet->update(['in_trash' => false]);
                $worksheet->checkCourierTask($worksheet->status);
                PackingEngNew::where('work_sheet_id', $id)->update(['in_trash' => false]);
                ReceiptArchive::where('worksheet_id', $id)->update(['in_trash' => false]);
                break;
            case 'courier_draft_worksheet';
                $worksheet = CourierDraftWorksheet::find($id);
                $worksheet->update(['in_trash' => false]);
                $worksheet->checkCourierTask($worksheet->status);
                PackingSea::where('work_sheet_id', $id)->update(['in_trash' => false]);
                ReceiptArchive::where('worksheet_id', $id)->update(['in_trash' => false]);
                break;
            case 'courier_eng_draft_worksheet';
                $worksheet = CourierEngDraftWorksheet::find($id);
                $worksheet->update(['in_trash' => false]);
                $worksheet->checkCourierTask($worksheet->status);
                PackingEng::where('work_sheet_id', $id)->update(['in_trash' => false]);
                ReceiptArchive::where('worksheet_id', $id)->update(['in_trash' => false]);               
                break;       
            default:
            break;
        }

        return true;
    }


    public function removeСompletely()
    {              
        $table = $this->table_name;
        $id = $this->worksheet_id;       
        switch($table) {
            case 'new_worksheet';
                NewWorksheet::find($id)->delete();
                NewPacking::where('work_sheet_id', $id)->delete();
                Invoice::where('work_sheet_id', $id)->delete();
                Manifest::where('work_sheet_id', $id)->delete();
                ReceiptArchive::where('worksheet_id', $id)->delete();
                break;
            case 'phil_ind_worksheet';
                PhilIndWorksheet::find($id)->delete();
                PackingEngNew::where('work_sheet_id', $id)->delete();
                ReceiptArchive::where('worksheet_id', $id)->delete();
                break;
            case 'courier_draft_worksheet';
                CourierDraftWorksheet::find($id)->delete();
                PackingSea::where('work_sheet_id', $id)->delete();
                ReceiptArchive::where('worksheet_id', $id)->delete();
                break;
            case 'courier_eng_draft_worksheet';
                CourierEngDraftWorksheet::find($id)->delete();
                PackingEng::where('work_sheet_id', $id)->delete();
                ReceiptArchive::where('worksheet_id', $id)->delete();
                break;       
            default:
                break;
        }
        return $this->delete();
    }

}
