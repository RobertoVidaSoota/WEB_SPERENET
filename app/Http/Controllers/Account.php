<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\InfoPessoais;
use App\Models\User;
use Illuminate\Http\Request;

class Account extends Controller
{
    // -------------- SUMÁRIO -------------- \\

    // PEGAR INFORMAÇÕES GERAIS DA CONTA (localstorage é melhor ?)
    // ALTERAR INFORMAÇÕES GERAIS
    // ALTERAR E-MAIL
    // ALTERAR SENHA
    // CONFIRMAR DOIS FATORES POR E-MAIL
    // ALTERA PREFERÊNCIAS DE NOTIFICAÇÃO
    


    // PEGAR INFORMAÇÕES GERAIS DA CONTA
    public function getInfoAccount()
    {
        $user = User::with("info_pessoais", "endereco")->get();
    }




    // ALTERAR INFORMAÇÕES GERAIS
    public function changeInfoAccount(Request $req)
    {
        $id_user = $req->id_user;

        $reqInfo_pessoais = [ 
            $req->nome_usuario, 
            $req->telefone, 
            $req->cpf, 
            $req->nascimento,
            $req->$id_user
        ];

        $reqEndereco = [ 
            $req->cep,
            $req->pais,
            $req->uf,
            $req->cidade,
            $req->bairro,
            $req->rua,
            $req->numero,
            $req->$id_user,
        ];

        $user = User::findOrFail($id_user);
        $endereco = Endereco::findOrFail($reqEndereco[$req->$id_user]);
        $info_pessoais = InfoPessoais::findOrFail($reqInfo_pessoais[$req->$id_user]);

        $endereco->update($reqEndereco);
        $info_pessoais->update($reqInfo_pessoais);

        if($user and $endereco and $info_pessoais)
        {
            return response()->json([
                "msg" => "Alteração feita com sucesso."
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Alteração não foi concluida."
            ]);
        }
    }





    // ALTERAR E-MAIL
    public function changeEmail(Request $req)
    {
        $id = $req->id;
        $email = $req->email;

        $user = User::findOrFail($id);
        $user->update(["email" => $email]);

        if($user)
        {
            return response()->json([
                "msg" => "E-mail alterado com sucesso."
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Erro para alterar o e-mail."
            ]);
        }
    }




    // ALTERAR SENHA
    public function changePassword(Request $req)
    {
        $id = $req->id;
        $password = bcrypt($req->password);

        $user = User::findOrFail($id);
        $user->update(["password" => $password]);

        if($user)
        {
            return response()->json([
                "msg" => "Senha alterada com sucesso."
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Erro para alterar a senha."
            ]);
        }
    }




    // CONFIRMAR DOIS FATORES POR E-MAIL
    public function confirmEmailTwoFA(Request $req)
    {
        $device = $req->device;
        $id = $req->id;
        $two_factor_secret = password_hash($device.$id, "12");

        $user = User::findOrFail($id);
        $user->update([
            "two_factor_secret" => $two_factor_secret,
            "dois_fatores" => 'Y'
        ]);

        if($user)
        {
            return response()->json([
                "msg" => "Chave de dois fatores foi ativada."
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Erro na ativação na chave de dois fatores."
            ]);
        }
    }
}
