<?php

namespace App\Exports;

use App\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;

class WorksheetExport implements FromCollection
{
    public function collection()
    {
        return Worksheet::all();
    }
}