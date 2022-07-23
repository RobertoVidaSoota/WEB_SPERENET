<?php

namespace App\Http\Controllers;

use App\Models\Carrinho;
use App\Models\Compras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PagSeguro\Configuration\Configure;

class Checkout extends Controller
{
    private $_configs;

    public function __construct()
    {
        $this->_configs = new Configure();
        $this->_configs->setCharset("UTF-8");
        $this->_configs->setAccountCredentials(
            env('PAGSEGURO_EMAIL'),
            env('PAGSEGURO_TOKEN')
        );
        $this->_configs->setEnvironment(env('PAGSEGURO_AMBIENTE'));
        $this->_configs->setLog(
            true,
            storage_path('logs/pagseguro_'.date('Ymd').'.log')
        );
    }

    public function getCrt()
    {
        return $this->_configs->getAccountCredentials();
    }


    public function getSessionPagseguro(Request $req)
    {
        $data = [];
        $sessionCode = \PagSeguro\Services\Session::create(
            $this->getCrt()
        );
        $IDSession = $sessionCode->getResult();
        $data["sessionID"] = $IDSession;

        return response()->json([
            "pag_id" => $data
        ]);
    }


    // FINALIZAR PAGAMENTO (NO CALL)
    public function finalPayment(Request $req)
    {
        $idPedido = rand(2, 999);

        $credCard = new \PagSeguro\Domains\Requests\DirectPayment\CreditCard();
        $credCard->setReference("PED_".$idPedido);
        $credCard->setCurrency("BRL");

        $credCard->addItems()->withParameters(
            $idPedido.$req->items,
            $req->items, // <--- NOME DO PEDIDO
            1,  // <--- COPIAS
            // VALOR DE CADA PRODUTO NO CARRINHO
            number_format($req->total, 2, ".", "")
        );

        // $user = $req->user_name::user(); 

        $credCard->setSender()->setName("Roberto". " ". "Carlos");
        $credCard->setSender()->setEmail(env("PAGSEGURO_EMAIL_SD"));
        $credCard->setSender()->setHash($req->hash);
        $credCard->setSender()->setPhone()->withParameters(71, 87728789);
        $credCard->setSender()->setDocument()->withParameters("CPF", "11111111111");

        $credCard->setShipping()->setAddress()->withParameters(
            'Av Optuco',
            '1234',
            'Vale do Silêncio',
            '22775559',
            'Goiânia',
            'GO',
            'BRA',
            'terreo'
        );
        $credCard->setBilling()->setAddress()->withParameters(
            'Av Optuco',
            '1234',
            'Vale do Silêncio',
            '22775559',
            'Goiânia',
            'GO',
            'BRA',
            'terreo'
        );
        $credCard->setToken($req->token);
        $credCard->setInstallment()->withParameters(
            // PARCELA ATUAL
            $req->installments,
            // VALOR DA PARCELAS
            number_format($req->total, 2, ".", ""), 
            false
        );

        $credCard->setHolder()->setName("Roberto". " "."Carlos");
        $credCard->setHolder()->setDocument()->withParameters("CPF", "11111111111");
        $credCard->setHolder()->setBirthDate("03/01/1990");
        $credCard->setHolder()->setPhone()->withParameters(71, 87728789);
        $credCard->setMode("DEFAULT");

        $result = $credCard->register($this->getCrt());

        if($result)
        {
            echo "TUDO CERTO";
        }
        else
        {
            echo "DEU ERRADO";
        }
    }
    



    // -----------------------------------
    // CARINHO DE COMPRA
    // -----------------------------------
    
    // ADICIONAR NO CARRINHO
    public function postAddCart(Request $req)
    {
        $user_id = $req->user_id;
        $id_produto = $req->id_produto;
        
        $carrinhoCheck = Compras::where("status", "carrinho")
            ->where("fk_id_usuario", $user_id)
            ->get();

        if($carrinhoCheck && count($carrinhoCheck) == 1)
        {
            $produtoCarrinho = Carrinho::where("fk_id_produto", $id_produto)
                ->get();
            if($produtoCarrinho && count($produtoCarrinho) == 1)
            {
                return response()->json([
                    "msg" => "Produto ja está no carrinho",
                    "carrinho" => "on",
                    "success" => false
                ]);
            }
        }
        else
        {
            $compras = Compras::create([
                "valor_total" => '0.00',
                "status" => "carrinho",
                "fk_id_usuario" => $user_id,
            ]);
        }
        
        $compraCriada = Compras::where("status", "carrinho")
            ->where("fk_id_usuario", $user_id)
            ->get();
        $carrinho = Carrinho::create([
            "quantidade_produto" => 1,
            "fk_id_produto" => $id_produto,
            "fk_id_compras" => $compraCriada[0]->id
        ]);

        if($carrinhoCheck && $compraCriada && $carrinho)
        {
            return response()->json([
                "msg" => "Deu certo",
                "success" => true,
                "id_compra" => $compraCriada[0]->id
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Deu Errado",
                "success" => false
            ]);
        }
    } 




    // VERIFICAR CARRINHO
    public function postVerifyProdChart(Request $req)
    {
        $id_produto = $req->id_produto;
        $id_compra = $req->id_compra;

        $produto = Carrinho::where("fk_id_produto", $id_produto)
            ->where("fk_id_compras", $id_compra)
            ->get();
        
        if($produto && count($produto) == 0)
        {
            return response()->json([
                "msg" => "Deu certo",
                "success" => true
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Deu errado",
                "success" => false
            ]);
        }
    }




    // PEGAR PRODUTOS DO CARRINHO 
    public function postChangeQuantityCart(Request $req)
    {
        $user_id = $req->user_id;
        

        $carrinho = DB::table("compras")
            ->get();
    }





    // PEGAR PRODUTOS DO CARRINHO 
    public function postCart(Request $req)
    {
        $user_id = $req->user_id;
        

        $carrinho = DB::table("compras")
            ->get();
    }




    // LISTAR COMPRAS
    public function postPurchases(Request $req)
    {
        $user_id = $req->id_user;

        $purchases = Compras::where("fk_id_usuario", "=", $user_id)
            ->get();    

        for ($i = 0; $i < count($purchases); $i++)
        {
            $products = DB::table("carrinho")
                ->select("carrinho.id", "carrinho.quantidade_produto",
                "carrinho.fk_id_produto", "carrinho.fk_id_compras",
                "produto.nome_produto", "produto.preco_produto",
                "produto.link_imagem", "compras.local_entrega", "compras.local_atual",
                "compras.status", "compras.fk_id_usuario", "compras.data_hora_compra",
                "compras.metodo_pagamento", "compras.link_boleto")
                ->join("compras", "compras.id", "carrinho.fk_id_compras")
                ->join("produto", "produto.id", "carrinho.fk_id_produto")
                ->where("compras.id" , "=", $purchases[$i]->id)
                ->get();

            $purchases[$i]["produtos"] = $products;
        }

        if($purchases)
        {
            return response()->json([
                "compras" => $purchases
            ]);
        }
        else
        {
            return response()->json([
                "msg", "Erro no servidor"
            ]);
        }
    }




    // MÉTODO DE PAGAMENTO(TESTAR COM APP)
    public function postPayMethod(Request $req)
    {
        $pix = $req->metodo_pix;
        $boleto = $req->metodo_boleto;
        $cartao = $req->metodo_cartao;
    }

}
