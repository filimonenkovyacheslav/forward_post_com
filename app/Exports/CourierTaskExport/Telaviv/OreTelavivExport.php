<?php

namespace App\Exports\CourierTaskExport\Telaviv;

use App\CourierTask;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class OreTelavivExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return CourierTask::query()->where([
            ['shipper_region','Tel Aviv'],
            ['site_name','ORE']
        ]);
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('couriers_tasks');
    }
}