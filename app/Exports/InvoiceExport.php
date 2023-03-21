<?php

namespace App\Exports;

use App\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class InvoiceExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return Invoice::query()->where('in_trash',false)->orderBy('work_sheet_id');
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('invoice');
    }
}