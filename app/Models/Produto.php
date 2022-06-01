<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = "produto";
    protected $fillable = [
        "nome_produto",
        "link_imagem",
        "preco_produto",
        "descricao"
    ];



    public function usuarioDesejos()
    {
        return $this->hasMany(UsuarioDesejos::class, "fk_id_produto");
    }



    public function produtoCategoria()
    {
        return $this->hasMany(ProdutoCategoria::class, "fk_id_produto");
    }



    public function especificacoes()
    {
        return $this->hasMany(especificacao::class, "fk_id_produto");
    }



    public function comentarios()
    {
        return $this->hasMany(Comentarios::class, "fk_id_produto");
    }



    public function compras()
    {
        return $this->hasMany(Compras::class, "fk_id_produto");
    }
}
