<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\
{
    Account,
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
Route::post("/post_login_user", [AuthApi::class, "loginUser"]);

Route::post("/post_register_user", [AuthApi::class, "registerUser"]);

Route::post("/post_new_password", [AuthApi::class, "newPassword"]);


// CONTA
Route::get("/get_info_account", [Account::class, "getInfoAccount"]);

Route::post("/post_change_info_account", [
    Account::class, "changeInfoAccount"
]);

Route::post("/post_change_email", [Account::class, "changeEmail"]);

Route::post("/post_change_password", [Account::class, "changePassword"]);

Route::post("/post_confirm_email_twofa", [
    Account::class, "confirmEmailTwoFA"
]);

Route::post("/post_change_user_notification", [
    Account::class, "changeUserNotification"
]);








// Route::group(["middleware" => "auth:api"], function () {

    

// });
