<?php

namespace App\Exports;

use App\NewWorksheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class NewWorksheetExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return NewWorksheet::query()->where('in_trash',false);
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('new_worksheet');
    }
}