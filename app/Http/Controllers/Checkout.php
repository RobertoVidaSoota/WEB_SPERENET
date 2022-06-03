<?php

namespace App\Http\Controllers;

use App\Models\Carrinho;
use Illuminate\Http\Request;

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
    // CARINHO DE COMPRA(TRAVADO)
    // -----------------------------------
    public function postCart(Request $req)
    {
        $user_id = $req->user_id;
        $carrinho = Carrinho::with("compras")
            ->where("fk_id_usuario", "=", $user_id)
            ->get();
    }


    // MÉTODO DE PAGAMENTO(TESTAR COM APP)
    public function postPayMethod(Request $req)
    {
        $pix = $req->metodo_pix;
        $boleto = $req->metodo_boleto;
        $cartao = $req->metodo_cartao;
    }



    // REASTREAMENTO DE PRODUTO(TESTAR COM APP)
    public function postTrackProduct(Request $req)
    {
        $user_id = $req->user_id;
        $carrinho = Carrinho::with("compras")
            ->where("fk_id_usuario", "=", $user_id)
            ->get();
    }
}
