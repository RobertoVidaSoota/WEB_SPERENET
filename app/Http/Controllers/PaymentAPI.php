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
        // PEGA DO BANCO DE DADOS
        $verIdAsaas = $this->getIdClient($req->id_user);

        return response()->json([
            "success" => true,
            "data" => $verIdAsaas
        ]);

        // if($verIdAsaas["success"] === false)
        // {
        //     // CRIA CLIENTE NO ASAAS E SETA NO BANCO
        //     $criarCliente = $this->createClient($req->id_user);
            
        //     return response()->json([
        //         "success" => true,
        //         "data" => $criarCliente
        //     ]); 
        // }
        // else
        // {
        //     return response()->json([
        //         "success" => true,
        //         "data" => $verIdAsaas
        //     ]);
        //     // PEGA CLIENTE REGISTRADO NO ASAAS
        //     // $pegarClienteCriado = $this->getOneClient($verIdAsaas["id_asaas"]);
        // }
        
    }



    // CRIAR O CLIENTE NO ASSAS
    public function createClient($id_user)
    {

        $user = User::where("id", $id_user)
            ->get();
        $infoPessoais = InfoPessoais::where("fk_id_usuario", $id_user)
            ->get();
        $endereco = Endereco::where("fk_id_usuario", $id_user)
            ->get();;

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
        
        // if($user && $infoPessoais && $endereco)
        // {
        //     $cliente = $this->requestAsaas("customers", $data, "POST");

        //     if($cliente && $cliente["success"] === true)
        //     {
        //         $registerID = User::where("id", $id_user)
        //         ->update([
        //             "id_asaas" => $cliente["data"]["id"]
        //         ]);
        //     }
        
        // }
        dd($data);
        // return [
        //     "resgister" => $registerID,
        //     "reponseUserAsaas" => $cliente
        // ];
    }



    // VERIFICAR O ID ASSAS DO USUÁRIO
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

        if($cliente)
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
    public function payCard()
    {

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
            "Content-Type" => "application/json",
            "access_token" => env('ASAAS_TOKEN')
        ];

        if($method === "GET" && $path === "")
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

        curl_close($curl);

       
        return [
            "success" => true,
            "data" => $response
        ];
    }
}
