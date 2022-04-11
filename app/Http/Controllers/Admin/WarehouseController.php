<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Warehouse;
use App\PhilIndWorksheet;
use App\NewWorksheet;
use App\ReceiptArchive;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use Auth;
use Excel;
use App\Exports\WarehouseExport;


class WarehouseController extends AdminController
{
    public function index(Request $request){
        $title = 'Warehouse';
        $delete_date = Date('Y-m-d', strtotime('-90 days'));
        Warehouse::where('left','<=',$delete_date)->delete();
        if ($request->input('hide_left')) {
        	$warehouse_obj = Warehouse::where('left',null)->paginate(10);
        }
        else{
        	$warehouse_obj = Warehouse::paginate(10);
        }       
        
        $result = $this->palletCount();
        $in_count =  $result[0];
        $cd_count =  $result[1];  
          
        $user = Auth::user();
        $data = $request->all();
        
        return view('admin.warehouse.warehouse', compact('title','warehouse_obj','user','in_count','cd_count','data'));
    }


    private function palletCount()
    {
    	$in_count = Warehouse::where([
        	['tracking_numbers', 'like', '%IN%'],
        	['arrived', '<>', null],
        	['cell', '<>', null],
        	['left', null]
        ])
        ->orWhere([
        	['tracking_numbers', 'like', '%NE%'],
        	['arrived', '<>', null],
        	['cell', '<>', null],
        	['left', null]
        ])
        ->get();
        $in_count = $in_count ? $in_count->count() : 0;

        $cd_count = Warehouse::where([
        	['tracking_numbers', 'like', '%CD%'],
        	['arrived', '<>', null],
        	['cell', '<>', null],
        	['left', null]
        ])->get();
        $cd_count = $cd_count ? $cd_count->count() : 0;

        return [$in_count,$cd_count];
    }


	public function warehouseOpen($id)
	{
		$warehouse = Warehouse::find($id);
		if ($warehouse) {
			$title = 'Pallet No. '.$warehouse->pallet;
			$track_arr = json_decode($warehouse->tracking_numbers);
			return view('admin.warehouse.warehouse_update', compact('title','warehouse','track_arr','id'));
		}
		else{
			return redirect()->route('adminWarehouse');
		}
	}


	public function deleteTrackingFromPallet(Request $request)
	{
		$pallet = $request->input('pallet');
		$tracking = $request->input('action');
		$this->updateWarehouseWorksheet($pallet, $tracking);
		$this->updateWarehouseWorksheet($pallet, $tracking, false, true);
		ReceiptArchive::where([
				['tracking_main', $tracking],
				['worksheet_id', null],
				['receipt_id', null]
			])->delete();
		return redirect()->to(session('this_previous_url'))->with('status', 'Row deleted successfully!');
	}


	public function warehouseEditShow($id)
	{
		$warehouse = Warehouse::find($id);
		$title = 'pallet No. '.$warehouse->pallet;

		return response()->json(['title' => $title, 'warehouse' => json_encode($warehouse)]);
	}


	public function warehouseEdit(Request $request, $id)
	{
		$warehouse = Warehouse::find($id);
		$warehouse->cell = $request->input('cell');
		$warehouse->arrived = date('Y-m-d');
		$warehouse->save();
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Row updated successfully!');	
	}


	public function warehouseAddTrackingShow($id)
	{
		return response()->json(['id' => $id]);
	}


