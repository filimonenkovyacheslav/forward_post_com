<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\PackingEngNew;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use App\Receipt;
use App\Exports\ReceiptExport;
use App\ReceiptArchive;
use DB;
use Excel;

class AdminController extends Controller
{
	const ROLES_ARR = array('admin' => 'admin', 'user' => 'user', 'warehouse' => 'warehouse', 'office_1' => 'office_1','office_ru' => 'office_ru', 'office_agent_ru' => 'office_agent_ru', 'viewer' => 'viewer', 'china_admin' => 'china_admin', 'china_viewer' => 'china_viewer', 'office_eng' => 'office_eng', 'office_ind' => 'office_ind', 'viewer_eng' => 'viewer_eng', 'viewer_1' => 'viewer_1', 'viewer_2' => 'viewer_2', 'viewer_3' => 'viewer_3', 'viewer_4' => 'viewer_4', 'viewer_5' => 'viewer_5', 'courier' => 'courier');
	const VIEWER_ARR = array('viewer_1', 'viewer_2', 'viewer_3', 'viewer_4', 'viewer_5');
	private $ru_status_arr = ["Возврат", "Коробка", "Забрать", "Уточнить", "Думают", "Отмена", "Подготовка", "Дубль","Пакинг лист"];
	private $en_status_arr = ["Pending", "Return", "Box", "Pick up", "Specify", "Think", "Canceled", "Double","Packing list"];
	private $ru_status_arr_2 = ["Доставляется на склад в стране отправителя", "Возврат", "Коробка", "Забрать", "Уточнить", "Думают", "Отмена", "Подготовка", "Дубль","Пакинг лист"];
	private $en_status_arr_2 = ["Forwarding to the warehouse in the sender country", "Pending", "Return", "Box", "Pick up", "Specify", "Think", "Canceled", "Double","Packing list"];
	private $ru_status_arr_3 = ["На таможне в стране отправителя", "На складе в стране отправителя", "Доставляется на склад в стране отправителя", "Возврат", "Коробка", "Забрать", "Уточнить", "Думают", "Отмена", "Подготовка", "Дубль","Пакинг лист"];
	private $en_status_arr_3 = ["At the customs in the sender country", "At the warehouse in the sender country", "Forwarding to the warehouse in the sender country", "Pending", "Return", "Box", "Pick up", "Specify", "Think", "Canceled", "Double","Packing list"];
	private $ru_status_arr_4 = ["На таможне в стране отправителя", "На складе в стране отправителя", "Доставляется в страну получателя", "На таможне в стране получателя"];
	private $en_status_arr_4 = ["At the customs in the sender country", "At the warehouse in the sender country", "Forwarding to the receiver country", "At the customs in the receiver country"];


    protected function checkRowColor(Request $request)
    {
        $which_admin = $request->input('which_admin');
        $row_arr = $request->input('row_id');
        $old_color_arr = $request->input('old_color');

        for ($i=0; $i < count($row_arr); $i++) { 
            if ($which_admin === 'ru') {
            	if ($old_color_arr[$i] === 'tr-orange') {
                	
                	$worksheet = NewWorksheet::find($row_arr[$i]);             
                    $error_message = 'Заполните обязателные поля в строке с телефоном отправителя '.$worksheet->standard_phone.': ';

                    if (!$worksheet->sender_name) $error_message .= 'Отправитель,';
                    if (!$worksheet->standard_phone) $error_message .= 'Телефон (стандарт),';
                    if (!$worksheet->recipient_name) $error_message .= 'Получатель,';
                    if (!$worksheet->recipient_city) $error_message .= 'Город получателя,';
                    if (!$worksheet->recipient_street) $error_message .= 'Улица получателя,';
                    if (!$worksheet->recipient_house) $error_message .= '№ дома получателя,';
                    if (!$worksheet->recipient_room) $error_message .= '№ кв. получателя,';
                    if (!$worksheet->recipient_phone) $error_message .= 'Телефон получателя,';

                    if ($error_message !== 'Заполните обязателные поля в строке с телефоном отправителя '.$worksheet->standard_phone.': ') {
                    	return response()->json(['error' => $error_message]);
                    }
                }             
            }
            elseif ($which_admin === 'en') {
            	if ($old_color_arr[$i] === 'tr-orange') {
            		
            		$worksheet = PhilIndWorksheet::find($row_arr[$i]);
            		$packing = PackingEngNew::where('work_sheet_id',$row_arr[$i])->first();
            		$country = '';
            		$error_message = 'Fill in required fields with shipper phone '.$worksheet->standard_phone.': ';

            		if ($packing) $country = $packing->country;
            		if (!$country) $country = $worksheet->consignee_country; 

            		if ($country && $country === 'India') {
            			if (!$worksheet->shipper_name) $error_message .= 'Shipper\'s name,';
            			if (!$worksheet->shipper_address) $error_message .= 'Shipper\'s address,';
            			if (!$worksheet->standard_phone) $error_message .= 'Shipper\'s phone (standard),';
            			if (!$worksheet->consignee_name) $error_message .= 'Consignee\'s name,';
            			if (!$worksheet->consignee_address) $error_message .= 'Consignee\'s address,';
            			if (!$worksheet->consignee_phone) $error_message .= 'Consignee\'s phone number,';
            			if (!$worksheet->house_name) $error_message .= 'House name,';
            			if (!$worksheet->state_pincode) $error_message .= 'State pincode,';
            			if (!$worksheet->post_office) $error_message .= 'Local post office,';
            			if (!$worksheet->district) $error_message .= 'District/City,';

            			if ($error_message !== 'Fill in required fields with shipper phone '.$worksheet->standard_phone.': ') {
            				return response()->json(['error' => $error_message]);
            			}			
            		}
            		elseif ($country && $country === 'Nepal') {
            			if (!$worksheet->shipper_name) $error_message .= 'Shipper\'s name,';
            			if (!$worksheet->shipper_address) $error_message .= 'Shipper\'s address,';
            			if (!$worksheet->standard_phone) $error_message .= 'Shipper\'s phone (standard),';
            			if (!$worksheet->consignee_name) $error_message .= 'Consignee\'s name,';
            			if (!$worksheet->consignee_address) $error_message .= 'Consignee\'s address,';
            			if (!$worksheet->consignee_phone) $error_message .= 'Consignee\'s phone number,';

            			if ($error_message !== 'Fill in required fields with shipper phone '.$worksheet->standard_phone.': ') {
            				return response()->json(['error' => $error_message]);
            			}
            		}
            		elseif ($country) {
            			if (!$worksheet->shipper_name) $error_message .= 'Shipper\'s name,';
            			if (!$worksheet->shipper_city) $error_message .= 'Shipper\'s city,';
            			if (!$worksheet->shipper_address) $error_message .= 'Shipper\'s address,';
            			if (!$worksheet->standard_phone) $error_message .= 'Shipper\'s phone (standard),';
            			if (!$worksheet->consignee_name) $error_message .= 'Consignee\'s name,';
            			if (!$worksheet->consignee_address) $error_message .= 'Consignee\'s address,';
            			if (!$worksheet->consignee_phone) $error_message .= 'Consignee\'s phone number,';

            			if ($error_message !== 'Fill in required fields with shipper phone '.$worksheet->standard_phone.': ') {
            				return response()->json(['error' => $error_message]);
            			}
            		}
            	}           	
            }
        }
        
        return response()->json(['success' => 'success']);
    }


