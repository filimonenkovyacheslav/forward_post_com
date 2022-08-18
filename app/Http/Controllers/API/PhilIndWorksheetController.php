<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\PhilIndWorksheet;
use App\EngDraftWorksheet;
use App\CourierEngDraftWorksheet;
use Validator;


class PhilIndWorksheetController extends BaseController
{
    // Worksheet status
    private $en_arr = ["Forwarding to the warehouse in the sender country", "At the warehouse in the sender country", "At the customs in the sender country", "Forwarding to the receiver country", "At the customs in the receiver country", "Forwarding to the receiver", "Delivered"];
    private $ru_arr = [
        "Forwarding to the warehouse in the sender country" => "Доставляется на склад в стране отправителя",
        "At the warehouse in the sender country" => "На складе в стране отправителя",
        "At the customs in the sender country" => "На таможне в стране отправителя",
        "Forwarding to the receiver country" => "Доставляется в страну получателя",
        "At the customs in the receiver country" => "На таможне в стране получателя",
        "Forwarding to the receiver" => "Доставляется получателю",
        "Delivered" => "Доставлено"
    ];
    private $he_arr = [
        "Forwarding to the warehouse in the sender country" => "נשלח למחסן במדינת השולח",
        "At the warehouse in the sender country" => "במחסן במדינת השולח",
        "At the customs in the sender country" => " במכס במדינת השולח",
        "Forwarding to the receiver country" => " נשלח למדינת המקבל",
        "At the customs in the receiver country" => " במכס במדינת המקבל",
        "Forwarding to the receiver" => " נמסר למקבל",
        "Delivered" => " נמסר"
    ];
    private $message_arr = ['ru' => '', 'en' => '', 'he' => ''];


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

