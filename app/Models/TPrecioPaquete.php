<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TPrecioPaquete extends Model
{
    protected $table = "tpreciopaquetes";

    public function paquetes()
    {
        return $this->belongsTo(TPaquete::class, 'idpaquetes');
    }
}
