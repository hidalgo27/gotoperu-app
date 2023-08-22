<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPaqueteVuelo extends Model
{
    protected $table = "tpaquetesvuelos";

    public function paquetes()
    {
        return $this->belongsTo(TPaquete::class, 'idpaquetes');
    }

    public function vuelos()
    {
        return $this->belongsTo(TVuelo::class, 'idvuelos');
    }
}
