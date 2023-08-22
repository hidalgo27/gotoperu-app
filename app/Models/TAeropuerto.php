<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TAeropuerto extends Model
{

    protected $table = "taeropuerto";

    public function precio_aeropuerto()
    {
        return $this->hasMany(TPrecioAeropuerto::class, 'idaeropuerto');
    }
}
