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

Route::get('packages', [PageController::class, 'packages'])->name('package');
Route::get('packages-top', [PageController::class, 'packages_top'])->name('package.top');
Route::get('packages-offers', [PageController::class, 'packages_offers'])->name('package.offers');
Route::get('packages-offers/{country}', [PageController::class, 'offers_country'])->name('package.offers.country');
Route::get('hotels-destinations', [PageController::class, 'hotels_destinations'])->name('hotels');
Route::get('destinations-hotels', [PageController::class, 'destinations_hotels'])->name('destinations_hotels');

Route::get('{latam}-travel-packages/{url}', [PageController::class, 'packages_detail'])->name('packages.detail');
//Route::get('ecuador-travel-packages/{url}', [PageController::class, 'packages_detail'])->name('packages.detail');
Route::get('team', [PageController::class, 'team'])->name('team');
Route::get('team/{team}', [PageController::class, 'team_show'])->name('team.show');
Route::get('team/destino/{destino}', [PageController::class, 'team_destino'])->name('team.show.destino');
Route::get('team/country/{country}', [PageController::class, 'team_country'])->name('team.show.country');
Route::get('faq', [PageController::class, 'faq'])->name('faq');
Route::get('pais', [PageController::class, 'pais'])->name('pais');
Route::get('pais/{country}', [PageController::class, 'country'])->name('destination.country');
Route::get('destinations/{pais}', [PageController::class, 'destinations'])->name('destination');
Route::get('destinations/region/{destinos}', [PageController::class, 'destinations_show'])->name('destination.show.show');
Route::get('pais/packages/{pais}', [PageController::class, 'packages_by_country'])->name('destination.packages.pais.show');
Route::get('pais/datalle/{country}', [PageController::class, 'CountryDetails'])->name('destination..propiedades');
//Route::get('pais/region/{region}', [PageController::class, 'RegionDetails'])->name('destination.region.propiedades');

Route::get('categorias', [PageController::class, 'category'])->name('category');
Route::get('categorias/{categoria}', [PageController::class, 'categories_show'])->name('category.show.show');
Route::get('categorias/{pais}/{categoria}', [PageController::class, 'packages_by_country_and_category'])->name('category.pais.show');

// routes/web.php o api.php
Route::get('/countries/{pais:url}/categories', [PageController::class, 'country_categories_show'])->name('category.pais');;


Route::post('/formulario-diseno', [PageController::class, 'formulario_diseno'])->name('formulario_diseno');

Route::get('blog', [PageController::class, 'blog'])->name('blog');
Route::get('/blog/{post}', [PageController::class, 'blog_show'])->name('blog.show');

Route::get('/inquires', [PageController::class, 'list_inquires'])->name('list_inquires');

Route::post('/store/inquire', [PageController::class, 'store_inquire'])->name('store_inquire');

Route::put('/update/inquire/{id}', [PageController::class, 'update_inquire'])->name('update_inquire');

Route::get('/inquires/filter', [PageController::class, 'filter_inquires'])->name('filter_inquires');

Route::post('/send/inquire', [PageController::class, 'sendInquire'])->name('sendInquire');
