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
    // PEGAR UM PRODUTO
    public function postOneProduct(Request $req)
    {
        $id_produto = $req->id_produto;

        $produto = DB::select("
            SELECT produto.*, format(avg(estrelas), 1) as media_estrelas,
            count(comentarios.fk_id_produto) as quantidade_comentarios
            FROM produto 
            JOIN comentarios
            ON produto.id = comentarios.fk_id_produto
            WHERE produto.id = ".$id_produto."
            GROUP BY produto.id, nome_produto, link_imagem, preco_produto,
            descricao, created_at, updated_at;
        ");

        if($produto)
        {   
            return response()->json([
                "msg" => "Deu certo",
                "produto" => $produto
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Deu errado",
            ]);
        }
    }




    // PEGAR NOVOS PRODUTOS
    public function getNewProducts()
    {
        $products = DB::select("
        SELECT produto.*, format(avg(estrelas), 1) as media_estrelas,
        count(comentarios.fk_id_produto) as quantidade_comentarios
        FROM produto 
        JOIN comentarios
        ON produto.id = comentarios.fk_id_produto
        GROUP BY produto.id, nome_produto, link_imagem, preco_produto,
        descricao, created_at, updated_at
        ORDER BY produto.created_at desc
        LIMIT 8;
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




    // PEGAR MAIS PRODUTOS
    public function getMoreProducts()
    {
        $productsMore = DB::select("
            SELECT produto.*, format(avg(estrelas), 1) as media_estrelas,
            count(comentarios.fk_id_produto) as quantidade_comentarios
            FROM produto 
            JOIN comentarios
            ON produto.id = comentarios.fk_id_produto
            GROUP BY produto.id, nome_produto, link_imagem, preco_produto,
            descricao, created_at, updated_at
            ORDER BY avg(estrelas) desc
            LIMIT 3, 8;
        ");

        if($productsMore)
        {
            return response()->json([
                "data" => $productsMore
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
            SELECT usuario_desejos.id as id, link_imagem, nome_produto, preco_produto,
            format(avg(estrelas), 1) as media_estrelas, produto.id as 
            id_produto
            FROM produto
            JOIN users JOIN usuario_desejos JOIN comentarios
            ON users.id = usuario_desejos.fk_id_usuario
            AND produto.id = usuario_desejos.fk_id_produto
            AND produto.id = comentarios.fk_id_produto
            WHERE usuario_desejos.fk_id_usuario = ".$user_id."
            GROUP BY usuario_desejos.id, usuario_desejos.fk_id_produto,
            usuario_desejos.fk_id_usuario, usuario_desejos.created_at,
            usuario_desejos.updated_at, link_imagem, nome_produto, 
            preco_produto, produto.id;
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



    // VERIFICAR SE O PRODUTO TA NA LISTA DE DESEJOS
    public function checkWishlist(Request $req)
    {
        $id_produto = $req->id_produto;
        $id_user = $req->id_user;

        $produtoLista = UsuarioDesejos::where(
            "fk_id_produto", $id_produto,
        )
        ->where(
            "fk_id_usuario", $id_user
        )
        ->get();

        if($produtoLista)
        {
            return response()->json([
                "msg" => "Deu certo",
                "produto" => $produtoLista
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Deu errado",
            ]);
        }
    }




    // ADICIONAR PRODUTOS DA LISTA DE DESEJOS
    public function addWishlist(Request $req)
    {
        $id_produto = $req->id_produto;
        $id_user = $req->id_user;

        $wishList = UsuarioDesejos::create([
            "fk_id_usuario" => $id_user,
            "fk_id_produto" => $id_produto
        ]);

        if($wishList)
        {
            return response()->json([
                "msg" => "Deu certo",
                "data" => $wishList
            ]);
        }else
        {
            return response()->json([
                "msg" => "Deu errado" 
            ]);
        }
    }




    // REMOVER PRODUTOS DA LISTA DE DESEJOS
    public function removeWishlist(Request $req)
    {
        $id_produto = $req->id_produto;
        $id_user = $req->id_user;

        $idProdutoLista = UsuarioDesejos::where(
            "fk_id_produto", $id_produto
        )
        ->where(
            "fk_id_usuario", $id_user
        )
        ->get(); 
        $wishList = UsuarioDesejos::destroy($idProdutoLista[0]["id"]);

        if($wishList)
        {
            return response()->json([
                "msg" => "Deu certo",
                "data" => $wishList
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

        $specifications = Especificacoes::where(
            "fk_id_produto", $product_id
        )
        ->get();

        $coments = DB::table("comentarios")
            ->join("users", "users.id", "=", "comentarios.fk_id_usuario")
            ->join("info_pessoais", "info_pessoais.fk_id_usuario", "=", 
            "comentarios.fk_id_usuario")
            ->select("users.id", "users.name", "users.profile_photo_path", 
                "comentarios.estrelas", "comentarios.texto_comentario", "info_pessoais.nome_usuario",  
            )->orderBy("comentarios.id", "desc")
            ->where("comentarios.fk_id_produto", "=", $product_id)
            ->get();
        
        if($coments)
        {
            return response()->json([
                "msg" => "Deu certo",
                "data" => [
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




    // POSTAR COMENTARIO SOBRE O PRODUTO
    public function postComents(Request $req)
    {
        $id_user = $req->id_user;
        $id_produto = $req->id_produto;
        $comentario = [
            "texto_comentario" => $req->comentario,
            "estrelas" => $req->estrelas,
            "resposta" => "N",
            "fk_id_usuario" => $id_user,
            "fk_id_produto" => $id_produto
        ];

        $inserirComaentario = Comentarios::create($comentario);

        if($inserirComaentario)
        {
            return response()->json([
                "msg" => "Comentário postado com sucesso.",
                "comentario" => $comentario
            ]);
        }
        else
        {
            return response()->json([
                "msg" => "Comentário não foi postado."
            ]);
        }
    }

}
