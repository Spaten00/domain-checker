<?php

use App\Http\Controllers\BillController;
use App\Models\Domain;
use Illuminate\Support\Facades\Route;

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
    return view('home')->with('domains', Domain::paginate(15)->withQueryString());
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::post('create-bill', [BillController::class, 'store'])
        ->name('new-bill.store');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';

Auth::routes();
