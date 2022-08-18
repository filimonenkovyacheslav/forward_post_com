<?php
namespace App\Imports;
use App\TrackingEng; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TrackingsImportEng implements ToModel, WithHeadingRow
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
		$model = TrackingEng::firstOrCreate( 
			['tracking_main' => $row['tracking_main']]
		);
		
		$this->data->push($model);
		return $model;
	}
}