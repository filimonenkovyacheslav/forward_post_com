<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class Archive extends BaseModel
{   
    protected $table = 'archive';
    protected $fillable = ['tracking_main', 'status', 'status_date', 'status_ru', 'status_he', 'operator','date','direction','tracking_local','pallet_number','comments_1','comments_2','shipper_name', 'shipper_city','passport_number','return_date','shipper_address','standard_phone','shipper_phone','shipper_id','consignee_name','house_name','post_office','district','state_pincode','consignee_address','consignee_phone','consignee_id','shipped_items','shipment_val','courier','delivery_date_comments','weight','width','height','length','volume_weight','lot','payment_date_comments','amount_payment','status_date','shipper_country','consignee_country','parcels_qty','site_name','tariff','partner','region','body','recipient_street','quantity_things','status_ua','recipient_room','table_name','worksheet_id','order_date'];


    /**
    * Create archive.
    */
    public function createArchive($request)
    {
        $data = $request->all();
        $table = $data['table_name'];
        $date = $data['order_date'];
        $id_arr = [];

        switch($table) {
            case 'new_worksheet';
                $id_arr = NewWorksheet::where([                   
                    ['order_date','<>',null],
                    [DB::raw("STR_TO_DATE(order_date, '%Y-%m-%d')"),'<=',$date]
                ])
                ->orWhere([
                    [DB::raw("STR_TO_DATE(date, '%Y-%m-%d')"),'<=',$date]
                ])
                ->orWhere([
                    [DB::raw("STR_TO_DATE(date, '%Y.%m.%d')"),'<=',$date]
                ])
                ->pluck('id')->toArray();
                break;
            case 'phil_ind_worksheet';
                $id_arr = PhilIndWorksheet::where([                    
                    ['order_date','<>',null],
                    [DB::raw("STR_TO_DATE(order_date, '%Y-%m-%d')"),'<=',$date]
                ])
                ->orWhere([
                    [DB::raw("STR_TO_DATE(date, '%Y-%m-%d')"),'<=',$date]
                ])
                ->orWhere([
                    [DB::raw("STR_TO_DATE(date, '%Y.%m.%d')"),'<=',$date]
                ])
                ->pluck('id')->toArray();
                break;      
            default:
            break;
        }
        
        for ($i=0; $i < count($id_arr); $i++) { 
            $new_archive = new Archive();
            $this->__createArchive($table, $new_archive, $id_arr[$i]);
        }
        return $id_arr;                                         
    }


    /**
    * Create archive.
    */
    public function repeatCreateArchive($table, $id_arr)
    {
        for ($i=0; $i < count($id_arr); $i++) { 
            $new_archive = new Archive();
            $this->__createArchive($table, $new_archive, $id_arr[$i]);
        }
        return $id_arr;                                         
    }


    protected function __createArchive($table, $new_archive, $id)
    {
        switch($table) {
            case 'new_worksheet';
                $worksheet = NewWorksheet::find($id);
                $new_archive->table_name = $table;
                $new_archive->site_name = $worksheet->site_name;
                $new_archive->tariff = $worksheet->tariff;
                $new_archive->partner = $worksheet->partner;
                $new_archive->comments_1 = $worksheet->comment_2;
                $new_archive->comments_2 = $worksheet->comments;
                $new_archive->shipper_name = $worksheet->sender_name;
                $new_archive->shipper_country = $worksheet->sender_country;
                $new_archive->shipper_city = $worksheet->sender_city;
                $new_archive->passport_number = $worksheet->sender_passport;
                $new_archive->shipper_address = $worksheet->sender_address;
                $new_archive->shipper_phone = $worksheet->sender_phone;
                $new_archive->consignee_name = $worksheet->recipient_name;
                $new_archive->consignee_country = $worksheet->recipient_country;
                $new_archive->house_name = $worksheet->recipient_house;
                $new_archive->post_office = $worksheet->recipient_postcode;
                $new_archive->region = $worksheet->region;
                $new_archive->recipient_street = $worksheet->recipient_street;
                $new_archive->recipient_room = $worksheet->recipient_room;
                $new_archive->body = $worksheet->body;
                $new_archive->consignee_phone = $worksheet->recipient_phone;
                $new_archive->consignee_id = $worksheet->recipient_passport;
                $new_archive->shipped_items = $worksheet->package_content;
                $new_archive->shipment_val = $worksheet->package_cost;
                $new_archive->delivery_date_comments = $worksheet->pick_up_date;
                $new_archive->lot = $worksheet->batch_number;
                $new_archive->quantity_things = $worksheet->quantity_things;
                $new_archive->payment_date_comments = $worksheet->pay_date;
                $new_archive->amount_payment = $worksheet->pay_sum;
                $new_archive->status_ru = $worksheet->status_en;
                $new_archive->status_ua = $worksheet->status_ua;
                break;
            case 'phil_ind_worksheet';
                $worksheet = PhilIndWorksheet::find($id);
                $new_archive->table_name = $table;
                $new_archive->comments_1 = $worksheet->comments_1;
                $new_archive->comments_2 = $worksheet->comments_2;
                $new_archive->shipper_name = $worksheet->shipper_name;
                $new_archive->shipper_country = $worksheet->shipper_country;
                $new_archive->shipper_city = $worksheet->shipper_city;
                $new_archive->passport_number = $worksheet->passport_number;
                $new_archive->return_date = $worksheet->return_date;
                $new_archive->shipper_address = $worksheet->shipper_address;
                $new_archive->shipper_phone = $worksheet->shipper_phone;
                $new_archive->shipper_id = $worksheet->shipper_id;
                $new_archive->consignee_name = $worksheet->consignee_name;
                $new_archive->consignee_country = $worksheet->consignee_country;
                $new_archive->house_name = $worksheet->house_name;
                $new_archive->post_office = $worksheet->post_office;
                $new_archive->state_pincode = $worksheet->state_pincode;
                $new_archive->consignee_address = $worksheet->consignee_address;
                $new_archive->consignee_phone = $worksheet->consignee_phone;
                $new_archive->consignee_id = $worksheet->consignee_id;
                $new_archive->shipped_items = $worksheet->shipped_items;
                $new_archive->shipment_val = $worksheet->shipment_val;
                $new_archive->operator = $worksheet->operator;
                $new_archive->delivery_date_comments = $worksheet->delivery_date_comments;
                $new_archive->lot = $worksheet->lot;
                $new_archive->payment_date_comments = $worksheet->payment_date_comments;
                $new_archive->amount_payment = $worksheet->amount_payment;
                $new_archive->status_ru = $worksheet->status_ru;
                break;     
            default:
                $new_archive->table = $table;
                break;
        } 

        $new_archive->worksheet_id = $id;
        $new_archive->date = $worksheet->date;
        $new_archive->direction = $worksheet->direction;
        $new_archive->status = $worksheet->status;
        $new_archive->status_date = $worksheet->status_date;
        $new_archive->order_date = $worksheet->order_date;
        $new_archive->tracking_main = $worksheet->tracking_main;
        $new_archive->tracking_local = $worksheet->tracking_local;
        $new_archive->pallet_number = $worksheet->pallet_number;
        $new_archive->standard_phone = $worksheet->standard_phone;
        $new_archive->district = $worksheet->district;
        $new_archive->courier = $worksheet->courier;
        $new_archive->weight = $worksheet->weight;
        $new_archive->width = $worksheet->width;
        $new_archive->height = $worksheet->height;
        $new_archive->length = $worksheet->length;
        $new_archive->volume_weight = $worksheet->volume_weight;
        $new_archive->status_he = $worksheet->status_he; 
        $new_archive->save();
        
        return $new_archive;
    }


    public function createTempArchiveDataTable($archive_ids,$archive_table,$files_folder)
    {
        if (!Schema::hasTable('temp_archive_table')) {
            Schema::create('temp_archive_table', function (Blueprint $table) {
                $table->increments('id');
                $table->text('archive_ids');
                $table->string('archive_table');
                $table->string('files_folder')->nullable();
                $table->timestamps();
            });
            DB::table('temp_archive_table')->insert([
                'archive_ids'=>json_encode($archive_ids),
                'archive_table'=>$archive_table,
                'files_folder'=>$files_folder
            ]); 
            return true;
        } 
        else return false;
    }


    public function getTempArchiveDataTable()
    {
        if (Schema::hasTable('temp_archive_table')) {            
            return DB::table('temp_archive_table')->first();
        } 
        else return false;
    }


    public function deleteTempArchiveDataTable()
    {
        if (Schema::hasTable('temp_archive_table')) {
            Schema::dropIfExists('temp_archive_table'); 
            return true;
        } 
        else return false;
    }


    public function deleteArchive($from_date, $to_date)
    {
        $result = Archive::where([                   
            ['order_date','<>',null],
            [DB::raw("STR_TO_DATE(order_date, '%Y-%m-%d')"),'<=',$to_date],
            [DB::raw("STR_TO_DATE(order_date, '%Y-%m-%d')"),'>=',$from_date]
        ])
        ->orWhere([                   
            [DB::raw("STR_TO_DATE(date, '%Y-%m-%d')"),'<=',$to_date],
            [DB::raw("STR_TO_DATE(date, '%Y-%m-%d')"),'>=',$from_date]
        ])
        ->orWhere([
            [DB::raw("STR_TO_DATE(date, '%Y.%m.%d')"),'<=',$to_date],
            [DB::raw("STR_TO_DATE(date, '%Y.%m.%d')"),'>=',$from_date]
        ])
        ->pluck('id')->toArray();

        if ($result) {
            Archive::whereIn('id',$result)->delete();
        }

        return $result;
    }
}