    public function generalSearchShow()
    {
        $title = 'General Search';
        return view('admin.general_search', compact('title'));
    }


    public function generalSearch(Request $request)
    {
    	$adapted_column = ['comments_1' => 'comment_2','comments_2' => 'comments','shipper_name' => 'sender_name','shipper_country' => 'sender_country','shipper_city' => 'sender_city','passport_number' => 'sender_passport','shipper_address' => 'sender_address','shipper_phone' => 'sender_phone','consignee_name' => 'recipient_name','consignee_country' => 'recipient_country','house_name' => 'recipient_house','post_office' => 'recipient_postcode','consignee_phone' => 'recipient_phone','consignee_id' => 'recipient_passport','shipped_items' => 'package_content','shipment_val' => 'package_cost','delivery_date_comments' => 'pick_up_date','lot' => 'batch_number','payment_date_comments' => 'pay_date','amount_payment' => 'pay_sum'];
        $search = $request->table_filter_value;
        $column = $request->table_columns;
        $message = '';         

        if ($column) {
        	if (Schema::hasColumn('courier_draft_worksheet', $column)) {
        		$result = CourierDraftWorksheet::where($column, 'like', '%'.$search.'%')->first();
        		if ($result) $message .= 'Черновик содержит значение,';
        	}
            elseif (array_key_exists($column, $adapted_column)) {
            	$result = CourierDraftWorksheet::where($adapted_column[$column], 'like', '%'.$search.'%')->first();
        		if ($result) $message .= 'Черновик содержит значение,';
            }

            if (Schema::hasColumn('new_worksheet', $column)) {
        		$result = NewWorksheet::where($column, 'like', '%'.$search.'%')->first();
        		if ($result) $message .= ' Новый рабочий лист содержит значение,';
        	}
            elseif (array_key_exists($column, $adapted_column)) {
            	$result = NewWorksheet::where($adapted_column[$column], 'like', '%'.$search.'%')->first();
        		if ($result) $message .= ' Новый рабочий лист содержит значение,';
            }

            if (Schema::hasColumn('courier_eng_draft_worksheet', $column)) {
        		$result = CourierEngDraftWorksheet::where($column, 'like', '%'.$search.'%')->first();
        		if ($result) $message .= ' Draft contains value,';
        	}

        	if (Schema::hasColumn('phil_ind_worksheet', $column)) {
        		$result = PhilIndWorksheet::where($column, 'like', '%'.$search.'%')->first();
        		if ($result) $message .= ' Worksheet contains value';
        	}

        	if (!$message) {
        		$message .= ' Worksheets and Drafts don\'t contain value';
        		return redirect()->to(session('this_previous_url'))->with('status-error', $message);
        	}
            
            return redirect()->to(session('this_previous_url'))->with('status', $message);
        }
        else
        	return redirect()->to(session('this_previous_url'))->with('status-error', 'Choose column!');
    }
	
	
	protected function checkStatus($table, $id, $status)
	{
		switch ($table) {
			
			case "new_worksheet":

			$worksheet = NewWorksheet::find($id);
			if (!$worksheet->tracking_main && !in_array($status, $this->ru_status_arr)) {
				return 'Status cannot be higher than "Забрать" without tracking number!';
			}
			elseif (!$worksheet->getLastDocUniq() && $status === "Забрать") {
				return 'Status cannot be "Забрать" without PDF!';
			}			
			elseif ($worksheet->tracking_main && in_array($status, $this->ru_status_arr)) {
				return 'Status cannot be lower than "Доставляется на склад в стране отправителя" with tracking number!';
			}
			else return '';
		
			break;
			
			case "phil_ind_worksheet":

			$worksheet = PhilIndWorksheet::find($id);
			if (!$worksheet->tracking_main && !in_array($status, $this->en_status_arr)) {
				return 'Status cannot be higher than "Pick up" without tracking number!';
			}
			elseif (!$worksheet->getLastDocUniq() && $status === "Pick up") {
				return 'Status cannot be "Pick up" without PDF!';
			}
			elseif ($worksheet->tracking_main && in_array($status, $this->en_status_arr)) {
				return 'Status cannot be lower than "Forwarding to the warehouse in the sender country" with tracking number!';
			}
			else return '';

			break;

			case "courier_draft_worksheet":

			$worksheet = CourierDraftWorksheet::find($id);
			if (!$worksheet->tracking_main && !in_array($status, $this->ru_status_arr)) {
				return 'Status cannot be higher than "Забрать" without tracking number!';
			}
			elseif (!$worksheet->getLastDocUniq() && $status === "Забрать") {
				return 'Status cannot be "Забрать" without PDF!';
			}
			elseif ($worksheet->tracking_main && in_array($status, $this->ru_status_arr)) {
				return 'Status cannot be lower than "Доставляется на склад в стране отправителя" with tracking number!';
			}
			else return '';
		
			break;
			
			case "courier_eng_draft_worksheet":

			$worksheet = CourierEngDraftWorksheet::find($id);
			if (!$worksheet->tracking_main && !in_array($status, $this->en_status_arr)) {
				return 'Status cannot be higher than "Pick up" without tracking number!';
			}
			elseif (!$worksheet->getLastDocUniq() && $status === "Pick up") {
				return 'Status cannot be "Pick up" without PDF!';
			}
			elseif ($worksheet->tracking_main && in_array($status, $this->en_status_arr)) {
				return 'Status cannot be lower than "Forwarding to the warehouse in the sender country" with tracking number!';
			}
			else return '';

			break;
		}
	}


