<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\SpreadsheetController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->middleware(['web'])->group(function () {
    Route::post('/register-user', [AuthController::class, 'registerUser'])->name('api.register-user');
    Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('api.authenticate');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('api.showRegistrationForm');
    Route::post('/register', [AuthController::class, 'registerUser'])->name('api.register');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user-profile', [AuthController::class, 'getUserProfile'])->name('api.user-profile');
    Route::get('/user/profile', [UserController::class, 'profile']);
});

Route::get('/public-route', function () {
    return response()->json(['message' => 'This is a public route accessible to all']);
});


Route::middleware('auth:sanctum')->prefix('admin')->group(function(){
    Route::post('/admin/saveUser', [UserManagementController::class, 'saveUser'])->name('admin.saveUser');
    Route::get('/admin/user/{id}', [UserManagementController::class, 'getEditUserData'])->name('admin.getEditUserData');
    Route::delete('admin/users/delete/{id}', [UserManagementController::class, 'deleteUser'])->name('api.admin.deleteUser');
    Route::post('/admin/user/update', [UserManagementController::class, 'updateUserData'])->name('admin.updateUserData');
    Route::get('/users/fetchUsers', [UserManagementController::class, 'fetchUsers'])->name('api.admin.fetchUsers');
            Route::post('/users/store', [UserManagementController::class, 'storeUser'])->name('api.admin.storeUser');
            Route::post('admin/users/import', [SpreadSheetController::class, 'importUsers'])->name('api.admin.importUsers');
            Route::get('admin/users/export', [SpreadSheetController::class, 'exportUsers'])->name('api.admin.exportUsers');


});


