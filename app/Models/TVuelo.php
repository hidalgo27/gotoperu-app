<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TVuelo extends Model
{
    protected $table = "tvuelos";

    public function paquetes_vuelos()
    {
        return $this->hasMany(TPaqueteVuelo::class, 'idvuelos');
    }
}
