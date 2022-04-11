<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\UpdatesArchive;


class UpdatesArchiveController extends AdminController
{
	public function index()
	{
		$title = 'Updates Archive';		
		$updates_archive_obj = UpdatesArchive::paginate(10);
		return view('admin.updates_archive.updates_archive', compact('title', 'updates_archive_obj'));
	}


	public function updatesArchiveFilter(Request $request)
	{
        $title = 'Updates Archive Filter';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = UpdatesArchive::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$updates_archive_obj = UpdatesArchive::where($request->table_columns, 'like', '%'.$search.'%')->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = UpdatesArchive::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = UpdatesArchive::where($key, 'like', '%'.$search.'%')->get();
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

        	return view('admin.updates_archive.updates_archive_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.updates_archive.updates_archive', compact('title','updates_archive_obj','data'));
    }


    public function destroy(Request $request)
    {
        $id = $request->input('action');        
        UpdatesArchive::find($id)->delete();
        return redirect()->to(session('this_previous_url'))->with('status', 'Row deleted successfully!');
    }


    public function destroyArchiveById(Request $request)
    {
        $row_arr = $request->input('row_id');
        for ($i=0; $i < count($row_arr); $i++) { 
            UpdatesArchive::find($row_arr[$i])->delete();
        }

        return redirect()->to(session('this_previous_url'))->with('status', 'Rows deleted successfully!');
    }

}
