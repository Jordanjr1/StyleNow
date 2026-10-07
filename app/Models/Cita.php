<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'Tbl_Cita';
    protected $primaryKey = 'cit_id';
    public $timestamps = false;
    
    protected $fillable = [
        'cit_clienteId',
        'cit_empleadoId',
        'cit_servicioId',
        'cit_sucursalId',
        'cit_fechaCita',
        'cit_estadoCita',
        'cit_calificacion',
        'cit_fechaCreacion',
        'cit_fechaModificacion',
        'cit_fechaCancelacion'
    ];
    
    public function count()
    {
        return $this->query()->count();
    }
    
    public static function whereDate($column, $date)
    {
        return static::where($column, '>=', $date->startOfDay())
                    ->where($column, '<=', $date->endOfDay());
    }
}