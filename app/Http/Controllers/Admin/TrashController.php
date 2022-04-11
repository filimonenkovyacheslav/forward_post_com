<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Trash;


class TrashController extends AdminController
{
	public function index()
	{
		$title = 'Корзина/Trash';
		$delete_date = Date('Y-m-d', strtotime('-90 days'));
		$trash = Trash::where('created_at','<=',$delete_date)->get();
		$result = $trash->map(function ($item, $key) {
			return $item->removeСompletely();
		});
		$trash_obj = Trash::paginate(10);
		return view('admin.trash.trash', compact('title', 'trash_obj'));
	}


	public function trashFilter(Request $request)
	{
        $title = 'Trash Filter';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = Trash::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$trash_obj = Trash::where($request->table_columns, 'like', '%'.$search.'%')->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = Trash::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = Trash::where($key, 'like', '%'.$search.'%')->get();
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

        	return view('admin.trash.trash_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.trash.trash', compact('title','trash_obj','data'));
    }


    public function toTrash(Request $request)
    {
    	$trash = new Trash();
    	$trash_count = $trash->createTrash($request);
    	if ($trash_count) {
    		$data = $request->all();
    		$table = $data['table'];
    		if (isset($data['action'])) {
    			$id = $data['action'];
    			$this->__toTrash($table, $id);
    		}
    		elseif (isset($data['row_id'])) {
    			$id_arr = $data['row_id'];
    			for ($i=0; $i < count($id_arr); $i++) { 
    				$this->__toTrash($table, $id_arr[$i]);
    			}
    		}    		
    	}

    	return redirect()->to(session('this_previous_url'))->with('status', 'Строка добавлена в корзину / Row adds to trash!');
    }


    private function __toTrash($table, $id)
    {
    	switch($table) {
    		case 'new_worksheet';
    			$this->removeTrackingFromPalletWorksheet($id, 'ru');
    			break;
    		case 'phil_ind_worksheet';
    			$this->removeTrackingFromPalletWorksheet($id, 'en');
    			break;
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


    public function fromTrash($id)
    {
    	$trash = Trash::find($id);
    	$activate = $trash->trashActivate();
    	return redirect()->to(session('this_previous_url'))->with('status', 'Строка восстановлена / Row restored!');
    }

}
