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
            with('paquetes_destinos.destinos.pais', 'precio_paquetes')->get();
            return response()->json($paquetes, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }
    public function packages_top(){
        try {
            $paquetes = TPaquete::
            with('paquetes_destinos.destinos.pais', 'precio_paquetes')
                ->where('estado', '1')
                ->get();
            return response()->json($paquetes, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }
    public function packages_offers(){
        try {
            $paquetes = TPaquete::
            with('paquetes_destinos.destinos.pais', 'precio_paquetes')
                ->where('offers_home', '1')
                ->get();
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
            $team = TTeam::orderBy('id', 'desc')->get();

            return response()->json($team);
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
            /*$paquetes_de = TPaqueteDestino::with(['paquetes.precio_paquetes','paquetes.paquetes_destinos.destinos.pais','destinos'=>function(Builder $query) use ($pais) { $query->where('idpais', $pais->id);}])->get();

            $paquetes_show = $paquetes_de->where('destinos', '!=', null)->unique('idpaquetes');*/

            $paquetes_api = DB::table('tpaquetesdestinos')
                ->join('tdestinos', 'tpaquetesdestinos.iddestinos', '=', 'tdestinos.id')
//            ->select('idpais', DB::raw('count(*) as user_count'))
////            ->count('idpais')
/// ->join('tdestinos', 'tpaquetesdestinos.iddestinos', '=', 'tdestinos.id')
                ->select('idpaquetes', 'idpais')
                ->groupByRaw('idpaquetes, idpais')
//                ->select('idpaquetes', 'idpais',DB::raw('count(idpaquetes) as user_count'))
//            ->toArray();
//            ->select('idpaquetes', 'user_count')
//                ->groupByRaw('idpaquetes, user_count')
                ->get();


//        $paquetes_api = $paquetes_api
//            ->select(DB::raw('count(idpaquetes) as user_count'))
//            ->groupBy('idpaquetes')
//            ->get()
//        ;


            $paquetes_api = ($paquetes_api->groupBy('idpaquetes'));

            return response()->json($paquetes_api, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

}
