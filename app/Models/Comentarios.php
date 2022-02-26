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
        "resposta"
    ];
    protected $casts = [
        "fk_id_usuario" => "int",
        "fk_id_pruduto" => "int"
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
