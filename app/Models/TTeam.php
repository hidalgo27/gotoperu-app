<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TTeam extends Model
{
    use HasFactory;
    protected $table = "tteam";
    //

    public function destinos()
    {
        return $this->belongsToMany(TDestino::class, 'tteams_destinos', 'idteam', 'iddestino');
    }

    public function paises()
    {
        return $this->belongsToMany(TPais::class, 'tteams_pais', 'idteam', 'idpais');
    }
}
