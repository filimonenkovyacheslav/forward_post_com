<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Worksheet;
use DB;
use Excel;
use App\Exports\WorksheetExport;

class WorksheetController extends AdminController
{
  public function index(){
    $title = 'Рабочий лист';
    $worksheet_obj = Worksheet::all();
    return view('admin.worksheet', ['title' => $title,'worksheet_obj' => $worksheet_obj]);
  }


  public function showStatus(){
    $title = 'Изменение статусов по номеру партии';
    $worksheet_obj = Worksheet::all();
    $number_arr = [];
    foreach ($worksheet_obj as $row) {
      if (!in_array($row->batch_number, $number_arr)) {
        $number_arr[$row->batch_number] = $row->batch_number;
      }
    }
    return view('admin.worksheet_status_number', ['title' => $title,'number_arr' => $number_arr]);
  }


  public function changeStatus(Request $request){
    if ($request->input('batch_number') && $request->input('status')) {
      DB::table('worksheet')
      ->where('batch_number', $request->input('batch_number'))
      ->update([
        'status' => $request->input('status'), 
        'guarantee_text_en' => $request->input('status_en'),
        'guarantee_text_ua' => $request->input('status_ua'),
        'guarantee_text_he' => $request->input('status_he')
      ]);
    }
    return redirect()->route('adminWorksheet');
  }


  public function showStatusDate(){
    $title = 'Изменение статусов по дате';
    $worksheet_obj = Worksheet::all();
    $date_arr = [];
    foreach ($worksheet_obj as $row) {
      if (!in_array($row->date, $date_arr)) {
        $date_arr[$row->date] = $row->date;
      }
    }
    return view('admin.worksheet_status_date', ['title' => $title,'date_arr' => $date_arr]);
  }


  public function changeStatusDate(Request $request){
    if ($request->input('date') && $request->input('status')) {
      DB::table('worksheet')
      ->where('date', $request->input('date'))
      ->update([
        'status' => $request->input('status'), 
        'guarantee_text_en' => $request->input('status_en'),
        'guarantee_text_ua' => $request->input('status_ua'),
        'guarantee_text_he' => $request->input('status_he')
      ]);
    }
    return redirect()->route('adminWorksheet');
  }


  public function show($id)
  {
    $worksheet = Worksheet::find($id);
    $title = 'Изменение строки '.$worksheet->id;

    return view('admin.worksheet_update', ['title' => $title,'worksheet' => $worksheet]);
  }


  public function update(Request $request, $id)
  {
    $worksheet = Worksheet::find($id);
    $fields = ['num_row','date', 'direction', 'status', 'local', 'tracking', 'manager_comments', 'comment', 'comments', 'sender', 'data_sender', 'recipient', 'data_recipient', 'email_recipient', 'parcel_cost', 'packaging', 'pays_parcel', 'number_weight', 'width', 'height', 'length', 'batch_number', 'shipment_type', 'parcel_description', 'position_1', 'position_2', 'position_3', 'position_4', 'position_5', 'position_6', 'position_7', 'guarantee_text_en', 'guarantee_text_ru', 'guarantee_text_he', 'guarantee_text_ua', 'payment', 'phys_weight', 'volume_weight', 'quantity', 'comments_2', 'cost_price', 'shipment_cost'];

    foreach($fields as $field){

      $worksheet->$field = $request->input($field);
    }

    $worksheet->save();
    return redirect()->route('adminWorksheet')->with('status', 'Строка успешно обновлена!');
  }


  public function destroy(Request $request)
  {
    $id = $request->input('action');

    DB::table('worksheet')
    ->where('id', '=', $id)
    ->delete();

    return redirect()->route('adminWorksheet')->with('status', 'Строка успешно удалена!');
  }


  public function exportExcel(Request $request)
  {
    
    return Excel::download(new WorksheetExport, 'WorksheetExport.xlsx');
  
  }
}
