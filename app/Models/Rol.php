<?php
// app/Models/Rol.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'Tbl_Rol';
    protected $primaryKey = 'rol_id';
    public $timestamps = false;

    protected $fillable = [
        'rol_nombre',
        'rol_descripcion',
        'rol_estado'
    ];
}