<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public static $status_arr = ["Коробка", "Забрать"];
    public static $status_arr_eng = ["Box", "Pick up"];
    

    /**
    * Check the courier task.
    */
    public static function isNecessaryCourierTask($status,$table)
    {
        switch($table) {
            case 'new_worksheet';
                $result = (in_array($status, self::$status_arr)) ? true : false;
                break;
            case 'phil_ind_worksheet';
                $result = (in_array($status, self::$status_arr_eng)) ? true : false;
                break;
            case 'courier_draft_worksheet';
                $result = (in_array($status, self::$status_arr)) ? true : false;
                break;
            case 'courier_eng_draft_worksheet';
                $result = (in_array($status, self::$status_arr_eng)) ? true : false;
                break;       
            default:
                $result = false;
                break;
        }        

        return $result;
    }
}
