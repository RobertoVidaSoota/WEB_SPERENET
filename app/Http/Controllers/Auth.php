<?php

namespace App\Http\Controllers;

use App\Mail\NovaSenha;
use App\Models\Endereco;
use App\Models\InfoPessoais;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthApi extends Controller
{
    // -------------- SUMÁRIO -------------- \\

    // LOGIN DO USUÁRIO
    // CADASTRO DO USUARIO
    // RECUPERAR SENHA

    // public function __construct()
    // {   
    //     $this->middleware('auth:api');
    // }




    // LOGIN DO USUÁRIO
    public function loginUser(Request $req)
    {
        $email = $req->email;
        $user = User::where("email", "=", $email)->get();
        $password = Hash::check($req->password, $user->password);
        
        if($user)
        {
            $token = Auth::guard('api')->attempt(
                [
                  "email" => $email,
                  "password" => $password
                ]
            );

            if ($token) {
                return response()->json(["user" => $user]);
            }
            else
            {
                return response()->json(["msg", "Erro la no server."]);
            }
        }
        else
        {
            return response()->json(["msg", "Esses dados são inválidos."]);
        }
    }



    // CADASTRO DO USUARIO
    public function resgisterUser(Request $req)
    {
        $user = [ $req->email, $req->senha];

        $info_pessoais = [ 
            $req->nome_usuario, 
            $req->telefone, 
            $req->cpf, 
            $req->nascimento,
            $req->fk_id_usuario];

        $endereco = [ 
            $req->cep,
            $req->pais,
            $req->uf,
            $req->cidade,
            $req->bairro,
            $req->rua,
            $req->numero,
            $req->fk_id_usuario,
        ];


        $user = User::create($user);
        $endereco = Endereco::create($endereco);
        $info_pessoais = InfoPessoais::create($info_pessoais);

        if($user && $endereco && $info_pessoais)
        {
            return response()->json(["msg" => "Cadastro realizado"]);
        }
        else
        {
            return response()->json(["msg" => "Erro ao Cadastrar"]);
        }
    }




    // RECUPERAR SENHA
    public function newPassword(Request $req)
    {
        $email = $req->email;
        $text = "
            Clique no link abaixo para definir a nova senha. <br><br>
            <a href='#'>Mudar senha</a>
        ";

        $enviar = Mail::to($email)->send(new NovaSenha);

        if($enviar)
        {
            return response()->json(
                ["msg" => "Email de recuperação enviado com sucesso."]
            );
        }
        else
        {
            return response()->json(["msg" => "Erro ao enviar o e-mail"]);
        }
    }
}
