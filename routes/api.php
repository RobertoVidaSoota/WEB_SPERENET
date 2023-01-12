<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\
{
    Account,
    AuthUser,
    Checkout,
    PaymentAPI,
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
]);//

// PEGAR PRODUTOS PORCATEGORIA
Route::post("/post_catogory_product", [
    Shop::class, "postCatogoryProduct"
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

// LISTA DE COMPRAS
Route::post("/post_purchases", [
    Checkout::class, "postPurchases"
]);//

// ADICIONAR PRODTO NO CARRINHO
Route::post("/post_add_cart", [
    Checkout::class, "postAddCart"
]);//

// REMOVER DO CARRINHO
Route::post("/post_remove_chart", [
    Checkout::class, "postRemoveChart"
]);//

// VERIFICAR CARRINHO
Route::post("/post_verify_prod_chart", [
    Checkout::class, "postVerifyProdChart"
]);//


// MUDAR QUANTIDADE DO PRODUTO
Route::post("/post_change_quantity_cart", [
    Checkout::class, "postChangeQuantityCart"
]);//

// CARINHO DE COMPRA
Route::post("/post_cart", [
    Checkout::class, "postCart"
]);//

// INICIAR PAGAMENTO
Route::post("/post_payment", [
    Checkout::class, "postPayment"
]);

// MÉTODO DE PAGAMENTO
Route::post("/post_pay_method", [
    Checkout::class, "postPayMethod"
]);//



// PAGAR COM ASAAS
Route::post("/post_pay_transaction", [
    PaymentAPI::class, "postPayTransaction"
]);//






//
// COM PAGSEGURO
// 

// PEGAR SESSÃO DA TRANSAÇÃO
// Route::get("/get_session_pagseguro", [
//     Checkout::class, "getSessionPagseguro"
// ]);//

// // CONFIRMAR PAGAMENTO COM CARTÃO
// Route::post("/post_final_payment", [
//     Checkout::class, "finalPayment"
// ]);

// Route::post("/post_boleto_payment", [
//     Checkout::class, "boletoPayment"
// ]);

