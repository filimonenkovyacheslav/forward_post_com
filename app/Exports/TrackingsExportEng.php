<?php

namespace App\Exports;

use App\TrackingEng;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class TrackingsExportEng implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return TrackingEng::query();
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('trackings_eng');
    }
}