	protected function checkTracking($table, $tracking, $id)
	{
		$status_error = '';
		if (!$this->trackingValidate($tracking)){
			$status_error = "Tracking number is not correct.";
			return $status_error;
		}
		
		switch ($table) {
			
			case "new_worksheet":

			$check_tracking = NewWorksheet::where([
				['tracking_main', '=', $tracking],
				['id', '<>', $id]
			])->first();

			if (!$check_tracking) {
				$check_tracking = CourierDraftWorksheet::where([
					['tracking_main', '=', $tracking]
				])->first();

			}
			
			if($check_tracking) $status_error = 'ВНИМАНИЕ! В СИСТЕМЕ УЖЕ СУЩЕСТВУЕТ ТАКОЙ ТРЕКИНГ-НОМЕР. ИСПРАВЬТЕ ОШИБОЧНУЮ ЗАПИСЬ ИЛИ ВНЕСИТЕ ДРУГОЙ НОМЕР!';
		
			break;
			
			case "phil_ind_worksheet":
			
			$check_tracking = PhilIndWorksheet::where([
				['tracking_main', '=', $tracking],
				['id', '<>', $id]
			])->first();

			if (!$check_tracking) {
				$check_tracking = CourierEngDraftWorksheet::where([
					['tracking_main', '=', $tracking]
				])->first();
			}			
			
			if($check_tracking) $status_error = 'WARNING! THE TRACKING NUMBER ALREADY EXISTS. FIX THE DEFECT RECORD OR CHANGE THE TRACKING NUMBER';
			
			break;

			case "courier_draft_worksheet":

			$check_tracking = CourierDraftWorksheet::where([
				['tracking_main', '=', $tracking],
				['id', '<>', $id]
			])->first();

			if (!$check_tracking) {
				$check_tracking = NewWorksheet::where([
					['tracking_main', '=', $tracking]
				])->first();

			}

			$worksheet = CourierDraftWorksheet::find($id);
			
			if($check_tracking) $status_error = 'ВНИМАНИЕ! В СИСТЕМЕ УЖЕ СУЩЕСТВУЕТ ТАКОЙ ТРЕКИНГ-НОМЕР. ИСПРАВЬТЕ ОШИБОЧНУЮ ЗАПИСЬ ИЛИ ВНЕСИТЕ ДРУГОЙ НОМЕР!';
			elseif ($worksheet->tracking_main !== $tracking) $this->setTrackingToDocument($worksheet,$tracking);
		
			break;
			
			case "courier_eng_draft_worksheet":

			$check_tracking = CourierEngDraftWorksheet::where([
				['tracking_main', '=', $tracking],
				['id', '<>', $id]
			])->first();

			if (!$check_tracking) {
				$check_tracking = PhilIndWorksheet::where([
					['tracking_main', '=', $tracking]
				])->first();
			}

			$worksheet = CourierEngDraftWorksheet::find($id);
			
			if($check_tracking) $status_error = 'WARNING! THE TRACKING NUMBER ALREADY EXISTS. FIX THE DEFECT RECORD OR CHANGE THE TRACKING NUMBER';
			elseif ($worksheet->tracking_main !== $tracking) $this->setTrackingToDocument($worksheet,$tracking);

			break;
		}

		return $status_error;
	}


	protected function updateStatusByTracking($table, $worksheet)
	{
		$check_result = '';
		
		switch ($table) {
			
			case "new_worksheet":

			if (in_array($worksheet->status, $this->ru_status_arr_2)) {
				$check_result .= "ВНИМАНИЕ! ПРИ ДОБАВЛЕНИИ ТРЕКИНГ-НОМЕРА СТАТУС НЕ МОЖЕТ БЫТЬ - '$worksheet->status'. СТАТУС БУДЕТ ИЗМЕНЕН АВТОМАТИЧЕСКИ!";
				$worksheet->status = "На складе в стране отправителя";
				$worksheet->status_en = "At the warehouse in the sender country";
				$worksheet->status_he = "במחסן במדינת השולח";
				$worksheet->status_ua = "На складі в країні відправника";
				$worksheet->save();
			}
		
			break;
			
			case "phil_ind_worksheet":
			
			if (in_array($worksheet->status, $this->en_status_arr_2)){
				$check_result .= "WARNING! A STATUS CANNOT BE '$worksheet->status' AFTER ADDING A TRACKING NUMBER. THE STATUS WILL BE UPDATED BY THE SYSTEM!";
				$worksheet->status = "At the warehouse in the sender country";
				$worksheet->status_ru = "На складе в стране отправителя";
				$worksheet->status_he = "במחסן במדינת השולח";
				$worksheet->save();
			}
			
			break;

			case "courier_draft_worksheet":

			if (in_array($worksheet->status, $this->ru_status_arr_2)) {
				$check_result .= "ВНИМАНИЕ! ПРИ ДОБАВЛЕНИИ ТРЕКИНГ-НОМЕРА СТАТУС НЕ МОЖЕТ БЫТЬ - '$worksheet->status'. СТАТУС БУДЕТ ИЗМЕНЕН АВТОМАТИЧЕСКИ!";
				$worksheet->status = "На складе в стране отправителя";
				$worksheet->status_en = "At the warehouse in the sender country";
				$worksheet->status_he = "במחסן במדינת השולח";
				$worksheet->status_ua = "На складі в країні відправника";
				$worksheet->save();
			}
		
			break;
			
			case "courier_eng_draft_worksheet":

			if (in_array($worksheet->status, $this->en_status_arr_2)){
				$check_result .= "WARNING! A STATUS CANNOT BE '$worksheet->status' AFTER ADDING A TRACKING NUMBER. THE STATUS WILL BE UPDATED BY THE SYSTEM!";
				$worksheet->status = "At the warehouse in the sender country";
				$worksheet->status_ru = "На складе в стране отправителя";
				$worksheet->status_he = "במחסן במדינת השולח";
				$worksheet->save();
			}

			break;
		}

		return $check_result;
	}


