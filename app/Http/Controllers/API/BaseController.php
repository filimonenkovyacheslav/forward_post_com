<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
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
use App\CourierTask;
use App\ReceiptArchive;
use App\Receipt;
use DB;
use App\SignedDocument;
use App\Checklist;
use App\TrackingList;


class BaseController extends AdminController
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

            if (!$this->trackingValidate($tracking)) return $this->sendError('The tracking number is invalid. Please try again.');

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
                    'date' => date('Y-m-d'),
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
                    'date' => date('Y-m-d'),
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
                return $this->sendError('The tracking number is invalid. Please try again.');
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


    public function getCourierTasks(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token) {
            $input = $request->all();
            $validator = Validator::make($input, [
                'role' => 'required',
                'name' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $role = $input['role'];
            $name = $input['name'];

            if ($role === 'admin') {
                $result = CourierTask::where('packing_num','<>',null)
                ->orWhere([
                    ['packing_num',null],
                    ['status','Box']
                ])
                ->orWhere([
                    ['packing_num',null],
                    ['status','Коробка']
                ])
                ->get();
            }
            elseif (in_array($role, parent::COURIERS_ARR) || $role === 'agent') {
                $courier = User::where('email', 'like', '%'.$name.'%')->first();
                if ($courier) {
                    $email_arr = User::where('role', $courier->role)->pluck('email')->toArray();
                    $name_arr = [];
                    foreach ($email_arr as $value) {
                        $name_arr[] = explode('@', $value)[0];
                    }
                    $result = CourierTask::whereIn('courier',$name_arr)->get();
                }
                else return $this->sendError('Role error.');               
            }
            else return $this->sendError('Role error.');
            
            if ($result){
                $result = $result->toArray();
                return $this->sendResponse($result, 'Courier tasks retrieved successfully.');
            }
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    public function updateTaskStatusBox(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token) {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $task = CourierTask::find($input['id']);
            if ($task) {
                $task->taskDone();
                return $this->sendResponse($task, 'Courier task updated successfully.');
            }
            else{
                return $this->sendError('Data error.');
            }           
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    public function addDataWithTracking(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token) {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required',
                'tracking' => 'required',
                'role' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $task = CourierTask::find($input['id']);
            if ($task) {
                $worksheet = $task->getWorksheet();
                $old_tracking = $worksheet->tracking_main;
                $pallet = $worksheet->pallet_number;

                switch ($worksheet->table) {

                    case "courier_draft_worksheet":

                    $error_message = $this->checkTracking("courier_draft_worksheet", $input['tracking'], $worksheet->id);
                    if($error_message) return $this->sendError($error_message);
                    
                    $lot = $worksheet->batch_number;

                    if ($old_tracking && $old_tracking !== $input['tracking']) {
                        ReceiptArchive::where('tracking_main', $old_tracking)->delete();
                        Receipt::where('tracking_main', $old_tracking)->update(
                            ['tracking_main' => $input['tracking']]
                        );
                    }
                    $notification = ReceiptArchive::where('tracking_main', $input['tracking'])->first();
                    if (!$notification) $this->checkReceipt($worksheet->id, null, 'ru', $input['tracking'],null,$old_tracking); 

                    PackingSea::where('work_sheet_id',$worksheet->id)->update([
                        'track_code' => $input['tracking']
                    ]);

                    // Check for missing tracking
                    $this->checkForMissingTracking($input['tracking']);
                    // Update Warehouse pallet
                    $message = $this->updateWarehousePallet($old_tracking, $input['tracking'], $pallet, $pallet, $lot, $lot, 'ru', $worksheet);
                    if ($message) return $this->sendError($message);
                                       
                    $worksheet->pay_sum = $input['amountPayment'];
                    $worksheet->tracking_main = $input['tracking'];
                    $worksheet->weight = $input['weight'];
                    $worksheet->width = $input['width'];
                    $worksheet->height = $input['height'];
                    $worksheet->length = $input['length'];
                    $worksheet->save();

                    if (in_array($input['role'], parent::COURIERS_ARR)) $this->updateStatusByTracking('courier_draft_worksheet', $worksheet, true);
                    else $this->updateStatusByTracking('courier_draft_worksheet', $worksheet);

                    // Activate PDF
                    if (!$old_tracking && $worksheet->getLastDocUniq()) {
                        app('App\Http\Controllers\Admin\CourierDraftController')->courierDraftActivate($worksheet->id, true, true);
                    }

                    break;

                    case "courier_eng_draft_worksheet":

                    $error_message = $this->checkTracking("courier_eng_draft_worksheet", $input['tracking'], $worksheet->id);
                    if($error_message) return $this->sendError($error_message);

                    $lot = $worksheet->lot;

                    if ($old_tracking && $old_tracking !== $input['tracking']) {
                        ReceiptArchive::where('tracking_main', $old_tracking)->delete();
                        Receipt::where('tracking_main', $old_tracking)->update(
                            ['tracking_main' => $input['tracking']]
                        );
                    }
                    $notification = ReceiptArchive::where('tracking_main', $input['tracking'])->first();
                    if (!$notification) $this->checkReceipt($worksheet->id, null, 'en', $input['tracking'],null,$old_tracking); 

                    PackingEng::where('work_sheet_id',$worksheet->id)->update([
                        'tracking' => $input['tracking']
                    ]);

                    // Check for missing tracking
                    $this->checkForMissingTracking($input['tracking']);
                    // Update Warehouse pallet
                    $message = $this->updateWarehousePallet($old_tracking, $input['tracking'], $pallet, $pallet, $lot, $lot, 'en', $worksheet);
                    if ($message) return $this->sendError($message);
                    
                    $worksheet->amount_payment = $input['amountPayment'];
                    $worksheet->tracking_main = $input['tracking'];
                    $worksheet->weight = $input['weight'];
                    $worksheet->width = $input['width'];
                    $worksheet->height = $input['height'];
                    $worksheet->length = $input['length'];
                    $worksheet->save();
                    
                    if (in_array($input['role'], parent::COURIERS_ARR)) $this->updateStatusByTracking('courier_eng_draft_worksheet', $worksheet, true);
                    else $this->updateStatusByTracking('courier_eng_draft_worksheet', $worksheet);

                    // Activate PDF
                    if (!$old_tracking && $worksheet->getLastDocUniq()) {
                        app('App\Http\Controllers\Admin\CourierEngDraftController')->courierEngDraftActivate($worksheet->id, true);
                    }
                    
                    break;
                }                              
                
                $worksheet->checkCourierTask($worksheet->status);

                return $this->sendResponse($task, 'Courier task updated successfully.');
            }
            else{
                return $this->sendError('Task id error.');
            }           
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    public function addNewSignedForm(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token) {
            $input = $request->all();
            $validator = Validator::make($input, [
                'which_admin' => 'required',
                'session_token' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $user = User::where('api_token',$input['token'])->first();
            if ($user) {
                $link = ($input['which_admin'] === 'ru') ? '/form-with-signature/' : '/form-with-signature-eng/';
                $result = app('App\Http\Controllers\SignedDocumentController')->createTempTable($request);
                if ($result) {
                    if ($input['id']) {
                        $task = CourierTask::find($input['id']);
                        if ($task) {
                            $worksheet = $task->getWorksheet();
                            $link .= $worksheet->id.'/'.$result.'/'.$user->name.'?quantity_sender=1&quantity_recipient=1&api=true';
                        }
                        else return $this->sendError('Task id error.');
                    }
                    else
                        $link .= '0/'.$result.'/'.$user->name;
                    return $this->sendResponse(compact('link'), 'Link created successfully.');
                }               
            }
            else{
                return $this->sendError('Data error.');
            }           
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    public function addDuplicateSignedForm(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token) {
            $input = $request->all();
            $validator = Validator::make($input, [
                'packing_number' => 'required',
                'session_token' => 'required',
                'duplicate_qty' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $document = SignedDocument::where('uniq_id',$input['packing_number'])->first();
            $user = User::where('api_token',$input['token'])->first();
            
            if ($document) {
                $which_admin = ($document->worksheet_id || $document->draft_id) ? 'ru' : 'eng';
                $worksheet = $document->getWorksheet();
                $result = app('App\Http\Controllers\SignedDocumentController')->createTempTable($request);

                if ($which_admin !== 'ru') {
                    $id = app('App\Http\Controllers\Admin\CourierEngDraftController')->courierEngDraftWorksheetDouble($request,$worksheet->id,true);
                }
                else{
                    $id = app('App\Http\Controllers\Admin\CourierDraftController')->courierDraftWorksheetDouble($request,$worksheet->id,true);
                }               
                
                $link = ($which_admin === 'ru') ? '/form-with-signature/' : '/form-with-signature-eng/';
                $link .= $id.'/'.$result.'/'.$user->name.'?quantity_sender=1&quantity_recipient=1&api=true';
                return $this->sendResponse(compact('link'), 'Link created successfully.');
            }
            else{
                return $this->sendError('There is not this packing number.');
            }           
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    public function addNewSignedFormForUser(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'user' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $user = $input['user'];
        $session_token = $this->generateRandomString(15);
        $request->request->add(['session_token' => $session_token]);
        $link = '/form-with-signature/';
        $result = app('App\Http\Controllers\SignedDocumentController')->createTempTable($request);
        if ($result) {
            $link .= '0/'.$result.'/'.$user;
            return redirect()->to($link);
        }                         
    }


    public function addNewSignedFormForUserEng(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'user' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $user = $input['user'];
        $session_token = $this->generateRandomString(15);
        $request->request->add(['session_token' => $session_token]);
        $link = '/form-with-signature-eng/';
        $result = app('App\Http\Controllers\SignedDocumentController')->createTempTable($request);
        if ($result) {
            $link .= '0/'.$result.'/'.$user;
            return redirect()->to($link);
        }                         
    }


    public function addTrackingList(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token) {
            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_list' => 'required',
                'list_name' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $this->createTrackingListTable($request->list_name,$request->tracking_list);
            return $this->sendResponse($request->list_name, 'List created successfully.');
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    public function getTrackingListNames(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token) {
            $input = $request->all();
            $validator = Validator::make($input, [
                'role' => 'required',
                'name' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $role = $input['role'];
            $name = $input['name'];

            if ($role === 'admin' || in_array($role, parent::COURIERS_ARR) || $role === 'agent') {
                $result = TrackingList::pluck('list_name')->unique()->toArray();                
            }
            else return $this->sendError('Role error.');
            
            if ($result){
                return $this->sendResponse($result, 'Tracking List Names retrieved successfully.');
            }
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    public function getChecklist(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token) {
            $input = $request->all();
            $validator = Validator::make($input, [
                'role' => 'required',
                'name' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $role = $input['role'];
            $name = $input['name'];

            if ($role === 'admin' || in_array($role, parent::COURIERS_ARR) || $role === 'agent') {
                $result = Checklist::all()->toArray();                
            }
            else return $this->sendError('Role error.');
            
            if ($result){
                return $this->sendResponse($result, 'Checklist retrieved successfully.');
            }
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    public function addChecksHistory(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token) {
            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking' => 'required',
                'value' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $update_date = Date('Y-m-d H:i', strtotime('+3 houers'));
            $result = DB::table('checks_history')->where('list_name','>',$update_date)->first();
            
            if ($result) {
                $last = DB::table('checks_history')->orderBy('list_name', 'DESC')->first();
                DB::table('checks_history')->insert([
                    'tracking' => $request->tracking,
                    'list_name' => $last->list_name,
                    'value' => $request->value,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } else{
                DB::table('checks_history')->insert([
                    'tracking' => $request->tracking,
                    'list_name' => date('Y-m-d H:i'),
                    'value' => $request->value,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }            

            return $this->sendResponse($request->tracking, 'List created successfully.');
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    public function addNewReceipt(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'senderPhone' => 'required',
            'senderName' => 'required',
            'quantity' => 'required',
            'amount' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $jpg = $this->createNewReceipt($input);
        if ($this->getDomainRule() !== 'forward') {
            $text = 'קיבלה קבלה חדשה מחברת שליחויות בינלאומית, לצפייה לחצו כאן'.' '.$jpg;
        }
        elseif($this->getDomainRule() === 'forward'){
            if (isset($input['country']) && $input['country']) {
                $country = $input['country'];
            }
            else {
                $result = PhilIndWorksheet::where('tracking_main',$input['tracking'])->first();
                $country = $result ? $result->consignee_country : '';
            }

            if ($country === 'India') {
                $text = 'קיבלה קבלה חדשה מחברת דזי.סי.אס.דיליברי 0559912684 ,לצפייה לחצו כאן'.' '.$jpg;
            }
            else{
                $text = 'קיבלה קבלה חדשה מחברת אוריינטל אקספרס 0559398039 ,לצפייה לחצו כאן'.' '.$jpg;
            }
            
        }
        
        return $this->sendResponse($text, 'Receipt added successfully.');        
    }
}