<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TItinerario extends Model
{
    protected $table = "titinerario";

    public function paquete_itinerario()
    {
        return $this->hasMany(TpaqueteItinerario::class, 'iditinerario');
    }

    public function itinerario_imagen()
    {
        return $this->hasMany(TItinerarioImagen::class, 'iditinerario');
    }

    public function comentario()
    {
        return $this->hasMany(TComentario::class, 'iditinerario');
    }
}
