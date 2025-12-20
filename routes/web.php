<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        //'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/container', 'App\Http\Controllers\ContainersController@index')->name('container');
Route::get('/container/add', 'App\Http\Controllers\ContainersController@create')->name('createcontainer');
Route::post('/container/store', 'App\Http\Controllers\ContainersController@store')->name('storeContainer');
Route::get('/container/edit/{id}', 'App\Http\Controllers\ContainersController@edit')->name('editContainer');
Route::post('/container/update/{id}', 'App\Http\Controllers\ContainersController@update')->name('updateContainer');
Route::delete('/container/delete/{id}', 'App\Http\Controllers\ContainersController@destroy')->name('deleteContainer');

Route::get('/partida', 'App\Http\Controllers\PartidasController@index')->name('partida');
Route::get('/partida/add', 'App\Http\Controllers\PartidasController@create')->name('createPartida');
Route::get('/partida/edit/{id}', 'App\Http\Controllers\PartidasController@edit')->name('editPartida');
Route::get('/partida/show/{id}', 'App\Http\Controllers\PartidasController@show')->name('showPartida');
Route::post('/partida/update/{id}', 'App\Http\Controllers\PartidasController@update')->name('updatePartida');
Route::post('/partida/store', 'App\Http\Controllers\PartidasController@store')->name('storePartida');
Route::delete('/partida/delete/{id}', 'App\Http\Controllers\PartidasController@destroy')->name('deletePartida');

Route::get('/maintenance', 'App\Http\Controllers\MaintenancesController@index')->name('maintenance');
Route::get('/maintenance/add', 'App\Http\Controllers\MaintenancesController@create')->name('createMaintenance');
Route::get('/maintenance/edit/{id}', 'App\Http\Controllers\MaintenancesController@edit')->name('editMaintenance');
Route::post('/maintenance/update/{id}', 'App\Http\Controllers\MaintenancesController@update')->name('updateMaintenance');
Route::post('/maintenance/updateAccesorios/{id}', 'App\Http\Controllers\MaintenancesController@updateAccesorios')->name('updateAccesorios');
Route::post('/maintenance/store', 'App\Http\Controllers\MaintenancesController@store')->name('storeMaintenance');
Route::delete('/maintenance/delete/{id}', 'App\Http\Controllers\MaintenancesController@destroy')->name('deleteMaintenance');
Route::post('/maintenance/add', 'App\Http\Controllers\MaintenancesController@getPartida')->name('getPartida');

Route::get('/billing', 'App\Http\Controllers\BillingsController@index')->name('billing');
Route::get('/billing/add/{id}', 'App\Http\Controllers\BillingsController@create')->name('createBilling');
Route::get('/billing/edit/{bill}', 'App\Http\Controllers\BillingsController@edit')->name('editBilling');
Route::post('/billing/store', 'App\Http\Controllers\BillingsController@store')->name('storeBilling');
Route::post('/billing/update/{id}', 'App\Http\Controllers\BillingsController@update')->name('updateBilling');
Route::delete('/billing/delete/{id}', 'App\Http\Controllers\BillingsController@destroy')->name('deleteBilling');
Route::get('/billing/return/{id}', 'App\Http\Controllers\BillingsController@return')->name('returnBilling');
Route::post('/billing/returnSubmit/{id}', 'App\Http\Controllers\BillingsController@returnSubmit')->name('returnSubmit');

Route::get('/billing-requests', 'App\Http\Controllers\BillingRequestController@index')->name('billing.requests.index');
Route::post('/billing-requests/store', 'App\Http\Controllers\BillingRequestController@store')->name('billing.requests.store');
Route::post('/billing-requests/process', 'App\Http\Controllers\BillingRequestController@process')->name('billing.requests.process');
Route::put('/billing-requests/update/{id}', 'App\Http\Controllers\BillingRequestController@update')->name('billing.requests.update');
Route::delete('/billing-requests/delete/{id}', 'App\Http\Controllers\BillingRequestController@destroy')->name('billing.requests.destroy');
Route::get('/billing/pdf/{id}', 'App\Http\Controllers\BillingsController@pdf')->name('billing.pdf');

Route::get('/autopart', 'App\Http\Controllers\AutopartsController@index')->name('autopart');
Route::get('/autopart/add', 'App\Http\Controllers\PartidasController@create')->name('createAutopart');
Route::get('/autopart/edit/{id}', 'App\Http\Controllers\PartidasController@edit')->name('editAutopart');

Route::get('/camara', 'App\Http\Controllers\CamarasController@index')->name('camara');
Route::get('/camara/add', 'App\Http\Controllers\PartidasController@create')->name('createCamara');
Route::get('/camara/edit/{id}', 'App\Http\Controllers\PartidasController@edit')->name('editCamara');

Route::get('/reports', 'App\Http\Controllers\ReportsController@index')->name('reports');
Route::get('/report/reporteExcel/{tipo}/{caso}/{termino?}', 'App\Http\Controllers\ReportsController@exportExcel')->name('reporteExcel');
Route::get('/report/reportePdf/{tipo}/{caso}/{termino?}', 'App\Http\Controllers\ReportsController@exportPdf')->name('reportePdf');

Route::get('/bitacora', 'App\Http\Controllers\BitacorasController@index')->name('bitacora.index');
Route::get('/history', 'App\Http\Controllers\HistorysController@index')->name('history.index');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
