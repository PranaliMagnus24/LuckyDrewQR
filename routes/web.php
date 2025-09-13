<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;

// Route::get('/', function () {
//     return view('welcome');
// });

///Home Controller
Route::get('/', [HomeController::class, 'home'])->name('home');
// plain route (keep existing)
Route::get('/scanner/form/{unique_id}', [HomeController::class, 'showScannerPage'])
    ->name('scanner.show');

// base64 route reusing same method
Route::get('/scanner/form/b64/{b64}', [HomeController::class, 'showScannerPage'])
    ->name('scanner.show.encoded');

Route::post('/scanner/submit', [HomeController::class, 'submit'])
    ->name('scanner.submit');
Route::post('/scanner/exists-bulk', [HomeController::class, 'existsBulk'])
    ->name('scanner.existsBulk');
Route::get('/scanner/exists/{unique_id}', [HomeController::class, 'exists'])
    ->name('scanner.exists');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
