<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class THotel extends Model
{
    protected $table = "thotel";

    public function hotel_destinos()
    {
        return $this->hasMany(THotelDestino::class, 'idhotel');
    }

    public function destinos()
    {
        return $this->belongsToMany(TDestino::class, 'thoteldestino', 'idhotel', 'iddestinos');
    }
}
