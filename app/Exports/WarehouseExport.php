<?php

namespace App\Exports;

use App\Warehouse;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class WarehouseExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return Warehouse::query();
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('warehouse');
    }
}