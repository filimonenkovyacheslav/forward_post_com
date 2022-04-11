<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\ChinaWorksheet;
use DB;
use Excel;
use App\Exports\ChinaWorksheetExport;


class ChinaWorksheetController extends AdminController
{
    public function index(){
        $title = 'Work sheet';
        $china_worksheet_obj = ChinaWorksheet::all();       

        $arr_columns = parent::new_china_columns();
        
        return view('admin.china.china_worksheet', ['title' => $title,'china_worksheet_obj' => $china_worksheet_obj,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
    }


    public function showAdd()
	{
		$arr_columns = parent::new_china_columns();
		$title = 'Add row';
		return view('admin.china.china_worksheet_add', ['title' => $title,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
	}


	public function add(Request $request)
	{
		$china_worksheet = new ChinaWorksheet();
		$arr_columns = parent::new_china_columns();
		$fields = ['date', 'tracking_main', 'tracking_local', 'status', 'customer_name', 'customer_address', 'customer_phone', 'customer_email', 'supplier_name', 'supplier_address', 'supplier_phone', 'supplier_email', 'shipment_description', 'weight', 'length', 'width', 'height', 'lot_number', 'status_he', 'status_ru'];
		
		for ($i=0; $i < count($arr_columns); $i++) { 
			if ($arr_columns[$i]) {
				$fields[] = 'new_column_'.($i+1);
			}
		}

		foreach($fields as $field){
			$china_worksheet->$field = $request->input($field);
		}

		$china_worksheet->save();

		return redirect()->route('adminChinaWorksheet')->with('status', 'Row added successfully!');
	}


	public function show($id)
	{
		$arr_columns = parent::new_china_columns();
		$china_worksheet = ChinaWorksheet::find($id);
		$title = 'Update row '.$china_worksheet->id;

		return view('admin.china.china_worksheet_update', ['title' => $title,'china_worksheet' => $china_worksheet,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
	}


	public function update(Request $request, $id)
	{
		$china_worksheet = ChinaWorksheet::find($id);
		$arr_columns = parent::new_china_columns();
		$fields = ['date', 'tracking_main', 'tracking_local', 'status', 'customer_name', 'customer_address', 'customer_phone', 'customer_email', 'supplier_name', 'supplier_address', 'supplier_phone', 'supplier_email', 'shipment_description', 'weight', 'length', 'width', 'height', 'lot_number', 'status_he', 'status_ru'];

		for ($i=0; $i < count($arr_columns); $i++) { 
			if ($arr_columns[$i]) {
				$fields[] = 'new_column_'.($i+1);
			}
		}

		foreach($fields as $field){
			$china_worksheet->$field = $request->input($field);
		}
		
		$china_worksheet->save();
		return redirect()->route('adminChinaWorksheet')->with('status', 'Row updated successfully!');
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');

		DB::table('china_worksheet')
		->where('id', '=', $id)
		->delete();

		return redirect()->route('adminChinaWorksheet')->with('status', 'Row deleted successfully!');
	}


	public function addColumn()
	{
		$message = 'Column added successfully!';
		
		if (!Schema::hasColumn('china_worksheet', 'new_column_1'))
		{
			Schema::table('china_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_1')->nullable();
			});
		}
		else if (!Schema::hasColumn('china_worksheet', 'new_column_2'))
		{
			Schema::table('china_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_2')->nullable();
			});
		}
		else if (!Schema::hasColumn('china_worksheet', 'new_column_3'))
		{
			Schema::table('china_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_3')->nullable();
			});
		}
		else if (!Schema::hasColumn('china_worksheet', 'new_column_4'))
		{
			Schema::table('china_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_4')->nullable();
			});
		}
		else if (!Schema::hasColumn('china_worksheet', 'new_column_5'))
		{
			Schema::table('china_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_5')->nullable();
			});
		}
		else
		{
			$message = 'The quantity of columns is limited!';
		}

		return redirect()->route('adminChinaWorksheet')->with('status', $message);
	}


	public function deleteColumn(Request $request)
	{
		$name_column = $request->input('name_column');

		if ($name_column === 'new_column_1') {
			Schema::table('china_worksheet', function($table)
			{
				$table->dropColumn('new_column_1');
			});
		}
		elseif ($name_column === 'new_column_2') {
			Schema::table('china_worksheet', function($table)
			{
				$table->dropColumn('new_column_2');
			});
		}
		elseif ($name_column === 'new_column_3') {
			Schema::table('china_worksheet', function($table)
			{
				$table->dropColumn('new_column_3');
			});
		}
		elseif ($name_column === 'new_column_4') {
			Schema::table('china_worksheet', function($table)
			{
				$table->dropColumn('new_column_4');
			});
		}
		elseif ($name_column === 'new_column_5') {
			Schema::table('china_worksheet', function($table)
			{
				$table->dropColumn('new_column_5');
			});
		}

		return redirect()->route('adminChinaWorksheet')->with('status', 'Column deleted successfully!');
	}


	public function showChinaStatus(){
        $title = 'Changing statuses by lot number';
        $worksheet_obj = ChinaWorksheet::all();
        $number_arr = [];
        foreach ($worksheet_obj as $row) {
        	if (!in_array($row->lot_number, $number_arr)) {
        		$number_arr[$row->lot_number] = $row->lot_number;
        	}
        }
        return view('admin.china.china_worksheet_status_number', ['title' => $title,'number_arr' => $number_arr]);
    }


    public function changeChinaStatus(Request $request){
        if ($request->input('lot_number') && $request->input('status')) {
        	DB::table('china_worksheet')
        	->where('lot_number', $request->input('lot_number'))
          	->update([
          		'status' => $request->input('status'), 
          		'status_he' => $request->input('status_he'),
          		'status_ru' => $request->input('status_ru')
          	]);
        }
        return redirect()->route('adminChinaWorksheet');
    }


    public function showChinaStatusDate(){
        $title = 'Changing statuses by date';
        $worksheet_obj = ChinaWorksheet::all();
        $date_arr = [];
        foreach ($worksheet_obj as $row) {
        	if (!in_array($row->date, $date_arr)) {
        		$date_arr[$row->date] = $row->date;
        	}
        }
        return view('admin.china.china_worksheet_status_date', ['title' => $title,'date_arr' => $date_arr]);
    }


    public function changeChinaStatusDate(Request $request){
        if ($request->input('date') && $request->input('status')) {
        	DB::table('china_worksheet')
        	->where('date', $request->input('date'))
          	->update([
          		'status' => $request->input('status'), 
          		'status_he' => $request->input('status_he'),
          		'status_ru' => $request->input('status_ru')
          	]);
        }
        return redirect()->route('adminChinaWorksheet');
    }


    public function exportExcel()
	{

    	return Excel::download(new ChinaWorksheetExport, 'ChinaWorksheetExport.xlsx');

	}
}
