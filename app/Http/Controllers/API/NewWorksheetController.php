<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use App\NewWorksheet;
use App\DraftWorksheet;
use App\CourierDraftWorksheet;
use Validator;
use Illuminate\Support\Facades\Storage;


class NewWorksheetController extends BaseController
{

    use AuthenticatesUsers;
    
    // Worksheet status
    private $ru_arr = ["Доставляется на склад в стране отправителя", "На складе в стране отправителя", "На таможне в стране отправителя", "Доставляется в страну получателя", "На таможне в стране получателя", "Доставляется получателю", "Доставлено"];
    private $en_arr = [
        "Доставляется на склад в стране отправителя" => "Forwarding to the warehouse in the sender country",
        "На складе в стране отправителя" => "At the warehouse in the sender country",
        "На таможне в стране отправителя" => "At the customs in the sender country",
        "Доставляется в страну получателя" => "Forwarding to the receiver country",
        "На таможне в стране получателя" => "At the customs in the receiver country",
        "Доставляется получателю" => "Forwarding to the receiver",
        "Доставлено" => "Delivered"
    ];
    private $he_arr = [
        "Доставляется на склад в стране отправителя" => "נשלח למחסן במדינת השולח",
        "На складе в стране отправителя" => "במחסן במדינת השולח",
        "На таможне в стране отправителя" => " במכס במדינת השולח",
        "Доставляется в страну получателя" => " נשלח למדינת המקבל",
        "На таможне в стране получателя" => " במכס במדינת המקבל",
        "Доставляется получателю" => " נמסר למקבל",
        "Доставлено" => " נמסר"
    ];
    private $ua_arr = [
        "Доставляется на склад в стране отправителя" => "Доставляється до складу в країні відправника",
        "На складе в стране отправителя" => "На складі в країні відправника",
        "На таможне в стране отправителя" => "На митниці в країні відправника",
        "Доставляется в страну получателя" => "Доставляється в країну отримувача",
        "На таможне в стране получателя" => "На митниці в країні отримувача",
        "Доставляется получателю" => "Доставляється отримувачу",
        "Доставлено" => "Доставлено"
    ];
    private $message_arr = ['ru' => '', 'en' => '', 'he' => '', 'ua' => ''];
    