	protected function checkColumns($arr, $value_by, $column, $check_column, $table){
		$status_error = '';

		switch ($table) {
			
			case "new_worksheet":

			if ($column === 'recipient_phone') {
				$status_error = $this->checkConsigneePhone($value_by, 'ru');
				if ($status_error) return $status_error;
			} 

			$check_sheet = NewWorksheet::where('in_trash',false)->whereIn($check_column, $arr)->whereIn('status',$this->ru_status_arr_2)->first();
			if ($check_sheet) {
				if ($column === 'pay_sum')
				{
					$status_error = "ВНИМАНИЕ! ПРИ ПОЛУЧЕНИИ ОПЛАТЫ СТАТУС НЕ МОЖЕТ БЫТЬ НИЖЕ - 'На складе в стране отправителя'. ДОБАВЬТЕ ЗАПИСЬ ОБ ОПЛАТЕ В ПРАВИЛЬНУЮ СТРОКУ ИЛИ ИЗМЕНИТЕ СТАТУС";
					return $status_error;
				}

				if ($column === 'pallet_number')
				{
					$status_error = "ВНИМАНИЕ! ПРИ ДОБАВЛЕНИИ НОМЕРА ПАЛЛЕТЫ СТАТУС НЕ МОЖЕТ БЫТЬ НИЖЕ - 'На складе в стране отправителя'. ДОБАВЬТЕ ЗАПИСЬ О НОМЕРЕ ПАЛЛЕТЫ В ПРАВИЛЬНУЮ СТРОКУ ИЛИ ИЗМЕНИТЕ СТАТУС";
					return $status_error;
				}

				if ($column === 'batch_number')
				{
					$status_error = "ВНИМАНИЕ! ПРИ ДОБАВЛЕНИИ НОМЕРА ПАРТИИ СТАТУС НЕ МОЖЕТ БЫТЬ НИЖЕ - 'На складе в стране отправителя'. ДОБАВЬТЕ ЗАПИСЬ О НОМЕРЕ ПАРТИИ В ПРАВИЛЬНУЮ СТРОКУ ИЛИ ИЗМЕНИТЕ СТАТУС";
					return $status_error;
				}
			}

			break;
			
			case "phil_ind_worksheet":

			if ($column === 'consignee_phone') {
				$status_error = $this->checkConsigneePhone($value_by, 'en');
				if ($status_error) return $status_error;
			} 

			$check_sheet = PhilIndWorksheet::where('in_trash',false)->whereIn($check_column, $arr)->whereIn('status',$this->en_status_arr_2)->first();
			if ($check_sheet) {
				if ($column === 'amount_payment')
				{
					$status_error = "WARNING! A STATUS CANNOT BE LOWER - 'At the warehouse in the sender country' AFTER PAYMENT. PLEASE ADD THE DATA TO A CORRECT RECORD OR UPDATE THE STATUS";
					return $status_error;
				}

				if ($column === 'pallet_number')
				{
					$status_error = "WARNING! A STATUS CANNOT BE LOWER - 'At the warehouse in the sender country' AFTER ADDING A PALLET NUMBER. PLEASE ADD THE DATA TO A CORRECT RECORD OR UPDATE THE STATUS";
					return $status_error;
				}

				if ($column === 'lot')
				{
					$status_error = "WARNING! A STATUS CANNOT BE LOWER - 'At the warehouse in the sender country' BEFORE ADDING A LOT. PLEASE ADD THE DATA TO A CORRECT RECORD OR UPDATE THE STATUS";
					return $status_error;
				}
			}

			break;
			
			case "courier_draft_worksheet":

			if ($column === 'recipient_phone') {
				$status_error = $this->checkConsigneePhone($value_by, 'ru');
				if ($status_error) return $status_error;
			} 

			$check_sheet = CourierDraftWorksheet::where('in_trash',false)->whereIn($check_column, $arr)->whereIn('status',$this->ru_status_arr_2)->first();
			if ($check_sheet) {
				if ($column === 'pay_sum')
				{
					$status_error = "ВНИМАНИЕ! ПРИ ПОЛУЧЕНИИ ОПЛАТЫ СТАТУС НЕ МОЖЕТ БЫТЬ НИЖЕ - 'На складе в стране отправителя'. ДОБАВЬТЕ ЗАПИСЬ ОБ ОПЛАТЕ В ПРАВИЛЬНУЮ СТРОКУ ИЛИ ИЗМЕНИТЕ СТАТУС";
					return $status_error;
				}

				if ($column === 'pallet_number')
				{
					$status_error = "ВНИМАНИЕ! ПРИ ДОБАВЛЕНИИ НОМЕРА ПАЛЛЕТЫ СТАТУС НЕ МОЖЕТ БЫТЬ НИЖЕ - 'На складе в стране отправителя'. ДОБАВЬТЕ ЗАПИСЬ О НОМЕРЕ ПАЛЛЕТЫ В ПРАВИЛЬНУЮ СТРОКУ ИЛИ ИЗМЕНИТЕ СТАТУС";
					return $status_error;
				}

				if ($column === 'batch_number')
				{
					$status_error = "ВНИМАНИЕ! ПРИ ДОБАВЛЕНИИ НОМЕРА ПАРТИИ СТАТУС НЕ МОЖЕТ БЫТЬ НИЖЕ - 'На складе в стране отправителя'. ДОБАВЬТЕ ЗАПИСЬ О НОМЕРЕ ПАРТИИ В ПРАВИЛЬНУЮ СТРОКУ ИЛИ ИЗМЕНИТЕ СТАТУС";
					return $status_error;
				}
			}

			break;

			case "courier_eng_draft_worksheet":
			
			if ($column === 'consignee_phone') {
				$status_error = $this->checkConsigneePhone($value_by, 'en');
				if ($status_error) return $status_error;
			} 

			$check_sheet = CourierEngDraftWorksheet::where('in_trash',false)->whereIn($check_column, $arr)->whereIn('status',$this->en_status_arr_2)->first();
			if ($check_sheet) {
				if ($column === 'amount_payment')
				{
					$status_error = "WARNING! A STATUS CANNOT BE LOWER - 'At the warehouse in the sender country' AFTER PAYMENT. PLEASE ADD THE DATA TO A CORRECT RECORD OR UPDATE THE STATUS";
					return $status_error;
				}

				if ($column === 'pallet_number')
				{
					$status_error = "WARNING! A STATUS CANNOT BE LOWER - 'At the warehouse in the sender country' AFTER ADDING A PALLET NUMBER. PLEASE ADD THE DATA TO A CORRECT RECORD OR UPDATE THE STATUS";
					return $status_error;
				}

				if ($column === 'lot')
				{
					$status_error = "WARNING! A STATUS CANNOT BE LOWER - 'At the warehouse in the sender country' BEFORE ADDING A LOT. PLEASE ADD THE DATA TO A CORRECT RECORD OR UPDATE THE STATUS";
					return $status_error;
				}
			}

			break;
		}
		
		return $status_error;
	}


    public function showPalletData(){
        
        $title = 'Mass change of data on pallets (supports mass selection of checkboxes)';
        $pallet_arr = NewWorksheet::where([
            ['in_trash',false],
            ['pallet_number','<>',null]
        ])
        ->whereIn('status',$this->ru_status_arr_4)
        ->pluck('pallet_number');
        $pallet_arr = $pallet_arr->merge(CourierDraftWorksheet::where([
            ['in_trash',false],
            ['pallet_number','<>',null]
        ])
        ->whereIn('status',$this->ru_status_arr_4)
        ->pluck('pallet_number'));
        $pallet_arr = $pallet_arr->merge(PhilIndWorksheet::where([
            ['in_trash',false],
            ['pallet_number','<>',null]
        ])
        ->whereIn('status',$this->en_status_arr_4)
        ->pluck('pallet_number'));
        $pallet_arr = $pallet_arr->merge(CourierEngDraftWorksheet::where([
            ['in_trash',false],
            ['pallet_number','<>',null]
        ])
        ->whereIn('status',$this->en_status_arr_4)
        ->pluck('pallet_number'));
        $pallet_arr = $pallet_arr->toArray();
        $pallet_arr = array_unique($pallet_arr);
        sort($pallet_arr);
        
        return view('admin.pallet_data', compact('title','pallet_arr'));
    }


