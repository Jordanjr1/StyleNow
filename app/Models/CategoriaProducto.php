<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    protected $table = 'tbl_categoriaproducto';  // ← Cambiado
    protected $primaryKey = 'catp_id';
    public $timestamps = false;
    
    protected $fillable = [
        'catp_nombre',
        'catp_descripcion',
        'catp_estado'
    ];
}