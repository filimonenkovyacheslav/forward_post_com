<?php

namespace App\Exports\CourierTaskExport\South;

use App\CourierTask;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class DdcSouthExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return CourierTask::query()->where([
            ['shipper_region','South'],
            ['site_name','DD-C']
        ]);
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('couriers_tasks');
    }
}