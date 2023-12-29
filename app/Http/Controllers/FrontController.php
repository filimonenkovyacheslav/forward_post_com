<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\NewWorksheet;
use App\ChinaWorksheet;
use App\PhilIndWorksheet;
use App\PackingSea;
use App\PackingEng;
use App\PackingEngNew;
use App\Http\Controllers\Admin\AdminController;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use DB;


class FrontController extends AdminController
{    
    public function parcelForm()
    {
        $israel_cities = $this->israelCities();
        $israel_cities['other'] = 'Другой город';
        return view('parcel_form',compact('israel_cities'));       
    }


    public function parcelFormGcs()
    {
        $israel_cities = $this->israelCities();
        $israel_cities['other'] = 'Другой город';
        return view('parcel_form_gcs',compact('israel_cities'));       
    }


    public function parcelFormEngGcs()
    {
        $israel_cities = $this->israelCities();
        $israel_cities['other'] = 'Other city';
        $to_country = $this->to_country_arr;
        return view('phil_ind_parcel_form_gcs',compact('israel_cities','to_country'));      
    }


    public function parcelFormOld()
    {
        return view('parcel_form_old');       
    }


    public function newParcelAdd(Request $request)
    {        
        $parcels_qty = (int)$request->input('parcels_qty');
        $message = '';
        if (!$request->input('phone_exist_checked')) {
            $message = $this->checkExistPhone($request,'courier_draft_worksheet');
            if ($message) return redirect()->route('parcelForm')->with('phone_exist', $message)->with('phone_number',$request->input('standard_phone'));
        }
        else{
            $message = $this->__newParcelAdd($request);
            return redirect()->route('parcelForm')->with('status', $message);
        }        
        
        $message = $this->__newParcelAdd($request);

        return redirect()->route('parcelForm')->with('status', $message);
    }


