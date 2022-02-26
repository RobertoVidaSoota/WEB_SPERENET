<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthApi extends Controller
{

    // public function __construct()
    // {   
    //     $this->middleware('auth:api');
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $users = User::get();

    //     return response()->json($users);
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




    public function resgisterUser(Request $req)
    {
        
    }
}
