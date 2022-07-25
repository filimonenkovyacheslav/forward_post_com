<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use Auth;


class UpdatesArchive extends Model
{   
    protected $table = 'updates_archive';
    protected $fillable = ['worksheet_id','eng_worksheet_id','draft_id', 'eng_draft_id', 'updates_date', 'user_name', 'column_name', 'old_data', 'new_data'];
    protected $column_arr = ['site_name','status','tracking_main','tracking_local','tracking_transit','pallet_number','comments_1','comments_2','comment_2','comments','sender_city','shipper_city','standard_phone','courier','pick_up_date','delivery_date_comments','lot','batch_number','pay_date','payment_date_comments','amount_payment','pay_sum'];

    
    /**
    * Get the worksheet record that owns the updates archive.
    */
    public function worksheet()
    {
        return $this->belongsTo('App\NewWorksheet','worksheet_id');
    }

    
    /**
    * Get the eng worksheet record that owns the updates archive.
    */
    public function worksheetEng()
    {
        return $this->belongsTo('App\PhilIndWorksheet','eng_worksheet_id');
    }


    /**
    * Get the draft record that owns the updates archive.
    */
    public function draft()
    {
        return $this->belongsTo('App\CourierDraftWorksheet','draft_id');
    }

    
    /**
    * Get the eng draft record that owns the updates archive.
    */
    public function draftEng()
    {
        return $this->belongsTo('App\CourierEngDraftWorksheet','eng_draft_id');
    }


