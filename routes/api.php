<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\
{
    Account,
    AuthApi,
    Shop
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

// Route::group(["middleware" => "auth:api"], function () {
// });




// ---------------- AUTENTICAÇÃO ----------------
Route::post("/post_login_user", [AuthApi::class, "loginUser"]);

Route::post("/post_register_user", [AuthApi::class, "registerUser"]);

Route::post("/post_new_password", [AuthApi::class, "newPassword"]);



// ---------------- CONTA ----------------
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



// ---------------- LOJA ----------------



// NOVOS PRODUTOS
Route::get("/get_new_products", [
    Shop::class, "getNewProducts"
]);

// PRODUTOS POPULARES
Route::get("/post_pop_products", [
    Shop::class, "postPopProducts"
]);

// PESQUISAR
Route::post("/post_search", [
    Shop::class, "postSearch"
]);

// LISTAR DESEJOS
Route::post("/post_wishlist", [
    Shop::class, "postWishlist"
]);

// PÁGINA DO PRODUTO
Route::post("/post_page_product", [
    Shop::class, "postPageProduct"
]);





