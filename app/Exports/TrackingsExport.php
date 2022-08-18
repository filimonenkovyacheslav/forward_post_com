<?php

namespace App\Exports;

use App\Tracking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class TrackingsExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return Tracking::query();
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('trackings');
    }
}