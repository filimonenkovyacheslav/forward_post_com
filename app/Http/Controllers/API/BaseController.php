<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use App\PackingSea;
use App\PackingEng;
use App\NewPacking;
use App\PackingEngNew;
use App\Invoice;
use App\Manifest;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\DraftWorksheet;
use App\EngDraftWorksheet;
use Validator;
use App\User;
use App\ReceiptArchive;
use DB;


class BaseController extends Controller
{
    protected $token = 'd1k6Lpr2nxEa0R96jCSI5xxUjNkJOLFo2vGllglbqZ1MTHFNunB5b8wfy2pc';
    
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
      $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
      $response = [
            'success' => false,
            'message' => $error,
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }


    protected function checkToken($token)
    {
        $row = User::where([
          ['api_token', $token],
          ['role','<>', 'user']
        ])->get();
        if(count($row)){
            return true;
        }
        else{
            return false;
        }
    }


    private function checkCollection($worksheet, $field)
    {
        $value = $worksheet->first()->$field;
        $filtered = $worksheet->filter(function ($item) use ($value, $field) {
            return $item[$field] !== $value;
        });
        return $filtered->all();
    }


    private function updateAllPacking($id, $table, $tracking)
    {
        switch ($table) {
            
            case "new_worksheet":

            NewPacking::where('work_sheet_id',$id)
            ->update(['track_code' => $tracking]);
            Invoice::where('work_sheet_id',$id)
            ->update(['tracking' => $tracking]);
            Manifest::where('work_sheet_id',$id)
            ->update(['tracking' => $tracking]);
        
            break;
            
            case "phil_ind_worksheet":

            PackingEngNew::where('work_sheet_id',$id)
            ->update(['tracking' => $tracking]);

            break;

            case "courier_draft_worksheet":

            PackingSea::where('work_sheet_id',$id)
            ->update(['track_code' => $tracking]);

            break;
            
            case "courier_eng_draft_worksheet":

            PackingEng::where('work_sheet_id',$id)
            ->update(['tracking' => $tracking]);

            break;
        }
    }


