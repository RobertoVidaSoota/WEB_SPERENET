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
            SELECT produto.*, format(avg(estrelas), 1) as media_estrelas,
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
                "data" => $productsObj
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

        $products = DB::select("
            SELECT link_imagem, nome_produto, preco_produto,
            avg(estrelas) as media_estrelas
            FROM produto
            JOIN users JOIN usuario_desejos JOIN comentarios
            ON users.id = usuario_desejos.fk_id_usuario
            AND produto.id = usuario_desejos.fk_id_produto
            AND produto.id = comentarios.fk_id_produto
            WHERE usuario_desejos.fk_id_usuario = ".$user_id."
            GROUP BY usuario_desejos.id, usuario_desejos.fk_id_produto,
            usuario_desejos.fk_id_usuario, usuario_desejos.created_at,
            usuario_desejos.updated_at, link_imagem, nome_produto, 
            preco_produto;
        ");
        
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

        $coments = DB::table("comentarios")
            ->join("users", "users.id", "=", "comentarios.fk_id_usuario")
            ->select("users.id", "users.name", "users.profile_photo_path", 
                "comentarios.estrelas", "comentarios.texto_comentario", 
            )
            ->where("comentarios.fk_id_produto", "=", $product_id)
            ->get();
        
        if($products)
        {
            return response()->json([
                "msg" => "Deu certo",
                "data" => [
                    "produto" => $products,
                    "especificacoes" => $specifications,
                    "comentarios" => $coments
                ]
            ]);
        }else
        {
            return response()->json([
                "msg" => "Deu errado" 
            ]);
        }
    }

}
