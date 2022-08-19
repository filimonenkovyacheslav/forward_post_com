<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\CourierEngDraftWorksheet;
use App\PhilIndWorksheet;
use App\PackingEng;
use App\PackingEngNew;
use Auth;
use Excel;
use DB;
use App\Exports\CourierEngDraftWorksheetExport;
use App\ReceiptArchive;
use App\Receipt;
use App\SignedDocument;


class CourierEngDraftController extends AdminController
{
	private $status_arr = ["Forwarding to the warehouse in the sender country", "Pending", "Return", "Box", "Pick up", "Specify", "Think", "Canceled", 'At the warehouse in the sender country', "Double","Packing list"];
	private $status_arr_2 = ["At the customs in the sender country", "At the warehouse in the sender country", "Forwarding to the warehouse in the sender country", "Pending", "Return", "Box", "Pick up", "Specify", "Think", "Canceled", "Double","Packing list"];
	private $status_arr_3 = ["Pending", "Return", "Box", "Pick up", "Specify", "Think", "Canceled", "Double","Packing list"];
	private $status_arr_4 = ["Pending", "Return", "Box", "Specify", "Think", "Canceled", "Double","Packing list"];
    

    public function index(Request $request){
        $title = 'Draft';
        // Auto-delete operator
        $delete_date = Date('Y-m-d', strtotime('-3 days'));
        CourierEngDraftWorksheet::where('in_trash',false)->where([
        	['status_date','<=',$delete_date],
        	['status_date','<>',null]
        ])
        ->whereNotIn('status', $this->status_arr_4)
        ->orWhere([
        	['status_date', null],
        	['created_at','<=',$delete_date]
        ])
        ->orWhere([
        	['status_date', null],
        	['updated_at','<=',$delete_date]
        ])       
        ->update([
        	'operator' => null
        ]);
        
        if ($request->input('for_active')) {
        	$courier_eng_draft_worksheet_obj = CourierEngDraftWorksheet::where('in_trash',false)->where('tracking_main','<>',null)
        	->orWhere('status','Pick up')
        	->paginate(10);
        }
        else{
        	$courier_eng_draft_worksheet_obj = CourierEngDraftWorksheet::where('in_trash',false)->paginate(10);
        }    
        $user = Auth::user();
        $data = $request->all();
        $viewer_arr = parent::VIEWER_ARR;
        
        return view('admin.courier_draft.courier_eng_draft_worksheet', ['title' => $title,'data' => $data,'courier_eng_draft_worksheet_obj' => $courier_eng_draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


	public function show($id)
	{
		$courier_eng_draft_worksheet = CourierEngDraftWorksheet::find($id);
		$title = 'Update row '.$courier_eng_draft_worksheet->id;
		$israel_cities = $this->israelCities();
		$to_country = $this->to_country_arr;

		return view('admin.courier_draft.courier_eng_draft_worksheet_update', compact('title','courier_eng_draft_worksheet','israel_cities','to_country'));
	}


	private function validateUpdate($request, $id)
	{
		$error_message = '';

		$message_pdf = $this->checkPdfId('eng_draft_id',$id);
		if ($message_pdf !== 'success') {
			return $message_pdf;
		}
		
		if ($request->input('tracking_main')) {
			$error_message = $this->checkTracking("courier_eng_draft_worksheet", $request->input('tracking_main'), $id);
			return $error_message;
		}
		elseif (!$request->input('tracking_main') && !in_array($request->input('status'), $this->status_arr_3)){
			$error_message = "Status cannot be higher than Pick up without tracking number";
			return $error_message;
		}	
		elseif (!$request->input('tracking_main') && ($request->input('lot') || $request->input('pallet_number'))){
			$error_message = "You cannot enter a lot or pallet number without a tracking number";
			return $error_message;
		}

		if ($request->input('consignee_phone')) {
			$error_message = $this->checkConsigneePhone($request->input('consignee_phone'), 'en');
			return $error_message;
		}
	}


	public function update(Request $request, $id)
	{
		$courier_eng_draft_worksheet = CourierEngDraftWorksheet::find($id);		
		$old_status = $courier_eng_draft_worksheet->status;
		$old_tracking = $courier_eng_draft_worksheet->tracking_main;
		$old_pallet = $courier_eng_draft_worksheet->pallet_number;
		$old_lot = $courier_eng_draft_worksheet->lot;
		$check_result = '';
		$fields = $this->getTableColumns('courier_eng_draft_worksheet');
		$operator_change = true;
		$user = Auth::user();	
		$status_error = '';			

		$status_error = $this->validateUpdate($request, $id);
		if (!$status_error) {
			$status_error = $this->checkStatus('courier_eng_draft_worksheet', $id, $request->input('status'));
		}
		if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);	

		if ($courier_eng_draft_worksheet->operator) $operator_change = false;

		$this->toUpdatesArchive($request,$courier_eng_draft_worksheet);

		foreach($fields as $field){			
			if ($field !== 'created_at' && $field !== 'operator' && $field !== 'tracking_main'){
				$courier_eng_draft_worksheet->$field = $request->input($field);
			}
			elseif ($field !== 'created_at' && ($user->role === 'admin' || $user->role === 'office_1')) {
				$courier_eng_draft_worksheet->$field = $request->input($field);
			}
		}

		if ($request->input('tracking_main')) {
			$check_result .= $this->updateStatusByTracking('courier_eng_draft_worksheet', $courier_eng_draft_worksheet);
		}

		$courier_eng_draft_worksheet->direction = $this->createDirection($request->input('shipper_country'), $request->input('consignee_country'));
		// New parcel form
		if (!$courier_eng_draft_worksheet->consignee_address) $courier_eng_draft_worksheet->consignee_address =  $request->input('consignee_country');

		if ($old_status !== $courier_eng_draft_worksheet->status) {
			CourierEngDraftWorksheet::where('id', $id)
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
		if (!$notification) $check_result = $this->checkReceipt($id, null, 'en', $request->input('tracking_main'),null,$old_tracking);
		
		if (in_array($courier_eng_draft_worksheet->shipper_city, array_keys($this->israel_cities))) {
			$courier_eng_draft_worksheet->shipper_region = $this->israel_cities[$courier_eng_draft_worksheet->shipper_city];
		}		
		
		if ($courier_eng_draft_worksheet->save()){

			$courier_eng_draft_worksheet->checkCourierTask($courier_eng_draft_worksheet->status);

			$this->addingOrderNumber($courier_eng_draft_worksheet->standard_phone, 'en');
			
			// Update Update Old Packing Eng
			$address = explode(' ',$request->input('consignee_address'));
			// New parcel form
			if ($request->input('consignee_country')) $consignee_country = $request->input('consignee_country');
			else $consignee_country = null;
			
			PackingEng::where('work_sheet_id', $id)
			->update([
				'tracking' => $request->input('tracking_main'),
				'country' => $consignee_country,
				'shipper_name' => $request->input('shipper_name'),
				'shipper_address' => $request->input('shipper_address'),
				'shipper_phone' => $request->input('standard_phone'),
				'shipper_id' => $request->input('shipper_id'),
				'consignee_name' => $request->input('consignee_name'),
				'consignee_address' => $request->input('consignee_address'),
				'consignee_phone' => $request->input('consignee_phone'),
				'consignee_id' => $request->input('consignee_id'),
				'length' => $request->input('length'),
				'width' => $request->input('width'),
				'height' => $request->input('height'),
				'weight' => $request->input('weight'),
				'items' => $request->input('shipped_items'),
				'shipment_val' => $request->input('shipment_val')
			]);			
			// End Update Old Packing Eng

			if ($request->input('tracking_main')) {
				// Check for missing tracking
				$this->checkForMissingTracking($request->input('tracking_main'));
				
				// Update Warehouse pallet
				if ($old_pallet !== $request->input('pallet_number') || $old_tracking !== $request->input('tracking_main')) {
					$message = $this->updateWarehousePallet($old_tracking, $request->input('tracking_main'), $old_pallet, $request->input('pallet_number'), $old_lot, $courier_eng_draft_worksheet->lot, 'en', $courier_eng_draft_worksheet);
					if ($message) {
						return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
					}				
				}

				// Update Warehouse lot
				if ($old_lot !== $courier_eng_draft_worksheet->lot){
					$this->updateWarehouseLot($request->input('tracking_main'), $courier_eng_draft_worksheet->lot, 'en');	
				}

				// Activate PDF
				if (!$old_tracking && $courier_eng_draft_worksheet->getLastDocUniq()) {
					return redirect('/admin/courier-eng-draft-activate/'.$courier_eng_draft_worksheet->id);
				}
			}
		}	
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Row updated successfully!'.' '.$check_result);
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');
		$this->removeTrackingFromPalletWorksheet($id, 'en',true);
		/*$this->deleteUploadFiles('eng_draft_id',$id);*/
		$worksheet = CourierEngDraftWorksheet::find($id);
		$this->deletedToUpdatesArchive($worksheet);

		CourierEngDraftWorksheet::where('id', $id)->delete();
		PackingEng::where('work_sheet_id', $id)->delete();
		ReceiptArchive::where('worksheet_id', $id)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Row deleted successfully!');
	}


    public function addCourierEngDraftDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('phil-ind-tracking-columns');
    	$shipper_country_val = $request->input('shipper_country_val');
    	$consignee_country_val = $request->input('consignee_country_val');
    	$user = Auth::user();
    	$operator_change_row_arr = [];
    	$old_lot_arr = [];
    	$old_pallet_arr = [];
    	$this_column = 'id';
    	$status_error = '';
    	$check_result = '';

    	for ($i=0; $i < count($row_arr); $i++) { 
    		$courier_eng_draft_worksheet = CourierEngDraftWorksheet::find($row_arr[$i]);
    		if (!$courier_eng_draft_worksheet->operator) {
    			$operator_change_row_arr[] = $row_arr[$i];
    		}
    	}

    	if ($row_arr) {

    		for ($i=0; $i < count($row_arr); $i++) { 
    			$message_pdf = $this->checkPdfId('eng_draft_id',$row_arr[$i]);
    			if ($message_pdf !== 'success') {
    				return redirect()->to(session('this_previous_url'))->with('status-error', $message_pdf);
    			}
    		} 

    		if ($column === 'shipper_country') $value_by = $shipper_country_val;
    		if ($column === 'consignee_country') $value_by = $consignee_country_val;
    		
    		if ($value_by && $column) { 

    			$status_error = $this->checkColumns($row_arr, $value_by, $column, $this_column, 'courier_eng_draft_worksheet');
				if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);   			

    			if ($column === 'lot') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = CourierEngDraftWorksheet::where('id',$row_arr[$i])->first();
    					$old_lot_arr[] = $worksheet->lot;
    				}
    			}

    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = CourierEngDraftWorksheet::where('id',$row_arr[$i])->first();
    					$old_pallet_arr[] = $worksheet->pallet_number;
    				}
    			}   	