    public function addPalletData(Request $request){
        $pallet_arr = $request->input('pallet');
        $value_by = $request->input('value-by-pallet');       
        
        $status_error = '';
        $check_column = 'pallet_number';        
        
        if ($pallet_arr) {

            $worksheet_pallet_exist = NewWorksheet::whereIn('pallet_number',$pallet_arr)->get()->count();
            $draft_pallet_exist = CourierDraftWorksheet::whereIn('pallet_number',$pallet_arr)->get()->count();
            $eng_worksheet_pallet_exist = PhilIndWorksheet::whereIn('pallet_number',$pallet_arr)->get()->count();
            $eng_draft_pallet_exist = CourierEngDraftWorksheet::whereIn('pallet_number',$pallet_arr)->get()->count();

            if ($worksheet_pallet_exist AND $eng_worksheet_pallet_exist) {
            	$status_error = 'These pallets cannot be in the same lot!';
            	return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
            }
            if ($worksheet_pallet_exist AND $eng_draft_pallet_exist) {
            	$status_error = 'These pallets cannot be in the same lot!';
            	return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
            }
            if ($draft_pallet_exist AND $eng_worksheet_pallet_exist) {
            	$status_error = 'These pallets cannot be in the same lot!';
            	return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
            }
            if ($draft_pallet_exist AND $eng_draft_pallet_exist) {
            	$status_error = 'These pallets cannot be in the same lot!';
            	return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
            }
            
            if ($worksheet_pallet_exist) {
                
                $column = 'batch_number';
                $old_lot_arr_tracking = [];
                $status_error = $this->checkColumns($pallet_arr, $value_by, $column, $check_column, 'new_worksheet');                
                if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);

                for ($i=0; $i < count($pallet_arr); $i++) { 
                    $worksheets = NewWorksheet::where('pallet_number',$pallet_arr[$i])->get();
                    foreach ($worksheets as $sheet) {
                        $this->toUpdatesArchive($request,$sheet);
                    }                   
                } 

                for ($i=0; $i < count($pallet_arr); $i++) { 
                    $worksheets = NewWorksheet::where('pallet_number',$pallet_arr[$i])->get();
                    foreach ($worksheets as $sheet) {
                        if ($sheet->batch_number !== $value_by) {
                            $old_lot_arr_tracking[] = $sheet->tracking_main;
                        }                       
                    }                    
                }
                
                NewWorksheet::whereIn('pallet_number', $pallet_arr)
                ->update([
                    $column => $value_by
                ]); 

                NewWorksheet::whereIn('pallet_number', $pallet_arr)
                ->whereIn('status',$this->ru_status_arr_3)
                ->update([
                    'status' => "Доставляется в страну получателя",
                    'status_en' => "Forwarding to the receiver country",
                    'status_he' => " נשלח למדינת המקבל",
                    'status_ua' => "Forwarding to the receiver country",
                    'status_date' => date('Y-m-d')
                ]);

                for ($i=0; $i < count($old_lot_arr_tracking); $i++) { 
                    $this->updateWarehouseLot($old_lot_arr_tracking[$i], $value_by, 'ru');
                }
            }      
            
            if ($draft_pallet_exist) {
                
                $column = 'batch_number';
                $old_lot_arr_tracking = [];
                $status_error = $this->checkColumns($pallet_arr, $value_by, $column, $check_column, 'courier_draft_worksheet');                
                if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);

                for ($i=0; $i < count($pallet_arr); $i++) { 
                    $worksheets = CourierDraftWorksheet::where('pallet_number',$pallet_arr[$i])->get();
                    foreach ($worksheets as $sheet) {
                        $this->toUpdatesArchive($request,$sheet);
                    }                   
                } 

                for ($i=0; $i < count($pallet_arr); $i++) { 
                    $worksheets = CourierDraftWorksheet::where('pallet_number',$pallet_arr[$i])->get();
                    foreach ($worksheets as $sheet) {
                        if ($sheet->batch_number !== $value_by) {
                            $old_lot_arr_tracking[] = $sheet->tracking_main;
                        }                       
                    }                    
                }
                
                CourierDraftWorksheet::whereIn('pallet_number', $pallet_arr)
                ->update([
                    $column => $value_by
                ]); 

                CourierDraftWorksheet::whereIn('pallet_number', $pallet_arr)
                ->whereIn('status',$this->ru_status_arr_3)
                ->update([
                    'status' => "Доставляется в страну получателя",
                    'status_en' => "Forwarding to the receiver country",
                    'status_he' => " נשלח למדינת המקבל",
                    'status_ua' => "Forwarding to the receiver country",
                    'status_date' => date('Y-m-d')
                ]);

                for ($i=0; $i < count($old_lot_arr_tracking); $i++) { 
                    $this->updateWarehouseLot($old_lot_arr_tracking[$i], $value_by, 'ru');
                }
            }  

            if ($eng_worksheet_pallet_exist) {
                
                $column = 'lot';
                $old_lot_arr_tracking = [];
                $status_error = $this->checkColumns($pallet_arr, $value_by, $column, $check_column, 'phil_ind_worksheet');                
                if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);

                for ($i=0; $i < count($pallet_arr); $i++) { 
                    $worksheets = PhilIndWorksheet::where('pallet_number',$pallet_arr[$i])->get();
                    foreach ($worksheets as $sheet) {
                        $this->toUpdatesArchive($request,$sheet);
                    }                   
                } 

                for ($i=0; $i < count($pallet_arr); $i++) { 
                    $worksheets = PhilIndWorksheet::where('pallet_number',$pallet_arr[$i])->get();
                    foreach ($worksheets as $sheet) {
                        if ($sheet->lot !== $value_by) {
                            $old_lot_arr_tracking[] = $sheet->tracking_main;
                        }                       
                    }                    
                }
                
                PhilIndWorksheet::whereIn('pallet_number', $pallet_arr)
                ->update([
                    $column => $value_by
                ]); 

                PhilIndWorksheet::whereIn('pallet_number', $pallet_arr)
                ->whereIn('status',$this->en_status_arr_3)
                ->update([
                	'status' => "Forwarding to the receiver country",
                	'status_ru' => "Доставляется в страну получателя",
                	'status_he' => " נשלח למדינת המקבל",
                    'status_date' => date('Y-m-d')
                ]);

