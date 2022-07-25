<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\NewWorksheet;
use App\PackingSea;
use DB;
use Excel;
use App\Exports\NewWorksheetExport;
use App\Exports\PackingSeaExport;
use Auth;
use App\NewPacking;
use App\Invoice;
use App\Manifest;
use App\ReceiptArchive;
use App\Receipt;
use \Dejurin\GoogleTranslateForFree;
use App\Warehouse;
use App\SignedDocument;


class NewWorksheetController extends AdminController
{
	
	private $status_arr = ["Доставляется на склад в стране отправителя", "Возврат", "Коробка", "Забрать", "Уточнить", "Думают", "Отмена", "Подготовка", "Дубль","Пакинг лист"];
	private $status_arr_2 = ["На таможне в стране отправителя", "На складе в стране отправителя", "Доставляется на склад в стране отправителя", "Возврат", "Коробка", "Забрать", "Уточнить", "Думают", "Отмена", "Подготовка", "Дубль","Пакинг лист"];
    

    public function index(){
        $title = 'Новый рабочий лист';
        
        //dd(NewWorksheet::find(1014)->courierTask);
        
        // Auto-update status
        $update_date = Date('Y-m-d', strtotime('-7 days'));
        NewWorksheet::where('in_trash',false)->where([
        	['status_date','<=',$update_date],
        	['status_date','<>',null],
        	['status',"Доставляется на склад в стране отправителя"]
        ]) 
        ->orWhere([
        	['status_date','<=',$update_date],
        	['status_date','<>',null],
        	['status',"На складе в стране отправителя"]
        ])            
        ->update([
        	'status' => "Доставляется в страну получателя",
        	'status_en' => "Forwarding to the receiver country",
        	'status_ua' => "Доставляється в країну отримувача",
        	'status_he' => " נשלח למדינת המקבל",
        	'status_date' => date('Y-m-d')
        ]);
        
        $new_worksheet_obj = NewWorksheet::where('in_trash',false)->paginate(10);     

        $arr_columns = parent::new_columns();

        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR;

        $update_all_statuses = NewWorksheet::where('in_trash',false)->where('update_status_date','=', date('Y-m-d'))->get()->count();
        
        return view('admin.new_worksheet', ['title' => $title,'new_worksheet_obj' => $new_worksheet_obj,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4], 'user' => $user, 'viewer_arr' => $viewer_arr, 'update_all_statuses' => $update_all_statuses]);
    }


	public function show($id)
	{
		$arr_columns = parent::new_columns();
		$new_worksheet = NewWorksheet::find($id);
		$title = 'Изменение строки '.$new_worksheet->id;
		$user = Auth::user();
		$israel_cities = $this->israelCities();

		return view('admin.new_worksheet_update', ['title' => $title,'new_worksheet' => $new_worksheet, 'user' => $user,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4],'israel_cities' => $israel_cities]);
	}


	private function validateUpdate($request, $id, $new_worksheet){
		$status_error = '';
		if (!$request->input('status')) return 'ERROR STATUS!';
		
		$status_error = $this->checkStatus('new_worksheet', $id, $request->input('status'));
		if($status_error) return $status_error;

		if ($request->input('recipient_phone')) {
			$status_error = $this->checkConsigneePhone($request->input('recipient_phone'), 'ru');
			if($status_error) return $status_error;
		}
		
		if ($request->input('tracking_main')) {			
			$status_error = $this->checkTracking("new_worksheet", $request->input('tracking_main'), $id);
			if($status_error) return $status_error;
		}
		elseif (!$request->input('tracking_main') && ($request->input('batch_number') || $request->input('pallet_number'))){
			$status_error = "Нельзя ввести номер партии или паллеты без трекинг-номера";
			return $status_error;
		}
		
		if ($request->input('pay_sum')){
			if (in_array($request->input('status'), $this->status_arr)){
				$status_error = "ВНИМАНИЕ! ПРИ ПОЛУЧЕНИИ ОПЛАТЫ СТАТУС НЕ МОЖЕТ БЫТЬ - '".$request->input('status')."'. ДОБАВЬТЕ ЗАПИСЬ ОБ ОПЛАТЕ В ПРАВИЛЬНУЮ СТРОКУ ИЛИ ИЗМЕНИТЕ СТАТУС";
				return $status_error;
			}
		}
		
		if ($request->input('pallet_number')){
			if (in_array($request->input('status'), $this->status_arr)){
				$status_error = "ВНИМАНИЕ! ПРИ ДОБАВЛЕНИИ НОМЕРА ПАЛЛЕТЫ СТАТУС НЕ МОЖЕТ БЫТЬ - '".$request->input('status')."'. ДОБАВЬТЕ ЗАПИСЬ О НОМЕРЕ ПАЛЛЕТЫ В ПРАВИЛЬНУЮ СТРОКУ ИЛИ ИЗМЕНИТЕ СТАТУС";
				return $status_error;
			}			
		}

		return $status_error;		
	}


	public function update(Request $request, $id)
	{
		$new_worksheet = NewWorksheet::find($id);	
		$this->toUpdatesArchive($request,$new_worksheet);	
		$old_tracking = $new_worksheet->tracking_main;
		$old_pallet = $new_worksheet->pallet_number;
		$old_batch_number = $new_worksheet->batch_number;
		$old_status = $new_worksheet->status;
		$arr_columns = parent::new_columns();
		$fields = $this->getTableColumns('new_worksheet');
		$status_error = '';
		$status = 'Строка успешно обновлена!';
		$check_result = '';		

		for ($i=0; $i < count($arr_columns); $i++) { 
			if ($arr_columns[$i]) {
				$fields[] = 'new_column_'.($i+1);
			}
		}	

		$status_error = $this->validateUpdate($request, $id, $new_worksheet);
		if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);	

		foreach($fields as $field){	
			if ($field !== 'created_at') {
				$new_worksheet->$field = $request->input($field);
			}
		}

		if (in_array($new_worksheet->sender_city, array_keys($this->israel_cities))) {
			$new_worksheet->shipper_region = $this->israel_cities[$new_worksheet->sender_city];
		}

		if ($request->input('tracking_main')){

			$check_result .= $this->updateStatusByTracking('new_worksheet', $new_worksheet);

			if ($old_batch_number !== $new_worksheet->batch_number) {
				if (in_array($old_status, $this->status_arr_2)){
					$new_worksheet->status = "Доставляется в страну получателя";
					$new_worksheet->status_en = "Forwarding to the receiver country";
					$new_worksheet->status_he = " נשלח למדינת המקבל";
					$new_worksheet->status_ua = "Доставляється в країну відправника";
				}				
			}			

			$date_result = (strtotime('2021-09-20') <= strtotime(str_replace('.', '-', $new_worksheet->date)));
			if ($date_result) {
				
				if ($old_tracking && $request->input('tracking_main')) {
					ReceiptArchive::where('tracking_main', $old_tracking)->delete();
					if ($old_tracking !== $request->input('tracking_main')) {
						Receipt::where('tracking_main', $old_tracking)->update(
							['tracking_main' => $request->input('tracking_main')]
						);
					}
				}
				
				$notification = ReceiptArchive::where('tracking_main', $request->input('tracking_main'))->first();
				if (!$notification) {
					$check_result .= $this->checkReceipt($id, null, 'ru', $request->input('tracking_main'),null,$old_tracking);
				}
				
				if ($status_error) {
					if ($check_result) {
						$status_error .= ' '.$check_result;
					}
				}
				else{
					if ($check_result) {
						$status .= ' '.$check_result;
					}
				}
			}			
		}	

		if ($old_status !== $new_worksheet->status) {
			NewWorksheet::where('id', $id)
			->update([
				'status_date' => date('Y-m-d')
			]);
		}	

		$temp = rtrim($request->input('package_content'), ";");
		$content_arr = explode(";",$temp);		
		
		if ($content_arr[0]) {

			$tr = new GoogleTranslateForFree();

			$this->updateInvoice($request, $id, $tr);
			$this->updateNewPacking($request, $id, $tr, $content_arr);			
			$this->updateManifest($request, $id, $tr, $content_arr);									
			
			$new_worksheet->save();
			$new_worksheet->checkCourierTask($new_worksheet->status);

			// Adding order number
			if ($new_worksheet->standard_phone) {
				$this->addingOrderNumber($new_worksheet->standard_phone, 'ru');
			}

			if ($request->input('tracking_main')) {
				// Check for missing tracking
				$this->checkForMissingTracking($request->input('tracking_main'));
				
				// Update Warehouse pallet
				if ($old_pallet !== $request->input('pallet_number') || $old_tracking !== $request->input('tracking_main')) {
					$message = $this->updateWarehousePallet($old_tracking, $request->input('tracking_main'), $old_pallet, $request->input('pallet_number'), $old_batch_number, $new_worksheet->batch_number, 'ru', $new_worksheet);
					if ($message) {
						return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
					}			
				}

				// Update Warehouse lot
				if ($old_batch_number !== $new_worksheet->batch_number){
					$this->updateWarehouseLot($request->input('tracking_main'), $new_worksheet->batch_number, 'ru');
				}
			}
			
			if ($status_error) {
				return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
			}
			else{
				return redirect()->to(session('this_previous_url'))->with('status', $status);
			}			
		}		
		else{
			return redirect('/admin/new-worksheet')->with('status-error', 'Ошибка колонки Содержание!');
		}		
	}


	private function updateInvoice($request, $id, $tr){
		$invoice_num = 1;
		$result = Invoice::where('number','<>', null)->latest()->first();
		if ($result) {
			$invoice_num = (int)$result->number + 1;
		}
		$address = '';
		if ($request->input('recipient_postcode')) {
			$address .= $request->input('recipient_postcode').', ';
		}
		if ($request->input('region')) {
			$address .= $this->translit($request->input('region')).', ';
		}
		if ($request->input('district')) {
			$address .= $this->translit($request->input('district')).', ';
		}
		if ($request->input('recipient_city')) {
			$address .= $this->translit($request->input('recipient_city')).', ';
		}
		if ($request->input('recipient_street')) {
			$address .= $this->translit($request->input('recipient_street')).', ';
		}
		if ($request->input('recipient_house')) {
			$address .= $request->input('recipient_house');
		}	
		if ($request->input('body')) {
			$address .= '/'.$this->translit($request->input('body'));
		}
		if ($request->input('recipient_room')) {
			$address .= ', '.$request->input('recipient_room');					
		}

		$has_post = Invoice::where('work_sheet_id', $id)->first();
		if ($has_post) {
			if ($has_post->number){
				Invoice::where('work_sheet_id', $id)
				->update([
					'tracking' => $request->input('tracking_main'),
					'tracking' => $request->input('tracking_main'),
					'shipper_name' => $this->translit($request->input('sender_name')),
					'shipper_address_phone' => $this->translit($request->input('sender_city').', '.$request->input('sender_address')).'; '.$request->input('standard_phone'),
					'consignee_name' => $this->translit($request->input('recipient_name')),
					'consignee_address' => $address,
					'shipped_items' => $tr->translate('ru', 'en', $request->input('package_content'), 5),
					'weight' => $request->input('weight'),
					'height' => $request->input('height'),
					'length' => $request->input('length'),
					'width' => $request->input('width'),
					'batch_number' => $request->input('batch_number'),
					'declared_value' => $request->input('package_cost')
				]);
			}
			else{
				Invoice::where('work_sheet_id', $id)
				->update([
					'number' => $invoice_num,
					'tracking' => $request->input('tracking_main'),
					'shipper_name' => $this->translit($request->input('sender_name')),
					'shipper_address_phone' => $this->translit($request->input('sender_city').', '.$request->input('sender_address')).'; '.$request->input('standard_phone'),
					'consignee_name' => $this->translit($request->input('recipient_name')),
					'consignee_address' => $address,
					'shipped_items' => $tr->translate('ru', 'en', $request->input('package_content'), 5),
					'weight' => $request->input('weight'),
					'height' => $request->input('height'),
					'length' => $request->input('length'),
					'width' => $request->input('width'),
					'batch_number' => $request->input('batch_number'),
					'declared_value' => $request->input('package_cost')
				]);
			}
		}
		else{
			$invoice = new Invoice();
			$invoice->number = $invoice_num;
			$invoice->work_sheet_id = $id;
			$invoice->tracking = $request->input('tracking_main');
			$invoice->shipper_name = $this->translit($request->input('sender_name'));
			$invoice->shipper_address_phone = $this->translit($request->input('sender_city').', '.$request->input('sender_address')).'; '.$request->input('standard_phone');
			$invoice->consignee_name = $this->translit($request->input('recipient_name'));
			$invoice->consignee_address = $address;
			$invoice->shipped_items = $tr->translate('ru', 'en', $request->input('package_content'), 5);
			$invoice->weight = $request->input('weight');
			$invoice->height = $request->input('height');
			$invoice->length = $request->input('length');
			$invoice->width = $request->input('width');
			$invoice->batch_number = $request->input('batch_number');
			$invoice->declared_value = $request->input('package_cost');
			$invoice->save();
		}
		
		return true;
	}


	private function updateNewPacking($request, $id, $tr, $content_arr){
		NewPacking::where('work_sheet_id', $id)
		->update([
			'track_code' => $request->input('tracking_main'),
			'type' => $request->input('tariff'),
			'full_shipper' => $request->input('sender_name'),
			'full_consignee' => $request->input('recipient_name'),
			'country_code' => $request->input('recipient_country'),
			'postcode' => $request->input('recipient_postcode'),
			'region' => $request->input('region'),
			'district' => $request->input('district'),
			'city' => $request->input('recipient_city'),
			'street' => $request->input('recipient_street'),
			'house' => $request->input('recipient_house'),
			'body' => $request->input('body'),
			'room' => $request->input('recipient_room'),
			'phone' => $request->input('recipient_phone'),
			'batch_number' => $request->input('batch_number'),
			'weight_kg' => $request->input('weight')
		]);

		if ($request->input('package_content')) {				
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
						$new_packing->weight_kg = $request->input('weight');
						$new_packing->batch_number = $request->input('batch_number');
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


	private function updateManifest($request, $id, $tr, $content_arr){	
		$result = Manifest::where('number','<>', null)->latest()->first();
		$manifest_num = 1;
		if ($result) {
			$manifest_num = (int)$result->number + 1;
		}	
		
		$address = '';
		if ($request->input('recipient_postcode')) {
			$address .= $request->input('recipient_postcode').', ';
		}
		if ($request->input('region')) {
			$address .= $this->translit($request->input('region')).', ';
		}
		if ($request->input('district')) {
			$address .= $this->translit($request->input('district')).', ';
		}
		if ($request->input('recipient_city')) {
			$address .= $this->translit($request->input('recipient_city')).', ';
		}
		if ($request->input('recipient_street')) {
			$address .= $this->translit($request->input('recipient_street')).', ';
		}
		if ($request->input('recipient_house')) {
			$address .= $request->input('recipient_house');
		}	
		if ($request->input('body')) {
			$address .= '/'.$this->translit($request->input('body'));
		}
		if ($request->input('recipient_room')) {
			$address .= ', '.$request->input('recipient_room');					
		}

		$has_post = Manifest::where('work_sheet_id', $id)->first();
		Manifest::where('work_sheet_id', $id)
		->update([
			'tracking' => $request->input('tracking_main'),
			'sender_country' => $tr->translate('ru', 'en', $request->input('sender_country'), 5),
			'sender_name' => $this->translit($request->input('sender_name')),
			'recipient_name' => $this->translit($request->input('recipient_name')),
			'recipient_city' => $this->translit($request->input('recipient_city')),
			'recipient_address' => $address,
			'weight' => $request->input('weight'),
			'batch_number' => $request->input('batch_number'),
			'cost' => $request->input('package_cost')
		]);

		if ($request->input('package_content')) {

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
						$new_packing->tracking = $request->input('tracking_main');
						$new_packing->sender_country = $tr->translate('ru', 'en', $request->input('sender_country'), 5);
						$new_packing->sender_name = $this->translit($request->input('sender_name'));
						$new_packing->recipient_name = $this->translit($request->input('recipient_name'));
						$new_packing->recipient_city = $this->translit($request->input('recipient_city'));
						$new_packing->recipient_address = $address;
						$new_packing->weight = $request->input('weight');
						$new_packing->cost = $request->input('package_cost');
						$new_packing->batch_number = $request->input('batch_number');
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


	public function destroy(Request $request)
	{
		$id = $request->input('action');
		$this->removeTrackingFromPalletWorksheet($id, 'ru');
		$this->deleteUploadFiles('worksheet_id',$id);
		$worksheet = NewWorksheet::find($id);
		$this->deletedToUpdatesArchive($worksheet);
		
		NewWorksheet::where('id', $id)->delete();
		NewPacking::where('work_sheet_id', $id)->delete();
		Invoice::where('work_sheet_id', $id)->delete();
		Manifest::where('work_sheet_id', $id)->delete();
		ReceiptArchive::where('worksheet_id', $id)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно удалена!');
	}


	public function addColumn()
	{
		$message = 'Колонка успешно добавлена!';
		
		if (!Schema::hasColumn('new_worksheet', 'new_column_1'))
		{
			Schema::table('new_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_1')->nullable();
			});
		}
		else if (!Schema::hasColumn('new_worksheet', 'new_column_2'))
		{
			Schema::table('new_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_2')->nullable();
			});
		}
		else if (!Schema::hasColumn('new_worksheet', 'new_column_3'))
		{
			Schema::table('new_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_3')->nullable();
			});
		}
		else if (!Schema::hasColumn('new_worksheet', 'new_column_4'))
		{
			Schema::table('new_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_4')->nullable();
			});
		}
		else if (!Schema::hasColumn('new_worksheet', 'new_column_5'))
		{
			Schema::table('new_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_5')->nullable();
			});
		}
		else
		{
			return redirect()->to(session('this_previous_url'))->with('status-error', 'Лимит колонок исчерпан!');
		}

		return redirect()->to(session('this_previous_url'))->with('status', $message);
	}


	public function deleteColumn(Request $request)
	{
		$name_column = $request->input('name_column');

		if ($name_column === 'new_column_1') {
			Schema::table('new_worksheet', function($table)
			{
				$table->dropColumn('new_column_1');
			});
		}
		elseif ($name_column === 'new_column_2') {
			Schema::table('new_worksheet', function($table)
			{
				$table->dropColumn('new_column_2');
			});
		}
		elseif ($name_column === 'new_column_3') {
			Schema::table('new_worksheet', function($table)
			{
				$table->dropColumn('new_column_3');
			});
		}
		elseif ($name_column === 'new_column_4') {
			Schema::table('new_worksheet', function($table)
			{
				$table->dropColumn('new_column_4');
			});
		}
		elseif ($name_column === 'new_column_5') {
			Schema::table('new_worksheet', function($table)
			{
				$table->dropColumn('new_column_5');
			});
		}

		return redirect()->to(session('this_previous_url'))->with('status', 'Колонка успешно удалена!');
	}


	public function showNewStatus(){
        $title = 'Изменение статусов по номеру партии';
        $worksheet_obj = NewWorksheet::where('in_trash',false)->get();
        $number_arr = [];
        foreach ($worksheet_obj as $row) {
        	if (!in_array($row->batch_number, $number_arr)) {
        		$number_arr[$row->batch_number] = $row->batch_number;
        	}
        }
        return view('admin.new_worksheet_status_number', ['title' => $title,'number_arr' => $number_arr]);
    }


    public function changeNewStatus(Request $request){
        if ($request->input('batch_number') && $request->input('status')) {
        	$request->by_lot = true;
        	$worksheet_arr = NewWorksheet::where([
        		['batch_number', $request->input('batch_number')],
        		['tracking_main','<>',null]
        	])->get();
        	foreach ($worksheet_arr as $worksheet) {
        		$this->toUpdatesArchive($request,$worksheet);
        	}

        	DB::table('new_worksheet')
        	->where('in_trash',false)
        	->where([
        		['batch_number', $request->input('batch_number')],
        		['tracking_main','<>',null]
        	])
          	->update([
          		'status' => $request->input('status'), 
          		'status_en' => $request->input('status_en'),
          		'status_ua' => $request->input('status_ua'),
          		'status_he' => $request->input('status_he'),
          		'status_date' => date('Y-m-d')
          	]);
        }
        return redirect()->to(session('this_previous_url'));
    }


    public function showNewStatusDate(){
        $title = 'Изменение статусов по дате';
        $worksheet_obj = NewWorksheet::where('in_trash',false)->get();
        $date_arr = [];
        foreach ($worksheet_obj as $row) {
        	if (!in_array($row->date, $date_arr)) {
        		$date_arr[$row->date] = $row->date;
        	}
        }
        return view('admin.new_worksheet_status_date', ['title' => $title,'date_arr' => $date_arr]);
    }


    public function changeNewStatusDate(Request $request){
        if ($request->input('date') && $request->input('status')) {
        	$request->by_lot = true;
        	$worksheet_arr = NewWorksheet::where([
        		['date', $request->input('date')],
        		['tracking_main','<>',null]
        	])->get();
        	foreach ($worksheet_arr as $worksheet) {
        		$this->toUpdatesArchive($request,$worksheet);
        	}
        	
        	DB::table('new_worksheet')
        	->where('in_trash',false)
        	->where([
        		['date', $request->input('date')],
        		['tracking_main','<>',null]
        	])
          	->update([
          		'status' => $request->input('status'), 
          		'status_en' => $request->input('status_en'),
          		'status_ua' => $request->input('status_ua'),
          		'status_he' => $request->input('status_he'),
          		'status_date' => date('Y-m-d')
          	]);
        }
        return redirect()->to(session('this_previous_url'));
    }


    public function showNewData(){
        $title = 'Массовое изменение данных по трекингу (поддерживает массовое выделение чекбоксов)';
        $worksheet_obj = NewWorksheet::where('in_trash',false)->orderBy('tracking_main')->get();
        $date_arr = [];
        foreach ($worksheet_obj as $row) {
        	$temp = $row->tracking_main;
        	if (strripos($row->tracking_main, ', ') !== false) {
        		$temp = explode(', ', $row->tracking_main)[0];
        	}
        	if (is_numeric($temp)) {
        		if (!in_array($row->tracking_main, $date_arr)) {
        			$date_arr[$row->tracking_main] = $row->tracking_main;
        		}
        	}
        	if ($this->checkWhichAdmin($temp)) {
        		if (!in_array($row->tracking_main, $date_arr)) {
        			$date_arr[$row->tracking_main] = $row->tracking_main;
        		}
        	}
        }
        return view('admin.new_worksheet_tracking_data', ['title' => $title,'date_arr' => $date_arr]);
    }


    public function addNewData(Request $request){
    	$track_arr = $request->input('tracking');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('tracking-columns');
    	$status_error = '';
    	$check_column = 'tracking_main';
    	$old_lot_arr = [];
    	$old_pallet_arr = [];
    	
    	if ($track_arr) {
    		if ($value_by && $column) {

				$status_error = $this->checkColumns($track_arr, $value_by, $column, $check_column, 'new_worksheet');   				
				if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);

    			for ($i=0; $i < count($track_arr); $i++) { 
    				$worksheet = NewWorksheet::where('tracking_main',$track_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			} 

				if ($column === 'batch_number') {
    				for ($i=0; $i < count($track_arr); $i++) { 
    					$worksheet = NewWorksheet::where('tracking_main',$track_arr[$i])->first();
    					$old_lot_arr[] = $worksheet->batch_number;
    				}
    			}

    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($track_arr); $i++) { 
    					$worksheet = NewWorksheet::where('tracking_main',$track_arr[$i])->first();
    					$old_pallet_arr[] = $worksheet->pallet_number;
    				}
    			}
    			
    			NewWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				$column => $value_by
    			]); 

    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($track_arr); $i++) { 
    					$worksheet = NewWorksheet::where('tracking_main',$track_arr[$i])->first();
    					if ($old_pallet_arr[$i] !== $value_by){
    						$message = $this->updateWarehousePallet($worksheet->tracking_main, $worksheet->tracking_main, $old_pallet_arr[$i], $value_by, $worksheet->batch_number, $worksheet->batch_number, 'ru', $worksheet);
    						if ($message) {
    							return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
    						}
    					}
    				}
    			}

    			if ($column === 'batch_number') {
    				NewWorksheet::whereIn('tracking_main', $track_arr)
    				->whereIn('status',$this->status_arr_2)
    				->update([
    					'status' => "Доставляется в страну получателя",
    					'status_en' => "Forwarding to the receiver country",
    					'status_he' => " נשלח למדינת המקבל",
    					'status_ua' => "Forwarding to the receiver country",
    					'status_date' => date('Y-m-d')
    				]);

    				for ($i=0; $i < count($track_arr); $i++) { 
    					if ($old_lot_arr[$i] !== $value_by){
    						$worksheet = NewWorksheet::where('tracking_main',$track_arr[$i])->first();
    						$this->updateWarehouseLot($worksheet->tracking_main, $value_by, 'ru');
    					}
    				}
    			} 
				
				$this->updateAllPackingByTracking($track_arr, $value_by, $column);

    		}
    		else if ($request->input('status')){
    			for ($i=0; $i < count($track_arr); $i++) { 
    				$worksheet = NewWorksheet::where('tracking_main',$track_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}
    			
    			NewWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'status' => $request->input('status'), 
    				'status_en' => $request->input('status_en'),
    				'status_ua' => $request->input('status_ua'),
    				'status_he' => $request->input('status_he'),
    				'status_date' => date('Y-m-d')
    			]);
    		}
    		else if ($request->input('site_name')) {
    			for ($i=0; $i < count($track_arr); $i++) { 
    				$worksheet = NewWorksheet::where('tracking_main',$track_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}
    			
    			NewWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'site_name' => $request->input('site_name')
    			]);       	
    		}
    		else if ($request->input('tariff')) {
    			NewWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'tariff' => $request->input('tariff')
    			]);       	
    		}
    		else if ($request->input('partner')) {
    			NewWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'partner' => $request->input('partner')
    			]);       	
    		}   
    		else if ($request->input('sender_city')) {
    			for ($i=0; $i < count($track_arr); $i++) { 
    				$worksheet = NewWorksheet::where('tracking_main',$track_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}
    			
    			NewWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'sender_city' => $request->input('sender_city')
    			]);  

    			if (in_array($request->input('sender_city'), array_keys($this->israel_cities))) {
    				NewWorksheet::whereIn('tracking_main', $track_arr)
    				->update([
    					'shipper_region' => $this->israel_cities[$request->input('sender_city')]
    				]);
    			}
    		} 		
    	}
        
        if($status_error){
        	return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
        }
        else{
        	return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно изменены!');
        }       
    }


    private function updateAllPackingByTracking($track_arr, $value_by, $column){
    	// Update Invoice 
    	$tr = new GoogleTranslateForFree();  

    	$invoice_arr = ['weight' => 'weight', 'height' => 'height', 'length' => 'length', 'width' => 'width', 'package_cost' => 'declared_value', 'batch_number' => 'batch_number']; 
    	if (array_key_exists($column, $invoice_arr)) {
    		Invoice::whereIn('tracking', $track_arr)
    		->update([
    			$invoice_arr[$column] => $value_by
    		]);
    	}    	

    	if ($column === 'package_content') {
    		Invoice::whereIn('tracking', $track_arr)
    		->update([
    			'shipped_items' => $tr->translate('ru', 'en', $value_by, 5)
    		]);
    	} 
    	if ($column === 'sender_name') {
    		Invoice::whereIn('tracking', $track_arr)
    		->update([
    			'shipper_name' => $this->translit($value_by)
    		]);
    	} 
    	if ($column === 'recipient_name') {
    		Invoice::whereIn('tracking', $track_arr)
    		->update([
    			'consignee_name' => $this->translit($value_by)
    		]);
    	} 
    	if ($column === 'package_content') {
    		Invoice::whereIn('tracking', $track_arr)
    		->update([
    			'shipped_items' => $tr->translate('ru', 'en', $value_by, 5)
    		]);
    	} 
    	// End Update Invoice

		// Update New Packing	
    	$new_packing_arr = ['tariff' => 'type', 'sender_name' => 'full_shipper', 'recipient_name' => 'full_consignee', 'recipient_country' => 'country_code', 'recipient_postcode' => 'postcode', 'region' => 'region', 'district' => 'district', 'recipient_city' => 'city', 'recipient_street' => 'street', 'recipient_house' => 'house', 'body' => 'body', 'recipient_room' => 'room', 'recipient_phone' => 'phone', 'weight' => 'weight_kg', 'batch_number' => 'batch_number']; 
    	if (array_key_exists($column, $new_packing_arr)){
    		NewPacking::whereIn('track_code', $track_arr)
    		->update([
    			$new_packing_arr[$column] => $value_by
    		]);
    	}   	
		// End Update New Packing

		// Update Manifest
    	$manifest_arr = ['weight' => 'weight', 'package_cost' => 'cost', 'sender_name' => 'sender_name', 'recipient_name' => 'recipient_name', 'batch_number' => 'batch_number']; 
    	if (array_key_exists($column, $manifest_arr)){
    		Manifest::whereIn('tracking', $track_arr)
    		->update([
    			$manifest_arr[$column] => $this->translit($value_by)
    		]);
    	}    	
		// End Update Manifest

		return true;
    }


    private function updateAllPackingById($row_arr, $value_by, $column){
    	// Update Invoice 
    	$tr = new GoogleTranslateForFree();  

    	$invoice_arr = ['weight' => 'weight', 'height' => 'height', 'length' => 'length', 'width' => 'width', 'package_cost' => 'declared_value', 'batch_number' => 'batch_number', 'tracking_main' => 'tracking']; 

    	if (array_key_exists($column, $invoice_arr)) {
    		Invoice::whereIn('work_sheet_id', $row_arr)
    		->update([
    			$invoice_arr[$column] => $value_by
    		]);
    	}

    	if ($column === 'package_content') {
    		Invoice::whereIn('work_sheet_id', $row_arr)
    		->update([
    			'shipped_items' => $tr->translate('ru', 'en', $value_by, 5)
    		]);
    	} 
    	if ($column === 'sender_name') {
    		Invoice::whereIn('work_sheet_id', $row_arr)
    		->update([
    			'shipper_name' => $this->translit($value_by)
    		]);
    	} 
    	if ($column === 'recipient_name') {
    		Invoice::whereIn('work_sheet_id', $row_arr)
    		->update([
    			'consignee_name' => $this->translit($value_by)
    		]);
    	} 
    	if ($column === 'package_content') {
    		Invoice::whereIn('work_sheet_id', $row_arr)
    		->update([
    			'shipped_items' => $tr->translate('ru', 'en', $value_by, 5)
    		]);
    	} 
		// End Update Invoice

		// Update New Packing	
    	$new_packing_arr = ['tariff' => 'type', 'sender_name' => 'full_shipper', 'recipient_name' => 'full_consignee', 'recipient_country' => 'country_code', 'recipient_postcode' => 'postcode', 'region' => 'region', 'district' => 'district', 'recipient_city' => 'city', 'recipient_street' => 'street', 'recipient_house' => 'house', 'body' => 'body', 'recipient_room' => 'room', 'recipient_phone' => 'phone', 'weight' => 'weight_kg', 'batch_number' => 'batch_number', 'tracking_main' => 'track_code']; 

    	if (array_key_exists($column, $new_packing_arr)){
    		NewPacking::whereIn('work_sheet_id', $row_arr)
    		->update([
    			$new_packing_arr[$column] => $value_by
    		]);
    	}   	
		// End Update New Packing

		// Update Manifest
    	$manifest_arr = ['weight' => 'weight', 'package_cost' => 'cost', 'sender_name' => 'sender_name', 'recipient_name' => 'recipient_name', 'batch_number' => 'batch_number', 'tracking_main' => 'tracking']; 
    	if (array_key_exists($column, $manifest_arr)){
    		Manifest::whereIn('work_sheet_id', $row_arr)
    		->update([
    			$manifest_arr[$column] => $this->translit($value_by)
    		]);
    	}				
		// End Update Manifest 

		return true;
    }


    public function addNewDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('tracking-columns');
    	$status_error = '';
    	$check_column = 'id';
    	$old_lot_arr = [];
    	$old_pallet_arr = [];
    	$color = $request->input('tr_color');   
    	$check_result = ''; 	

    	if ($row_arr) {
    		if ($color) {
    			if ($color !== 'transparent') {
    				NewWorksheet::whereIn('id', $row_arr)
    				->update([
    					'background' => $color
    				]);
    			}
    			else{
    				NewWorksheet::whereIn('id', $row_arr)
    				->update([
    					'background' => null
    				]);
    			}
    		}
    		elseif ($value_by && $column) {
    			
    			$status_error = $this->checkColumns($row_arr, $value_by, $column, $check_column, 'new_worksheet');
    			if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);

    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = NewWorksheet::where('id',$row_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}

    			if ($column === 'batch_number') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = NewWorksheet::where('id',$row_arr[$i])->first();
    					$old_lot_arr[] = $worksheet->batch_number;
    				}
    			}

    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = NewWorksheet::where('id',$row_arr[$i])->first();
    					$old_pallet_arr[] = $worksheet->pallet_number;
    				}
    			}

    			if ($column === 'tracking_main') {   				    				
    				for ($i=0; $i < count($row_arr); $i++) { 

    					$error_message = $this->checkTracking("new_worksheet", $value_by, $row_arr[$i]);
    					if($error_message) return redirect()->to(session('this_previous_url'))->with('status-error', $error_message);
    					
    					$worksheet = NewWorksheet::where('id',$row_arr[$i])->first();
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

    					$check_result .= $this->updateStatusByTracking('new_worksheet', $worksheet);		
    				}

    				// Check for missing tracking
    				$this->checkForMissingTracking($value_by);
    				// Update Warehouse pallet
    				$message = $this->updateWarehousePallet($old_tracking, $value_by, $pallet, $pallet, $lot, $lot, 'ru', $worksheet);
    				if ($message) {
    					return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
    				}					
    			}
    			
    			NewWorksheet::whereIn('id', $row_arr)
    			->update([
    				$column => $value_by
    			]); 

    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = NewWorksheet::where('id',$row_arr[$i])->first();
    					if ($old_pallet_arr[$i] !== $value_by){
    						$message = $this->updateWarehousePallet($worksheet->tracking_main, $worksheet->tracking_main, $old_pallet_arr[$i], $value_by, $worksheet->batch_number, $worksheet->batch_number, 'ru', $worksheet);
    						if ($message) {
    							return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
    						}
    					}
    				}
    			}

    			if ($column === 'batch_number') {
    				NewWorksheet::whereIn('id', $row_arr)
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
    						$worksheet = NewWorksheet::where('id',$row_arr[$i])->first();
    						$worksheet->checkCourierTask($worksheet->status);
    						$this->updateWarehouseLot($worksheet->tracking_main, $value_by, 'ru');
    					}
    				}
    			} 

    			$this->updateAllPackingById($row_arr, $value_by, $column);    	
    		}
    		else if ($request->input('status')){
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$status_error = $this->checkStatus('new_worksheet', $row_arr[$i], $request->input('status'));
    				if (!$status_error) {

	    				$worksheet = NewWorksheet::where('id',$row_arr[$i])->first();
	    				$this->toUpdatesArchive($request,$worksheet);
    					
    					NewWorksheet::where('id', $row_arr[$i])
    					->update([
    						'status' => $request->input('status'), 
    						'status_en' => $request->input('status_en'),
    						'status_ua' => $request->input('status_ua'),
    						'status_he' => $request->input('status_he'),
    						'status_date' => date('Y-m-d')
    					]);
    				}

    				$worksheet = NewWorksheet::find($row_arr[$i]);
    				$worksheet->checkCourierTask($worksheet->status);
    			}    			
    		}
    		else if ($request->input('site_name')) {

    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = NewWorksheet::where('id',$row_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}
    			
    			NewWorksheet::whereIn('id', $row_arr)
    			->update([
    				'site_name' => $request->input('site_name')
    			]);     

    			
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = NewWorksheet::find($row_arr[$i]);
    				$worksheet->checkCourierTask($worksheet->status);
    			}    	
    		}
    		else if ($request->input('tariff')) {
    			NewWorksheet::whereIn('id', $row_arr)
    			->update([
    				'tariff' => $request->input('tariff')
    			]);       	
    		}
    		else if ($request->input('partner')) {
    			NewWorksheet::whereIn('id', $row_arr)
    			->update([
    				'partner' => $request->input('partner')
    			]);       	
    		} 
    		else if ($request->input('sender_city')) {

    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = NewWorksheet::where('id',$row_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}
    			
    			NewWorksheet::whereIn('id', $row_arr)
    			->update([
    				'sender_city' => $request->input('sender_city')
    			]);  

    			if (in_array($request->input('sender_city'), array_keys($this->israel_cities))) {
    				NewWorksheet::whereIn('id', $row_arr)
    				->update([
    					'shipper_region' => $this->israel_cities[$request->input('sender_city')]
    				]);
    			}

    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = NewWorksheet::find($row_arr[$i]);
    				$worksheet->checkCourierTask($worksheet->status);
    			}
    		}

    		for ($i=0; $i < count($row_arr); $i++) { 
    			$worksheet = NewWorksheet::find($row_arr[$i]);
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


    public function deleteNewWorksheetById(Request $request)
	{
		$row_arr = $request->input('row_id');
		for ($i=0; $i < count($row_arr); $i++) { 
			$this->removeTrackingFromPalletWorksheet($row_arr[$i], 'ru');
			$this->deleteUploadFiles('worksheet_id',$row_arr[$i]);
			$worksheet = NewWorksheet::find($row_arr[$i]);
			$this->deletedToUpdatesArchive($worksheet);
		}

		NewWorksheet::whereIn('id', $row_arr)->delete();
		NewPacking::whereIn('work_sheet_id', $row_arr)->delete();
		Invoice::whereIn('work_sheet_id', $row_arr)->delete();
		Manifest::whereIn('work_sheet_id', $row_arr)->delete();
		ReceiptArchive::whereIn('worksheet_id', $row_arr)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно удалены!');
	}


    public function exportExcel()
	{
		ini_set('memory_limit', '256M');
    	return Excel::download(new NewWorksheetExport, 'NewWorksheetExport.xlsx');
	}


	public function indexPackingSea(){
        $title = 'Старый пакинг лист';
        $packing_sea_obj = PackingSea::where('in_trash',false)->get();       
        
        return view('admin.packing.packing_sea', ['title' => $title,'packing_sea_obj' => $packing_sea_obj]);
    }


	public function exportExcelPackingSea()
	{

    	return Excel::download(new PackingSeaExport, 'PackingSeaExport.xlsx');

	}


	public function newWorksheetFilter(Request $request){
        $title = 'Фильтр Нового рабочего листа';
        $search = $request->table_filter_value;
        $new_worksheet_arr = [];
        $attributes = NewWorksheet::first()->attributesToArray();

        $arr_columns = parent::new_columns();

        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR; 
        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$new_worksheet_obj = NewWorksheet::where('in_trash',false)->where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at' && $key !== 'update_status_date') {
        			$sheet = NewWorksheet::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = NewWorksheet::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get();
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$new_worksheet_arr[] = $new_arr;   				         		
        			}
        		}         		
        	}

        	return view('admin.new_worksheet_find', ['title' => $title,'new_worksheet_arr' => $new_worksheet_arr,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4], 'user' => $user, 'viewer_arr' => $viewer_arr]);      	
        }
        
        $data = $request->all();    
        $update_all_statuses = NewWorksheet::where('in_trash',false)->where('update_status_date','=', date('Y-m-d'))->get()->count();
        
        return view('admin.new_worksheet', ['title' => $title,'data' => $data,'new_worksheet_obj' => $new_worksheet_obj,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4], 'user' => $user, 'viewer_arr' => $viewer_arr, 'update_all_statuses' => $update_all_statuses]);
    }


    public function deactivate($id)
    {
    	$worksheet = NewWorksheet::find($id);
    	$draft = $worksheet->deactivateWorksheet();
    	$message = '';
    	
    	if ($draft) {
    		
    		if ($draft->pallet_number) {
				$this->updateWarehouse(null, $draft->pallet_number, $draft->tracking_main);
			}

			if ($draft->tracking_main) {
				ReceiptArchive::where([
					['tracking_main', $draft->tracking_main],
					['worksheet_id', null],
					['receipt_id', null]
				])->delete();
				$result = Receipt::where('tracking_main', $draft->tracking_main)->first();
				if (!$result) {
					$message = $this->checkReceipt($draft->id, null, 'ru', $draft->tracking_main);
				}
				$this->checkForMissingTracking($draft->tracking_main);
			}			

            // Transfer documents
            SignedDocument::where('worksheet_id',$id)
    		->update([
    			'worksheet_id' => null,
    			'draft_id' => $draft->id
    		]);					    		
    		
    		$worksheet->delete();
    		NewPacking::where('work_sheet_id', $id)->delete();
    		Invoice::where('work_sheet_id', $id)->delete();
    		Manifest::where('work_sheet_id', $id)->delete();

    		$draft->checkCourierTask($draft->status);
    		
    		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно возвращена в черновик! '.$message);
    	}
    	else{
			return redirect()->to(session('this_previous_url'))->with('status-error', 'Ошибка деактивации!');
		}
    }

}
