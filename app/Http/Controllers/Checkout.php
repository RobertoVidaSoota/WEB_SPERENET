<?php

namespace App\Http\Controllers;

use App\Models\Carrinho;
use App\Models\Compras;
use App\Models\Endereco;
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
                ->where("fk_id_compras", $carrinhoCheck[0]->id)
                ->get();
            if($produtoCarrinho && count($produtoCarrinho) == 1)
            {
                return response()->json([
                    "msg" => "Produto ja está no carrinho",
                    "carrinho" => "on",
                    "success" => false
                ]);
            }

            $carrinho = Carrinho::create([
                "quantidade_produto" => 1,
                "fk_id_produto" => $id_produto,
                "fk_id_compras" => $carrinhoCheck[0]->id
            ]);

            $idCompra = $carrinhoCheck[0]->id;
        }
        else
        {
            $compras = Compras::create([
                "valor_total" => '0.00',
                "status" => "carrinho",
                "fk_id_usuario" => $user_id,
            ]);

            $compras = Compras::where("status", "carrinho")
            ->where("fk_id_usuario", $user_id)
            ->get();

            $carrinho = Carrinho::create([
                "quantidade_produto" => 1,
                "fk_id_produto" => $id_produto,
                "fk_id_compras" => $compras[0]->id
            ]);

            $idCompra = $compras[0]->id;
        }

        if($carrinhoCheck || $carrinho)
        {
            return response()->json([
                "msg" => "Deu certo",
                "success" => true,
                "id_compra" => $idCompra
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



    // REMOVER DO CARRINHO
    public function postRemoveChart(Request $req)
    {
        $id_produto = $req->id_produto;
        $id_compra = $req->id_compra;

        $carrinhoId = Carrinho::where("fk_id_produto", $id_produto)
            ->where("fk_id_compras", $id_compra)
            ->get();
        
        $carrinho = Carrinho::destroy($carrinhoId[0]->id);
        
        if($carrinho)
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




    // MUDAR QUANTIDADE DO PRODUTO 
    public function postChangeQuantityCart(Request $req)
    {
        $id_produto = $req->id_produto;
        $id_compra = $req->id_compra;
        $direcao = $req->direcao;

        $number = Carrinho::where("fk_id_compras", $id_compra)
            ->where("fk_id_produto", $id_produto)
            ->get();
        
        if($direcao === "frente")
        {
            $alterar = Carrinho::where("fk_id_compras", $id_compra)
            ->where("fk_id_produto", $id_produto)
            ->update([
                "quantidade_produto" => $number[0]->quantidade_produto + 1
            ]);
        }
        elseif($direcao === "traz" && $number[0]->quantidade_produto > 0)
        {
            $alterar = Carrinho::where("fk_id_compras", $id_compra)
            ->where("fk_id_produto", $id_produto)
            ->update([
                "quantidade_produto" => $number[0]->quantidade_produto -1
            ]);
        }
        
        
        if($alterar)
        {
            return response()->json([
                "msg" => "Deu certo",
                "success" => true,
                "valor" => $alterar
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
    public function postCart(Request $req)
    {
        $user_id = $req->id_user;

        $carrinho = DB::select("SELECT carrinho.id, carrinho.quantidade_produto, carrinho.fk_id_produto,	
        carrinho.fk_id_compras, produto.nome_produto, produto.preco_produto,
        produto.link_imagem, compras.valor_total, compras.metodo_pagamento, 
        compras.data_hora_compra, compras.status, compras.local_entrega,compras.local_atual,
        compras.fk_id_usuario, compras.id as id_compra
        FROM produto
        JOIN compras JOIN carrinho
        ON compras.id = carrinho.fk_id_compras
        ON produto.id = carrinho.fk_id_produto
        WHERE compras.fk_id_usuario = '".$user_id."' and compras.status = 'carrinho';");
        
        if($carrinho && count($carrinho) > 0)
        {
            return response()->json([
                "success" => true,
                "carrinho" => $carrinho
            ]);
        }
        else
        {
            return response()->json([
                "success" => false
            ]);
        }
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




    // INICIAR O PAGAMENTO
    public function postPayment(Request $req)
    {
        $id_compra = $req->id_compra;
        $id_user = $req->id_user;
        $valorTotal = $req->valorTotal;

        $endereco = Endereco::where("fk_id_usuario", $id_user)
            ->get();
        $entrega = $endereco[0]->rua.", ".
            $endereco[0]->bairro.", ".
            $endereco[0]->cidade.", ".
            $endereco[0]->uf;
        $payment = Compras::where("id", $id_compra)
            ->where("fk_id_usuario", $id_user)
            ->update([
                "valor_total" => $valorTotal,
                "local_entrega" => $entrega
            ]);

        if($payment && $endereco)
        {
            return response()->json([
                "success" => true,
            ]);
        }
        else
        {
            return response()->json([
                "success" => true,
                "msg", "Erro no servidor"
            ]);
        }
    }




    // MÉTODO DE PAGAMENTO(TESTAR COM APP)
    public function postPayMethod(Request $req)
    {
        $metodo = $req->metodo;
        $id_compra = $req->id_compra;

        $payment = Compras::where("id", $id_compra)
            ->update([
                "metodo_pagamento" => $metodo
            ]);

        if($payment)
        {
            return response()->json([
                "success" => true,
            ]);
        }
        else
        {
            return response()->json([
                "success" => true,
                "msg", "Erro no servidor"
            ]);
        }
    }


}
