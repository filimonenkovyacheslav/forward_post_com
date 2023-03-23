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
			['tracking_main' => $row['tracking_main']]
		);
		
		$this->data->push($model);
		return $model;
	}
}