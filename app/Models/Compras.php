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
        "valor_total",
        "metodo_pagamento",
        "link_boleto",
        "data_hora_compra",
        "status",
        "local_entrega",
        "local_atual",
        "fk_id_usuario" => "int",
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
