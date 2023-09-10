<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Page\PageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('packages-top', [PageController::class, 'packages_top'])->name('package.top');
Route::get('packages-offers', [PageController::class, 'packages_offers'])->name('package.offers');
Route::get('hotels-destinations', [PageController::class, 'hotels_destinations'])->name('hotels');

Route::get('latam-travel-packages/{url}', [PageController::class, 'packages_detail'])->name('packages.detail');
Route::get('team', [PageController::class, 'team'])->name('team');
Route::get('pais', [PageController::class, 'pais'])->name('pais');
Route::get('destinations/{pais}', [PageController::class, 'destinations'])->name('destination');
