<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable. (VEM NO SELECT)
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization. (NÃO VEM NO SELECT)
     * 
     * @var array
     */
    protected $hidden = [
        // 'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function infoPessoais()
    {
        return $this->hasOne(InfoPessoais::class, 'fk_id_usuario');
    }



    public function endereco()
    {
        return $this->hasOne(Endereco::class, 'fk_id_usuario');
    }



    public function usuarioDesejos()
    {
        return $this->hasMany(UsuarioDesejos::class, 'fk_id_usuario');
    }



    public function comentarios()
    {
        return $this->hasMany(Comentarios::class, 'fk_id_usuario');
    }



    public function compras()
    {
        return $this->hasMany(Compras::class, 'fk_id_usuario');
    }



    public function notificacoes()
    {
        return $this->hasOne(Notificacoes::class, 'fk_id_usuario');
    }
}
