<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('workers/waiters', 'API\WorkersControllerAPI@waiters')->middleware('auth:api');
Route::post("workers/photo", "API\WorkersControllerAPI@storePhoto")->middleware('auth:api');
Route::post("workers/{id}/photo", "API\WorkersControllerAPI@updatePhoto")->middleware('auth:api');
Route::post('workers/password/{token}', 'API\WorkersControllerAPI@password');
Route::post('workers/{id}/block', 'API\WorkersControllerAPI@block')->middleware('auth:api');
Route::post('workers/{id}/unblock', 'API\WorkersControllerAPI@unblock')->middleware('auth:api');
Route::put('workers/{id}/password', 'API\WorkersControllerAPI@updatePassword')->middleware('auth:api');
Route::post('workers/{id}/password', 'API\WorkersControllerAPI@checkPassword')->middleware('auth:api');
Route::put('workers/{worker}/startshift', 'API\WorkersControllerAPI@startShift')->middleware('auth:api');
Route::put('workers/{worker}/endshift', 'API\WorkersControllerAPI@endShift')->middleware('auth:api');
Route::resource('workers', 'API\WorkersControllerAPI')->middleware('auth:api');
Route::post('workers/{id}/changestatus', 'API\WorkersControllerAPI@changeStatus')->middleware('auth:api');


Route::post("menus/photo", "API\MenusControllerAPI@createPhoto")->middleware('auth:api');;
Route::post("menus/{id}", "API\MenusControllerAPI@updatePhoto")->middleware('auth:api');;
Route::resource("menus", "API\MenusControllerAPI")->middleware('auth:api');

Route::get("tables/available","API\TablesControllerAPI@availableTables")->middleware('auth:api');
Route::resource("tables", "API\TablesControllerAPI")->middleware('auth:api');


Route::post("meals/{id}/notpaid", "API\MealsControllerAPI@notPaid")->middleware('auth:api');;
Route::post("meals/{id}/paid", "API\MealsControllerAPI@paid")->middleware('auth:api');;
Route::resource("meals", "API\MealsControllerAPI")->middleware('auth:api');;

Route::post("invoices/{id}/download", "API\InvoicesControllerAPI@download")->middleware('auth:api');
Route::resource("invoices", "API\InvoicesControllerAPI")->middleware('auth:api');
Route::post("invoices/{id}", "API\InvoicesControllerAPI@update")->middleware('auth:api');
Route::get("invoices/{invoice}/items", "API\InvoicesControllerAPI@invoiceItems")->middleware('auth:api');

Route::get('statistics/waiters', "API\StatisticsControllerAPI@getWaiterStatistics")->middleware('auth:api');
Route::get('statistics/cooks', "API\StatisticsControllerAPI@getCookStatistics")->middleware('auth:api');

Route::post("login", "API\UsersControllerAPI@login");
Route::post("logout", "API\UsersControllerAPI@logout")->middleware('auth:api');