    private function __newParcelAdd($request)
    {        
        $fields = $this->getTableColumns('courier_draft_worksheet'); 
        $new_worksheet = new CourierDraftWorksheet();         

        foreach($fields as $field){

            if ($field === 'sender_name') {
                $new_worksheet->$field = $request->input('first_name').' '.$request->input('last_name');
            }
            else if($field === 'site_name'){
                $new_worksheet->$field = 'DD-C';
            }
            else if($field === 'recipient_name'){
                $new_worksheet->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
            }
            else if($field === 'package_content'){
                $content = '';
                if ($request->input('clothing_quantity')) {
                    $content .= 'Одежда: '.$request->input('clothing_quantity').'; ';
                }
                if ($request->input('shoes_quantity')) {
                    $content .= 'Обувь: '.$request->input('shoes_quantity').'; ';
                }               
                if ($request->input('other_content_1')) {
                    $content .= $request->input('other_content_1').': '.$request->input('other_quantity_1').'; ';
                }
                if ($request->input('other_content_2')) {
                    $content .= $request->input('other_content_2').': '.$request->input('other_quantity_2').'; ';
                }
                if ($request->input('other_content_3')) {
                    $content .= $request->input('other_content_3').': '.$request->input('other_quantity_3').'; ';
                }
                if ($request->input('other_content_4')) {
                    $content .= $request->input('other_content_4').': '.$request->input('other_quantity_4').'; ';
                }
                if ($request->input('other_content_5')) {
                    $content .= $request->input('other_content_5').': '.$request->input('other_quantity_5').'; ';
                }
                if ($request->input('other_content_6')) {
                    $content .= $request->input('other_content_6').': '.$request->input('other_quantity_6').'; ';
                }
                if ($request->input('other_content_7')) {
                    $content .= $request->input('other_content_7').': '.$request->input('other_quantity_7').'; ';
                }
                if ($request->input('other_content_8')) {
                    $content .= $request->input('other_content_8').': '.$request->input('other_quantity_8').'; ';
                }
                if(!$content){
                    $content = 'Пусто: 0';
                }                

                $new_worksheet->$field = trim($content);
            }
            else if ($field === 'comment_2'){
                if ($request->input('need_box')) $new_worksheet->$field = $request->input('need_box');
                if ($request->input('comment_2')) $new_worksheet->$field = $request->input('comment_2');
            }
            else if ($field !== 'created_at'){
                $new_worksheet->$field = $request->input($field);
            }           
        }

        $new_worksheet->in_trash = false;
        if (in_array($new_worksheet->sender_city, array_keys($this->israel_cities))) {
            $new_worksheet->shipper_region = $this->israel_cities[$new_worksheet->sender_city];
        }        

        // New parcel form
        if (null !== $request->status_box) {
            if ($request->status_box === 'false') {
                if(!$request->short_order)
                    $new_worksheet->status = 'Забрать';
                else
                    $new_worksheet->status = 'Пакинг лист';
            } 
            else{
                $new_worksheet->status = 'Коробка';
            }
        }
         
        if (null !== $request->need_box) {
            if ($request->need_box === 'Мне не нужна коробка') {
                if(!$request->short_order)
                    $new_worksheet->status = 'Забрать';
                else
                    $new_worksheet->status = 'Пакинг лист';
            }
            else{
                $new_worksheet->status = 'Коробка';
            }
        }        

        $new_worksheet->date = date('Y-m-d');
        $new_worksheet->status_date = date('Y-m-d'); 
        $new_worksheet->order_date = date('Y-m-d');      

        if ($new_worksheet->save()){           
            if(!$request->short_order)
                $this->addingOrderNumber($new_worksheet->standard_phone, 'ru');
            $work_sheet_id = $new_worksheet->id;       
            $message = 'Заказ посылки успешно создан !';
            $new_worksheet = CourierDraftWorksheet::find($work_sheet_id);
            $new_worksheet->checkCourierTask($new_worksheet->status);

            // Packing
            $fields_packing = ['payer', 'contract', 'type', 'track_code', 'full_shipper', 'full_consignee', 'country_code', 'postcode', 'region', 'district', 'city', 'street', 'house', 'body', 'room', 'phone', 'tariff', 'tariff_cent', 'weight_kg', 'weight_g', 'service_code', 'amount_1', 'amount_2', 'attachment_number', 'attachment_name', 'amount_3', 'weight_enclosures_kg', 'weight_enclosures_g', 'value_euro', 'value_cent', 'work_sheet_id'];
            $j=1;
            $paking_not_create = true;

            if ($request->input('clothing_quantity')) {
                $packing_sea = new PackingSea();
                foreach($fields_packing as $field){
                    if ($field === 'type') {
                        $packing_sea->$field = $request->input('tariff');
                    }
                    else if ($field === 'full_shipper') {
                        $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                    }
                    else if ($field === 'full_consignee') {
                        $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                    }
                    else if ($field === 'country_code') {
                        $packing_sea->$field = $request->input('recipient_country');
                    }
                    else if ($field === 'postcode') {
                        $packing_sea->$field = $request->input('recipient_postcode');
                    }
                    else if ($field === 'city') {
                        $packing_sea->$field = $request->input('recipient_city');
                    }
                    else if ($field === 'street') {
                        $packing_sea->$field = $request->input('recipient_street');
                    }
                    else if ($field === 'house') {
                        $packing_sea->$field = $request->input('recipient_house');
                    }
                    else if ($field === 'room') {
                        $packing_sea->$field = $request->input('recipient_room');
                    }
                    else if ($field === 'phone') {
                        $packing_sea->$field = $request->input('recipient_phone');
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
                        $packing_sea->$field = 'Одежда';
                    }
                    else if ($field === 'amount_3') {
                        $packing_sea->$field = $request->input('clothing_quantity');
                    }
                    else{
                        $packing_sea->$field = $request->input($field);
                    }
                }
                $j++;
                if ($packing_sea->save()) {
                    $paking_not_create = false;
                }
            }

            if ($request->input('shoes_quantity')) {
                $packing_sea = new PackingSea();
                foreach($fields_packing as $field){
                    if ($field === 'type') {
                        $packing_sea->$field = $request->input('tariff');
                    }
                    else if ($field === 'full_shipper') {
                        $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                    }
                    else if ($field === 'full_consignee') {
                        $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                    }
                    else if ($field === 'country_code') {
                        $packing_sea->$field = $request->input('recipient_country');
                    }
                    else if ($field === 'postcode') {
                        $packing_sea->$field = $request->input('recipient_postcode');
                    }
                    else if ($field === 'city') {
                        $packing_sea->$field = $request->input('recipient_city');
                    }
                    else if ($field === 'street') {
                        $packing_sea->$field = $request->input('recipient_street');
                    }
                    else if ($field === 'house') {
                        $packing_sea->$field = $request->input('recipient_house');
                    }
                    else if ($field === 'room') {
                        $packing_sea->$field = $request->input('recipient_room');
                    }
                    else if ($field === 'phone') {
                        $packing_sea->$field = $request->input('recipient_phone');
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
                        $packing_sea->$field = 'Обувь';
                    }
                    else if ($field === 'amount_3') {
                        $packing_sea->$field = $request->input('shoes_quantity');
                    }
                    else{
                        $packing_sea->$field = $request->input($field);
                    }
                }
                $j++;
                if ($packing_sea->save()) {
                    $paking_not_create = false;
                }
            }

            for ($i=1; $i < 9; $i++) { 
                if ($request->input('other_content_'.$i)) {
                    $packing_sea = new PackingSea();
                    foreach($fields_packing as $field){
                        if ($field === 'type') {
                            $packing_sea->$field = $request->input('tariff');
                        }
                        else if ($field === 'full_shipper') {
                            $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                        }
                        else if ($field === 'full_consignee') {
                            $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                        }
                        else if ($field === 'country_code') {
                            $packing_sea->$field = $request->input('recipient_country');
                        }
                        else if ($field === 'postcode') {
                            $packing_sea->$field = $request->input('recipient_postcode');
                        }
                        else if ($field === 'city') {
                            $packing_sea->$field = $request->input('recipient_city');
                        }
                        else if ($field === 'street') {
                            $packing_sea->$field = $request->input('recipient_street');
                        }
                        else if ($field === 'house') {
                            $packing_sea->$field = $request->input('recipient_house');
                        }
                        else if ($field === 'room') {
                            $packing_sea->$field = $request->input('recipient_room');
                        }
                        else if ($field === 'phone') {
                            $packing_sea->$field = $request->input('recipient_phone');
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
                            $packing_sea->$field = $request->input('other_content_'.$i);
                        }
                        else if ($field === 'amount_3') {
                            $packing_sea->$field = $request->input('other_quantity_'.$i);
                        }
                        else{
                            $packing_sea->$field = $request->input($field);
                        }
                    }
                    $j++;
                    if ($packing_sea->save()) {
                        $paking_not_create = false;
                    }
                }
            } 

            if ($paking_not_create) {
                $packing_sea = new PackingSea();
                foreach($fields_packing as $field){
                    if ($field === 'type') {
                        $packing_sea->$field = $request->input('tariff');
                    }
                    else if ($field === 'full_shipper') {
                        $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                    }
                    else if ($field === 'full_consignee') {
                        $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                    }
                    else if ($field === 'country_code') {
                        $packing_sea->$field = $request->input('recipient_country');
                    }
                    else if ($field === 'postcode') {
                        $packing_sea->$field = $request->input('recipient_postcode');
                    }
                    else if ($field === 'city') {
                        $packing_sea->$field = $request->input('recipient_city');
                    }
                    else if ($field === 'street') {
                        $packing_sea->$field = $request->input('recipient_street');
                    }
                    else if ($field === 'house') {
                        $packing_sea->$field = $request->input('recipient_house');
                    }
                    else if ($field === 'room') {
                        $packing_sea->$field = $request->input('recipient_room');
                    }
                    else if ($field === 'phone') {
                        $packing_sea->$field = $request->input('recipient_phone');
                    }
                    else if ($field === 'tariff') {
                        $packing_sea->$field = null;
                    }
                    else if ($field === 'work_sheet_id') {
                        $packing_sea->$field = $work_sheet_id;
                    }
                    else if ($field === 'attachment_number') {
                        $packing_sea->$field = 1;
                    }
                    else if ($field === 'attachment_name') {
                        $packing_sea->$field = 'Пусто';
                    }
                    else if ($field === 'amount_3') {
                        $packing_sea->$field = '0';
                    }
                    else{
                        $packing_sea->$field = $request->input($field);
                    }
                }

                $packing_sea->save();
            }
        }
        else{
            $message = 'Ошибка сохранения !';
        }             
        
        return $message;        
    }


