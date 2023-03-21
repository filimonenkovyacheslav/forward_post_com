<?php

namespace App\Exports;

use App\PackingEngNew;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class PackingEngNewExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return PackingEngNew::query()->where('in_trash',false);
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('packing_eng_new');
    }
}