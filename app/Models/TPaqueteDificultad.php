<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPaqueteDificultad extends Model
{
    protected $table = "tpaquetesdificultad";

    public function paquetes()
    {
        return $this->belongsTo(TPaquete::class, 'idpaquetes');
    }

    public function dificultad()
    {
        return $this->belongsTo(TDificultad::class, 'iddificultad');
    }
}
