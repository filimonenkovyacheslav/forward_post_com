<?php

namespace App\Exports;

use App\NewPacking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class NewPackingExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return NewPacking::query()->where('in_trash',false)->orderBy('work_sheet_id');
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('new_packing');
    }
}