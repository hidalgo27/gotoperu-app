<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TCategoria extends Model
{
    use HasFactory;
    protected $table = "tcategoria";

    public function getRouteKeyName()
    {
        return "url";
    }

    public function paquetes_categorias()
    {
        return $this->hasMany(TPaqueteCategoria::class, 'idcategoria');
    }

    public function paquetes()
    {
        return $this->belongsToMany(TPaquete::class, 'tpaquetescategoria', 'idcategoria', 'idpaquetes');
    }

    // App/Models/TCategoria.php
    public function paises()
    {
        return $this->belongsToMany(
            TPais::class,
            'tpais_categoria',
            'categoria_id',
            'pais_id'
        )->withTimestamps();
    }

}
