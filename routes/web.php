<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return view('/auth/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');  // Show profile view
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');  // Edit profile form
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');  // Update profile
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');  // Delete profile

    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Route::get('/dashboard/issued-assets', [DashboardController::class, 'getIssuedAssets'])->name('dashboard.issued-assets');
    
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/assign-assets', [UserController::class, 'assignAssets'])->name('users.assignAssets');
    // Route::get('/users/{user}/print', [UserController::class, 'print'])->name('users.print');
    // Route::patch('/users/{user}/resign', [UserController::class, 'resign'])->name('users.resign');
    // Route::get('/users/{user}/check-issued-assets', [UserController::class, 'checkIssuedAssets'])->name('users.checkIssuedAssets');
    // routes/web.php
Route::get('users/{user}/check-issued-assets', [UserController::class, 'checkIssuedAssets'])->name('users.checkIssuedAssets');
Route::patch('users/{user}/resign', [UserController::class, 'resign'])->name('users.resign');
// Add this to your `web.php`
Route::get('/users/{user}/assets/issue', [UserController::class, 'issueAssetPage'])->name('users.assets.issue');
Route::get('/users/{user}/print', [UserController::class, 'print'])->name('users.print');
Route::get('/users/{user}/email-pdf', [UserController::class, 'emailPdf'])->name('users.emailPdf');


    
    
    Route::resource('assets', AssetController::class);
    Route::get('/assets/{asset}/clone', [AssetController::class, 'clone'])->name('assets.clone');
    Route::get('/assets/{assetTag}/history', [AssetController::class, 'history'])->name('assets.history');


    Route::resource('companies', CompanyController::class);

    Route::resource('device_types', DeviceTypeController::class);

    Route::resource('brands', BrandController::class);


   
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

require __DIR__.'/auth.php';
