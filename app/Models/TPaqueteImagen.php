<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPaqueteImagen extends Model
{
    protected $table = "tpaquetesimagen";
    public function paquetes()
    {
        return $this->belongsTo(TPaquete::class, 'idpaquetes');
    }
}
