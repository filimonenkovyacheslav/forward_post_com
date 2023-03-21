<?php

namespace App\Exports;

use App\ChinaWorksheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class ChinaWorksheetExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return ChinaWorksheet::query();
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('china_worksheet');
    }
}