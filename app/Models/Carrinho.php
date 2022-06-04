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
        "fk_id_produto",
        "fk_id_compras"
    ];



    
}