	public function warehouseAddTracking(Request $request, $id)
	{
		$tracking = $request->input('tracking');

		if ($tracking) {
			if (!$this->trackingValidate($tracking)) return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking number is not correct.');
		}

		$which_admin = $this->checkWhichAdmin($tracking);
		if (!$which_admin) {
			return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking number is not correct!');
		}

        $exist_tracking = Warehouse::where('tracking_numbers', 'like', '%'.$tracking.'%')->first();
        if ($exist_tracking) {
        	return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking exists!');
        }

        $notifications = (object)['pallet'=>'','tracking'=>''];
        $warehouse = Warehouse::find($id);
        if ($which_admin !== $warehouse->which_admin) {
        	return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking number is not correct!');
        }
        $worksheet_ru = NewWorksheet::where('in_trash',false)->where('tracking_main', $tracking)->first();
        if (!$worksheet_ru) $worksheet_ru = CourierDraftWorksheet::where('in_trash',false)->where('tracking_main', $tracking)->first();
        $worksheet_en = PhilIndWorksheet::where('in_trash',false)->where('tracking_main', $tracking)->first();
        if (!$worksheet_en) $worksheet_en = CourierEngDraftWorksheet::where('in_trash',false)->where('tracking_main', $tracking)->first();

        if (!$worksheet_ru && !$worksheet_en) {
        	if($warehouse->notifications){
        		$notifications = json_decode($warehouse->notifications);
        	}
        	if ($notifications->tracking) {
        		$notifications_tracking = json_decode($notifications->tracking);
        		$notifications_tracking->arr .= ','.$tracking;
        		$notifications_tracking->message = 'The ('.$notifications_tracking->arr.') are missing in the work sheet. Check the tracking number or add it to the work sheet';
        	}
        	else{
        		$notifications_tracking = (object)['arr'=>'','message'=>''];
        		$notifications_tracking->arr .= $tracking;
        		$notifications_tracking->message = 'The ('.$notifications_tracking->arr.') is missing in the work sheet. Check the tracking number or add it to the work sheet';
        	}

        	$notifications_tracking = json_encode($notifications_tracking);		
        	$notifications->tracking = $notifications_tracking;		
        	$notifications = json_encode($notifications);
        	
        	$track_arr = json_decode($warehouse->tracking_numbers);
        	if (!in_array($tracking, $track_arr)) $track_arr[] = $tracking;
        	$track_arr = json_encode($track_arr);
        	$warehouse->tracking_numbers = $track_arr;
        	$warehouse->notifications = $notifications;
        	$warehouse->save();

        	// Adding to ReceiptArchive
        	$message = 'The '.$tracking.' is missing in the work sheet. Check the tracking number or add it to the work sheet';
        	$update_date = Date('Y-m-d', strtotime('+3 days'));
        	$archive = [
        		'tracking_main' => $tracking,
        		'which_admin' => $which_admin,
        		'update_date' => $update_date,
                'status' => false,
        		'description' => $message
        	];
        	ReceiptArchive::create($archive);
        }			

        return redirect()->to(session('this_previous_url'))->with('status', 'Tracking added successfully!');
	}


	public function warehouseTrackingMoveShow($tracking)
	{
		$warehouse = Warehouse::where('tracking_numbers', 'like', '%'.$tracking.'%')->first();
		$title = 'tracking No. '.$tracking;
		$pallet_arr = Warehouse::where('pallet','<>',$warehouse->pallet)->pluck('pallet')->toArray();
		
		return response()->json(['title' => $title, 'tracking' => $tracking, 'palletArr' => json_encode($pallet_arr)]);
	}


	public function warehouseTrackingMove(Request $request, $tracking)
	{
		if ($request->input('pallet')) {

			$which_admin = $this->checkWhichAdmin($tracking);
			
			$warehouse_to = Warehouse::where('pallet', $request->input('pallet'))->first();
			$warehouse = Warehouse::where('tracking_numbers', 'like', '%'.$tracking.'%')->first();
			if ($which_admin !== $warehouse_to->which_admin) {
				return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking number is not correct!');
			}
			$this->updateWarehouse($warehouse->pallet, $request->input('pallet'), $tracking);			
			$this->updateWarehouseWorksheet($warehouse->pallet, $tracking, $request->input('pallet'));
			$this->updateWarehouseWorksheet($warehouse->pallet, $tracking, $request->input('pallet'), true);
				
			return redirect()->to(session('this_previous_url'))->with('status', 'Tracking Moved successfully!');
		}
		else{
			return redirect()->to(session('this_previous_url'))->with('status-error', 'Pallet number is empty!');
		}	
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');		
		$this->destroyById($id);
		return redirect()->to(session('this_previous_url'))->with('status', 'Row deleted successfully!');
	}


	private function destroyById($id)
	{
		$warehouse = Warehouse::find($id);
		$track_arr = json_decode($warehouse->tracking_numbers);
		if ($this->checkWhichAdmin($track_arr[0]) === 'ru'){
			NewWorksheet::whereIn('tracking_main', $track_arr)
			->update([
				'pallet_number' => null,
				'batch_number' => null
			]);
        }
        else if ($this->checkWhichAdmin($track_arr[0]) === 'en'){
        	PhilIndWorksheet::whereIn('tracking_main', $track_arr)
			->update([
				'pallet_number' => null,
				'lot' => null
			]);
        }
		$warehouse->delete();
	}


	public function warehouseFilter(Request $request){
        $title = 'Warehouse Filter';
        $search = $request->table_filter_value;
        $warehouse_arr = [];
        $attributes = Warehouse::first()->attributesToArray();
        $user = Auth::user(); 
        $id_arr = [];
        $new_arr = [];            

        $result = $this->palletCount();
        $in_count =  $result[0];
        $cd_count =  $result[1];   

        if ($request->table_columns) {
        	if ($request->input('hide_left')) {
        		$warehouse_obj = Warehouse::where([
        			[$request->table_columns, 'like', '%'.$search.'%'],
        			['left',null]
        		])
        	->paginate(10);
        	}
        	else{
        		$warehouse_obj = Warehouse::where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        	}       	
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			if ($request->input('hide_left')) {
        				$sheet = Warehouse::where([
        					[$key, 'like', '%'.$search.'%'],
        					['left',null]
        				])->get()->first();
        			}
        			else{
        				$sheet = Warehouse::where($key, 'like', '%'.$search.'%')->get()->first();
        			}
        			
        			if ($sheet) { 
        				if ($request->input('hide_left')) {
        					$temp_arr = Warehouse::where([
        						[$key, 'like', '%'.$search.'%'],
        						['left',null]
        					])->get();  					
        				}
        				else{
        					$temp_arr = Warehouse::where($key, 'like', '%'.$search.'%')->get();
        				}      				
        				
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$warehouse_arr[] = $new_arr;   				         		
        			}
        		}         		
        	}

