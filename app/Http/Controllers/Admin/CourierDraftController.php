<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use CourierDraftWorksheet;
use NewWorksheet;
use App\NewPacking;
use App\PackingSea;
use App\Invoice;
use App\Manifest;
use Auth;
use \Dejurin\GoogleTranslateForFree;
use Excel;
use DB;
use App\Exports\CourierDraftWorksheetExport;
use App\ReceiptArchive;
use App\Receipt;
use App\SignedDocument;


class CourierDraftController extends AdminController
{
	private $status_arr = ["Доставляется на склад в стране отправителя", "Возврат", "Коробка", "Забрать", "Уточнить", "Думают", "Отмена", "Подготовка", "Дубль","Пакинг лист"];
	private $status_arr_2 = ["На таможне в стране отправителя", "На складе в стране отправителя", "Доставляется на склад в стране отправителя", "Возврат", "Коробка", "Забрать", "Уточнить", "Думают", "Отмена", "Подготовка", "Дубль","Пакинг лист"];
	private $status_arr_3 = ["Возврат", "Коробка", "Забрать", "Уточнить", "Думают", "Отмена", "Подготовка", "Дубль","Пакинг лист"];
    

    public function index(Request $request){
        $title = 'Черновик';
        if ($request->input('for_active')) {
        	$courier_draft_worksheet_obj = CourierDraftWorksheet::where('in_trash',false)->where('tracking_main','<>',null)
        	->orWhere('status','Забрать')
        	->orderBy('index_number')
        	->paginate(10);
        }
        else{
        	$courier_draft_worksheet_obj = CourierDraftWorksheet::where('in_trash',false)
        	->orderBy('index_number')
        	->paginate(10);
        }
        $data = $request->all();   
        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR;               
        
        return view('admin.courier_draft.courier_draft_worksheet', ['title' => $title,'data' => $data,'courier_draft_worksheet_obj' => $courier_draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


	public function show($id)
	{
		$courier_draft_worksheet = CourierDraftWorksheet::find($id);
		$title = 'Изменение строки '.$courier_draft_worksheet->id;
		$israel_cities = $this->israelCities();

		return view('admin.courier_draft.courier_draft_worksheet_update',compact('title','courier_draft_worksheet','israel_cities'));
	}


	private function validateUpdate($request, $id)
	{
		$error_message = '';

		$message_pdf = $this->checkPdfId('draft_id',$id);
		if ($message_pdf !== 'success') {
			return $message_pdf;
		}
		
		if ($request->input('tracking_main')) {
			$error_message = $this->checkTracking("courier_draft_worksheet", $request->input('tracking_main'), $id);
			return $error_message;
		}
		elseif (!$request->input('tracking_main') && !in_array($request->input('status'), $this->status_arr_3)){
			$error_message = "Статус не может быть выше чем Забрать без трекинг-номера";
			return $error_message;
		}
		elseif (!$request->input('tracking_main') && ($request->input('batch_number') || $request->input('pallet_number'))){
			$error_message = "Нельзя ввести номер партии или паллеты без трекинг-номера";
			return $error_message;
		}	

		if ($request->input('recipient_phone')) {
			$error_message = $this->checkConsigneePhone($request->input('recipient_phone'), 'ru');
			return $error_message;
		}
	}


	public function update(Request $request, $id)
	{
		$courier_draft_worksheet = CourierDraftWorksheet::find($id);		
		$old_status = $courier_draft_worksheet->status;
		$old_tracking = $courier_draft_worksheet->tracking_main;
		$old_pallet = $courier_draft_worksheet->pallet_number;
		$old_batch_number = $courier_draft_worksheet->batch_number;
		$check_result = '';
		$fields = $this->getTableColumns('courier_draft_worksheet');
		$user = Auth::user();
		$status_error = '';			

		$status_error = $this->validateUpdate($request, $id);
		if (!$status_error) {
			$status_error = $this->checkStatus('courier_draft_worksheet', $id, $request->input('status'));
		}		
		if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);

		$this->toUpdatesArchive($request,$courier_draft_worksheet);

		foreach($fields as $field){						
			if ($field !== 'created_at' && $field !== 'tracking_main') {
				if ($request->has($field)) {
					$courier_draft_worksheet->$field = $request->input($field);
				}				
			}
			elseif ($field !== 'created_at' && ($user->role === 'admin' || $user->role === 'office_1')){
				$courier_draft_worksheet->$field = $request->input($field);
			}
		}

		if ($request->input('tracking_main')) {
			$check_result .= $this->updateStatusByTracking('courier_draft_worksheet', $courier_draft_worksheet);
		}

		$courier_draft_worksheet->direction = $this->createRuDirection($request->input('sender_country'), $request->input('recipient_country'));

		if ($old_status !== $courier_draft_worksheet->status) {
			CourierDraftWorksheet::where('id', $id)
			->update([
				'status_date' => date('Y-m-d')
			]);
		}

		if ($old_tracking && $request->input('tracking_main')) {
			ReceiptArchive::where('tracking_main', $old_tracking)->delete();	
			if ($old_tracking !== $request->input('tracking_main')) {
				Receipt::where('tracking_main', $old_tracking)->update(
					['tracking_main' => $request->input('tracking_main')]
				);
			}		
		}
		
		$notification = ReceiptArchive::where('tracking_main', $request->input('tracking_main'))->first();
		if (!$notification) $check_result .= $this->checkReceipt($id, null, 'ru', $request->input('tracking_main'),null,$old_tracking);

		if (in_array($courier_draft_worksheet->sender_city, array_keys($this->israel_cities))) {
			$courier_draft_worksheet->shipper_region = $this->israel_cities[$courier_draft_worksheet->sender_city];
		}

		$temp = rtrim($request->input('package_content'), ";");
		$content_arr = explode(";",$temp);

		if ($content_arr[0]) {
			
			// Update Packing Sea
			PackingSea::where('work_sheet_id', $id)
			->update([
				'track_code' => $request->input('tracking_main'),
				'type' => $request->input('tariff'),
				'full_shipper' => $request->input('sender_name'),
				'full_consignee' => $request->input('recipient_name'),
				'country_code' => $request->input('recipient_country'),
				'region' => $request->input('region'),
				'district' => $request->input('district'),
				'postcode' => $request->input('recipient_postcode'),
				'city' => $request->input('recipient_city'),
				'street' => $request->input('recipient_street'),
				'house' => $request->input('recipient_house'),
				'body' => $request->input('body'),
				'room' => $request->input('recipient_room'),
				'phone' => $request->input('recipient_phone')
			]);

			if ($request->input('package_content')) {
				
				$old_packing = PackingSea::where('work_sheet_id', $id)->get();
				$qty = 1;

				for ($i=0; $i < count($content_arr); $i++) { 
					$qty = $i+1;
					$content = explode(':', $content_arr[$i]);

					if (count($content) == 2) {
						if ($qty <= count($old_packing)) {
							PackingSea::where([
								['work_sheet_id', $id],
								['attachment_number', $qty]
							])
							->update([
								'attachment_name' => trim($content[0]),
								'amount_3' => trim($content[1])
							]);
						}
						else{
							$new_packing = new PackingSea();
							$new_packing->work_sheet_id = $id;
							$new_packing->track_code = $request->input('tracking_main');
							$new_packing->type = $request->input('tariff');
							$new_packing->full_shipper = $request->input('sender_name');
							$new_packing->full_consignee = $request->input('recipient_name');
							$new_packing->country_code = $request->input('recipient_country');
							$new_packing->postcode = $request->input('recipient_postcode');
							$new_packing->region = $request->input('region');
							$new_packing->district = $request->input('district');
							$new_packing->city = $request->input('recipient_city');
							$new_packing->street = $request->input('recipient_street');
							$new_packing->house = $request->input('recipient_house');
							$new_packing->body = $request->input('body');
							$new_packing->room = $request->input('recipient_room');
							$new_packing->phone = $request->input('recipient_phone');
							$new_packing->attachment_number = $qty;
							$new_packing->attachment_name = trim($content[0]);
							$new_packing->amount_3 = trim($content[1]);
							$new_packing->save();
						}
					}
					else{
						return redirect()->to(session('this_previous_url'))->with('status-error', 'Ошибка колонки Содержание!'.' '.$check_result);
					}
				}
				PackingSea::where([
					['work_sheet_id', $id],
					['attachment_number','>',$qty]
				])->delete();
			}
			else{
				PackingSea::where('work_sheet_id', $id)->delete();
			}
			// End Update Packing Sea
			$courier_draft_worksheet->save();
			$courier_draft_worksheet->checkCourierTask($courier_draft_worksheet->status);

			$this->addingOrderNumber($courier_draft_worksheet->standard_phone, 'ru');

			if ($request->input('tracking_main')) {
				// Check for missing tracking
				$this->checkForMissingTracking($request->input('tracking_main'));
				
				// Update Warehouse pallet
				if ($old_pallet !== $request->input('pallet_number') || $old_tracking !== $request->input('tracking_main')) {
					$message = $this->updateWarehousePallet($old_tracking, $request->input('tracking_main'), $old_pallet, $request->input('pallet_number'), $old_batch_number, $courier_draft_worksheet->batch_number, 'ru', $courier_draft_worksheet);
					if ($message) {
						return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
					}			
				}

				// Update Warehouse lot
				if ($old_batch_number !== $courier_draft_worksheet->batch_number){
					$this->updateWarehouseLot($request->input('tracking_main'), $courier_draft_worksheet->batch_number, 'ru');
				}

				// Activate PDF
				if (!$old_tracking && $courier_draft_worksheet->getLastDocUniq()) {
					return redirect('/admin/courier-draft-activate/'.$courier_draft_worksheet->id);
				}
			}

			return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно обновлена!'.' '.$check_result);
		}	
		else{
			return redirect('/admin/courier-draft-worksheet')->with('status-error', 'Ошибка колонки Содержание!'.' '.$check_result);
		}		
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');
		$this->removeTrackingFromPalletWorksheet($id, 'ru', true);
		/*$this->deleteUploadFiles('draft_id',$id);*/
		$worksheet = CourierDraftWorksheet::find($id);
		$this->deletedToUpdatesArchive($worksheet);

		CourierDraftWorksheet::where('id', $id)->delete();
		PackingSea::where('work_sheet_id', $id)->delete();
		ReceiptArchive::where('worksheet_id', $id)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно удалена!');
	}


    public function addCourierDraftDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('tracking-columns');
    	$user = Auth::user();
    	$old_lot_arr = [];
    	$old_pallet_arr = [];
    	$check_column = 'id';
    	$status_error = '';
    	$check_result = '';

    	if ($row_arr) {

    		for ($i=0; $i < count($row_arr); $i++) { 
    			$message_pdf = $this->checkPdfId('draft_id',$row_arr[$i]);
    			if ($message_pdf !== 'success') {
    				return redirect()->to(session('this_previous_url'))->with('status-error', $message_pdf);
    			}
    		} 
    		
    		if ($value_by && $column) {    			

    			$status_error = $this->checkColumns($row_arr, $value_by, $column, $check_column, 'courier_draft_worksheet');
    			if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);   			
    			
    			if ($column === 'batch_number') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = CourierDraftWorksheet::where('id',$row_arr[$i])->first();
    					$old_lot_arr[] = $worksheet->batch_number;
    				}
    			}

    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = CourierDraftWorksheet::where('id',$row_arr[$i])->first();
    					$old_pallet_arr[] = $worksheet->pallet_number;
    				}
    			}

    			if ($column === 'tracking_main') {   				    				
    				for ($i=0; $i < count($row_arr); $i++) { 

    					$error_message = $this->checkTracking("courier_draft_worksheet", $value_by, $row_arr[$i]);
    					if($error_message) return redirect()->to(session('this_previous_url'))->with('status-error', $error_message);
    					
    					$worksheet = CourierDraftWorksheet::where('id',$row_arr[$i])->first();
    					$old_tracking = $worksheet->tracking_main;
    					$pallet = $worksheet->pallet_number;
    					$lot = $worksheet->batch_number;

    					if ($old_tracking && $old_tracking !== $value_by) {
    						ReceiptArchive::where('tracking_main', $old_tracking)->delete();
    						Receipt::where('tracking_main', $old_tracking)->update(
    							['tracking_main' => $value_by]
    						);
    					}
    					$notification = ReceiptArchive::where('tracking_main', $value_by)->first();
    					if (!$notification) $check_result .= $this->checkReceipt($worksheet->id, null, 'ru', $value_by,null,$old_tracking); 

    					$check_result .= $this->updateStatusByTracking('courier_draft_worksheet', $worksheet);		    					
    				}

    				PackingSea::where('work_sheet_id',$worksheet->id)->update([
    					'track_code' => $value_by
    				]);

    				// Check for missing tracking
    				$this->checkForMissingTracking($value_by);
    				// Update Warehouse pallet
    				$message = $this->updateWarehousePallet($old_tracking, $value_by, $pallet, $pallet, $lot, $lot, 'ru', $worksheet);
    				if ($message) {
    					return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
    				}	   								
    			}

    			if ($column !== 'index_number') {
    				CourierDraftWorksheet::whereIn('id', $row_arr)
    				->update([
    					$column => $value_by
    				]); 
    			}   			
    			elseif ((int)$value_by > 0 && count($row_arr) === 1) {
    				$worksheet = CourierDraftWorksheet::find($row_arr[0]);
    				$worksheet->reIndex((int)$value_by);       	
    			}

    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = CourierDraftWorksheet::where('id',$row_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			} 
    			
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				$column => $value_by
    			]); 

    			if ($column === 'tracking_main') {    				
    				// Activate PDF
    				if (!$old_tracking && $worksheet->getLastDocUniq()) {
    					return redirect('/admin/courier-draft-activate/'.$worksheet->id);
    				}
    			}

    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = CourierDraftWorksheet::where('id',$row_arr[$i])->first();
    					if ($old_pallet_arr[$i] !== $value_by){
    						$message = $this->updateWarehousePallet($worksheet->tracking_main, $worksheet->tracking_main, $old_pallet_arr[$i], $value_by, $worksheet->batch_number, $worksheet->batch_number, 'ru', $worksheet);
    						if ($message) {
    							return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
    						}
    					}
    				}
    			}

    			if ($column === 'batch_number') {
    				CourierDraftWorksheet::whereIn('id', $row_arr)
    				->whereIn('status',$this->status_arr_2)
    				->update([
    					'status' => "Доставляется в страну получателя",
    					'status_en' => "Forwarding to the receiver country",
    					'status_he' => " נשלח למדינת המקבל",
    					'status_ua' => "Forwarding to the receiver country",
    					'status_date' => date('Y-m-d')
    				]);

    				for ($i=0; $i < count($row_arr); $i++) { 
    					if ($old_lot_arr[$i] !== $value_by){
    						$worksheet = CourierDraftWorksheet::where('id',$row_arr[$i])->first();
    						$this->updateWarehouseLot($worksheet->tracking_main, $value_by, 'ru');
    					}
    				}
    			}    

    			if ($column === 'sender_country') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = CourierDraftWorksheet::where('id',$row_arr[$i])->first();
    					if (!$worksheet->direction) {
    						$worksheet->direction = $this->from_country_dir[$value_by].'-';
    						$worksheet->save();
    					}
    					else{
    						$temp = explode('-', $worksheet->direction);
    						if (count($temp) == 2) {
    							$worksheet->direction = $this->from_country_dir[$value_by].'-'.$temp[1];
    						}
    						else{
    							$worksheet->direction = $this->from_country_dir[$value_by].'-';
    						} 
    						$worksheet->save();  						
    					}
    				}
    			}

    			if ($column === 'recipient_country') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = CourierDraftWorksheet::where('id',$row_arr[$i])->first();
    					if (!$worksheet->direction) {
    						$worksheet->direction = '-'.$value_by;
    						$worksheet->save();
    					}
    					else{
    						$temp = explode('-', $worksheet->direction);
    						if (count($temp) == 2) {
    							$worksheet->direction = $temp[0].'-'.$value_by;
    						}
    						else{
    							$worksheet->direction = '-'.$value_by;
    						} 
    						$worksheet->save();  						
    					}
    				}
    			}			  			      	
    		}
    		else if ($request->input('status')){
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$status_error = $this->checkStatus('courier_draft_worksheet', $row_arr[$i], $request->input('status'));
    				if (!$status_error) {
    					$worksheet = CourierDraftWorksheet::find($row_arr[$i]);
    					$this->toUpdatesArchive($request,$worksheet);
    					
    					CourierDraftWorksheet::where('id', $row_arr[$i])
    					->update([
    						'status' => $request->input('status'), 
    						'status_en' => $request->input('status_en'),
    						'status_ua' => $request->input('status_ua'),
    						'status_he' => $request->input('status_he'),
    						'status_date' => date('Y-m-d')
    					]);
    				}
    				
    				$worksheet = CourierDraftWorksheet::find($row_arr[$i]);
    				$worksheet->checkCourierTask($worksheet->status);    				
    			} 
    		}
    		else if ($request->input('site_name')) {
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = CourierDraftWorksheet::find($row_arr[$i]);
    				$this->toUpdatesArchive($request,$worksheet);   				
    			}
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'site_name' => $request->input('site_name')
    			]);  

    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = CourierDraftWorksheet::find($row_arr[$i]);
    				$worksheet->checkCourierTask($worksheet->status);    				
    			}     	
    		}
    		else if ($request->input('status_date')) {
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'status_date' => $request->input('status_date')
    			]);       	
    		}
    		else if ($request->input('order_date')) {
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'order_date' => $request->input('order_date')
    			]);       	
    		}
    		else if ($request->input('date')) {
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'date' => $request->input('date')
    			]);       	
    		}
    		else if ($request->input('tariff')) {
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'tariff' => $request->input('tariff')
    			]);       	
    		}
    		else if ($request->input('partner')) {
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'partner' => $request->input('partner')
    			]);       	
    		}
    		else if ($request->input('sender_city')) {
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = CourierDraftWorksheet::find($row_arr[$i]);
    				$this->toUpdatesArchive($request,$worksheet);   				
    			}
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'sender_city' => $request->input('sender_city')
    			]);  

    			if (in_array($request->input('sender_city'), array_keys($this->israel_cities))) {
    				CourierDraftWorksheet::whereIn('id', $row_arr)
    				->update([
    					'shipper_region' => $this->israel_cities[$request->input('sender_city')]
    				]);
    			}
    		}
    		else if ($request->input('courier')) {
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = CourierDraftWorksheet::where('id',$row_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}
    			
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'courier' => $request->input('courier')
    			]);  
    		}
    		else $status_error = 'New fields error!';

    		for ($i=0; $i < count($row_arr); $i++) { 
    			$worksheet = CourierDraftWorksheet::find($row_arr[$i]);
    			$worksheet->checkCourierTask($worksheet->status);
    		}
    	}

    	if($status_error){
        	return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
        }
        else{
        	return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно изменены!'.' '.$check_result);
        }
    }


    public function deleteCourierDraftWorksheetById(Request $request)
	{
		$row_arr = $request->input('row_id');
		for ($i=0; $i < count($row_arr); $i++) { 
			$this->removeTrackingFromPalletWorksheet($row_arr[$i], 'ru',true);
			/*$this->deleteUploadFiles('draft_id',$row_arr[$i]);*/
			$worksheet = CourierDraftWorksheet::find($row_arr[$i]);
			$this->deletedToUpdatesArchive($worksheet);
		}

		CourierDraftWorksheet::whereIn('id', $row_arr)->delete();
		PackingSea::whereIn('work_sheet_id', $row_arr)->delete();
		ReceiptArchive::whereIn('worksheet_id', $row_arr)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно удалены!');
	}


	public function courierDraftWorksheetFilter(Request $request){
        $title = 'Фильтр Черновика';               
        $model = $this->getModel('Worksheet');
        $data = $request->all();
        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR; 

        $result = $model::searchByMultipleParameters($model, $request);
        if (!$result) return redirect()->to(session('this_previous_url'))->with('status-error', 'You must specify a value!');
        if (is_array($result)){
        	return view('admin.courier_draft.courier_draft_worksheet_find', ['title' => $title,'courier_draft_worksheet_arr' => $result, 'user' => $user, 'viewer_arr' => $viewer_arr]);
        }
        else{
        	return view('admin.courier_draft.courier_draft_worksheet', ['title' => $title,'data' => $data,'courier_draft_worksheet_obj' => $result, 'user' => $user, 'viewer_arr' => $viewer_arr]);
        }
    }


    public function courierDraftWorksheetDouble(Request $request,$id,$api = false)
    {
    	$duplicate_qty = $request->duplicate_qty;
    	$worksheet = CourierDraftWorksheet::find($id);
    	if (!$worksheet->getLastDocUniq())
    		return redirect()->to(session('this_previous_url'))->with('status-error', 'You can not duplicate without PDF!');
    	$old_delete = false;
    	$worksheet_data = [
    		'standard_phone' => $worksheet->standard_phone,
    		'sender_name' => $worksheet->sender_name,
    		'sender_country' => $worksheet->sender_country,
    		'sender_city' => $worksheet->sender_city,
    		'sender_postcode' => $worksheet->sender_postcode,
    		'sender_address' => $worksheet->sender_address,
    		'sender_phone' => $worksheet->sender_phone,
    		'sender_passport' => $worksheet->sender_passport,
    		'recipient_name' => $worksheet->recipient_name,
    		'recipient_country' => $worksheet->recipient_country,
    		'region' => $worksheet->region,
    		'district' => $worksheet->district,
    		'recipient_city' => $worksheet->recipient_city,
    		'recipient_postcode' => $worksheet->recipient_postcode,
    		'recipient_street' => $worksheet->recipient_street,
    		'recipient_house' => $worksheet->recipient_house,
    		'body' => $worksheet->body,
    		'recipient_room' => $worksheet->recipient_room,
    		'recipient_phone' => $worksheet->recipient_phone,
    		'recipient_passport' => $worksheet->recipient_passport,
    		'recipient_email' => $worksheet->recipient_email,
    		'site_name' => $worksheet->site_name,  
    		'order_date' => $worksheet->order_date,  		
    		'direction' => $worksheet->direction
    	]; 

    	for ($j=0; $j < (int)$duplicate_qty; $j++) { 
    		
    		$object = (object)[];   		
    		$all_worksheet = CourierDraftWorksheet::where([
    			['in_trash',false],
    			['standard_phone',$worksheet->standard_phone]
    		])->get();   		  	

    		if (!$worksheet->getLastDocUniq() && !$old_delete)
    			$old_delete = $this->deleteOldWorksheet($id,'ru');

    		$qty = $all_worksheet->count(); 
    		CourierDraftWorksheet::create($worksheet_data);
    		$new_id = DB::getPdo()->lastInsertId();
    		CourierDraftWorksheet::find($new_id)
    		->update([
    			'date'=>date('Y-m-d'),
    			'status' => 'Дубль',
    			'package_content' => 'Пусто: 0',
    			'order_date' => $worksheet->order_date,
    			'status_date' => date('Y-m-d')
    		]);

    		$id_arr = CourierDraftWorksheet::where([
    			['in_trash',false],
    			['standard_phone',$worksheet->standard_phone]
    		])->pluck('id');
    		if (!$old_delete) $qty++;
    		$last_num = 1;
    		for ($i=0; $i < count($id_arr); $i++) { 
    			$worksheet_data['parcels_qty'] = $last_num.' of '.$qty;
    			CourierDraftWorksheet::where([
    				['id',$id_arr[$i]],
    				['in_trash',false],
    				['standard_phone',$worksheet->standard_phone]
    			])->update($worksheet_data);
    			$last_num++;
    		} 

    		$new_worksheet = CourierDraftWorksheet::find($new_id);
    		$new_worksheet->setIndexNumber();
    		$new_worksheet->checkCourierTask($new_worksheet->status);

    		$packing = PackingSea::where('work_sheet_id',$id)->get();
    		if ($packing) {
    			$packing->each(function ($item, $key) use($new_id){                                 
    				$new_packing = $item->replicate();
    				$new_packing->work_sheet_id = $new_id;
    				$new_packing->track_code = null;
    				$new_packing->save();
    			});
    		}  
    		if (!$api) $this->toUpdatesArchive($object,$new_worksheet,true,$new_id);
    	}    	
    	
    	if ($api) return $new_worksheet->id;
    	else
    		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно продублирована!');
    }


    public function adminActivate(Request $request)
    {
    	$row_arr = $request->row_id;
    	$message = '';
    	for ($i=0; $i < count($row_arr); $i++) { 
    		$worksheet = CourierDraftWorksheet::find($row_arr[$i]);
    		$message_pdf = $this->checkPdfId('draft_id',$row_arr[$i]);
    		if ($message_pdf !== 'success') 
    			return redirect()->to(session('this_previous_url'))->with('status-error', $message_pdf);
    		if ($worksheet->tracking_main)
    			$message .= $this->courierDraftActivate($row_arr[$i], true);
    		else
    			return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking number is required!');
    	}
    	return redirect()->to(session('this_previous_url'))->with('status', $message);
    }


    public function courierDraftCheckActivate($id)
    {
    	$courier_draft_worksheet = CourierDraftWorksheet::find($id);
		$error_message = 'Заполните обязателные поля: ';
		$user = Auth::user();

		$message_pdf = $this->checkPdfId('draft_id',$id);
		if ($message_pdf !== 'success') {
			return response()->json(['error' => $message_pdf]);
		}

		if (!$courier_draft_worksheet->sender_name) $error_message .= 'Отправитель,';
		if (!$courier_draft_worksheet->standard_phone) $error_message .= 'Телефон (стандарт),';
		if (!$courier_draft_worksheet->recipient_name) $error_message .= 'Получатель,';
		if (!$courier_draft_worksheet->recipient_city) $error_message .= 'Город получателя,';
		if (!$courier_draft_worksheet->recipient_street) $error_message .= 'Улица получателя,';
		if (!$courier_draft_worksheet->recipient_house) $error_message .= '№ дома получателя,';
		if (!$courier_draft_worksheet->recipient_room) $error_message .= '№ кв. получателя,';
		if (!$courier_draft_worksheet->recipient_phone) $error_message .= 'Телефон получателя,';
		if (!$courier_draft_worksheet->package_content) $error_message .= 'Содержание,';	

		if ($error_message !== 'Заполните обязателные поля: ') {
			return response()->json(['error' => $error_message]);
		}
		else{
			return response()->json(['success' => 'success']);
		}			
    }


    public function courierDraftActivate($id, $admin = false, $courier = false)
	{
		$courier_draft_worksheet = CourierDraftWorksheet::find($id);		
		$new_worksheet = new NewWorksheet();
		$fields = $this->getTableColumns('courier_draft_worksheet');
		$message = '';
		$user = Auth::user();

		if ($courier_draft_worksheet->tracking_main) {
			$check_tracking	= NewWorksheet::where('tracking_main', $courier_draft_worksheet->tracking_main)->first();
			if ($check_tracking) return redirect()->to(session('this_previous_url'))->with('status-error', 'Трекинг существует!');
		}			

		foreach($fields as $field){
			if ($field !== 'created_at' && $field !== 'id' && $field !== 'index_number') {
				$new_worksheet->$field = $courier_draft_worksheet->$field;
			}			
		}

		if (!$admin) {
			if ($user->role === 'office_1' || $user->role === 'admin') {
				$new_worksheet->background = 'tr-orange';
			}
		}						

		$temp = rtrim($courier_draft_worksheet->package_content, ";");
		$content_arr = explode(";",$temp);
		if (!$courier)
			$new_worksheet->setIndexNumber();
		else{
			$new_worksheet->reIndex(100, true);
		}
				
		if ($content_arr[0]) {
			
			$new_worksheet->save();
			$work_sheet_id = $new_worksheet->id;

			if ($new_worksheet->pallet_number) {
				$this->updateWarehouse(null, $new_worksheet->pallet_number, $new_worksheet->tracking_main);
			}

			if ($new_worksheet->tracking_main) {
				// Notification of Warehouse
				ReceiptArchive::where([
					['tracking_main', $new_worksheet->tracking_main],
					['worksheet_id', null],
					['receipt_id', null]
				])->delete();
				$result = Receipt::where('tracking_main', $new_worksheet->tracking_main)->first();
				if (!$result) {
					$message = $this->checkReceipt($work_sheet_id, null, 'ru', $new_worksheet->tracking_main);
				}

				$this->checkForMissingTracking($new_worksheet->tracking_main);
				// End Notification of Warehouse
			}			

			ReceiptArchive::where('worksheet_id', $id)->update(['worksheet_id' => $work_sheet_id]);
			
			$tr = new GoogleTranslateForFree();
			$packing = PackingSea::where('work_sheet_id', $id)->get();
			
			$this->createNewPacking($new_worksheet, $work_sheet_id, $packing);
			$this->createInvoice($new_worksheet, $tr, $work_sheet_id, $packing);
			$this->createManifest($new_worksheet, $tr, $work_sheet_id, $packing);
			PackingSea::where('work_sheet_id', $id)->delete();

			// Adding order number
            if ($new_worksheet->standard_phone) {
				$this->addingOrderNumber($new_worksheet->standard_phone, 'ru');               
            }

            // Transfer documents
            SignedDocument::where('draft_id',$courier_draft_worksheet->id)
    		->update([
    			'draft_id' => null,
    			'worksheet_id' => $new_worksheet->id
    		]);					
			
			CourierDraftWorksheet::where('id', $id)->delete();
			$draft = CourierDraftWorksheet::first();
			$draft->setIndexNumber();
			$new_worksheet->checkCourierTask($new_worksheet->status);			
			
			if (!$admin)
			return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно активирована!'.$message);
			else
				return 'Строка успешно активирована!'.$message;
		}
		else{
			return redirect()->to(session('this_previous_url'))->with('status-error', 'Ошибка активации!'.$message);
		}				
	}


	private function createNewPacking($new_worksheet, $work_sheet_id, $packing){			
		$packing_fields = $this->getTableColumns('new_packing');					

		$packing->each(function ($item, $key) use($work_sheet_id, $packing_fields, $new_worksheet) {
			$new_packing = new NewPacking();
			for ($i=0; $i < count($packing_fields); $i++) { 
				if ($packing_fields[$i] !== 'work_sheet_id' && $packing_fields[$i] !== 'id') {
					$new_packing[$packing_fields[$i]] = $item[$packing_fields[$i]];
				}
				else{
					$new_packing->work_sheet_id = $work_sheet_id;
				}					
			}
			$new_packing->weight_kg = $new_worksheet->weight;
			$new_packing->save();
		});

		return true;
	}


	private function createInvoice($new_worksheet, $tr, $work_sheet_id, $packing){
		$invoice_num = 1;
		$result = Invoice::latest()->first();
		if ($result) {
			$invoice_num = (int)$result->number + 1;
		}			
		$address = '';
		for ($i=0; $i < 8; $i++) { 
			if ($i == 0 && $packing[0]->postcode) {
				$address .= $packing[0]->postcode.', ';
			}
			if ($i == 1 && $packing[0]->region) {
				$address .= $this->translit($packing[0]->region).', ';
			}
			if ($i == 2 && $packing[0]->district) {
				$address .= $this->translit($packing[0]->district).', ';
			}
			if ($i == 3 && $packing[0]->city) {
				$address .= $this->translit($packing[0]->city).', ';
			}
			if ($i == 4 && $packing[0]->street) {
				$address .= $this->translit($packing[0]->street).', ';
			}
			if ($i == 5 && $packing[0]->house) {
				$address .= $packing[0]->house;
			}
			if ($i == 6 && $packing[0]->body) {
				$address .= '/'.$packing[0]->body;
			}
			if ($i == 7 && $packing[0]->room) {
				$address .= ', '.$packing[0]->room;
			}
		}

		$invoice = new Invoice();
		$invoice->number = $invoice_num;
		$invoice->tracking = $new_worksheet->tracking_main;
		$invoice->box = 1;
		$invoice->shipper_name = $this->translit($packing[0]->full_shipper);
		$invoice->shipper_address_phone = $this->translit($new_worksheet->sender_city.', '.$new_worksheet->sender_address).'; '.$new_worksheet->standard_phone;
		$invoice->consignee_name = $this->translit($packing[0]->full_consignee);
		$invoice->consignee_address = $address;
		$invoice->shipped_items = $tr->translate('ru', 'en', $new_worksheet->package_content, 5);
		$invoice->weight = $new_worksheet->weight;
		$invoice->height = $new_worksheet->height;
		$invoice->length = $new_worksheet->length;
		$invoice->width = $new_worksheet->width;
		$invoice->declared_value = $new_worksheet->package_cost;
		$invoice->work_sheet_id = $work_sheet_id;
		$invoice->save();

		return true;
	}


	private function createManifest($new_worksheet, $tr, $work_sheet_id, $packing){
		$manifest_num = 1;
		$result = Manifest::where('number','<>', null)->latest()->first();
		if ($result) {
			$manifest_num = (int)$result->number + 1;
		}

		$address = '';
		for ($i=0; $i < 8; $i++) { 
			if ($i == 0 && $packing[0]->postcode) {
				$address .= $packing[0]->postcode.', ';
			}
			if ($i == 1 && $packing[0]->region) {
				$address .= $this->translit($packing[0]->region).', ';
			}
			if ($i == 2 && $packing[0]->district) {
				$address .= $this->translit($packing[0]->district).', ';
			}
			if ($i == 3 && $packing[0]->city) {
				$address .= $this->translit($packing[0]->city).', ';
			}
			if ($i == 4 && $packing[0]->street) {
				$address .= $this->translit($packing[0]->street).', ';
			}
			if ($i == 5 && $packing[0]->house) {
				$address .= $packing[0]->house;
			}
			if ($i == 6 && $packing[0]->body) {
				$address .= '/'.$packing[0]->body;
			}
			if ($i == 7 && $packing[0]->room) {
				$address .= ', '.$packing[0]->room;
			}
		}

		for ($i=0; $i < count($packing); $i++) { 
			$manifest = new Manifest();
			if ($i == 0) {
				$manifest->number = $manifest_num;
			}
			else{
				$manifest->number = null;
			}
			$manifest->tracking = $new_worksheet->tracking_main;
			$manifest->sender_country = $tr->translate('ru', 'en', $new_worksheet->sender_country, 5);
			$manifest->sender_name = $this->translit($new_worksheet->sender_name);
			$manifest->recipient_name = $this->translit($new_worksheet->recipient_name);
			$manifest->recipient_city = $this->translit($new_worksheet->recipient_city);
			$manifest->recipient_address = $address;
			$manifest->content = $tr->translate('ru', 'en', $packing[$i]->attachment_name, 5);
			$manifest->quantity = $packing[$i]->amount_3;
			$manifest->weight = $new_worksheet->weight;
			$manifest->cost = $new_worksheet->package_cost;
			$manifest->attachment_number = $packing[$i]->attachment_number;
			$manifest->work_sheet_id = $work_sheet_id;
			$manifest->save();
		}

		return true;
	}


	public function exportExcel()
	{

		return Excel::download(new CourierDraftWorksheetExport, 'CourierDraftWorksheetExport.xlsx');

	}


    public function setIndexes()
    {
    	$worksheets = CourierDraftWorksheet::all();
    	foreach ($worksheets as $item) {
    		$item->setIndexNumber();
    	}
    }

}
