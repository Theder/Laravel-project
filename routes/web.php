<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\Payment\PaymentController;
use App\Http\Controllers\Panel\Contact\ContactFormController;

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
    return view('welcome');
})->name('welcome');

Route::post('/conact', [ContactFormController::class, 'sendPublic'])->name('landing.contact');

Route::get('/terms', function () {
    return view('services.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('services.privacy');
})->name('privacy');

Route::post('/paypal/notify', [PaymentController::class, 'paypalNotify']);

require __DIR__.'/auth.php';
require __DIR__.'/panel.php';
require __DIR__.'/admin.php';
