<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserControler;
use App\http\Controllers\API\HomeController;
use App\Http\Controllers\API\SearchController;

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

Route::get('/', function () {
    return view('welcome');
});
Route::post("/register",[UserControler::class,"register"]);
Route::post("/login",[UserControler::class,"login"]);
Route::post("/getcars",[HomeController::class,"getCars"]);
Route::post("/addcars",[HomeController::class,"addCars"]);
Route::post("/addShops",[HomeController::class,"addShops"]);
Route::post("/selectcar",[HomeController::class,"selectCar"]);
Route::post("/search",[SearchController::class,"search"]);
Route::post("/order",[SearchController::class,"orders"]);