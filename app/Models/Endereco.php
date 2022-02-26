<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    use HasFactory;

    protected $table = "endereco";
    protected $fillable = [
        "cep",
        "pais",
        "uf",
        "cidade",
        "bairro",
        "rua",
        "numero",
    ];
    protected $casts = [
        "fk_id_usuario" => "int"
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