    			if ($column === 'tracking_main') {   				    				
    				for ($i=0; $i < count($row_arr); $i++) { 

    					$error_message = $this->checkTracking("courier_eng_draft_worksheet", $value_by, $row_arr[$i]);
    					if($error_message) return redirect()->to(session('this_previous_url'))->with('status-error', $error_message);
    					
    					$worksheet = CourierEngDraftWorksheet::where('id',$row_arr[$i])->first();
    					$old_tracking = $worksheet->tracking_main;
    					$pallet = $worksheet->pallet_number;
    					$lot = $worksheet->lot;

    					if ($old_tracking && $old_tracking !== $value_by) {
    						ReceiptArchive::where('tracking_main', $old_tracking)->delete();
    						Receipt::where('tracking_main', $old_tracking)->update(
    							['tracking_main' => $value_by]
    						);
    					}
    					$notification = ReceiptArchive::where('tracking_main', $value_by)->first();
    					if (!$notification) $check_result .= $this->checkReceipt($worksheet->id, null, 'en', $value_by,null,$old_tracking); 

    					$check_result .= $this->updateStatusByTracking('courier_eng_draft_worksheet', $worksheet);		
    				}

    				PackingEng::where('work_sheet_id',$worksheet->id)->update([
    					'tracking' => $value_by
    				]);

    				// Check for missing tracking
    				$this->checkForMissingTracking($value_by);
    				// Update Warehouse pallet
    				$message = $this->updateWarehousePallet($old_tracking, $value_by, $pallet, $pallet, $lot, $lot, 'en', $worksheet);
    				if ($message) {
    					return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
    				}					
    			}

    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = CourierEngDraftWorksheet::where('id',$row_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}		
    			
