<?php

use App\Http\Controllers\Authentication\AuthenticationController;
use App\Http\Controllers\Division\DivisionController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\LocationWork\LocationWorkController;
use App\Http\Controllers\Presence\PresenceController;
use App\Http\Controllers\UserManagement\UserManagementController;
use App\Http\Controllers\Warrant\WarrantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('login', [AuthenticationController::class, 'login'])->name('login');
Route::post('authenticate', [AuthenticationController::class, 'authenticate'])->name('authenticate');
Route::get('logout', [AuthenticationController::class, 'logout'])->name('logout');

/**
 * Home Route
 */
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
});

/**
 * Admin Route Access
 */
Route::group(['middleware' => ['role:admin']], function () {
    /**
     * Route Warrant Module
     */
    Route::group(['controller' => WarrantController::class, 'prefix' => 'warrant', 'as' => 'warrant.'], function () {
        Route::get('export/{id}/presence', 'presenceWarrantExport')->name('presenceWarrantExport');
    });
    Route::resource('warrant', WarrantController::class, ['except' => ['index', 'show']])->parameters(['warrant' => 'id']);

    /**
     * Location Work Route
     */
    Route::group(['controller' => LocationWorkController::class, 'prefix' => 'location-work', 'as' => 'location-work.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('location-work', LocationWorkController::class, ['except' => ['show']])->parameters(['location-work' => 'id']);

    /**
     * Division Route
     */
    Route::group(['controller' => DivisionController::class, 'prefix' => 'division', 'as' => 'division.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('division', DivisionController::class)->parameters(['division' => 'id']);

    /**
     * User Management Route
     */
    Route::group(['controller' => UserManagementController::class, 'prefix' => 'user-management', 'as' => 'user-management.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
        Route::get('verification/{id}', 'verification')->name('verification');
        Route::post('verification/{id}', 'verificationUpdate')->name('verificationUpdate');
    });
    Route::resource('user-management', UserManagementController::class, ['except' => ['show']])->parameters(['user-management' => 'id']);
});

/**
 * Admin and Staff Access
 */
Route::group(['middleware' => ['role:admin|staff']], function () {
    /**
     * Route Presence Module
     */
    Route::group(['controller' => PresenceController::class, 'prefix' => 'presence', 'as' => 'presence.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('presence', PresenceController::class)->parameters(['presence' => 'id']);

    /**
     * Location Work Route
     */
    Route::resource('location-work', LocationWorkController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['location-work' => 'id']);

    /**
     * Route Warrant Module
     */
    Route::group(['controller' => WarrantController::class, 'prefix' => 'warrant', 'as' => 'warrant.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('warrant', WarrantController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['warrant' => 'id']);

    /**
     * Route User Management Module
     */
    Route::group(['controller' => UserManagementController::class, 'prefix' => 'user-management', 'as' => 'user-management.'], function () {
        Route::get('face-verification', 'faceVerification')->name('faceVerification');
    });
    Route::resource('user-management', UserManagementController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['user-management' => 'id']);
});
