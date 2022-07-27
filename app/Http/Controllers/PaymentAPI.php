<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\InfoPessoais;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentAPI extends Controller
{
    private $asaasURL = "https://sandbox.asaas.com/api/v3/";

    // SEQUENCIA DA TRANSAÇÃO
    public function transaction(Request $req)
    {
        $verIdAsaas = $this->getIdClient($req->id_user);

        if($verIdAsaas["success"] === false)
        {
            $criarCliente = $this->createClient($req->id_user);
        }
        else
        {
            $pegarClienteCriado = $this->getOneClient($verIdAsaas["id_asaas"]);
        }
        
    }



    // CRIAR O CLIENTE NO ASSAS
    public function createClient($id_user)
    {

        $user = User::where("id", $id_user);
        $infoPessoais = InfoPessoais::where("fk_id_usuario", $id_user);
        $endereco = Endereco::where("fk_id_usuario", $id_user);

        $data = [
            "name" => "Marcelo Almeida",
            "email" => "marcelo.almeida@gmail.com",
            "phone" => "4738010919",
            "mobilePhone" => "4799376637",
            "cpfCnpj" => "24971563792",
            "postalCode" => "01310-000",
            "address" => "Av. Paulista",
            "addressNumber" => "150",
            "complement" => "Sala 201",
            "province" => "Centro",
            "externalReference" => "12987382",
            "notificationDisabled" => false,
            "additionalEmails" => "marcelo.almeida2@gmail.com,marcelo.almeida3@gmail.com",
            "municipalInscription" => "46683695908",
            "stateInscription" => "646681195275",
            "observations" => "ótimo pagador, nenhum problema até o momento"
            ];

        $cliente = $this->requestAsaas("customers", $data, "POST");

        $registerID = User::where("id", $id_user)
            ->update([
                $cliente
            ]);

        return $registerID;
    }



    // VERIFICAR O ID ASSAS DO USUÁRIO
    public function getIdClient($id_user)
    {
        $id = User::where("id", $id_user);

        if(!$id && $id[0]->id_asaas == "")
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




    // LISTAR UM CLIENTE DO ASSAS
    public function getOneClient($id_cliente)
    {
        $cliente = $this->requestAsaas("", $id_cliente, 'GET');

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



    // LISTAR CLIENTES DO ASSAS
    public function listClient()
    {

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



    // PAGAR UMA COBRANÇA
    public function getOneCobranca()
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

        return $response ? $response : "";
    }
}
