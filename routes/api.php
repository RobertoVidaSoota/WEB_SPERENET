<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\
{
    Account,
    AuthUser,
    Checkout,
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


Route::post("/post_login_user", [AuthUser::class, "loginUser"]);//

Route::post("/post_register_user", [AuthUser::class, "registerUser"]);//

Route::post("/post_send_email_new_password", [
    AuthUser::class, "sendEmailNewPassword"]);//

Route::post("/post_new_password", [AuthUser::class, "newPassword"]);//



// ---------------- CONTA ----------------


Route::get("/get_info_account/{id}", [Account::class, "getInfoAccount"]);//

Route::post("/post_change_info_account", [
    Account::class, "changeInfoAccount"
]);//

Route::post("/post_change_email", [
    Account::class, "changeEmail"
]);//

Route::post("/post_change_password", [
    Account::class, "changePassword"
]);//

// Route::post("/post_confirm_email_twofa", [
//     Account::class, "confirmEmailTwoFA"
// ]);

Route::post("/post_change_user_notification", [
    Account::class, "changeUserNotification"
]);//

Route::post("/post_get_user_notification", [
    Account::class, "getUserNotification"
]);//



// ---------------- LOJA ----------------

// PEGAR UM PRODUTO
Route::post("/post_one_product", [
    Shop::class, "postOneProduct"
]);

// NOVOS PRODUTOS
Route::get("/get_new_products", [
    Shop::class, "getNewProducts"
]);//

// PRODUTOS POPULARES
Route::get("/get_pop_products", [
    Shop::class, "getPopProducts"
]);//

// PRODUTOS POPULARES
Route::get("/get_more_products", [
    Shop::class, "getMoreProducts"
]);//

// PESQUISAR
Route::post("/post_search", [
    Shop::class, "postSearch"
]);//

// LISTAR DESEJOS
Route::post("/post_wishlist", [
    Shop::class, "postWishlist"
]);//

// ADICIONAR EM DESEJOS
Route::post("/add_wishlist", [
    Shop::class, "addWishlist"
]);

// REMOVER DESEJO
Route::post("remove_wishlist", [
    Shop::class, "removeWishlist"
]);

// VERIFICAR SE TA NOS DESEJOS
Route::post("check_wishlist", [
    Shop::class, "checkWishlist"
]);

// PÁGINA DO PRODUTO
Route::post("/post_page_product", [
    Shop::class, "postPageProduct"
]);//

// POSTAR COMENTÁRIO
Route::post("/post_coments", [
    Shop::class, "postComents"
]);//


// ---------------- PAGAMENTO ----------------

Route::post("/post_purchases", [
    Checkout::class, "postPurchases"
]);

// CARINHO DE COMPRA(TESTAR COM O APP)
Route::post("/post_cart", [
    Checkout::class, "postCart"
]);

// MÉTODO DE PAGAMENTO
Route::post("/post_pay_method", [
    Checkout::class, "postPayMethod"
]);//


// PEGAR SESSÃO DA TRANSAÇÃO
Route::get("/get_session_pagseguro", [
    Checkout::class, "getSessionPagseguro"
]);//

// CONFIRMAR PAGAMENTO
Route::post("/post_final_payment", [
    Checkout::class, "finalPayment"
]);//