    /**
     * Display the specified resource.
     *
     * @param  string  $tracking
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $tracking)
    {   
        if ($this->checkToken($request->token) && $request->token) {

            $row = NewWorksheet::select('status','status_en','status_he','status_ua')
            ->where([
                ['tracking_main', '=', $tracking]
            ])
            ->get();

            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {                    
                        $this->message_arr['ru'] = $val->status;
                    }
                    if ($val->status) {
                        $this->message_arr['en'] = $val->status_en;
                    }
                    if ($val->status) {
                        $this->message_arr['he'] = $val->status_he;
                    }
                    if ($val->status) {
                        $this->message_arr['ua'] = $val->status_ua;
                    }
                }
            }
            else{
                return $this->sendError('Tracking number not found.');
            }

            return $this->sendResponse($this->message_arr, 'Status retrieved successfully.');
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    /**
     * Display the specified resource for clients.
     *
     * @param  string  $tracking
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStatus(Request $request, $tracking)
    {   
        if ($this->checkToken($request->token) && $request->token) {

            $row = NewWorksheet::select('status','status_en','status_he','status_ua')
            ->where([
                ['tracking_main', '=', $tracking]
            ])
            ->get();

            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {                    
                        $this->message_arr['ru'] = $val->status;
                    }
                    if ($val->status) {
                        $this->message_arr['en'] = $val->status_en;
                    }
                    if ($val->status) {
                        $this->message_arr['he'] = $val->status_he;
                    }
                    if ($val->status) {
                        $this->message_arr['ua'] = $val->status_ua;
                    }
                }
            }
            else{
                return $this->sendError('Tracking number not found.');
            }

            return $this->sendResponse($this->message_arr, 'Status retrieved successfully.');
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $tracking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tracking)
    {               
        if ($this->checkToken($request->token) && $request->token) {

            $input = $request->all();
            $validator = Validator::make($input, [
                'next_status' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
            
            if ($input['next_status']) {
                $row = NewWorksheet::where([
                    ['tracking_main', '=', $tracking]
                ])
                ->get();

                if ($row->count()) {
                    $sheet = NewWorksheet::find($row[0]->id);
                    foreach ($row as $val) {
                        if ($val->status) {                      
                            $key_num = array_search($val->status, $this->ru_arr);
                            if ((int)$key_num < 6) {
                                $ru_status = $this->ru_arr[(int)$key_num+1];
                                $sheet->status = $ru_status;
                                $this->message_arr['ru'] = $ru_status;
                                $sheet->status_en = $this->en_arr[$ru_status];
                                $this->message_arr['en'] = $this->en_arr[$ru_status];
                                $sheet->status_he = $this->he_arr[$ru_status];
                                $this->message_arr['he'] = $this->he_arr[$ru_status];
                                $sheet->status_ua = $this->ua_arr[$ru_status];
                                $this->message_arr['ua'] = $this->ua_arr[$ru_status];
                                $sheet->status_date = date('Y-m-d');
                                if ($sheet->save()) {
                                    return $this->sendResponse($this->message_arr, 'Status updated successfully.');
                                }
                                else{
                                    return $this->sendError('Saving error.');
                                }
                            }
                            else{
                                return $this->sendError('Status is last.');
                            }
                            break;
                        }
                    }
                }
                else{
                    return $this->sendError('Tracking number not found.');
                }
            } 
        }
        else{
            return $this->sendError('Token error.');
        }                                 
    }
    

    /**
     * Edit the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $tracking
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $tracking)
    {
        if ($this->checkToken($request->token) && $request->token){
            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            if ($input['tracking_main']) {
                
                $check_tracking = NewWorksheet::where([
                    ['tracking_main', '=', $input['tracking_main']]
                ])->first();
                if($check_tracking) return $this->sendError('Main tracking number already exist.', ['tracking_main' => $input['tracking_main']]);
                
                $row = NewWorksheet::where([
                    ['tracking_transit', '=', $tracking]
                ])
                ->get();

                if ($row->count()) {
                    $sheet = NewWorksheet::find($row[0]->id);
                    if (!$sheet->tracking_main) {
                        $sheet->tracking_main = $input['tracking_main'];
                        $sheet->status = $this->ru_arr[0];
                        $sheet->status_en = $this->en_arr[$this->ru_arr[0]];
                        $sheet->status_he = $this->he_arr[$this->ru_arr[0]];
                        $sheet->status_ua = $this->ua_arr[$this->ru_arr[0]];
                        $sheet->status_date = date('Y-m-d');
                    }
                    else{
                        return $this->sendError('Main tracking number already exist.', ['tracking_main' => $sheet->tracking_main]);
                    }                     
                    
                    if ($sheet->save()) {
                        return $this->sendResponse($sheet->toArray(), 'Main tracking number created successfully.');
                    }
                    else{
                        return $this->sendError('Saving error.');
                    }
                }
                else{
                    $sheet = new NewWorksheet;
                    $sheet->tracking_main = $input['tracking_main'];
                    $sheet->tracking_transit = $tracking;
                    $sheet->status = $this->ru_arr[0];
                    $sheet->status_en = $this->en_arr[$this->ru_arr[0]];
                    $sheet->status_he = $this->he_arr[$this->ru_arr[0]];
                    $sheet->status_ua = $this->ua_arr[$this->ru_arr[0]];
                    $sheet->status_date = date('Y-m-d');
                    if ($sheet->save()) {
                        return $this->sendResponse($sheet->toArray(), 'Row created successfully.');
                    }
                    else{
                        return $this->sendError('Saving error.');
                    }
                }
            }
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    /**
     * Login and get token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {        
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $user->api_token = Hash::make(mt_rand(8,15));
            $user->save();
            
            return response()->json([
                'data' => $user->toArray(),
            ]);
        }

        return $this->sendFailedLoginResponse($request);
    }


    /**
     * Adding tracking number by phone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addTrackingByPhone(Request $request)
    {    
        if ($this->checkToken($request->token) && $request->token){    

            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'standard_phone' => 'required',
                'site_name' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            if (!$this->trackingValidate($input['tracking_main'])) return $this->sendError('The tracking number is invalid. Please try again.');

            $check_tracking = NewWorksheet::where([
                ['tracking_main', '=', $input['tracking_main']]
            ])->first();
            if (!$check_tracking) {
                $check_tracking = CourierDraftWorksheet::where([
                    ['tracking_main', '=', $input['tracking_main']]
                ])->first(); 
            }
            if($check_tracking) return $this->sendError('Main tracking number already exist.', ['tracking_main' => $input['tracking_main']]);

            $standard_phone = ltrim(urldecode($input['standard_phone']), " \+");

            $this_tracking = NewWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['tracking_main', $input['tracking_main']]
            ])->get();
            if (!$this_tracking->count()) {
                $this_tracking = CourierDraftWorksheet::where([
                    ['standard_phone', '+'.$standard_phone],
                    ['tracking_main', $input['tracking_main']]
                ])->get();
            } 

            if ($this_tracking->count()) {
                return $this->sendError('This tracking number is exist!');
            }           

            $data = NewWorksheet::where('standard_phone', '+'.$standard_phone)
            ->get();
            if (!$data->count()) {
                $data = CourierDraftWorksheet::where('standard_phone', '+'.$standard_phone)
                ->get();
            }

            if ($data->count()) {                
                // Adding order number
                $this->addingOrderNumber($standard_phone, 'ru');
            }            

            $empty_tracking = NewWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['tracking_main', null]
            ])->get();
            if (!$empty_tracking->count()) {
                $empty_tracking = CourierDraftWorksheet::where([
                    ['standard_phone', '+'.$standard_phone],
                    ['tracking_main', null]
                ])->get();
            }

            $number_of_empty = $empty_tracking->count();

            if ($number_of_empty > 1) {
                return $this->sendResponse($empty_tracking->toArray(), 'Found multiple orders.');
            }
            else if($number_of_empty == 1){

                $empty_tracking->last()->update(['tracking_main'=> $input['tracking_main']]);
                $empty_tracking->last()->update(['status'=> $this->ru_arr[0]]);
                $empty_tracking->last()->update(['status_en'=> $this->en_arr[$this->ru_arr[0]]]);
                $empty_tracking->last()->update(['status_he'=> $this->he_arr[$this->ru_arr[0]]]);
                $empty_tracking->last()->update(['status_ua'=> $this->ua_arr[$this->ru_arr[0]]]);
                $empty_tracking->last()->update(['status_date'=> date('Y-m-d')]);

                $message = 'Row updated successfully.';
                $date_result = (strtotime('2021-09-20') <= strtotime(str_replace('.', '-', $empty_tracking->last()->date)));
                if ($date_result) {
                    $check_result = $this->checkReceipt($empty_tracking->last()->id, null, 'ru', $input['tracking_main']);                    
                    if ($check_result) {
                        $message .= ' '.$check_result;
                    }
                }
                                
                return $this->sendResponse($empty_tracking->last()->toArray(), $message);
            }
            else{
                return $this->sendError('Main tracking number by this phone already exist.', ['tracking_main' => $input['tracking_main']]);
            } 
        }
        else{
            return $this->sendError('Token error.');
        }          
    } 


    /**
     * Adding tracking number by phone number with order number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addTrackingByPhoneWithOrder(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token){
            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'standard_phone' => 'required',
                'order_number' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            if (!$this->trackingValidate($input['tracking_main'])) return $this->sendError('The tracking number is invalid. Please try again.');

            $check_tracking = NewWorksheet::where([
                ['tracking_main', '=', $input['tracking_main']]
            ])->first();
            if (!$check_tracking) {
                $check_tracking = CourierDraftWorksheet::where([
                    ['tracking_main', '=', $input['tracking_main']]
                ])->first(); 
            }
            if($check_tracking) return $this->sendError('Main tracking number already exist.', ['tracking_main' => $input['tracking_main']]);

            $standard_phone = ltrim(urldecode($input['standard_phone']), " \+");

            $data = NewWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['order_number', $input['order_number']]
            ])->get()->first();
            if (!$data->count()) {
                $data = CourierDraftWorksheet::where([
                    ['standard_phone', '+'.$standard_phone],
                    ['order_number', $input['order_number']]
                ])->get()->first();
            }

            if ($data->tracking_main) {
                return $this->sendError('Main tracking number by this phone already exist.', ['tracking_main' => $input['tracking_main']]);
            }

            $data->update(['tracking_main'=> $input['tracking_main']]);
            $data->update(['status'=> $this->ru_arr[0]]);
            $data->update(['status_en'=> $this->en_arr[$this->ru_arr[0]]]);
            $data->update(['status_he'=> $this->he_arr[$this->ru_arr[0]]]);
            $data->update(['status_ua'=> $this->ua_arr[$this->ru_arr[0]]]);
            $data->update(['status_date'=> date('Y-m-d')]);

            $message = 'Row updated successfully.';
            $date_result = (strtotime('2021-09-20') <= strtotime(str_replace('.', '-', $data->date)));
            if ($date_result) {
                $check_result = $this->checkReceipt($data->id, null, 'ru', $input['tracking_main']);                
                if ($check_result) {
                    $message .= ' '.$check_result;
                }
            }                       
            
            return $this->sendResponse($data->toArray(), $message);
        }
        else{
            return $this->sendError('Token error.');
        } 
    }  


    /**
     * Adding batch number and updating status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addBatchNumber(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token){
            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'batch_number' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $tracking = $input['tracking_main'];
            $sheet = NewWorksheet::where('tracking_main', $tracking)->first();
            if (!$sheet) $sheet = CourierDraftWorksheet::where('tracking_main', $tracking)->first();

            if ($sheet) {                    
                $ru_status = $this->ru_arr[3];
                $sheet->status = $ru_status;
                $sheet->batch_number = $input['batch_number'];
                $sheet->status_en = $this->en_arr[$ru_status];
                $sheet->status_he = $this->he_arr[$ru_status];
                $sheet->status_ua = $this->ua_arr[$ru_status];
                $sheet->status_date = date('Y-m-d');
                if ($sheet->save()) {
                    // Update Warehouse lot
                    $this->updateWarehouseLot($sheet->tracking_main, $sheet->batch_number, 'ru');
                    return $this->sendResponse($sheet->toArray(), 'Status updated successfully. Batch number added.');
                }
                else{
                    return $this->sendError('Saving error.');
                }              
            }
            else{
                return $this->sendError('Tracking number not found.');
            }
        }
        else{
            return $this->sendError('Token error.');
        } 
    }  


    /**
     * Adding pallet number and updating status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addPalletNumber(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token){
            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'pallet_number' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $tracking = $input['tracking_main'];
            $sheet = NewWorksheet::where('tracking_main', $tracking)->first();
            if (!$sheet) $sheet = CourierDraftWorksheet::where('tracking_main', $tracking)->first();

            if ($sheet) {
                $old_pallet = $sheet->pallet_number;                     
                $ru_status = $this->ru_arr[1];
                $sheet->status = $ru_status;
                $sheet->pallet_number = $input['pallet_number'];
                $sheet->status_en = $this->en_arr[$ru_status];
                $sheet->status_he = $this->he_arr[$ru_status];
                $sheet->status_ua = $this->ua_arr[$ru_status];
                $sheet->status_date = date('Y-m-d');
                if ($sheet->save()) {
                    // Update Warehouse pallet
                    $message = $this->updateWarehousePallet($sheet->tracking_main, $sheet->tracking_main, $old_pallet, $input['pallet_number'], $sheet->batch_number, $sheet->batch_number, 'ru', $sheet);
                    if ($message) {
                        return $this->sendError('Pallet number is not correct');
                    } 
                    return $this->sendResponse($sheet->toArray(), 'Status updated successfully. Pallet number added.');
                }
                else{
                    return $this->sendError('Saving error.');
                }                        
            }
            else{
                return $this->sendError('Tracking number not found.');
            }
        }
        else{
            return $this->sendError('Token error.');
        } 
    }  


    /**
     * Store a newly created resource in storage for clients
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }
}