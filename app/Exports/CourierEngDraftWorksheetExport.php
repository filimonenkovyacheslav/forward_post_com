<?php

namespace App\Exports;

use App\CourierEngDraftWorksheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class CourierEngDraftWorksheetExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return CourierEngDraftWorksheet::query()->where('in_trash',false);
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('courier_eng_draft_worksheet');
    }
}