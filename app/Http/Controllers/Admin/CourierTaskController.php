<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\CourierTask;
use App\Exports\CourierTaskExport\South\DdcSouthExport;
use App\Exports\CourierTaskExport\South\ForSouthExport;
use App\Exports\CourierTaskExport\South\OreSouthExport;
use App\Exports\CourierTaskExport\Center\DdcCenterExport;
use App\Exports\CourierTaskExport\Center\ForCenterExport;
use App\Exports\CourierTaskExport\Center\OreCenterExport;
use App\Exports\CourierTaskExport\Eilat\DdcEilatExport;
use App\Exports\CourierTaskExport\Eilat\ForEilatExport;
use App\Exports\CourierTaskExport\Eilat\OreEilatExport;
use App\Exports\CourierTaskExport\Haifa\DdcHaifaExport;
use App\Exports\CourierTaskExport\Haifa\ForHaifaExport;
use App\Exports\CourierTaskExport\Haifa\OreHaifaExport;
use App\Exports\CourierTaskExport\Jerusalem\DdcJerusalemExport;
use App\Exports\CourierTaskExport\Jerusalem\ForJerusalemExport;
use App\Exports\CourierTaskExport\Jerusalem\OreJerusalemExport;
use App\Exports\CourierTaskExport\North\DdcNorthExport;
use App\Exports\CourierTaskExport\North\ForNorthExport;
use App\Exports\CourierTaskExport\North\OreNorthExport;
use App\Exports\CourierTaskExport\Telaviv\DdcTelavivExport;
use App\Exports\CourierTaskExport\Telaviv\ForTelavivExport;
use App\Exports\CourierTaskExport\Telaviv\OreTelavivExport;
use App\Exports\CourierTaskExport\CourierTaskExport;
use Excel;


class CourierTaskController extends AdminController
{
	public function import()
	{
		$tasks = new CourierTask();
		return $tasks->importWorksheet();
	}
	

	public function index()
	{
		$title = 'Задания Курьерам/Couriers Tasks';
		$couriers_tasks_obj = CourierTask::paginate(10);		
		
		return view('admin.couriers_tasks.couriers_tasks', compact('title', 'couriers_tasks_obj'));
	}


	public function courierTaskFilter(Request $request)
	{
        $title = 'Couriers Tasks Filter';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = CourierTask::first()->attributesToArray();        

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$couriers_tasks_obj = CourierTask::where($request->table_columns, 'like', '%'.$search.'%')->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = CourierTask::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = CourierTask::where($key, 'like', '%'.$search.'%')->get();
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

        	return view('admin.couriers_tasks.couriers_tasks_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.couriers_tasks.couriers_tasks', compact('title','couriers_tasks_obj','data'));
    }


    public function courierTaskDone($id)
    {
    	$task = CourierTask::find($id);
    	$done = $task->taskDone();
    	return redirect()->to(session('this_previous_url'))->with('status', 'Задание отмечено как выполненное / Task marked as completed!');
    }


    public function doneById(Request $request)
    {
    	$row_arr = $request->input('row_id');
		for ($i=0; $i < count($row_arr); $i++) { 
			$task = CourierTask::find($row_arr[$i]);
			$done = $task->taskDone();
		}
    	return redirect()->to(session('this_previous_url'))->with('status', 'Задания отмечены как выполненные / Tasks marked as completed!');
    }


    public function addCourierTaskDataById(Request $request)
    {
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('tracking-columns');

    	if ($column === 'courier') $value_by = $request->input('courier');

    	for ($i=0; $i < count($row_arr); $i++) { 
    		$task = CourierTask::find($row_arr[$i]);
    		$worksheet = $task->getWorksheet();

    		if ($column === 'pick_up_date_comments') {
    			switch($worksheet->table) {

    				case "new_worksheet":

    				$request->merge(['tracking-columns' => 'pick_up_date']);

    				break;

    				case "phil_ind_worksheet":

    				$request->merge(['tracking-columns' => 'delivery_date_comments']);

    				break;

    				case "courier_draft_worksheet":

    				$request->merge(['tracking-columns' => 'pick_up_date']);

    				break;

    				case "courier_eng_draft_worksheet":

    				$request->merge(['tracking-columns' => 'delivery_date_comments']);

    				break;       
    				default:
    				break;
    			} 
    		} 
    		
    		$this->toUpdatesArchive($request,$worksheet);
    		$task->taskUpdate($value_by,$column);
    	}

    	return redirect()->to(session('this_previous_url'))->with('status', 'Задание успешно обновлено / Task updated successfully!');
    }


