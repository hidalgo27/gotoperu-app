<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TPaisCategoria extends Model
{
    protected $table = "tpais_categoria";
    public $timestamps = true;

    // No hay id autoincremental: PK compuesta (pais_id, categoria_id)
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = ['pais_id', 'categoria_id'];

    public function pais()
    {
        return $this->belongsTo(TPais::class, 'pais_id');
    }

    public function categoria()
    {
        return $this->belongsTo(TCategoria::class, 'categoria_id');
    }
}
