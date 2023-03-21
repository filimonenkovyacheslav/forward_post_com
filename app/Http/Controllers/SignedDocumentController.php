<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\SignedDocument;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\PackingSea;
use App\PackingEng;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use PDF;
use DB;
use Auth;
use App\User;


class SignedDocumentController extends Controller
{
    public function getSignature()
    {
        $type = null;
        $id = null;
        $create_new = null;
        $cancel = null;
        $document = null;

        return view('pdf.signature_page',compact('type','id','create_new','cancel','document'));
    }


    public function tempLinks()
    {
        $items = [];
        $title = 'Temporary links';
        $items = DB::table('temp_tables')->select('name')->get();
        $temp = [];
        foreach ($items as $item) {
            if (Schema::hasTable('table_'.$item->name)) {
                $t = DB::table('table_'.$item->name)->where('id',1)->select('link','name')->get();
                if ($t->count()) $temp[] = $t;
            }     
        }
        $items = $temp;
        
        if (Auth::user()->role === 'office_ru') {
            return view('pdf.temp_links',compact('items','title'));
        }
        else{
            return view('pdf.temp_links_eng',compact('items','title'));
        }
    }


    public function formSuccess()
    {
        return view('pdf.form_success');
    }


    public function formWithSignature(Request $request, $id, $token, $user_name)
    {   
        if (Schema::hasTable('table_'.$token)) {
            $worksheet = null;
            $data_parcel = null;
            if ($id !== '0') {
                $worksheet = CourierDraftWorksheet::find($id);
                if ($worksheet->getLastDocUniq()) return redirect()->to(session('this_previous_url'))->with('status-error', 'Document exists!');
                else if ($worksheet->getLastDoc()) {
                    $document = $worksheet->getLastDoc();
                    $from_country = $worksheet->sender_country;
                    $document->updateSignedUniqId($from_country);
                    $document_id = $document->id;
                    $type = 'draft_id';
                    $token = $this->generateRandomString(15);
                    $this->createTempTableAfterCancel($token);
                    return redirect()->route('formAfterCancel',compact('type','id','document_id','token')); 
                }
                
                $data_parcel = $this->fillResponseDataRu($worksheet, $request, true, true);
                $data_parcel['parcels_qty'] = $worksheet->parcels_qty;
                if ($data_parcel) {
                    $data_parcel = json_encode($data_parcel);            
                    $result = DB::table('table_'.$token)->find(1);
                    if (!$result) {
                        DB::table('table_'.$token)
                        ->insert([
                            'data' => $data_parcel
                        ]);
                    }  
                    elseif ($request->api === 'true') {
                        $result->update([
                            'data' => $data_parcel
                        ]);
                    }         
                } 
            } 
            else{
                $data_parcel = $request->data_parcel;
                $result = DB::table('table_'.$token)->find(1);
                if (!$result && $data_parcel) {
                    DB::table('table_'.$token)
                    ->insert([
                        'data' => $data_parcel
                    ]);
                }                 
            }      
            
            $israel_cities = $this->israelCities();
            $israel_cities['other'] = 'Другой город';                   

            return view('pdf.form_with_signature',compact('israel_cities','data_parcel','token','worksheet','user_name','id'));
        }    
        else return '<h1>Session ended!</h1>';
    }


    public function formWithSignatureEng(Request $request, $id, $token, $user_name)
    {
        if (Schema::hasTable('table_'.$token)){
            $worksheet = null;
            $data_parcel = null;
            if ($id !== '0') {
                $worksheet = CourierEngDraftWorksheet::find($id);
                if ($worksheet->getLastDocUniq()) return redirect()->to(session('this_previous_url'))->with('status-error', 'Document exists!'); 
                else if ($worksheet->getLastDoc()) {
                    $document = $worksheet->getLastDoc();
                    $from_country = $worksheet->shipper_country;
                    $document->updateSignedUniqId($from_country);
                    $document_id = $document->id;
                    $type = 'eng_draft_id';
                    $token = $this->generateRandomString(15);
                    $this->createTempTableAfterCancel($token);
                    return redirect()->route('formAfterCancel',compact('type','id','document_id','token')); 
                }
                
                $data_parcel = $this->fillResponseDataEng($worksheet, $request, true, true);
                $data_parcel['parcels_qty'] = $worksheet->parcels_qty;
                if ($data_parcel) {
                    $data_parcel = json_encode($data_parcel);
                    $result = DB::table('table_'.$token)->find(1);
                    if (!$result) {
                        DB::table('table_'.$token)
                        ->insert([
                            'data' => $data_parcel
                        ]);
                    }  
                    elseif ($request->api === 'true') {
                        $result->update([
                            'data' => $data_parcel
                        ]);
                    }         
                } 
                $this->signedToUpdatesArchive($worksheet);
            }   
            else{
                $data_parcel = $request->data_parcel;
                $result = DB::table('table_'.$token)->find(1);
                if (!$result && $data_parcel) {
                    DB::table('table_'.$token)
                    ->insert([
                        'data' => $data_parcel
                    ]);
                }                 
            } 

            $israel_cities = $this->israelCities();
            $israel_cities['other'] = 'Other city';  
            $to_country = $this->to_country_arr;          
            $domain = $this->getDomainRule();

            return view('pdf.form_with_signature_eng',compact('israel_cities','data_parcel','domain','token','worksheet','to_country','user_name','id')); 
        }
        else return '<h1>Session ended!</h1>';     
    }


