<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use App\PackingSea;
use App\PackingEng;
use App\ReceiptArchive;
use App\CourierTask;


class DeletedLog extends BaseModel
{   
    protected $table = 'deleted_logs';
    protected $fillable = ['tracking_main', 'status', 'status_date', 'status_ru', 'status_he', 'operator','date','direction','tracking_local','pallet_number','comments_1','comments_2','shipper_name', 'shipper_city','passport_number','return_date','shipper_address','standard_phone','shipper_phone','shipper_id','consignee_name','house_name','post_office','district','state_pincode','consignee_address','consignee_phone','consignee_id','shipped_items','shipment_val','courier','delivery_date_comments','weight','width','height','length','volume_weight','lot','payment_date_comments','amount_payment','status_date','shipper_country','consignee_country','parcels_qty','site_name','tariff','partner','region','body','recipient_street','quantity_things','status_ua','recipient_room','table_name','worksheet_id','packing_num','packing_files'];


    /**
    * Create log.
    */
    public function createDeletedLog($request)
    {
        $data = $request->all();
        $table = $data['table'];
        if (isset($data['action'])) {
            $id = $data['action'];
            $new_log = new DeletedLog();
            return $this->__createLog($table, $new_log, $id);
        }
        elseif (isset($data['row_id'])) {
            $log_arr = [];
            $id_arr = $data['row_id'];
            for ($i=0; $i < count($id_arr); $i++) { 
                $new_log = new DeletedLog();
                $log_arr[] = $this->__createLog($table, $new_log, $id_arr[$i]);
            }
            return $log_arr;
        }                                          
    }


    protected function __createLog($table, $new_log, $id)
    {
        switch($table) {
            case 'courier_draft_worksheet';
                $worksheet = CourierDraftWorksheet::find($id);
                $new_log->table_name = $table;
                $new_log->parcels_qty = $worksheet->parcels_qty;
                $new_log->site_name = $worksheet->site_name;
                $new_log->tariff = $worksheet->tariff;
                $new_log->partner = $worksheet->partner;
                $new_log->comments_1 = $worksheet->comment_2;
                $new_log->comments_2 = $worksheet->comments;
                $new_log->shipper_name = $worksheet->sender_name;
                $new_log->shipper_country = $worksheet->sender_country;
                $new_log->shipper_city = $worksheet->sender_city;
                $new_log->passport_number = $worksheet->sender_passport;
                $new_log->shipper_address = $worksheet->sender_address;
                $new_log->shipper_phone = $worksheet->sender_phone;
                $new_log->consignee_name = $worksheet->recipient_name;
                $new_log->consignee_country = $worksheet->recipient_country;
                $new_log->house_name = $worksheet->recipient_house;
                $new_log->post_office = $worksheet->recipient_postcode;
                $new_log->region = $worksheet->region;
                $new_log->recipient_street = $worksheet->recipient_street;
                $new_log->recipient_room = $worksheet->recipient_room;
                $new_log->body = $worksheet->body;
                $new_log->consignee_phone = $worksheet->recipient_phone;
                $new_log->consignee_id = $worksheet->recipient_passport;
                $new_log->shipped_items = $worksheet->package_content;
                $new_log->shipment_val = $worksheet->package_cost;
                $new_log->delivery_date_comments = $worksheet->pick_up_date;
                $new_log->lot = $worksheet->batch_number;
                $new_log->quantity_things = $worksheet->quantity_things;
                $new_log->payment_date_comments = $worksheet->pay_date;
                $new_log->amount_payment = $worksheet->pay_sum;
                $new_log->status_ru = $worksheet->status_en;
                $new_log->status_ua = $worksheet->status_ua;
                break;
            case 'courier_eng_draft_worksheet';
                $worksheet = CourierEngDraftWorksheet::find($id);               
                $new_log->table_name = $table;
                $new_log->parcels_qty = $worksheet->parcels_qty;
                $new_log->comments_1 = $worksheet->comments_1;
                $new_log->comments_2 = $worksheet->comments_2;
                $new_log->shipper_name = $worksheet->shipper_name;
                $new_log->shipper_country = $worksheet->shipper_country;
                $new_log->shipper_city = $worksheet->shipper_city;
                $new_log->passport_number = $worksheet->passport_number;
                $new_log->return_date = $worksheet->return_date;
                $new_log->shipper_address = $worksheet->shipper_address;
                $new_log->shipper_phone = $worksheet->shipper_phone;
                $new_log->shipper_id = $worksheet->shipper_id;
                $new_log->consignee_name = $worksheet->consignee_name;
                $new_log->consignee_country = $worksheet->consignee_country;
                $new_log->house_name = $worksheet->house_name;
                $new_log->post_office = $worksheet->post_office;
                $new_log->state_pincode = $worksheet->state_pincode;
                $new_log->consignee_address = $worksheet->consignee_address;
                $new_log->consignee_phone = $worksheet->consignee_phone;
                $new_log->consignee_id = $worksheet->consignee_id;
                $new_log->shipped_items = $worksheet->shipped_items;
                $new_log->shipment_val = $worksheet->shipment_val;
                $new_log->operator = $worksheet->operator;
                $new_log->delivery_date_comments = $worksheet->delivery_date_comments;
                $new_log->lot = $worksheet->lot;
                $new_log->payment_date_comments = $worksheet->payment_date_comments;
                $new_log->amount_payment = $worksheet->amount_payment;
                $new_log->status_ru = $worksheet->status_ru;
                break;       
            default:
                $new_log->table = $table;
                break;
        } 

        $new_log->packing_num = $worksheet->getLastDocUniq();
        $new_log->worksheet_id = $id;
        $new_log->date = $worksheet->date;
        $new_log->direction = $worksheet->direction;
        $new_log->status = $worksheet->status;
        $new_log->status_date = $worksheet->status_date;
        $new_log->tracking_main = $worksheet->tracking_main;
        $new_log->tracking_local = $worksheet->tracking_local;
        $new_log->pallet_number = $worksheet->pallet_number;
        $new_log->standard_phone = $worksheet->standard_phone;
        $new_log->district = $worksheet->district;
        $new_log->courier = $worksheet->courier;
        $new_log->weight = $worksheet->weight;
        $new_log->width = $worksheet->width;
        $new_log->height = $worksheet->height;
        $new_log->length = $worksheet->length;
        $new_log->volume_weight = $worksheet->volume_weight;
        $new_log->status_he = $worksheet->status_he; 

        $new_log->save();
        
        return $new_log;
    }


    public function removeСompletely()
    {
        $items = json_decode($this->packing_files);
        if ($items) {
            foreach($items as $item) {
                if (file_exists($item['path'])) unlink($item['path']);
                if (file_exists($item['signature'])) unlink($item['signature']);
                if (file_exists($item['signature_for_cancel'])) unlink($item['signature_for_cancel']);
            } 
        }
        return $this->delete();
    }

}