    public function forwardParcelAdd(Request $request)
    {
        $parcels_qty = (int)$request->input('parcels_qty');
        $message = '';
        if (!$request->input('phone_exist_checked')){           
            $message = $this->checkExistPhone($request,'courier_draft_worksheet');
            if ($message) return redirect($request->input('url_name').'?phone_exist='.$message.'&phone_number='.$request->input('standard_phone'));
        }
        else{
            $message = $this->__forwardParcelAdd($request);
            return redirect($request->input('url_name').'?message='.$message);
        }
        
        $message = $this->__forwardParcelAdd($request);

        return redirect($request->input('url_name').'?message='.$message);
    }


    private function __forwardParcelAdd($request)
    {
        if ($request->input('url_name')) {
            $new_worksheet = new CourierDraftWorksheet();
            $fields = $this->getTableColumns('courier_draft_worksheet');     

            foreach($fields as $field){
                if ($field === 'sender_name') {
                    $new_worksheet->$field = $request->input('first_name').' '.$request->input('last_name');
                }
                else if($field === 'site_name'){
                    $new_worksheet->$field = 'For';
                }
                else if($field === 'recipient_name'){
                    $new_worksheet->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                }
                else if($field === 'package_content'){
                    $content = '';
                    if ($request->input('clothing_quantity')) {
                        $content .= 'Одежда: '.$request->input('clothing_quantity').'; ';
                    }
                    if ($request->input('shoes_quantity')) {
                        $content .= 'Обувь: '.$request->input('shoes_quantity').'; ';
                    }
                    if ($request->input('other_content_1')) {
                        $content .= $request->input('other_content_1').': '.$request->input('other_quantity_1').'; ';
                    }
                    if ($request->input('other_content_2')) {
                        $content .= $request->input('other_content_2').': '.$request->input('other_quantity_2').'; ';
                    }
                    if ($request->input('other_content_3')) {
                        $content .= $request->input('other_content_3').': '.$request->input('other_quantity_3').'; ';
                    }
                    if ($request->input('other_content_4')) {
                        $content .= $request->input('other_content_4').': '.$request->input('other_quantity_4').'; ';
                    }
                    if ($request->input('other_content_5')) {
                        $content .= $request->input('other_content_5').': '.$request->input('other_quantity_5').'; ';
                    }
                    if ($request->input('other_content_6')) {
                        $content .= $request->input('other_content_6').': '.$request->input('other_quantity_6').'; ';
                    }
                    if ($request->input('other_content_7')) {
                        $content .= $request->input('other_content_7').': '.$request->input('other_quantity_7').'; ';
                    }
                    if ($request->input('other_content_8')) {
                        $content .= $request->input('other_content_8').': '.$request->input('other_quantity_8').'; ';
                    }
                    if(!$content){
                        $content = 'Пусто: 0';
                    } 

                    $new_worksheet->$field = trim($content);
                }
                else if ($field === 'comment_2'){
                    $new_worksheet->$field = $request->input('need_box');
                }
                else if ($field !== 'created_at'){
                    $new_worksheet->$field = $request->input($field);
                }           
            }

            $new_worksheet->in_trash = false;
            if (in_array($new_worksheet->sender_city, array_keys($this->israel_cities))) {
                $new_worksheet->shipper_region = $this->israel_cities[$new_worksheet->sender_city];
            }

            $new_worksheet->date = date('Y-m-d');
            $new_worksheet->status_date = date('Y-m-d');
            $new_worksheet->order_date = date('Y-m-d');
            if ($request->input('need_box') === 'Мне не нужна коробка') {
                if(!$request->short_order)
                    $new_worksheet->status = 'Забрать';
                else
                    $new_worksheet->status = 'Пакинг лист';
            }
            else{
                $new_worksheet->status = 'Коробка';
            }           

            if($new_worksheet->save()){              
                if(!$request->short_order)
                    $this->addingOrderNumber($new_worksheet->standard_phone, 'ru');
                
                $work_sheet_id = $new_worksheet->id;
                $message = 'Заказ посылки успешно создан !';
                $new_worksheet = CourierDraftWorksheet::find($work_sheet_id);
                $new_worksheet->checkCourierTask($new_worksheet->status);

                // Packing
                $fields_packing = ['payer', 'contract', 'type', 'track_code', 'full_shipper', 'full_consignee', 'country_code', 'postcode', 'region', 'district', 'city', 'street', 'house', 'body', 'room', 'phone', 'tariff', 'tariff_cent', 'weight_kg', 'weight_g', 'service_code', 'amount_1', 'amount_2', 'attachment_number', 'attachment_name', 'amount_3', 'weight_enclosures_kg', 'weight_enclosures_g', 'value_euro', 'value_cent', 'work_sheet_id'];
                $j=1;
                $paking_not_create = true;

                if ($request->input('clothing_quantity')) {
                    $packing_sea = new PackingSea();
                    foreach($fields_packing as $field){
                        if ($field === 'type') {
                            $packing_sea->$field = $request->input('tariff');
                        }
                        else if ($field === 'full_shipper') {
                            $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                        }
                        else if ($field === 'full_consignee') {
                            $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                        }
                        else if ($field === 'country_code') {
                            $packing_sea->$field = $request->input('recipient_country');
                        }
                        else if ($field === 'postcode') {
                            $packing_sea->$field = $request->input('recipient_postcode');
                        }
                        else if ($field === 'city') {
                            $packing_sea->$field = $request->input('recipient_city');
                        }
                        else if ($field === 'street') {
                            $packing_sea->$field = $request->input('recipient_street');
                        }
                        else if ($field === 'house') {
                            $packing_sea->$field = $request->input('recipient_house');
                        }
                        else if ($field === 'room') {
                            $packing_sea->$field = $request->input('recipient_room');
                        }
                        else if ($field === 'phone') {
                            $packing_sea->$field = $request->input('recipient_phone');
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
                            $packing_sea->$field = 'Одежда';
                        }
                        else if ($field === 'amount_3') {
                            $packing_sea->$field = $request->input('clothing_quantity');
                        }
                        else{
                            $packing_sea->$field = $request->input($field);
                        }
                    }
                    $j++;
                    if ($packing_sea->save()) {
                        $paking_not_create = false;
                    }
                }

                if ($request->input('shoes_quantity')) {
                    $packing_sea = new PackingSea();
                    foreach($fields_packing as $field){
                        if ($field === 'type') {
                            $packing_sea->$field = $request->input('tariff');
                        }
                        else if ($field === 'full_shipper') {
                            $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                        }
                        else if ($field === 'full_consignee') {
                            $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                        }
                        else if ($field === 'country_code') {
                            $packing_sea->$field = $request->input('recipient_country');
                        }
                        else if ($field === 'postcode') {
                            $packing_sea->$field = $request->input('recipient_postcode');
                        }
                        else if ($field === 'city') {
                            $packing_sea->$field = $request->input('recipient_city');
                        }
                        else if ($field === 'street') {
                            $packing_sea->$field = $request->input('recipient_street');
                        }
                        else if ($field === 'house') {
                            $packing_sea->$field = $request->input('recipient_house');
                        }
                        else if ($field === 'room') {
                            $packing_sea->$field = $request->input('recipient_room');
                        }
                        else if ($field === 'phone') {
                            $packing_sea->$field = $request->input('recipient_phone');
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
                            $packing_sea->$field = 'Обувь';
                        }
                        else if ($field === 'amount_3') {
                            $packing_sea->$field = $request->input('shoes_quantity');
                        }
                        else{
                            $packing_sea->$field = $request->input($field);
                        }
                    }
                    $j++;
                    if ($packing_sea->save()) {
                        $paking_not_create = false;
                    }
                }

                for ($i=1; $i < 9; $i++) { 
                    if ($request->input('other_content_'.$i)) {
                        $packing_sea = new PackingSea();
                        foreach($fields_packing as $field){
                            if ($field === 'type') {
                                $packing_sea->$field = $request->input('tariff');
                            }
                            else if ($field === 'full_shipper') {
                                $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                            }
                            else if ($field === 'full_consignee') {
                                $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                            }
                            else if ($field === 'country_code') {
                                $packing_sea->$field = $request->input('recipient_country');
                            }
                            else if ($field === 'postcode') {
                                $packing_sea->$field = $request->input('recipient_postcode');
                            }
                            else if ($field === 'city') {
                                $packing_sea->$field = $request->input('recipient_city');
                            }
                            else if ($field === 'street') {
                                $packing_sea->$field = $request->input('recipient_street');
                            }
                            else if ($field === 'house') {
                                $packing_sea->$field = $request->input('recipient_house');
                            }
                            else if ($field === 'room') {
                                $packing_sea->$field = $request->input('recipient_room');
                            }
                            else if ($field === 'phone') {
                                $packing_sea->$field = $request->input('recipient_phone');
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
                                $packing_sea->$field = $request->input('other_content_'.$i);
                            }
                            else if ($field === 'amount_3') {
                                $packing_sea->$field = $request->input('other_quantity_'.$i);
                            }
                            else{
                                $packing_sea->$field = $request->input($field);
                            }
                        }
                        $j++;
                        if ($packing_sea->save()) {
                            $paking_not_create = false;
                        }
                    }
                }

                if ($paking_not_create) {
                    $packing_sea = new PackingSea();
                    foreach($fields_packing as $field){
                        if ($field === 'type') {
                            $packing_sea->$field = $request->input('tariff');
                        }
                        else if ($field === 'full_shipper') {
                            $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                        }
                        else if ($field === 'full_consignee') {
                            $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                        }
                        else if ($field === 'country_code') {
                            $packing_sea->$field = $request->input('recipient_country');
                        }
                        else if ($field === 'postcode') {
                            $packing_sea->$field = $request->input('recipient_postcode');
                        }
                        else if ($field === 'city') {
                            $packing_sea->$field = $request->input('recipient_city');
                        }
                        else if ($field === 'street') {
                            $packing_sea->$field = $request->input('recipient_street');
                        }
                        else if ($field === 'house') {
                            $packing_sea->$field = $request->input('recipient_house');
                        }
                        else if ($field === 'room') {
                            $packing_sea->$field = $request->input('recipient_room');
                        }
                        else if ($field === 'phone') {
                            $packing_sea->$field = $request->input('recipient_phone');
                        }
                        else if ($field === 'tariff') {
                            $packing_sea->$field = null;
                        }
                        else if ($field === 'work_sheet_id') {
                            $packing_sea->$field = $work_sheet_id;
                        }
                        else if ($field === 'attachment_number') {
                            $packing_sea->$field = 1;
                        }
                        else if ($field === 'attachment_name') {
                            $packing_sea->$field = 'Пусто';
                        }
                        else if ($field === 'amount_3') {
                            $packing_sea->$field = '0';
                        }
                        else{
                            $packing_sea->$field = $request->input($field);
                        }
                    }
                    $j++;
                    $packing_sea->save();
                }
            }
            else{
                $message = 'Ошибка формы !';
            }                       

            return $message;
        }

    }


