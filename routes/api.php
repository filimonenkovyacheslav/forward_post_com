<?php

use Illuminate\Http\Request;
use Http\Controllers\FrontController;
use Http\Controllers\API\NewWorksheetController;
use Http\Controllers\API\PhilIndWorksheetController;
use Http\Controllers\RuPostalTrackingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('new_worksheet', 'API\NewWorksheetController');
Route::resource('phil_ind_worksheet', 'API\PhilIndWorksheetController');
Route::post('/login', 'API\NewWorksheetController@login')->name('login');
Route::get('/get-status/{tracking}', 'API\NewWorksheetController@getStatus')->name('getStatus');
Route::get('/get-status-eng/{tracking}', 'API\PhilIndWorksheetController@getStatusEng')->name('getStatusEng');
// add Tracking By Phone
Route::post('/add-tracking-by-phone', 'API\NewWorksheetController@addTrackingByPhone')->name('addTrackingByPhone');
Route::post('/add-tracking-by-phone-eng', 'API\PhilIndWorksheetController@addTrackingByPhoneEng')->name('addTrackingByPhoneEng');
Route::post('/add-tracking-by-phone-with-order', 'API\NewWorksheetController@addTrackingByPhoneWithOrder')->name('addTrackingByPhoneWithOrder');
Route::post('/add-tracking-by-phone-with-order-eng', 'API\PhilIndWorksheetController@addTrackingByPhoneWithOrderEng')->name('addTrackingByPhoneWithOrderEng');
// add new shipment
/*Route::post('/add-new-shipment', 'API\NewWorksheetController@addNewShipment')->name('addNewShipment');
Route::post('/add-new-shipment-eng', 'API\PhilIndWorksheetController@addNewShipmentEng')->name('addNewShipmentEng');*/
// add batch number
Route::post('/add-batch-number', 'API\NewWorksheetController@addBatchNumber')->name('addBatchNumber');
Route::post('/add-batch-number-eng', 'API\PhilIndWorksheetController@addBatchNumberEng')->name('addBatchNumberEng');
// add pallet number
Route::post('/add-pallet-number', 'API\NewWorksheetController@addPalletNumber')->name('addPalletNumber');
Route::post('/add-pallet-number-eng', 'API\PhilIndWorksheetController@addPalletNumberEng')->name('addPalletNumberEng');
// add to courier draft
Route::post('/add-courier-data', 'API\BaseController@addCourierData')->name('addCourierData');
// get shipment qty by batch number
Route::get('/get-shipment-qty-by-batch-number', 'API\BaseController@getShipmentQtyByBatchNumber')->name('getShipmentQtyByBatchNumber');

// https://union-il.com/
Route::get('/forward-parcel-form', 'FrontController@forwardParcelAdd')->name('forwardParcelAdd');
Route::get('/forward-tracking-form', 'FrontController@getForwardTracking')->name('getForwardTracking');
Route::get('/forward-check-phone', 'FrontController@forwardCheckPhone')->name('forwardCheckPhone');

// Check phone in draft
Route::post('/parcel-form-check', 'FrontController@checkAvailabilityPhone');

// Ru Post
Route::get('/ru-postal-tracking', 'RuPostalTrackingController@updateStatus')->name('updateStatus');
Route::get('/ru-postal-tracking-from-user/{barcode}', 'RuPostalTrackingController@updateStatusFromUser')->name('updateStatusFromUser');

// Form with signature
Route::post('/add-to-temp-table', 'SignedDocumentController@addToTempTable')->name('addToTempTable');
Route::get('/get-from-temp-table/{id}', 'SignedDocumentController@getFromTempTable')->name('getFromTempTable');
Route::post('/add-signed-ru-form', 'SignedDocumentController@addSignedRuForm')->name('addSignedRuForm');
Route::post('/add-signed-eng-form', 'SignedDocumentController@addSignedEngForm')->name('addSignedEngForm');
Route::post('/signature-page', 'SignedDocumentController@setSignature')->name('setSignature');
Route::post('/check-temp-table', 'SignedDocumentController@checkTempTable')->name('checkTempTable');
Route::post('/form-update-after-cancel', 'SignedDocumentController@formUpdateAfterCancel')->name('formUpdateAfterCancel');
Route::post('/check-phone-api',['uses' => 'SignedDocumentController@checkPhoneApi','as' => 'checkPhoneApi']);
Route::post('/phil-ind-check-phone-api',['uses' => 'SignedDocumentController@philIndCheckPhoneApi','as' => 'philIndCheckPhoneApi']);

// Courier tasks for application
Route::get('/get-courier-tasks', 'API\BaseController@getCourierTasks')->name('getCourierTasks');
Route::post('/update-task-status-box', 'API\BaseController@updateTaskStatusBox')->name('updateTaskStatusBox');
Route::post('/add-data-with-tracking', 'API\BaseController@addDataWithTracking')->name('addDataWithTracking');
Route::post('/add-new-signed-form', 'API\BaseController@addNewSignedForm')->name('addNewSignedForm');
Route::post('/add-duplicate-signed-form', 'API\BaseController@addDuplicateSignedForm')->name('addDuplicateSignedForm');
Route::post('/add-tracking-list', 'API\BaseController@addTrackingList')->name('addTrackingList');
Route::get('/get-checklist', 'API\BaseController@getChecklist')->name('getChecklist');

// PDF for simple users
Route::get('/add-new-signed-form-for-user', 'API\BaseController@addNewSignedFormForUser');
Route::get('/add-new-signed-form-for-user-eng', 'API\BaseController@addNewSignedFormForUserEng');

// Crone
Route::get('/ru-postal-tracking-cron', 'RuPostalTrackingController@cronScript')->name('cronScript');