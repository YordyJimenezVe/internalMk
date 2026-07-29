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

    Route::get('/inventario/precio-pendiente', [\App\Http\Controllers\InventarioController::class, 'precioPendienteIndex'])->name('inventario.precio_pendiente');
    Route::post('/inventario/precio-pendiente/update/{id}', [\App\Http\Controllers\InventarioController::class, 'updatePrecioPendiente'])->name('inventario.precio_pendiente.update');

    // Containers Management
    Route::group(['middleware' => ['check_permission:manage roles']], function () {
        Route::get('/container', 'App\Http\Controllers\ContainersController@index')->name('container');
        Route::get('/container/show/{id}', 'App\Http\Controllers\ContainersController@show')->name('showContainer');
        Route::get('/container/add', 'App\Http\Controllers\ContainersController@create')->name('createcontainer');
        Route::post('/container/store', 'App\Http\Controllers\ContainersController@store')->name('storeContainer');
        Route::get('/container/edit/{id}', 'App\Http\Controllers\ContainersController@edit')->name('editContainer');
        Route::post('/container/update/{id}', 'App\Http\Controllers\ContainersController@update')->name('updateContainer');
        Route::delete('/container/delete/{id}', 'App\Http\Controllers\ContainersController@destroy')->name('deleteContainer');
    });

    // Inventario & Scan Read Access
    Route::group(['middleware' => ['check_permission:view partida']], function () {
        Route::get('/inventario', [\App\Http\Controllers\InventarioController::class, 'index'])->name('inventario');
        Route::get('/inventario/show/{id}', [\App\Http\Controllers\InventarioController::class, 'show'])->name('showInventario');
        Route::get('/inventario/print/{id}', [\App\Http\Controllers\InventarioController::class, 'printLabel'])->name('printInventario');
        Route::get('/inventario/vehicle-type', [\App\Http\Controllers\InventarioController::class, 'getVehicleType'])->name('inventario.vehicle_type');
        Route::get('/autopart', 'App\Http\Controllers\AutopartsController@index')->name('autopart');
        Route::get('/camara', 'App\Http\Controllers\CamarasController@index')->name('camara');
        
        Route::get('/scan', [\App\Http\Controllers\ScanController::class, 'index'])->name('scan.index');
    });

    // Global Scanner Process (accessible by any authenticated user, redirects based on role authorization)
    Route::post('/scan/process', [\App\Http\Controllers\ScanController::class, 'process'])->name('scan.process');

    // Inventario Write Access
    Route::group(['middleware' => ['check_permission:manage partida']], function () {
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

    // Mantenimiento Read Access
    Route::group(['middleware' => ['check_permission:view maintenance']], function () {
        Route::get('/maintenance', 'App\Http\Controllers\MaintenancesController@index')->name('maintenance');
        Route::get('/maintenance/history', 'App\Http\Controllers\MaintenancesController@history')->name('maintenance.history');
        Route::get('/maintenance/show/{id}', 'App\Http\Controllers\MaintenancesController@show')->name('maintenance.show');
        Route::get('/maintenance/pdf/{id}', 'App\Http\Controllers\MaintenancesController@pdf')->name('maintenance.pdf');
    });

    // Mantenimiento Write Access
    Route::group(['middleware' => ['check_permission:create maintenance']], function () {
        Route::get('/maintenance/add', 'App\Http\Controllers\MaintenancesController@create')->name('createMaintenance');
        Route::get('/maintenance/edit/{id}', 'App\Http\Controllers\MaintenancesController@edit')->name('editMaintenance');
        Route::post('/maintenance/update/{id}', 'App\Http\Controllers\MaintenancesController@update')->name('updateMaintenance');
        Route::post('/maintenance/updateAccesorios/{id}', 'App\Http\Controllers\MaintenancesController@updateAccesorios')->name('updateAccesorios');
        Route::post('/maintenance/store', 'App\Http\Controllers\MaintenancesController@store')->name('storeMaintenance');
        Route::delete('/maintenance/delete/{id}', 'App\Http\Controllers\MaintenancesController@destroy')->name('deleteMaintenance');
        Route::post('/maintenance/add', 'App\Http\Controllers\MaintenancesController@getInventario')->name('getInventario');
        Route::get('/maintenance/item/{id}', [\App\Http\Controllers\ScanController::class, 'directToMaintenance'])->name('maintenance.item');
        Route::post('/maintenance/revert-error/{id}', 'App\Http\Controllers\MaintenancesController@revertError')->name('maintenance.revert_error');
        
        // Rutas de repuestos y servicios dinámicos de taller
        Route::post('/maintenance/{id}/items', 'App\Http\Controllers\MaintenancesController@storeItem')->name('maintenance.store_item');
        Route::delete('/maintenance/items/{itemId}', 'App\Http\Controllers\MaintenancesController@deleteItem')->name('maintenance.delete_item');
        Route::post('/maintenance/items/{itemId}/outflow', 'App\Http\Controllers\MaintenancesController@registerOutflow')->name('maintenance.register_outflow');
        Route::post('/maintenance/items/{itemId}/return', 'App\Http\Controllers\MaintenancesController@registerReturn')->name('maintenance.register_return');
        Route::post('/maintenance/items/{itemId}/update', 'App\Http\Controllers\MaintenancesController@updateItem')->name('maintenance.update_item');
    });

    // Bandeja de Conciliación Contable de Taller
    Route::group(['middleware' => ['check_permission:manage billing']], function () {
        Route::get('/maintenance/conciliacion', 'App\Http\Controllers\MaintenancesController@conciliacionIndex')->name('maintenance.conciliacion');
        Route::post('/maintenance/items/{itemId}/conciliar', 'App\Http\Controllers\MaintenancesController@conciliarItem')->name('maintenance.conciliar_item');
        Route::post('/maintenance/items/{itemId}/revert-conciliar', 'App\Http\Controllers\MaintenancesController@revertConciliarItem')->name('maintenance.revert_conciliar_item');
    });

    // In-app Notification System Routes
    Route::get('/notifications/unread', 'App\Http\Controllers\NotificationController@getUnread')->name('notifications.unread');
    Route::post('/notifications/read-all', 'App\Http\Controllers\NotificationController@markAllAsRead')->name('notifications.read_all');
    Route::post('/notifications/{id}/read', 'App\Http\Controllers\NotificationController@markAsRead')->name('notifications.read');
    
    // Panel de Administración de Notificaciones (Solo Admin)
    Route::get('/admin/notifications', 'App\Http\Controllers\NotificationController@index')->name('admin.notifications.index');
    Route::post('/admin/notifications/broadcast', 'App\Http\Controllers\NotificationController@sendBroadcast')->name('admin.notifications.broadcast');
    Route::post('/admin/notifications/toggle-switch/{id}', 'App\Http\Controllers\NotificationController@toggleSetting')->name('admin.notifications.toggle_setting');

    // Billing Read Access
    Route::group(['middleware' => ['check_permission:view billing']], function () {
        Route::get('/billing', 'App\Http\Controllers\BillingsController@index')->name('billing');
        Route::get('/billing/pdf/{id}', 'App\Http\Controllers\BillingsController@pdf')->name('billing.pdf');
        Route::get('/billing-requests', 'App\Http\Controllers\BillingRequestController@index')->name('billing.requests.index');
    });

    // Billing Write Access
    Route::group(['middleware' => ['check_permission:manage billing']], function () {
        Route::get('/billing/add/{id}', 'App\Http\Controllers\BillingsController@create')->name('createBilling');
        Route::get('/billing/edit/{bill}', 'App\Http\Controllers\BillingsController@edit')->name('editBilling');
        Route::post('/billing/store', 'App\Http\Controllers\BillingsController@store')->name('storeBilling');
        Route::post('/billing/update/{id}', 'App\Http\Controllers\BillingsController@update')->name('updateBilling');
        Route::delete('/billing/delete/{id}', 'App\Http\Controllers\BillingsController@destroy')->name('deleteBilling');
        Route::get('/billing/return/{id}', 'App\Http\Controllers\BillingsController@return')->name('returnBilling');
        Route::post('/billing/returnSubmit/{id}', 'App\Http\Controllers\BillingsController@returnSubmit')->name('billing.returnSubmit');

        Route::post('/billing-requests/process', 'App\Http\Controllers\BillingRequestController@process')->name('billing.requests.process');
        Route::put('/billing-requests/update/{id}', 'App\Http\Controllers\BillingRequestController@update')->name('billing.requests.update');
        Route::delete('/billing-requests/delete/{id}', 'App\Http\Controllers\BillingRequestController@destroy')->name('billing.requests.destroy');
        Route::post('/settings/update-utility', [\App\Http\Controllers\DashboardController::class, 'updateUtility'])->name('settings.update_utility');
    });

    // Billing requests creation (open to store requests, e.g. for mechanics)
    Route::post('/billing-requests/store', 'App\Http\Controllers\BillingRequestController@store')->name('billing.requests.store');

    // Reports Access
    Route::group(['middleware' => ['check_permission:view reports']], function () {
        Route::get('/reports', 'App\Http\Controllers\ReportsController@index')->name('reports');
        Route::get('/report/reporteExcel/{tipo}/{caso}/{termino?}', 'App\Http\Controllers\ReportsController@exportExcel')->name('reporteExcel');
        Route::get('/report/reportePdf/{tipo}/{caso}/{termino?}', 'App\Http\Controllers\ReportsController@exportPdf')->name('reportePdf');
        Route::get('/report/print-labels/{tipo}', 'App\Http\Controllers\ReportsController@bulkPrintLabels')->name('printLabels');

        // Thermal Label Generator Routes
        Route::get('/generar-qr-etiquetas', [\App\Http\Controllers\InventarioController::class, 'generatorDashboard'])->name('labels.generator');
        Route::get('/generar-qr-etiquetas/imprimir/logo-info', [\App\Http\Controllers\InventarioController::class, 'printLogoInfoLabel'])->name('labels.print.logo-info');
        Route::get('/generar-qr-etiquetas/imprimir/qr-code', [\App\Http\Controllers\InventarioController::class, 'printQrCodeLabel'])->name('labels.print.qr-code');
        Route::get('/generar-qr-etiquetas/imprimir/hoja-completa', [\App\Http\Controllers\InventarioController::class, 'printFullPageGrid'])->name('labels.print.full-page');
        Route::get('/generar-qr-etiquetas/imprimir/por-contenedor', [\App\Http\Controllers\InventarioController::class, 'printContainerLabels'])->name('labels.print.container');
    });

    // Bitacora Access
    Route::group(['middleware' => ['check_permission:view bitacora']], function () {
        Route::get('/bitacora', 'App\Http\Controllers\BitacorasController@index')->name('bitacora.index');
    });

    // Users Management
    Route::group(['middleware' => ['check_permission:manage users']], function () {
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/temporary-permission', [\App\Http\Controllers\UserController::class, 'assignTemporaryPermission'])->name('users.temp_permission');
    });

    // Roles Management
    Route::group(['middleware' => ['check_permission:manage roles']], function () {
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



