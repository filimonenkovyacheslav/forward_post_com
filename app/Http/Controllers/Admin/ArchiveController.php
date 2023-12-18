<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Archive;
use App\NewWorksheet;
use App\NewPacking;
use App\Invoice;
use App\Manifest;
use App\ReceiptArchive;
use App\PhilIndWorksheet;
use App\PackingEngNew;
use Illuminate\Support\Facades\File;
use Excel;
use App\Exports\DataExport;


class ArchiveController extends AdminController
{
	public function index()
	{
        $archive = new Archive();
        $temp_archive_data = $archive->getTempArchiveDataTable();

		$title = 'Archive';
		$archive_obj = Archive::paginate(10);
		return view('admin.archive.archive', compact('title', 'archive_obj', 'temp_archive_data'));
	}


	public function archiveFilter(Request $request)
	{
        $title = 'Archive Filter';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = Archive::first()->attributesToArray();
        $archive = new Archive();
        $temp_archive_data = $archive->getTempArchiveDataTable();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$archive_obj = Archive::where($request->table_columns, 'like', '%'.$search.'%')->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = Archive::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = Archive::where($key, 'like', '%'.$search.'%')->get();
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

        	return view('admin.archive.archive_find', compact('title','filter_arr', 'temp_archive_data'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.archive.archive', compact('title','archive_obj','data', 'temp_archive_data'));
    }


    public function toArchive(Request $request)
    {
    	$archive = new Archive();
    	$id_arr = $archive->createArchive($request);
    	
        if ($id_arr) {
            $data = $request->all();
            $table = $data['table_name'];

            $files_folder = $this->downloadZip($id_arr, $table, $data['order_date']);
            $archive->createTempArchiveDataTable($id_arr,$table,$files_folder);
            
            if ($files_folder) return redirect('/api/download-zip?files_folder='.$files_folder);
            else return redirect()->to(session('this_previous_url'))->with('status', 'There are not files for download!');                       		
        }
        else return redirect()->to(session('this_previous_url'))->with('status-error', 'Request Error!');
    }


    public function toRepeatDownloadFiles()
    {
        $archive = new Archive();
        $temp_archive_data = $archive->getTempArchiveDataTable();
        
        if ($temp_archive_data) {            
            if ($temp_archive_data->files_folder) return response()->download(public_path($temp_archive_data->files_folder.'.zip'));
            else return redirect()->to(session('this_previous_url'))->with('status', 'There are not files for download!');                              
        }
        else return redirect()->to(session('this_previous_url'))->with('status-error', 'Request Error!');
    }


    public function toRepeatImport()
    {
        $archive = new Archive();
        $temp_archive_data = $archive->getTempArchiveDataTable();       
        
        if ($temp_archive_data) {            
            $archive->repeatCreateArchive($temp_archive_data->archive_table, json_decode($temp_archive_data->archive_ids)); 
            return redirect()->to(session('this_previous_url'))->with('status', 'Re-recording to archive completed successfully!');                              
        }
        else return redirect()->to(session('this_previous_url'))->with('status-error', 'Request Error!');
    }


    public function toArchiveRemoveFiles()
    {        
        $archive = new Archive();
        $temp_archive_data = $archive->getTempArchiveDataTable();
        $files_folder = $temp_archive_data->files_folder;
        
        if ($files_folder) {
            if (file_exists(public_path($files_folder.'.zip'))) unlink(public_path($files_folder.'.zip'));
            $folderPath = $this->checkDirectory($files_folder);

            if (is_dir(public_path('upload/'.$files_folder))) {
                $files = File::files(public_path('upload/'.$files_folder));
                foreach ($files as $key => $value){                 
                    unlink($folderPath.basename($value));                
                }  
                rmdir(public_path('upload/'.$files_folder));         
            }
            return redirect()->to(session('this_previous_url'))->with('status', 'Temporary files deleted successfully!');
        }
        else
            return redirect()->to(session('this_previous_url'))->with('status-error', 'Nothing to delete!');
    }


    public function toArchiveRemoveData()
    {        
        $archive = new Archive();
        $temp_archive_data = $archive->getTempArchiveDataTable();
        $id_arr = json_decode($temp_archive_data->archive_ids);
        $table = $temp_archive_data->archive_table;

        if ($id_arr && $table) {
            foreach ($id_arr as $id) {           
                $this->__toArchive($table, $id);
            }
            return redirect()->to(session('this_previous_url'))->with('status', 'Rows deleted successfully!');
        }
        else
            return redirect()->to(session('this_previous_url'))->with('status-error', 'Nothing to delete!');
    }


    public function toArchiveRemoveTempData()
    {        
        $archive = new Archive();
        $result = $archive->deleteTempArchiveDataTable();
        if ($result) return redirect()->to(session('this_previous_url'))->with('status', 'Temporary data deleted successfully!');
        else
        return redirect()->to(session('this_previous_url'))->with('status-error', 'There is not temporary data!');
    }


    private function __toArchive($table, $id)
    {
    	switch($table) {
    		case 'new_worksheet';
    			$this->removeTrackingFromPalletWorksheet($id, 'ru');
                $this->deleteUploadFiles('worksheet_id',$id);
                NewWorksheet::find($id)->delete();
                NewPacking::where('work_sheet_id', $id)->delete();
                Invoice::where('work_sheet_id', $id)->delete();
                Manifest::where('work_sheet_id', $id)->delete();
                ReceiptArchive::where('worksheet_id', $id)->delete();
    			break;
    		case 'phil_ind_worksheet';
    			$this->removeTrackingFromPalletWorksheet($id, 'en');
                $this->deleteUploadFiles('eng_worksheet_id',$id);
                PhilIndWorksheet::find($id)->delete();
                PackingEngNew::where('work_sheet_id', $id)->delete();
                ReceiptArchive::where('worksheet_id', $id)->delete();
    			break;      
    		default:
    		break;
    	}
    }


    public function exportArchive(Request $request)
    {
        return Excel::download(new DataExport('archive'), 'archive_'.date('Y_m_d').'.csv');
    }


    public function deleteFromArchive(Request $request)
    {
        if ($request->to_date >= $request->from_date) {
            $archive = new Archive();
            $result = $archive->deleteArchive($request->from_date, $request->to_date);
            if ($result) return redirect()->to(session('this_previous_url'))->with('status', 'Rows deleted successfully!');
            else
                return redirect()->to(session('this_previous_url'))->with('status-error', 'Nothing to delete!');
        }
        else
            return redirect()->to(session('this_previous_url'))->with('status-error', 'Incorrect dates!');
    }

}
