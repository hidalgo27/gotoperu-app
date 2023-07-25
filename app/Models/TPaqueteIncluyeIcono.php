<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TPaqueteIncluyeIcono extends Model
{
    protected $table = "tpaqueteincluyeiconos";

    public function paquete()
    {
        return $this->belongsTo(TPaquete::class, 'idpaquetes');
    }

    public function incluye_iconos()
    {
        return $this->belongsTo(TIncluyeIcono::class, 'idincluyeiconos');
    }
}
