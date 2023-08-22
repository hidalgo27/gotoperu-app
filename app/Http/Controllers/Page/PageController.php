<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\TCategoria;
use App\Models\TDestino;
use App\Models\THotelDestino;
use App\Models\TPais;
use App\Models\TPaquete;
use App\Models\TTeam;
use App\Models\TTestimonio;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function packages(){
        try {
            $paquetes = TPaquete::where('estado', 1)->get();
            return response()->json($paquetes, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }
    public function destinations(){
        $destinos = TDestino::latest()->take(3)->get();
        return (compact('destinos'));
    }
    public function packages_detail(TPaquete $paquete) {
        $testinomials = TTestimonio::all();
        $testinomials_r = TTestimonio::inRandomOrder()->limit(1)->get();
        $category = TCategoria::all();

        $hoteles_destinos = THotelDestino::all();

        $teams = TTeam::all();
        $pais2 = TPais::all();

        return view('page.detail',
            compact(
                'paquete',
                'category',
                'testinomials',
                'hoteles_destinos',
                'testinomials_r',
                'teams',
                'pais2'
            ));
    }
}
