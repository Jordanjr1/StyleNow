<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaServicio extends Model
{
    protected $table = 'tbl_categoriaservicio';  // ← Cambiado
    protected $primaryKey = 'cats_id';
    public $timestamps = false;
    
    protected $fillable = [
        'cats_nombre',
        'cats_descripcion',
        'cats_estado'
    ];
}