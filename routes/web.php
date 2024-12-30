<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use App\Http\Controllers\Auth\Register;

/**
 * 'web' middleware applied to all routes
 *
 * @see \App\Providers\Route::mapWebRoutes
 */

Livewire::setScriptRoute(function ($handle) {
    $base = request()->getBasePath();
    return Route::get($base . '/vendor/livewire/livewire/dist/livewire.min.js', $handle);
});


Route::get('/', function(){
    return view('home.index') ;
})->name('home');


Route::middleware(['guest'])->group(function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::get('register', [Register::class, 'create'])->name('register');
        Route::post('register', [Register::class, 'store'])->name('register.store');
    });
});


// Route::get('/test', function () {
//     return route('register.store');
// });