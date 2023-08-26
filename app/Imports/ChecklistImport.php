<?php
namespace App\Imports;
use App\Checklist; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ChecklistImport implements ToModel, WithHeadingRow
{
	public $data;
	public function __construct()
	{
		$this->data = collect();
	}
	

	/**
	* @param array $row
	*
	* @return \Illuminate\Database\Eloquent\Model|null
	*/
	public function model(array $row)
	{
		$model = Checklist::firstOrCreate( 
			[
				'tracking_main' => $row['tracking_main'],
				'value' => $row['value']
			]	
		);

		$this->data->push($model);
		return $model;
	}


	public function checkRow($row): bool
    {
        $result = false;
        $keys = array_keys($row);
        $attributes = Checklist::getAttr();
        for ($i=0; $i < count($attributes); $i++) { 
            if (!in_array($attributes[$i], $keys)) {
                return $result;
            }
        }
        return $result = true;
    }
}