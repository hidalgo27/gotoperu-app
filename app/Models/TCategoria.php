<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TCategoria extends Model
{
    protected $table = "tcategoria";

    public function paquetes_categorias()
    {
        return $this->hasMany(TPaqueteCategoria::class, 'idcategoria');
    }
}
