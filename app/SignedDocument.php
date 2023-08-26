<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use Illuminate\Support\Facades\Schema;
use \Dejurin\GoogleTranslateForFree;
use App\BaseModel;
use App\PackingSea;
use App\NewPacking;
use App\Invoice;
use App\Manifest;
use App\PackingEng;
use App\PackingEngNew;


class SignedDocument extends BaseModel
{   
    protected $table = 'signed_documents';
    protected $fillable = ['worksheet_id','eng_worksheet_id','draft_id', 'eng_draft_id', 'signature', 'pdf_file', 'first_file', 'signature_for_cancel', 'file_for_cancel', 'old_document_id', 'uniq_id', 'date', 'screen_ru_form'];

    
    /**
    * Get the worksheet record that owns the signed document.
    */
    public function worksheet()
    {
        return $this->belongsTo('App\NewWorksheet','worksheet_id');
    }

    
    /**
    * Get the eng worksheet record that owns the signed document.
    */
    public function worksheetEng()
    {
        return $this->belongsTo('App\PhilIndWorksheet','eng_worksheet_id');
    }


    /**
    * Get the draft record that owns the signed document.
    */
    public function draft()
    {
        return $this->belongsTo('App\CourierDraftWorksheet','draft_id');
    }

    
    /**
    * Get the eng draft record that owns the signed document.
    */
    public function draftEng()
    {
        return $this->belongsTo('App\CourierEngDraftWorksheet','eng_draft_id');
    }


    /**
    * Getting the worksheet for this signed document.
    */
    public function getWorksheet()
    {
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

        return $this_worksheet;
    }


    protected function getNewWorksheet($request)
    {
        $this_worksheet = null;
        $type = '';

        if (isset($request->worksheet_id)) {
            $this_worksheet = NewWorksheet::find($request->worksheet_id);
            $type = 'worksheet_id';
        }
        elseif (isset($request->eng_worksheet_id)) {
            $this_worksheet = PhilIndWorksheet::find($request->eng_worksheet_id);
            $type = 'eng_worksheet_id';
        }
        elseif (isset($request->draft_id)) {
            $this_worksheet = CourierDraftWorksheet::find($request->draft_id);
            $type = 'draft_id';
        }
        elseif (isset($request->eng_draft_id)) {
            $this_worksheet = CourierEngDraftWorksheet::find($request->eng_draft_id);
            $type = 'eng_draft_id';
        };

        return ['worksheet'=>$this_worksheet,'type'=>$type];
    }


    private function getUniqId($id, $from_country)
    {
        $uniq_id = '';
        $domain = $_SERVER['SERVER_NAME'];
        if($from_country === 'Germany') $uniq_id = 'G'.(10000 + $id);
        elseif(strripos($domain, 'forward-post') !== false) $uniq_id = 'R-'.(11000 + $id);
        else $uniq_id = 'DD'.(10000 + $id);
        return $uniq_id;
    }


    /**
    * Create signed document.
    */
    public function createSignedDocument($request,$file_name,$other = false)
    {
        if (!$other) {
            $worksheet = $this->getNewWorksheet($request);
            $type = $worksheet['type'];
            $worksheet = $worksheet['worksheet'];
            if ($type === 'worksheet_id' || $type === 'draft_id')
                $from_country = $worksheet->sender_country;
            elseif ($type === 'eng_worksheet_id' || $type === 'eng_draft_id')
                $from_country = $worksheet->shipper_country;
            $this->$type = $worksheet->id;  
            $this->signature = $file_name;
            $this->date = date('Y-m-d');
            $this->screen_ru_form = $request->form_screen;
            $this->save();
            $this->uniq_id = $this->getUniqId($this->id, $from_country);
            $this->save();
            return $this;
        }
        else{
            $worksheet = $this->getNewWorksheet($request);
            $type = $worksheet['type'];
            $worksheet = $worksheet['worksheet'];
            if ($type === 'worksheet_id' || $type === 'draft_id')
                $from_country = $worksheet->sender_country;
            elseif ($type === 'eng_worksheet_id' || $type === 'eng_draft_id')
                $from_country = $worksheet->shipper_country;
            $document_old = $worksheet->getLastDoc();
            $this->$type = $worksheet->id;  
            $this->signature_for_cancel = $file_name;
            $this->date = date('Y-m-d');           
            $this->first_file = false;   
            $this->screen_ru_form = $request->form_screen;  
            $this->old_document_id = $document_old->id;      
            $this->save();
            
            if ($request->create_new) {
                $this->updateSignedUniqId($from_country);
            }            
            
            return $this;
        }
    }


