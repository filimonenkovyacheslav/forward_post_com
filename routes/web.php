<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/*
*  Front
*/
Route::group(['prefix' => App\Http\Middleware\LocaleMiddleware::getLocale()], function(){
	
	Route::get('/', function () {
		return view('welcome');
	})->name('welcome');

	Route::get('/test-pdf','Admin\AdminController@testPDF');
	
	// Full Status Parcel
	Route::get('/full-status-parcel', function () {
		return view('full_status_parcel_form');
	})->name('fullStatusParcelForm');
	Route::post('/full-status-parcel','Admin\FullStatusParcelController@getFullStatusParcel')->name('getFullStatusParcel');

	// Set Indexes
	Route::get('/set-indexes','Admin\NewWorksheetController@setIndexes');
	Route::get('/set-draft-indexes','Admin\CourierDraftController@setIndexes');

	// New Receipt
	Route::get('/download-new-receipt/{id}',['uses' => 'Admin\AdminController@downloadNewReceipt','as' => 'downloadNewReceipt']);
	Route::get('/send-sms',['uses' => 'Controller@sendSms','as' => 'sendSms']);
	Route::get('/download-jpg-new-receipt/{id}',['uses' => 'Admin\AdminController@downloadJpgNewReceipt','as' => 'downloadJpgNewReceipt']);

	// Update all packing numbers
	Route::get('/all-packing-numbers', 'Controller@updateAllPdfPacking');

	// Form with signature
	Route::get('/form-with-signature/{id}/{token}/{user_name}', 'SignedDocumentController@formWithSignature')->name('formWithSignature');
	
	Route::get('/form-with-signature-eng/{id}/{token}/{user_name}', 'SignedDocumentController@formWithSignatureEng')->name('formWithSignatureEng');
	
	Route::get('/signature-page', 'SignedDocumentController@getSignature')->name('getSignature');
	
	Route::get('/pdfview/{id}', 'SignedDocumentController@pdfview')->name('pdfview');
	
	Route::get('/pdfview-forward/{id}', 'SignedDocumentController@pdfviewForward')->name('pdfviewForward');
	
	Route::get('/pdfview-ru/{id}', 'SignedDocumentController@pdfviewRu')->name('pdfviewRu');
	
	Route::get('/download-pdf/{id}/{api?}', 'SignedDocumentController@downloadPdf')->name('downloadPdf');
	
	Route::post('/download-all-pdf', 'SignedDocumentController@downloadAllPdf')->name('downloadAllPdf');
	
	Route::post('/download-directory', 'SignedDocumentController@downloadDirectory')->name('downloadDirectory');
	
	Route::post('/cancel-pdf', 'Controller@cancelPdf')->name('cancelPdf');
	
	Route::get('/cancel-pdf-id/{type}/{id}', 'Controller@cancelPdfId')->name('cancelPdfId');
	
	Route::get('/signature-for-cancel', 'SignedDocumentController@signatureForCancel')->name('signatureForCancel');
	
	Route::get('/form-after-cancel/{type}/{id}/{document_id}/{token}', 'SignedDocumentController@formAfterCancel')->name('formAfterCancel');

	Route::post('/create-temp-table', 'SignedDocumentController@createTempTable')->name('createTempTable');

	Route::get('/form-success', 'SignedDocumentController@formSuccess')->name('formSuccess');

	Route::get('/temp-links', 'SignedDocumentController@tempLinks')->name('tempLinks');
	
	// End Form with signature
	
	Route::get('/page-{page_urn}','Admin\FrontPagesController@frontPage')->name('frontPage');
	
	Route::get('/parcel-form', 'FrontController@parcelForm')->name('parcelForm');

	Route::get('/parcel-form-gcs', 'FrontController@parcelFormGcs')->name('parcelFormGcs');

	Route::get('/phil-ind-parcel-form-gcs', 'FrontController@parcelFormEngGcs')->name('parcelFormEngGcs');

	Route::post('/parcel-form', 'FrontController@newParcelAdd')->name('newParcelAdd');	

	Route::get('/parcel-form-prior', 'FrontController@parcelFormOld')->name('parcelFormOld');

	Route::post('/parcel-form-prior', 'FrontController@newParcelAdd')->name('newParcelAdd');	

	Route::post('/check-phone',['uses' => 'FrontController@checkPhone','as' => 'checkPhone']);

	Route::post('/phil-ind-check-phone',['uses' => 'FrontController@philIndCheckPhone','as' => 'philIndCheckPhone']);

	Route::get('/tracking-form', 'FrontController@trackingForm')->name('trackingForm');

	Route::post('/tracking-form', 'FrontController@getTracking')->name('getTracking');

	Route::get('/tracking-ru-form', 'FrontController@trackingRuForm')->name('trackingRuForm');

	Route::get('/tracking-ru-form-gcs', function () {
		return view('tracking_ru_form_gcs');
	})->name('trackingRuFormGcs');

	Route::get('/tracking-form-gcs', function () {
		return view('tracking_form_gcs');
	})->name('trackingFormGcs');

	Route::get('/china-parcel-form', 'FrontController@chinaParcelForm')->name('chinaParcelForm');

	Route::post('/china-parcel-form', 'FrontController@chinaParcelAdd')->name('chinaParcelAdd');

	Route::get('/phil-ind-parcel-form', 'FrontController@philIndParcelForm')->name('philIndParcelForm');

	Route::post('/phil-ind-parcel-form', 'FrontController@philIndParcelAdd')->name('philIndParcelAdd');

	Route::get('/phil-ind-parcel-form-prior', 'FrontController@philIndParcelFormOld')->name('philIndParcelFormOld');

	Route::post('/phil-ind-parcel-form-prior', 'FrontController@philIndParcelAdd')->name('philIndParcelAdd');

	Route::get('/form-for-adding-eng', 'FrontController@showFormEng')->name('showFormEng');

	Route::post('/form-for-adding-eng', 'FrontController@addFormEng')->name('addFormEng');

	Route::post('/check-tracking-phone-eng',['uses' => 'FrontController@engCheckTrackingPhone','as' => 'engCheckTrackingPhone']);
});

