<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, DashboardController, WarehouseController, StructureController,
    ProductController, InventoryController, UserController, RoleController,
    TreeController, LayoutController, QrController
};

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->middleware('permission:dashboard.view')->name('dashboard');

    Route::get('/warehouses', [WarehouseController::class, 'index'])->middleware('permission:warehouses.view')->name('warehouses.index');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->middleware('permission:warehouses.create')->name('warehouses.store');
    Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->middleware('permission:warehouses.edit')->name('warehouses.update');
    Route::patch('/warehouses/{warehouse}/toggle', [WarehouseController::class, 'toggle'])->middleware('permission:warehouses.edit')->name('warehouses.toggle');
    Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->middleware('permission:warehouses.delete')->name('warehouses.destroy');

    foreach (['aisles', 'racks', 'rows'] as $type) {
        Route::get('/'.$type, [StructureController::class, 'index'])->middleware('permission:'.$type.'.view')->name($type.'.index');
        Route::post('/'.$type, [StructureController::class, 'store'])->middleware('permission:'.$type.'.create')->name($type.'.store');
        Route::put('/'.$type.'/{id}', [StructureController::class, 'update'])->middleware('permission:'.$type.'.edit')->name($type.'.update');
        Route::patch('/'.$type.'/{id}/toggle', [StructureController::class, 'toggle'])->middleware('permission:'.$type.'.edit')->name($type.'.toggle');
        Route::delete('/'.$type.'/{id}', [StructureController::class, 'destroy'])->middleware('permission:'.$type.'.delete')->name($type.'.destroy');
    }

    Route::get('/rows/{row}/detail', [QrController::class, 'detail'])->middleware('permission:rows.view')->name('rows.show');
    Route::get('/rows/{row}/qr', [QrController::class, 'show'])->middleware('permission:qr.view')->name('rows.qr');

    Route::get('/products', [ProductController::class, 'index'])->middleware('permission:products.view')->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->middleware('permission:products.create')->name('products.store');
    Route::post('/products/import-excel', [ProductController::class, 'importExcel'])->middleware('permission:products.create')->name('products.import');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('permission:products.edit')->name('products.update');
    Route::patch('/products/{product}/toggle', [ProductController::class, 'toggle'])->middleware('permission:products.edit')->name('products.toggle');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('permission:products.delete')->name('products.destroy');

    Route::get('/inventory', [InventoryController::class, 'index'])->middleware('permission:inventory.view')->name('inventory.index');
    Route::post('/inventory/assign', [InventoryController::class, 'assign'])->middleware('permission:inventory.assign')->name('inventory.assign');
    Route::post('/inventory/move', [InventoryController::class, 'move'])->middleware('permission:inventory.move')->name('inventory.move');
    Route::post('/inventory/remove', [InventoryController::class, 'remove'])->middleware('permission:inventory.remove')->name('inventory.remove');

    Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('users.update');
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->middleware('permission:users.edit')->name('users.toggle');
    Route::get('/users/{user}/permissions', [UserController::class, 'permissions'])->middleware('permission:users.edit')->name('users.permissions');
    Route::put('/users/{user}/permissions', [UserController::class, 'savePermissions'])->middleware('permission:users.edit')->name('users.permissions.save');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles.view')->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('roles.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.edit')->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('roles.destroy');

    Route::get('/tree', TreeController::class)->middleware('permission:tree.view')->name('tree.index');
    Route::get('/layouts', [LayoutController::class, 'index'])->middleware('permission:layout.view')->name('layouts.index');
    Route::get('/layouts/{warehouse}', [LayoutController::class, 'show'])->middleware('permission:layout.view')->name('layouts.show');
    Route::post('/layouts/{warehouse}', [LayoutController::class, 'save'])->middleware('permission:layout.design')->name('layouts.save');
});