    private function checkPhoneAndAddData($phone, $input, $which_admin)
    {
        $response = ['different_recipient' => '', 'added_data' => ''];
        $tracking = $input['tracking_main'];
        
        if ($which_admin === 'ru') {
            $site_name = isset($input['site_name'])?(($input['site_name'] === 'DD')?'DD-C':'For'):'For';
            
            $worksheet = NewWorksheet::where([
                ['standard_phone',$phone],
                ['tracking_main',null]
            ])->orderBy('order_number')->get();  
            $draft = CourierDraftWorksheet::where([
                ['standard_phone',$phone],
                ['tracking_main',null]
            ])->orderBy('order_number')->get();  
            if ($worksheet && $draft) {
                $worksheet = $worksheet->merge($draft);
            }
            elseif (!$worksheet && $draft) {
                $worksheet = $draft;
            }                                 

            if ($worksheet->count()) {
                $different_recipient = $this->checkCollection($worksheet, 'recipient_phone');
                if (count($different_recipient)) {
                    $response['different_recipient'] = count($different_recipient);
                    return $response;
                }

                $table = "new_worksheet";
                $worksheet = $worksheet->first();
                $order_number = $worksheet->order_number;               
                $min_number = NewWorksheet::where([
                    ['standard_phone',$phone],
                    ['order_number',$order_number],
                    ['tracking_main',null]
                ])->orderBy('order_number')->first(); 
                if (!$min_number) {                    
                    $table = "courier_draft_worksheet";
                } 
                
                $worksheet->update([
                    'tracking_main' => $tracking,
                    'status' => 'Доставляется на склад в стране отправителя',
                    'status_en'=> 'Forwarding to the warehouse in the sender country',
                    'status_he'=> "נשלח למחסן במדינת השולח",
                    'status_ua'=> 'Доставляється до складу в країні відправника',
                    'status_date' => date('Y-m-d')
                ]);
                $response['added_data'] = 'Трекинг-номер добавлен в существующую запись';
                $this->updateAllPacking($worksheet->id, $table, $tracking);               
            }
            return $response;
        }
        elseif ($which_admin === 'en') {
            $worksheet = PhilIndWorksheet::where([
                ['standard_phone',$phone],
                ['tracking_main',null]
            ])->orderBy('order_number')->get();  
            $draft = CourierEngDraftWorksheet::where([
                ['standard_phone',$phone],
                ['tracking_main',null]
            ])->orderBy('order_number')->get();  
            if ($worksheet && $draft) {
                $worksheet = $worksheet->merge($draft);
            }
            elseif (!$worksheet && $draft) {
                $worksheet = $draft;
            }                                 

            if ($worksheet->count()) {
                $different_recipient = $this->checkCollection($worksheet, 'recipient_phone');
                if (count($different_recipient)) {
                    $response['different_recipient'] = count($different_recipient);
                    return $response;
                }

                $table = "phil_ind_worksheet";
                $worksheet = $worksheet->first();
                $order_number = $worksheet->order_number;               
                $min_number = PhilIndWorksheet::where([
                    ['standard_phone',$phone],
                    ['order_number',$order_number],
                    ['tracking_main',null]
                ])->orderBy('order_number')->first(); 
                if (!$min_number) {                    
                    $table = "courier_eng_draft_worksheet";
                } 
                
                $worksheet->update([
                    'tracking_main' => $tracking,
                    'status' => 'Forwarding to the warehouse in the sender country',
                    'status_ru'=> 'Доставляется на склад в стране отправителя',
                    'status_he'=> "נשלח למחסן במדינת השולח",
                    'status_date' => date('Y-m-d')
                ]);
                $response['added_data'] = 'Трекинг-номер добавлен в существующую запись';
                $this->updateAllPacking($worksheet->id, $table, $tracking);               
            }
            return $response;
        }       
    }

    
    public function addCourierData(Request $request)
    {
      if ($this->checkToken($request->token) && $request->token) {

            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'standard_phone' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }            

            $standard_phone = ltrim(urldecode($input['standard_phone']), " \+");
            $tracking = $input['tracking_main'];
            $site_name = isset($input['site_name'])?(($input['site_name'] === 'DD')?'DD-C':'For'):'For';

            if (!$this->trackingValidate($tracking)) return $this->sendError('Tracking number is not correct.');

            if ($this->checkWhichAdmin($tracking) === 'ru') {
                
                $has_tracking = CourierDraftWorksheet::where('tracking_main',$tracking)->first();
                if (!$has_tracking) $has_tracking = NewWorksheet::where('tracking_main',$tracking)->first();
                if ($has_tracking) return $this->sendError('Tracking number exists.');

                // Checking draft and worksheet
                $added_data = $this->checkPhoneAndAddData('+'.$standard_phone, $input, 'ru');
                if ($added_data['different_recipient']) {
                    return $this->sendError('У отправителя несколько посылок разным получателям. Трекинг-номер добавлен не будет. Внимательно заполните бумажный пакинг-лист.');
                }
                elseif ($added_data['added_data']) {
                    return $this->sendResponse(['tracking_main' => $tracking], $added_data['added_data']);
                }
                
                CourierDraftWorksheet::create([
                    'tracking_main' => $tracking,
                    'standard_phone' => '+'.$standard_phone,
                    'site_name' => $site_name,
                    'package_content' => 'Пусто: 0',
                    'date' => date('Y.m.d'),
                    'status' => 'Доставляется на склад в стране отправителя',
                    'status_en'=> 'Forwarding to the warehouse in the sender country',
                    'status_he'=> "נשלח למחסן במדינת השולח",
                    'status_ua'=> 'Доставляється до складу в країні відправника',
                    'status_date' => date('Y-m-d')
                ]);
                $work_sheet_id = DB::getPdo()->lastInsertId();

                PackingSea::create([
                    'track_code' => $tracking,
                    'work_sheet_id' => $work_sheet_id,
                    'attachment_number' => '1',
                    'attachment_name' => 'Пусто',
                    'amount_3' => '0'
                ]);

                $this->addingOrderNumber($standard_phone, 'ru');
                $notification = ReceiptArchive::where('tracking_main', $tracking)->first();
                if (!$notification) $this->checkReceipt($work_sheet_id, null, 'ru', $tracking);
            }
            else if ($this->checkWhichAdmin($tracking) === 'en') {

                $has_tracking = CourierEngDraftWorksheet::where('tracking_main',$tracking)->first();
                if (!$has_tracking) $has_tracking = PhilIndWorksheet::where('tracking_main',$tracking)->first();
                if ($has_tracking) return $this->sendError('Tracking number exists.');

                // Checking draft and worksheet
                $added_data = $this->checkPhoneAndAddData('+'.$standard_phone, $input, 'en');
                if ($added_data['different_recipient']) {
                    return $this->sendError('У отправителя несколько посылок разным получателям. Трекинг-номер добавлен не будет. Внимательно заполните бумажный пакинг-лист.');
                }
                elseif ($added_data['added_data']) {
                    return $this->sendResponse(['tracking_main' => $tracking], $added_data['added_data']);
                }
                
                CourierEngDraftWorksheet::create([
                    'tracking_main' => $tracking,
                    'standard_phone' => '+'.$standard_phone,
                    'date' => date('Y.m.d'),
                    'shipped_items' => 'Empty: 0',
                    'status' => 'Forwarding to the warehouse in the sender country',
                    'status_ru'=> 'Доставляется на склад в стране отправителя',
                    'status_he'=> "נשלח למחסן במדינת השולח",
                    'status_date' => date('Y-m-d')
                ]);
                $work_sheet_id = DB::getPdo()->lastInsertId();

                PackingEng::create([
                    'tracking' => $tracking,
                    'work_sheet_id' => $work_sheet_id,
                    'shipper_phone' => '+'.$standard_phone
                ]); 

                $this->addingOrderNumber($standard_phone, 'en');
                $notification = ReceiptArchive::where('tracking_main', $tracking)->first();
                if (!$notification) $this->checkReceipt($work_sheet_id, null, 'en', $tracking);
            }
            else{
                return $this->sendError('Tracking number is not correct.');
            }

            return $this->sendResponse(['tracking_main' => $tracking], 'Post added successfully.');
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    public function getShipmentQtyByBatchNumber(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token) {

            $qty = 0;
            $input = $request->all();
            $validator = Validator::make($input, [
                'batch_number' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $batch_number = $input['batch_number'];

            $qty = NewWorksheet::where('batch_number',$batch_number)->count();
            $qty += PhilIndWorksheet::where('lot',$batch_number)->count();

            return $this->sendResponse(['shipment_qty' => $qty], 'Shipment qty retrieved successfully.');
        }
        else{
            return $this->sendError('Token error.');
        }
    }
}