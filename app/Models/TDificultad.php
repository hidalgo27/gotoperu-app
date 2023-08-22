<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDificultad extends Model
{
    protected $table = "tdificultad";

    public function paquetes_dificultad()
    {
        return $this->hasMany(TPaqueteDificultad::class, 'iddificultad');
    }
}
