<?php

namespace App\Http\Controllers;

use App\Models\Carrinho;
use App\Models\Compras;
use App\Models\Endereco;
use App\Models\InfoPessoais;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PagSeguro\Configuration\Configure;

class Checkout extends Controller
{
    private $_configs;

    public function __construct()
    {
        // $this->_configs = new Configure();
        // $this->_configs->setCharset("UTF-8");
        // $this->_configs->setAccountCredentials(
        //     env('PAGSEGURO_EMAIL'),
        //     env('PAGSEGURO_TOKEN')
        // );
        // $this->_configs->setEnvironment(env('PAGSEGURO_AMBIENTE'));
        // $this->_configs->setLog(
        //     true,
        //     storage_path('logs/pagseguro_'.date('Ymd').'.log')
        // );
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




    // FINALIZAR PAGAMENTO COM CARTÃO
    public function finalPayment(Request $req)
    {
        // DADOS DO USUARIO
        $user = User::where("id", $req->id_user)
                ->get();
        $infoPessoais = InfoPessoais::where("fk_id_usuario", $req->id_user)
            ->get();
        $endereco = Endereco::where("fk_id_usuario", $req->id_user)
            ->get();
        
        $idPedido = $req->id_compra;
        $nomeDividido = explode(" ", $req->name);
        $primeiroNome = $nomeDividido[0];
        $segundoNome = $nomeDividido[1];
        $telefones = explode(" ", $infoPessoais[0]->telefone);
        $ddd = $telefones[0];
        $telefone = $telefones[1];
        $cpf = $req->cpf;
        $niver = date_format(date_create($infoPessoais[0]->nascimento), "d/m/Y");
        $cep = explode("-", $endereco[0]->cep);
        $cep1 = $cep[0];
        $cep2 = $cep[1];
        $cep = $cep1.$cep2;

        // INICIAR A TRANSAÇÃO COM O PAGSEGURO
        $credCard = new \PagSeguro\Domains\Requests\DirectPayment\CreditCard();
        $credCard->setReference("PED_".$idPedido);
        $credCard->setCurrency("BRL");

        for($posicao = 0; $posicao < count($req->items); $posicao++)
        {
            $credCard->addItems()->withParameters(
                $idPedido."_".date("d_m_Y"),
                $req->items[$posicao]["nome_produto"],
                $req->items[$posicao]["quantidade_produto"],
                number_format($req->items[$posicao]["preco_float"], 2, ".", "")
            );
        }

        // INFORMAÇÕES DO COMPRADOR (COLOCAR EMAIL NORMAL EM PRODUÇÃO)
        $credCard->setSender()->setName($primeiroNome." ".$segundoNome);
        $credCard->setSender()->setEmail($primeiroNome."@sandbox.pagseguro.com.br");
        $credCard->setSender()->setHash($req->hash);
        $credCard->setSender()->setPhone()->withParameters($ddd, $telefone);
        $credCard->setSender()->setDocument()->withParameters("CPF", $cpf);

        // COMPRADOR
        $credCard->setShipping()->setAddress()->withParameters(
            $endereco[0]->rua,
            $endereco[0]->numero,
            $endereco[0]->bairro,
            $cep,
            $endereco[0]->cidade,
            $endereco[0]->uf,
            'BRA',
            ''
        );
        // ENTREAGA
        $credCard->setBilling()->setAddress()->withParameters(
            $endereco[0]->rua,
            $endereco[0]->numero,
            $endereco[0]->bairro,
            $cep,
            $endereco[0]->cidade,
            $endereco[0]->uf,
            'BRA',
            ''
        );
        $credCard->setToken($req->token);
        $credCard->setInstallment()->withParameters(
            $req->parcelas,
            number_format($req->valorPorParcela, 2, ".", "")
        );

        // INFORMAÇÕES DO CARTÃO
        $credCard->setHolder()->setName($primeiroNome." ".$segundoNome);
        $credCard->setHolder()->setDocument()->withParameters("CPF", $cpf);
        $credCard->setHolder()->setBirthDate($niver);
        $credCard->setHolder()->setPhone()->withParameters($ddd, $telefone);
        $credCard->setMode("DEFAULT");

        $result = $credCard->register($this->getCrt());

        if($result)
        {
            $status = Compras::where("id", $idPedido)
                ->update([
                    "status" => "Aguardando Pagamento",
                    "data_hora_compra" => date("Y-m-d H:i:s"),
                    "local_atual" => "No depósito"
                ]);

            if($status)
            {
                return response()->json([
                    "success" => true,
                    "result" => $result
                ]);
            }
            else
            {
                return response()->json([
                    "success" => false
                ]);
            }
        }
        else
        {
            return response()->json([
                "success" => false
            ]);
        }
    }




    // FINALIZAR PAGAMENTO COM BOLETO
    public function boletoPayment(Request $req)
    {
        // DADOS DO USUÁRIO
        $user = User::where("id", $req->id_user)
                ->get();
        $infoPessoais = InfoPessoais::where("fk_id_usuario", $req->id_user)
            ->get();
        $endereco = Endereco::where("fk_id_usuario", $req->id_user)
            ->get();
        
        $idPedido = $req->id_compra;
        $nomeDividido = explode(" ", $infoPessoais[0]->nome_usuario);
        $valorTotal = $req->total;
        $vencimento = strtotime("+2 day", strtotime(date("Y-m-d")));
        $vencimento = date("Y-m-d", $vencimento);
        $primeiroNome = $nomeDividido[0];
        $segundoNome = $nomeDividido[1];
        $telefones = explode(" ", $infoPessoais[0]->telefone);
        $ddd = $telefones[0];
        $telefone = $telefones[1];
        // $cpf = $infoPessoais[0]->cpf;
        $cpf = env('CPF_PS');
        $cep = explode("-", $endereco[0]->cep);
        $cep1 = $cep[0];
        $cep2 = $cep[1];
        $cep = $cep1.$cep2;

        $result = [
            "reference_id" => "PED-0000".$idPedido,
            "description" => "Compra por boleto do produto na SPERENET",
            "amount" => [
              "value" => $valorTotal,
              "currency" => "BRL"
            ],
            "payment_method" => [
              "type" => "BOLETO",
              "boleto" => [
                "due_date" => $vencimento,
                "instruction_lines" => [
                    "line_1" => "Pagamento processado para DESC Fatura",
                    "line_2" => "Via PagSeguro"
                ],
                "holder" => [
                    "name" => $primeiroNome." ".$segundoNome,
                    "tax_id" => $cpf,
                    "email" => $primeiroNome.'@sandbox.pagseguro.com.br',
                    "address" => [
                        "street" => $endereco[0]->rua,
                        "number" => $endereco[0]->numero,
                        "locality" => $endereco[0]->bairro,
                        "city" => $endereco[0]->cidade,
                        "region" => $endereco[0]->estado,
                        "region_code" => $endereco[0]->uf,
                        "country" => "Brasil",
                        "postal_code" => $cep
                  ]
                ]
              ]
            ],
            "notification_urls" => [
                ""
            ],
            "metadata" => [
                "Exemplo" => "Aceita qualquer informação",
                "NotaFiscal" => $req->id_user."__"."PED-0000".$idPedido,
                "idComprador" => $req->id_user
            ]
        ];

        $token = env('PAGSEGURO_TOKEN');

        // // INICIAR TRANSAÇÃO
        // $boleto = new \PagSeguro\Domains\Requests\DirectPayment\Boleto;

        // $boleto->setMode('DEFAULT');
        // $boleto->setCurrency("BRL");

        // // PRODUTOS DO CARRINHO
        // for($posicao = 0; $posicao < count($req->items); $posicao++)
        // {
        //     $boleto->addItems()->withParameters(
        //         $idPedido."_".date("d_m_Y"),
        //         $req->items[$posicao]["nome_produto"],
        //         $req->items[$posicao]["quantidade_produto"],
        //         number_format($req->items[$posicao]["preco_float"], 2, ".", "")
        //     );
        // }

        // $boleto->setReference($idPedido."_".date("Y.m.d")."_boleto");

        // $boleto->setExtraAmount(0.00);

        // // DADOS DO COMPRADOR
        // $boleto->setSender()->setName($primeiroNome.' '.$segundoNome);
        // $boleto->setSender()->setEmail($primeiroNome.'@sandbox.pagseguro.com.br');
        // $boleto->setSender()->setPhone()->withParameters($ddd, $telefone);
        // $boleto->setSender()->setDocument()->withParameters('CPF', env('CPF_PS'));
        // $boleto->setSender()->setHash($req->hash);

        // // ENDEREÇO DO COMPRADOR
        // $boleto->setShipping()->setAddress()->withParameters(
        //     $endereco[0]->rua,
        //     $endereco[0]->numero,
        //     $endereco[0]->bairro,
        //     $cep,
        //     $endereco[0]->cidade,
        //     $endereco[0]->uf,
        //     'BRA',
        //     ''
        // );

        // $result = $boleto->register($this->getCrt());


        if($result && $token)
        {
            $status = Compras::where("id", $idPedido)
                ->update([
                    "status" => "Aguardando Pagamento",
                    "data_hora_compra" => date("Y-m-d H:i:s"),
                    "local_atual" => "No depósito"
                ]);

            if($status)
            {
                return response()->json([
                    "success" => true,
                    "result" => $result,
                    "token" => $token
                ]);
            }
            else
            {
                return response()->json([
                    "success" => false
                ]);
            }
        }
        else
        {
            return response()->json([
                "success" => false
            ]);
        }
    } 


    



    // -----------------------------------
    // CARINHO DE COMPRA
    // -----------------------------------
    
    // ADICIONAR PRODUTO NO CARRINHO
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




    // VERIFICAR SE O PRODUTO ESTÁ NO CARRINHO
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
            ->orderBy("id", "desc")
            ->limit(5)
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




    // ESCOLHER MÉTODO DE PAGAMENTO
    public function postPayMethod($metodo, $id_compra)
    {
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
                "success" => false,
                "msg", "Erro no servidor"
            ]);
        }
    }


}