        	return view('admin.warehouse.warehouse_find', compact('title','warehouse_arr','user','in_count','cd_count'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.warehouse.warehouse', compact('title','warehouse_obj','user','data','in_count','cd_count'));
    }


	public function exportExcel()
	{
		return Excel::download(new WarehouseExport, 'WarehouseExport.xlsx');
	}


	public function addWarehouseDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-id');
    	$column = $request->input('warehouse-columns');
    	$status_error = '';
		
    	if ($row_arr) {
    		if ($value_by && $column) {
    			Warehouse::whereIn('id', $row_arr)
    			->update([
    				$column => $value_by,
    				'arrived' => date('Y-m-d')
    			]);    	    		
    		}
    	}
        
        if($status_error){
        	return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
        }
        else{
        	return redirect()->to(session('this_previous_url'))->with('status', 'Rows updated successfully!');
        }
    }


    public function deleteWarehouseById(Request $request)
	{
		$row_arr = $request->input('row_id');
		for ($i=0; $i < count($row_arr); $i++) { 
			$this->destroyById($row_arr[$i]);
		}

		return redirect()->to(session('this_previous_url'))->with('status', 'Rows deleted successfully!');
	}


    public function palletsShow(){
        $title = 'Mass change of data by pallet number (supports mass selection of checkboxes)';
        $worksheet_obj = Warehouse::orderBy('pallet')->get();
        $date_arr = [];
        foreach ($worksheet_obj as $row) {
        	$temp = $row->pallet;
        	if (!in_array($row->pallet, $date_arr)) {
        		$date_arr[$row->pallet] = $row->pallet;
        	}       	
        }
        return view('admin.warehouse.warehouse_pallet_data', ['title' => $title,'date_arr' => $date_arr]);
    }

	
	public function addWarehouseDataByPallet(Request $request){
    	$row_arr = $request->input('pallet');
    	$value_by = $request->input('value-by-pallet');
    	$column = $request->input('warehouse-columns');
    	$status_error = '';
		
    	if ($row_arr) {
    		if ($value_by && $column) {
    			Warehouse::whereIn('pallet', $row_arr)
    			->update([
    				$column => $value_by,
    				'arrived' => date('Y-m-d')
    			]);    	    		
    		}
    	}
        
        if($status_error){
        	return redirect()->to(session('this_previous_url'))->with('status-error', $status_error);
        }
        else{
        	return redirect()->to(session('this_previous_url'))->with('status', 'Rows updated successfully!');
        }
    }


    public function palletsSum(Request $request)
    {
    	$from_date = $request->input('from_date');
    	$to_date = $request->input('to_date');
    	$sum = 0;

    	if (strtotime($from_date) > strtotime($to_date)) {
    		return json_encode(['error'=>'Start date is greater than end date!']);
    	}
    	else{
    		$sum = Warehouse::where([
    			['arrived','>=',$from_date],
    			['left','<=',$to_date]
    		])->get();
    		$sum = $sum ? $sum->count() : 0;
    		return json_encode(['sum'=>$sum]);
    	}
    }


	public function importWorksheet()
	{
		$ru_arr = CourierDraftWorksheet::where([
			['pallet_number', '<>', null],
			['batch_number', null],
			['tracking_main', 'like', 'CD%'],
			['status', '<>', 'Доставлено']
		])->get()->toArray();

		$en_arr = CourierEngDraftWorksheet::where([
			['pallet_number', '<>', null],
			['lot', null],
			['tracking_main', 'like', 'IN%'],
			['status', '<>', 'Delivered']
		])
		->orWhere([
			['pallet_number', '<>', null],
			['lot', null],
			['tracking_main', 'like', 'NE%'],
			['status', '<>', 'Delivered']
		])
		->get()->toArray();

		foreach ($ru_arr as $row) {
			$this->updateWarehouse(null, $row['pallet_number'], $row['tracking_main']);
		}

		foreach ($en_arr as $row) {
			$this->updateWarehouse(null, $row['pallet_number'], $row['tracking_main']);
		}

		return '<h1>Worksheet imported successfully !</h1>';
	}

}
