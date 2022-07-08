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

    private $emailUserComfirm;
    private $emailCode;



    // LOGIN DO USUÁRIO
    public function loginUser(Request $req)
    {
        $email = $req->email;
        $password = $req->password;

        $user = User::where("email", $email)->get();

        $token = Auth::attempt(
            [
                "email" => $email,
                "password" => $password
            ]
        );

        if ($token && $user) 
        {
            $user[0]->password = "";
            return response()->json([
                "user" => $user[0]
            ]);
        }
        else
        {
            return response()->json(["msg" => "Dados inválidos"]);
        }
        
    }



    // CADASTRO DO USUARIO
    public function registerUser(Request $req)
    {
        $user = [
            "email" => $req->email, 
            "password" => bcrypt($req->password)
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
            "fk_id_usuario" => $user->id
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




    // ENVIAR E-MAIL PARA MUDAR SENHA
    public function sendEmailNewPassword(Request $req)
    {
        $emailUser = $req->email;
        $emailCode = rand(163451, 912658);
        $emailVerify = User::where("email", $emailUser)->get();

        if($emailVerify)
        {
            $emailUserComfirm = $emailUser;

            $send = Mail::send("codeRec", ["emailCode" => $emailCode], function($message) 
            use ($emailUserComfirm, $emailCode){
                $message
                ->to($emailUserComfirm)
                ->subject("SPERENET: Nova senha");
            });

            return response()->json([
                "msg" => "O código de confirmação foi enviado para o seu e-mail.",
                "send" => true,
                "code" => $emailCode
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "E-mail não está registrado."
            ]);
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
                    "msg" => "Sua senha foi atualizada com sucesso.",
                    "change" => true
                ]);
        }
        else
        {
            return response()->json(["msg" => "Erro ao atualizar a senha"]);
        }
    }
}
