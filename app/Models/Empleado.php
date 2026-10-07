<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'Tbl_Empleado';
    protected $primaryKey = 'emp_id';
    public $timestamps = false;
    
    protected $fillable = [
        'emp_usuarioId',
        'emp_sucursalId',
        'emp_especialidadId',
        'emp_citasCompletadas',
        'emp_comisionTotal',
        'emp_estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'emp_usuarioId', 'usr_id');
    }
    
    
}