    public function trackingForm()
    {
        return view('tracking_form');        
    }


    public function trackingRuForm()
    {
        return view('tracking_ru_form');
    }


    public function getTracking(Request $request)
    {
        $tracking = $request->input('get_tracking');
        $message_arr['en'] = '';   

        if (stripos($tracking, 'T') !== false){
            if (stripos($tracking, 'T-') === false) {
                $tracking = preg_replace("/[^0-9]/", '', $tracking);
                $tracking = 'T-'.$tracking;
            }
        }   
      
        $row = PhilIndWorksheet::select('status')
        ->where('tracking_main', $tracking)
        ->get();
        if ($row->count()) {
            foreach ($row as $val) {
                if ($val->status) {
                    $message_arr['en'] = $val->status;
                }
            }
        }
        
        if (!$row->count()) {
            $row = CourierEngDraftWorksheet::select('status')
            ->where('tracking_main', $tracking)
            ->get();
            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {
                        $message_arr['en'] = $val->status;
                    }
                }
            }
        }  
        
        if (!$row->count()) {
            $row = PhilIndWorksheet::select('status')
            ->where('packing_number', $tracking)
            ->get();
            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {
                        $message_arr['en'] = $val->status;
                    }
                }
            }
        }
        
        if (!$row->count()) {
            $row = CourierEngDraftWorksheet::select('status')
            ->where('packing_number', $tracking)
            ->get();
            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {
                        $message_arr['en'] = $val->status;
                    }
                }
            }
        }
        
        return redirect()->route('trackingForm')
        ->with( 'message_en', $message_arr['en'] )
        ->with( 'not_found', 'not_found' );        
    }


    public function getForwardTracking(Request $request)
    {
        //dd($request->input('get_tracking'));
        $tracking = $request->input('get_tracking');
        $message_arr['ru'] = '';
        $message_arr['en'] = '';
        $message_arr['he'] = '';
        $message_arr['ua'] = '';
        $message = 'Не найдено !';

        /*$update_status_date = NewWorksheet::where('update_status_date','=', date('Y-m-d'))->get()->count();

        if ($update_status_date === 0) {
            app()->call('App\Http\Controllers\RuPostalTrackingController@updateStatusFromUser', [$tracking]);
        }*/
        
        $row = DB::table('new_worksheet')
        ->select('status','status_en','status_he','status_ua')
        ->where([
            ['tracking_main', '=', $tracking]
        ])
        ->get();

        if (!$row->count()){
            $row = DB::table('courier_draft_worksheet')
            ->select('status','status_en','status_he','status_ua')
            ->where([
                ['tracking_main', '=', $tracking]
            ])->get();
        }

        if (!$row->count()){
            $row = DB::table('new_worksheet')
            ->select('status','status_en','status_he','status_ua')
            ->where([
                ['packing_number', '=', $tracking]
            ])->get();
        }

        if (!$row->count()){
            $row = DB::table('courier_draft_worksheet')
            ->select('status','status_en','status_he','status_ua')
            ->where([
                ['packing_number', '=', $tracking]
            ])->get();
        }
        
        if ($row->count()) {
            foreach ($row as $val) {
                if ($val->status) {
                    $message_arr['ru'] = $val->status;
                }
                if ($val->status) {
                    $message_arr['en'] = $val->status_en;
                }
                if ($val->status) {
                    $message_arr['he'] = $val->status_he;
                }
                if ($val->status) {
                    $message_arr['ua'] = $val->status_ua;
                }
            }

            $message = $message_arr['ru'];
        } 

        if ($request->input('url_name')) {
            return redirect($request->input('url_name').'?message='.$message.'&update_status_date='.$update_status_date);
        }
        else{
            return response()->json($message_arr, 200);
        }                                           
    }


    public function chinaParcelForm()
    {
        return view('china_parcel_form');        
    }


    public function chinaParcelAdd(Request $request)
    {
        $china_worksheet = new ChinaWorksheet();
        $fields = ['date', 'tracking_main', 'tracking_local', 'status', 'customer_name', 'customer_address', 'customer_phone', 'customer_email', 'supplier_name', 'supplier_address', 'supplier_phone', 'supplier_email', 'shipment_description', 'weight', 'length', 'width', 'height', 'lot_number', 'status_he', 'status_ru'];
        
        foreach($fields as $field){           
            $china_worksheet->$field = $request->input($field);          
        }

        $china_worksheet->save();

        $message = 'Shipment order successfully created !';
        
        return redirect()->route('chinaParcelForm')->with('status', $message);        
    }


    public function philIndParcelForm()
    {  
        $israel_cities = $this->israelCities();
        $israel_cities['other'] = 'Other city';
        $to_country = $this->to_country_arr;
        return view('phil_ind_parcel_form',compact('israel_cities','to_country'));      
    }


    public function philIndParcelFormOld()
    {
        $to_country = $this->to_country_arr;
        return view('phil_ind_parcel_form_old',compact('to_country'));        
    }


    public function philIndParcelAdd(Request $request)
    {
        $parcels_qty = (int)$request->input('parcels_qty');
        $message = '';
        if (!$request->input('phone_exist_checked')) {
            $message = $this->checkExistPhone($request,'courier_eng_draft_worksheet');
            if ($message) return redirect()->route('philIndParcelForm')->with('phone_exist', $message)->with('phone_number',$request->input('standard_phone'));
        }  
        else{
            $message = $this->__philIndParcelAdd($request);
            return redirect()->route('philIndParcelForm')->with('status', $message);
        }     
        
        $message = $this->__philIndParcelAdd($request);

        return redirect()->route('philIndParcelForm')->with('status', $message);
    }
    

    public function forwardParcelAddEng(Request $request)
    {
        $parcels_qty = (int)$request->parcels_qty;
        $message = '';
        if (!$request->phone_exist_checked) {
            $message = $this->checkExistPhone($request,'courier_eng_draft_worksheet');
            if ($message) {
                return redirect($request->url_name.'?phone_exist='.$message.'&phone_number='.$request->input('standard_phone'));
            }
        }  
        else{
            $message = $this->__philIndParcelAdd($request);
            return redirect($request->url_name.'?message='.$message);
        }     
        
        $message = $this->__philIndParcelAdd($request);
        return redirect($request->url_name.'?message='.$message);
    }
    

    private function __philIndParcelAdd($request)
    {
        $worksheet = new CourierEngDraftWorksheet();
        $fields = $this->getTableColumns('courier_eng_draft_worksheet');
        $message = [];

        foreach($fields as $field){
            if ($field === 'shipper_name') {
                if ($request->shipper_name) 
                    $worksheet->$field = $request->shipper_name;
                else
                    $worksheet->$field = $request->first_name.' '.$request->last_name;
            }
            else if ($field === 'consignee_name') {
                $worksheet->$field = $request->consignee_first_name.' '.$request->consignee_last_name;
            }
            else if ($field === 'consignee_address') {
                $worksheet->$field = $request->consignee_country.' '.$request->consignee_address;
            }
            else if ($field === 'shipped_items') {
                $temp = '';
                for ($i=1; $i < 11; $i++) { 
                    $t_1 = 'item_'.$i;
                    $t_2 = 'q_item_'.$i;
                    if ($request->$t_1) {
                        $temp .= $request->$t_1.': '.$request->$t_2.'; ';
                    }
                }
                if ($temp) $worksheet->$field = $temp;
                else $worksheet->$field = 'Empty: 0';

            }
            else if ($field === 'direction') {
                $worksheet->$field = $this->createDirection($request->shipper_country, $request->consignee_country);
            }
            else if ($field !== 'created_at'){
                $worksheet->$field = $request->$field;
            }                               
        }

        $worksheet->in_trash = false;
        if ($worksheet->shipper_country === 'Israel') {
            if (in_array($worksheet->shipper_city, array_keys($this->israel_cities))) {
                $worksheet->shipper_region = $this->israel_cities[$worksheet->shipper_city];
            }
        }        

        $worksheet->date = date('Y-m-d');
        $worksheet->status_date = date('Y-m-d');
        $worksheet->order_date = date('Y-m-d');

        if (!$request->status_box) {
            if(!$request->short_order)
                $worksheet->status = 'Pick up';
            else
                $worksheet->status = 'Packing list';           
        } 
        else{
            $worksheet->status = 'Box';
        }                        

        if ($worksheet->save()) {          
            if(!$request->short_order)
                $this->addingOrderNumber($worksheet->standard_phone, 'en');
            $work_sheet_id = $worksheet->id;
            $new_worksheet = CourierEngDraftWorksheet::find($work_sheet_id);
            $new_worksheet->checkCourierTask($new_worksheet->status);

            // Packing
            $fields_packing = ['tracking', 'country', 'shipper_name', 'shipper_address', 'shipper_phone', 'shipper_id', 'consignee_name', 'consignee_address', 'consignee_phone', 'consignee_id', 'length', 'width', 'height', 'weight', 'items', 'shipment_val', 'work_sheet_id'];
            $packing = new PackingEng;
            foreach($fields_packing as $field){
                if ($field === 'country') {
                    $packing->$field = $request->consignee_country;
                    // New parcel form
                    if (!$request->consignee_address) $packing->consignee_address = $request->consignee_country;                    
                }
                elseif ($field === 'shipper_name') {
                    $packing->$field = $request->first_name.' '.$request->last_name;
                }
                elseif ($field === 'shipper_phone') {
                    $packing->$field = $request->standard_phone;
                }
                elseif ($field === 'consignee_name') {
                    $packing->$field = $request->consignee_first_name.' '.$request->consignee_last_name;
                }
                elseif ($field === 'work_sheet_id') {
                    $packing->$field = $work_sheet_id;
                }
                else if ($field === 'items') {
                    $temp = '';
                    for ($i=1; $i < 11; $i++) { 
                        $t_1 = 'item_'.$i;
                        $t_2 = 'q_item_'.$i;
                        if ($request->$t_1) {
                            $temp .= $request->$t_1.': '.$request->$t_2.'; ';
                        }
                    }
                    if ($temp) $packing->$field = $temp;
                    else $packing->$field = 'Empty: 0';
                }
                else{
                    $packing->$field = $request->$field;
                } 
            }
            $packing->save();

            $message['id'] = $work_sheet_id;
            $message['message'] = 'Shipment order successfully created !';
        }
        else{
            $message['message'] = 'Saving error !';
        }

        if ($request->url_name) 
            return $message['message'];
        else
            return $message;        
    }


    public function showFormEng()
    {
        $to_country = $this->to_country_arr;
        return view('additional_form_eng',compact('to_country'));        
    }


    public function engCheckTrackingPhone(Request $request)
    {
        $message = 'Please fill or edit or skip the boxes below.';
        
        if ($request->input('tracking_main')) {
            $courier_result = CourierEngDraftWorksheet::where([
                ['standard_phone', $request->input('standard_phone')],
                ['tracking_main', $request->input('tracking_main')]
            ])->first();
            $worksheet_result = PhilIndWorksheet::where([
                ['standard_phone', $request->input('standard_phone')],
                ['tracking_main', $request->input('tracking_main')]
            ])->first();

            if ($courier_result) {
                $id = $courier_result->id;
                $data_parcel = $this->fillResponseDataEng($courier_result, $request, true);
                return redirect()->route('showFormEng')->with('status', $message)->with('id', $id)->with('data_parcel', json_encode($data_parcel))->with('sheet', 'courier');
            }
            elseif ($worksheet_result) {
                $id = $worksheet_result->id;
                $data_parcel = $this->fillResponseDataEng($worksheet_result, $request, true);
                return redirect()->route('showFormEng')->with('status', $message)->with('id', $id)->with('data_parcel', json_encode($data_parcel))->with('sheet', 'worksheet');
            }
            else{
                $message = 'The data you entered is not true or do not match each other. Please try again';
                return redirect()->route('showFormEng')->with('status-error', $message);
            }
        }
        else{
            $draft_result = CourierEngDraftWorksheet::where('standard_phone', $request->input('standard_phone'))->first();

            if ($draft_result) {
                $id = $draft_result->id;
                $data_parcel = $this->fillResponseDataEng($draft_result, $request, true);
                return redirect()->route('showFormEng')->with('status', $message)->with('id', $id)->with('data_parcel', json_encode($data_parcel))->with('sheet', 'courier');
            }
            else{
                $message = 'The data you entered is not found. Please try again';
                return redirect()->route('showFormEng')->with('status-error', $message);
            }
        }                
    }


    public function addFormEng(Request $request)
    {
        $worksheet = null;
        if ($request->input('sheet') === 'courier') {
            $worksheet = CourierEngDraftWorksheet::find($request->input('id'));
            $fields = $this->getTableColumns('courier_eng_draft_worksheet');
        }
        elseif ($request->input('sheet') === 'worksheet') {
            $worksheet = PhilIndWorksheet::find($request->input('id'));
            $fields = $this->getTableColumns('phil_ind_worksheet');
        }
        
        if ($worksheet) {
            
            foreach($fields as $field){
                if ($field === 'shipper_name' && $request->input('first_name') && $request->input('last_name')) {
                    $worksheet->$field = $request->input('first_name').' '.$request->input('last_name');
                }
                else if ($field === 'consignee_name' && $request->input('consignee_first_name') && $request->input('consignee_last_name')) {
                    $worksheet->$field = $request->input('consignee_first_name').' '.$request->input('consignee_last_name');
                }
                else if ($field === 'consignee_address' && $request->input('consignee_country') && $request->input('consignee_address')) {
                    $worksheet->$field = $request->input('consignee_country').' '.$request->input('consignee_address');
                }
                else if ($field === 'shipped_items') {
                    $temp = '';
                    for ($i=1; $i < 11; $i++) { 
                        if (null !== $request->input('item_'.$i)) {
                            $temp .= $request->input('item_'.$i).' - '.$request->input('q_item_'.$i).'; ';
                        }
                    }
                    if ($temp) {
                        $worksheet->$field = $temp;
                    }                    
                }
                else if ($field !== 'created_at' && $request->input($field)){
                    $worksheet->$field = $request->input($field);
                }                               
            }                       

            if ($worksheet->save()) {

                $worksheet->checkCourierTask($worksheet->status);
                
                $work_sheet_id = $worksheet->id;
                $packing = null;

                if ($request->input('sheet') === 'draft' || $request->input('sheet') === 'courier') {
                    $packing = PackingEng::where('work_sheet_id',$work_sheet_id)->first();
                }
                elseif ($request->input('sheet') === 'worksheet') {
                    $packing = PackingEngNew::where('work_sheet_id',$work_sheet_id)->first();
                }

                if ($packing) {
                    // Packing
                    $fields_packing = ['tracking', 'country', 'shipper_name', 'shipper_address', 'shipper_phone', 'shipper_id', 'consignee_name', 'consignee_address', 'consignee_phone', 'consignee_id', 'items', 'shipment_val'];

                    foreach($fields_packing as $field){
                        if ($field === 'country' && $request->input('consignee_country')) {
                            $packing->$field = $request->input('consignee_country');
                        }
                        elseif ($field === 'shipper_name' && $request->input('first_name') && $request->input('last_name')) {
                            $packing->$field = $request->input('first_name').' '.$request->input('last_name');
                        }
                        elseif ($field === 'shipper_phone' && $request->input('standard_phone')) {
                            $packing->$field = $request->input('standard_phone');
                        }
                        elseif ($field === 'consignee_name' && $request->input('consignee_first_name') && $request->input('consignee_last_name')) {
                            $packing->$field = $request->input('consignee_first_name').' '.$request->input('consignee_last_name');
                        }
                        else if ($field === 'items') {
                            $temp = '';
                            for ($i=1; $i < 11; $i++) { 
                                if (null !== $request->input('item_'.$i)) {
                                    $temp .= $request->input('item_'.$i).' - '.$request->input('q_item_'.$i).'; ';
                                }
                            }
                            if ($temp) {
                                $packing->$field = $temp;
                            }                       
                        }
                        elseif ($request->input($field)){
                            $packing->$field = $request->input($field);
                        } 
                    }
                    $packing->save();
                    // End Packing
                    $message = 'The data on your order was updated successfully !';
                    return redirect()->route('showFormEng')->with('status', $message);
                }
                else{
                    $message = 'The data on your packing was not updated. Please try again !';
                    return redirect()->route('showFormEng')->with('status-error', $message);
                }                              
            }
            else{
                $message = 'The data on your order was not updated. Please try again !';
                return redirect()->route('showFormEng')->with('status-error', $message);
            }
        }
        else{
            $message = 'The data on your order was not updated. Please try again !';
            return redirect()->route('showFormEng')->with('status-error', $message);
        }                              
    }


    public function checkPhone(Request $request)
    {
        if ($request->input('draft')) {
            $data = CourierDraftWorksheet::where([
                ['standard_phone', 'like', '%'.$request->input('sender_phone').'%'],
                ['site_name', '=', 'DD-C']
            ])->get()->last();
        }
        else{
            $data = NewWorksheet::where([
                ['sender_phone',$request->input('sender_phone')],
                ['site_name', '=', 'DD-C']
            ])
            ->orWhere([
                ['standard_phone', 'like', '%'.$request->input('sender_phone').'%'],
                ['site_name', '=', 'DD-C']
            ])
            ->get()->last();
        }
        
        $message = 'Данный номер телефона в системе отсутствует';
        $add_parcel = 'true';

        if ($data) {
            if ($request->input('draft')) {
                $data_parcel = $this->fillResponseDataRu($data, $request, false, true);
            }
            else{
                $data_parcel = $this->fillResponseDataRu($data, $request);
            }
            return redirect()->route('parcelForm', ['data_parcel' => $data_parcel])->with('add_parcel', $add_parcel)->with('data_parcel', json_encode($data_parcel));
        }
        else{
            return redirect()->route('parcelForm')->with('no_phone', $message);
        }        
    }


    public function forwardCheckPhone(Request $request)
    {       
        if ($request->input('url_name')) {

            if ($request->input('draft')) {
                $data = CourierDraftWorksheet::where([
                    ['standard_phone', 'like', '%'.$request->input('sender_phone').'%'],
                    ['site_name', '=', 'For']
                ])->get()->last();
            }
            else{
                $data = NewWorksheet::where([
                    ['sender_phone',$request->input('sender_phone')],
                    ['site_name', '=', 'For']
                ])
                ->orWhere([
                    ['standard_phone', 'like', '%'.$request->input('sender_phone').'%'],
                    ['site_name', '=', 'For']
                ])
                ->get()->last();
            }

            $message = 'Данный номер телефона в системе отсутствует';
            if ($request->input('draft')) {
                $data_parcel = '?phone_exist_checked=true&';
            }
            else{
                $data_parcel = '?';
            }

            //dd($data);

            if ($data) {
                if ($request->input('quantity_sender') === '1') {
                    $sender_name = explode(" ", $data->sender_name);
                    if (count($sender_name) > 1) {
                        $data_parcel .= 'first_name='. $sender_name[0].'&';
                        $data_parcel .= 'last_name='. $sender_name[1].'&';
                    }
                    elseif (count($sender_name) == 1) {
                        $data_parcel .= 'first_name='. $sender_name[0].'&';
                        $data_parcel .= 'last_name=&';
                    }
                    else{
                        $data_parcel .= 'first_name=&';
                        $data_parcel .= 'last_name=&';
                    }
                    $data_parcel .= 'sender_address='. $data->sender_address.'&';
                    $data_parcel .= 'sender_city='. $data->sender_city.'&';
                    $data_parcel .= 'sender_postcode='. $data->sender_postcode.'&';
                    $data_parcel .= 'sender_country='. $data->sender_country.'&';
                    $data_parcel .= 'standard_phone=%2B'. ltrim($data->standard_phone, " \+").'&';
                    $data_parcel .= 'sender_phone='. $data->sender_phone.'&';
                    $data_parcel .= 'sender_passport='.  $data->sender_passport.'&';
                }
                if ($request->input('quantity_recipient') === '1') {
                    $recipient_name = explode(" ", $data->recipient_name);
                    if (count($recipient_name) > 1) {
                        $data_parcel .= 'recipient_first_name='. $recipient_name[0].'&';
                        $data_parcel .= 'recipient_last_name='. $recipient_name[1].'&';
                    }
                    elseif (count($recipient_name) == 1) {
                        $data_parcel .= 'recipient_first_name='. $recipient_name[0].'&';
                        $data_parcel .= 'recipient_last_name=&';
                    }
                    else{
                        $data_parcel .= 'recipient_first_name=&';
                        $data_parcel .= 'recipient_last_name=&';
                    }
                    $data_parcel .= 'recipient_street='.  $data->recipient_street.'&';
                    $data_parcel .= 'recipient_house='. $data->recipient_house.'&';
                    $data_parcel .= 'recipient_room='.  $data->recipient_room.'&';                
                    $data_parcel .= 'recipient_city='.  $data->recipient_city.'&';
                    $data_parcel .= 'recipient_postcode='. $data->recipient_postcode.'&';
                    $data_parcel .= 'recipient_country='. $data->recipient_country.'&';
                    $data_parcel .= 'recipient_email='.  $data->recipient_email.'&';
                    $data_parcel .= 'recipient_phone='. $data->recipient_phone.'&';
                    $data_parcel .= 'recipient_passport='.  $data->recipient_passport.'&';
                    $data_parcel .= 'body='. $data->body.'&';
                    $data_parcel .= 'district='. $data->district.'&';
                    $data_parcel .= 'region='.  $data->region;
                }
                return redirect($request->input('url_name').$data_parcel);
            }
            else{
                return redirect($request->input('url_name').'?err_message='.$message);
            }  
        }
    }


    public function philIndCheckPhone(Request $request)
    {
        if ($request->input('draft')) {
            $data = CourierEngDraftWorksheet::where('standard_phone', 'like', '%'.$request->input('shipper_phone').'%')->get()->last();
        }
        else{
            $data = PhilIndWorksheet::where('shipper_phone',$request->input('shipper_phone'))
            ->orWhere('standard_phone', 'like', '%'.$request->input('shipper_phone').'%')
            ->get()->last();
            if (!$data) {
                $data = CourierEngDraftWorksheet::where('standard_phone', 'like', '%'.$request->input('shipper_phone').'%')->get()->last();
            }
        }
        
        $message = 'This phone number is not available in the system';
        $add_parcel = 'true';
        $data_parcel = [];

        if ($data) {
            if ($request->input('draft')) {
                $data_parcel = $this->fillResponseDataEng($data, $request, false, true);
            }
            else{
                $data_parcel = $this->fillResponseDataEng($data, $request);
            }
            
            return redirect()->route('philIndParcelForm', ['data_parcel' => $data_parcel])->with('add_parcel', $add_parcel)->with('data_parcel', json_encode($data_parcel));
        }
        else{
            return redirect()->route('philIndParcelForm')->with('no_phone', $message);
        }        
    }


    public function forwardCheckPhoneEng(Request $request)
    {
        if (!$request->url_name) return redirect()->back();
        
        $data = PhilIndWorksheet::where('shipper_phone',$request->shipper_phone)
        ->orWhere('standard_phone', 'like', '%'.$request->shipper_phone.'%')
        ->get()->last();

        if (!$data) {
            $data = CourierEngDraftWorksheet::where('standard_phone', 'like', '%'.$request->shipper_phone.'%')->get()->last();
        }

        if (!$data) {
            $archive_data = Archive::where([
                ['standard_phone', 'like', '%'.$request->shipper_phone.'%']
            ])->get()->last();
        }
        
        $message = 'This phone number is not available in the system';
        $add_parcel = 'true';
        $data_parcel = [];

        if ($data) {
            if ($request->draft) {
                $data_parcel = $this->fillResponseDataEng($data, $request, false, true);
            }
            else{
                $data_parcel = $this->fillResponseDataEng($data, $request);
            }

            return redirect($request->url_name.'?'.http_build_query($data_parcel));
            
        }
        elseif ($archive_data) {
            if ($request->draft) {
                $data_parcel = $this->fillResponseDataArchiveEng($archive_data, $request, false, true);
            }
            else{
                $data_parcel = $this->fillResponseDataArchiveEng($archive_data, $request);
            }

            return redirect($request->url_name.'?'.http_build_query($data_parcel));
        }
        else{
            return redirect($request->url_name.'?err_message='.$message);
        }  
    }


    public function getForwardTrackingEng(Request $request)
    {
        $tracking = $request->get_tracking;
        $message_arr['ru'] = '';
        $message_arr['en'] = '';
        $message_arr['he'] = '';
        $message_arr['ua'] = '';
        $message = '';

        if (stripos($tracking, 'T') !== false){
            if (stripos($tracking, 'T-') === false) {
                $tracking = preg_replace("/[^0-9]/", '', $tracking);
                $tracking = 'T-'.$tracking;
            }
        }

        $row = DB::table('phil_ind_worksheet')
        ->select('status','status_he','status_ru')
        ->where('tracking_main', '=', $tracking)
        ->get();   

        if (!$row->count())
            $row = DB::table('courier_eng_draft_worksheet')
        ->select('status','status_he','status_ru')
        ->where('tracking_main', '=', $tracking)
        ->get(); 

        if (!$row->count())
            $row = DB::table('phil_ind_worksheet')
        ->select('status','status_he','status_ru')
        ->where('packing_number', '=', $tracking)
        ->get();  

        if (!$row->count())
            $row = DB::table('courier_eng_draft_worksheet')
        ->select('status','status_he','status_ru')
        ->where('packing_number', '=', $tracking)
        ->get(); 

        if ($row->count()) {
            foreach ($row as $val) {
                if ($val->status) {
                    $message_arr['ru'] = $val->status_ru;
                }
                if ($val->status) {
                    $message_arr['en'] = $val->status;
                }
                if ($val->status) {
                    $message_arr['he'] = $val->status_he;
                }
            }
        }
        $message_arr['ua'] = '';
        $message = $message_arr['en'];

        if ($request->url_name) {
            if ($message) {
                return redirect($request->url_name.'?message='.$message);
            }
            else
                return redirect($request->url_name.'?err_message=Not found');
        } 
        else
            return redirect()->back();
    }
}