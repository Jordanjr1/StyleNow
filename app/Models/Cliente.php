<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'Tbl_Cliente';
    protected $primaryKey = 'cli_id';
    public $timestamps = false;
    
    protected $fillable = [
        'cli_usuarioId',
        'cli_puntosFidelizacion'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'cli_usuarioId', 'usr_id');
    }
}