    public function updateSignedUniqId($from_country)
    {
        $this->uniq_id = $this->getUniqId($this->id, $from_country);
        $this->save();
    }


    public function updateSignedDocument($request,$file_name)
    {
        $this->signature = $file_name;
        $this->screen_ru_form = $request->form_screen;
        $this->save();
        return $this;
    }


    private function updateWorksheetRu($request,$worksheet,$fields)
    {
        foreach($fields as $field)
        {
            if ($field === 'sender_name') {
                $worksheet->$field = $request->first_name.' '.$request->last_name;
            }
            else if($field === 'recipient_name'){
                $worksheet->$field = $request->recipient_first_name.' '.$request->recipient_last_name;
            }
            else if($field === 'package_content'){
                $content = ''; 

                for ($i=1; $i < 41; $i++){
                    $temp = 'other_content_'.$i;
                    $temp_2 = 'other_quantity_'.$i;
                    if (isset($request->$temp) AND !empty($request->$temp)) {
                        $content .= $request->$temp.': '.$request->$temp_2.'; ';
                    }
                }            

                if(!$content){
                    $content = 'Пусто: 0';
                }                

                $worksheet->$field = trim($content);
            }
            else if ($field === 'comment_2'){
                if (isset($request->need_box)) $worksheet->$field = $request->need_box;
                if (isset($request->comment_2)) $worksheet->$field = $request->comment_2;
            } 
            elseif (isset($request->$field)) {
                $worksheet->$field = $request->$field;
            } 
        }

        $israel_cities = static::israelCities();
        if (in_array($worksheet->sender_city, array_keys($israel_cities))) {
            $worksheet->shipper_region = $israel_cities[$worksheet->sender_city];
        }

        $worksheet->save();
        $worksheet->checkCourierTask($worksheet->status);

        return $worksheet;
    }


    private function updateWorksheetEng($request,$worksheet,$fields)
    {
        foreach($fields as $field)
        {
            if ($field === 'shipper_name' && $request->first_name && $request->last_name) {
                $worksheet->$field = $request->first_name.' '.$request->last_name;
            }
            else if ($field === 'consignee_name' && $request->consignee_first_name && $request->consignee_last_name) {
                $worksheet->$field = $request->consignee_first_name.' '.$request->consignee_last_name;
            }
            else if ($field === 'consignee_address' && $request->consignee_country && $request->consignee_address) {
                $worksheet->$field = $request->consignee_country.' '.$request->consignee_address;
            }
            else if ($field === 'shipped_items') {
                $temp = '';
                for ($i=1; $i < 41; $i++) { 
                    $var = 'item_'.$i;
                    $var_2 = 'q_item_'.$i;
                    if (isset($request->$var) AND !empty($request->$var)) {
                        $temp .= $request->$var.': '.$request->$var_2.'; ';
                    }
                }
                if ($temp) {
                    $worksheet->$field = $temp;
                }                    
            }
            else if (isset($request->$field)){
                $worksheet->$field = $request->$field;
            }                               
        }

        $israel_cities = static::israelCities();
        if (in_array($worksheet->shipper_city, array_keys($israel_cities))) {
            $worksheet->shipper_region = $israel_cities[$worksheet->shipper_city];
        }

        $worksheet->save();
        $worksheet->checkCourierTask($worksheet->status);

        return $worksheet;
    }