Route::middleware('auth')->group(function () {	

	// Tracking Lists	
	Route::get('/tracking-lists',['uses' => 'Admin\TrackingListController@index','as' => 'trackingLists']);
	Route::get('/tracking-lists-filter',['uses' => 'Admin\TrackingListController@trackingListFilter','as' => 'trackingListFilter']);
	Route::post('/tracking-lists',['uses' => 'Admin\TrackingListController@destroy','as' => 'trackingListDelete']);
	Route::post('/tracking-lists-export',['uses' => 'Admin\TrackingListController@exportTrackingList','as' => 'exportTrackingList']);

	// Checklist
	Route::get('/checklist',['uses' => 'ChecklistController@index','as' => 'checklist']);
	Route::get('/checks-history',['uses' => 'ChecklistController@checksHistory','as' => 'checksHistory']);
	Route::post('/checks-history',['uses' => 'ChecklistController@destroy','as' => 'checksHistoryDelete']);
	Route::post('/checks-history-export',['uses' => 'ChecklistController@exportChecksHistory','as' => 'exportChecksHistory']);
	Route::post('/import-checklist','ChecklistController@importChecklist')->name('importChecklist');
	
	// Import csv
	Route::post('/import-trackings','TrackingController@importTrackings')->name('importTrackings');
	Route::get('/export-trackings',['uses' => 'TrackingController@exportTrackings','as' => 'exportTrackings']);
	Route::post('/import-trackings-eng','TrackingController@importTrackingsEng')->name('importTrackingsEng');
	Route::get('/export-trackings-eng',['uses' => 'TrackingController@exportTrackingsEng','as' => 'exportTrackingsEng']);
	
	// Full Status Parcel
	Route::get('/show-full-status-parcel','Admin\FullStatusParcelController@showFullStatusParcel')->name('showFullStatusParcel');
	Route::get('/export-full-status-parcel','Admin\FullStatusParcelController@exportFullStatusParcel')->name('exportFullStatusParcel');
	Route::post('/import-full-status-parcel','Admin\FullStatusParcelController@importFullStatusParcel')->name('importFullStatusParcel');

});

