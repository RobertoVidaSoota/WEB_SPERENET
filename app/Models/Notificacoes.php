<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacoes extends Model
{
    use HasFactory;

    protected $table = "notificacoes";
    protected $fillable = [
        "promocoes",
        "novidades",
        "atualizacoes",
        "pedidos"
    ];
    protected $casts = [
        "fk_id_usuario" => "int"
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
