<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TPaquete extends Model
{
    protected $table = "tpaquetes";

    public function paquete_itinerario()
    {
        return $this->hasMany(TpaqueteItinerario::class, 'idpaquetes');
    }

    public function precio_paquetes()
    {
        return $this->hasMany(TPrecioPaquete::class, 'idpaquetes');
    }

    public function imagen_paquetes()
    {
        return $this->hasMany(TPaqueteImagen::class, 'idpaquetes');
    }

    public function paquetes_destinos()
    {
        return $this->hasMany(TPaqueteDestino::class, 'idpaquetes');
    }

    public function paquetes_categoria()
    {
        return $this->hasMany(TPaqueteCategoria::class, 'idpaquetes');
    }

    public function paquetes_dificultad()
    {
        return $this->hasMany(TPaqueteDificultad::class, 'idpaquetes');
    }

    public function paquetes_incluye_iconos()
    {
        return $this->hasMany(TPaqueteIncluyeIcono::class, 'idpaquetes');
    }

    public function paquete_incluye()
    {
        return $this->hasMany(TPaqueteIncluye::class, 'idpaquetes');
    }

    public function paquete_no_incluye()
    {
        return $this->hasMany(TPaqueteNoIncluye::class, 'idpaquetes');
    }
}
