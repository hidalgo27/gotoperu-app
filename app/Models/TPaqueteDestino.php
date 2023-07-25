<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TPaqueteDestino extends Model
{
    protected $table = "tpaquetesdestinos";

    public function paquetes()
    {
        return $this->belongsTo(TPaquete::class, 'idpaquetes');
    }

    public function destinos()
    {
        return $this->belongsTo(TDestino::class, 'iddestinos');
    }
}
