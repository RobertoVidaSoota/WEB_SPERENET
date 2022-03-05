<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class Account extends Controller
{
    // -------------- SUMÁRIO -------------- \\

    // PEGAR INFORMAÇÕES PESSOAIS DA CONTA (localstorage é melhor ?)
    // ALTERAR SENHA



    // PEGAR INFORMAÇÕES PESSOAIS DA CONTA
    public function getInfoAccount()
    {
        $data = User::with("info_pessoais", "endereco")->get();
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
}
