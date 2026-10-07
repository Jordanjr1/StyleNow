<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'tbl_producto';
    protected $primaryKey = 'prd_id';
    public $timestamps = false;

    protected $fillable = [
    'prd_nombre', 'prd_categoriaId', 'prd_proveedorId', 'prd_sucursalId', 
    'prd_stockActual', 'prd_stockMinimo', 'prd_unidadMedida', 
    'prd_precioCompra', 'prd_precioVenta', 'prd_estado', 
    'prd_imagen', 'prd_tieneIva' // <-- ESTO ES VITAL PARA QUE SE GUARDE
];

    public function proveedor() {
        return $this->belongsTo(Proveedor::class, 'prd_proveedorId', 'prv_id');
    }
public function sucursal()
{
    return $this->belongsTo(Sucursal::class, 'prd_sucursalId', 'suc_id');
}
    public function categoria() {
        return $this->belongsTo(CategoriaProducto::class, 'prd_categoriaId', 'catp_id');
    }
}