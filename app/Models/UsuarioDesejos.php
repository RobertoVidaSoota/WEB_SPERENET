<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioDesejos extends Model
{
    use HasFactory;

    protected $table = "usuario_desejos";
    protected $casts = [
        "fk_id_usuario" => "int",
        "fk_id_produto" => "int"
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
