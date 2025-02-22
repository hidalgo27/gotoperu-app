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
}
