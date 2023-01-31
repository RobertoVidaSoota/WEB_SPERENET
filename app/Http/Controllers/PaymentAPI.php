<?php

namespace App\Http\Controllers;

use App\Models\Compras;
use App\Models\Endereco;
use App\Models\InfoPessoais;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentAPI extends Controller
{
    private $asaasURL = "https://sandbox.asaas.com/api/v3/";

    // SEQUENCIA DA TRANSAÇÃO
    public function postPayTransaction(Request $req)
    {
        $id_user = $req->id_user;
        $motedo = $req->metodo;
        $id_compra = $req->id_compra;

        // SALVAR MÉTODO DE PAGAMENTO NO BANCO DE DADOS $verIdAsaas["id_asaas"]
        $escolherMetodo = new Checkout();
        $escolherMetodoResponse = $escolherMetodo->postPayMethod($motedo, $id_compra);
        if($escolherMetodoResponse->original["success"] === false)
        {
            return response()->json([
                "success" => false,
            ]);
        }

        // PEGA ID CLIENTE ASAAS DO BANCO DE DADOS
        $verIdAsaas = $this->getIdClient($id_user);
        

        if($verIdAsaas["success"] === false)
        {
            // CRIA CLIENTE NO ASAAS E SETA NO BANCO
            $criarCliente = $this->createClient($id_user);
            if($criarCliente["success"] === false)
            {
                return response()->json([
                    "success" => false,
                ]);
            }
            else
            {
                return response()->json([
                    "success" => true,
                ]);
            }
        }
        else
        {
            return response()->json([
                "success" => true,
                "data" => $verIdAsaas["id_asaas"]
            ]);
        }
        
    }







    // CRIAR O CLIENTE NO ASSAS
    public function createClient($id_user)
    {

        $user = User::where("id", $id_user)
            ->get();
        $infoPessoais = InfoPessoais::where("fk_id_usuario", $id_user)
            ->get();
        $endereco = Endereco::where("fk_id_usuario", $id_user)
            ->get();

        $data = [
            "name" => $infoPessoais[0]->nome_usuario,
            "email" => $user[0]->email,
            "phone" => $infoPessoais[0]->telefone,
            "mobilePhone" => $infoPessoais[0]->telefone,
            "cpfCnpj" => $infoPessoais[0]->cpf,
            "postalCode" => $endereco[0]->cep,
            "address" => $endereco[0]->rua,
            "addressNumber" => $endereco[0]->numero,
            "complement" => "",
            "province" => $endereco[0]->bairro,
            "externalReference" => $id_user,
            "notificationDisabled" => false,
            "additionalEmails" => "",
            "municipalInscription" => "",
            "stateInscription" => "",
            "observations" => ""
            ];

        $data = json_encode($data);
        
        if($user && $infoPessoais && $endereco)
        {
            $cliente = $this->requestAsaas("customers", $data, "POST");

            if($cliente && $cliente["success"] === true)
            {
                $registerID = User::where("id", $id_user)
                ->update([
                    "id_asaas" => $cliente["data"]["id"]
                ]);
            }
        
        }
        
        return [
            "resgister" => $registerID,
            "reponseUserAsaas" => $cliente
        ];
    }



    // VERIFICAR O ID ASSAS DO USUÁRIO NO BANCO
    public function getIdClient($id_user)
    {
        $id = User::where("id", $id_user)
            ->get();

        if($id && $id[0]->id_asaas == "")
        {
            return [
                "success" => false
            ];
        }
        else
        {
            return [
                "success" => true,
                "id_asaas" => $id[0]->id_asaas
            ];
        }
    }



    // PEGAR A COMPRA ATUAL DO USUÁRIO
    public function getIdCompra($id_user)
    {
        $compra = Compras::where("fk_id_usuario", $id_user)
            ->where("status", "carrinho")
            ->get();

        if($compra && count($compra) > 0)
        {
            return [
                "success" => true,
                "compra" => $compra
            ];
        }
        else
        {
            return [
                "success" => false
            ];
        }
    }



    // LISTAR UM CLIENTE DO ASSAS
    public function getOneClient($id_cliente)
    {
        $cliente = $this->requestAsaas(
            "customers".$id_cliente, 
            $id_cliente, 
            'GET'
        );

        if($cliente["success"] === true)
        {
            return [
                "success" => true,
                "cliente" => $cliente
            ];
        }
        else
        {
            return [
                "success" => false
            ];
        }
    }



    // PAGAR COM CARTÃO
    public function payCard(Request $req)
    {
        // PEGA CLIENTE ID DO CLIENTE ASAAS NO BANCO DE DADOS
        $verIdAsaas = $this->getIdClient($req->id_user);
        $user = User::where("id", $req->id_user)->get();
        $adress = Endereco::where("fk_id_usuario", $req->id_user)->get();
        $info = InfoPessoais::where("fk_id_usuario", $req->id_user)->get();
        $desc = "PEDIDO User_".$verIdAsaas["id_asaas"].":\n\n";
        for($i = 0; $i < count($req->items); $i++)
        {
            if($i < 3)
            {
                $desc.="Produto: ".$req->items[$i]["nome_produto"]."\n";
                $desc.="Preço: ".$req->items[$i]["preco_produto"]."\n";
                $desc.="Quantidade: ".$req->items[$i]["quantidade_produto"]."\n\n";
            }else if($i === 3){
                $desc.="Tem mais produtos. confira na sua lista de compras da SPERENET.\n";
            }
        }
        $body = [
            'customer' => $verIdAsaas["id_asaas"],
            'billingType' => 'CREDIT_CARD',
            'dueDate' => date('Y-m-d', strtotime(date('Y-m-d').' + 10 days')),
            'value' => $req->total,
            'description' => $desc,
            'installmentCount' => $req->parcelas,
            'installmentValue' => $req->valorPorParcela,
            'creditCard' => [
              'holderName' => $req->name,
              'number' => $req->number,
              'expiryMonth' => $req->mouthExp,
              'expiryYear' => $req->year,
              'ccv' => $req->cvv
            ],
            'creditCardHolderInfo' => [
              'name' => $req->name,
              'email' => $user[0]->email,
              'cpfCnpj' => $info[0]->cpf,
              'postalCode' => $adress[0]->cep,
              'addressNumber' => "".$adress[0]->numero."",
              'phone' => $info[0]->telefone,
            ],
            'remoteIp' => $req->ip()
        ];
        $body = json_encode($body);
        
        // EFETUAR O PAGAMENTO
        if($body)
        {
            $pagar = $this->requestAsaas("payments", $body, "POST");
            if($pagar && $pagar["success"] === true)
            {
                // $status = Compras::where("id", $req->id_compra)
                // ->update([
                //     "status" => "Aguardando Pagamento",
                //     "data_hora_compra" => date("Y-m-d H:i:s"),
                //     "local_atual" => "No depósito"
                // ]);
                return [
                    "success" => true,
                    "data" => $pagar["data"]
                ];
            }
            else
            {
                return [
                    "success" => false,
                    "data" => $pagar["data"]
                ];
            }
        }
    }



    // PAGAR COM BOLETO
    public function payBoleto()
    {

    }



    // PAGAR COM PIX
    public function payPix()
    {

    }



    // PAGAR PARCELADO
    public function payInstallment()
    {

    }



    // PEGAR NÚMERO DIGITÁVEL DO BOLETO
    public function getNumberLineBoleto()
    {

    }



    // PAGAR PARCELADO
    public function getQRcodePix()
    {

    }



    // MANDAR REQUISIÇÃO PRO ASAAS
    private function requestAsaas($path, $data, $method)
    {
        $curl = curl_init();

        $headers = [
            "Content-Type: application/json",
            "access_token: ".env('ASAAS_TOKEN')
        ];

        if($method === "GET")
        {
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->asaasURL.$path,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers
            ]);
        }
        else
        {
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->asaasURL.$path,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $data
            ]);
        }
        

        $response = curl_exec($curl);
        $response = json_decode($response);
        curl_close($curl);

        if($response)
        {
            return [
                "success" => true,
                "data" => $response
            ];
        }
        else
        {
            return [
                "success" => false,
                "data" => $response
            ];
        }
        
    }
}
