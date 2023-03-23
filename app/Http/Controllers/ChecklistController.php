<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Imports\ChecklistImport;
use App\Checklist;
use Excel;

class ChecklistController extends Controller
{
    public function index()
    {
        $title = 'Checklist';
        $checklist_obj = Checklist::paginate(10);
        return view('admin.checklist', compact('title','checklist_obj'));
    }
    

    /**
    * Uploads the records in a csv file or excel using maatwebsite package 
    *
    * @param Request $request
    * @return mixed
    */
    public function importChecklist(Request $request)
    {
        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');
            $import = new ChecklistImport();

            if (Schema::hasTable('checklist')) Schema::dropIfExists('checklist');
            Schema::create('checklist', function (Blueprint $table) {
                $table->increments('id');
                $table->string('tracking_main')->nullable();
                $table->timestamps();
            });
            
            Excel::import($import, $file);

            return redirect()->to(session('this_previous_url'))->with('status', 'File uploaded successfully!');
        } else {
            return redirect()->to(session('this_previous_url'))->with('status-error', 'File did not upload!');
        }
    }

}
