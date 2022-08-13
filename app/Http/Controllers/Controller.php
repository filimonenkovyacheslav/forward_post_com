<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
use Illuminate\Support\Facades\File;
use App\SignedDocument;
use PDF;
use DB;
use Auth;
use App\BaseModel;
use App\DeletedLog;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
    protected $from_country_dir = ['Israel' => 'IL','Germany' => 'GE'];
    protected $to_country_dir = ['India' => 'IND','Ivory Coast' => 'IC','Nigeria' => 'NIG','Ghana' => 'GHN','Philippines' => 'PH','Thailand' => 'TH'];
    protected $to_country_arr = ['India' => 'India','Nigeria' => 'Nigeria','Ghana' => 'Ghana','Ivory Coast' => 'Ivory Coast','Philippines' => 'Philippines','Thailand' => 'Thailand'];
    protected $israel_cities = ['Acre' => 'Nahariya','Afula' => 'Kiryat Shmona','Arad' => 'Eilat','Ariel' => 'Center','Ashdod' => 'South','Ashkelon' => 'South','Baqa-Jatt' => 'Haifa','Bat Yam' => 'Tel Aviv','Beersheba' => 'South','Beit She\'an' => 'Kiryat Shmona','Beit Shemesh' => 'Jerusalem','Beitar Illit' => 'Jerusalem','Binyamina' => 'North','Bnei Brak' => 'Tel Aviv','Caesaria' => 'North','Dimona' => 'Eilat','Eilat' => 'Eilat','El\'ad' => 'Center','Giv\'atayim' => 'Tel Aviv','Giv\'at Shmuel' => 'Center','Hadera' => 'Haifa','Haifa' => 'Haifa','Herzliya' => 'Tel Aviv','Hod HaSharon' => 'Center','Holon' => 'Tel Aviv','Jerusalem' => 'Jerusalem','Karmiel' => 'Nahariya','Kafr Qasim' => 'Center','Kfar Saba' => 'Center','Kiryat Ata' => 'Haifa','Kiryat Bialik' => 'Haifa','Kiryat Gat' => 'South','Kiryat Malakhi' => 'South','Kiryat Motzkin' => 'Haifa','Kiryat Ono' => 'Tel Aviv','Kiryat Shmona' => 'Kiryat Shmona','Kiryat Yam' => 'Haifa','Lod' => 'Center','Ma\'ale Adumim' => 'Jerusalem','Ma\'alot-Tarshiha' => 'Nahariya','Migdal HaEmek' => 'Nahariya','Modi\'in Illit' => 'Center','Modi\'in-Maccabim-Re\'ut' => 'Center','Nahariya' => 'Nahariya','Nazareth' => 'Nahariya','Nazareth Illit' => 'Nahariya','Nesher' => 'Haifa','Ness Ziona' => 'Center','Netanya' => 'Center','Netivot' => 'South','Ofakim' => 'South','Or Akiva' => 'Haifa','Or Yehuda' => 'Tel Aviv','Pardes Hana' => 'North','Petah Tikva' => 'Center','Qalansawe' => 'Center','Ra\'anana' => 'Center','Rahat' => 'South','Ramat Gan' => 'Tel Aviv','Ramat HaSharon' => 'Tel Aviv','Ramla' => 'Center','Rehovot' => 'Center','Rishon LeZion' => 'Center','Rosh HaAyin' => 'Center','Safed' => 'Kiryat Shmona','Sakhnin' => 'Nahariya','Sderot' => 'South','Shefa-\'Amr (Shfar\'am)' => 'Haifa','Tamra' => 'Haifa','Tayibe' => 'Center','Tel Aviv' => 'Tel Aviv','Tiberias' => 'Kiryat Shmona','Tira' => 'Center','Tirat Carmel' => 'Haifa','Umm al-Fahm' => 'Haifa','Yavne' => 'Center','Yehud-Monosson' => 'Center','Yokneam' => 'Haifa','Zikhron Yakov' => 'North'];

    

    public function updateAllPdfPacking()
    {
        $arr = [];
        $packing = SignedDocument::all();
        foreach ($packing as $item) {
            $worksheet = $item->getWorksheet();
            $worksheet->packing_number = $worksheet->getLastDocUniq();
            $arr[] = $worksheet->getLastDocUniq();
            $worksheet->save();
        }
        return $arr;
    }
    

    protected function contentToObj($request)
    {
        $result = [];
        parse_str($request->getContent(),$result);        
        $result = (object)$result;
        $request = $result;
        return $request;
    }


    protected function checkExistPhone($request, $table)
    {
        $phone = $request->standard_phone;
        switch ($table) {           

            case "courier_draft_worksheet":

            $worksheet = CourierDraftWorksheet::where('standard_phone',$phone)->first();;
            if ($worksheet) return 'В нашей базе данных уже существует ваш заказ. Вы хотите добавить новый заказ?';
            else return '';
        
            break;
            
            case "courier_eng_draft_worksheet":

            $worksheet = CourierEngDraftWorksheet::where('standard_phone',$phone)->first();;
            if ($worksheet) return 'One of your orders already exists in our database. Would you like to add one more?';
            else return '';

            break;
        }
    }


    protected function generateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
    
    
    protected function toUpdatesArchive($request,$worksheet,$double = false,$double_create = 0)
    {
        $archive = new UpdatesArchive();
        $archive->createUpdatesArchive($request,$worksheet,$double,$double_create);
        return true;
    }


    protected function signedToUpdatesArchive($worksheet,$user_name = '',$uniq_id = '',$old_uniq_id = '')
    {
        $archive = new UpdatesArchive();
        $archive->signedDocumentToUpdatesArchive($worksheet,$user_name,$uniq_id,$old_uniq_id);
        return true;
    }


    protected function deletedToUpdatesArchive($worksheet)
    {
        $archive = new UpdatesArchive();
        $archive->deletedToArchive($worksheet);
        return true;
    }


    protected function checkDocument($type,$id)
    {
        $items = DB::table('temp_tables')->select('name')->get();
        if ($items->count()) {
            foreach ($items as $item) {
                if (Schema::hasColumn('table_'.$item->name, 'worksheet_id')) {
                    $t = DB::table('table_'.$item->name)->where([
                        ['id',1],
                        ['type',$type],
                        ['worksheet_id',$id]
                    ])->first();
                    if ($t) return true;
                }     
            }
        }
        return false;
    }


    protected function checkDocument_2($type,$id)
    {
        $items = $this->getUploadFiles($type,$id);
        $worksheet = $this->getWorkSheet($type,$id);
        $uniq_id = $worksheet->getLastDocUniq();
        if (count($items) && $uniq_id){
            foreach($items as $item){
                if ($uniq_id === $item['uniq_id']) {
                    return true;
                }                
            }           
        }
        else
            return false;
    }


    protected function getWorkSheet($type,$id)
    {
        $worksheet = null;
        
        switch ($type) {

            case "draft_id":

            $worksheet = CourierDraftWorksheet::find($id);

            break;

            case "eng_draft_id":

            $worksheet = CourierEngDraftWorksheet::find($id);

            break;

            case "worksheet_id":

            $worksheet = NewWorksheet::find($id);

            break;

            case "eng_worksheet_id":

            $worksheet = PhilIndWorksheet::find($id);

            break;
        }

        return $worksheet;
    }


    protected function getUploadFiles($type,$id)
    {
          
        $worksheet = $this->getWorkSheet($type,$id);
        
        if (!$worksheet) {
            if ($type === 'eng_draft_id') $table_name = 'courier_eng_draft_worksheet';
            else $table_name = 'courier_draft_worksheet';
            $items = DeletedLog::where([
                ['worksheet_id',$id],
                ['table_name',$table_name]
            ])->first();
            if ($items) {
                return json_decode($items->packing_files);
            }
            else return [];
        }
        
        $documents = $worksheet->signedDocuments;
        $last_doc = $worksheet->getLastDoc();
        $items = [];
        if ($documents) {
            foreach ($documents as $document) { 
                $signaturesPath = $this->checkDirectory('signatures');                    
                if ($document->file_for_cancel) {
                    $folderPath = $this->checkDirectory('documents_for_cancel');
                    $file = $document->file_for_cancel;
                    if ($file) $items[] = [
                        'path'=>$folderPath.$file, 
                        'name'=>$file,
                        'signature'=>'',
                        'uniq_id'=>'',                       
                        'signature_for_cancel'=>$signaturesPath.$document->signature_for_cancel
                    ];
                }
                if ($last_doc->id != $document->id) 
                    $folderPath = $this->checkDirectory('canceled_documents');
                else
                    $folderPath = $this->checkDirectory('documents');
                $file = $document->pdf_file;
                if ($file) $items[] = [
                    'path'=>$folderPath.$file, 
                    'name'=>$file,
                    'uniq_id'=>$document->uniq_id,
                    'signature'=>$signaturesPath.$document->signature,
                    'signature_for_cancel'=>''
                ];
            }
        }       

        return $items;
    }


    protected function deleteUploadFiles($type,$id)
    {
        $items = $this->getUploadFiles($type,$id);
        if ($items) {
            foreach($items as $item) {
                if (file_exists($item['path'])) unlink($item['path']);
                if (file_exists($item['signature'])) unlink($item['signature']);
                if (file_exists($item['signature_for_cancel'])) unlink($item['signature_for_cancel']);
            } 
            return true;
        }
        else return false;             
    }


    protected function checkDirectory($name)
    {
        $folderPath = public_path().'/upload/'.$name;

        if(!File::exists($folderPath)){
            File::makeDirectory($folderPath);
            $folderPath = $folderPath.'/';
        }
        else $folderPath = public_path('upload/'.$name.'/');

        return $folderPath;
    }


    protected function formToImg($request)
    {
        /*$folderPath = $this->checkDirectory('ru_forms');

        $img = $request->form_canvas;
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file_name = uniqid().".jpeg";
        $success = file_put_contents($folderPath.$file_name, $data);*/

        return 'file_name';
    }


    protected function getDomainRule()
    {
        $domain = $_SERVER['SERVER_NAME'];
        if (strripos($domain, 'forward-post') !== false) return 'forward';
        else return 'ddcargos';
    }


    public function setTrackingToDocument($worksheet,$tracking)
    {
        $folderPath = $this->checkDirectory('documents');
        $cancel = null;
        
        $document = $worksheet->getLastDoc();
        if ($document) {
            $file_name = $document->pdf_file;
            if (file_exists($folderPath.$file_name))unlink($folderPath.$file_name);
            
            if (!$document->screen_ru_form) {
                if ($this->getDomainRule() !== 'forward') {
                    $pdf = PDF::loadView('pdf.pdfview',compact('worksheet','document','tracking','cancel'));
                }
                elseif($this->getDomainRule() === 'forward'){
                    $pdf = PDF::loadView('pdf.pdfview_forward',compact('worksheet','document','tracking','cancel'));
                }
            }
            else{
                $pdf = PDF::loadView('pdf.pdfview_ru',compact('worksheet','document','tracking','cancel'));
            }
            $pdf->save($folderPath.$file_name);
        }       

        return $document;
    }


    public function cancelPdf(Request $request)
    {
        $worksheet = null;
        $type = '';
        $id = 0;
        $document = null;
        
        if ($request->draft_id) {
            $worksheet = CourierDraftWorksheet::find($request->draft_id);
            $type = 'draft_id';
            $document = $worksheet->getLastDoc();
            $id = $worksheet->id;
        }
        elseif ($request->eng_draft_id) {
            $worksheet = CourierEngDraftWorksheet::find($request->eng_draft_id);
            $type = 'eng_draft_id';
            $document = $worksheet->getLastDoc();
            $id = $worksheet->id;
        }
        elseif ($request->worksheet_id) {
            $worksheet = NewWorksheet::find($request->worksheet_id);
            $type = 'worksheet_id';
            $document = $worksheet->getLastDoc();
            $id = $worksheet->id;
        }
        elseif ($request->eng_worksheet_id) {
            $worksheet = PhilIndWorksheet::find($request->eng_worksheet_id);
            $type = 'eng_worksheet_id';
            $document = $worksheet->getLastDoc();
            $id = $worksheet->id;
        }

        if ($document) {
            if ($document->uniq_id) {
                $this->signedToUpdatesArchive($worksheet,'','',$document->uniq_id);
                return redirect("/cancel-pdf-id/$type/$id/");
            }
            else return back();
        }
        else return back();        
    }


    public function messageForCancelPdf($type,$id)
    {
        $message = '';

        switch ($type) {
            
            case "worksheet_id":

            $worksheet = NewWorksheet::find($id);
        
            break;
            
            case "eng_worksheet_id":

            $worksheet = PhilIndWorksheet::find($id);
            $message .= 'Please consider the packing list I had submitted invalid. The packing list number is '.$worksheet->getLastDocUniq();
            if ($worksheet->tracking_main) {
                $message .= ', the tracking number is '.$worksheet->tracking_main;
            }

            break;

            case "draft_id":

            $worksheet = CourierDraftWorksheet::find($id);
        
            break;
            
            case "eng_draft_id":

            $worksheet = CourierEngDraftWorksheet::find($id);
            $message .= 'Please consider the packing list I had submitted invalid. The packing list number is '.$worksheet->getLastDocUniq();
            if ($worksheet->tracking_main) {
                $message .= ', the tracking number is '.$worksheet->tracking_main;
            }

            break;
        }

        return [$message,$worksheet];
    }


    protected function checkPdfId($type,$id)
    {
        $message_error = 'There is an unfinished process! Complete the process or recreate the order with client!';
        switch ($type) {

            case "draft_id":

            $worksheet = CourierDraftWorksheet::find($id);
        
            break;
            
            case "eng_draft_id":

            $worksheet = CourierEngDraftWorksheet::find($id);

            break;
        }
        if (!$worksheet->getLastDoc()) return 'success';
        elseif ($this->checkDocument($type,$id)) return $message_error; 
        elseif (!$this->checkDocument_2($type,$id)) return $message_error; 
        else return 'success';     
    }


    public function cancelPdfId($type,$id)
    {    
        if ($this->checkDocument($type,$id)) return redirect()->back()->with('status-error', 'There is an unfinished process! Complete the process or recreate the order with client!'); 
        if (!$this->checkDocument_2($type,$id)) return redirect()->back()->with('status-error', 'There is nothing to cancel or an unfinished process! Complete the process or recreate the order with client!');  
        $message = $this->messageForCancelPdf($type,$id)[0];
        $worksheet = $this->messageForCancelPdf($type,$id)[1];
        return view('pdf.form_cancel_pdf',compact('worksheet','type','message'));
    }


    protected function deleteOldWorksheet($id, $which_admin)
    {
        if ($id) {
            switch ($which_admin) {

                case "ru":

                $this->removeTrackingFromPalletWorksheet($id, 'ru', true);
                $this->deleteUploadFiles('draft_id',$id);
                CourierDraftWorksheet::where('id', $id)->delete();
                PackingSea::where('work_sheet_id', $id)->delete();
                ReceiptArchive::where('worksheet_id', $id)->delete();

                break;

                case "eng":

                $this->removeTrackingFromPalletWorksheet($id, 'en',true);
                $this->deleteUploadFiles('eng_draft_id',$id);
                CourierEngDraftWorksheet::where('id', $id)->delete();
                PackingEng::where('work_sheet_id', $id)->delete();
                ReceiptArchive::where('worksheet_id', $id)->delete();

                break;
            }
        }  
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


    protected function fillResponseDataRu($data, $request, $content = false, $draft = false){
        $data_parcel = [];
        if ($draft) $data_parcel['phone_exist_checked'] = 'true';
        
        if ($request->quantity_sender === '1') {               
            $sender_name = explode(" ", $data->sender_name);
            if (count($sender_name) > 1) {
                $data_parcel['first_name'] = $sender_name[0];
                $data_parcel['last_name'] = $sender_name[1];
            }
            elseif (count($sender_name) == 1) {
                $data_parcel['first_name'] = $sender_name[0];
                $data_parcel['last_name'] = '';
            }
            else{
                $data_parcel['first_name'] = '';
                $data_parcel['last_name'] = '';
            }               
            $data_parcel['sender_address'] = $data->sender_address;
            $data_parcel['sender_city'] = $data->sender_city;
            $data_parcel['sender_postcode'] = $data->sender_postcode;
            $data_parcel['sender_country'] = $data->sender_country;
            $data_parcel['standard_phone'] = $data->standard_phone;
            $data_parcel['sender_phone'] = $data->sender_phone;
            $data_parcel['sender_passport'] = $data->sender_passport;
        }
        if ($request->quantity_recipient === '1') {
            $recipient_name = explode(" ", $data->recipient_name);
            if (count($recipient_name) > 1) {
                $data_parcel['recipient_first_name'] = $recipient_name[0];
                $data_parcel['recipient_last_name'] = $recipient_name[1];
            }
            elseif (count($recipient_name) == 1) {
                $data_parcel['recipient_first_name'] = $recipient_name[0];
                $data_parcel['recipient_last_name'] = '';
            }
            else{
                $data_parcel['recipient_first_name'] = '';
                $data_parcel['recipient_last_name'] = '';
            }
            $data_parcel['recipient_street'] = $data->recipient_street;
            $data_parcel['recipient_house'] = $data->recipient_house;
            $data_parcel['recipient_room'] = $data->recipient_room;                
            $data_parcel['recipient_city'] = $data->recipient_city;
            $data_parcel['recipient_postcode'] = $data->recipient_postcode;
            $data_parcel['recipient_country'] = $data->recipient_country;
            $data_parcel['recipient_email'] = $data->recipient_email;
            $data_parcel['recipient_phone'] = $data->recipient_phone;
            $data_parcel['recipient_passport'] = $data->recipient_passport;               
            $data_parcel['body'] = $data->body;
            $data_parcel['district'] = $data->district;
            $data_parcel['region'] = $data->region;
        }

        if ($content) {
            $items = explode(";", $data->package_content);            
            if (count($items)) {
                $temp = '';
                for ($i=0; $i < count($items); $i++) {                    
                    if (strripos($items[$i], '-') !== false) {
                        $temp = explode("-", $items[$i]);
                        $data_parcel['item_'.($i+1)] = trim($temp[0]);
                        $data_parcel['q_item_'.($i+1)] = trim($temp[1]);
                    }
                    elseif (strripos($items[$i], ':') !== false) {
                        $temp = explode(":", $items[$i]);
                        $data_parcel['item_'.($i+1)] = trim($temp[0]);
                        $data_parcel['q_item_'.($i+1)] = trim($temp[1]);
                    }
                }
            }
        }

        return $data_parcel;
    }


    protected function fillResponseDataEng($data, $request, $content = false, $draft = false)
    {
        $data_parcel = [];
        if ($draft) $data_parcel['phone_exist_checked'] = 'true';
        
        if ($request->quantity_sender === '1') {
            $shipper_name = explode(" ", $data->shipper_name);
            if (count($shipper_name) > 1) {
                $data_parcel['first_name'] = $shipper_name[0];
                $data_parcel['last_name'] = $shipper_name[1];
            }
            elseif (count($shipper_name) == 1) {
                $data_parcel['first_name'] = $shipper_name[0];
                $data_parcel['last_name'] = '';
            }
            else{
                $data_parcel['first_name'] = '';
                $data_parcel['last_name'] = '';
            }
            $data_parcel['shipper_address'] = $data->shipper_address;
            $data_parcel['standard_phone'] = $data->standard_phone;
            $data_parcel['shipper_phone'] = $data->shipper_phone;
            $data_parcel['shipper_country'] = $data->shipper_country;
            $data_parcel['shipper_id'] = $data->shipper_id;
            $data_parcel['shipper_city'] = $data->shipper_city;
        }
        
        if ($request->quantity_recipient === '1') {
            if (!$draft) {
                $data = PhilIndWorksheet::where([
                    ['shipper_phone',$request->input('shipper_phone')],
                    ['consignee_name','<>', null],
                    ['consignee_address','<>', null],
                    ['consignee_phone','<>', null]
                ])
                ->orWhere([
                    ['standard_phone', 'like', '%'.$request->input('shipper_phone').'%'],
                    ['consignee_name','<>', null],
                    ['consignee_address','<>', null],
                    ['consignee_phone','<>', null]
                ])
                ->get()->last();
            }
            
            if ($data) {
                $address = trim(stristr($data->consignee_address, " "));                    
                $consignee_name = explode(" ", $data->consignee_name);
                if (count($consignee_name) > 1) {
                    $data_parcel['consignee_first_name'] = $consignee_name[0];
                    $data_parcel['consignee_last_name'] = $consignee_name[1];
                }
                elseif (count($consignee_name) == 1) {
                    $data_parcel['consignee_first_name'] = $consignee_name[0];
                    $data_parcel['consignee_last_name'] = '';
                }
                else{
                    $data_parcel['consignee_first_name'] = '';
                    $data_parcel['consignee_last_name'] = '';
                }
                $data_parcel['consignee_address'] = $address;
                $data_parcel['consignee_country'] = $data->consignee_country;
                $data_parcel['consignee_phone'] = $data->consignee_phone;
                $data_parcel['consignee_id'] = $data->consignee_id;
            }
            else{
                $data_parcel['consignee_first_name'] = '';
                $data_parcel['consignee_last_name'] = '';
                $data_parcel['consignee_address'] = '';
                $data_parcel['consignee_phone'] = '';
                $data_parcel['consignee_id'] = '';
                $data_parcel['consignee_country'] = '';
            }
        }

        if ($content) {
            $shipper_name = explode(" ", $data->shipper_name);
            if (count($shipper_name) > 1) {
                $data_parcel['first_name'] = $shipper_name[0];
                $data_parcel['last_name'] = $shipper_name[1];
            }
            elseif (count($shipper_name) == 1) {
                $data_parcel['first_name'] = $shipper_name[0];
                $data_parcel['last_name'] = '';
            }
            else{
                $data_parcel['first_name'] = '';
                $data_parcel['last_name'] = '';
            }
            $data_parcel['shipper_address'] = $data->shipper_address;
            $data_parcel['standard_phone'] = $data->standard_phone;
            $data_parcel['shipper_phone'] = $data->shipper_phone;
            $data_parcel['shipper_country'] = $data->shipper_country;
            $data_parcel['shipper_id'] = $data->shipper_id;
            $data_parcel['shipper_city'] = $data->shipper_city;

            $address = trim(stristr($data->consignee_address, " "));               
            $consignee_name = explode(" ", $data->consignee_name);
            if (count($consignee_name) > 1) {
                $data_parcel['consignee_first_name'] = $consignee_name[0];
                $data_parcel['consignee_last_name'] = $consignee_name[1];
            }
            elseif (count($consignee_name) == 1) {
                $data_parcel['consignee_first_name'] = $consignee_name[0];
                $data_parcel['consignee_last_name'] = '';
            }
            else{
                $data_parcel['consignee_first_name'] = '';
                $data_parcel['consignee_last_name'] = '';
            }
            $data_parcel['consignee_country'] = $data->consignee_country;
            $data_parcel['consignee_address'] = $address;
            $data_parcel['consignee_phone'] = $data->consignee_phone;
            $data_parcel['consignee_id'] = $data->consignee_id;
            $data_parcel['shipment_val'] = $data->shipment_val;

            $items = explode(";", $data->shipped_items);            
            if (count($items)) {
                $temp = '';
                for ($i=0; $i < count($items); $i++) {                    
                    if (strripos($items[$i], '-') !== false) {
                        $temp = explode("-", $items[$i]);
                        $data_parcel['item_'.($i+1)] = trim($temp[0]);
                        $data_parcel['q_item_'.($i+1)] = trim($temp[1]);
                    }
                    elseif (strripos($items[$i], ':') !== false) {
                        $temp = explode(":", $items[$i]);
                        $data_parcel['item_'.($i+1)] = trim($temp[0]);
                        $data_parcel['q_item_'.($i+1)] = trim($temp[1]);
                    }
                }
            }
        }
        
        return $data_parcel;
    }


    public function importDraft()
    {
        return '<h1>Draft imported successfully !</h1>';
    }

}
