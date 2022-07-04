<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', function () {
    return view('home');
});


Route::get('/get-test-res/{id}', 'TestController@getResultsFromScan')->name('gettestresults');
Route::get('/get-test-mail/{id}', 'TestController@getResultsFromMail')->name('gettestmails');


Route::group(['middleware' => 'auth'], function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::resource('user', 'UserController');


    Route::get('/patient', 'PatientController@index')->name('patient.details');
    Route::get('/create-patient', 'PatientController@create')->name('patient.createPatient');
    Route::get('/create-type', 'TypeController@create')->name('type.createType');
    Route::get('/create-sample', 'SampleController@create')->name('sample.createSample');
    Route::get('/create-price', 'PriceController@create')->name('price.createPrice');
    Route::get('/create-test-pay', 'TestController@payment')->name('payment.createPayment');
    Route::get('/create-test-sample', 'TestController@takesample')->name('sample.takeSample');
    Route::get('/create-test-mix', 'TestController@takemix')->name('mixing.mix');
    Route::get('/create-test-control', 'TestController@getcontrolpage')->name('control.makeControl');
    Route::get('/create-test-result', 'TestController@getresultpage')->name('result.makeResults');
    Route::get('/get-pay-res/{id}', 'TestController@getResultsForPay');


    Route::get('/profile', 'UserController@profile')->name('user.profile');

    Route::post('/profile', 'UserController@postProfile')->name('user.postProfile');

    Route::get('/password/change', 'UserController@getPassword')->name('userGetPassword');

    Route::post('/password/change', 'UserController@postPassword')->name('userPostPassword');
});


Route::group(['middleware' => ['auth', 'role_or_permission:admin|create role|create permission']], function() {

    Route::resource('role', 'RoleController');

    Route::resource('permission', 'PermissionController');


});


Auth::routes();



//////////////////////////////// axios request

Route::get('/getAllPermission', 'PermissionController@getAllPermissions');
Route::post("/postRole", "RoleController@store");
Route::get("/getAllUsers", "UserController@getAll");
Route::get("/getAllRoles", "RoleController@getAll");
Route::get("/getAllPermissions", "PermissionController@getAll");

Route::get("/getAllPatients", "PatientController@getAll");
Route::get("/getAllTypes", "TypeController@getAll");
Route::get("/getSamples", "SampleController@getAll");
Route::get("/getAllPrices", "PriceController@getAll");
Route::get("/getAllPayments", "TestController@getPayments");
Route::get("/getAllSamples", "TestController@getSamplesAvailable");
Route::get("/getAllMixes", "TestController@getMixesAvailable");
Route::get("/getAllControls", "TestController@getControlAvailable");
Route::get("/getAllResults", "TestController@getResultsAvailable");

/////////////axios create user
Route::post('/account/create', 'UserController@store');
Route::put('/account/update/{id}', 'UserController@update');
Route::delete('/delete/user/{id}', 'UserController@delete');
Route::get('/search/user', 'UserController@search');



/////////////axios create Patient
Route::post('/patient/create', 'PatientController@store');
Route::get('/search/patient', 'PatientController@search');
Route::put('/patient/update/{id}', 'PatientController@update');
Route::delete('/delete/patient/{id}', 'PatientController@destroy');


/////////////axios create Type
Route::post('/type/create', 'TypeController@store');
Route::get('/search/type', 'TypeController@search');
Route::put('/type/update/{id}', 'TypeController@update');
Route::delete('/delete/type/{id}', 'TypeController@destroy');



/////////////axios create Samples
Route::post('/sample/create', 'SampleController@store');
Route::get('/search/sample', 'SampleController@search');
Route::put('/sample/update/{id}', 'SampleController@update');
Route::delete('/delete/sample/{id}', 'SampleController@destroy');


/////////////axios create Prices
Route::post('/price/create', 'PriceController@store');
Route::get('/search/price', 'PriceController@search');
Route::put('/price/update/{id}', 'PriceController@update');
Route::delete('/delete/price/{id}', 'PriceController@destroy');

/////////////axios create Payment
Route::post('/payment/create', 'TestController@makePayment');
Route::put('/sample/test/update/{id}', 'TestController@updateSampleTaken');
Route::put('/mix/test/update/{id}', 'TestController@updateMixTaken');
Route::put('/control/test/update/{id}', 'TestController@updateControlTaken');
Route::put('/results/test/update/{id}', 'TestController@updateResultTaken');
Route::put('/mail/test/update/{batch}', 'TestController@sendTestMail');
Route::get('/search/test', 'TestController@search');
Route::get('/search/price', 'PriceController@search');
Route::put('/price/update/{id}', 'PriceController@update');
Route::put('/price/update/{id}', 'PriceController@update');
Route::delete('/delete/price/{id}', 'PriceController@destroy');











