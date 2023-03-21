<?php

namespace App\Exports;

use App\CourierDraftWorksheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class CourierDraftWorksheetExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return CourierDraftWorksheet::query()->where('in_trash',false);
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('courier_draft_worksheet');
    }
}