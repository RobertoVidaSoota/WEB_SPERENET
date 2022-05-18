<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoPessoais extends Model
{
    use HasFactory;

    protected $table = "info_pessoais";
    protected $fillable = [
        "nome_usuario",
        "telefone",
        "cpf",
        "nascimento",
        "fk_id_usuario" => "int"
    ];
    // protected $casts = [
    //     "fk_id_usuario" => "int"
    // ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
