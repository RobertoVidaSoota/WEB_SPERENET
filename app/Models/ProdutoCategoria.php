<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoCategoria extends Model
{
    use HasFactory;

    protected $table = "produto_categoria";
    protected $casts = [
        "fk_id_categoria" => "int",
        "fk_id_produto" => "int",
    ];



    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }



    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
