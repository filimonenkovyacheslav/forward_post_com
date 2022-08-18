<?php
namespace App\Imports;
use App\Tracking; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TrackingsImport implements ToModel, WithHeadingRow
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
		$model = Tracking::firstOrCreate( 
			['tracking_main' => $row['tracking_main']]
		);
		
		$this->data->push($model);
		return $model;
	}
}