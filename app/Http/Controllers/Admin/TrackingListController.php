<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use App\TrackingList;
use App\Exports\TrackingListExport;
use Excel;

class TrackingListController extends AdminController
{
    public function index()
    {
        $title = 'Tracking lists';
        $tracking_list_obj = TrackingList::paginate(10);
        return view('admin.tracking_list.tracking_list', compact('title','tracking_list_obj'));
    }


    public function exportTrackingList(Request $request)
    {
        return Excel::download(new TrackingListExport($request->list_name), $request->list_name.'.csv');
    }


    public function destroy(Request $request)
    {
        $list_name = $request->action;
        TrackingList::where('list_name', $list_name)->delete();
        return redirect()->to(session('this_previous_url'))->with('status', 'List deleted successfully!');
    }


    public function trackingListFilter(Request $request)
    {
        $title = 'Tracking lists Filter';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = TrackingList::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
            $tracking_list_obj = TrackingList::where($request->table_columns, 'like', '%'.$search.'%')->paginate(10);
        }
        else{
            foreach($attributes as $key => $value)
            {
                if ($key !== 'created_at' && $key !== 'updated_at') {
                    $sheet = TrackingList::where($key, 'like', '%'.$search.'%')->get()->first();
                    if ($sheet) {                       
                        $temp_arr = TrackingList::where($key, 'like', '%'.$search.'%')->get();
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

            return view('admin.tracking_list.tracking_list_find', compact('title','filter_arr'));       
        }
        
        $data = $request->all();             
        
        return view('admin.tracking_list.tracking_list', compact('title','tracking_list_obj','data'));
    }

}