    public function updateWorksheet($request)
    {
        $worksheet = $this->getWorksheet();

        switch ($request->type) {           

            case "draft_id":

            $fields = Schema::getColumnListing('courier_draft_worksheet');
            $worksheet = $this->updateWorksheetRu($request,$worksheet,$fields);

            // Update Packing Sea
            $temp = rtrim($worksheet->package_content, ";");
            $content_arr = explode(";",$temp);
            if ($content_arr[0]) {                
                PackingSea::where('work_sheet_id', $worksheet->id)
                ->update([
                    'type' => $worksheet->tariff,
                    'full_shipper' => $worksheet->sender_name,
                    'full_consignee' => $worksheet->recipient_name,
                    'country_code' => $worksheet->recipient_country,
                    'region' => $worksheet->region,
                    'district' => $worksheet->district,
                    'postcode' => $worksheet->recipient_postcode,
                    'city' => $worksheet->recipient_city,
                    'street' => $worksheet->recipient_street,
                    'house' => $worksheet->recipient_house,
                    'body' => $worksheet->body,
                    'room' => $worksheet->recipient_room,
                    'phone' => $worksheet->recipient_phone
                ]);

                if ($worksheet->package_content) {

                    $old_packing = PackingSea::where('work_sheet_id', $worksheet->id)->get();
                    $qty = 1;

                    for ($i=0; $i < count($content_arr); $i++) { 
                        $qty = $i+1;
                        $content = explode(':', $content_arr[$i]);

                        if (count($content) == 2) {
                            if ($qty <= count($old_packing)) {
                                PackingSea::where([
                                    ['work_sheet_id', $worksheet->id],
                                    ['attachment_number', $qty]
                                ])
                                ->update([
                                    'attachment_name' => trim($content[0]),
                                    'amount_3' => trim($content[1])
                                ]);
                            }
                            else{
                                $new_packing = new PackingSea();
                                $new_packing->work_sheet_id = $worksheet->id;
                                $new_packing->type = $worksheet->tariff;
                                $new_packing->full_shipper = $worksheet->sender_name;
                                $new_packing->full_consignee = $worksheet->recipient_name;
                                $new_packing->country_code = $worksheet->recipient_country;
                                $new_packing->postcode = $worksheet->recipient_postcode;
                                $new_packing->region = $worksheet->region;
                                $new_packing->district = $worksheet->district;
                                $new_packing->city = $worksheet->recipient_city;
                                $new_packing->street = $worksheet->recipient_street;
                                $new_packing->house = $worksheet->recipient_house;
                                $new_packing->body = $worksheet->body;
                                $new_packing->room = $worksheet->recipient_room;
                                $new_packing->phone = $worksheet->recipient_phone;
                                $new_packing->attachment_number = $qty;
                                $new_packing->attachment_name = trim($content[0]);
                                $new_packing->amount_3 = trim($content[1]);
                                $new_packing->save();
                            }
                        }
                        else{
                            return null;
                        }
                    }
                    PackingSea::where([
                        ['work_sheet_id', $worksheet->id],
                        ['attachment_number','>',$qty]
                    ])->delete();
                }
                else{
                    PackingSea::where('work_sheet_id', $worksheet->id)->delete();
                }
                // End Update Packing Sea
            }

            break;
            
            case "eng_draft_id":

            $fields = Schema::getColumnListing('courier_eng_draft_worksheet');
            $worksheet = $this->updateWorksheetEng($request,$worksheet,$fields);
            $packing = PackingEng::where('work_sheet_id',$worksheet->id)->first();
            if($packing) $this->updatePackingEng($packing,$worksheet);

            break;

            case "worksheet_id":

            $fields = Schema::getColumnListing('new_worksheet');
            $worksheet = $this->updateWorksheetRu($request,$worksheet,$fields);
            $temp = rtrim($worksheet->package_content, ";");
            $content_arr = explode(";",$temp); 
            $id = $worksheet->id;     

            if ($content_arr[0]) {
                $tr = new GoogleTranslateForFree();
                $this->updateInvoice($worksheet, $id, $tr);
                $this->updateNewPacking($worksheet, $id, $tr, $content_arr);          
                $this->updateManifest($worksheet, $id, $tr, $content_arr);                     
                $worksheet->checkCourierTask($worksheet->status);          
            }
            else{
                return null;
            }
            
            break;

            case "eng_worksheet_id":

            $fields = Schema::getColumnListing('phil_ind_worksheet');
            $worksheet = $this->updateWorksheetEng($request,$worksheet,$fields);
            $packing = PackingEngNew::where('work_sheet_id',$worksheet->id)->first();
            if($packing) $this->updatePackingEng($packing,$worksheet);

            break;
        }

        return $worksheet;
    }


