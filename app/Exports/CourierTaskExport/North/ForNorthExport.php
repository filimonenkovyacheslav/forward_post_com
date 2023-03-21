<?php

namespace App\Exports\CourierTaskExport\North;

use App\CourierTask;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class ForNorthExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return CourierTask::query()->where([
            ['shipper_region','North'],
            ['site_name','For']
        ]);
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('couriers_tasks');
    }
}