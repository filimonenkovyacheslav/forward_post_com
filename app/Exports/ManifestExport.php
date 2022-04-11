<?php

namespace App\Exports;

use App\Manifest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class ManifestExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return Manifest::query()->where('in_trash',false)->orderBy('work_sheet_id');
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('manifest');
    }
}