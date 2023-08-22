<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpaqueteItinerario extends Model
{
    protected $table = "tpaquetesitinerario";

    public function paquetes()
    {
        return $this->belongsTo(TPaquete::class, 'idpaquetes');
    }
    public function itinerarios()
    {
        return $this->belongsTo(TItinerario::class, 'iditinerario');
    }
}