    private function updatePackingEng($packing,$worksheet)
    {
        $fields_packing = ['tracking', 'country', 'shipper_name', 'shipper_address', 'shipper_phone', 'shipper_id', 'consignee_name', 'consignee_address', 'consignee_phone', 'consignee_id', 'items', 'shipment_val'];

        foreach($fields_packing as $field){
            if ($field === 'country') {
                $packing->$field = $worksheet->consignee_country;
            }
            elseif ($field === 'shipper_name') {
                $packing->$field = $worksheet->shipper_name;
            }
            elseif ($field === 'shipper_phone') {
                $packing->$field = $worksheet->standard_phone;
            }
            elseif ($field === 'consignee_name') {
                $packing->$field = $worksheet->consignee_name;
            }
            else if ($field === 'items') {
                    $packing->$field = $worksheet->shipped_items;
                      
            }
            elseif ($worksheet->$field){
                $packing->$field = $worksheet->$field;
            } 
        }
        $packing->save();

        return true;
    }


    private function updateInvoice($worksheet, $id, $tr){
        $invoice_num = 1;
        $result = Invoice::where('number','<>', null)->latest()->first();
        if ($result) {
            $invoice_num = (int)$result->number + 1;
        }
        $address = '';
        if ($worksheet->recipient_postcode) {
            $address .= $worksheet->recipient_postcode.', ';
        }
        if ($worksheet->region) {
            $address .= $this->translit($worksheet->region).', ';
        }
        if ($worksheet->district) {
            $address .= $this->translit($worksheet->district).', ';
        }
        if ($worksheet->recipient_city) {
            $address .= $this->translit($worksheet->recipient_city).', ';
        }
        if ($worksheet->recipient_street) {
            $address .= $this->translit($worksheet->recipient_street).', ';
        }
        if ($worksheet->recipient_house) {
            $address .= $worksheet->recipient_house;
        }   
        if ($worksheet->body) {
            $address .= '/'.$this->translit($worksheet->body);
        }
        if ($worksheet->recipient_room) {
            $address .= ', '.$worksheet->recipient_room;                 
        }

        $has_post = Invoice::where('work_sheet_id', $id)->first();
        if ($has_post) {
            if ($has_post->number){
                Invoice::where('work_sheet_id', $id)
                ->update([
                    'tracking' => $worksheet->tracking_main,
                    'shipper_name' => $this->translit($worksheet->sender_name),
                    'shipper_address_phone' => $this->translit($worksheet->sender_city).', '.$worksheet->sender_address.'; '.$worksheet->standard_phone,
                    'consignee_name' => $this->translit($worksheet->recipient_name),
                    'consignee_address' => $address,
                    'shipped_items' => $tr->translate('ru', 'en', $worksheet->package_content, 5),
                    'weight' => $worksheet->weight,
                    'height' => $worksheet->height,
                    'length' => $worksheet->length,
                    'width' => $worksheet->width,
                    'batch_number' => $worksheet->batch_number,
                    'declared_value' => $worksheet->package_cost
                ]);
            }
            else{
                Invoice::where('work_sheet_id', $id)
                ->update([
                    'number' => $invoice_num,
                    'tracking' => $worksheet->tracking_main,
                    'shipper_name' => $this->translit($worksheet->sender_name),
                    'shipper_address_phone' => $this->translit($worksheet->sender_city.', '.$worksheet->sender_address).'; '.$worksheet->standard_phone,
                    'consignee_name' => $this->translit($worksheet->recipient_name),
                    'consignee_address' => $address,
                    'shipped_items' => $tr->translate('ru', 'en', $worksheet->package_content, 5),
                    'weight' => $worksheet->weight,
                    'height' => $worksheet->height,
                    'length' => $worksheet->length,
                    'width' => $worksheet->width,
                    'batch_number' => $worksheet->batch_number,
                    'declared_value' => $worksheet->package_cost
                ]);
            }
        }
        else{
            $invoice = new Invoice();
            $invoice->number = $invoice_num;
            $invoice->work_sheet_id = $id;
            $invoice->tracking = $worksheet->tracking_main;
            $invoice->shipper_name = $this->translit($worksheet->sender_name);
            $invoice->shipper_address_phone = $this->translit($worksheet->sender_city.', '.$worksheet->sender_address).'; '.$worksheet->standard_phone;
            $invoice->consignee_name = $this->translit($worksheet->recipient_name);
            $invoice->consignee_address = $address;
            $invoice->shipped_items = $tr->translate('ru', 'en', $worksheet->package_content, 5);
            $invoice->weight = $worksheet->weight;
            $invoice->height = $worksheet->height;
            $invoice->length = $worksheet->length;
            $invoice->width = $worksheet->width;
            $invoice->batch_number = $worksheet->batch_number;
            $invoice->declared_value = $worksheet->package_cost;
            $invoice->save();
        }
        
        return true;
    }


