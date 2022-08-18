<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Imports\TrackingsImport;
use App\Exports\TrackingsExport;
use App\Imports\TrackingsImportEng;
use App\Exports\TrackingsExportEng;
use Excel;

class TrackingController extends Controller
{
    /**
    * Uploads the records in a csv file or excel using maatwebsite package 
    *
    * @param Request $request
    * @return mixed
    */
    public function importTrackings(Request $request)
    {
        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');
            $import = new TrackingsImport();

            if (Schema::hasTable('trackings')) Schema::dropIfExists('trackings');
            Schema::create('trackings', function (Blueprint $table) {
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


    public function exportTrackings()
    {
        if (Schema::hasTable('trackings')) 
            return Excel::download(new TrackingsExport, 'tracking_numbers.csv');
        else
            return redirect()->to(session('this_previous_url'))->with('status-error', 'File did not find!');
    }


    public function importTrackingsEng(Request $request)
    {
        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');
            $import = new TrackingsImportEng();

            if (Schema::hasTable('trackings_eng')) Schema::dropIfExists('trackings_eng');
            Schema::create('trackings_eng', function (Blueprint $table) {
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


    public function exportTrackingsEng()
    {
        if (Schema::hasTable('trackings_eng'))
            return Excel::download(new TrackingsExportEng, 'tracking_numbers_eng.csv');
        else
            return redirect()->to(session('this_previous_url'))->with('status-error', 'File did not find!');
    }

}
