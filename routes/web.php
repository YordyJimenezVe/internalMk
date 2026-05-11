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
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});




Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Containers Management
    Route::group(['middleware' => ['can:manage roles']], function () {
        Route::get('/container', 'App\Http\Controllers\ContainersController@index')->name('container');
        Route::get('/container/show/{id}', 'App\Http\Controllers\ContainersController@show')->name('showContainer');
        Route::get('/container/add', 'App\Http\Controllers\ContainersController@create')->name('createcontainer');
        Route::post('/container/store', 'App\Http\Controllers\ContainersController@store')->name('storeContainer');
        Route::get('/container/edit/{id}', 'App\Http\Controllers\ContainersController@edit')->name('editContainer');
        Route::post('/container/update/{id}', 'App\Http\Controllers\ContainersController@update')->name('updateContainer');
        Route::delete('/container/delete/{id}', 'App\Http\Controllers\ContainersController@destroy')->name('deleteContainer');
    });

    // Inventario Read Access
    Route::get('/inventario/show/{id}', [\App\Http\Controllers\InventarioController::class, 'show'])->name('showInventario');
    Route::get('/inventario/print/{id}', [\App\Http\Controllers\InventarioController::class, 'printLabel'])->name('printInventario');
    Route::get('/inventario', [\App\Http\Controllers\InventarioController::class, 'index'])->name('inventario');

    Route::group(['middleware' => ['can:view partida']], function () {
        Route::get('/autopart', 'App\Http\Controllers\AutopartsController@index')->name('autopart');
        Route::get('/camara', 'App\Http\Controllers\CamarasController@index')->name('camara');
    });

    // Inventario Write Access
    Route::group(['middleware' => ['can:manage partida']], function () {
        Route::get('/inventario/add', 'App\Http\Controllers\InventarioController@create')->name('createInventario');
        Route::get('/inventario/edit/{id}', 'App\Http\Controllers\InventarioController@edit')->name('editInventario');
        Route::post('/inventario/update/{id}', 'App\Http\Controllers\InventarioController@update')->name('updateInventario');
        Route::post('/inventario/store', 'App\Http\Controllers\InventarioController@store')->name('storeInventario');
        Route::delete('/inventario/delete/{id}', 'App\Http\Controllers\InventarioController@destroy')->name('deleteInventario');

        // Aliases for autoparts/camaras write operations if needed
        Route::get('/autopart/add', 'App\Http\Controllers\InventarioController@create')->name('createAutopart');
        Route::get('/autopart/edit/{id}', 'App\Http\Controllers\InventarioController@edit')->name('editAutopart');
        Route::get('/camara/add', 'App\Http\Controllers\InventarioController@create')->name('createCamara');
        Route::get('/camara/edit/{id}', 'App\Http\Controllers\InventarioController@edit')->name('editCamara');
    });

    Route::get('/maintenance', 'App\Http\Controllers\MaintenancesController@index')->name('maintenance');
    Route::get('/maintenance/add', 'App\Http\Controllers\MaintenancesController@create')->name('createMaintenance');
    Route::get('/maintenance/show/{id}', 'App\Http\Controllers\MaintenancesController@show')->name('maintenance.show');
    Route::get('/maintenance/pdf/{id}', 'App\Http\Controllers\MaintenancesController@pdf')->name('maintenance.pdf');
    Route::get('/maintenance/edit/{id}', 'App\Http\Controllers\MaintenancesController@edit')->name('editMaintenance');
    Route::post('/maintenance/update/{id}', 'App\Http\Controllers\MaintenancesController@update')->name('updateMaintenance');
    Route::post('/maintenance/updateAccesorios/{id}', 'App\Http\Controllers\MaintenancesController@updateAccesorios')->name('updateAccesorios');
    Route::post('/maintenance/store', 'App\Http\Controllers\MaintenancesController@store')->name('storeMaintenance');
    Route::delete('/maintenance/delete/{id}', 'App\Http\Controllers\MaintenancesController@destroy')->name('deleteMaintenance');
    Route::post('/maintenance/add', 'App\Http\Controllers\MaintenancesController@getInventario')->name('getInventario');

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

    Route::get('/reports', 'App\Http\Controllers\ReportsController@index')->name('reports');
    Route::get('/report/reporteExcel/{tipo}/{caso}/{termino?}', 'App\Http\Controllers\ReportsController@exportExcel')->name('reporteExcel');
    Route::get('/report/reportePdf/{tipo}/{caso}/{termino?}', 'App\Http\Controllers\ReportsController@exportPdf')->name('reportePdf');
    Route::get('/report/print-labels/{tipo}', 'App\Http\Controllers\ReportsController@bulkPrintLabels')->name('printLabels');

    Route::get('/bitacora', 'App\Http\Controllers\BitacorasController@index')->name('bitacora.index');

    Route::get('/scan', [\App\Http\Controllers\ScanController::class, 'index'])->name('scan.index')->middleware(['can:access scan']);
    Route::post('/scan/process', [\App\Http\Controllers\ScanController::class, 'process'])->name('scan.process');

    // Users Management
    Route::group(['middleware' => ['can:manage users']], function () {
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/temporary-permission', [\App\Http\Controllers\UserController::class, 'assignTemporaryPermission'])->name('users.temp_permission');
    });

    // Roles Management
    Route::group(['middleware' => ['can:manage roles']], function () {
        Route::get('/roles', [\App\Http\Controllers\RolesController::class, 'index'])->name('roles.index');
        Route::post('/roles', [\App\Http\Controllers\RolesController::class, 'store'])->name('roles.store');
        Route::put('/roles/{id}', [\App\Http\Controllers\RolesController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{id}', [\App\Http\Controllers\RolesController::class, 'destroy'])->name('roles.destroy');
    });
    Route::get('/maintenance/item/{id}', [\App\Http\Controllers\ScanController::class, 'directToMaintenance'])->name('maintenance.item');

    Route::get('/debug-me', function() {
        return response()->json([
            'user' => auth()->user()->only(['id', 'name', 'email', 'rol']),
            'roles' => auth()->user()->getRoleNames(),
            'permissions' => auth()->user()->getAllPermissions()->pluck('name'),
        ]);
    });
});


