<?php

namespace App\Http\Controllers;

use App\Models\Comentarios;
use App\Models\Especificacoes;
use App\Models\Produto;
use App\Models\UsuarioDesejos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function getPopProducts()
    {
        $productsObj = DB::select("
            SELECT produto.*, avg(estrelas) as media_estrelas,
            count(comentarios.fk_id_produto) as quantidade_comentarios
            FROM produto 
            JOIN comentarios
            ON produto.id = comentarios.fk_id_produto
            GROUP BY produto.id, nome_produto, link_imagem, preco_produto,
            descricao, created_at, updated_at
            ORDER BY avg(estrelas) desc
            LIMIT 3;
        ");

        if($productsObj)
        {
            return response()->json([
                "produtos" => $productsObj
            ]);
        }else
        {
            return response()->json([
                "msg" => "Deu errado" 
            ]);
        }
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
