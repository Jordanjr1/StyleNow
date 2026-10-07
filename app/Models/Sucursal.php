<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'tbl_sucursal';
    protected $primaryKey = 'suc_id';
    public $timestamps = false; // Tu tabla no tiene created_at/updated_at

    protected $fillable = [
        'suc_nombre', 
        'suc_direccion', 
        'suc_telefono', 
        'suc_email', 
        'suc_estado'
    ];
}