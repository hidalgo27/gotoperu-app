<?php
// gotoperu-app/app/Models/TPaqueteSalida.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPaqueteSalida extends Model
{
    protected $table = 'tpaquetes_salidas';

    protected $fillable = [
        'idpaquetes',
        'departure_date',
        'label',
        'seats_total',
        'seats_available',
        'price_from',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'departure_date' => 'date:Y-m-d',
        'seats_total' => 'integer',
        'seats_available' => 'integer',
        'price_from' => 'decimal:2',
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    public function paquete()
    {
        return $this->belongsTo(TPaquete::class, 'idpaquetes');
    }
}
