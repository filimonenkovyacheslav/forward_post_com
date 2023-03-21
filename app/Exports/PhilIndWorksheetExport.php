<?php

namespace App\Exports;

use App\PhilIndWorksheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class PhilIndWorksheetExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return PhilIndWorksheet::query()->where('in_trash',false);
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('phil_ind_worksheet');
    }
}