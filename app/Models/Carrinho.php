<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrinho extends Model
{
    use HasFactory;
    
    protected $table = "carrinho";
    protected $fillable = [
        "quantidade_produto",
        "preco_acumulado",
    ];



    public function compras()
    {
        return $this->hasMany(Compras::class, "fk_id_carrinho");
    }
}
