<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TPaqueteIncluye extends Model
{
    protected $table = "tpaquetesincluye";

    public function paquetes()
    {
        return $this->belongsTo(TPaquete::class, 'idpaquetes');
    }

    public function incluye()
    {
        return $this->belongsTo(TIncluye::class, 'idincluye');
    }
}
