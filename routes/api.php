<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\
{
    AuthApi
};

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

// AUTENTICAÇÃO
Route::post("/get_user_auth", [AuthApi::class, "loginUser"]);










// Route::group(["middleware" => "auth:api"], function () {

    

// });
