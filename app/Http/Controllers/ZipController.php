<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;

class ZipController extends Controller
{
    public function downloadZipPdf(Request $request)
    {
        $zip = new \ZipArchive();
        $fileName = $request->files_folder.'.zip';
        if ($zip->open(public_path($fileName), \ZipArchive::CREATE)== TRUE)
        {
            $files = File::files(public_path('upload/'.$request->files_folder));
            foreach ($files as $key => $value){
                $relativeName = basename($value);
                $zip->addFile($value, $relativeName);
            }
            $zip->close();
        }

        return response()->download(public_path($fileName));
    }
}