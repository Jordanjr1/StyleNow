<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'Tbl_Venta';
    protected $primaryKey = 'vnt_id';
    public $timestamps = false;
    
    protected $fillable = [
        'vnt_numeroFactura',
        'vnt_citaId',
        'vnt_clienteId',
        'vnt_empleadoId',
        'vnt_servicioId',
        'vnt_promocionId',
        'vnt_subtotal',
        'vnt_iva',
        'vnt_descuento',
        'vnt_total',
        'vnt_comisionEmpleado',
        'vnt_metodoPago',
        'vnt_fechaVenta',
        'vnt_estado'
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