<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use App\NewPacking;
use App\PackingSea;
use App\Invoice;
use App\Manifest;
use App\PackingEng;
use \Dejurin\GoogleTranslateForFree;
use App\Receipt;
use App\ReceiptArchive;
use App\Warehouse;
use App\UpdatesArchive;
use DB;
use Auth;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
    protected $from_country_dir = ['Israel' => 'IL','Germany' => 'GE'];
    protected $to_country_dir = ['India' => 'IND','Nepal' => 'NEP','Nigeria' => 'NIG','Ghana' => 'GHN','Cote D\'Ivoire' => 'CTD','South Africa' => 'SAR','Thailand' => 'TH'];
    protected $israel_cities = ['Acre' => 'North','Afula' => 'North','Arad' => 'Eilat','Ariel' => 'Center','Ashdod' => 'South','Ashkelon' => 'South','Baqa-Jatt' => 'Haifa','Bat Yam' => 'Tel Aviv','Beersheba' => 'South','Beit She\'an' => 'North','Beit Shemesh' => 'Jerusalem','Beitar Illit' => 'Jerusalem','Bnei Brak' => 'Tel Aviv','Dimona' => 'Eilat','Eilat' => 'Eilat','El\'ad' => 'Center','Giv\'atayim' => 'Tel Aviv','Giv\'at Shmuel' => 'Center','Hadera' => 'Haifa','Haifa' => 'Haifa','Herzliya' => 'Tel Aviv','Hod HaSharon' => 'Center','Holon' => 'Tel Aviv','Jerusalem' => 'Jerusalem','Karmiel' => 'North','Kafr Qasim' => 'Center','Kfar Saba' => 'Center','Kiryat Ata' => 'Haifa','Kiryat Bialik' => 'Haifa','Kiryat Gat' => 'South','Kiryat Malakhi' => 'South','Kiryat Motzkin' => 'Haifa','Kiryat Ono' => 'Tel Aviv','Kiryat Shmona' => 'North','Kiryat Yam' => 'Haifa','Lod' => 'Center','Ma\'ale Adumim' => 'Jerusalem','Ma\'alot-Tarshiha' => 'North','Migdal HaEmek' => 'North','Modi\'in Illit' => 'Center','Modi\'in-Maccabim-Re\'ut' => 'Center','Nahariya' => 'North','Nazareth' => 'North','Nazareth Illit' => 'North','Nesher' => 'Haifa','Ness Ziona' => 'Center','Netanya' => 'Center','Netivot' => 'South','Ofakim' => 'South','Or Akiva' => 'Haifa','Or Yehuda' => 'Tel Aviv','Petah Tikva' => 'Center','Qalansawe' => 'Center','Ra\'anana' => 'Center','Rahat' => 'South','Ramat Gan' => 'Tel Aviv','Ramat HaSharon' => 'Tel Aviv','Ramla' => 'Center','Rehovot' => 'Center','Rishon LeZion' => 'Center','Rosh HaAyin' => 'Center','Safed' => 'North','Sakhnin' => 'North','Sderot' => 'South','Shefa-\'Amr (Shfar\'am)' => 'North','Tamra' => 'North','Tayibe' => 'Center','Tel Aviv' => 'Tel Aviv','Tiberias' => 'North','Tira' => 'Center','Tirat Carmel' => 'Haifa','Umm al-Fahm' => 'Haifa','Yavne' => 'Center','Yehud-Monosson' => 'Center','Yokneam' => 'North'];


    protected function toUpdatesArchive($request,$worksheet,$double = false,$double_create = 0)
    {
        $archive = new UpdatesArchive();
        $archive->createUpdatesArchive($request,$worksheet,$double,$double_create);
        return true;
    }
    

    protected function israelCities()
    {
        $israel_cities = array_keys($this->israel_cities);
        $temp = [];
        for ($i=0; $i < count($israel_cities); $i++) { 
            $temp[$israel_cities[$i]] = $israel_cities[$i];
        }
        $israel_cities = $temp;
        return $israel_cities; 
    }

    
    protected function createDirection($from, $to)
    {
        $from = ($from) ? $this->from_country_dir[$from] : '';
        $to = ($to) ? $this->to_country_dir[$to] : '';
        return $from.'-'.$to;        
    }

    
    protected function trackingValidate($tracking)
    {
        $pattern = '/^[a-z0-9]+$/i';
        $tracking = str_replace("-", "", $tracking);
        if (preg_match($pattern, $tracking) && (strlen($tracking) >= 4 && strlen($tracking) <= 18)) {
            return true;
        } else {
            return false;
        }
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


    protected function checkConsigneePhone($phone, $which_admin)
    {
        $strlen = mb_strlen($phone,'UTF-8');
        $str = substr($phone, 1);
        $message = '';
        
        if ($strlen < 6 || $strlen > 24) {
            if ($which_admin === 'ru') {
                return $message = 'Кол-во знаков в телефоне получателя должно быть от 6 до 24!';
            }
            elseif ($which_admin === 'en') {
                return $message = 'The number of characters in the consignee phone must be from 6 to 24!';
            }
        }
        elseif ($phone[0] !== '+'){
            if ($which_admin === 'ru') {
                return $message = 'Телефон получателя должен начинаться с "+"!';
            }
            elseif ($which_admin === 'en') {
                return $message = 'The consignee phone must start with "+"!';
            }
        }
        elseif (!ctype_digit($str)) {
            if ($which_admin === 'ru') {
                return $message = 'Телефон получателя должен содержать только цифры!';
            }
            elseif ($which_admin === 'en') {
                return $message = 'The consignee phone must contain only numbers!';
            }
        }
        else return $message;
    }


    protected function getTableColumns($table)
    {       
        return Schema::getColumnListing($table);
    }
        

    protected function checkReceipt($id, $receipt_id, $which_admin, $tracking_main, $receipt_number = null, $old_tracking = null)
    {
        $message = '';
        $receipt = Receipt::where('tracking_main',$tracking_main)->first();
        $update_date = Date('Y-m-d', strtotime('+4 days'));

        if ($receipt_id == null) {
            if (!$receipt) {
                if ($which_admin === 'ru') {
                    if ($old_tracking) {
                        ReceiptArchive::where([
                            ['tracking_main',$old_tracking],
                            ['worksheet_id',$id],
                            ['which_admin','ru']
                        ])->delete();
                    }
                    $message = 'ВНИМАНИЕ! В ТАБЛИЦЕ «КВИТАНЦИИ» ОСТУСТСТВУЕТ ТРЕКИНГ НОМЕР ...('.$tracking_main.')!';
                    $archive = [
                        'worksheet_id' => $id,
                        'tracking_main' => $tracking_main,
                        'which_admin' => 'ru',
                        'update_date' => $update_date,
                        'status' => false,
                        'description' => $message
                    ];
                    ReceiptArchive::create($archive);
                }
                else if ($which_admin === 'en') {
                    if ($old_tracking) {
                        ReceiptArchive::where([
                            ['tracking_main',$old_tracking],
                            ['worksheet_id',$id],
                            ['which_admin','en']
                        ])->delete();
                    }
                    $message = 'WARNING! DETECTED TRACKING NUMBERS MISSING IN THE RECEIPTS SHEET ...('.$tracking_main.')!';
                    $archive = [
                        'worksheet_id' => $id,
                        'tracking_main' => $tracking_main,
                        'which_admin' => 'en',
                        'update_date' => $update_date,
                        'status' => false,
                        'description' => $message
                    ];
                    ReceiptArchive::create($archive);
                }
            }
            else {
                ReceiptArchive::where('tracking_main', $tracking_main)->delete();
            }
        } 
        elseif ($id == null) {
            if ($which_admin === 'en') {
                $message = 'WARNING! DETECTED TRACKING NUMBERS MISSING IN THE WORK SHEET ...('.$tracking_main.')!';
                $archive = [
                    'receipt_id' => $receipt_id,
                    'receipt_number' => $receipt_number,
                    'tracking_main' => $tracking_main,
                    'which_admin' => 'en',
                    'description' => $message
                ];
                ReceiptArchive::create($archive);
            }
            if ($which_admin === 'ru') {
                $message = 'ВНИМАНИЕ! В РАБОЧЕМ ЛИСТЕ ОСТУСТСТВУЕТ ТРЕКИНГ НОМЕР ...('.$tracking_main.')!';
                $archive = [
                    'receipt_id' => $receipt_id,
                    'receipt_number' => $receipt_number,
                    'tracking_main' => $tracking_main,
                    'which_admin' => 'ru',
                    'description' => $message
                ];
                ReceiptArchive::create($archive);
            }
        }      
        
        return $message;
    }


    protected function checkWhichAdmin($tracking)
    {
        if (stripos($tracking, 'CD') !== false || stripos($tracking, 'BL') !== false){
            return 'ru';
        }
        else return 'en';
    }


    protected function updateWarehouse($old_pallet, $new_pallet, $old_tracking, $new_tracking = null)
    {
        $track_arr = [];
        $tracking_main = ($new_tracking)?$new_tracking:$old_tracking;
        
        if ($new_pallet) {
            
            $result = Warehouse::where('pallet', $new_pallet)->first();  

            $which_admin = $this->checkWhichAdmin($tracking_main);
            if (!$which_admin) return false;      

            // Adding tracking to pallet
            if ($result) {
                if ($which_admin !== $result->which_admin) {
                    return false;
                }
                $track_arr = json_decode($result->tracking_numbers);
                if (!in_array($tracking_main, $track_arr)) $track_arr[] = $tracking_main;            
                $track_arr = json_encode($track_arr);
                $result->tracking_numbers = $track_arr;
                $result->save();
            }
            else{
                $track_arr[] = $tracking_main;
                $track_arr = json_encode($track_arr);
                Warehouse::create([
                    'pallet' => $new_pallet,
                    'tracking_numbers' => $track_arr,
                    'which_admin' => $which_admin
                ]);
            }

            // Removing tracking from old pallet
            if ($old_pallet && $old_pallet !== $new_pallet) {
                $this->removeTrackingFromPallet($old_pallet, $tracking_main);                 
            }

            // Removing old tracking from pallet
            if ($old_pallet && $old_tracking && $new_tracking) {
                $this->removeTrackingFromPallet($old_pallet, $old_tracking);                  
            }
        }
        elseif ($old_pallet && !$new_pallet){           
            // Removing tracking from old pallet
            $this->removeTrackingFromPallet($old_pallet, $tracking_main);
        } 

        return true;              
    }


    private function removeTrackingFromPallet($pallet, $tracking)
    {
        $result = Warehouse::where('pallet', $pallet)->first();
        
        if ($result) {
            $track_arr = json_decode($result->tracking_numbers);                
            while (($i = array_search($tracking, $track_arr)) !== false) {
                unset($track_arr[$i]);
            }
            $temp = [];
            foreach ($track_arr as $key => $value) {
                $temp[] = $value;
            }
            $track_arr = $temp;
            if (!$track_arr) $result->delete();
            else{
                $new_arr = json_encode($track_arr);
                $result->tracking_numbers = $new_arr;
                $result->save();
            }                              
        }

        return true;
    }


    protected function updateWarehouseWorksheet($pallet, $tracking, $new_pallet = false, $courier = false)
    {
        $lot = '';        
       
        if ($this->checkWhichAdmin($tracking) === 'ru'){
            if (!$courier) {
                $result = NewWorksheet::where('tracking_main', $tracking)->first();
                if ($result) $lot = $result->batch_number;             

                if (!$new_pallet) {
                    $this->updateNotificationsRu($pallet, $lot, $tracking);
                    NewWorksheet::where('tracking_main', $tracking)
                    ->update([
                        'pallet_number' => null
                    ]);
                }
                else{               
                    NewWorksheet::where('tracking_main', $tracking)
                    ->update([
                        'pallet_number' => $new_pallet
                    ]);
                    $this->updateWarehouseLot($tracking, $lot, 'ru', $pallet, $lot);
                }
            } 
            else{
                $result = CourierDraftWorksheet::where('tracking_main', $tracking)->first();
                if ($result) $lot = $result->batch_number;             

                if (!$new_pallet) {
                    $this->updateNotificationsRu($pallet, $lot, $tracking);
                    CourierDraftWorksheet::where('tracking_main', $tracking)
                    ->update([
                        'pallet_number' => null
                    ]);
                }
                else{               
                    CourierDraftWorksheet::where('tracking_main', $tracking)
                    ->update([
                        'pallet_number' => $new_pallet
                    ]);
                    $this->updateWarehouseLot($tracking, $lot, 'ru', $pallet, $lot);
                }
            }                      
        }
        else if ($this->checkWhichAdmin($tracking) === 'en'){
            if (!$courier) {
                $result = PhilIndWorksheet::where('tracking_main', $tracking)->first();
                if ($result) $lot = $result->lot;             

                if (!$new_pallet) {
                    $this->updateNotificationsEn($pallet, $lot, $tracking);
                    PhilIndWorksheet::where('tracking_main', $tracking)
                    ->update([
                        'pallet_number' => null
                    ]);
                }
                else{               
                    PhilIndWorksheet::where('tracking_main', $tracking)
                    ->update([
                        'pallet_number' => $new_pallet
                    ]);
                    $this->updateWarehouseLot($tracking, $lot, 'en', $pallet, $lot);
                }
            }
            else{
                $result = CourierEngDraftWorksheet::where('tracking_main', $tracking)->first();
                if ($result) $lot = $result->lot;             

                if (!$new_pallet) {
                    $this->updateNotificationsEn($pallet, $lot, $tracking);
                    CourierEngDraftWorksheet::where('tracking_main', $tracking)
                    ->update([
                        'pallet_number' => null
                    ]);
                }
                else{               
                    CourierEngDraftWorksheet::where('tracking_main', $tracking)
                    ->update([
                        'pallet_number' => $new_pallet
                    ]);
                    $this->updateWarehouseLot($tracking, $lot, 'en', $pallet, $lot);
                }
            }            
        }

        $this->removeTrackingFromPallet($pallet, $tracking);

        return true;
    }


    private function checkMaxLotRu($pallet, $lot)
    {
        $lots = NewWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['batch_number',$lot]
        ])->get();
        $other_lots = NewWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['batch_number','<>',null],
            ['batch_number','<>',$lot]
        ])->get();
        $empty_lots = NewWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['batch_number',null]
        ])->get();

        $c_lots = CourierDraftWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['batch_number',$lot]
        ])->get();
        $c_other_lots = CourierDraftWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['batch_number','<>',null],
            ['batch_number','<>',$lot]
        ])->get();
        $c_empty_lots = CourierDraftWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['batch_number',null]
        ])->get();

        if ($lots && $c_lots) {
            $lots = $lots->merge($c_lots);
        }
        elseif (!$lots && $c_lots) {
            $lots = $c_lots;
        }
        
        if ($other_lots && $c_other_lots) {
            $other_lots = $other_lots->merge($c_other_lots);
        }
        elseif (!$other_lots && $c_other_lots) {
            $other_lots = $c_other_lots;
        }
        
        if ($empty_lots && $c_empty_lots) {
            $empty_lots = $empty_lots->merge($c_empty_lots);
        }
        elseif (!$empty_lots && $c_empty_lots) {
            $empty_lots = $c_empty_lots;
        }                       

        // Check of batch number
        $batch_number = $other_lots->pluck('batch_number')->toArray();
        if ($batch_number) {
            $batch_number = array_count_values($batch_number);            
            $batch_number = array_keys($batch_number, max($batch_number))[0];
            
            $max_other_lots = NewWorksheet::where('in_trash',false)->where([
                ['pallet_number',$pallet],
                ['batch_number',$batch_number]
            ])->get();
            $c_max_other_lots = CourierDraftWorksheet::where('in_trash',false)->where([
                ['pallet_number',$pallet],
                ['batch_number',$batch_number]
            ])->get();
            if ($max_other_lots && $c_max_other_lots) {
                $max_other_lots = $max_other_lots->merge($c_max_other_lots);
            }
            elseif (!$max_other_lots && $c_max_other_lots) {
                $max_other_lots = $c_max_other_lots;
            }           

            if ($max_other_lots->count() > $lots->count()) {
                $lots = $max_other_lots;
                $lot = $batch_number;
                $other_lots = NewWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$pallet],
                    ['batch_number','<>',null],
                    ['batch_number','<>',$batch_number]
                ])->get();
                $c_other_lots = CourierDraftWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$pallet],
                    ['batch_number','<>',null],
                    ['batch_number','<>',$batch_number]
                ])->get();
                if ($other_lots && $c_other_lots) {
                    $other_lots = $other_lots->merge($c_other_lots);
                }
                elseif (!$other_lots && $c_other_lots) {
                    $other_lots = $c_other_lots;
                }                
            }
        }

        return [$lots, $other_lots, $empty_lots, $lot];
    }


    private function checkMaxLotEn($pallet, $lot)
    {
        $lots = PhilIndWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['lot',$lot]
        ])->get();
        $other_lots = PhilIndWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['lot','<>',null],
            ['lot','<>',$lot]
        ])->get();
        $empty_lots = PhilIndWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['lot',null]
        ])->get();
        $c_lots = CourierEngDraftWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['lot',$lot]
        ])->get();
        $c_other_lots = CourierEngDraftWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['lot','<>',null],
            ['lot','<>',$lot]
        ])->get();
        $c_empty_lots = CourierEngDraftWorksheet::where('in_trash',false)->where([
            ['pallet_number',$pallet],
            ['lot',null]
        ])->get();

        if ($lots && $c_lots) {
            $lots = $lots->merge($c_lots);
        }
        elseif (!$lots && $c_lots) {
            $lots = $c_lots;
        }
        
        if ($other_lots && $c_other_lots) {
            $other_lots = $other_lots->merge($c_other_lots);
        }
        elseif (!$other_lots && $c_other_lots) {
            $other_lots = $c_other_lots;
        }
        
        if ($empty_lots && $c_empty_lots) {
            $empty_lots = $empty_lots->merge($c_empty_lots);
        }
        elseif (!$empty_lots && $c_empty_lots) {
            $empty_lots = $c_empty_lots;
        } 

        // Check of batch number
        $batch_number = $other_lots->pluck('lot')->toArray();
        if ($batch_number) {
            $batch_number = array_count_values($batch_number);            
            $batch_number = array_keys($batch_number, max($batch_number))[0];
            $max_other_lots = PhilIndWorksheet::where('in_trash',false)->where([
                ['pallet_number',$pallet],
                ['lot',$batch_number]
            ])->get();
            $c_max_other_lots = CourierEngDraftWorksheet::where('in_trash',false)->where([
                ['pallet_number',$pallet],
                ['lot',$batch_number]
            ])->get();

            if ($max_other_lots && $c_max_other_lots) {
                $max_other_lots = $max_other_lots->merge($c_max_other_lots);
            }
            elseif (!$max_other_lots && $c_max_other_lots) {
                $max_other_lots = $c_max_other_lots;
            }
            
            if ($max_other_lots->count() > $lots->count()) {
                $lots = $max_other_lots;
                $lot = $batch_number;
                $other_lots = PhilIndWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$pallet],
                    ['lot','<>',null],
                    ['lot','<>',$batch_number]
                ])->get();
                $c_other_lots = CourierEngDraftWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$pallet],
                    ['lot','<>',null],
                    ['lot','<>',$batch_number]
                ])->get();

                if ($other_lots && $c_other_lots) {
                    $other_lots = $other_lots->merge($c_other_lots);
                }
                elseif (!$other_lots && $c_other_lots) {
                    $other_lots = $c_other_lots;
                }
            }
        }

        return [$lots, $other_lots, $empty_lots, $lot];
    }


    protected function updateNotificationsRu($old_pallet, $old_lot, $tracking = false)
    {
        $notifications = (object)['pallet'=>'','tracking'=>''];
        
        $warehouse = Warehouse::where('pallet',$old_pallet)->first();
        if ($warehouse) {
            if($warehouse->notifications){
                $notifications = json_decode($warehouse->notifications);
            }
        }

        if ($old_lot) {
            // For other lots
            $other_lots = $this->checkMaxLotRu($old_pallet, $old_lot)[1];                
            if ($other_lots) $temp_arr = $other_lots->pluck('tracking_main')->toArray();

            if ($temp_arr) {
                if ($tracking) {
                    while (($i = array_search($tracking, $temp_arr)) !== false) {
                        unset($temp_arr[$i]);
                    }
                    $temp = [];
                    foreach ($temp_arr as $key => $value) {
                        $temp[] = $value;
                    }
                    $temp_arr = $temp;
                }

                if ($temp_arr) {
                    $other_track_arr = implode(",", $temp_arr);
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->other_arr = 'CHANGE OR DELETE PALLET NO. FOR THE PARCELS ('.$other_track_arr.') AND TRY AGAIN';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);                  
                }
                else{
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->other_arr = '';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);
                }
            } 
            elseif ($warehouse) {
                $notifications_pallet = json_decode($notifications->pallet);
                if (is_object($notifications_pallet)) {
                    $notifications_pallet->other_arr = '';
                }
                else{
                    $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                }
                $notifications->pallet = json_encode($notifications_pallet);
            } 
        }
        else{
            // For empty lots
            $empty_lots = NewWorksheet::where('in_trash',false)->where([
                ['pallet_number',$old_pallet],
                ['batch_number',null]
            ])->get();  
            $c_empty_lots = CourierDraftWorksheet::where('in_trash',false)->where([
                ['pallet_number',$old_pallet],
                ['batch_number',null]
            ])->get(); 
            if ($empty_lots && $c_empty_lots) {
                $empty_lots = $empty_lots->merge($c_empty_lots);
            }
            elseif (!$empty_lots && $c_empty_lots) {
                $empty_lots = $c_empty_lots;
            }
                         
            if ($empty_lots) $temp_arr = $empty_lots->pluck('tracking_main')->toArray();

            $first_lot = NewWorksheet::where('in_trash',false)->where([
                ['pallet_number',$old_pallet],
                ['batch_number', '<>',null]
            ])->first();  
            if (!$first_lot) {
                $first_lot = CourierDraftWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$old_pallet],
                    ['batch_number', '<>',null]
                ])->first();
            }         

            if ($temp_arr && $first_lot) {

                $result = $this->checkMaxLotRu($old_pallet, $first_lot->batch_number);
                $lots = $result[0];            
                $other_lots = $result[1];           
                $empty_lots = $result[2];
                $lot = $result[3];
                
                if ($tracking) {
                    while (($i = array_search($tracking, $temp_arr)) !== false) {
                        unset($temp_arr[$i]);
                    }
                    $temp = [];
                    foreach ($temp_arr as $key => $value) {
                        $temp[] = $value;
                    }
                    $temp_arr = $temp;
                }

                if ($temp_arr && $lots->count() > 3) {
                    $other_track_arr = implode(",", $temp_arr);
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->empty_arr = 'CHANGE PALLET NO. FOR THE MISSING PARCELS ('.$other_track_arr.')';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);                  
                }
                else{
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->empty_arr = '';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);
                }
            } 
            elseif ($warehouse) {
                $notifications_pallet = json_decode($notifications->pallet);
                if (is_object($notifications_pallet)) {
                    $notifications_pallet->empty_arr = '';
                }
                else{
                    $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                }
                $notifications->pallet = json_encode($notifications_pallet);
            } 
        }             
        
        $notifications = json_encode($notifications);
        Warehouse::where('pallet',$old_pallet)->update([
            'notifications' => $notifications
        ]);

        return true;
    }


    protected function updateNotificationsEn($old_pallet, $old_lot, $tracking = false)
    {
        $notifications = (object)['pallet'=>'','tracking'=>''];
        
        $warehouse = Warehouse::where('pallet',$old_pallet)->first();
        if ($warehouse) {
            if($warehouse->notifications){
                $notifications = json_decode($warehouse->notifications);
            }
        }

        if ($old_lot) {
            // For other lots
            $other_lots = $this->checkMaxLotEn($old_pallet, $old_lot)[1];                
            if ($other_lots) $temp_arr = $other_lots->pluck('tracking_main')->toArray();

            if ($temp_arr) {
                if ($tracking) {
                    while (($i = array_search($tracking, $temp_arr)) !== false) {
                        unset($temp_arr[$i]);
                    }
                    $temp = [];
                    foreach ($temp_arr as $key => $value) {
                        $temp[] = $value;
                    }
                    $temp_arr = $temp;
                }

                if ($temp_arr) {
                    $other_track_arr = implode(",", $temp_arr);
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->other_arr = 'CHANGE OR DELETE PALLET NO. FOR THE PARCELS ('.$other_track_arr.') AND TRY AGAIN';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);                  
                }
                else{
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->other_arr = '';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);
                }
            } 
            elseif ($warehouse) {
                $notifications_pallet = json_decode($notifications->pallet);
                if (is_object($notifications_pallet)) {
                    $notifications_pallet->other_arr = '';
                }
                else{
                    $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                }
                $notifications->pallet = json_encode($notifications_pallet);
            } 
        }
        else{
            // For empty lots
            $empty_lots = PhilIndWorksheet::where('in_trash',false)->where([
                ['pallet_number',$old_pallet],
                ['lot',null]
            ])->get(); 
            $c_empty_lots = CourierEngDraftWorksheet::where('in_trash',false)->where([
                ['pallet_number',$old_pallet],
                ['lot',null]
            ])->get();    
            if ($empty_lots && $c_empty_lots) {
                $empty_lots = $empty_lots->merge($c_empty_lots);
            }
            elseif (!$empty_lots && $c_empty_lots) {
                $empty_lots = $c_empty_lots;
            }            
            if ($empty_lots) $temp_arr = $empty_lots->pluck('tracking_main')->toArray();

            $first_lot = PhilIndWorksheet::where('in_trash',false)->where([
                ['pallet_number',$old_pallet],
                ['lot', '<>',null]
            ])->first();  
            if (!$first_lot) {
                $first_lot = CourierEngDraftWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$old_pallet],
                    ['lot', '<>',null]
                ])->first();
            }         

            if ($temp_arr && $first_lot) {

                $result = $this->checkMaxLotEn($old_pallet, $first_lot->lot);
                $lots = $result[0];            
                $other_lots = $result[1];           
                $empty_lots = $result[2];
                $lot = $result[3];
                
                if ($tracking) {
                    while (($i = array_search($tracking, $temp_arr)) !== false) {
                        unset($temp_arr[$i]);
                    }
                    $temp = [];
                    foreach ($temp_arr as $key => $value) {
                        $temp[] = $value;
                    }
                    $temp_arr = $temp;
                }

                if ($temp_arr && $lots->count() > 3) {
                    $other_track_arr = implode(",", $temp_arr);
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->empty_arr = 'CHANGE PALLET NO. FOR THE MISSING PARCELS ('.$other_track_arr.')';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);                  
                }
                else{
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->empty_arr = '';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);
                }
            } 
            elseif ($warehouse) {
                $notifications_pallet = json_decode($notifications->pallet);
                if (is_object($notifications_pallet)) {
                    $notifications_pallet->empty_arr = '';
                }
                else{
                    $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                }
                $notifications->pallet = json_encode($notifications_pallet);
            } 
        }                     
        
        $notifications = json_encode($notifications);
        Warehouse::where('pallet',$old_pallet)->update([
            'notifications' => $notifications
        ]);

        return true;
    }


    private function checkLotRu($lot, $pallet)
    {     
        if ($lot) {
            $result = $this->checkMaxLotRu($pallet, $lot);
        }
        else{           
            $first_lot = NewWorksheet::where('in_trash',false)->where([
                ['pallet_number',$pallet],
                ['batch_number', '<>',null]
            ])->first();   
            if (!$first_lot) {
                $first_lot = CourierDraftWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$pallet],
                    ['batch_number', '<>',null]
                ])->first();
            }        

            if ($first_lot) {
                $result = $this->checkMaxLotRu($pallet, $first_lot->batch_number);                
            }
            else {
                $lots = NewWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$pallet],
                    ['batch_number',null]
                ])->get();
                $c_lots = CourierDraftWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$pallet],
                    ['batch_number',null]
                ])->get();
                if ($lots && $c_lots) {
                    $lots = $lots->merge($c_lots);
                }
                elseif (!$lots && $c_lots) {
                    $lots = $c_lots;
                }

                $empty_lots = $lots;
                $result = [$lots, collect([]), $empty_lots, $lot];
            }
        }        
                                
        return $result;
    }


    private function checkLotEn($lot, $pallet)
    {           
        if ($lot) {
            $result = $this->checkMaxLotEn($pallet, $lot);
        }
        else{           
            $first_lot = PhilIndWorksheet::where('in_trash',false)->where([
                ['pallet_number',$pallet],
                ['lot', '<>',null]
            ])->first();
            if (!$first_lot) {
                $first_lot = CourierEngDraftWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$pallet],
                    ['lot', '<>',null]
                ])->first();
            }            

            if ($first_lot) {
                $result = $this->checkMaxLotEn($pallet, $first_lot->lot);                
            }
            else {
                $lots = PhilIndWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$pallet],
                    ['lot',null]
                ])->get();
                $c_lots = CourierEngDraftWorksheet::where('in_trash',false)->where([
                    ['pallet_number',$pallet],
                    ['lot',null]
                ])->get();

                if ($lots && $c_lots) {
                    $lots = $lots->merge($c_lots);
                }
                elseif (!$lots && $c_lots) {
                    $lots = $c_lots;
                }
                
                $empty_lots = $lots;
                $result = [$lots, collect([]), $empty_lots, $lot];
            }
        }        
                                
        return $result;
    }


    protected function updateWarehouseLot($tracking, $lot, $which_admin, $old_pallet = false, $old_lot = false)
    {        
        $notifications = (object)['pallet'=>'','tracking'=>''];
        $other_track_arr = '';
        $empty_track_arr = '';
        $empty_lots_count = 0;

        if ($which_admin === 'ru') {
            $worksheet = NewWorksheet::where('in_trash',false)->where('tracking_main',$tracking)->first();
            if (!$worksheet) {
                $worksheet = CourierDraftWorksheet::where('in_trash',false)->where('tracking_main',$tracking)->first();
            }
            $pallets = NewWorksheet::where('in_trash',false)->where('pallet_number',$worksheet->pallet_number)->get();
            $c_pallets = CourierDraftWorksheet::where('in_trash',false)->where('pallet_number',$worksheet->pallet_number)->get();
            if ($pallets && $c_pallets) {
                $pallets = $pallets->merge($c_pallets);
            }
            elseif (!$pallets && $c_pallets) {
                $pallets = $c_pallets;
            }

            $result = $this->checkLotRu($lot, $worksheet->pallet_number);
            $lots = $result[0];            
            $other_lots = $result[1];           
            $empty_lots = $result[2];
            $lot = $result[3];
            $different_lots = $pallets->count() - $lots->count();                        
        }
        elseif ($which_admin === 'en') {
            $worksheet = PhilIndWorksheet::where('in_trash',false)->where('tracking_main',$tracking)->first();
            if (!$worksheet) {
                $worksheet = CourierEngDraftWorksheet::where('in_trash',false)->where('tracking_main',$tracking)->first();
            }
            $pallets = PhilIndWorksheet::where('in_trash',false)->where('pallet_number',$worksheet->pallet_number)->get();
            $c_pallets = CourierEngDraftWorksheet::where('in_trash',false)->where('pallet_number',$worksheet->pallet_number)->get();
            if ($pallets && $c_pallets) {
                $pallets = $pallets->merge($c_pallets);
            }
            elseif (!$pallets && $c_pallets) {
                $pallets = $c_pallets;
            }

            $result = $this->checkLotEn($lot, $worksheet->pallet_number);
            $lots = $result[0];            
            $other_lots = $result[1];           
            $empty_lots = $result[2];
            $lot = $result[3];
            $different_lots = $pallets->count() - $lots->count();                                    
        }

        $empty_lots_count = $empty_lots->count();

        //dd([$pallets->count(),$lots->count(),$other_lots->count(),$empty_lots->count(),$lot]);

        // Updating of notifications for new pallet
        if ($other_lots) $temp_arr = $other_lots->pluck('tracking_main')->toArray();
        if ($temp_arr) $other_track_arr = implode(",", $temp_arr);
        if ($empty_lots) $temp_arr = $empty_lots->pluck('tracking_main')->toArray();
        if ($temp_arr) $empty_track_arr = implode(",", $temp_arr);

        $warehouse = Warehouse::where('pallet',$worksheet->pallet_number)->first();
        if ($warehouse) {
            if($warehouse->notifications){
                $notifications = json_decode($warehouse->notifications);
            }
        }            

        if ($pallets->count() === $lots->count()) {
            $notifications->pallet = '';
            $notifications = json_encode($notifications); 
            if ($lot) {
                Warehouse::where('pallet',$worksheet->pallet_number)->update([
                    'lot' => $lot,
                    'left' => date('Y-m-d'),
                    'notifications' => $notifications
                ]);
            } 
            else{
                Warehouse::where('pallet',$worksheet->pallet_number)->update([
                    'lot' => null,
                    'left' => null,
                    'notifications' => $notifications
                ]);
            }                          
        }
        elseif ($lots->count() > 3) {            
            $notifications_pallet = json_decode($notifications->pallet);
            if (!$notifications_pallet) $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
            elseif (!is_object($notifications_pallet)) $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
            if ($empty_track_arr) $notifications_pallet->empty_arr = 'CHANGE PALLET NO. FOR THE MISSING PARCELS ('.$empty_track_arr.')';
            else $notifications_pallet->empty_arr = '';
            if ($other_track_arr) $notifications_pallet->other_arr = 'CHANGE OR DELETE PALLET NO. FOR THE PARCELS ('.$other_track_arr.') AND TRY AGAIN';
            else $notifications_pallet->other_arr = '';

            $notifications->pallet = json_encode($notifications_pallet);
            $notifications = json_encode($notifications);
            if ($lot) {
                Warehouse::where('pallet',$worksheet->pallet_number)->update([
                    'lot' => $lot,
                    'left' => date('Y-m-d'),
                    'notifications' => $notifications
                ]);
            } 
            else{
                Warehouse::where('pallet',$worksheet->pallet_number)->update([
                    'lot' => null,
                    'left' => null,
                    'notifications' => $notifications
                ]);
            } 
        } 

        // Updating of notifications for old pallet
        if ($which_admin === 'ru') {           
            if ($old_pallet && $old_lot) {
                $this->updateNotificationsRu($old_pallet, $old_lot);
            }
            if ($empty_lots_count && $old_pallet) {
                $this->updateNotificationsRu($old_pallet, null);
            }
        }
        elseif ($which_admin === 'en'){
            if ($old_pallet && $old_lot) {
                $this->updateNotificationsEn($old_pallet, $old_lot);
            }
            if ($empty_lots_count && $old_pallet) {
                $this->updateNotificationsEn($old_pallet, null);
            }
        }
    }


    protected function checkForMissingTracking($tracking)
    {
        $result = Warehouse::where('notifications', 'like', '%'.$tracking.'%')->first();
        if ($result) {
            $notifications = json_decode($result->notifications);
            if ($notifications->tracking) {
                $notifications_tracking = json_decode($notifications->tracking);
                if (stripos($notifications_tracking->arr, $tracking) !== false) {
                    $notifications_tracking->arr = str_replace(",$tracking", "", $notifications_tracking->arr);
                    $notifications_tracking->arr = str_replace("$tracking,", "", $notifications_tracking->arr);
                    $notifications_tracking->arr = str_replace($tracking, "", $notifications_tracking->arr);
                    if ($notifications_tracking->arr) {
                        $notifications_tracking->message = 'The ('.$notifications_tracking->arr.') are missing in the work sheet. Check the tracking number or add it to the work sheet';
                    }
                    else{
                        $notifications_tracking->message = '';
                    }
                }

                $notifications_tracking = json_encode($notifications_tracking);     
                $notifications->tracking = $notifications_tracking;     
                $notifications = json_encode($notifications);
                $result->notifications = $notifications;
                $result->save();
            }
            if ($this->checkWhichAdmin($tracking) === 'ru'){                   
                NewWorksheet::where('in_trash',false)->where('tracking_main', $tracking)
                ->update([
                    'pallet_number' => $result->pallet,
                    'batch_number' => $result->lot
                ]);
                CourierDraftWorksheet::where('in_trash',false)->where('tracking_main', $tracking)
                ->update([
                    'pallet_number' => $result->pallet,
                    'batch_number' => $result->lot
                ]);
            }
            else if ($this->checkWhichAdmin($tracking) === 'en'){
                PhilIndWorksheet::where('in_trash',false)->where('tracking_main', $tracking)
                ->update([
                    'pallet_number' => $result->pallet,
                    'lot' => $result->lot
                ]);
                CourierEngDraftWorksheet::where('in_trash',false)->where('tracking_main', $tracking)
                ->update([
                    'pallet_number' => $result->pallet,
                    'lot' => $result->lot
                ]);
            }
        }
        return true;
    }


    protected function updateWarehousePallet($old_tracking, $new_tracking, $old_pallet, $new_pallet, $old_lot, $new_lot, $which_admin, $worksheet)
    {
        $message = '';
        if ($old_tracking !== $new_tracking) {
            $update_result = $this->updateWarehouse($old_pallet, $new_pallet, $old_tracking, $new_tracking);
            if (!$update_result) {
                $worksheet->pallet_number = $old_pallet;
                $worksheet->save();
                $message = 'Pallet number is not correct!';
            }
        }
        else{
            $update_result = $this->updateWarehouse($old_pallet, $new_pallet, $old_tracking);
            if (!$update_result) {
                $worksheet->pallet_number = $old_pallet;
                $worksheet->save();
                $message = 'Pallet number is not correct!';
            }
            if ($old_pallet) {
                $this->updateWarehouseLot($old_tracking, $new_lot, $which_admin, $old_pallet, $old_lot);
            }                       
        }               
        return $message;
    }


    public function removeTrackingFromPalletWorksheet($id, $which_admin, $courier = false)
    {
        if ($which_admin === 'ru') {
            if (!$courier) {
                $worksheet = NewWorksheet::find($id);
                $tracking = $worksheet->tracking_main;
                if ($tracking) {
                    $result = Warehouse::where('tracking_numbers', 'like', '%'.$tracking.'%')->first();
                    if ($result) {
                        $pallet = $result->pallet;                    
                        $this->updateWarehouseWorksheet($pallet, $tracking);
                    }
                }
            }
            else{
                $worksheet = CourierDraftWorksheet::find($id);
                $tracking = $worksheet->tracking_main;
                if ($tracking) {
                    $result = Warehouse::where('tracking_numbers', 'like', '%'.$tracking.'%')->first();
                    if ($result) {
                        $pallet = $result->pallet;                    
                        $this->updateWarehouseWorksheet($pallet, $tracking, false, true);
                    }
                }
            }            
        }
        elseif ($which_admin === 'en') {
            if (!$courier) {
                $worksheet = PhilIndWorksheet::find($id);
                $tracking = $worksheet->tracking_main;
                if ($tracking) {
                    $result = Warehouse::where('tracking_numbers', 'like', '%'.$tracking.'%')->first();
                    if ($result) {
                        $pallet = $result->pallet;
                        $this->updateWarehouseWorksheet($pallet, $tracking);
                    }
                }
            }
            else{
                $worksheet = CourierEngDraftWorksheet::find($id);
                $tracking = $worksheet->tracking_main;
                if ($tracking) {
                    $result = Warehouse::where('tracking_numbers', 'like', '%'.$tracking.'%')->first();
                    if ($result) {
                        $pallet = $result->pallet;
                        $this->updateWarehouseWorksheet($pallet, $tracking, false, true);
                    }
                }
            }
        }
        
        return true;
    }


    protected function addingOrderNumber($phone, $which_admin)
    {
        $standard_phone = ltrim($phone, " \+");

        if ($which_admin === 'ru') {
            
            $data = NewWorksheet::where('in_trash',false)->where('standard_phone', '+'.$standard_phone)
            ->get();
            $c_data = CourierDraftWorksheet::where('in_trash',false)->where('standard_phone', '+'.$standard_phone)->get();
            if ($data && $c_data) {
                $data = $data->merge($c_data);
            }
            elseif (!$data && $c_data) {
                $data = $c_data;
            }

            if (!$data->first()) return false;

            if (!$data->first()->order_number) {
                $data->transform(function ($item, $key) {
                    return $item->update(['order_number'=> ((int)$key+1)]);             
                });
            }
            else{
                $data->transform(function ($item, $key) use($standard_phone) {
                    if (!$item->order_number) {

                        $i = NewWorksheet::where('in_trash',false)->where([
                            ['standard_phone', '+'.$standard_phone],
                            ['order_number', '<>', null]
                        ])->get()->last();
                        if ($i) $i = (int)$i->order_number;

                        $c_i = CourierDraftWorksheet::where('in_trash',false)->where([
                            ['standard_phone', '+'.$standard_phone],
                            ['order_number', '<>', null]
                        ])->get()->last();
                        if ($c_i) $c_i = (int)$c_i->order_number;

                        if (!$i || $c_i > $i) $i = $c_i;

                        $i++;
                        return $item->update(['order_number'=> $i]);
                    }               
                });
            }
        } 
        elseif ($which_admin === 'en') {

            $data = PhilIndWorksheet::where('in_trash',false)->where('standard_phone', '+'.$standard_phone)->get();
            $c_data = CourierEngDraftWorksheet::where('in_trash',false)->where('standard_phone', '+'.$standard_phone)->get();
            if ($data && $c_data) {
                $data = $data->merge($c_data);
            }
            elseif (!$data && $c_data) {
                $data = $c_data;
            }

            if (!$data->first()) return false;

            if (!$data->first()->order_number) {
                $data->transform(function ($item, $key) {
                    return $item->update(['order_number'=> ((int)$key+1)]);             
                });
            }
            else{
                $data->transform(function ($item, $key) use($standard_phone) {
                    if (!$item->order_number) {

                        $i = PhilIndWorksheet::where('in_trash',false)->where([
                                ['standard_phone', '+'.$standard_phone],
                                ['order_number', '<>', null]
                            ])->get()->last();
                        if ($i) $i = (int)$i->order_number;
                        
                        $c_i = CourierEngDraftWorksheet::where('in_trash',false)->where([
                                ['standard_phone', '+'.$standard_phone],
                                ['order_number', '<>', null]
                            ])->get()->last();
                        if ($c_i) $c_i = (int)$c_i->order_number;
                        
                        if (!$i || $c_i > $i) $i = $c_i;

                        $i++;
                        return $item->update(['order_number'=> $i]);
                    }               
                });
            }
        }      
    }


    public function importDraft()
    {

        return '<h1>Draft imported successfully !</h1>';
    }

}
