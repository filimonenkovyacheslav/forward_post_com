<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public static $status_arr = ["Коробка", "Забрать"];
    public static $status_arr_eng = ["Box", "Pick up"];
    

    public static function israelCities()
    {
        return $israel_cities = ['Acre' => 'Nahariya','Afula' => 'Kiryat Shmona','Arad' => 'Eilat','Ariel' => 'Center','Ashdod' => 'South','Ashkelon' => 'South','Baqa-Jatt' => 'Haifa','Bat Yam' => 'Tel Aviv','Beersheba' => 'South','Beit She\'an' => 'Kiryat Shmona','Beit Shemesh' => 'Jerusalem','Beitar Illit' => 'Jerusalem','Binyamina' => 'North','Bnei Brak' => 'Tel Aviv','Caesaria' => 'North','Dimona' => 'Eilat','Eilat' => 'Eilat','El\'ad' => 'Center','Giv\'atayim' => 'Tel Aviv','Giv\'at Shmuel' => 'Center','Hadera' => 'Haifa','Haifa' => 'Haifa','Herzliya' => 'Tel Aviv','Hod HaSharon' => 'Center','Holon' => 'Tel Aviv','Jerusalem' => 'Jerusalem','Karmiel' => 'Nahariya','Kafr Qasim' => 'Center','Kfar Saba' => 'Center','Kiryat Ata' => 'Haifa','Kiryat Bialik' => 'Haifa','Kiryat Gat' => 'South','Kiryat Malakhi' => 'South','Kiryat Motzkin' => 'Haifa','Kiryat Ono' => 'Tel Aviv','Kiryat Shmona' => 'Kiryat Shmona','Kiryat Yam' => 'Haifa','Lod' => 'Center','Ma\'ale Adumim' => 'Jerusalem','Ma\'alot-Tarshiha' => 'Nahariya','Migdal HaEmek' => 'Nahariya','Modi\'in Illit' => 'Center','Modi\'in-Maccabim-Re\'ut' => 'Center','Nahariya' => 'Nahariya','Nazareth' => 'Nahariya','Nazareth Illit' => 'Nahariya','Nesher' => 'Haifa','Ness Ziona' => 'Center','Netanya' => 'Center','Netivot' => 'South','Ofakim' => 'South','Or Akiva' => 'Haifa','Or Yehuda' => 'Tel Aviv','Pardes Hana' => 'North','Petah Tikva' => 'Center','Qalansawe' => 'Center','Ra\'anana' => 'Center','Rahat' => 'South','Ramat Gan' => 'Tel Aviv','Ramat HaSharon' => 'Tel Aviv','Ramla' => 'Center','Rehovot' => 'Center','Rishon LeZion' => 'Center','Rosh HaAyin' => 'Center','Safed' => 'Kiryat Shmona','Sakhnin' => 'Nahariya','Sderot' => 'South','Shefa-\'Amr (Shfar\'am)' => 'Haifa','Tamra' => 'Haifa','Tayibe' => 'Center','Tel Aviv' => 'Tel Aviv','Tiberias' => 'Kiryat Shmona','Tira' => 'Center','Tirat Carmel' => 'Haifa','Umm al-Fahm' => 'Haifa','Yavne' => 'Center','Yehud-Monosson' => 'Center','Yokneam' => 'Haifa','Zikhron Yakov' => 'North'];
    }
    

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


    protected function translit($s) {
        $s = (string) $s; // преобразуем в строковое значение
        $s = strip_tags($s); // убираем HTML-теги
        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
        $s = trim($s); // убираем пробелы в начале и конце строки
        //$s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        
        $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>'','А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'E','Ж'=>'J','З'=>'Z','И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'C','Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Shch','Ы'=>'Y','Э'=>'E','Ю'=>'Yu','Я'=>'Ya','Ь'=>'','Ъ'=>''));
        
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
        //$s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
        
        return $s; // возвращаем результат
    }


    private static function createQuery($query,$column,$search,$active,$model,$ru_models)
    {
        if($search){
            if ($active) {                
                $query->where([
                    [$column, 'like', '%'.$search.'%'],
                    ['tracking_main','<>',null],
                    ['in_trash',false]
                ]);

                if (in_array($model, $ru_models)) {
                    $query->orWhere([
                        [$column, 'like', '%'.$search.'%'],
                        ['status','Забрать'],
                        ['in_trash',false]
                    ])
                    ->orderBy('index_number');
                }
                else{
                    $query->orWhere([
                        [$column, 'like', '%'.$search.'%'],
                        ['status','Pick up'],
                        ['in_trash',false]
                    ]);
                }
            }
            else{
                $query->where([
                    [$column, 'like', '%'.$search.'%'],
                    ['in_trash',false]
                ]);

                if (in_array($model, $ru_models)) {
                    $query->orderBy('index_number');
                }
            } 
        }
        return $query;
    }


    public static function searchByMultipleParameters($model, $request)
    {     
        $attributes = $model::first()->attributesToArray();
        $search = $request->table_filter_value;
        $column = $request->table_columns;
        $active = $request->for_active;       
        $ru_models = ['CourierDraftWorksheet','NewWorksheet'];
        
        if (!in_array(null, $column)) {     
            if (in_array(null, $search)) return null;       
            $query = static::query();            
            for ($i=0; $i < count($column); $i++) {  
                $query = static::createQuery($query,$column[$i],$search[$i],$active,$model,$ru_models);
            } 
            return $query->paginate(10);  
        }
        else{
            if (in_array(null, $search)) return null;
            $arr = [];
            $id_arr = [];            

            for ($i=0; $i < count($column); $i++) { 
                                
                if ($column[$i]) {
                    $query = static::query();
                    $query = static::createQuery($query,$column[$i],$search[$i],$active,$model,$ru_models);

                    $temp_arr = $query->get();

                    if ($temp_arr->first()){
                        if (!$id_arr) {
                            $arr[] = $temp_arr;
                            $id_arr = $temp_arr->pluck('id')->toArray();                           
                        }
                        else{
                            $arr = [];
                            $new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
                                if (in_array($item->id, $id_arr)) {                               
                                    return $item;                       
                                }                                                   
                            });                     
                            $id_arr = $new_arr->pluck('id')->toArray();
                            $arr[] = $new_arr;
                        }                          
                    }
                }
                else{
                    $this_arr = [];
                    $this_id_arr = [];
                    
                    foreach($attributes as $key => $value) {
                        if ($key !== 'created_at' && $key !== 'updated_at' && $key !== 'update_status_date') {

                            $query = static::query();
                            $query = static::createQuery($query,$key,$search[$i],$active,$model,$ru_models);
                            $temp_arr = $query->get();
                                                        
                            if ($temp_arr->first()){
                                $new_arr = $temp_arr->filter(function ($item, $k) use($this_id_arr) {
                                    if (!in_array($item->id, $this_id_arr)) { 
                                        $this_id_arr[] = $item->id;                      
                                        return $item;                       
                                    }  
                                }); 
                                $this_arr[] = $new_arr;
                                $temp = $new_arr->pluck('id')->toArray();
                                $this_id_arr = array_unique(array_merge($this_id_arr, $temp));
                            }                                
                        }               
                    }

                    if ($this_id_arr){
                        if (!$id_arr) {
                            $arr = $this_arr;
                            $id_arr = $this_id_arr;
                        }
                        else{
                            $arr = [];
                            for ($j=0; $j < count($this_arr); $j++) { 
                                $new_arr = $this_arr[$j]->filter(function ($item, $k) use($id_arr) {
                                    if (in_array($item->id, $id_arr))  
                                        return $item;               
                                });                                                               
                                $arr[] = $new_arr; 
                                $id_arr = $new_arr->pluck('id')->toArray();
                            }                                                  
                        }                                                  
                    } 
                }                
            }
            
            return $arr;
        }
    }
}
