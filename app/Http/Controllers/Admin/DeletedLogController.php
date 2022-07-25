<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\DeletedLog;


class DeletedLogController extends AdminController
{
	public function index()
	{
		$title = 'Log of deleted orders';
		$delete_date = Date('Y-m-d', strtotime('-90 days'));
		$log = DeletedLog::where('created_at','<=',$delete_date)->get();
		$result = $log->map(function ($item, $key) {
			return $item->removeСompletely();
		});
		$log_obj = DeletedLog::paginate(10);
		return view('admin.log.log', compact('title', 'log_obj'));
	}


	public function logsFilter(Request $request)
	{
        $title = 'Log of deleted orders Filter';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = DeletedLog::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$log_obj = DeletedLog::where($request->table_columns, 'like', '%'.$search.'%')->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = DeletedLog::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = DeletedLog::where($key, 'like', '%'.$search.'%')->get();
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

        	return view('admin.log.log_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.log.log', compact('title','log_obj','data'));
    }


    private function updateLogs($log)
    {
        if ($log->table_name === 'courier_draft_worksheet') $type = 'draft_id';
        else $type = 'eng_draft_id';
        $items = $this->getUploadFiles($type,$log->worksheet_id);
        $log->packing_files = json_encode($items);
        $log->save();
        return $log;
    }


    public function toLogs(Request $request)
    {
    	$log = new DeletedLog();
    	$logs = $log->createDeletedLog($request);
    	if ($logs) {
    		$data = $request->all();
    		$table = $data['table'];
    		if (isset($data['action'])) {
    			$id = $data['action'];
    			$this->__toDeletedLog($table, $id);
                $this->updateLogs($logs);
    		}
    		elseif (isset($data['row_id'])) {
    			$id_arr = $data['row_id'];
    			for ($i=0; $i < count($id_arr); $i++) { 
    				$this->__toDeletedLog($table, $id_arr[$i]);
                    $this->updateLogs($logs[$i]);
    			}
    		}    		
    	}
        
        return json_encode(['status'=>'success']);
    }


    private function __toDeletedLog($table, $id)
    {
    	switch($table) {
    		case 'courier_draft_worksheet';
    			$this->removeTrackingFromPalletWorksheet($id, 'ru',true);
    			break;
    		case 'courier_eng_draft_worksheet';
    			$this->removeTrackingFromPalletWorksheet($id, 'en',true);
    			break;       
    		default:
    		break;
    	}
    }


    public function logShow($id)
    {
        $title = 'Log of deleted orders No. '.$id;
        $log = DeletedLog::find($id);
        return view('admin.log.log_show', compact('title','log'));
    }

}