    public function signatureForCancel(Request $request)
    {
        $type = $request->type;
        $id = $request->id;
        $create_new = $request->create_new;
        $cancel = 'cancel';
        return view('pdf.signature_page',compact('type','id','create_new','cancel'));       
    }


    public function cancelingDocument($document)
    {
        $folderPath = $this->checkDirectory('canceled_documents');
        $oldPath = $this->checkDirectory('documents');
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;       
        $cancel = 'cancel';
        
        $worksheet->packing_number = null;
        $worksheet->save();

        $old_document = SignedDocument::find($document->old_document_id);
        $file_name = $old_document->pdf_file;
        if (file_exists($oldPath.$file_name)) unlink($oldPath.$file_name);
        $document = $old_document;
        
        if (!$old_document->screen_ru_form) {
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

        return $document;
    }


    public function getType($worksheet)
    {
        $type = '';
        
        switch ($worksheet->table) {
            case 'new_worksheet':
                $type = 'worksheet_id';
                break;
            case 'phil_ind_worksheet':
                $type = 'eng_worksheet_id';
                break;
            case 'courier_draft_worksheet':
                $type = 'draft_id';
                break;
            case 'courier_eng_draft_worksheet':
                $type = 'eng_draft_id';
                break;
            default:
                // code...
                break;
        }

        return $type;
    }

    
    /**
     *  Create signature image
     */
    public function setSignature(Request $request)
    {
        $folderPath = $this->checkDirectory('signatures');

        if (!$request->signed) {
            $request = $this->contentToObj($request);
        }
        
        $user_name = $request->user_name;
        $img = $request->signed;
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file_name = uniqid().".png";
        $success = file_put_contents($folderPath.$file_name, $data);
        
        if (!$request->type && !$request->form_screen) {
            if (!$request->document_id) $document = $this->createNewDocument($request,$file_name);
            else $document = $this->updateDocument($request,$file_name);
            $id = $document->id;
            $pdf_file = $this->savePdf($id);
            $worksheet = $document->getWorksheet();
            $type = $this->getType($worksheet);
            if ($user_name) {
                $this->signedToUpdatesArchive($worksheet,$user_name,$document->uniq_id);
            } 
            $worksheet->packing_number = $document->uniq_id;
            $worksheet->save(); 
            $worksheet->checkCourierTask($worksheet->status);      
            CourierEngDraftWorksheet::where('standard_phone',$worksheet->standard_phone)
            ->whereIn('status',['Box','Pending','Packing list'])->delete();   
            $this->deleteTempTable($request->session_token); 

            return redirect('/form-success?pdf_file='.$pdf_file.'&new_document_id='.$id.'&type='.$type);
        }
        elseif ($request->form_screen) {
            if (!$request->document_id) $document = $this->createNewDocument($request,$file_name);
            else $document = $this->updateDocument($request,$file_name);
            $id = $document->id;
            $pdf_file = $this->savePdfRu($id);
            $worksheet = $document->getWorksheet();
            $type = $this->getType($worksheet);
            if ($user_name) {
                $this->signedToUpdatesArchive($worksheet,$user_name,$document->uniq_id);
            } 
            $worksheet->packing_number = $document->uniq_id;
            $worksheet->save();   
            $worksheet->setIndexNumber();        
            $worksheet->checkCourierTask($worksheet->status);
            CourierDraftWorksheet::where('standard_phone',$worksheet->standard_phone)
            ->whereIn('status',['Коробка','Подготовка','Пакинг лист'])->delete();
            $this->deleteTempTable($request->session_token);
          
            return redirect('/form-success?pdf_file='.$pdf_file.'&new_document_id='.$id.'&type='.$type);
        }
        elseif ($request->cancel){    
            if (!$request->document_id) $document = $this->createNewDocument($request,$file_name,true);
            else $document = $this->updateDocument($request,$file_name);        
            $old_document = $this->cancelingDocument($document);
            $document_id = $document->id;            
            $pdf_file = $this->savePdfForCancel($document_id,$request->type);
            $worksheet = $document->getWorksheet();
            $type = $this->getType($worksheet);

            if (!$request->create_new) {

                return redirect('/form-success?pdf_file='.$pdf_file.'&new_document_id='.$old_document->id.'&old_file='.$old_document->pdf_file.'&type='.$type);
            }
            else{                
                $id = $request->id;
                $type = $request->type;
                $token = $this->generateRandomString(15);
                $this->createTempTableAfterCancel($token);
                return redirect()->route('formAfterCancel',compact('type','id','document_id','token'));
            }          
        }               
    }


    public function createTempTableAfterCancel($token)
    {
        $this->destroyTempTables();
        Schema::create('table_'.$token, function (Blueprint $table) {
            $table->increments('id');
            $table->text('data')->nullable();
            $table->string('link')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('worksheet_id')->nullable();
            $table->timestamps();
        });
        DB::table('temp_tables')->insert([
            'name'=>$token,
            'created_at'=>date('Y-m-d')
        ]); 
        return true;        
    }


    public function formAfterCancel($type, $id, $document_id, $token)
    {
        if (!Schema::hasTable('table_'.$token)) return '<h1>Session ended!</h1>';
        
        $israel_cities = $this->israelCities();
        $worksheet = null;
        $request = (object)[];
        $request->quantity_sender = '1';
        $request->quantity_recipient = '1';

        if ($type === 'worksheet_id' || $type === 'draft_id') {

            if ($type === 'worksheet_id') $worksheet = NewWorksheet::find($id);
            elseif ($type === 'draft_id') $worksheet = CourierDraftWorksheet::find($id);

            $israel_cities['other'] = 'Другой город';
            $data_parcel = $this->fillResponseDataRu($worksheet, $request, true, true);
            $data_parcel['parcels_qty'] = $worksheet->parcels_qty;
            $form_link = url('/form-after-cancel/'.$type.'/'.$id.'/'.$document_id.'/'.$token);
            DB::table('table_'.$token)->insert([
                'link'=>$form_link,
                'type'=>$type,
                'worksheet_id'=>$id,
                'name'=>$data_parcel['first_name'].' '.$data_parcel['last_name']
            ]);
            $data_parcel = json_encode($data_parcel);
            
            
            return view('pdf.form_after_cancel',compact('israel_cities','data_parcel','document_id','type','id','token'));
        } 
        elseif ($type === 'eng_worksheet_id' || $type === 'eng_draft_id') {
            if ($type === 'eng_worksheet_id') $worksheet = PhilIndWorksheet::find($id);
            elseif ($type === 'eng_draft_id') $worksheet = CourierEngDraftWorksheet::find($id);

            $israel_cities['other'] = 'Other city';
            $data_parcel = $this->fillResponseDataEng($worksheet, $request, true, true);
            $data_parcel['parcels_qty'] = $worksheet->parcels_qty;
            $form_link = url('/form-after-cancel/'.$type.'/'.$id.'/'.$document_id.'/'.$token);
            DB::table('table_'.$token)->insert([
                'link'=>$form_link,
                'type'=>$type,
                'worksheet_id'=>$id,
                'name'=>$data_parcel['first_name'].' '.$data_parcel['last_name']
            ]);
            $data_parcel = json_encode($data_parcel);
            $domain = $this->getDomainRule();
            $to_country = $this->to_country_arr;
            
            return view('pdf.form_after_cancel_eng',compact('israel_cities','data_parcel','document_id','type','id','domain','to_country','token'));
        }
    }


    public function formUpdateAfterCancel(Request $request)
    {
        if (!$request->document_id) {
            $request = $this->contentToObj($request);
        }
        
        $document = SignedDocument::find($request->document_id);
        $result = $document->updateWorksheet($request);
        //$this->deleteTempTable($request->session_token);
        
        if ($result) {

            switch ($request->type) {

                case "draft_id":

                $form_screen = $this->formToImg($request);
                return redirect('/signature-page?draft_id='.$result->id.'&form_screen='.$form_screen.'&document_id='.$request->document_id.'&session_token='.$request->session_token);

                break;

                case "eng_draft_id":

                return redirect('/signature-page?eng_draft_id='.$result->id.'&document_id='.$request->document_id.'&session_token='.$request->session_token);

                break;

                case "worksheet_id":

                $form_screen = $this->formToImg($request);
                return redirect('/signature-page?worksheet_id='.$result->id.'&form_screen='.$form_screen.'&document_id='.$request->document_id.'&session_token='.$request->session_token);

                break;

                case "eng_worksheet_id":

                return redirect('/signature-page?eng_worksheet_id='.$result->id.'&document_id='.$request->document_id.'&session_token='.$request->session_token);

                break;
            }                      
        }
        else{
            return redirect()->route('formAfterCancel')->with('status', 'Error update!');
        }
    }

    
    /**
     *  Create pdf file
     */
    public function pdfview($id)
    {
        $cancel = null;
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;
        //view()->share('items',$items);        
        return view('pdf.pdfview',compact('worksheet','document','tracking','cancel'));
    }


    /**
     *  Create pdf file
     */
    public function pdfviewForward($id)
    {
        $cancel = null;
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;      
        return view('pdf.pdfview_forward',compact('worksheet','document','tracking','cancel'));
    }


    /**
     *  Create pdf file
     */
    public function pdfviewRu($id)
    {
        $cancel = null;
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;      
        return view('pdf.pdfview_ru',compact('worksheet','document','tracking','cancel'));
    }

    
    public function savePdf($id)
    {
        $folderPath = $this->checkDirectory('documents');       
        $cancel = null;
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;
        $file_name = $document->uniq_id.'.pdf';
        $document->pdf_file = $file_name;
        $document->save();

        if ($this->getDomainRule() !== 'forward') {
            $pdf = PDF::loadView('pdf.pdfview',compact('worksheet','document','tracking','cancel'));
        }
        elseif($this->getDomainRule() === 'forward'){
            $pdf = PDF::loadView('pdf.pdfview_forward',compact('worksheet','document','tracking','cancel'));
        }
        $pdf->save($folderPath.$file_name);

        return $file_name;
    }


    public function savePdfRu($id)
    {
        $folderPath = $this->checkDirectory('documents');
        $cancel = null;
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;
        $file_name = $document->uniq_id.'.pdf';
        $document->pdf_file = $file_name;
        $document->save();

        $pdf = PDF::loadView('pdf.pdfview_ru',compact('worksheet','document','tracking','cancel'));
        $pdf->save($folderPath.$file_name);

        return $file_name;
    }


    public function savePdfForCancel($id,$type)
    {
        $folderPath = $this->checkDirectory('documents_for_cancel');
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $message = $this->messageForCancelPdf($type,$worksheet->id)[0];
        $old_document = SignedDocument::find($document->old_document_id);
        $file_name = 'for_cancel_'.$old_document->uniq_id.'.pdf';
        $document->file_for_cancel = $file_name;
        $document->save();        

        $pdf = PDF::loadView('pdf.pdf_for_cancel',compact('worksheet','document','message','type','old_document'));
        $pdf->save($folderPath.$file_name);

        return $file_name;
    }


    public function downloadPdf($id, $api = null)
    {
        $document = SignedDocument::find($id);
        if (!$document && $api) $document = SignedDocument::where('uniq_id',$id)->first();
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;
        $cancel = null;

        if (!$document->screen_ru_form) {
            if ($this->getDomainRule() !== 'forward') {
                $pdf = PDF::loadView('pdf.pdfview',compact('worksheet','document','tracking','cancel'));
            }
            elseif($this->getDomainRule() === 'forward'){
                $pdf = PDF::loadView('pdf.pdfview_forward',compact('worksheet','document','tracking','cancel'));
            }
        }
        else        
            $pdf = PDF::loadView('pdf.pdfview_ru',compact('worksheet','document','tracking','cancel'));
        
        return $pdf->download($document->uniq_id.'.pdf');
    }


    public function downloadAllPdf(Request $request)
    {
        $items = $this->getUploadFiles($request->type,$request->id);
        if (count($items))
            return view('pdf.download_pdf',compact('items'));
        else
            return redirect()->back()->with('status-error', 'There is nothing!');
    }


    public function downloadDirectory(Request $request)
    {
        return response()->download($request->path);
    }


    /**
     *  Create new document
     */
    public function createNewDocument($request,$file_name,$other = false)
    {
        if (!$other) {
            $new_document = new SignedDocument();
            $document = $new_document->createSignedDocument($request,$file_name);
            return $document;
        }
        else{
            $new_document = new SignedDocument();
            $document = $new_document->createSignedDocument($request,$file_name,$other);
            return $document;
        }
    }


    public function updateDocument($request,$file_name)
    {
        $document = SignedDocument::find($request->document_id);
        $document = $document->updateSignedDocument($request,$file_name);
        return $document;
    }


    public function checkTempTable(Request $request)
    {
        // For signed forms
        if (!$request->session_token) {
            $request = $this->contentToObj($request);
        }
        if (Schema::hasTable('table_'.$request->session_token)) return 'true';
        else return 'false';       
    }


    protected function deleteTempTable($session_token)
    {
        if ($session_token) {
            Schema::dropIfExists('table_'.$session_token);
        }       
    }


    protected function destroyTempTables()
    {
        $delete_date = Date('Y-m-d', strtotime('-1 days'));
        $tables = DB::table('temp_tables')
        ->where('created_at','<',$delete_date)
        ->get();
        foreach ($tables as $table) {
            if (Schema::hasTable('table_'.$table->name)) $this->deleteTempTable($table->name);
            DB::table('temp_tables')->where('id',$table->id)->delete();
        }        
    }


    public function createTempTable(Request $request)
    {
        $this->destroyTempTables();
        
        if ($request->session_token) {
            Schema::create('table_'.$request->session_token, function (Blueprint $table) {
                $table->increments('id');
                $table->text('data')->nullable();
                $table->string('link')->nullable();
                $table->string('name')->nullable();
                $table->string('type')->nullable();
                $table->string('worksheet_id')->nullable();
                $table->timestamps();
            });
            DB::table('temp_tables')->insert([
                'name'=>$request->session_token,
                'created_at'=>date('Y-m-d')
            ]);
        }  
        return $request->session_token;            
    }


    public function addToTempTable(Request $request)
    {       
        if ($request->get('session_token')) {
            $post = DB::table('table_'.$request->get('session_token'))->find(1);
            if (!$post) {
                DB::table('table_'.$request->get('session_token'))
                ->insert([
                    'data' => $request->getContent()
                ]);
            }
            else
                DB::table('table_'.$request->get('session_token'))
            ->where('id',1)
            ->update([
                'data' => $request->getContent()
            ]);
        }
        return $request->getContent();
    }


    public function getFromTempTable($id)
    {
        if ($id) {
            $post = DB::table('table_'.$id)->find(1);
            if ($post) {
                return $post->data;
            }                        
        } 
        else return 'error';      
    }


    public function addSignedRuForm(Request $request)
    {
        $message = '';
        // For signed forms
        if (!$request->parcels_qty) {
            $request = $this->contentToObj($request);
        }

        if (!Schema::hasTable('table_'.$request->session_token)) return '<h1>Session ended!</h1>';
        
        $user_name = $request->user_name;
        
        if (!$request->phone_exist_checked) {
            $message = $this->checkExistPhone($request,'courier_draft_worksheet');
            if ($message) {
                if ($request->signature) {
                    return redirect()->route('formWithSignature')->with('phone_exist', $message)->with('phone_number',$request->standard_phone);
                }                
            }
        }
        else{
            $message = $this->createRuParcel($request);

            if ($request->signature) {
                if ($message['id']) {
                    if (isset($request->worksheet_id))
                        $this->deleteOldWorksheet($request->worksheet_id,'ru');
                    if ($request->session_token)
                        //$this->deleteTempTable($request->session_token);
                    $form_screen = $this->formToImg($request);
                    return redirect('/signature-page?draft_id='.$message['id'].'&form_screen='.$form_screen.'&user_name='.$user_name.'&session_token='.$request->session_token);
                }
                else{
                    return redirect()->route('formWithSignature')->with('status', $message['message']);
                }
            }                     
        }        
        
        $message = $this->createRuParcel($request);

        if ($request->signature) {
            if ($message['id']) {
                if (isset($request->worksheet_id))
                    $this->deleteOldWorksheet($request->worksheet_id,'ru');
                if ($request->session_token)
                    //$this->deleteTempTable($request->session_token);
                $form_screen = $this->formToImg($request);
                return redirect('/signature-page?draft_id='.$message['id'].'&form_screen='.$form_screen.'&user_name='.$user_name.'&session_token='.$request->session_token);
            }
            else{
                return redirect()->route('formWithSignature')->with('status', $message['message']);
            }
        }    
    }


    private function createRuParcel($request)
    {        
        $fields = Schema::getColumnListing('courier_draft_worksheet');
        $new_worksheet = new CourierDraftWorksheet();         

        foreach($fields as $field){

            if ($field === 'sender_name') {
                $new_worksheet->$field = $request->first_name.' '.$request->last_name;
            }
            else if($field === 'site_name'){
                $new_worksheet->$field = 'DD-C';
            }
            else if($field === 'recipient_name'){
                $new_worksheet->$field = $request->recipient_first_name.' '.$request->recipient_last_name;
            }
            else if($field === 'courier'){
                $user = User::where('name',$request->user_name)->first();
                $role_arr = ['agent','courier'];
                if ($user && in_array($user->role, $role_arr)) {
                    $user_name = explode('@', $user->email)[0];
                    $new_worksheet->$field = $user_name;
                }               
            }
            else if($field === 'package_content'){
                $content = '';       
                
                for ($i=1; $i < 11; $i++){
                    $temp = 'other_content_'.$i;
                    $temp_2 = 'other_quantity_'.$i;
                    if (isset($request->$temp) AND !empty($request->$temp)) {
                        $content .= $request->$temp.': '.$request->$temp_2.'; ';
                    }
                }       
                
                if(!$content){
                    $content = 'Пусто: 0';
                }                

                $new_worksheet->$field = trim($content);
            }
            else if ($field === 'comment_2'){
                if (isset($request->need_box)) $new_worksheet->$field = $request->need_box;
                if (isset($request->comment_2)) $new_worksheet->$field = $request->comment_2;
            }
            else if ($field === 'direction') {
                $new_worksheet->$field = $this->createRuDirection($request->sender_country, $request->recipient_country);
            }
            else if ($field !== 'created_at'){
                if (isset($request->$field)) {
                    $new_worksheet->$field = $request->$field;
                }               
            }           
        }

        $new_worksheet->in_trash = false;
        if (in_array($new_worksheet->sender_city, array_keys($this->israel_cities))) {
            $new_worksheet->shipper_region = $this->israel_cities[$new_worksheet->sender_city];
        }        

        // New parcel form
        if (isset($request->status_box)) {
            if ($request->status_box === 'false') {
                $new_worksheet->status = 'Забрать';
            } 
            else{
                $new_worksheet->status = 'Коробка';
            }
        }

        if (isset($request->need_box)) {
            if ($request->need_box === 'Мне не нужна коробка') {
                $new_worksheet->status = 'Забрать';
            }
            else{
                $new_worksheet->status = 'Коробка';
            }
        }        

        $new_worksheet->date = date('Y-m-d');
        $new_worksheet->status_date = date('Y-m-d'); 
        if (isset($request->order_date))
            $new_worksheet->order_date = $request->order_date;
        else
            $new_worksheet->order_date = date('Y-m-d');    

        if ($new_worksheet->save()){           

            $this->addingOrderNumber($new_worksheet->standard_phone, 'ru');
            $work_sheet_id = $new_worksheet->id;       
            $message = ['message'=>'Заказ посылки успешно создан !','id'=>$work_sheet_id];
            $new_worksheet = CourierDraftWorksheet::find($work_sheet_id);
            $new_worksheet->checkCourierTask($new_worksheet->status);

            // Packing
            $fields_packing = ['payer', 'contract', 'type', 'track_code', 'full_shipper', 'full_consignee', 'country_code', 'postcode', 'region', 'district', 'city', 'street', 'house', 'body', 'room', 'phone', 'tariff', 'tariff_cent', 'weight_kg', 'weight_g', 'service_code', 'amount_1', 'amount_2', 'attachment_number', 'attachment_name', 'amount_3', 'weight_enclosures_kg', 'weight_enclosures_g', 'value_euro', 'value_cent', 'work_sheet_id'];
            $j=1;
            $paking_not_create = true;

            for ($i=1; $i < 11; $i++) { 
                $temp = 'other_content_'.$i;
                if (isset($request->$temp) AND !empty($request->$temp)) {
                    $packing_sea = new PackingSea();
                    foreach($fields_packing as $field){
                        if ($field === 'type') {
                            $packing_sea->$field = $new_worksheet->tariff;
                        }
                        else if ($field === 'full_shipper') {
                            $packing_sea->$field = $new_worksheet->sender_name;
                        }
                        else if ($field === 'full_consignee') {
                            $packing_sea->$field = $new_worksheet->recipient_name;
                        }
                        else if ($field === 'country_code') {
                            $packing_sea->$field = $new_worksheet->recipient_country;
                        }
                        else if ($field === 'postcode') {
                            $packing_sea->$field = $new_worksheet->recipient_postcode;
                        }
                        else if ($field === 'city') {
                            $packing_sea->$field = $new_worksheet->recipient_city;
                        }
                        else if ($field === 'street') {
                            $packing_sea->$field = $new_worksheet->recipient_street;
                        }
                        else if ($field === 'house') {
                            $packing_sea->$field = $new_worksheet->recipient_house;
                        }
                        else if ($field === 'room') {
                            $packing_sea->$field = $new_worksheet->recipient_room;
                        }
                        else if ($field === 'phone') {
                            $packing_sea->$field = $new_worksheet->recipient_phone;
                        }
                        else if ($field === 'tariff') {
                            $packing_sea->$field = null;
                        }
                        else if ($field === 'work_sheet_id') {
                            $packing_sea->$field = $work_sheet_id;
                        }
                        else if ($field === 'attachment_number') {
                            $packing_sea->$field = $j;
                        }
                        else if ($field === 'attachment_name') {
                            $packing_sea->$field = $request->$temp;
                        }
                        else if ($field === 'amount_3') {
                            $temp_2 = 'other_quantity_'.$i;
                            $packing_sea->$field = $request->$temp_2;
                        }
                        else{
                            if (isset($request->$field)) {
                                $packing_sea->$field = $request->$field;
                            }                           
                        }
                    }
                    $j++;
                    if ($packing_sea->save()) {
                        $paking_not_create = false;
                    }
                }
            } 

            if ($paking_not_create) {
                $packing_sea = new PackingSea();
                foreach($fields_packing as $field){
                    if ($field === 'type') {
                        $packing_sea->$field = $new_worksheet->tariff;
                    }
                    else if ($field === 'full_shipper') {
                        $packing_sea->$field = $new_worksheet->sender_name;
                    }
                    else if ($field === 'full_consignee') {
                        $packing_sea->$field = $new_worksheet->recipient_name;
                    }
                    else if ($field === 'country_code') {
                        $packing_sea->$field = $new_worksheet->recipient_country;
                    }
                    else if ($field === 'postcode') {
                        $packing_sea->$field = $new_worksheet->recipient_postcode;
                    }
                    else if ($field === 'city') {
                        $packing_sea->$field = $new_worksheet->recipient_city;
                    }
                    else if ($field === 'street') {
                        $packing_sea->$field = $new_worksheet->recipient_street;
                    }
                    else if ($field === 'house') {
                        $packing_sea->$field = $new_worksheet->recipient_house;
                    }
                    else if ($field === 'room') {
                        $packing_sea->$field = $new_worksheet->recipient_room;
                    }
                    else if ($field === 'phone') {
                        $packing_sea->$field = $new_worksheet->recipient_phone;
                    }
                    else if ($field === 'tariff') {
                        $packing_sea->$field = null;
                    }
                    else if ($field === 'work_sheet_id') {
                        $packing_sea->$field = $work_sheet_id;
                    }
                    else if ($field === 'attachment_number') {
                        $packing_sea->$field = 1;
                    }
                    else if ($field === 'attachment_name') {
                        $packing_sea->$field = 'Пусто';
                    }
                    else if ($field === 'amount_3') {
                        $packing_sea->$field = '0';
                    }
                    else{
                        if (isset($request->$field)) {
                            $packing_sea->$field = $request->$field;
                        } 
                    }
                }

                $packing_sea->save();
            }
        }
        else{
            $message = ['message'=>'Ошибка сохранения !','id'=>''];
        }             
        
        return $message;        
    }


    public function addSignedEngForm(Request $request)
    {
        $message = '';
        // For signed forms
        if (!$request->parcels_qty) {
            $request = $this->contentToObj($request);
        }

        if (!Schema::hasTable('table_'.$request->session_token)) return '<h1>Session ended!</h1>';

        $user_name = $request->user_name;
        
        if (!$request->phone_exist_checked) {
            $message = $this->checkExistPhone($request,'courier_eng_draft_worksheet');
            if ($message) {
                if ($request->signature) {
                    return redirect()->route('formWithSignatureEng')->with('phone_exist', $message)->with('phone_number',$request->standard_phone);
                }                
            }
        }
        else{
            $message = $this->createEngParcel($request);

            if ($request->signature) {
                if ($message['id']) {
                    if (isset($request->worksheet_id))
                        $this->deleteOldWorksheet($request->worksheet_id,'eng');
                    if ($request->session_token)
                        //$this->deleteTempTable($request->session_token);
                    return redirect('/signature-page?eng_draft_id='.$message['id'].'&user_name='.$user_name.'&session_token='.$request->session_token);
                }
                else{
                    return redirect()->route('formWithSignatureEng')->with('status', $message['message']);
                }
            }                   
        }        
        
        $message = $this->createEngParcel($request);

        if ($request->signature) {
            if ($message['id']) {
                if (isset($request->worksheet_id))
                    $this->deleteOldWorksheet($request->worksheet_id,'eng');
                if ($request->session_token)
                    //$this->deleteTempTable($request->session_token);               
                return redirect('/signature-page?eng_draft_id='.$message['id'].'&user_name='.$user_name.'&session_token='.$request->session_token);
            }
            else{
                return redirect()->route('formWithSignatureEng')->with('status', $message['message']);
            }
        } 
    }


    private function createEngParcel($request)
    {
        $worksheet = new CourierEngDraftWorksheet();
        $fields = Schema::getColumnListing('courier_eng_draft_worksheet');
        $message = [];

        foreach($fields as $field){
            if ($field === 'shipper_name') {
                $worksheet->$field = $request->first_name.' '.$request->last_name;
            }
            else if ($field === 'consignee_name') {
                $worksheet->$field = $request->consignee_first_name.' '.$request->consignee_last_name;
            }
            else if ($field === 'consignee_address') {
                $worksheet->$field = $request->consignee_country.' '.$request->consignee_address;
            }
            else if($field === 'courier'){
                $user = User::where('name',$request->user_name)->first();
                $role_arr = ['agent','courier'];
                if ($user && in_array($user->role, $role_arr)) {
                    $user_name = explode('@', $user->email)[0];
                    $worksheet->$field = $user_name;
                }               
            }
            else if ($field === 'shipped_items') {
                $temp = '';
                for ($i=1; $i < 11; $i++) { 
                    $var = 'item_'.$i;
                    $var_2 = 'q_item_'.$i;
                    if (isset($request->$var) AND !empty($request->$var)) {
                        $temp .= $request->$var.': '.$request->$var_2.'; ';
                    }
                }
                $worksheet->$field = $temp;
            }
            else if ($field === 'direction') {
                $worksheet->$field = $this->createDirection($request->shipper_country, $request->consignee_country);
            }
            else if ($field !== 'created_at'){
                if (isset($request->$field)) {
                    $worksheet->$field = $request->$field;
                } 
            }                               
        }

        $worksheet->in_trash = false;
        if ($worksheet->shipper_country === 'Israel') {
            if (in_array($worksheet->shipper_city, array_keys($this->israel_cities))) {
                $worksheet->shipper_region = $this->israel_cities[$worksheet->shipper_city];
            }
        }        

        $worksheet->date = date('Y-m-d');
        $worksheet->status_date = date('Y-m-d');
        if (isset($request->order_date))
            $worksheet->order_date = $request->order_date;
        else
            $worksheet->order_date = date('Y-m-d');

        if (isset($request->status_box)) {
            if (!$request->status_box) {
                $worksheet->status = 'Pick up';
            } 
            else{
                $worksheet->status = 'Box';
            }
        }  

        if ($worksheet->save()) {          

            $this->addingOrderNumber($worksheet->standard_phone, 'en');
            $work_sheet_id = $worksheet->id;
            $new_worksheet = CourierEngDraftWorksheet::find($work_sheet_id);
            $new_worksheet->checkCourierTask($new_worksheet->status);

            // Packing
            $fields_packing = ['tracking', 'country', 'shipper_name', 'shipper_address', 'shipper_phone', 'shipper_id', 'consignee_name', 'consignee_address', 'consignee_phone', 'consignee_id', 'length', 'width', 'height', 'weight', 'items', 'shipment_val', 'work_sheet_id'];
            $packing = new PackingEng;
            foreach($fields_packing as $field){
                if ($field === 'country') {
                    $packing->$field = $request->consignee_country;
                    $packing->consignee_address = $request->consignee_address;                    
                }
                elseif ($field === 'shipper_name') {
                    $packing->$field = $request->first_name.' '.$request->last_name;
                }
                elseif ($field === 'shipper_phone') {
                    $packing->$field = $request->standard_phone;
                }
                elseif ($field === 'consignee_name') {
                    $packing->$field = $request->consignee_first_name.' '.$request->consignee_last_name;
                }
                elseif ($field === 'work_sheet_id') {
                    $packing->$field = $work_sheet_id;
                }
                else if ($field === 'items') {
                    $temp = '';
                    for ($i=1; $i < 11; $i++) { 
                        $var = 'item_'.$i;
                        $var_2 = 'q_item_'.$i;
                        if (isset($request->$var) AND !empty($request->$var)) {
                            $temp .= $request->$var.': '.$request->$var_2.'; ';
                        }
                    }
                    $packing->$field = $temp;
                }
                else{
                    if (isset($request->$field))
                        $packing->$field = $request->$field;
                } 
            }
            $packing->save();

            $message['id'] = $work_sheet_id;
            $message['message'] = 'Shipment order successfully created !';
        }
        else{
            $message['message'] = 'Saving error !';
        }

        return $message;  
    }


    public function philIndCheckPhoneApi(Request $request)
    {
        if (!$request->shipper_phone) {
            $request = $this->contentToObj($request);
        }
        
        if (isset($request->draft)) {
            $data = CourierEngDraftWorksheet::where('standard_phone', 'like', '%'.$request->shipper_phone.'%')->get()->last();
        }
        else{
            $data = PhilIndWorksheet::where('shipper_phone',$request->shipper_phone)
            ->orWhere('standard_phone', 'like', '%'.$request->shipper_phone.'%')
            ->get()->last();

            if (!$data) {
                $data = CourierEngDraftWorksheet::where('standard_phone', 'like', '%'.$request->shipper_phone.'%')->get()->last();
            }
        }
        
        $message = 'This phone number is not available in the system';
        $add_parcel = 'true';
        $data_parcel = [];
        $token = $request->session_token;
        $id = 0;

        if ($data) {
            $data_parcel = $this->fillResponseDataEng($data, $request, false, true);
            $data_parcel = json_encode($data_parcel);

            return redirect('/form-with-signature-eng/'.$id.'/'.$token.'?data_parcel='.$data_parcel);
        }
        else{
            return redirect('/form-with-signature-eng/'.$id.'/'.$token.'?no_phone='.$message);
        }        
    }


    public function checkPhoneApi(Request $request)
    {
        if (!$request->sender_phone) {
            $request = $this->contentToObj($request);
        }

        if (isset($request->draft)) {
            $data = CourierDraftWorksheet::where([
                ['standard_phone', 'like', '%'.$request->sender_phone.'%'],
                ['site_name', '=', 'DD-C']
            ])->get()->last();
        }
        else{
            $data = NewWorksheet::where([
                ['sender_phone',$request->sender_phone],
                ['site_name', '=', 'DD-C']
            ])
            ->orWhere([
                ['standard_phone', 'like', '%'.$request->sender_phone.'%'],
                ['site_name', '=', 'DD-C']
            ])
            ->get()->last();

            if (!$data) {
                $data = CourierDraftWorksheet::where([
                    ['standard_phone', 'like', '%'.$request->sender_phone.'%'],
                    ['site_name', '=', 'DD-C']
                ])->get()->last();
            }
        }

        $message = 'Данный номер телефона в системе отсутствует';
        $add_parcel = 'true';
        $token = $request->session_token;
        $id = 0;

        if ($data) {
            $data_parcel = $this->fillResponseDataRu($data, $request, false, true);
            $data_parcel = json_encode($data_parcel);

            return redirect('/form-with-signature/'.$id.'/'.$token.'?data_parcel='.$data_parcel);
        }
        else{
            return redirect('/form-with-signature/'.$id.'/'.$token.'?no_phone='.$message);
        }        
    }

}
