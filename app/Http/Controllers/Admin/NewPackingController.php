<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\NewPacking;
use App\Invoice;
use App\Manifest;
use Excel;
use App\Exports\NewPackingExport;
use App\Exports\InvoiceExport;
use App\Exports\ManifestExport;


class NewPackingController extends AdminController
{
	public function index()
	{
		$title = 'Новый пакинг лист';
        $collection = NewPacking::all();
        foreach ($collection as $item) {
            if ($item->worksheet && $item->worksheet->status === "Доставлено") 
                $item->delete();
        }
        $new_packing_obj = NewPacking::where('in_trash',false)->orderBy('work_sheet_id')->paginate(10);

        return view('admin.packing.new_packing', compact('title','new_packing_obj'));
	}


	public function exportExcelNewPacking()
	{

    	return Excel::download(new NewPackingExport, 'NewPackingExport.xlsx');

	}


	public function newPackingFilter(Request $request){
        $title = 'Фильтр Нового пакинга';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = NewPacking::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$new_packing_obj = NewPacking::where('in_trash',false)->where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at' && $key !== 'update_status_date') {
        			$sheet = NewPacking::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = NewPacking::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get();
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

        	return view('admin.packing.new_packing_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.packing.new_packing', compact('title','new_packing_obj','data'));
    }


    public function indexInvoice()
	{
		$title = 'Инвойс-Израиль';
        $invoice_obj = Invoice::where('in_trash',false)->orderBy('work_sheet_id')->paginate(10);

        return view('admin.packing.invoice', compact('title','invoice_obj'));
	}


	public function exportExcelInvoice()
	{

    	return Excel::download(new InvoiceExport, 'InvoiceExport.xlsx');

	}


	public function invoiceFilter(Request $request){
        $title = 'Фильтр Инвойс-Израиль';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = Invoice::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$invoice_obj = Invoice::where('in_trash',false)->where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = Invoice::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = Invoice::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get();
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

        	return view('admin.packing.invoice_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.packing.invoice', compact('title','invoice_obj','data'));
    }


        public function indexManifest()
	{
		$title = 'Манифест';
        $manifest_obj = Manifest::where('in_trash',false)->orderBy('work_sheet_id')->paginate(10);

        return view('admin.packing.manifest', compact('title','manifest_obj'));
	}


	public function exportExcelManifest()
	{

    	return Excel::download(new ManifestExport, 'ManifestExport.xlsx');

	}


	public function manifestFilter(Request $request){
        $title = 'Фильтр Манифест';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = Manifest::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$manifest_obj = Manifest::where('in_trash',false)->where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = Manifest::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = Manifest::where('in_trash',false)->where($key, 'like', '%'.$search.'%')->get();
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

        	return view('admin.packing.manifest_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.packing.manifest', compact('title','manifest_obj','data'));
    }
}