	public function exportExcelCourierTask(Request $request)
	{
		if (!$request->export_region && !$request->export_site) {
			return Excel::download(new CourierTaskExport, 'CourierTaskExport.xlsx');
		}
		if ($request->export_region === 'South') {
			if ($request->export_site === 'DD-C') {
				return Excel::download(new DdcSouthExport, 'DdcSouthExport.xlsx');
			}
			if ($request->export_site === 'For') {
				return Excel::download(new ForSouthExport, 'ForSouthExport.xlsx');
			}
			if ($request->export_site === 'ORE') {
				return Excel::download(new OreSouthExport, 'OreSouthExport.xlsx');
			}
		}
		if ($request->export_region === 'Center') {
			if ($request->export_site === 'DD-C') {
				return Excel::download(new DdcCenterExport, 'DdcCenterExport.xlsx');
			}
			if ($request->export_site === 'For') {
				return Excel::download(new ForCenterExport, 'ForCenterExport.xlsx');
			}
			if ($request->export_site === 'ORE') {
				return Excel::download(new OreCenterExport, 'OreCenterExport.xlsx');
			}
		}
		if ($request->export_region === 'Eilat') {
			if ($request->export_site === 'DD-C') {
				return Excel::download(new DdcEilatExport, 'DdcEilatExport.xlsx');
			}
			if ($request->export_site === 'For') {
				return Excel::download(new ForEilatExport, 'ForEilatExport.xlsx');
			}
			if ($request->export_site === 'ORE') {
				return Excel::download(new OreEilatExport, 'OreEilatExport.xlsx');
			}
		}
		if ($request->export_region === 'Haifa') {
			if ($request->export_site === 'DD-C') {
				return Excel::download(new DdcHaifaExport, 'DdcHaifaExport.xlsx');
			}
			if ($request->export_site === 'For') {
				return Excel::download(new ForHaifaExport, 'ForHaifaExport.xlsx');
			}
			if ($request->export_site === 'ORE') {
				return Excel::download(new OreHaifaExport, 'OreHaifaExport.xlsx');
			}
		}
		if ($request->export_region === 'Jerusalem') {
			if ($request->export_site === 'DD-C') {
				return Excel::download(new DdcJerusalemExport, 'DdcJerusalemExport.xlsx');
			}
			if ($request->export_site === 'For') {
				return Excel::download(new ForJerusalemExport, 'ForJerusalemExport.xlsx');
			}
			if ($request->export_site === 'ORE') {
				return Excel::download(new OreJerusalemExport, 'OreJerusalemExport.xlsx');
			}
		}
		if ($request->export_region === 'North') {
			if ($request->export_site === 'DD-C') {
				return Excel::download(new DdcNorthExport, 'DdcNorthExport.xlsx');
			}
			if ($request->export_site === 'For') {
				return Excel::download(new ForNorthExport, 'ForNorthExport.xlsx');
			}
			if ($request->export_site === 'ORE') {
				return Excel::download(new OreNorthExport, 'OreNorthExport.xlsx');
			}
		}
		if ($request->export_region === 'Tel Aviv') {
			if ($request->export_site === 'DD-C') {
				return Excel::download(new DdcTelavivExport, 'DdcTelavivExport.xlsx');
			}
			if ($request->export_site === 'For') {
				return Excel::download(new ForTelavivExport, 'ForTelavivExport.xlsx');
			}
			if ($request->export_site === 'ORE') {
				return Excel::download(new OreTelavivExport, 'OreTelavivExport.xlsx');
			}
		}		
	}

}