    			if ($column !== 'operator') {
    				CourierEngDraftWorksheet::whereIn('id', $row_arr)
    				->update([
    					$column => $value_by
    				]);
    			}
    			elseif($column === 'operator' && ($user->role === 'admin' || $user->role === 'office_1')){
    				CourierEngDraftWorksheet::whereIn('id', $row_arr)
    				->update([
    					'operator' => $value_by
    				]);
    			} 

    			if ($column === 'tracking_main') {    				
    				// Activate PDF
    				if (!$old_tracking && $worksheet->getLastDocUniq()) {
    					return redirect('/admin/courier-eng-draft-activate/'.$worksheet->id);
    				}
    			}
    			
    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = CourierEngDraftWorksheet::where('id',$row_arr[$i])->first();
    					if ($old_pallet_arr[$i] !== $value_by){
    						$message = $this->updateWarehousePallet($worksheet->tracking_main, $worksheet->tracking_main, $old_pallet_arr[$i], $value_by, $worksheet->lot, $worksheet->lot, 'en', $worksheet);
    						if ($message) {
    							return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
    						}
    					}
    				}
    			}

    			if ($column === 'lot') {
    				CourierEngDraftWorksheet::whereIn('id', $row_arr)
    				->whereIn('status',$this->status_arr_2)
    				->update([
    					'status' => "Forwarding to the receiver country",
    					'status_ru' => "Доставляется в страну получателя",
    					'status_he' => " נשלח למדינת המקבל",
    					'status_date' => date('Y-m-d')
    				]);

    				for ($i=0; $i < count($row_arr); $i++) { 
    					if ($old_lot_arr[$i] !== $value_by){
    						$worksheet = CourierEngDraftWorksheet::where('id',$row_arr[$i])->first();
    						$this->updateWarehouseLot($worksheet->tracking_main, $value_by, 'en');
    					}
    				}
    			}

    			if ($column === 'shipper_country') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = CourierEngDraftWorksheet::where('id',$row_arr[$i])->first();
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

    			if ($column === 'consignee_country') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = CourierEngDraftWorksheet::where('id',$row_arr[$i])->first();
    					if (!$worksheet->direction) {
    						$worksheet->direction = '-'.$this->to_country_dir[$value_by];
    						$worksheet->save();
    					}
    					else{
    						$temp = explode('-', $worksheet->direction);
    						if (count($temp) == 2) {
    							$worksheet->direction = $temp[0].'-'.$this->to_country_dir[$value_by];
    						}
    						else{
    							$worksheet->direction = '-'.$this->to_country_dir[$value_by];
    						} 
    						$worksheet->save();  						
    					}

    					// New parcel form
    					if (!$worksheet->consignee_address){
    						$worksheet->consignee_address = $value_by;
    						$worksheet->save();
    					}
    				}
    			}

    			// Update Old Packing Eng
    			$params_arr = ['shipper_name','shipper_address','shipper_id','consignee_name','country','consignee_address','consignee_phone','consignee_id','length','width','height','weight','shipment_val'];
    			if ($column === 'shipped_items') {
    				PackingEng::whereIn('work_sheet_id', $row_arr)
    				->update([
    					'items' => $value_by
    				]);
    			}   			
    			if ($column === 'standard_phone') {
    				PackingEng::whereIn('work_sheet_id', $row_arr)
    				->update([
    					'shipper_phone' => $value_by
    				]);
    			}
    			// New parcel form
    			if ($column === 'consignee_country') {
    				PackingEng::where('consignee_address', null)
    				->whereIn('work_sheet_id', $row_arr)
    				->update([
    					'consignee_address' => $value_by,
    					'country' => $value_by
    				]);
    				PackingEng::where('consignee_address','<>',null)
    				->whereIn('work_sheet_id', $row_arr)
    				->update([
    					'country' => $value_by
    				]);
    			}
    			if ($column === 'consignee_address') {
    				PackingEng::whereIn('work_sheet_id', $row_arr)
    				->update([
    					$column => $value_by
    				]);
    			}
    			if (in_array($column, $params_arr)) {
    				PackingEng::whereIn('work_sheet_id', $row_arr)
    				->update([
    					$column => $value_by
    				]);
    			}
    			// End Update Old Packing Eng     	

    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = CourierEngDraftWorksheet::find($row_arr[$i]);
    				$worksheet->checkCourierTask($worksheet->status);
    			}
    		
    		}
    		else if ($request->input('status_date')) {
    			CourierEngDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'status_date' => $request->input('status_date')
    			]);       	
    		}
    		else if ($request->input('order_date')) {
    			CourierEngDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'order_date' => $request->input('order_date')
    			]);       	
    		}
    		else if ($request->input('date')) {
    			CourierEngDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'date' => $request->input('date')
    			]);       	
    		}
    		else if ($request->input('status')){
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$status_error = $this->checkStatus('courier_eng_draft_worksheet', $row_arr[$i], $request->input('status'));
    				if (!$status_error) {
    					$worksheet = CourierEngDraftWorksheet::where('id',$row_arr[$i])->first();
    					$this->toUpdatesArchive($request,$worksheet);
    					
    					CourierEngDraftWorksheet::where('id', $row_arr[$i])
    					->update([
    						'status' => $request->input('status'), 
    						'status_ru' => $request->input('status_ru'),
    						'status_he' => $request->input('status_he'),
    						'status_date' => date('Y-m-d')
    					]);
    				}
    				
    				$worksheet = CourierEngDraftWorksheet::find($row_arr[$i]);
    				$worksheet->checkCourierTask($worksheet->status);
    			} 
    		}
    		else if ($request->input('shipper_city')) {
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = CourierEngDraftWorksheet::where('id',$row_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			} 
    			CourierEngDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'shipper_city' => $request->input('shipper_city')
    			]);  

    			if (in_array($request->input('shipper_city'), array_keys($this->israel_cities))) {
    				CourierEngDraftWorksheet::whereIn('id', $row_arr)
    				->update([
    					'shipper_region' => $this->israel_cities[$request->input('shipper_city')]
    				]);
    			}   			
    		}
    		else if ($request->input('courier')) {
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = CourierEngDraftWorksheet::where('id',$row_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}
    			
    			CourierEngDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'courier' => $request->input('courier')
    			]);  
    		}
    		else if ($user->role === 'admin' && !$value_by && $column === 'tracking_main') {
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = CourierEngDraftWorksheet::find($row_arr[$i]);
    				$old_tracking = $worksheet->tracking_main;
    				$this->removeTrackingFromPalletWorksheet($row_arr[$i], 'en',true);
    				$this->toUpdatesArchive($request,$worksheet);
    				ReceiptArchive::where('tracking_main', $old_tracking)->delete();
    				Receipt::where('tracking_main', $old_tracking)->update(
    					['tracking_main' => null]
    				);
    				$this->setTrackingToDocument($worksheet,$value_by);
    				PackingEng::where('work_sheet_id',$worksheet->id)->update([
    					'tracking' => null
    				]);
    				$worksheet->status = 'Pending';
    				$worksheet->status_ru = null;
    				$worksheet->status_he = null;
    				$worksheet->status_date = date('Y-m-d');
    				$worksheet->tracking_main = null;    				
    				$worksheet->save();
    			}    			
    		}
    		else $status_error = 'New fields error!';

    		for ($i=0; $i < count($row_arr); $i++) { 
    			$worksheet = CourierEngDraftWorksheet::find($row_arr[$i]);
    			$worksheet->checkCourierTask($worksheet->status);
    		}
    		
    	}
        
        if($status_error){
        	return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
        }
        else{
        	return redirect()->to(session('this_previous_url'))->with('status', 'Rows updated successfully!'.' '.$check_result);
        }
    }


    public function deleteCourierEngDraftWorksheetById(Request $request)
	{
		$row_arr = $request->input('row_id');
		for ($i=0; $i < count($row_arr); $i++) { 
			$this->removeTrackingFromPalletWorksheet($row_arr[$i], 'en',true);
			/*$this->deleteUploadFiles('eng_draft_id',$row_arr[$i]);*/
			$worksheet = CourierEngDraftWorksheet::find($row_arr[$i]);
			$this->deletedToUpdatesArchive($worksheet);
		}

		CourierEngDraftWorksheet::whereIn('id', $row_arr)->delete();
		PackingEng::whereIn('work_sheet_id', $row_arr)->delete();
		ReceiptArchive::whereIn('worksheet_id', $row_arr)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Rows deleted successfully!');
	}


	public function courierEngDraftWorksheetFilter(Request $request){
        $title = 'Draft Filter';
        $search = $request->table_filter_value;
        $courier_eng_draft_worksheet_arr = [];
        $attributes = CourierEngDraftWorksheet::first()->attributesToArray();

        $arr_columns = parent::new_columns();

        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR; 
        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	if ($request->input('for_active')) {
        		$courier_eng_draft_worksheet_obj = CourierEngDraftWorksheet::where('in_trash',false)->where([
        			[$request->table_columns, 'like', '%'.$search.'%'],
        			['tracking_main','<>',null]
        		])
        		->orWhere([
        			[$request->table_columns, 'like', '%'.$search.'%'],
        			['status','Pick up']
        		])->paginate(10);
        	}
        	else{
        		$courier_eng_draft_worksheet_obj = CourierEngDraftWorksheet::where('in_trash',false)->where($request->table_columns, 'like', '%'.$search.'%')
        		->paginate(10);
        	}
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			if ($request->input('for_active')) {
        				$sheet = CourierEngDraftWorksheet::where('in_trash',false)->where([
        					[$key, 'like', '%'.$search.'%'],
        					['tracking_main','<>',null]
        				])
        				->orWhere([
        					[$key, 'like', '%'.$search.'%'],
        					['status','Pick up']
        				])->first();
        			}
        			else{
        				$sheet = CourierEngDraftWorksheet::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->first();
        			} 

        			if ($sheet) {  
        				if ($request->input('for_active')) {
        					$temp_arr = CourierEngDraftWorksheet::where('in_trash',false)->where([
        						[$key, 'like', '%'.$search.'%'],
        						['tracking_main','<>',null]
        					])
        					->orWhere([
        						[$key, 'like', '%'.$search.'%'],
        						['status','Pick up']
        					])->get();
        				}      				
        				else{
        					$temp_arr = CourierEngDraftWorksheet::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get();
        				}     				

        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$courier_eng_draft_worksheet_arr[] = $new_arr;   				         		
        			}
        		}         		
        	}

        	return view('admin.courier_draft.courier_eng_draft_worksheet_find', ['title' => $title,'courier_eng_draft_worksheet_arr' => $courier_eng_draft_worksheet_arr, 'user' => $user, 'viewer_arr' => $viewer_arr]);      	
        }
        
        $data = $request->all();             
        
        return view('admin.courier_draft.courier_eng_draft_worksheet', ['title' => $title,'data' => $data,'courier_eng_draft_worksheet_obj' => $courier_eng_draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


    public function courierEngDraftWorksheetDouble(Request $request,$id)
    {
    	$duplicate_qty = $request->duplicate_qty;
    	$worksheet = CourierEngDraftWorksheet::find($id);
    	if (!$worksheet->getLastDocUniq())
    		return redirect()->to(session('this_previous_url'))->with('status-error', 'You can not duplicate without PDF!');
    	$old_delete = false;
    	$worksheet_data = [
    		'standard_phone' => $worksheet->standard_phone,
    		'shipper_name' => $worksheet->shipper_name,
    		'shipper_country' => $worksheet->shipper_country,
    		'shipper_city' => $worksheet->shipper_city,
    		'passport_number' => $worksheet->passport_number,
    		'shipper_address' => $worksheet->shipper_address,
    		'shipper_phone' => $worksheet->shipper_phone,
    		'shipper_id' => $worksheet->shipper_id,
    		'consignee_name' => $worksheet->consignee_name,
    		'consignee_country' => $worksheet->consignee_country,
    		'house_name' => $worksheet->house_name,
    		'post_office' => $worksheet->post_office,
    		'district' => $worksheet->district,
    		'state_pincode' => $worksheet->state_pincode,
    		'consignee_address' => $worksheet->consignee_address,
    		'consignee_phone' => $worksheet->consignee_phone,
    		'consignee_id' => $worksheet->consignee_id,
    		'order_date' => $worksheet->order_date,
    		'direction' => $worksheet->direction
    	]; 
    	
    	for ($j=0; $j < (int)$duplicate_qty; $j++) { 
    		
    		$object = (object)[];   	   	
    		$all_worksheet = CourierEngDraftWorksheet::where([
    			['in_trash',false],
    			['standard_phone',$worksheet->standard_phone]
    		])->get();   		

    		if (!$worksheet->getLastDocUniq() && !$old_delete)
    			$old_delete = $this->deleteOldWorksheet($id,'eng');

    		$qty = $all_worksheet->count();    	  		   		
    		CourierEngDraftWorksheet::create($worksheet_data);
    		$new_id = DB::getPdo()->lastInsertId();
    		CourierEngDraftWorksheet::find($new_id)
    		->update([
    			'date' => date('Y-m-d'),
    			'order_date' => $worksheet->order_date,
    			'status' => 'Double',
    			'shipped_items' => 'Empty: 0',
    			'status_date' => date('Y-m-d')
    		]);

    		$id_arr = CourierEngDraftWorksheet::where([
    			['in_trash',false],
    			['standard_phone',$worksheet->standard_phone]
    		])->pluck('id');
    		if (!$old_delete) $qty++;
    		$last_num = 1;
    		for ($i=0; $i < count($id_arr); $i++) { 
    			$worksheet_data['parcels_qty'] = $last_num.' of '.$qty;
    			CourierEngDraftWorksheet::where([
    				['id',$id_arr[$i]],
    				['in_trash',false],
    				['standard_phone',$worksheet->standard_phone]
    			])->update($worksheet_data);
    			$last_num++;
    		} 

    		$new_worksheet = CourierEngDraftWorksheet::find($new_id);
    		$new_worksheet->checkCourierTask($new_worksheet->status);

    		$packing = PackingEng::where('work_sheet_id',$id)->get();
    		if ($packing) {
    			$packing->each(function ($item, $key) use($new_id){                                 
    				$new_packing = $item->replicate();
    				$new_packing->work_sheet_id = $new_id;
    				$new_packing->tracking = null;
    				$new_packing->save();
    			});
    		}
    		$this->toUpdatesArchive($object,$new_worksheet,true,$new_id);
    	}   	   		
    	
    	return redirect()->to(session('this_previous_url'))->with('status', 'Row duplicated successfully!');
    }


    public function adminActivate(Request $request)
    {
    	$row_arr = $request->row_id;
    	$message = '';
    	for ($i=0; $i < count($row_arr); $i++) { 
    		$worksheet = CourierEngDraftWorksheet::find($row_arr[$i]);
    		$message_pdf = $this->checkPdfId('eng_draft_id',$row_arr[$i]);
    		if ($message_pdf !== 'success') 
    			return redirect()->to(session('this_previous_url'))->with('status-error', $message_pdf);
    		if ($worksheet->tracking_main)
    			$message .= $this->courierEngDraftActivate($row_arr[$i], true);
    		else
    			return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking number is required!');
    	}
    	return redirect()->to(session('this_previous_url'))->with('status', $message);
    }


    public function courierEngDraftCheckActivate($id){
    	$courier_eng_draft_worksheet = CourierEngDraftWorksheet::find($id);
    	$tracking = $courier_eng_draft_worksheet->tracking_main;
		$country = '';
		$error_message = 'Fill in required fields: ';
		$user = Auth::user();

		$message_pdf = $this->checkPdfId('eng_draft_id',$id);
		if ($message_pdf !== 'success') {
			return response()->json(['error' => $message_pdf]);
		}

		$country = $courier_eng_draft_worksheet->consignee_country;	

		if ($country && $country === 'India') {
			if (!$courier_eng_draft_worksheet->shipper_name) $error_message .= 'Shipper\'s name,';
			if (!$courier_eng_draft_worksheet->shipper_address) $error_message .= 'Shipper\'s address,';
			if (!$courier_eng_draft_worksheet->standard_phone) $error_message .= 'Shipper\'s phone (standard),';
			if (!$courier_eng_draft_worksheet->consignee_name) $error_message .= 'Consignee\'s name,';
			if (!$courier_eng_draft_worksheet->consignee_address) $error_message .= 'Consignee\'s address,';
			if (!$courier_eng_draft_worksheet->consignee_phone) $error_message .= 'Consignee\'s phone number,';
			if (!$courier_eng_draft_worksheet->house_name) $error_message .= 'House name,';
			if (!$courier_eng_draft_worksheet->state_pincode) $error_message .= 'State pincode,';
			if (!$courier_eng_draft_worksheet->post_office) $error_message .= 'Local post office,';
			if (!$courier_eng_draft_worksheet->district) $error_message .= 'District/City,';

			if ($error_message !== 'Fill in required fields: ') {
				return response()->json(['error' => $error_message]);
			}			
		}
		elseif ($country && $country === 'Nepal') {
			if (!$courier_eng_draft_worksheet->shipper_name) $error_message .= 'Shipper\'s name,';
			if (!$courier_eng_draft_worksheet->shipper_address) $error_message .= 'Shipper\'s address,';
			if (!$courier_eng_draft_worksheet->standard_phone) $error_message .= 'Shipper\'s phone (standard),';
			if (!$courier_eng_draft_worksheet->consignee_name) $error_message .= 'Consignee\'s name,';
			if (!$courier_eng_draft_worksheet->consignee_address) $error_message .= 'Consignee\'s address,';
			if (!$courier_eng_draft_worksheet->consignee_phone) $error_message .= 'Consignee\'s phone number,';

			if ($error_message !== 'Fill in required fields: ') {
				return response()->json(['error' => $error_message]);
			}
		}
		elseif ($country) {
			if (!$courier_eng_draft_worksheet->shipper_name) $error_message .= 'Shipper\'s name,';
			if (!$courier_eng_draft_worksheet->shipper_city) $error_message .= 'Shipper\'s city,';
			if (!$courier_eng_draft_worksheet->shipper_address) $error_message .= 'Shipper\'s address,';
			if (!$courier_eng_draft_worksheet->standard_phone) $error_message .= 'Shipper\'s phone (standard),';
			if (!$courier_eng_draft_worksheet->consignee_name) $error_message .= 'Consignee\'s name,';
			if (!$courier_eng_draft_worksheet->consignee_address) $error_message .= 'Consignee\'s address,';
			if (!$courier_eng_draft_worksheet->consignee_phone) $error_message .= 'Consignee\'s phone number,';

			if ($error_message !== 'Fill in required fields: ') {
				return response()->json(['error' => $error_message]);
			}
		}
		else{
			$error_message .= 'Consignee\'s country';
			return response()->json(['error' => $error_message]);
		}			
		
		$phone_exist = PhilIndWorksheet::where('in_trash',false)->where('standard_phone',$courier_eng_draft_worksheet->standard_phone)->get()->last();

		if ($phone_exist) {
			if (in_array($phone_exist->status, $this->status_arr)) {
				return response()->json(['phone_exist' => $id]);
			}
			else{
				return response()->json(['phone_exist' => '']);
			}
		}
		else{
			return response()->json(['phone_exist' => '']);
		}
	}


	public function courierEngDraftActivate($id, $admin = false)
	{
		$courier_eng_draft_worksheet = CourierEngDraftWorksheet::find($id);				
		$fields = $this->getTableColumns('courier_eng_draft_worksheet');
		$message = '';	
		$user = Auth::user();

		if ($courier_eng_draft_worksheet->tracking_main) {
			$check_tracking	= PhilIndWorksheet::where('tracking_main', $courier_eng_draft_worksheet->tracking_main)->first();
			if ($check_tracking) return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking number exists!');
		}							

		$worksheet = new PhilIndWorksheet();

		foreach($fields as $field){
			if ($field !== 'created_at' && $field !== 'id') {
				$worksheet->$field = $courier_eng_draft_worksheet->$field;
			}			
		}

		if ($user->role === 'office_1' || $user->role === 'admin') {
			$worksheet->background = 'tr-orange';
		}

		if ($worksheet->save())	{

			if ($worksheet->pallet_number) {
				$this->updateWarehouse(null, $worksheet->pallet_number, $worksheet->tracking_main);
			}

			$work_sheet_id = $worksheet->id;

			if ($worksheet->tracking_main) {
				// Notification of Warehouse
				ReceiptArchive::where([
					['tracking_main', $worksheet->tracking_main],
					['worksheet_id', null],
					['receipt_id', null]
				])->delete();
				$result = Receipt::where('tracking_main', $worksheet->tracking_main)->first();
				if (!$result) {
					$message = $this->checkReceipt($work_sheet_id, null, 'en', $worksheet->tracking_main);
				}

				$this->checkForMissingTracking($worksheet->tracking_main);
				// End Notification of Warehouse
			}			
			
			ReceiptArchive::where('worksheet_id', $id)->update(['worksheet_id' => $work_sheet_id]);

			// New Packing Eng		
			$packing_fields = $this->getTableColumns('packing_eng');			
			$packing = PackingEng::where('work_sheet_id', $id)->get();

			$packing->each(function ($item, $key) use($work_sheet_id, $packing_fields) {
				$new_packing = new PackingEngNew();
				for ($i=0; $i < count($packing_fields); $i++) { 
					if ($packing_fields[$i] !== 'work_sheet_id' && $packing_fields[$i] !== 'id') {
						$new_packing[$packing_fields[$i]] = $item[$packing_fields[$i]];
					}
					else{
						$new_packing->work_sheet_id = $work_sheet_id;
					}					
				}
				$new_packing->save();
			});
			PackingEng::where('work_sheet_id', $id)->delete();

	        // Adding order number
			if ($worksheet->standard_phone) {
				$this->addingOrderNumber($worksheet->standard_phone, 'en');
			}

			// Transfer documents
            SignedDocument::where('eng_draft_id',$courier_eng_draft_worksheet->id)
    		->update([
    			'eng_draft_id' => null,
    			'eng_worksheet_id' => $worksheet->id
    		]);	
			
			CourierEngDraftWorksheet::where('id', $id)->delete();

			$worksheet->checkCourierTask($worksheet->status);
			
			if (!$admin) 
				return redirect()->to(session('this_previous_url'))->with('status', 'Row activated successfully!'.$message);
			else
				return 'Row activated successfully!'.$message;
		}
		else{
			return redirect()->to(session('this_previous_url'))->with('status-error', 'Activate error!'.$message);
		}			
	}


	public function exportExcel()
	{

		return Excel::download(new CourierEngDraftWorksheetExport, 'CourierEngDraftWorksheetExport.xlsx');

	}

}
