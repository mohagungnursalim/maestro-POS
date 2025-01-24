<?php

use App\Livewire\Dashboard\Product;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Dashboard\Supplier;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/dashboard/products', Product::class)
    ->middleware(['auth'])
    ->name('products');

Route::get('/dashboard/suppliers', Supplier::class)
    ->middleware(['auth'])
    ->name('suppliers');

    

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/'); // Atur redirect sesuai kebutuhan
})->name('logout');







Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
