<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class Account extends Controller
{
    // -------------- SUMÁRIO -------------- \\

    // PEGAR INFORMAÇÕES PESSOAIS DA CONTA (localstorage é melhor ?)




    // PEGAR INFORMAÇÕES PESSOAIS DA CONTA
    public function infoAccount()
    {
        $data = User::with("info_pessoais", "endereco")->get();
    }
}
