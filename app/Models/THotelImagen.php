<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class THotelImagen extends Model
{
    protected $table = 'thotelimagen';

    protected $fillable = [
        'idhotel',
        'imagen',
        'created_at',
        'updated_at',
    ];

    // Relación inversa: Una imagen pertenece a un hotel
    public function hotel()
    {
        return $this->belongsTo(THotel::class, 'idhotel');
    }
}
