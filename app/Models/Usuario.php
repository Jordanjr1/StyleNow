<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasApiTokens;

    protected $table = 'Tbl_Usuario';
    protected $primaryKey = 'usr_id';
    public $timestamps = false;
    
    protected $fillable = [
        'usr_nombre',
        'usr_apellido',
        'usr_cedula',
        'usr_email',
        'usr_telefono',
        'usr_password',
        'usr_rolId',
        'usr_fechaRegistro',
        'usr_estado',
        'usr_intentos',
        'requiere_reset'
    ];

    protected $hidden = [
        'usr_password',
        'remember_token',
    ];

    public function username()
    {
        return 'usr_email';
    }

    public function getAuthPassword()
    {
        return $this->usr_password;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'usr_rolId', 'rol_id');
    }

    public function getPasswordAttribute()
    {
        return $this->usr_password;
    }

    public function esAdministrador()
    {
        return $this->usr_rolId == 1;
    }

    public function esEmpleado()
    {
        return $this->usr_rolId == 2;
    }

    public function esCliente()
    {
        return $this->usr_rolId == 3;
    }

    #actualizar contaseña y

}