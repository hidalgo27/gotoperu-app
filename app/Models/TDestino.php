<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDestino extends Model
{
    protected $table = "tdestinos";

    public function getRouteKeyName()
    {
//        return parent::getRouteKeyName(); // TODO: Change the autogenerated stub
        return "url";
    }

    public function paquetes_destinos()
    {
        return $this->hasMany(TPaqueteDestino::class, 'iddestinos');
    }

    public function destino_imagen()
    {
        return $this->hasMany(TDestinoImagen::class, 'iddestinos');
    }

    public function destino_pais()
    {
        return $this->hasMany(TPais::class, 'id');
    }
}
