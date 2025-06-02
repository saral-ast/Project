<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::get('/',[DashboardController::class,'dashboard'])
    ->middleware(['verify.shopify'])
    ->name('home');


Route::get('/settings',[DashboardController::class,'settings'])
    ->middleware(['verify.shopify'])
    ->name('settings'); 


// Route::get('/', function () {
//     $shop = Auth::user()->name ?? null;
//     return Inertia::render('welcome',[
//         'shop' => $shop,
//     ]);
// })->middleware(['verify.shopify'])
// ->name('home');



// Route::get('/settings', function () {
    // return Inertia::render('setting');
// })->middleware(['verify.shopify'])->name('settings');


// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('dashboard', function () {
//         return Inertia::render('dashboard');
//     })->name('dashboard');
// });

// require __DIR__.'/settings.php';
// require __DIR__.'/auth.php';