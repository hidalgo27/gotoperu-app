<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TPaqueteImagen extends Model
{
    protected $table = "tpaquetesimagen";
    public function paquetes()
    {
        return $this->belongsTo(TPaquete::class, 'idpaquetes');
    }
}
