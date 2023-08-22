<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPrecioAeropuerto extends Model
{
    protected $table = "tprecioaeropuerto";

    public function aeropuerto()
    {
        return $this->belongsTo(TAeropuerto::class, 'idaeropuerto');
    }
}
