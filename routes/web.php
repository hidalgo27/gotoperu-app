<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Page\PageController;
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

//Route::get('/', function () {
//    return view('welcome');
//});

/*Route::get('/packages', [PageController::class, 'packages'])->name('package');*/
Route::get('/destinations', [PageController::class, 'destinations'])->name('destination');
