<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\TCategoria;
use App\Models\TDestino;
use App\Models\THotel;
use App\Models\THotelDestino;
use App\Models\TPais;
use App\Models\TPaquete;
use App\Models\TTeam;
use App\Models\TTestimonio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function packages(){
        try {
            $paquetes = TPaquete::
            with('paquetes_destinos.destinos.destino_pais', 'precio_paquetes')
                ->where('estado', '1')
                ->get();
            /*$paquetes = TPaquete::where('estado', 1)->get();*/

            /*$pais2 = TPais::all();*/

            $paquetes_api = DB::table('tpaquetesdestinos')
                ->join('tdestinos', 'tpaquetesdestinos.iddestinos', '=', 'tdestinos.id')
                ->select('idpaquetes', 'idpais')
                ->groupByRaw('idpaquetes, idpais')
                ->get();
            $paquetes_api = ($paquetes_api->groupBy('idpaquetes'));

            /*dd($paquetes_api);*/
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
    public function packages_detail($url) {
        try {
            $paquetes = TPaquete::with('paquete_itinerario.itinerarios', 'paquetes_destinos.destinos.destino_pais', 'precio_paquetes')->where('url', $url)->get();
            return response()->json($paquetes, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function hotels_destinations() {
        try {
            $hoteles = THotel::with('hotel_destinos.destinos')->get();
            return response()->json($hoteles, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }
}