            $row = PhilIndWorksheet::select('status','status_ru','status_he')
            ->where([
                ['tracking_main', '=', $tracking]
            ])
            ->get();

            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {                    
                        $this->message_arr['ru'] = $val->status_ru;
                    }
                    if ($val->status) {
                        $this->message_arr['en'] = $val->status;
                    }
                    if ($val->status) {
                        $this->message_arr['he'] = $val->status_he;
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
     * Display the specified resource for clients
     *
     * @param  string  $tracking
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStatusEng(Request $request, $tracking)
    {    
        if ($this->checkToken($request->token) && $request->token) {

            $row = PhilIndWorksheet::select('status','status_ru','status_he')
            ->where([
                ['tracking_main', '=', $tracking]
            ])
            ->get();

            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {                    
                        $this->message_arr['ru'] = $val->status_ru;
                    }
                    if ($val->status) {
                        $this->message_arr['en'] = $val->status;
                    }
                    if ($val->status) {
                        $this->message_arr['he'] = $val->status_he;
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
                $row = PhilIndWorksheet::where([
                    ['tracking_main', '=', $tracking]
                ])
                ->get();

                if ($row->count()) {
                    $sheet = PhilIndWorksheet::find($row[0]->id);
                    foreach ($row as $val) {
                        if ($val->status) {                      
                            $key_num = array_search($val->status, $this->en_arr);
                            if ((int)$key_num < 6) {
                                $en_status = $this->en_arr[(int)$key_num+1];
                                $sheet->status = $en_status;
                                $this->message_arr['en'] = $en_status;
                                $sheet->status_ru = $this->ru_arr[$en_status];
                                $this->message_arr['ru'] = $this->ru_arr[$en_status];
                                $sheet->status_he = $this->he_arr[$en_status];
                                $this->message_arr['he'] = $this->he_arr[$en_status];
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

                $check_tracking = PhilIndWorksheet::where([
                    ['tracking_main', '=', $input['tracking_main']]
                ])->first();
                if($check_tracking) return $this->sendError('Main tracking number already exist.', ['tracking_main' => $input['tracking_main']]);
                
                $row = PhilIndWorksheet::where([
                    ['tracking_local', '=', $tracking]
                ])
                ->get();

                if ($row->count()) {
                    $sheet = PhilIndWorksheet::find($row[0]->id);
                    if (!$sheet->tracking_main) {
                        $sheet->tracking_main = $input['tracking_main'];
                        $sheet->status = $this->en_arr[0];
                        $sheet->status_ru = $this->ru_arr[$this->en_arr[0]];
                        $sheet->status_he = $this->he_arr[$this->en_arr[0]];
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
                    $sheet = new PhilIndWorksheet;
                    $sheet->tracking_main = $input['tracking_main'];
                    $sheet->tracking_local = $tracking;
                    $sheet->status = $this->en_arr[0];
                    $sheet->status_ru = $this->ru_arr[$this->en_arr[0]];
                    $sheet->status_he = $this->he_arr[$this->en_arr[0]];
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
     * Adding tracking number by phone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addTrackingByPhoneEng(Request $request)
    {    
        if ($this->checkToken($request->token) && $request->token){    

            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'standard_phone' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            if (!$this->trackingValidate($input['tracking_main'])) return $this->sendError('The tracking number is invalid. Please try again.');

            $check_tracking = PhilIndWorksheet::where([
                ['tracking_main', '=', $input['tracking_main']]
            ])->first();
            if (!$check_tracking) {
                $check_tracking = CourierEngDraftWorksheet::where([
                    ['tracking_main', '=', $input['tracking_main']]
                ])->first();
            }
            if($check_tracking) return $this->sendError('Main tracking number already exist.', ['tracking_main' => $input['tracking_main']]);

            $standard_phone = ltrim(urldecode($input['standard_phone']), " \+");

            $this_tracking = PhilIndWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['tracking_main', $input['tracking_main']]
            ])->get();
            if (!$this_tracking->count()) {
                $this_tracking = CourierEngDraftWorksheet::where([
                    ['standard_phone', '+'.$standard_phone],
                    ['tracking_main', $input['tracking_main']]
                ])->get();
            }

            if ($this_tracking->count()) {
                return $this->sendError('This tracking number is exist!');
            }           

            $data = PhilIndWorksheet::where('standard_phone', '+'.$standard_phone)->get();
            if (!$data->count()) {
                $data = CourierEngDraftWorksheet::where('standard_phone', '+'.$standard_phone)->get();
            }

            if ($data->count()) {                
                // Adding order number
                $this->addingOrderNumber($standard_phone, 'en');
            }           

            $empty_tracking = PhilIndWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['tracking_main', null]
            ])->get();
            if (!$empty_tracking->count()) {
                $empty_tracking = CourierEngDraftWorksheet::where([
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
                $empty_tracking->last()->update(['status'=> $this->en_arr[0]]);
                $empty_tracking->last()->update(['status_ru'=> $this->ru_arr[$this->en_arr[0]]]);
                $empty_tracking->last()->update(['status_he'=> $this->he_arr[$this->en_arr[0]]]);
                $empty_tracking->last()->update(['status_date'=> date('Y-m-d')]);

                $message = 'Row updated successfully.';
                $date_result = (strtotime('2021-09-20') <= strtotime(str_replace('.', '-', $empty_tracking->last()->date)));
                if ($date_result) {
                    $check_result = $this->checkReceipt($empty_tracking->last()->id, null, 'en', $input['tracking_main']);
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
    public function addTrackingByPhoneWithOrderEng(Request $request)
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

            $check_tracking = PhilIndWorksheet::where([
                ['tracking_main', '=', $input['tracking_main']]
            ])->first();
            if (!$check_tracking) {
                $check_tracking = CourierEngDraftWorksheet::where([
                    ['tracking_main', '=', $input['tracking_main']]
                ])->first();
            }
            if($check_tracking) return $this->sendError('Main tracking number already exist.', ['tracking_main' => $input['tracking_main']]);

            $standard_phone = ltrim(urldecode($input['standard_phone']), " \+");

            $data = PhilIndWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['order_number', $input['order_number']]
            ])->get()->first();
            if (!$data) {
                $data = CourierEngDraftWorksheet::where([
                    ['standard_phone', '+'.$standard_phone],
                    ['order_number', $input['order_number']]
                ])->get()->first();
            }

            if ($data->tracking_main) {
                return $this->sendError('Main tracking number by this phone already exist.', ['tracking_main' => $input['tracking_main']]);
            }

            $data->update(['tracking_main'=> $input['tracking_main']]);
            $data->update(['status'=> $this->en_arr[0]]);
            $data->update(['status_ru'=> $this->ru_arr[$this->en_arr[0]]]);
            $data->update(['status_he'=> $this->he_arr[$this->en_arr[0]]]);
            $data->update(['status_date'=> date('Y-m-d')]);

            $message = 'Row updated successfully.';
            $date_result = (strtotime('2021-09-20') <= strtotime(str_replace('.', '-', $data->date)));
            if ($date_result) {
                $check_result = $this->checkReceipt($data->id, null, 'en', $input['tracking_main']);
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
    public function addBatchNumberEng(Request $request)
    {               
        if ($this->checkToken($request->token) && $request->token) {

            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'batch_number' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $tracking = $input['tracking_main'];            
            $sheet = PhilIndWorksheet::where('tracking_main', $tracking)->first();
            if (!$sheet) $sheet = CourierEngDraftWorksheet::where('tracking_main', $tracking)->first();

            if ($sheet) {                   
                $en_status = $this->en_arr[3];
                $sheet->status = $en_status;
                $sheet->status_ru = $this->ru_arr[$en_status];
                $sheet->status_he = $this->he_arr[$en_status];
                $sheet->status_date = date('Y-m-d');
                $sheet->lot = $input['batch_number'];
                if ($sheet->save()) {
                    // Update Warehouse lot
                    $this->updateWarehouseLot($sheet->tracking_main, $sheet->lot, 'en');
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
    public function addPalletNumberEng(Request $request)
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
            $sheet = PhilIndWorksheet::where('tracking_main', $tracking)->first();
            if (!$sheet) $sheet = CourierEngDraftWorksheet::where('tracking_main', $tracking)->first();

            if ($sheet) {               
                $old_pallet = $sheet->pallet_number;                     
                $en_status = $this->en_arr[1];
                $sheet->status = $en_status;
                $sheet->status_ru = $this->ru_arr[$en_status];
                $sheet->status_he = $this->he_arr[$en_status];
                $sheet->status_date = date('Y-m-d');
                $sheet->pallet_number = $input['pallet_number'];
                if ($sheet->save()) {
                    // Update Warehouse pallet
                    $message = $this->updateWarehousePallet($sheet->tracking_main, $sheet->tracking_main, $old_pallet, $input['pallet_number'], $sheet->lot, $sheet->lot, 'en', $sheet);
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