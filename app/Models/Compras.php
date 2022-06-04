<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
    use HasFactory;

    protected $table = "compras";
    // COLOCAR OS FILLABLES NO MODEL CARRINHO
    protected $fillable = [
        "metodo_pagamento",
        "link_boleto",
        "data_hora_compra",
        "status",
        "local_entrega",
        "local_atual",
    ];
    protected $casts = [
        "fk_id_usuario" => "int",
        "fk_id_carrinho" => "int"
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function carrinho()
    {
        return $this->belongsTo(Carrinho::class);
    }
}
