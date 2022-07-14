<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\InfoPessoais;
use App\Models\Notificacoes;
use App\Models\User;
use Illuminate\Http\Request;

class Account extends Controller
{
    // -------------- SUMÁRIO -------------- \\

    // PEGAR INFORMAÇÕES GERAIS DA CONTA (localstorage é melhor ?)
    // ALTERAR INFORMAÇÕES GERAIS
    // ALTERAR PREFERÊNCIAS DE NOTIFICAÇÃO
    


    // PEGAR INFORMAÇÕES GERAIS DA CONTA
    public function getInfoAccount(User $id)
    {
        $userInfoAc = User::with("infoPessoais", "endereco")->find($id);

        if($userInfoAc)
        {
            $userInfoAc[0]->password = "";
            return response()->json([
                "data" => $userInfoAc
            ]); 
        }
        else
        {
            return response()->json([
                "msg" => "Usuário não encontrado"
            ]);
        }
    }




    // ALTERAR INFORMAÇÕES GERAIS
    public function changeInfoAccount(Request $req)
    {
        $id_user = $req->id_user;

        $info_pessoais = [ 
            "nome_usuario" => $req->nome_usuario, 
            "telefone" => $req->telefone, 
            "cpf" => $req->cpf, 
            "nascimento" => $req->nascimento,
        ];

        $endereco = [ 
            "cep" => $req->cep,
            "pais" => $req->pais,
            "uf" => $req->uf,
            "cidade" => $req->cidade,
            "bairro" => $req->bairro,
            "rua" => $req->rua,
            "numero" => $req->numero,
        ];

        $info_pessoais = InfoPessoais::where("fk_id_usuario", "=", $id_user)
            ->update($info_pessoais);
        $endereco = Endereco::where("fk_id_usuario", "=", $id_user)
            ->update($endereco);

        if($endereco and $info_pessoais)
        {
            return response()->json([
                "msg" => "Alteração feita com sucesso.",
                "update" => true
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Alteração não foi concluida.",
                "update" => false
            ]);
        }
    }




    // ALTERAR EMAIL
    public function changeEmail(Request $req)
    {
        $id_user = $req->id_user;

        $email = User::where("id", $id_user)->update([
            "email" => $req->email
        ]);

        if($email)
        {
            return response()->json([
                "msg" => "Alteração feita com sucesso.",
                "update" => true
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Alteração não foi concluida.",
                "update" => false
            ]);
        }
    }




    // ALTERAR SENHA
    public function changePassword(Request $req)
    {
        $id_user = $req->id_user;

        $password = User::where("id", $id_user)->update([
            "password" => bcrypt($req->password)
        ]);

        if($password)
        {
            return response()->json([
                "msg" => "Alteração feita com sucesso.",
                "update" => true
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Alteração não foi concluida.",
                "update" => false
            ]);
        }
    }


    // // CONFIRMAR DOIS FATORES POR E-MAIL
    // public function confirmEmailTwoFA(Request $req)
    // {
    //     $device = $req->device;
    //     $id = $req->id;
    //     $two_factor_secret = password_hash($device.$id, "12");

    //     $user = User::findOrFail($id);
    //     $user->update([
    //         "two_factor_secret" => $two_factor_secret,
    //         "dois_fatores" => 'Y'
    //     ]);

    //     if($user)
    //     {
    //         return response()->json([
    //             "msg" => "Chave de dois fatores foi ativada."
    //         ]);
    //     }
    //     else
    //     {
    //         return response()->json([
    //             "msg" => "Erro na ativação na chave de dois fatores."
    //         ]);
    //     }
    // }




    // BUSCAR PREFERÊNCIAS DE NOTIFICAÇÃO
    public function getUserNotification(Request $req)
    {
        $id_user = $req->id_user;

        $notifications = Notificacoes::where("fk_id_usuario", "=", $id_user)
            ->get();


        if($notifications)
        {
            return response()->json([
                "msg" => "Consulta feita com sucesso.",
                "data" => $notifications,
                "get" => true
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Erro para consultar as notificações.",
                "get" => true
            ]);
        }
    }




    // ALTERAR PREFERÊNCIAS DE NOTIFICAÇÃO
    public function changeUserNotification(Request $req)
    {
        $id_user = $req->id_user;

        $notifications = [
            "promocoes" => $req->promocoes,
            "novidades" => $req->novidades,
            "atualizacoes" => $req->atualizacoes,
            "pedidos" => $req->pedidos,
        ];

        $notifications = Notificacoes::where("fk_id_usuario", "=", $id_user)
            ->update($notifications);

        if($notifications)
        {
            return response()->json([
                "msg" => "Alteração feita com sucesso.",
                "update" => true
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Erro para fazer a alteração.",
                "update" => true
            ]);
        }
    }
}