                for ($i=0; $i < count($old_lot_arr_tracking); $i++) { 
                    $this->updateWarehouseLot($old_lot_arr_tracking[$i], $value_by, 'en');
                }
            }      
            
            if ($eng_draft_pallet_exist) {
                
                $column = 'lot';
                $old_lot_arr_tracking = [];
                $status_error = $this->checkColumns($pallet_arr, $value_by, $column, $check_column, 'courier_eng_draft_worksheet');                
                if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);

                for ($i=0; $i < count($pallet_arr); $i++) { 
                    $worksheets = CourierEngDraftWorksheet::where('pallet_number',$pallet_arr[$i])->get();
                    foreach ($worksheets as $sheet) {
                        $this->toUpdatesArchive($request,$sheet);
                    }                   
                } 

                for ($i=0; $i < count($pallet_arr); $i++) { 
                    $worksheets = CourierEngDraftWorksheet::where('pallet_number',$pallet_arr[$i])->get();
                    foreach ($worksheets as $sheet) {
                        if ($sheet->lot !== $value_by) {
                            $old_lot_arr_tracking[] = $sheet->tracking_main;
                        }                       
                    }                    
                }
                
                CourierEngDraftWorksheet::whereIn('pallet_number', $pallet_arr)
                ->update([
                    $column => $value_by
                ]); 

                CourierEngDraftWorksheet::whereIn('pallet_number', $pallet_arr)
                ->whereIn('status',$this->en_status_arr_3)
                ->update([
                	'status' => "Forwarding to the receiver country",
                	'status_ru' => "Доставляется в страну получателя",
                	'status_he' => " נשלח למדינת המקבל",
                    'status_date' => date('Y-m-d')
                ]);

                for ($i=0; $i < count($old_lot_arr_tracking); $i++) { 
                    $this->updateWarehouseLot($old_lot_arr_tracking[$i], $value_by, 'en');
                }
            }  
        }
        else
            $status_error = "Pallets not selected!";
        
        if($status_error){
            return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
        }
        else{
            return redirect()->to(session('this_previous_url'))->with('status', 'Rows changed successfully!');
        }       
    }


    private function updatePalletData()
    {

    }
	
	
	protected function new_columns(){

		$arr_columns = [];
		$new_column_1 = '';
        $new_column_2 = '';
        $new_column_3 = '';
        $new_column_4 = '';
        $new_column_5 = '';

		if (Schema::hasColumn('new_worksheet', 'new_column_1'))
		{
			$new_column_1 = 'Дополнительная колонка 1';
		}
		$arr_columns[] = $new_column_1;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_2'))
		{
			$new_column_2 = 'Дополнительная колонка 2';
		}
		$arr_columns[] = $new_column_2;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_3'))
		{
			$new_column_3 = 'Дополнительная колонка 3';
		}
		$arr_columns[] = $new_column_3;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_4'))
		{
			$new_column_4 = 'Дополнительная колонка 4';
		}
		$arr_columns[] = $new_column_4;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_5'))
		{
			$new_column_5 = 'Дополнительная колонка 5';
		}
		$arr_columns[] = $new_column_5;

		return $arr_columns;
	}


	protected function new_china_columns(){

		$arr_columns = [];
		$new_column_1 = '';
        $new_column_2 = '';
        $new_column_3 = '';
        $new_column_4 = '';
        $new_column_5 = '';

		if (Schema::hasColumn('china_worksheet', 'new_column_1'))
		{
			$new_column_1 = 'Additional column 1';
		}
		$arr_columns[] = $new_column_1;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_2'))
		{
			$new_column_2 = 'Additional column 2';
		}
		$arr_columns[] = $new_column_2;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_3'))
		{
			$new_column_3 = 'Additional column 3';
		}
		$arr_columns[] = $new_column_3;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_4'))
		{
			$new_column_4 = 'Additional column 4';
		}
		$arr_columns[] = $new_column_4;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_5'))
		{
			$new_column_5 = 'Additional column 5';
		}
		$arr_columns[] = $new_column_5;

		return $arr_columns;
	}


	protected function new_phil_ind_columns(){

		$arr_columns = [];
		$new_column_1 = '';
        $new_column_2 = '';
        $new_column_3 = '';
        $new_column_4 = '';
        $new_column_5 = '';

		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_1'))
		{
			$new_column_1 = 'Additional column 1';
		}
		$arr_columns[] = $new_column_1;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_2'))
		{
			$new_column_2 = 'Additional column 2';
		}
		$arr_columns[] = $new_column_2;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_3'))
		{
			$new_column_3 = 'Additional column 3';
		}
		$arr_columns[] = $new_column_3;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_4'))
		{
			$new_column_4 = 'Additional column 4';
		}
		$arr_columns[] = $new_column_4;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_5'))
		{
			$new_column_5 = 'Additional column 5';
		}
		$arr_columns[] = $new_column_5;

		return $arr_columns;
	}


	public function adminReceipts($legal_entity)
	{        
        if ($legal_entity === 'dd') {
        	$receipts_obj = Receipt::where('legal_entity','Д.Дымщиц')->orderByRaw('CONVERT(SUBSTRING(receipt_number, 3), SIGNED)')->paginate(10);
        	$title = 'Квитанции ДД (Receipts DD)';
        }
        elseif ($legal_entity === 'ul') {
        	$receipts_obj = Receipt::where('legal_entity','Юнион Логистик')->orderByRaw('CONVERT(receipt_number, SIGNED)')->paginate(10);
        	$title = 'Квитанции ЮЛ (Receipts UL)';
        }
        else{
        	$receipts_obj = null;
        	$title = '';
        }            
        
        return view('admin.receipts.receipts', compact('title','receipts_obj','legal_entity'));
    }


    public function adminReceiptsArchive()
	{
        $title = 'Notifications';
        $check_archive = ReceiptArchive::where([
        	['status',false],
        	['update_date','<=',date('Y-m-d')]
        ])->first();

        if ($check_archive) {
        	ReceiptArchive::where([
        		['status',false],
        		['update_date','<=',date('Y-m-d')]
        	])->update([
        		'status' => true
        	]);
        }

        $archive_obj = ReceiptArchive::where('in_trash',false)->where('status',true)->paginate(10);     
        
        return view('admin.receipts.receipts_archive', compact('title','archive_obj'));
    }


    public function receiptsArchiveShow($id)
	{
		$receipt = ReceiptArchive::find($id);
		$title = 'Изменение строки (Update row) '.$receipt->id;

		return view('admin.receipts.receipts_archive_update', compact('title','receipt'));
	}

    
    public function receiptsArchiveUpdate(Request $request, $id)
    {
    	ReceiptArchive::where('id', $id)->update([
    		'comment' => $request->input('comment')
    	]);
    	return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно обновлена (Row updated successfully)!');
    }


    public function receiptsDouble($id)
    {
    	$receipt = Receipt::find($id);
    	$data = [
    		'receipt_number' => $receipt->receipt_number,
			'legal_entity' => $receipt->legal_entity,
			'courier_name' => $receipt->courier_name,
			'double' => 1
    	];
    	Receipt::insert($data);

    	return redirect()->to(session('this_previous_url'))->with('status', 'Дубль успешно добавлен (Double added successfully)!');
    }


	public function receiptsShow($id)
	{
		$receipt = Receipt::find($id);
		$title = 'Изменение строки (Update row) '.$receipt->id;

		return view('admin.receipts.receipts_update', compact('title','receipt'));
	}


	public function receiptsUpdate(Request $request, $id)
	{
		$receipt = Receipt::find($id);		
		$data = $request->all();
		$fields = $this->getTableColumns('receipts');
		$number = $request->input('receipt_number');
		$range = $request->input('range_number');
		$old_tracking = $receipt->tracking_main;		
		
		$message = 'Строка успешно обновлена (Row updated successfully)!';

		if (!$data['double']) {
			if (!$data['tracking_main'] || !$data['sum'] || !$data['date']){
				$message = 'Нельзя сохранить строку с пустыми: Номер посылки, Сумма, Дата (You cannot save a line with empty ones: Tracking number, Amount, Date)!';
				return redirect()->to(session('this_previous_url'))->with('status-error', $message);
			}								

			if ($data['tracking_main']) {
				if (!$this->trackingValidate($data['tracking_main'])) return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking number is not correct.');
				
				if ($receipt->tracking_main) {
					ReceiptArchive::where('tracking_main', $receipt->tracking_main)->delete();
				}
				
				$string = $this->checkSkipped($range, $id);
				if($string) $message .= $string;

				$string = $this->addReceiptRow($data, $id, $number, false);
				if($string) $message .= $string;
			}
		}
		else{
			if (!$data['tracking_main']){
				$message = 'Нельзя сохранить строку с пустым: Номер посылки (You cannot save a line with empty one: Tracking number)!';
				return redirect()->to(session('this_previous_url'))->with('status-error', $message);
			}

			if (!$this->trackingValidate($data['tracking_main'])) return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking number is not correct.');

			$origin = Receipt::where([
				['receipt_number',$number],
				['double',0]
			])->first();

			if (!$origin->tracking_main){
				$message = 'Нельзя сохранить дубль с пустым оригиналом (Can\'t save take with blank original)!';
				return redirect()->to(session('this_previous_url'))->with('status-error', $message);
			}

			if ($receipt->tracking_main) {
				ReceiptArchive::where('tracking_main', $receipt->tracking_main)->delete();
			}
			
			$string = $this->addReceiptRow($data, $id, $number, true);
			if($string) $message .= $string;
		}		
		
		foreach($fields as $field){						
			if ($field !== 'created_at') {
				$receipt->$field = $request->input($field);
			}
		}

		$receipt->save();

		if ($old_tracking && $old_tracking !== $request->input('tracking_main')) {
			
			$worksheet = $this->whoseTracking($old_tracking);
			
			if ($worksheet) {
				$worksheet->tracking_main = $request->input('tracking_main');
				$worksheet->save();
			}
		}

		$last_id = ReceiptArchive::where([
			['receipt_id',$id],
			['worksheet_id',null]])
		->get()->last();

		if ($last_id) {
			$last_id = $last_id->id;
			ReceiptArchive::where([
				['receipt_id',$id],
				['worksheet_id',null],
				['tracking_main','<>',null],
				['id','<>',$last_id]
			])->delete();
		}		

		ReceiptArchive::where([
				['receipt_id',$id],
				['worksheet_id',null],
				['which_admin',null]
			])->delete();
		return redirect()->to(session('this_previous_url'))->with('status', $message);	
	}


	protected function whoseTracking($tracking)
	{
		$worksheet = null;
		
		$worksheet = NewWorksheet::where([
			['tracking_main',$tracking],
			['in_trash',false]
		])->first();
		if (!$worksheet) {
			$worksheet = PhilIndWorksheet::where([
			['tracking_main',$tracking],
			['in_trash',false]
		])->first();
		}
		if (!$worksheet) {
			$worksheet = CourierDraftWorksheet::where([
			['tracking_main',$tracking],
			['in_trash',false]
		])->first();
		}
		if (!$worksheet) {
			$worksheet = CourierEngDraftWorksheet::where([
			['tracking_main',$tracking],
			['in_trash',false]
		])->first();
		}

		return $worksheet;
	}


	private function checkSkipped($range, $id)
	{
		$message = '';
		$skipped = '';
		$range_arr = Receipt::where([
			['range_number',$range],
			['id','<',(int)$id],
			['tracking_main',null]
		])->get();

		$archive = [];
		if ($range_arr->count()) {				
			foreach ($range_arr as $value) {
				$result = ReceiptArchive::where('receipt_number',$value->receipt_number)->first();
				if (!$result) {
					$archive[] = [
						'receipt_id' => $value->id,
						'receipt_number' => $value->receipt_number,
						'description' => 'Пропущена запись по квитанции (Skipped recording on receipt): '.$value->receipt_number
					];
					$skipped .= $value->receipt_number.',';
				}				
			}

			if ($archive) {
				ReceiptArchive::insert($archive);
				$skipped = substr($skipped,0,-1);
				$message = 'Пропущена запись по квитанции (Skipped recording on receipt): '.$skipped.' !';
			}			
		}

		return $message;
	}


	private function addReceiptRow($data, $id, $number, $double)
	{
		$message = '';
		$archive = [];

		// If double
		if ($double) {
			$origin = Receipt::where([
				['receipt_number',$number],
				['double',0]
			])->first();

			$data['date'] = $origin->date;
			$data['sum'] = $origin->sum;
		}		

		// If not double
		$worksheet = $this->whoseTracking($data['tracking_main']);
		$pos = strripos($data['tracking_main'], 'CD');
		if ($worksheet && $pos === false) {
			$worksheet->payment_date_comments = $data['date'];
			$worksheet->amount_payment = $data['sum'];
			$worksheet->save();	

			ReceiptArchive::where('tracking_main', $data['tracking_main'])->delete();
		}
		elseif (!$worksheet && $pos === false){
			$notification = ReceiptArchive::where('tracking_main', $data['tracking_main'])->first();
			if (!$notification) {
				$message = $this->checkReceipt(null, $id, 'en', $data['tracking_main'], $number);
			}				
		}
		elseif ($worksheet && $pos !== false){
			$worksheet->pay_date = $data['date'];
			$worksheet->pay_sum = $data['sum'];
			$worksheet->save();	

			ReceiptArchive::where('tracking_main', $data['tracking_main'])->delete();
		}
		elseif (!$worksheet && $pos !== false){
			$notification = ReceiptArchive::where('tracking_main', $data['tracking_main'])->first();
			if (!$notification) {
				$message = $this->checkReceipt(null, $id, 'ru', $data['tracking_main'], $number);
			}				
		}

		return $message;
	}


	public function deleteReceipt(Request $request)
	{
		$id = $request->input('action');
		$receipt = Receipt::find($id);

		Receipt::where('id', $id)->delete();
		ReceiptArchive::where('tracking_main', $receipt->tracking_main)->delete();
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно удалена (Row deleted successfully)!');
	}


	public function deleteReceipts(Request $request)
	{
		$start = $request->input('range_start');

		if ($start) {
			if ($request->input('range_select') === 'ХХ01') {

				if ($request->input('legal_entity') === 'DD') {
					Receipt::where([
						['receipt_number', '>=' ,'DD'.$start.'01'],
						['receipt_number', '<=' ,'DD'.$start.'50'],
					])->delete();
					ReceiptArchive::where([
						['receipt_number', '>=' ,'DD'.$start.'01'],
						['receipt_number', '<=' ,'DD'.$start.'50'],
					])->delete();
				}
				else{
					Receipt::where([
						['receipt_number', '>=' ,$start.'01'],
						['receipt_number', '<=' ,$start.'50'],
					])->delete();
					ReceiptArchive::where([
						['receipt_number', '>=' ,$start.'01'],
						['receipt_number', '<=' ,$start.'50'],
					])->delete();
				}

			}
			else{
				$last_symbol = ((int)substr($start, -1) != 9)?substr($start,0,-1).((int)substr($start, -1)+1):substr($start,0,-2).((int)substr($start, -2)+1);
				if ($request->input('legal_entity') === 'DD') {
					Receipt::where([
						['receipt_number', '>=' ,'DD'.$start.'51'],
						['receipt_number', '<=' ,'DD'.$last_symbol.'00'],
					])->delete();
					ReceiptArchive::where([
						['receipt_number', '>=' ,'DD'.$start.'51'],
						['receipt_number', '<=' ,'DD'.$last_symbol.'00'],
					])->delete();
				}
				else{
					Receipt::where([
						['receipt_number', '>=' ,$start.'51'],
						['receipt_number', '<=' ,$last_symbol.'00'],
					])->delete();
					ReceiptArchive::where([
						['receipt_number', '>=' ,$start.'51'],
						['receipt_number', '<=' ,$last_symbol.'00'],
					])->delete();
				}
			}
			return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно удалены (Rows deleted successfully)!');
		}
		return redirect()->to(session('this_previous_url'))->with('status-error', 'Введите начало диапазона (Enter the start of the range)!');		
	}


	public function receiptsAdd(Request $request)
	{
		$data = [];
		$start = $request->input('range_start');
		$courier = $request->input('courier_name');
		
		if ($start) {
			if ($request->input('range_select') === 'ХХ01') {
				for ($i=1; $i <= 50; $i++) { 
					if ($request->input('legal_entity') === 'DD') {
						$data[] = [
							'receipt_number' => 'DD'.$start.(($i<10)?'0'.$i:$i),
							'range_number' => 'DD'.$start,
							'courier_name' => $courier,
							'legal_entity' => 'Д.Дымщиц'
						];
					}
					else{
						$data[] = [
							'receipt_number' => $start.(($i<10)?'0'.$i:$i),
							'range_number' => $start,
							'courier_name' => $courier,
							'legal_entity' => 'Юнион Логистик'
						];
					}
				}
			}
			else{
				$last_symbol = ((int)substr($start, -1) != 9)?substr($start,0,-1).((int)substr($start, -1)+1):substr($start,0,-2).((int)substr($start, -2)+1);
				for ($i=51; $i <= 100; $i++) { 
					if ($request->input('legal_entity') === 'DD') {
						$data[] = [
							'receipt_number' => ($i==100)?('DD'.$last_symbol.'00'):('DD'.$start.$i),
							'range_number' => 'DD'.$start,
							'courier_name' => $courier,
							'legal_entity' => 'Д.Дымщиц'
						];
					}
					else{
						$data[] = [
							'receipt_number' => ($i==100)?($last_symbol.'00'):($start.$i),
							'range_number' => $start,
							'courier_name' => $courier,
							'legal_entity' => 'Юнион Логистик'
						];
					}
				}
			}

			Receipt::insert($data);
			return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно добавлены (Rows added successfully)!');
		}
		
		return redirect()->to(session('this_previous_url'))->with('status-error', 'Введите начало диапазона (Enter the start of the range)!');
	}


	public function receiptsFilter(Request $request, $legal_entity)
	{
		$search = $request->table_filter_value;
		$filter_arr = [];
		$attributes = Receipt::first()->attributesToArray();
		$receipts_obj = null;
		$title = '';

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	if ($legal_entity === 'dd') {
        		$receipts_obj = Receipt::where([
        			[$request->table_columns, 'like', '%'.$search.'%'],
        			['legal_entity','Д.Дымщиц']
        		])->orderByRaw('CONVERT(SUBSTRING(receipt_number, 3), SIGNED)')->paginate(10);
        		$title = 'Фильтр Квитанций ДД (Receipts Filter DD)';
        	}
        	elseif ($legal_entity === 'ul') {
        		$receipts_obj = Receipt::where([
        			[$request->table_columns, 'like', '%'.$search.'%'],
        			['legal_entity','Юнион Логистик']
        		])->orderByRaw('CONVERT(receipt_number, SIGNED)')->paginate(10);
        		$title = 'Фильтр Квитанций ЮЛ (Receipts Filter UL)';
        	}      	
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			if ($legal_entity === 'dd'){
        				$sheet = Receipt::where([
        					[$key, 'like', '%'.$search.'%'],
        					['legal_entity','Д.Дымщиц']
        				])->get()->first();
        				if ($sheet) {       				
        					$temp_arr = Receipt::where([
        						[$key, 'like', '%'.$search.'%'],
        						['legal_entity','Д.Дымщиц']
        					])->orderByRaw('CONVERT(SUBSTRING(receipt_number, 3), SIGNED)')->get();
        					$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        						if (!in_array($item->id, $id_arr)) { 
        							$id_arr[] = $item->id;       						  
        							return $item;    					
        						}       					       					
        					});        				
        					$filter_arr[] = $new_arr;   				         		
        				}
        			}
        			elseif ($legal_entity === 'ul'){
        				$sheet = Receipt::where([
        					[$key, 'like', '%'.$search.'%'],
        					['legal_entity','Юнион Логистик']
        				])->get()->first();
        				if ($sheet) {       				
        					$temp_arr = Receipt::where([
        						[$key, 'like', '%'.$search.'%'],
        						['legal_entity','Юнион Логистик']
        					])->orderByRaw('CONVERT(receipt_number, SIGNED)')->get();
        					$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        						if (!in_array($item->id, $id_arr)) { 
        							$id_arr[] = $item->id;       						  
        							return $item;    					
        						}       					       					
        					});        				
        					$filter_arr[] = $new_arr;   				         		
        				}
        			}
        		}         		
        	}

        	return view('admin.receipts.receipts_find', compact('title','filter_arr','legal_entity'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.receipts.receipts', compact('title','receipts_obj','legal_entity','data'));
    }


    public function exportExcelReceipts()
	{

    	return Excel::download(new ReceiptExport, 'ReceiptExport.xlsx');

	}


	public function receiptsArchiveFilter(Request $request)
	{
        $title = 'Notifications Filter';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = ReceiptArchive::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$archive_obj = ReceiptArchive::where('in_trash',false)->where($request->table_columns, 'like', '%'.$search.'%')->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = ReceiptArchive::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = ReceiptArchive::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get();
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$filter_arr[] = $new_arr;   				         		
        			}
        		}         		
        	}

        	return view('admin.receipts.receipts_archive_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.receipts.receipts_archive', compact('title','archive_obj','data'));
    }


    public function receiptsSum(Request $request, $legal_entity){
    	$from_date = $request->input('from_date');
    	$to_date = $request->input('to_date');
    	$sum = 0;

    	if ((int)$from_date > (int)$to_date) {
    		return json_encode(['error'=>'Начальная дата больше конечной']);
    	}
    	else{
    		if ($legal_entity === 'dd') {
    			$sum = Receipt::where([
    				['legal_entity','Д.Дымщиц'],
    				['date','>=',$from_date],
    				['date','<=',$to_date]
    			])->sum('sum');
    		}
    		elseif ($legal_entity === 'ul') {
    			$sum = Receipt::where([
    				['legal_entity','Юнион Логистик'],
    				['date','>=',$from_date],
    				['date','<=',$to_date]
    			])->sum('sum');
    		}
			return json_encode(['sum'=>$sum]);
    	}
    }


    public function deleteReceiptArchive(Request $request)
	{
		$id = $request->input('action');
		ReceiptArchive::where('id', $id)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно удалена!');
	}

}
