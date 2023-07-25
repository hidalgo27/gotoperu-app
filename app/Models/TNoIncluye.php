<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TNoIncluye extends Model
{
    protected $table = "tnoincluye";

    public function paquete_no_incluye()
    {
        return $this->hasMany(TPaqueteNoIncluye::class, 'idnoincluye');
    }
}
