<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentarios extends Model
{
    use HasFactory;

    protected $table = "comentarios";
    protected $fillable = [
        "texto_comentario",
        "estrelas",
        "resposta",
        "fk_id_usuario",
        "fk_id_produto"
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
