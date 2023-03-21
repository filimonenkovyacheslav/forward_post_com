<?php

namespace App\Exports\CourierTaskExport\Center;

use App\CourierTask;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class DdcCenterExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return CourierTask::query()->where([
            ['shipper_region','Center'],
            ['site_name','DD-C']
        ]);
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('couriers_tasks');
    }
}