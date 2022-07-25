<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\PhilIndWorksheet;
use DB;
use Excel;
use App\PackingEng;
use App\Exports\PhilIndWorksheetExport;
use App\Exports\PackingEngExport;
use App\PackingEngNew;
use App\ReceiptArchive;
use App\Receipt;
use App\Exports\PackingEngNewExport;
use Auth;
use App\SignedDocument;


class PhilIndWorksheetController extends AdminController
{
	private $status_arr = ["Forwarding to the warehouse in the sender country", "Pending", "Return", "Box", "Pick up", "Specify", "Think", "Canceled", "Double","Packing list"];
	private $status_arr_2 = ["At the customs in the sender country", "At the warehouse in the sender country", "Forwarding to the warehouse in the sender country", "Pending", "Return", "Box", "Pick up", "Specify", "Think", "Canceled", "Double","Packing list"];
    

    public function index(){
        $title = 'Work sheet';     

        // Auto-update status
        $update_date = Date('Y-m-d', strtotime('-14 days'));
        PhilIndWorksheet::where('in_trash',false)->where([
        	['status_date','<=',$update_date],
        	['status_date','<>',null],
        	['status',"Forwarding to the warehouse in the sender country"]
        ]) 
        ->orWhere([
        	['status_date','<=',$update_date],
        	['status_date','<>',null],
        	['status',"At the warehouse in the sender country"]
        ])            
        ->update([
        	'status' => "Forwarding to the receiver country",
        	'status_ru' => "Доставляется в страну получателя",
        	'status_he' => " נשלח למדינת המקבל",
        	'status_date' => date('Y-m-d')
        ]); 
        
        $phil_ind_worksheet_obj = PhilIndWorksheet::where('in_trash',false)->paginate(10);
        $arr_columns = parent::new_phil_ind_columns();
        
        return view('admin.phil_ind.phil_ind_worksheet', ['title' => $title,'phil_ind_worksheet_obj' => $phil_ind_worksheet_obj,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
    }


	public function show($id)
	{
		$arr_columns = parent::new_phil_ind_columns();
		$phil_ind_worksheet = PhilIndWorksheet::find($id);
		$title = 'Update row '.$phil_ind_worksheet->id;
		$user = Auth::user();
		$israel_cities = $this->israelCities();
		$to_country = $this->to_country_arr;

		return view('admin.phil_ind.phil_ind_worksheet_update', ['title' => $title,'phil_ind_worksheet' => $phil_ind_worksheet, 'user' => $user,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4],'israel_cities' => $israel_cities,'to_country' => $to_country]);
	}


	private function validateUpdate($request, $id){
		$status_error = '';
		if (!$request->input('status')) return 'ERROR STATUS!';
		
		$status_error = $this->checkStatus('phil_ind_worksheet', $id, $request->input('status'));
		if($status_error) return $status_error;
		
		if ($request->input('consignee_phone')) {
			$status_error = $this->checkConsigneePhone($request->input('consignee_phone'), 'en');
			if($status_error) return $status_error;
		}

		if ($request->input('tracking_main')) {
			$status_error = $this->checkTracking("phil_ind_worksheet", $request->input('tracking_main'), $id);
			if($status_error) return $status_error;
		}
		elseif (!$request->input('tracking_main') && ($request->input('lot') || $request->input('pallet_number'))){
			$status_error = "You cannot enter a lot or pallet number without a tracking number";
			return $status_error;
		}

		if ($request->input('amount_payment')){
			if (in_array($request->input('status'), $this->status_arr)){
				$status_error = "WARNING! A STATUS CANNOT BE - '".$request->input('status')."' AFTER PAYMENT. PLEASE ADD THE DATA TO A CORRECT RECORD OR UPDATE THE STATUS";
				return $status_error;
			}
		}

		if ($request->input('pallet_number')){
			if (in_array($request->input('status'), $this->status_arr)){
				$status_error = "WARNING! A STATUS CANNOT BE - '".$request->input('status')."' AFTER ADDING A PALLET NUMBER. PLEASE ADD THE DATA TO A CORRECT RECORD OR UPDATE THE STATUS";
				return $status_error;
			}
		}
		return $status_error;
	}


	public function update(Request $request, $id)
	{
		$phil_ind_worksheet = PhilIndWorksheet::find($id);
		$this->toUpdatesArchive($request,$phil_ind_worksheet);
		$old_tracking = $phil_ind_worksheet->tracking_main;
		$old_pallet = $phil_ind_worksheet->pallet_number;
		$old_lot = $phil_ind_worksheet->lot;
		$old_status = $phil_ind_worksheet->status;
		$arr_columns = parent::new_phil_ind_columns();
		$fields = $this->getTableColumns('phil_ind_worksheet');
		$operator_change = true;
		$user = Auth::user();
		$status_error = '';
		$status = 'Row updated successfully!';
		$check_result = '';

		for ($i=0; $i < count($arr_columns); $i++) { 
			if ($arr_columns[$i]) {
				$fields[] = 'new_column_'.($i+1);
			}
		}
		
		$status_error = $this->validateUpdate($request, $id);
		if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);	

		if ($phil_ind_worksheet->operator) $operator_change = false;

		foreach($fields as $field){
			if ($field !== 'created_at' && $field !== 'operator'){
				$phil_ind_worksheet->$field = $request->input($field);
			}
			elseif ($field === 'operator'  && ($user->role === 'admin' || $user->role === 'office_1')) {
				$phil_ind_worksheet->$field = $request->input($field);
			}
			elseif ($field === 'operator' && $user->role !== 'admin' && $operator_change) {
				$phil_ind_worksheet->$field = $request->input($field);
			}
		}

		if (in_array($phil_ind_worksheet->shipper_city, array_keys($this->israel_cities))) {
			$phil_ind_worksheet->shipper_region = $this->israel_cities[$phil_ind_worksheet->shipper_city];
		}

		$phil_ind_worksheet->direction = $this->createDirection($request->input('shipper_country'), $request->input('consignee_country'));

		if ($request->input('tracking_main')){
			$check_result .= $this->updateStatusByTracking('new_worksheet', $phil_ind_worksheet);

			if ($old_lot !== $phil_ind_worksheet->lot) {
				if (in_array($old_status, $this->status_arr_2)){
					$phil_ind_worksheet->status = "Forwarding to the receiver country";
					$phil_ind_worksheet->status_ru = "Доставляется в страну получателя";
					$phil_ind_worksheet->status_he = " נשלח למדינת המקבל";
				}
			}
			
			$date_result = (strtotime('2021-09-20') <= strtotime(str_replace('.', '-', $phil_ind_worksheet->date)));
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
					$check_result = $this->checkReceipt($id, null, 'en', $request->input('tracking_main'),null,$old_tracking);
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

		if ($old_status !== $phil_ind_worksheet->status) {
			PhilIndWorksheet::where('id', $id)
			->update([
				'status_date' => date('Y-m-d')
			]);
		}		
		
		if ($phil_ind_worksheet->save()) {

			$phil_ind_worksheet->checkCourierTask($phil_ind_worksheet->status);

			// Update Update New Packing Eng
			$address = explode(' ',$request->input('consignee_address'));
			$has_post = PackingEngNew::where('work_sheet_id', $id)->first();
			if ($has_post) {				
				PackingEngNew::where('work_sheet_id', $id)
				->update([
					'tracking' => $request->input('tracking_main'),
					'country' => $address[0],
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
					'lot' => $request->input('lot'),
					'shipment_val' => $request->input('shipment_val')
				]);
			}
			else{
				$packing = new PackingEngNew();
				$packing->work_sheet_id = $id;
				$packing->tracking = $request->input('tracking_main');
				$packing->country = $address[0];
				$packing->shipper_name = $request->input('shipper_name');
				$packing->shipper_address = $request->input('shipper_address');
				$packing->shipper_phone = $request->input('standard_phone');
				$packing->shipper_id = $request->input('shipper_id');
				$packing->consignee_name = $request->input('consignee_name');
				$packing->consignee_address = $request->input('consignee_address');
				$packing->consignee_phone = $request->input('consignee_phone');
				$packing->consignee_id = $request->input('consignee_id');
				$packing->length = $request->input('length');
				$packing->width = $request->input('width');
				$packing->height = $request->input('height');
				$packing->weight = $request->input('weight');
				$packing->items = $request->input('shipped_items');
				$packing->lot = $request->input('lot');
				$packing->shipment_val = $request->input('shipment_val');
				$packing->save();
			}						
			// End Update New Packing Eng
			
			// Adding order number
			if ($phil_ind_worksheet->standard_phone) {
				$this->addingOrderNumber($phil_ind_worksheet->standard_phone, 'en');
			}

			if ($request->input('tracking_main')) {
				// Check for missing tracking
				$this->checkForMissingTracking($request->input('tracking_main'));
				
				// Update Warehouse pallet
				if ($old_pallet !== $request->input('pallet_number') || $old_tracking !== $request->input('tracking_main')) {
					$message = $this->updateWarehousePallet($old_tracking, $request->input('tracking_main'), $old_pallet, $request->input('pallet_number'), $old_lot, $phil_ind_worksheet->lot, 'en', $phil_ind_worksheet);
					if ($message) {
						return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
					}				
				}

				// Update Warehouse lot
				if ($old_lot !== $phil_ind_worksheet->lot){
					$this->updateWarehouseLot($request->input('tracking_main'), $phil_ind_worksheet->lot, 'en');	
				}
			}
		}	

		if ($status_error) {
			return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
		}	
		else{
			return redirect()->to(session('this_previous_url'))->with('status', $status);
		}		
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');
		$this->removeTrackingFromPalletWorksheet($id, 'en');
		$this->deleteUploadFiles('eng_worksheet_id',$id);
		$worksheet = PhilIndWorksheet::find($id);
		$this->deletedToUpdatesArchive($worksheet);
		
		PhilIndWorksheet::where('id', $id)->delete();
		PackingEngNew::where('work_sheet_id', $id)->delete();
		ReceiptArchive::where('worksheet_id', $id)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Row deleted successfully!');
	}


	public function addColumn()
	{
		$message = 'Column added successfully!';
		
		if (!Schema::hasColumn('phil_ind_worksheet', 'new_column_1'))
		{
			Schema::table('phil_ind_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_1')->nullable();
			});
		}
		else if (!Schema::hasColumn('phil_ind_worksheet', 'new_column_2'))
		{
			Schema::table('phil_ind_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_2')->nullable();
			});
		}
		else if (!Schema::hasColumn('phil_ind_worksheet', 'new_column_3'))
		{
			Schema::table('phil_ind_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_3')->nullable();
			});
		}
		else if (!Schema::hasColumn('phil_ind_worksheet', 'new_column_4'))
		{
			Schema::table('phil_ind_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_4')->nullable();
			});
		}
		else if (!Schema::hasColumn('phil_ind_worksheet', 'new_column_5'))
		{
			Schema::table('phil_ind_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_5')->nullable();
			});
		}
		else
		{
			return redirect()->to(session('this_previous_url'))->with('status-error', 'The quantity of columns is limited!');
		}

		return redirect()->to(session('this_previous_url'))->with('status', $message);
	}


	public function deleteColumn(Request $request)
	{
		$name_column = $request->input('name_column');

		if ($name_column === 'new_column_1') {
			Schema::table('phil_ind_worksheet', function($table)
			{
				$table->dropColumn('new_column_1');
			});
		}
		elseif ($name_column === 'new_column_2') {
			Schema::table('phil_ind_worksheet', function($table)
			{
				$table->dropColumn('new_column_2');
			});
		}
		elseif ($name_column === 'new_column_3') {
			Schema::table('phil_ind_worksheet', function($table)
			{
				$table->dropColumn('new_column_3');
			});
		}
		elseif ($name_column === 'new_column_4') {
			Schema::table('phil_ind_worksheet', function($table)
			{
				$table->dropColumn('new_column_4');
			});
		}
		elseif ($name_column === 'new_column_5') {
			Schema::table('phil_ind_worksheet', function($table)
			{
				$table->dropColumn('new_column_5');
			});
		}

		return redirect()->to(session('this_previous_url'))->with('status', 'Column deleted successfully!');
	}


	public function showPhilIndStatus(){
        $title = 'Changing statuses by lot number';
        $worksheet_obj = PhilIndWorksheet::where('in_trash',false)->get();
        $number_arr = [];
        foreach ($worksheet_obj as $row) {
        	if (!in_array($row->lot, $number_arr)) {
        		$number_arr[$row->lot] = $row->lot;
        	}
        }
        return view('admin.phil_ind.phil_ind_worksheet_status_number', ['title' => $title,'number_arr' => $number_arr]);
    }


    public function changePhilIndStatus(Request $request){
        if ($request->input('lot_number') && $request->input('status')) {
        	
        	$request->by_lot = true;
        	$request->lot = $request->input('lot_number');
        	$worksheet_arr = PhilIndWorksheet::where([
        		['lot', $request->input('lot_number')],
        		['tracking_main','<>',null]
        	])->get();
        	foreach ($worksheet_arr as $worksheet) {
        		$this->toUpdatesArchive($request,$worksheet);
        	}
        	
        	DB::table('phil_ind_worksheet')
        	->where('in_trash',false)
        	->where([
        		['lot', $request->input('lot_number')],
        		['tracking_main','<>',null]
        	])
          	->update([
          		'status' => $request->input('status'), 
          		'status_he' => $request->input('status_he'),
          		'status_ru' => $request->input('status_ru'),
          		'status_date' => date('Y-m-d')
          	]);
        }
        return redirect()->to(session('this_previous_url'));
    }


    public function showPhilIndStatusDate(){
        $title = 'Changing statuses by date';
        $worksheet_obj = PhilIndWorksheet::where('in_trash',false)->get();
        $date_arr = [];
        foreach ($worksheet_obj as $row) {
        	if (!in_array($row->date, $date_arr)) {
        		$date_arr[$row->date] = $row->date;
        	}
        }
        return view('admin.phil_ind.phil_ind_worksheet_status_date', ['title' => $title,'date_arr' => $date_arr]);
    }


    public function changePhilIndStatusDate(Request $request){
        if ($request->input('date') && $request->input('status')) {
        	
        	$request->by_lot = true;
        	$worksheet_arr = PhilIndWorksheet::where([
        		['date', $request->input('date')],
        		['tracking_main','<>',null]
        	])->get();
        	foreach ($worksheet_arr as $worksheet) {
        		$this->toUpdatesArchive($request,$worksheet);
        	}
        	
        	DB::table('phil_ind_worksheet')
        	->where('in_trash',false)
        	->where([
        		['date', $request->input('date')],
        		['tracking_main','<>',null]
        	])
          	->update([
          		'status' => $request->input('status'), 
          		'status_he' => $request->input('status_he'),
          		'status_ru' => $request->input('status_ru'),
          		'status_date' => date('Y-m-d')
          	]);
        }
        return redirect()->to(session('this_previous_url'));
    }


    public function showPhilIndData(){
        $title = 'Mass change of data by tracking number (supports mass selection of checkboxes)';
        $worksheet_obj = PhilIndWorksheet::where('in_trash',false)->orderBy('tracking_main')->get();
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
        return view('admin.phil_ind.phil_ind_worksheet_tracking_data', ['title' => $title,'date_arr' => $date_arr]);
    }


    public function addPhilIndData(Request $request){    	
    	$track_arr = $request->input('tracking');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('phil-ind-tracking-columns');
    	$shipper_country_val = $request->input('shipper_country_val');
    	$consignee_country_val = $request->input('consignee_country_val');
    	$check_column = 'tracking';
    	$this_column = 'tracking_main';
    	$status_error = '';
    	$user = Auth::user();
    	$operator_change_row_arr = [];
    	$old_lot_arr = [];
    	$old_pallet_arr = [];

    	for ($i=0; $i < count($track_arr); $i++) { 
    		$phil_ind_worksheet = PhilIndWorksheet::where('tracking_main',$track_arr[$i])->first();
    		if (!$phil_ind_worksheet->operator) {
    			$operator_change_row_arr[] = $track_arr[$i];
    		}
    	}

    	if ($track_arr) {

    		if ($column === 'shipper_country') $value_by = $shipper_country_val;
    		if ($column === 'consignee_country') $value_by = $consignee_country_val;
    		
    		if ($value_by && $column) {

    			$status_error = $this->checkColumns($track_arr, $value_by, $column, $this_column, 'phil_ind_worksheet');
    			if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);

    			for ($i=0; $i < count($track_arr); $i++) { 
    				$worksheet = PhilIndWorksheet::where('tracking_main',$track_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}

    			if ($column === 'lot') {
    				for ($i=0; $i < count($track_arr); $i++) { 
    					$worksheet = PhilIndWorksheet::where('tracking_main',$track_arr[$i])->first();
    					$old_lot_arr[] = $worksheet->lot;
    				}
    			}

    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($track_arr); $i++) { 
    					$worksheet = PhilIndWorksheet::where('tracking_main',$track_arr[$i])->first();
    					$old_pallet_arr[] = $worksheet->pallet_number;
    				}
    			}
    			
    			if ($column !== 'operator') {
    				PhilIndWorksheet::whereIn('tracking_main', $track_arr)
    				->update([
    					$column => $value_by
    				]);
    			}
    			elseif($column === 'operator' && ($user->role === 'admin' || $user->role === 'office_1')){
    				PhilIndWorksheet::whereIn('tracking_main', $track_arr)
    				->update([
    					'operator' => $value_by
    				]);
    			}

    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($track_arr); $i++) { 
    					$worksheet = PhilIndWorksheet::where('tracking_main',$track_arr[$i])->first();
    					if ($old_pallet_arr[$i] !== $value_by){
    						$message = $this->updateWarehousePallet($track_arr[$i], $track_arr[$i], $old_pallet_arr[$i], $value_by, $worksheet->lot, $worksheet->lot, 'en', $worksheet);
    						if ($message) {
    							return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
    						}
    					}
    				}
    			}

    			if ($column === 'lot') {
    				PhilIndWorksheet::whereIn('tracking_main', $track_arr)
    				->whereIn('status',$this->status_arr_2)
    				->update([
    					'status' => "Forwarding to the receiver country",
    					'status_ru' => "Доставляется в страну получателя",
    					'status_he' => " נשלח למדינת המקבל",
    					'status_date' => date('Y-m-d')
    				]);

    				for ($i=0; $i < count($track_arr); $i++) { 
    					if ($old_lot_arr[$i] !== $value_by){
    						$this->updateWarehouseLot($track_arr[$i], $value_by, 'en');
    					}
    				}
    			}

    			if ($column === 'shipper_country') {
    				for ($i=0; $i < count($track_arr); $i++) { 
    					$worksheet = PhilIndWorksheet::where('tracking_main',$track_arr[$i])->first();
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
    				for ($i=0; $i < count($track_arr); $i++) { 
    					$worksheet = PhilIndWorksheet::where('tracking_main',$track_arr[$i])->first();
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
    				}
    			}

    			$this->updateNewPacking($track_arr, $value_by, $column, $check_column);     		
    		}
    		else if ($request->input('status')){
    			for ($i=0; $i < count($track_arr); $i++) { 
    				$worksheet = PhilIndWorksheet::where('tracking_main',$track_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}
    			
    			PhilIndWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'status' => $request->input('status'), 
    				'status_ru' => $request->input('status_ru'),
    				'status_he' => $request->input('status_he'),
    				'status_date' => date('Y-m-d')
    			]);
    		}
    		else if ($request->input('shipper_city')) {
    			for ($i=0; $i < count($track_arr); $i++) { 
    				$worksheet = PhilIndWorksheet::where('tracking_main',$track_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}
    			
    			PhilIndWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'shipper_city' => $request->input('shipper_city')
    			]);  

    			if (in_array($request->input('shipper_city'), array_keys($this->israel_cities))) {
    				PhilIndWorksheet::whereIn('tracking_main', $track_arr)
    				->update([
    					'shipper_region' => $this->israel_cities[$request->input('shipper_city')]
    				]);
    			}
    		}    		
    	}

    	if($status_error){
        	return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
        }
        else{
        	return redirect()->to(session('this_previous_url'))->with('status', 'Rows updated successfully!');
        }
    }


    public function exportExcel()
	{

    	return Excel::download(new PhilIndWorksheetExport, 'PhilIndWorksheetExport.xlsx');

	}


	public function indexPackingEng(){
        $title = 'Old Packing List';
        $packing_eng_obj = PackingEng::where('in_trash',false)->get();       
        
        return view('admin.packing.packing_eng', ['title' => $title,'packing_eng_obj' => $packing_eng_obj]);
    }


    public function indexPackingEngNew(){
        $title = 'New Packing List';
        $packing_eng_new_obj = PackingEngNew::where('in_trash',false)->paginate(10);       
        
        return view('admin.packing.packing_eng_new', compact('title','packing_eng_new_obj'));
    }


    public function exportExcelPackingEngNew()
	{

    	return Excel::download(new PackingEngNewExport, 'PackingEngNewExport.xlsx');

	}


    public function packingEngNewFilter(Request $request)
	{
        $title = 'New Packing Filter';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = PackingEngNew::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$packing_eng_new_obj = PackingEngNew::where('in_trash',false)->where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = PackingEngNew::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = PackingEngNew::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get();
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

        	return view('admin.packing.packing_eng_new_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.packing.packing_eng_new', compact('title','packing_eng_new_obj','data'));
    }


    private function updateNewPacking($arr, $value_by, $column, $check_column){
    	$params_arr = ['shipper_name','shipper_address','shipper_id','consignee_name','consignee_address','consignee_phone','consignee_id','length','width','height','weight','shipment_val','lot'];
    	if ($column === 'shipped_items') {
    		PackingEngNew::whereIn($check_column, $arr)
    		->update([
    			'items' => $value_by
    		]);
    	}
    	if ($column === 'standard_phone') {
    		PackingEngNew::whereIn($check_column, $arr)
    		->update([
    			'shipper_phone' => $value_by
    		]);
    	}
    	if ($column === 'consignee_address') {
    		$address = explode(' ',$value_by);
    		PackingEngNew::whereIn($check_column, $arr)
    		->update([
    			'country' => $address[0],
    			$column => $value_by
    		]);
    	}
    	if (in_array($column, $params_arr)) {
    		PackingEngNew::whereIn($check_column, $arr)
    		->update([
    			$column => $value_by
    		]);
    	}
    	return true;
    }


	public function addPhilIndDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('phil-ind-tracking-columns');
    	$shipper_country_val = $request->input('shipper_country_val');
    	$consignee_country_val = $request->input('consignee_country_val');
    	$user = Auth::user();
    	$operator_change_row_arr = [];
    	$status_error = '';
    	$check_column = 'work_sheet_id';
    	$this_column = 'id';
    	$old_lot_arr = [];
    	$old_pallet_arr = [];
    	$color = $request->input('tr_color');
    	$check_result = '';

    	for ($i=0; $i < count($row_arr); $i++) { 
    		$phil_ind_worksheet = PhilIndWorksheet::find($row_arr[$i]);
    		if (!$phil_ind_worksheet->operator) {
    			$operator_change_row_arr[] = $row_arr[$i];
    		}
    	}
		
    	if ($row_arr) {
    		
    		if ($column === 'shipper_country') $value_by = $shipper_country_val;
    		if ($column === 'consignee_country') $value_by = $consignee_country_val;
    		
    		if ($color) {
    			if ($color !== 'transparent') {
    				PhilIndWorksheet::whereIn('id', $row_arr)
    				->update([
    					'background' => $color
    				]);
    			}
    			else{
    				PhilIndWorksheet::whereIn('id', $row_arr)
    				->update([
    					'background' => null
    				]);
    			}
    		}
    		elseif ($value_by && $column) {

				$status_error = $this->checkColumns($row_arr, $value_by, $column, $this_column, 'phil_ind_worksheet');
				if($status_error) return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);

				for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = PhilIndWorksheet::where('id',$row_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}

				if ($column === 'lot') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = PhilIndWorksheet::where('id',$row_arr[$i])->first();
    					$old_lot_arr[] = $worksheet->lot;
    				}
    			}

    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = PhilIndWorksheet::where('id',$row_arr[$i])->first();
    					$old_pallet_arr[] = $worksheet->pallet_number;
    				}
    			}

    			if ($column === 'tracking_main') {   				    				
    				for ($i=0; $i < count($row_arr); $i++) { 

    					$error_message = $this->checkTracking("phil_ind_worksheet", $value_by, $row_arr[$i]);
    					if($error_message) return redirect()->to(session('this_previous_url'))->with('status-error', $error_message);
    					
    					$worksheet = PhilIndWorksheet::where('id',$row_arr[$i])->first();
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

    					$check_result .= $this->updateStatusByTracking('phil_ind_worksheet', $worksheet);		
    				}

    				// Check for missing tracking
    				$this->checkForMissingTracking($value_by);
    				// Update Warehouse pallet
    				$message = $this->updateWarehousePallet($old_tracking, $value_by, $pallet, $pallet, $lot, $lot, 'en', $worksheet);
    				if ($message) {
    					return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
    				}					
    			}	
    			
    			if ($column !== 'operator') {
    				PhilIndWorksheet::whereIn('id', $row_arr)
    				->update([
    					$column => $value_by
    				]);
    			}
    			elseif($column === 'operator' && ($user->role === 'admin' || $user->role === 'office_1')){
    				PhilIndWorksheet::whereIn('id', $row_arr)
    				->update([
    					'operator' => $value_by
    				]);
    			}

    			if ($column === 'pallet_number') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = PhilIndWorksheet::where('id',$row_arr[$i])->first();
    					if ($old_pallet_arr[$i] !== $value_by){
    						$message = $this->updateWarehousePallet($worksheet->tracking_main, $worksheet->tracking_main, $old_pallet_arr[$i], $value_by, $worksheet->lot, $worksheet->lot, 'en', $worksheet);
    						if ($message) {
    							return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is not correct!');
    						}
    					}
    				}
    			}

    			if ($column === 'lot') {
    				PhilIndWorksheet::whereIn('id', $row_arr)
    				->whereIn('status',$this->status_arr_2)
    				->update([
    					'status' => "Forwarding to the receiver country",
    					'status_ru' => "Доставляется в страну получателя",
    					'status_he' => " נשלח למדינת המקבל",
    					'status_date' => date('Y-m-d')
    				]);

    				for ($i=0; $i < count($row_arr); $i++) { 
    					if ($old_lot_arr[$i] !== $value_by){
    						$worksheet = PhilIndWorksheet::where('id',$row_arr[$i])->first();
    						$this->updateWarehouseLot($worksheet->tracking_main, $value_by, 'en');
    					}
    				}
    			}

    			if ($column === 'shipper_country') {
    				for ($i=0; $i < count($row_arr); $i++) { 
    					$worksheet = PhilIndWorksheet::where('id',$row_arr[$i])->first();
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
    					$worksheet = PhilIndWorksheet::where('id',$row_arr[$i])->first();
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
    				}
    			}

    			$this->updateNewPacking($row_arr, $value_by, $column, $check_column);   
    			
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = PhilIndWorksheet::find($row_arr[$i]);
    				$worksheet->checkCourierTask($worksheet->status);
    			}	  	    		
    		}
    		else if ($request->input('status')){
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$status_error = $this->checkStatus('phil_ind_worksheet', $row_arr[$i], $request->input('status'));
    				if (!$status_error) {
    					
    					$worksheet = PhilIndWorksheet::where('id',$row_arr[$i])->first();
    					$this->toUpdatesArchive($request,$worksheet);
    					
    					PhilIndWorksheet::where('id', $row_arr[$i])
    					->update([
    						'status' => $request->input('status'), 
    						'status_ru' => $request->input('status_ru'),
    						'status_he' => $request->input('status_he'),
    						'status_date' => date('Y-m-d')
    					]);
    				}
    				
    				$worksheet = PhilIndWorksheet::find($row_arr[$i]);
    				$worksheet->checkCourierTask($worksheet->status);
    			} 
    		}
    		else if ($request->input('shipper_city')) {
    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = PhilIndWorksheet::where('id',$row_arr[$i])->first();
    				$this->toUpdatesArchive($request,$worksheet);
    			}
    			
    			PhilIndWorksheet::whereIn('id', $row_arr)
    			->update([
    				'shipper_city' => $request->input('shipper_city')
    			]);  

    			if (in_array($request->input('shipper_city'), array_keys($this->israel_cities))) {
    				PhilIndWorksheet::whereIn('id', $row_arr)
    				->update([
    					'shipper_region' => $this->israel_cities[$request->input('shipper_city')]
    				]);
    			}

    			for ($i=0; $i < count($row_arr); $i++) { 
    				$worksheet = PhilIndWorksheet::find($row_arr[$i]);
    				$worksheet->checkCourierTask($worksheet->status);
    			}
    		}
    		else $status_error = 'New fields error!';
    	}
        
        if($status_error){
        	return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
        }
        else{
        	return redirect()->to(session('this_previous_url'))->with('status', 'Rows updated successfully!'.' '.$check_result);
        }
    }


    public function deletePhilIndWorksheetById(Request $request)
	{
		$row_arr = $request->input('row_id');

		for ($i=0; $i < count($row_arr); $i++) { 
			$this->removeTrackingFromPalletWorksheet($row_arr[$i], 'en');
			$this->deleteUploadFiles('eng_worksheet_id',$row_arr[$i]);
			$worksheet = PhilIndWorksheet::find($row_arr[$i]);
			$this->deletedToUpdatesArchive($worksheet);
		}

		PhilIndWorksheet::whereIn('id', $row_arr)->delete();
		PackingEngNew::whereIn('work_sheet_id', $row_arr)->delete();
		ReceiptArchive::whereIn('worksheet_id', $row_arr)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Rows deleted successfully!');
	}


	public function exportExcelPackingEng()
	{
		ini_set('memory_limit', '256M');
    	return Excel::download(new PackingEngExport, 'PackingEngExport.xlsx');
	}


	public function philIndWorksheetFilter(Request $request)
	{
        $title = 'Work sheet Filter';
        $arr_columns = parent::new_phil_ind_columns();   
        $data = $request->all(); 
        $search = $request->table_filter_value;
        $worksheet_arr = []; 
        $attributes = PhilIndWorksheet::first()->attributesToArray(); 
        $id_arr = [];
        $new_arr = []; 
        
        if ($request->table_columns) {
        	$phil_ind_worksheet_obj = PhilIndWorksheet::where('in_trash',false)->where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at' && $key !== 'update_status_date') {
        			$sheet = PhilIndWorksheet::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {
        				$temp_arr = PhilIndWorksheet::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get();
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$worksheet_arr[] = $new_arr;         		
        			} 
        		}        		
        	}
        	
        	return view('admin.phil_ind.phil_ind_worksheet_find', ['title' => $title,'worksheet_arr' => $worksheet_arr,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
        }                       
        
        return view('admin.phil_ind.phil_ind_worksheet', ['title' => $title,'data' => $data,'phil_ind_worksheet_obj' => $phil_ind_worksheet_obj,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
    }


    public function deactivate($id)
    {
    	$worksheet = PhilIndWorksheet::find($id);
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
					$message = $this->checkReceipt($draft->id, null, 'en', $draft->tracking_main);
				}
				$this->checkForMissingTracking($draft->tracking_main);
			}			

            // Transfer documents
            SignedDocument::where('eng_worksheet_id',$id)
    		->update([
    			'eng_worksheet_id' => null,
    			'eng_draft_id' => $draft->id
    		]);					    		
    		
    		$worksheet->delete();
    		PackingEngNew::where('work_sheet_id', $id)->delete();
    		
    		$draft->checkCourierTask($draft->status);
    		
    		return redirect()->to(session('this_previous_url'))->with('status', 'Row successfully returned to draft! '.$message);
    	}
    	else{
			return redirect()->to(session('this_previous_url'))->with('status-error', 'Deactivation error!');
		}
    }
}
