<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     * Laravel por defecto busca "proveedors", por eso daba error.
     */
    protected $table = 'tbl_proveedor';

    /**
     * Llave primaria de la tabla.
     * Laravel busca "id" por defecto, pero en tu DB es "prv_id".
     */
    protected $primaryKey = 'prv_id';

    /**
     * Si tu tabla NO tiene las columnas created_at y updated_at, 
     * pon esta propiedad en false.
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente (Mass Assignment).
     * Esto permite que el método Proveedor::create($request->all()) funcione.
     */
    protected $fillable = [
        'prv_nombre',
        'prv_telefono',
        'prv_email',
        'prv_direccion',
        'prv_estado',
    ];

    /**
     * Opcional: Si quieres que el estado se maneje siempre en mayúsculas
     */
    public function setPrvEstadoAttribute($value)
    {
        $this->attributes['prv_estado'] = strtoupper($value);
    }
}