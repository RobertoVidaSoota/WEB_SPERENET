<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioDesejos extends Model
{
    use HasFactory;

    protected $table = "usuario_desejos";
    protected $fillable = [
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
