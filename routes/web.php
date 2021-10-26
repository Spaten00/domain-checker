<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DomainController;
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

//Route::get('/', function () {
//    return view('home')->with('domains', Domain::paginate(20)->withQueryString());
//})->name('home');

Route::get('/', [DomainController::class, 'show'])
    ->name('home');

Route::get('/expiring', [DomainController::class, 'showExpiring'])
    ->name('domain.expiring');

Route::get('/search/', [DomainController::class, 'showSearch'])
    ->name('domain.search');

Route::middleware(['auth'])->group(function () {
    Route::post('create-bill', [BillController::class, 'store'])
        ->name('bill.store');

    Route::post('create-contract', [ContractController::class, 'store'])
        ->name('contract.store');

    Route::post('/contract/update/{contract}', [ContractController::class, 'update'])
        ->name('contract.update');

    Route::post('/bill/update/{bill}', [BillController::class, 'update'])
        ->name('bill.update');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';

Auth::routes();
