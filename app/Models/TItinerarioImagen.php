<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TItinerarioImagen extends Model
{
    protected $table = "titinerarioimagen";
    public function itinerario()
    {
        return $this->belongsTo(TItinerario::class, 'iditinerario');
    }
}