    private function updateNewPacking($worksheet, $id, $tr, $content_arr){
        NewPacking::where('work_sheet_id', $id)
        ->update([
            'track_code' => $worksheet->tracking_main,
            'type' => $worksheet->tariff,
            'full_shipper' => $worksheet->sender_name,
            'full_consignee' => $worksheet->recipient_name,
            'country_code' => $worksheet->recipient_country,
            'postcode' => $worksheet->recipient_postcode,
            'region' => $worksheet->region,
            'district' => $worksheet->district,
            'city' => $worksheet->recipient_city,
            'street' => $worksheet->recipient_street,
            'house' => $worksheet->recipient_house,
            'body' => $worksheet->body,
            'room' => $worksheet->recipient_room,
            'phone' => $worksheet->recipient_phone,
            'batch_number' => $worksheet->batch_number,
            'weight_kg' => $worksheet->weight
        ]);

        if ($worksheet->package_content) {               
            $old_packing = NewPacking::where('work_sheet_id', $id)->get();
            $qty = 1;

            for ($i=0; $i < count($content_arr); $i++) { 
                $qty = $i+1;
                $content = explode(':', $content_arr[$i]);

                if (count($content) == 2) {
                    if ($qty <= count($old_packing)) {
                        NewPacking::where([
                            ['work_sheet_id', $id],
                            ['attachment_number', $qty]
                        ])
                        ->update([
                            'attachment_name' => trim($content[0]),
                            'amount_3' => trim($content[1])
                        ]);
                    }
                    else{
                        $new_packing = new NewPacking();
                        $new_packing->work_sheet_id = $id;
                        $new_packing->track_code = $worksheet->tracking_main;
                        $new_packing->type = $worksheet->tariff;
                        $new_packing->full_shipper = $worksheet->sender_name;
                        $new_packing->full_consignee = $worksheet->recipient_name;
                        $new_packing->country_code = $worksheet->recipient_country;
                        $new_packing->postcode = $worksheet->recipient_postcode;
                        $new_packing->region = $worksheet->region;
                        $new_packing->district = $worksheet->district;
                        $new_packing->city = $worksheet->recipient_city;
                        $new_packing->street = $worksheet->recipient_street;
                        $new_packing->house = $worksheet->recipient_house;
                        $new_packing->body = $worksheet->body;
                        $new_packing->room = $worksheet->recipient_room;
                        $new_packing->phone = $worksheet->recipient_phone;
                        $new_packing->attachment_number = $qty;
                        $new_packing->attachment_name = trim($content[0]);
                        $new_packing->amount_3 = trim($content[1]);
                        $new_packing->weight_kg = $worksheet->weight;
                        $new_packing->batch_number = $worksheet->batch_number;
                        $new_packing->save();
                    }
                }
                else{
                    return redirect()->to(session('this_previous_url'))->with('status-error', 'Ошибка колонки Содержание!');
                }
            }
            NewPacking::where([
                ['work_sheet_id', $id],
                ['attachment_number','>',$qty]
            ])->delete();
        }
        else{
            NewPacking::where('work_sheet_id', $id)->delete();
        }

        return true;
    }


