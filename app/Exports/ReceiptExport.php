<?php

namespace App\Exports;

use App\Receipt;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class ReceiptExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return Receipt::query()->orderBy('receipt_number');
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('receipts');
    }
}