// Альтернатива php artisan storage:link
Route::get('storage/{filename}', function ($filename)
{
    $path = storage_path('public/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});


//Переключение языков
Route::get('setlocale/{lang}', function ($lang) {

    $referer = Redirect::back()->getTargetUrl(); //URL предыдущей страницы
    $parse_url = parse_url($referer, PHP_URL_PATH); //URI предыдущей страницы

    //разбиваем на массив по разделителю
    $segments = explode('/', $parse_url);

    //Если URL (где нажали на переключение языка) содержал корректную метку языка
    if (in_array($segments[1], App\Http\Middleware\LocaleMiddleware::$languages)) {

        unset($segments[1]); //удаляем метку
    } 
    
    //Добавляем метку языка в URL (если выбран не язык по-умолчанию)
    if ($lang != App\Http\Middleware\LocaleMiddleware::$mainLanguage){ 
        array_splice($segments, 1, 0, $lang); 
    }	

    //формируем полный URL
    $url = Request::root().implode("/", $segments);
    
    //если были еще GET-параметры - добавляем их
    if(parse_url($referer, PHP_URL_QUERY)){    
        $url = $url.'?'. parse_url($referer, PHP_URL_QUERY);
    }

    return redirect($url); //Перенаправляем назад на ту же страницу                            

})->name('setlocale');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


/*
*  Admin
*/
Route::group(['prefix' => 'admin','middleware' => 'auth'],function() {	

	// Archive
	Route::get('/archive',['uses' => 'Admin\ArchiveController@index','as' => 'adminArchive']);

	Route::post('/to-archive',['uses' => 'Admin\ArchiveController@toArchive','as' => 'toArchive']);

	Route::get('/archive-filter',['uses' => 'Admin\ArchiveController@archiveFilter','as' => 'archiveFilter']);

	Route::post('/to-archive-remove-files',['uses' => 'Admin\ArchiveController@toArchiveRemoveFiles','as' => 'toArchiveRemoveFiles']);

	Route::post('/to-archive-remove-data',['uses' => 'Admin\ArchiveController@toArchiveRemoveData','as' => 'toArchiveRemoveData']);

	Route::post('/to-archive-remove-temp-data',['uses' => 'Admin\ArchiveController@toArchiveRemoveTempData','as' => 'toArchiveRemoveTempData']);

	Route::post('/to-repeat-download-files',['uses' => 'Admin\ArchiveController@toRepeatDownloadFiles','as' => 'toRepeatDownloadFiles']);

	Route::post('/to-repeat-import',['uses' => 'Admin\ArchiveController@toRepeatImport','as' => 'toRepeatImport']);

	Route::get('/export-archive',['uses' => 'Admin\ArchiveController@exportArchive','as' => 'exportArchive']);

	Route::post('/delete-from-archive',['uses' => 'Admin\ArchiveController@deleteFromArchive','as' => 'deleteFromArchive']);

	// New Receipts
	Route::get('/new-receipts',['uses' => 'Admin\AdminController@showNewReceipts','as' => 'showNewReceipts']);

	Route::get('/new-receipts-filter',['uses' => 'Admin\AdminController@newReceiptsFilter','as' => 'newReceiptsFilter']);
		
	// General Search	
	Route::get('/general-search',['uses' => 'Admin\AdminController@generalSearchShow','as' => 'generalSearchShow']);

	Route::post('/general-search',['uses' => 'Admin\AdminController@generalSearch','as' => 'generalSearch']);	

	// Add data by pallet	
	Route::get('/pallet-data',['uses' => 'Admin\AdminController@showPalletData','as' => 'showPalletData']);

	Route::post('/pallet-data',['uses' => 'Admin\AdminController@addPalletData','as' => 'addPalletData']);	

	// Check row color
	Route::post('/check-row-color',['uses' => 'Admin\AdminController@checkRowColor']);

	// Import Draft
	Route::get('/courier-import-draft',['uses' => 'Controller@importDraft']);

	// Updates Archive
	Route::get('/updates-archive',['uses' => 'Admin\UpdatesArchiveController@index','as' => 'adminUpdatesArchive']);

	Route::post('/updates-archive',['uses' => 'Admin\UpdatesArchiveController@destroy','as' => 'deleteUpdatesArchive']);

	Route::post('/updates-archive-id-delete',['uses' => 'Admin\UpdatesArchiveController@destroyArchiveById','as' => 'destroyArchiveById']);

	Route::get('/updates-archive-filter',['uses' => 'Admin\UpdatesArchiveController@updatesArchiveFilter','as' => 'updatesArchiveFilter']);

	// Задания Курьерам/Couriers Tasks
	Route::get('/couriers-tasks-import',['uses' => 'Admin\CourierTaskController@import']);

	Route::get('/couriers-tasks',['uses' => 'Admin\CourierTaskController@index','as' => 'adminCourierTask']);

	Route::get('/couriers-tasks-filter',['uses' => 'Admin\CourierTaskController@courierTaskFilter','as' => 'courierTaskFilter']);

	Route::get('/couriers-tasks-export',['uses' => 'Admin\CourierTaskController@exportExcelCourierTask','as' => 'exportExcelCourierTask']);

	Route::get('/couriers-tasks-done/{id}',['uses' => 'Admin\CourierTaskController@courierTaskDone','as' => 'courierTaskDone']);

	Route::post('/couriers-tasks-done-id',['uses' => 'Admin\CourierTaskController@doneById','as' => 'doneById']);

	Route::post('/couriers-tasks-data-id',['uses' => 'Admin\CourierTaskController@addCourierTaskDataById','as' => 'addCourierTaskDataById']);

	// Корзина/Trash
	Route::get('/trash',['uses' => 'Admin\TrashController@index','as' => 'adminTrash']);

	Route::get('/to-trash',['uses' => 'Admin\TrashController@toTrash','as' => 'toTrash']);

	Route::get('/trash-filter',['uses' => 'Admin\TrashController@trashFilter','as' => 'trashFilter']);

	Route::get('/trash-activate/{id}',['uses' => 'Admin\TrashController@fromTrash','as' => 'fromTrash']);

	Route::get('/trash-delete/{id}',['uses' => 'Admin\TrashController@deleteFromTrash','as' => 'deleteFromTrash']);

	// Log of deleted orders
	Route::get('/logs',['uses' => 'Admin\DeletedLogController@index','as' => 'adminLog']);

	Route::get('/to-logs',['uses' => 'Admin\DeletedLogController@toLogs','as' => 'toLogs']);

	Route::get('/logs-filter',['uses' => 'Admin\DeletedLogController@logsFilter','as' => 'logsFilter']);

	Route::get('/log-show/{id}',['uses' => 'Admin\DeletedLogController@logShow','as' => 'logShow']);

	// Warehouse
	Route::get('/warehouse-import-worksheet',['uses' => 'Admin\WarehouseController@importWorksheet']);

	Route::get('/warehouse',['uses' => 'Admin\WarehouseController@index','as' => 'adminWarehouse']);

	Route::post('/warehouse',['uses' => 'Admin\WarehouseController@destroy','as' => 'deleteWarehouse']);

	Route::get('/warehouse-open/{id}', ['uses' => 'Admin\WarehouseController@warehouseOpen','as' => 'warehouseOpen']);

	Route::post('/warehouse-delete-tracking',['uses' => 'Admin\WarehouseController@deleteTrackingFromPallet','as' => 'deleteTrackingFromPallet']);

	Route::get('/warehouse-tracking-move/{tracking}', ['uses' => 'Admin\WarehouseController@warehouseTrackingMoveShow']);

	Route::post('/warehouse-tracking-move/{tracking}',['uses' => 'Admin\WarehouseController@warehouseTrackingMove','as' => 'warehouseTrackingMove']);

	Route::get('/warehouse-filter',['uses' => 'Admin\WarehouseController@warehouseFilter','as' => 'warehouseFilter']);

	Route::get('/warehouse-edit/{id}', ['uses' => 'Admin\WarehouseController@warehouseEditShow']);

	Route::post('/warehouse-edit/{id}', ['uses' => 'Admin\WarehouseController@warehouseEdit','as' => 'warehouseEdit']);

	Route::get('/warehouse-add-tracking/{id}', ['uses' => 'Admin\WarehouseController@warehouseAddTrackingShow']);

	Route::post('/warehouse-add-tracking/{id}', ['uses' => 'Admin\WarehouseController@warehouseAddTracking','as' => 'warehouseAddTracking']);

	Route::post('/warehouse-add-data-id', ['uses' => 'Admin\WarehouseController@addWarehouseDataById','as' => 'addWarehouseDataById']);

	Route::post('/warehouse-delete-data-id', ['uses' => 'Admin\WarehouseController@deleteWarehouseById','as' => 'deleteWarehouseById']);

	Route::get('/warehouse-show-pallets', ['uses' => 'Admin\WarehouseController@palletsShow','as' => 'palletsShow']);

	Route::post('/warehouse-add-data-pallets', ['uses' => 'Admin\WarehouseController@addWarehouseDataByPallet','as' => 'addWarehouseDataByPallet']);

	Route::get('/pallets-sum',['uses' => 'Admin\WarehouseController@palletsSum','as' => 'palletsSum']);

	// Receipt
	Route::get('/receipts/{legal_entity}',['uses' => 'Admin\AdminController@adminReceipts','as' => 'adminReceipts']);

	Route::get('/receipts-archive',['uses' => 'Admin\AdminController@adminReceiptsArchive','as' => 'adminReceiptsArchive']);

	Route::get('/receipts-archive-filter',['uses' => 'Admin\AdminController@receiptsArchiveFilter','as' => 'receiptsArchiveFilter']);

	Route::post('/receipts-archive',['uses' => 'Admin\AdminController@deleteReceiptArchive','as' => 'deleteReceiptArchive']);

	Route::get('/receipts-archive-update/{id}', ['uses' => 'Admin\AdminController@receiptsArchiveShow','as' => 'receiptsArchiveShow']);

	Route::post('/receipts-archive-update/{id}',['uses'=>'Admin\AdminController@receiptsArchiveUpdate','as'=>'receiptsArchiveUpdate']);

	Route::get('/receipts-double/{id}', ['uses' => 'Admin\AdminController@receiptsDouble','as' => 'receiptsDouble']);

	Route::get('/receipts-update/{id}', ['uses' => 'Admin\AdminController@receiptsShow','as' => 'receiptsShow']);

	Route::post('/receipts-update/{id}',['uses'=>'Admin\AdminController@receiptsUpdate','as'=>'receiptsUpdate']);

	Route::post('/receipts',['uses' => 'Admin\AdminController@deleteReceipt','as' => 'deleteReceipt']);

	Route::post('/receipts-delete',['uses' => 'Admin\AdminController@deleteReceipts','as' => 'deleteReceipts']);

	Route::post('/receipts-add',['uses'=>'Admin\AdminController@receiptsAdd','as'=>'receiptsAdd']);

	Route::get('/receipts-filter/{legal_entity}',['uses' => 'Admin\AdminController@receiptsFilter','as' => 'receiptsFilter']);

	Route::get('/receipts-export',['uses' => 'Admin\AdminController@exportExcelReceipts','as' => 'exportExcelReceipts']);

	Route::get('/receipts-sum/{legal_entity}',['uses' => 'Admin\AdminController@receiptsSum','as' => 'receiptsSum']);
	
	// Partners
	Route::get('/partners',['uses' => 'Admin\PartnersController@index','as' => 'adminPartners']);
	
	Route::get('/partners-up/{role}', 'Admin\PartnersController@show');

	Route::post('/partners-up/{role}',['uses'=>'Admin\PartnersController@update','as'=>'partnerUpdate']);	
	
	// Users
	Route::get('/',['uses' => 'Admin\IndexController@index','as' => 'adminIndex']);

	Route::get('/users',['uses' => 'Admin\RolesController@index','as' => 'adminUsers']);

	Route::get('/users/{id}', 'Admin\RolesController@show');

	Route::post('/users/{id}',['uses'=>'Admin\RolesController@update','as'=>'userUpdate']);

	Route::post('/users',['uses' => 'Admin\RolesController@destroy','as' => 'deleteUser']);

	Route::get('/user-add',['uses'=>'Admin\RolesController@showAdd','as'=>'showUser']);

	Route::post('/user-add',['uses'=>'Admin\RolesController@add','as'=>'userAdd']);
	
	// Worksheet
	Route::get('/worksheet',['uses' => 'Admin\WorksheetController@index','as' => 'adminWorksheet']);

	Route::get('/worksheet/batch-number',['uses' => 'Admin\WorksheetController@showStatus','as' => 'showStatus']);

	Route::post('/worksheet/batch-number',['uses' => 'Admin\WorksheetController@changeStatus','as' => 'changeStatus']);

	Route::get('/worksheet/date',['uses' => 'Admin\WorksheetController@showStatusDate','as' => 'showStatusDate']);

	Route::post('/worksheet/date',['uses' => 'Admin\WorksheetController@changeStatusDate','as' => 'changeStatusDate']);

	Route::get('/worksheet-update/{id}', ['uses' => 'Admin\WorksheetController@show','as' => 'adminWorksheetShow']);

	Route::post('/worksheet-update/{id}',['uses'=>'Admin\WorksheetController@update','as'=>'worksheetUpdate']);

	Route::post('/delete-worksheet',['uses' => 'Admin\WorksheetController@destroy','as' => 'deleteWorksheet']);

	// Courier Draft worksheet
	Route::get('/courier-draft-worksheet-double/{id}',['uses'=>'Admin\CourierDraftController@courierDraftWorksheetDouble','as'=>'courierDraftWorksheetDouble']);

	Route::get('/courier-draft-worksheet',['uses' => 'Admin\CourierDraftController@index','as' => 'adminCourierDraftWorksheet']);

	Route::get('/courier-draft-worksheet/{id}', ['uses' => 'Admin\CourierDraftController@show','as' => 'adminCourierDraftWorksheetShow']);

	Route::post('/courier-draft-worksheet/{id}',['uses'=>'Admin\CourierDraftController@update','as'=>'courierDraftWorksheetUpdate']);

	Route::post('/courier-draft-worksheet',['uses' => 'Admin\CourierDraftController@destroy','as' => 'deleteCourierDraftWorksheet']);

	Route::post('/courier-draft-worksheet-id-data',['uses' => 'Admin\CourierDraftController@addCourierDraftDataById','as' => 'addCourierDraftDataById']);

	Route::post('/courier-draft-worksheet-id-data-delete',['uses' => 'Admin\CourierDraftController@deleteCourierDraftWorksheetById','as' => 'deleteCourierDraftWorksheetById']);

	Route::get('/courier-draft-worksheet-filter',['uses' => 'Admin\CourierDraftController@courierDraftWorksheetFilter','as' => 'courierDraftWorksheetFilter']);

	Route::get('/courier-draft-check-activate/{id}', ['uses' => 'Admin\CourierDraftController@courierDraftCheckActivate','as' => 'courierDraftCheckActivate']);

	Route::get('/courier-draft-activate/{id}', ['uses' => 'Admin\CourierDraftController@courierDraftActivate','as' => 'courierDraftActivate']);

	Route::get('/courier-draft-admin-activate', ['uses' => 'Admin\CourierDraftController@adminActivate','as' => 'adminActivate']);

	// New worksheet
	Route::get('/new-worksheet',['uses' => 'Admin\NewWorksheetController@index','as' => 'adminNewWorksheet']);

	Route::get('/new-worksheet/{id}', ['uses' => 'Admin\NewWorksheetController@show','as' => 'adminNewWorksheetShow']);

	Route::post('/new-worksheet/{id}',['uses'=>'Admin\NewWorksheetController@update','as'=>'newWorksheetUpdate']);

	Route::post('/new-worksheet',['uses' => 'Admin\NewWorksheetController@destroy','as' => 'deleteNewWorksheet']);

	Route::get('/new-worksheet-add-column',['uses'=>'Admin\NewWorksheetController@addColumn','as'=>'newWorksheetAddColumn']);
	Route::post('/new-worksheet-add-column',['uses'=>'Admin\NewWorksheetController@deleteColumn','as'=>'newWorksheetDeleteColumn']);

	Route::get('/new-worksheet-batch-number',['uses' => 'Admin\NewWorksheetController@showNewStatus','as' => 'showNewStatus']);

	Route::post('/new-worksheet-batch-number',['uses' => 'Admin\NewWorksheetController@changeNewStatus','as' => 'changeNewStatus']);

	Route::get('/new-worksheet-date',['uses' => 'Admin\NewWorksheetController@showNewStatusDate','as' => 'showNewStatusDate']);

	Route::post('/new-worksheet-date',['uses' => 'Admin\NewWorksheetController@changeNewStatusDate','as' => 'changeNewStatusDate']);

	Route::get('/new-worksheet-tracking-data',['uses' => 'Admin\NewWorksheetController@showNewData','as' => 'showNewData']);

	Route::post('/new-worksheet-tracking-data',['uses' => 'Admin\NewWorksheetController@addNewData','as' => 'addNewData']);	

	Route::post('/new-worksheet-id-data',['uses' => 'Admin\NewWorksheetController@addNewDataById','as' => 'addNewDataById']);

	Route::post('/new-worksheet-id-data-delete',['uses' => 'Admin\NewWorksheetController@deleteNewWorksheetById','as' => 'deleteNewWorksheetById']);

	Route::get('/new-worksheet-filter',['uses' => 'Admin\NewWorksheetController@newWorksheetFilter','as' => 'newWorksheetFilter']);

	Route::get('/return-draft/{id}',['uses' => 'Admin\NewWorksheetController@deactivate']);

	// Old Packing Sea
	Route::get('/packing-sea',['uses' => 'Admin\NewWorksheetController@indexPackingSea','as' => 'indexPackingSea']);	

	// New Packing
	Route::get('/new-packing',['uses' => 'Admin\NewPackingController@index','as' => 'indexNewPacking']);

	Route::get('/new-packing-filter',['uses'=>'Admin\NewPackingController@newPackingFilter','as'=>'newPackingFilter']);

	// Invoice
	Route::get('/invoice',['uses' => 'Admin\NewPackingController@indexInvoice','as' => 'indexInvoice']);

	Route::get('/invoice-filter',['uses'=>'Admin\NewPackingController@invoiceFilter','as'=>'invoiceFilter']);

	// Manifest
	Route::get('/manifest',['uses' => 'Admin\NewPackingController@indexManifest','as' => 'indexManifest']);

	Route::get('/manifest-filter',['uses'=>'Admin\NewPackingController@manifestFilter','as'=>'manifestFilter']);

	// Export to Excel
	Route::get('/new-worksheet-export',['uses' => 'Admin\NewWorksheetController@exportExcel','as' => 'exportExcelNew']);

	Route::get('/worksheet-export',['uses' => 'Admin\WorksheetController@exportExcel','as' => 'exportExcel']);

	Route::get('/packing-sea-export',['uses' => 'Admin\NewWorksheetController@exportExcelPackingSea','as' => 'exportExcelPackingSea']);

	Route::get('/new-packing-export',['uses' => 'Admin\NewPackingController@exportExcelNewPacking','as' => 'exportExcelNewPacking']);

	Route::get('/invoice-export',['uses' => 'Admin\NewPackingController@exportExcelInvoice','as' => 'exportExcelInvoice']);

	Route::get('/manifest-export',['uses' => 'Admin\NewPackingController@exportExcelManifest','as' => 'exportExcelManifest']);

	Route::get('/draft-worksheet-export',['uses' => 'Admin\DraftWorksheetController@exportExcel','as' => 'exportExcelDraft']);

	Route::get('/eng-draft-worksheet-export',['uses' => 'Admin\EngDraftWorksheetController@exportExcel','as' => 'exportExcelEngDraft']);

	Route::get('/courier-draft-worksheet-export',['uses' => 'Admin\CourierDraftController@exportExcel','as' => 'exportExcelCourierDraft']);

	Route::get('/courier-eng-draft-worksheet-export',['uses' => 'Admin\CourierEngDraftController@exportExcel','as' => 'exportExcelCourierEngDraft']);

	Route::get('/warehouse-export',['uses' => 'Admin\WarehouseController@exportExcel','as' => 'exportExcelWarehouse']);

	// Front pages
	Route::get('/front-pages',['uses' => 'Admin\FrontPagesController@index','as' => 'frontPages']);

	Route::get('/add-front-page',['uses' => 'Admin\FrontPagesController@addFrontPage','as' => 'addFrontPage']);

	Route::post('/add-front-page',['uses' => 'Admin\FrontPagesController@createFrontPage','as' => 'createFrontPage']);

	Route::get('/update-front-page/{id}',['uses' => 'Admin\FrontPagesController@adminFrontPage','as' => 'adminFrontPage']);

	Route::post('/update-front-page/{id}',['uses' => 'Admin\FrontPagesController@updateFrontPage','as' => 'updateFrontPage']);

	Route::post('/delete-front-page',['uses' => 'Admin\FrontPagesController@deleteFrontPage','as' => 'deleteFrontPage']);
    
    Route::post('ckeditor/image_upload', 'CKEditorController@upload')->name('upload');
});
/*
*  End Admin
*/


/*
*  China admin
*/
Route::get('/admin/china',['uses' => 'Admin\IndexController@chinaIndex','as' => 'adminChinaIndex'])->middleware('can:china_rights');

// China users
Route::get('/admin/china-users',['uses' => 'Admin\ChinaRolesController@index','as' => 'adminChinaUsers'])->middleware('can:china_rights');

Route::get('/admin/china-users/{id}', 'Admin\ChinaRolesController@show')->middleware('can:china_rights');

Route::post('/admin/china-users/{id}',['uses'=>'Admin\ChinaRolesController@update','as'=>'userChinaUpdate'])->middleware('can:china_rights');

Route::post('/admin/china-users',['uses' => 'Admin\ChinaRolesController@destroy','as' => 'deleteChinaUser'])->middleware('can:china_rights');

Route::get('/admin/china-user-add',['uses'=>'Admin\ChinaRolesController@showAdd','as'=>'showChinaUser'])->middleware('can:china_rights');

Route::post('/admin/china-user-add',['uses'=>'Admin\ChinaRolesController@add','as'=>'userChinaAdd'])->middleware('can:china_rights');

// China worksheet
Route::get('/admin/china-worksheet', ['uses' => 'Admin\ChinaWorksheetController@index','as' => 'adminChinaWorksheet'])->middleware('can:china_rights');

Route::get('/admin/china-worksheet/{id}', ['uses' => 'Admin\ChinaWorksheetController@show','as' => 'adminChinaWorksheetShow'])->middleware('can:china_rights');

Route::post('/admin/china-worksheet/{id}',['uses'=>'Admin\ChinaWorksheetController@update','as'=>'chinaWorksheetUpdate'])->middleware('can:china_rights');

Route::post('/admin/china-worksheet',['uses' => 'Admin\ChinaWorksheetController@destroy','as' => 'deleteChinaWorksheet'])->middleware('can:china_rights');

Route::get('/admin/china-worksheet-add',['uses'=>'Admin\ChinaWorksheetController@showAdd','as'=>'showChinaWorksheet'])->middleware('can:china_rights');

Route::post('/admin/china-worksheet-add',['uses'=>'Admin\ChinaWorksheetController@add','as'=>'chinaWorksheetAdd'])->middleware('can:china_rights');

Route::get('/admin/china-worksheet-add-column',['uses'=>'Admin\ChinaWorksheetController@addColumn','as'=>'chinaWorksheetAddColumn'])->middleware('can:china_rights');
Route::post('/admin/china-worksheet-add-column',['uses'=>'Admin\ChinaWorksheetController@deleteColumn','as'=>'chinaWorksheetDeleteColumn'])->middleware('can:china_rights');

Route::get('/admin/china-worksheet-batch-number',['uses' => 'Admin\ChinaWorksheetController@showChinaStatus','as' => 'showChinaStatus'])->middleware('can:china_rights');

Route::post('/admin/china-worksheet-batch-number',['uses' => 'Admin\ChinaWorksheetController@changeChinaStatus','as' => 'changeChinaStatus'])->middleware('can:china_rights');

Route::get('/admin/china-worksheet-date',['uses' => 'Admin\ChinaWorksheetController@showChinaStatusDate','as' => 'showChinaStatusDate'])->middleware('can:china_rights');

Route::post('/admin/china-worksheet-date',['uses' => 'Admin\ChinaWorksheetController@changeChinaStatusDate','as' => 'changeChinaStatusDate'])->middleware('can:china_rights');

// Export to Excel
Route::get('/admin/admin/china-worksheet-export',['uses' => 'Admin\ChinaWorksheetController@exportExcel','as' => 'exportExcelChina'])->middleware('can:china_rights');
/*
*  End China admin
*/


/*
*  Philippines India admin
*/
Route::get('/admin/phil-ind',['uses' => 'Admin\IndexController@philIndIndex','as' => 'adminPhilIndIndex'])->middleware('can:phil_ind_rights');

// Philippines India users
Route::get('/admin/phil-ind-users',['uses' => 'Admin\PhilIndRolesController@index','as' => 'adminPhilIndUsers'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-users/{id}', 'Admin\PhilIndRolesController@show')->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-users/{id}',['uses'=>'Admin\PhilIndRolesController@update','as'=>'userPhilIndUpdate'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-users',['uses' => 'Admin\PhilIndRolesController@destroy','as' => 'deletePhilIndUser'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-user-add',['uses'=>'Admin\PhilIndRolesController@showAdd','as'=>'showPhilIndUser'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-user-add',['uses'=>'Admin\PhilIndRolesController@add','as'=>'userPhilIndAdd'])->middleware('can:phil_ind_rights');

// Philippines India Courier Draft
Route::get('/admin/courier-eng-draft-worksheet-double/{id}',['uses'=>'Admin\CourierEngDraftController@courierEngDraftWorksheetDouble','as'=>'courierEngDraftWorksheetDouble']);

Route::get('/admin/courier-eng-draft-worksheet', ['uses' => 'Admin\CourierEngDraftController@index','as' => 'adminCourierEngDraftWorksheet'])->middleware('can:phil_ind_rights');

Route::get('/admin/courier-eng-draft-worksheet/{id}', ['uses' => 'Admin\CourierEngDraftController@show','as' => 'adminCourierEngDraftWorksheetShow'])->middleware('can:phil_ind_rights');

Route::post('/admin/courier-eng-draft-worksheet/{id}',['uses'=>'Admin\CourierEngDraftController@update','as'=>'courierEngDraftWorksheetUpdate'])->middleware('can:phil_ind_rights');

Route::post('/admin/courier-eng-draft-worksheet',['uses' => 'Admin\CourierEngDraftController@destroy','as' => 'deleteCourierEngDraftWorksheet'])->middleware('can:phil_ind_rights');

Route::get('/admin/courier-eng-draft-worksheet-filter',['uses' => 'Admin\CourierEngDraftController@courierEngDraftWorksheetFilter','as' => 'courierEngDraftWorksheetFilter']);

Route::post('/admin/courier-eng-draft-worksheet-id-data',['uses' => 'Admin\CourierEngDraftController@addCourierEngDraftDataById','as' => 'addCourierEngDraftDataById']);

Route::post('/admin/courier-eng-draft-worksheet-id-data-delete',['uses' => 'Admin\CourierEngDraftController@deleteCourierEngDraftWorksheetById','as' => 'deleteCourierEngDraftWorksheetById']);

Route::get('/admin/courier-eng-draft-check-activate/{id}', ['uses' => 'Admin\CourierEngDraftController@courierEngDraftCheckActivate','as' => 'courierEngDraftCheckActivate'])->middleware('can:phil_ind_rights');

Route::get('/admin/courier-eng-draft-activate/{id}', ['uses' => 'Admin\CourierEngDraftController@courierEngDraftActivate','as' => 'courierEngDraftActivate'])->middleware('can:phil_ind_rights');

Route::get('/admin/courier-eng-draft-admin-activate', ['uses' => 'Admin\CourierEngDraftController@adminActivate','as' => 'adminActivate']);

// Philippines India Worksheet
Route::get('/admin/phil-ind-worksheet', ['uses' => 'Admin\PhilIndWorksheetController@index','as' => 'adminPhilIndWorksheet'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-worksheet/{id}', ['uses' => 'Admin\PhilIndWorksheetController@show','as' => 'adminPhilIndWorksheetShow'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-worksheet/{id}',['uses'=>'Admin\PhilIndWorksheetController@update','as'=>'philIndWorksheetUpdate'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-worksheet',['uses' => 'Admin\PhilIndWorksheetController@destroy','as' => 'deletePhilIndWorksheet'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-worksheet-add-column',['uses'=>'Admin\PhilIndWorksheetController@addColumn','as'=>'philIndWorksheetAddColumn'])->middleware('can:phil_ind_rights');
Route::post('/admin/phil-ind-worksheet-add-column',['uses'=>'Admin\PhilIndWorksheetController@deleteColumn','as'=>'philIndWorksheetDeleteColumn'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-worksheet-batch-number',['uses' => 'Admin\PhilIndWorksheetController@showPhilIndStatus','as' => 'showPhilIndStatus'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-worksheet-batch-number',['uses' => 'Admin\PhilIndWorksheetController@changePhilIndStatus','as' => 'changePhilIndStatus'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-worksheet-date',['uses' => 'Admin\PhilIndWorksheetController@showPhilIndStatusDate','as' => 'showPhilIndStatusDate'])->middleware('can:phil_ind_rights');

Route::post('/admin/phil-ind-worksheet-date',['uses' => 'Admin\PhilIndWorksheetController@changePhilIndStatusDate','as' => 'changePhilIndStatusDate'])->middleware('can:phil_ind_rights');

Route::get('/admin/phil-ind-worksheet-tracking-data',['uses' => 'Admin\PhilIndWorksheetController@showPhilIndData','as' => 'showPhilIndData']);

Route::post('/admin/phil-ind-worksheet-tracking-data',['uses' => 'Admin\PhilIndWorksheetController@addPhilIndData','as' => 'addPhilIndData']);

Route::get('/admin/phil-ind-worksheet-filter',['uses' => 'Admin\PhilIndWorksheetController@philIndWorksheetFilter','as' => 'philIndWorksheetFilter']);

Route::post('/admin/phil-ind-worksheet-id-data',['uses' => 'Admin\PhilIndWorksheetController@addPhilIndDataById','as' => 'addPhilIndDataById']);

Route::post('/admin/phil-ind-worksheet-id-data-delete',['uses' => 'Admin\PhilIndWorksheetController@deletePhilIndWorksheetById','as' => 'deletePhilIndWorksheetById']);

Route::get('/admin/return-eng-draft/{id}',['uses' => 'Admin\PhilIndWorksheetController@deactivate']);

// Packing Eng
Route::get('/admin/packing-eng',['uses' => 'Admin\PhilIndWorksheetController@indexPackingEng','as' => 'indexPackingEng']);

Route::get('/admin/packing-eng-new',['uses' => 'Admin\PhilIndWorksheetController@indexPackingEngNew','as' => 'indexPackingEngNew']);

Route::get('/admin/packing-eng-new-filter',['uses' => 'Admin\PhilIndWorksheetController@packingEngNewFilter','as' => 'packingEngNewFilter']);

// Export to Excel
Route::get('/admin/phil-ind-worksheet-export',['uses' => 'Admin\PhilIndWorksheetController@exportExcel','as' => 'exportExcelPhilInd'])->middleware('can:phil_ind_rights');

Route::get('/admin/packing-eng-export',['uses' => 'Admin\PhilIndWorksheetController@exportExcelPackingEng','as' => 'exportExcelPackingEng']);

Route::get('/admin/packing-eng-new-export',['uses' => 'Admin\PhilIndWorksheetController@exportExcelPackingEngNew','as' => 'exportExcelPackingEngNew']);
/*
*  End Philippines India admin
*/