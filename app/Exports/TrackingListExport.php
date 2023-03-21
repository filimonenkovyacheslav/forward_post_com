<?php

namespace App\Exports;

use App\TrackingList;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class TrackingListExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function __construct(string $list_name)
    {
        $this->list_name = $list_name;
    }

    public function query()
    {
        return TrackingList::query()->where('list_name',$this->list_name);
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('tracking_lists');
    }
}