    private function updateManifest($worksheet, $id, $tr, $content_arr){  
        $result = Manifest::where('number','<>', null)->latest()->first();
        $manifest_num = 1;
        if ($result) {
            $manifest_num = (int)$result->number + 1;
        }   
        
        $address = '';
        if ($worksheet->recipient_postcode) {
            $address .= $worksheet->recipient_postcode.', ';
        }
        if ($worksheet->region) {
            $address .= $this->translit($worksheet->region).', ';
        }
        if ($worksheet->district) {
            $address .= $this->translit($worksheet->district).', ';
        }
        if ($worksheet->recipient_city) {
            $address .= $this->translit($worksheet->recipient_city).', ';
        }
        if ($worksheet->recipient_street) {
            $address .= $this->translit($worksheet->recipient_street).', ';
        }
        if ($worksheet->recipient_house) {
            $address .= $worksheet->recipient_house;
        }   
        if ($worksheet->body) {
            $address .= '/'.$this->translit($worksheet->body);
        }
        if ($worksheet->recipient_room) {
            $address .= ', '.$worksheet->recipient_room;                 
        }

        $has_post = Manifest::where('work_sheet_id', $id)->first();
        Manifest::where('work_sheet_id', $id)
        ->update([
            'tracking' => $worksheet->tracking_main,
            'sender_country' => $tr->translate('ru', 'en', $worksheet->sender_country, 5),
            'sender_name' => $this->translit($worksheet->sender_name),
            'recipient_name' => $this->translit($worksheet->recipient_name),
            'recipient_city' => $this->translit($worksheet->recipient_city),
            'recipient_address' => $address,
            'weight' => $worksheet->weight,
            'batch_number' => $worksheet->batch_number,
            'cost' => $worksheet->package_cost
        ]);

        if ($worksheet->package_content) {

            $old_packing = Manifest::where('work_sheet_id', $id)->get();
            $qty = 1;

            for ($i=0; $i < count($content_arr); $i++) { 
                $qty = $i+1;
                $content = explode(':', $content_arr[$i]);

                if (count($content) == 2) {
                    if ($qty <= count($old_packing)) {
                        if ($i != 0) $manifest_num = null;
                        if ($has_post) {
                            if ($has_post->number && $i == 0) {
                                Manifest::where([
                                    ['work_sheet_id', $id],
                                    ['attachment_number', $qty]
                                ])
                                ->update([
                                    'content' => $tr->translate('ru', 'en', trim($content[0]), 5),
                                    'quantity' => trim($content[1])
                                ]);
                            }
                            elseif (!$has_post->number && $i == 0) {
                                Manifest::where([
                                    ['work_sheet_id', $id],
                                    ['attachment_number', $qty]
                                ])
                                ->update([
                                    'content' => $tr->translate('ru', 'en', trim($content[0]), 5),
                                    'quantity' => trim($content[1]),
                                    'number' => $manifest_num
                                ]);
                            }
                        }                       
                    }
                    else{
                        if ($i != 0) $manifest_num = null;
                        $new_packing = new Manifest();
                        $new_packing->number = $manifest_num;
                        $new_packing->work_sheet_id = $id;
                        $new_packing->tracking = $worksheet->tracking_main;
                        $new_packing->sender_country = $tr->translate('ru', 'en', $worksheet->sender_country, 5);
                        $new_packing->sender_name = $this->translit($worksheet->sender_name);
                        $new_packing->recipient_name = $this->translit($worksheet->recipient_name);
                        $new_packing->recipient_city = $this->translit($worksheet->recipient_city);
                        $new_packing->recipient_address = $address;
                        $new_packing->weight = $worksheet->weight;
                        $new_packing->cost = $worksheet->package_cost;
                        $new_packing->batch_number = $worksheet->batch_number;
                        $new_packing->attachment_number = $qty;
                        $new_packing->content = $tr->translate('ru', 'en', trim($content[0]), 5);
                        $new_packing->quantity = trim($content[1]);
                        $new_packing->save();
                    }
                }
                else{
                    return redirect()->to(session('this_previous_url'))->with('status-error', 'Ошибка колонки Содержание!');
                }
            }
            Manifest::where([
                ['work_sheet_id', $id],
                ['attachment_number','>',$qty]
            ])->delete();
        }
        else{
            Manifest::where('work_sheet_id', $id)->delete();
        }

        return true;
    }

}
