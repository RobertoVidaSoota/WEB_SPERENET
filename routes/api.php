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
/* 
    SELECT * FROM produtos ORDER BY id DESC LIMIT 10
*/

// PRODUTOS POPULARES
/* 
    SELECT * FROM produtos 
    INNER JOIN comentarios ON 
    comentarios.fk_id_produtos = produtos.id LIMIT 3

    SELECT COUNT(estrelas) FROM comentarios WHERE 
    fk_id_produto = $id
*/

// PESQUISAR
/*
    SELECT * FROM produtos WHERE nome_produto LIKE
    '%texto%' LIMIT 20
*/

// LISTAR DESEJOS
/*
    SELECT * FROM usuario_desejos 
    INNER JOIN usuario 
    INNER JOIN produto
    ON usuario_desejos.fk_id_usuario = $user_id
    ON usuario_desejos.fk_id_produto = produto.id

    SELECT AVG(estrelas) FROM comentarios WHERE 
    fk_id_produto = $id
*/

// PÁGINA DO PRODUTO
/*
    SELECT * FROM produtos WHERE produtos.id = $produto_id

    SELECT * FROM especificacoes WHERE 
    especificacoes.fk_id_produto = $produto_id

    SELECT * FROM comentarios
    INNER JOIN produtos 
    INNER JOIN users
    ON comentarios.fk_id_produtos = $produtos_id
    ON comentarios.fk_id_users = users.id
*/





// Route::group(["middleware" => "auth:api"], function () {

    

// });
