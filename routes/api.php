<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RoleAndPermissionController;
use Illuminate\Auth\Events\Login;

Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::middleware(['auth:api'])->group(function () {
    Route::get('/me', [LoginController::class, 'me']);
    Route::post('/logout', [LoginController::class, 'logout']);
});

Route::prefix('rubro')
    ->middleware(['auth:api', 'panel:rubro'])
    ->group(function () {

        Route::post('/roles/create', [RoleAndPermissionController::class, 'createRoles']);
        Route::post('/permissions/create', [RoleAndPermissionController::class, 'createPermissions']);
        Route::get('/roles', [RoleAndPermissionController::class, 'getRolesRubro']);
        Route::get('/permissions', [RoleAndPermissionController::class, 'getPermissionsRubro']);
        Route::post('/roles/assign-permissions', [RoleAndPermissionController::class, 'assignPermissionsToRole']);
        Route::post('/roles/assign-user', [RoleAndPermissionController::class, 'assignRoleToUser']);
    });

Route::prefix('client')
    ->middleware(['auth:api', 'panel:client'])
    ->group(function () {
        // rutas cliente
    });

Route::prefix('company')
    ->middleware(['auth:api', 'panel:company'])
    ->group(function () {

        Route::post('/test', [LoginController::class, 'test']);

        Route::post('/roles/create', [RoleAndPermissionController::class, 'createRoles']);
        Route::post('/permissions/create', [RoleAndPermissionController::class, 'createPermissions']);
        Route::post('/roles/assign-permissions', [RoleAndPermissionController::class, 'assignPermissionsToRole']);
        Route::post('/roles/assign-user', [RoleAndPermissionController::class, 'assignRoleToUser']);
    });
