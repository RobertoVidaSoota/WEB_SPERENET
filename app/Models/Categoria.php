<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = "categoria";
    protected $fillable = [
        "nome_categoria"
    ];
    

    public function produtoCategoria()
    {
        return $this->hasMany(ProdutoCategoria::class);
    }
}
