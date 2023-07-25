<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TIncluyeIcono extends Model
{
    protected $table = "tincluyeiconos";

    public function paquetes_incluye_iconos()
    {
        return $this->hasMany(TPaqueteIncluyeIcono::class, 'idincluyeiconos');
    }
}
