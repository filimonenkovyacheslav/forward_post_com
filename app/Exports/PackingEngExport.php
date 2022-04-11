<?php

namespace App\Exports;

use App\PackingEng;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class PackingEngExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return PackingEng::query()->where('in_trash',false);
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('packing_eng');
    }
}