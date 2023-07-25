<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TComentario extends Model
{
    protected $table = "tcomentario";
    public function itinerario()
    {
        return $this->belongsTo(TItinerario::class, 'iditinerario');
    }
}
