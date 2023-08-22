<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TIncluye extends Model
{
    protected $table = "tincluye";

    public function paquete_incluye()
    {
        return $this->hasMany(TPaqueteIncluye::class, 'idincluye');
    }
}
