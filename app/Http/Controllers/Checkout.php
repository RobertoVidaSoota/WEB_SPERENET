<?php

namespace App\Http\Controllers;

use App\Models\Carrinho;
use Illuminate\Http\Request;

class Checkout extends Controller
{
    
    // CARINHO DE COMPRA(TRAVADO)
    public function postCart(Request $req)
    {
        $user_id = $req->user_id;
        $carrinho = Carrinho::with("compras")
            ->where("fk_id_usuario", "=", $user_id)
            ->get();
    }


    // MÉTODO DE PAGAMENTO
    public function postPayMethod(Request $req)
    {
        $pix = $req->metodo_pix;
        $boleto = $req->metodo_boleto;
        $cartao = $req->metodo_cartao;
    }



    // FORMULÁRIO DE PAGAMENTO (PROVISORIO)
    public function postPayment(Request $req)
    {

    }



    // REASTREAMENTO DE PRODUTO
    public function postTrackProduct(Request $req)
    {
        $user_id = $req->user_id;
        $carrinho = Carrinho::with("compras")
            ->where("fk_id_usuario", "=", $user_id)
            ->get();
    }
}
