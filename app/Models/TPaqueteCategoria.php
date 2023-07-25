<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TPaqueteCategoria extends Model
{
    protected $table = "tpaquetescategoria";

    public function paquete()
    {
        return $this->belongsTo(TPaquete::class, 'idpaquetes');
    }

    public function categoria()
    {
        return $this->belongsTo(TCategoria::class, 'idcategoria');
    }
}
