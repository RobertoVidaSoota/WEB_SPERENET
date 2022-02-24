<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\
{
    Auth
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

// Route::get('/teste', function () {
//     return response()->json(["eu" => "22"]);
// });

Route::get("/get_user_auth", [Auth::class, "index"]);

Route::group(["middleware" => "auth:api"], function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

});
