<?php

namespace App\Http\Controllers;

use App\Models\Comentarios;
use App\Models\Especificacoes;
use App\Models\Produto;
use App\Models\UsuarioDesejos;
use Illuminate\Http\Request;

class Shop extends Controller
{
    // PEGAR NOVOS PRODUTOS
    public function getNewProducts()
    {
        $products = Produto::orderBy("created_at", "desc")
            ->limit(10)
            ->get();
        
        if($products)
        {
            return response()->json([
               "msg" => "Deu certo",
               "data" => $products
            ]);
        }else
        {
            return response()->json([
                "msg" => "Deu errado" 
             ]);
        }
    }



    // PEGAR PRODUTOS POPULARES ( with = lado de muitos[n])
    public function getPopProducts(Request $req)
    {
        $productsObj = Produto::with("comentarios")->limit(3)->get();

        for ($i = 0; $i < 3; $i++) 
        {
            $procuctsId[] = $productsObj[$i]->id;
            $countsComents = Comentarios::where("fk_id_produto", $procuctsId[$i])->count();
            $countsReturn[] = $countsComents;
        }

        return $countsReturn;

        // response()->json([
        //             "msg" => "Deu certo",
        //             "quantidade_comentarios" => $countsComents,
        //         ]);
        
        // $starsAVG = Comentarios::where("fk_id_produto", $product_id)
        //     ->avg("estrelas");

        

        // if($products)
        // {
        //     return response()->json([
        //         "msg" => "Deu certo",
        //         "produtos" => $products,
        //         "quantidade_comentarios" => $countsComents,
        //         "media_estrelas" => $starsAVG
        //     ]);
        // }else
        // {
        //     return response()->json([
        //         "msg" => "Deu errado" 
        //     ]);
        // }
    }



    // PEGAR PRODUTOS NA PESQUISA
    public function postSearch(Request $req)
    {
        $text = $req->text;
        $products = Produto::where("nome_produto", "like", "%".$text."%")
            ->get();
        
        if($products)
        {
            return response()->json([
                "msg" => "Deu certo",
                "data" => $products
            ]);
        }else
        {
            return response()->json([
                "msg" => "Deu errado" 
            ]);
        }
    }



    // PEGAR PRODUTOS DA LISTA DE DESEJOS
    public function postWishlist(Request $req)
    {
        $user_id = $req->user_id;
        $product_id = $req->product_id;
        $products = UsuarioDesejos::with("user", "produto")
            ->where("fk_id_usuario", "=", $user_id)
            ->get();
            
        $starsAVG = Comentarios::where("fk_id_produto", $product_id)
            ->avg("estrelas")
            ->get();
        
        if($products)
        {
            return response()->json([
                "msg" => "Deu certo",
                "data" => $products
            ]);
        }else
        {
            return response()->json([
                "msg" => "Deu errado" 
            ]);
        }
    }



    // PEGAR UM PRODUTO PARA A PÁGINA
    public function postPageProduct(Request $req)
    {
        $product_id = $req->product_id;
        $products = Produto::where("id", $product_id)
            ->get();

        $specifications = Especificacoes::where(
            "fk_id_produto", $product_id
        )
        ->get();

        $coments = Comentarios::with("user", "produto")
            ->where("fk_id_produto", $product_id);
        
        if($products)
        {
            return response()->json([
                "msg" => "Deu certo",
                "data" => $products
            ]);
        }else
        {
            return response()->json([
                "msg" => "Deu errado" 
            ]);
        }
    }

}
