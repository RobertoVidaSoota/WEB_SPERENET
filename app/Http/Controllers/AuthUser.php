<?php

namespace App\Http\Controllers;

use App\Mail\NovaSenha;
use App\Models\Endereco;
use App\Models\InfoPessoais;
use App\Models\Notificacoes;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthUser extends Controller
{
    // -------------- SUMÁRIO -------------- \\

    // LOGIN DO USUÁRIO
    // CADASTRO DO USUARIO
    // MUDAR SENHA

    // public function __construct()
    // {   
    //     $this->middleware('auth:api');
    // }




    // LOGIN DO USUÁRIO
    public function loginUser(Request $req)
    {
        $email = $req->email;
        $password = $req->password;
        $user = User::where("email", "=", $email)->get();

        $hashVerify = Hash::check($password, $user[0]->password);

        if($hashVerify && $user)
        {
            $token = Auth::attempt(
                [
                  "email" => $email,
                  "password" => $password
                ]
            );

            if ($token) {
                return response()->json(["user" => $user[0]]);
            }
            else
            {
                return response()->json(["msg" => "Erro la no server."]);
            }
        }
        else
        {
            return response()->json(["msg" => "Esses dados são inválidos."]);
        }
    }



    // CADASTRO DO USUARIO
    public function registerUser(Request $req)
    {
        $user = [
            "email" => $req->email, 
            "password" => bcrypt($req->senha)
        ];

        $user = User::create($user);

        $info_pessoais = [ 
            "nome_usuario" => $req->nome_usuario, 
            "telefone" => $req->telefone, 
            "cpf" => $req->cpf, 
            "nascimento" => $req->nascimento,
            "fk_id_usuario" => $user->id
        ];

        $endereco = [ 
            "cep" => $req->cep,
            "pais" => $req->pais,
            "uf" => $req->uf,
            "cidade" => $req->cidade,
            "bairro" => $req->bairro,
            "rua" => $req->rua,
            "numero" => $req->numero,
            "fk_id_usuario" => $user->id
        ];

        $endereco = Endereco::create($endereco);
        $info_pessoais = InfoPessoais::create($info_pessoais);
        $notifications = Notificacoes::create([
            "promocoes" => "Y",
            "novidades" => "Y",
            "atualizacoes" => "Y",
            "pedidos" => "Y",
        ]);

        if($user && $endereco && $info_pessoais && $notifications)
        {
            return response()->json(["msg" => "Cadastro realizado"]);
        }
        else
        {
            return response()->json(["msg" => "Erro ao Cadastrar"]);
        }
    }




    // MUDAR SENHA
    public function newPassword(Request $req)
    {
        $email = $req->email;
        $newPassword = bcrypt($req->password);

        $user = User::where("email", "=", $email)->update(["password" => $newPassword]);

        if($user)
        {
            return response()->json([
                    "msg" => "Sua senha foi atualizada com sucesso."
                ]);
        }
        else
        {
            return response()->json(["msg" => "Erro ao atualizar a senha"]);
        }
    }
}