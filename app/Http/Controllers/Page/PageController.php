<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\TDestino;
use App\Models\TPaquete;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function packages(){
        $paquetes = TPaquete::where('estado', 1)->get();
        return (compact('paquetes'));
    }
    public function destinations(){
        $destinos = TDestino::latest()->take(3)->get();
        return (compact('destinos'));
    }
}
