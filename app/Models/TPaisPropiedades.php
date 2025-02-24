<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TPaisPropiedades extends Model
{
    use HasFactory;
    protected $table = "tpaispropiedades";

    public function pais()
    {
        return $this->belongsTo(TPais::class, 'idpais');
    }

}
