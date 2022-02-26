<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especificacoes extends Model
{
    use HasFactory;

    protected $table = "especificacoes";
    protected $fillable = [
        "nome_especificacao",
        "valor_especificacao"
    ];
    protected $casts = [
        "fk_id_produto" => "int"
    ];



    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