    /**
    * Create updates archive.
    */
    public function createUpdatesArchive($request,$worksheet,$double,$double_create){
        $table = $worksheet->table;
        $id = $worksheet->id;
        $user = Auth::user();
        $column_name = [];
        $old_data = [];
        $new_data = [];

        if ($double) {
            $column_name[] = 'double';
            if ($double_create) {
                $old_data[] = 'Double created';
                $new_data[] = 'Double Id No. '.$double_create;
            }
            else{
                $old_data[] = 'Double updated';
                $new_data[] = 'Double updated';
            }
        }
        elseif ($request->input('value-by-tracking')) {            
            $column = $request->input('tracking-columns');
            if (!$column) $column = $request->input('phil-ind-tracking-columns');
            if (in_array($column, $this->column_arr)) {
                $column_name[] = $column;
                $old_data[] = $worksheet->$column;
                $new_data[] = $request->input('value-by-tracking');
            }
        }
        elseif ($request->input('value-by-pallet')) {  
            if ($worksheet->table === 'new_worksheet' || $worksheet->table === 'courier_draft_worksheet') 
                $column = 'batch_number';          
            else
                $column = 'lot';

            if (in_array($column, $this->column_arr)) {
                $column_name[] = $column;
                $old_data[] = $worksheet->$column;
                $new_data[] = $request->input('value-by-pallet');
            }
        }
        else{
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->site_name:true) && $request->site_name !== $worksheet->site_name) {
                $column_name[] = 'site_name';
                $old_data[] = $worksheet->site_name;
                $new_data[] = $request->site_name;
            } 
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->status:true) && $request->status !== $worksheet->status) {
                $column_name[] = 'status';
                $old_data[] = $worksheet->status;
                $new_data[] = $request->status;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->tracking_main:true) && $request->tracking_main !== $worksheet->tracking_main) {
                $column_name[] = 'tracking_main';
                $old_data[] = $worksheet->tracking_main;
                $new_data[] = $request->tracking_main;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->tracking_local:true) && $request->tracking_local !== $worksheet->tracking_local) {
                $column_name[] = 'tracking_local';
                $old_data[] = $worksheet->tracking_local;
                $new_data[] = $request->tracking_local;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->tracking_transit:true) && $request->tracking_transit !== $worksheet->tracking_transit) {
                $column_name[] = 'tracking_transit';
                $old_data[] = $worksheet->tracking_transit;
                $new_data[] = $request->tracking_transit;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->pallet_number:true) && $request->pallet_number !== $worksheet->pallet_number) {
                $column_name[] = 'pallet_number';
                $old_data[] = $worksheet->pallet_number;
                $new_data[] = $request->pallet_number;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->comments_1:true) && $request->comments_1 !== $worksheet->comments_1) {
                $column_name[] = 'Comments 1';
                $old_data[] = $worksheet->comments_1;
                $new_data[] = $request->comments_1;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->comments_2:true) && $request->comments_2 !== $worksheet->comments_2) {
                $column_name[] = 'Comments 2';
                $old_data[] = $worksheet->comments_2;
                $new_data[] = $request->comments_2;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->comment_2:true) && $request->comment_2 !== $worksheet->comment_2) {
                $column_name[] = 'Comments Off';
                $old_data[] = $worksheet->comment_2;
                $new_data[] = $request->comment_2;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->comments:true) && $request->comments !== $worksheet->comments) {
                $column_name[] = 'Comments Dir';
                $old_data[] = $worksheet->comments;
                $new_data[] = $request->comments;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->sender_city:true) && $request->sender_city !== $worksheet->sender_city) {
                $column_name[] = 'sender_city';
                $old_data[] = $worksheet->sender_city;
                $new_data[] = $request->sender_city;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->shipper_city:true) && $request->shipper_city !== $worksheet->shipper_city) {
                $column_name[] = 'shipper_city';
                $old_data[] = $worksheet->shipper_city;
                $new_data[] = $request->shipper_city;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->standard_phone:true) && $request->standard_phone !== $worksheet->standard_phone) {
                $column_name[] = 'standard_phone';
                $old_data[] = $worksheet->standard_phone;
                $new_data[] = $request->standard_phone;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->courier:true) && $request->courier !== $worksheet->courier) {
                $column_name[] = 'courier';
                $old_data[] = $worksheet->courier;
                $new_data[] = $request->courier;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->pick_up_date:true) && $request->pick_up_date !== $worksheet->pick_up_date) {
                $column_name[] = 'pick_up_date';
                $old_data[] = $worksheet->pick_up_date;
                $new_data[] = $request->pick_up_date;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->delivery_date_comments:true) && $request->delivery_date_comments !== $worksheet->delivery_date_comments) {
                $column_name[] = 'delivery_date_comments';
                $old_data[] = $worksheet->delivery_date_comments;
                $new_data[] = $request->delivery_date_comments;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->lot:true) && $request->lot !== $worksheet->lot) {
                $column_name[] = 'lot';
                $old_data[] = $worksheet->lot;
                $new_data[] = $request->lot;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->batch_number:true) && $request->batch_number !== $worksheet->batch_number) {
                $column_name[] = 'batch_number';
                $old_data[] = $worksheet->batch_number;
                $new_data[] = $request->batch_number;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->pay_date:true) && $request->pay_date !== $worksheet->pay_date) {
                $column_name[] = 'pay_date';
                $old_data[] = $worksheet->pay_date;
                $new_data[] = $request->pay_date;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->payment_date_comments:true) && $request->payment_date_comments !== $worksheet->payment_date_comments) {
                $column_name[] = 'payment_date_comments';
                $old_data[] = $worksheet->payment_date_comments;
                $new_data[] = $request->payment_date_comments;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->amount_payment:true) && $request->amount_payment !== $worksheet->amount_payment) {
                $column_name[] = 'amount_payment';
                $old_data[] = $worksheet->amount_payment;
                $new_data[] = $request->amount_payment;
            }
            if ((($request->row_id || $request->tracking || $request->by_lot)?$request->pay_sum:true) && $request->pay_sum !== $worksheet->pay_sum) {
                $column_name[] = 'pay_sum';
                $old_data[] = $worksheet->pay_sum;
                $new_data[] = $request->pay_sum;
            }
        }                
        
        if (count($column_name)) {
            for ($i=0; $i < count($column_name); $i++) { 
                $archive = new UpdatesArchive();
                $archive->column_name = $column_name[$i];
                $archive->old_data = $old_data[$i];
                $archive->new_data = $new_data[$i];
                $archive->user_name = $user->name;
                $archive->updates_date = date('Y-m-d');

                switch($table) {
                    case 'new_worksheet';
                    $archive->worksheet_id = $id;
                    break;
                    case 'phil_ind_worksheet';
                    $archive->eng_worksheet_id = $id;
                    break;
                    case 'courier_draft_worksheet';
                    $archive->draft_id = $id;
                    break;
                    case 'courier_eng_draft_worksheet';
                    $archive->eng_draft_id = $id;
                    break;       
                    default:
                    break;
                }
                $archive->save();
            } 
        }
        return true;
    }


    public function signedDocumentToUpdatesArchive($worksheet,$user_name = '',$uniq_id = '',$old_uniq_id = '')
    {
        $table = $worksheet->table;
        $id = $worksheet->id;
        $user = Auth::user();
        if ($user) {
            $this->column_name = 'PDF';
            $this->old_data = $old_uniq_id;
            $this->new_data = $uniq_id;
            $this->user_name = $user->name;
            $this->updates_date = date('Y-m-d');
            switch($table) {
                case 'new_worksheet';
                $this->worksheet_id = $id;
                break;
                case 'phil_ind_worksheet';
                $this->eng_worksheet_id = $id;
                break;
                case 'courier_draft_worksheet';
                $this->draft_id = $id;
                break;
                case 'courier_eng_draft_worksheet';
                $this->eng_draft_id = $id;
                break;       
                default:
                break;
            }
            $this->save();
            return true;
        }
        elseif($user_name){
            $this->column_name = 'PDF';
            $this->old_data = $old_uniq_id;
            $this->new_data = $uniq_id;
            $this->user_name = $user_name;
            $this->updates_date = date('Y-m-d');
            switch($table) {
                case 'new_worksheet';
                $this->worksheet_id = $id;
                break;
                case 'phil_ind_worksheet';
                $this->eng_worksheet_id = $id;
                break;
                case 'courier_draft_worksheet';
                $this->draft_id = $id;
                break;
                case 'courier_eng_draft_worksheet';
                $this->eng_draft_id = $id;
                break;       
                default:
                break;
            }
            $this->save();
            return true;
        }
        else return false;
    }


    public function deletedToArchive($worksheet)
    {
        if ($worksheet->table) {
            $table = $worksheet->table;
            $id = $worksheet->id;
            $user = Auth::user();
            $this->column_name = 'Deleted';
            $this->user_name = $user->name;
            $this->updates_date = date('Y-m-d');
            switch($table) {
                case 'new_worksheet';
                $this->old_data = 'ru worksheet id No. '.$id;
                break;
                case 'phil_ind_worksheet';
                $this->old_data = 'eng worksheet id No. '.$id;
                break;
                case 'courier_draft_worksheet';
                $this->old_data = 'draft id No. '.$id;
                break;
                case 'courier_eng_draft_worksheet';
                $this->old_data = 'eng draft id No. '.$id;
                break;       
                default:
                break;
            }
            $this->save();
            return true;
        }
        else
            return false;
    }

}
