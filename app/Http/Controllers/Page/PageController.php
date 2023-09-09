<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\TCategoria;
use App\Models\TDestino;
use App\Models\THotel;
use App\Models\THotelDestino;
use App\Models\TPais;
use App\Models\TPaquete;
use App\Models\TPaqueteDestino;
use App\Models\TTeam;
use App\Models\TTestimonio;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function packages(){
        try {
            $paquetes = TPaquete::
            with('paquetes_destinos.destinos.pais', 'precio_paquetes')
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

    public function packages_detail($url) {
        try {
            $paquetes = TPaquete::with('paquete_itinerario.itinerarios', 'paquetes_destinos.destinos.pais', 'precio_paquetes')->where('url', $url)->get();
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

    public function team(){
        try {
            $team = TTeam::all();
            return response()->json($team, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }
    public function pais(){
        try {
            $pais = TPais::with('destino')->get();
            return response()->json($pais, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function destinations(TPais $pais){

        try {
            $paquetes_de = TPaqueteDestino::with(['paquetes.precio_paquetes','paquetes.paquetes_destinos.destinos.pais','destinos'=>function(Builder $query) use ($pais) { $query->where('idpais', $pais->id);}])->get();

            $paquetes_show = $paquetes_de->where('destinos', '!=', null)->unique('idpaquetes');

            return response()->json($paquetes_show, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

}
