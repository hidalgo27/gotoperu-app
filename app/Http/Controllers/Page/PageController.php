<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\TCategoria;
use App\Models\TDestino;
use App\Models\THotel;
use App\Models\THotelDestino;
use App\Models\TInquire;
use App\Models\TPais;
use App\Models\TPaquete;
use App\Models\TPaqueteDestino;
use App\Models\TPost;
use App\Models\TTeam;
use App\Models\TTestimonio;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function packages(){
        try {
            $paquetes = TPaquete::
            with('paquetes_destinos.destinos.pais', 'precio_paquetes', 'imagen_paquetes')->get();
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

    public function packages_detail($latam,$url) {
        try {
            $paquetes = TPaquete::with('paquete_itinerario.itinerarios', 'paquetes_destinos.destinos.pais','paquetes_destinos.destinos.destino_imagen', 'precio_paquetes', 'imagen_paquetes')->where('url', $url)->get();
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
    public function     pais(){
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
            $destinations = TDestino::where('idpais',$pais->id)->get();
            return response()->json($destinations, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function destinations_show(TPais $pais, TDestino $destinos){

        try {
            $paquetes_api = TPaqueteDestino::
            with('paquetes.precio_paquetes','destinos', 'paquetes.paquetes_destinos.destinos')
                ->where('iddestinos', $destinos->id)
                ->get();
            return response()->json($paquetes_api, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }

    }

    public function country(TPais $pais){

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

    public function blog(){
        try {
            $blogs = TPost::latest('id')->get();
            $blogs_first = TPost::latest('id')->first();
            $category = TCategoria::all();
            return response()->json(['blog_first'=>$blogs_first, 'blogs'=>$blogs, 'category'=>$category], 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }
    public function blog_show(TPost $post){

//        return view('page.blog-show', compact('post', 'category'));

        $posts = TPost::with('categoria', 'imagenes')->where('id', $post->id)->get();


        try {
            return response()->json($posts, 200);
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }



    public function formulario_diseno(Request $request)
    {


        $from = 'paul@gotoperu.com';

        $category_all = '';
        if ($request->category_d){
            foreach ($request->category_d as $categorias){
                if (isset($categorias)){
                    $category_all.=$categorias.',';
                }
            }
        }

        $destination_all = '';
        if ($request->destino_d){
            foreach ($request->destino_d as $destinos){
                if (isset($destinos)){
                    $destination_all.=$destinos.',';
                }
            }
        }



//        $travellers_all = '';
//        if ($request->pasajeros_d){
//            foreach ($request->pasajeros_d as $pasajeros){
//                if (isset($pasajeros)){
//                    $travellers_all.=$pasajeros.',';
//                }
//            }
//        }

        $travellers_all = '';
        if ($request->pasajeros_d){
            $travellers_all = $request->pasajeros_d;
        }

        $duration_all = '';
        if ($request->duracion_d){
            foreach ($request->duracion_d as $duracion){
                if (isset($duracion)){
                    $duration_all.=$duracion.',';
                }
            }
        }


        $package = '';
        if ($request->el_package){
            $package = $request->el_package;
        }

        $nombre = '';
        if ($request->el_nombre){
            $nombre = $request->el_nombre;
        }

        $email = '';
        if ($request->el_email){
            $email = $request->el_email;
        }

//        $fecha = '';
//        if ($request->el_fecha){
//            $fecha = $request->el_fecha;
//        }

        $fecha = '';
        if ($request->el_fecha){
            foreach ($request->el_fecha as $date){
                if (isset($date)){
                    $fecha.=$date;
                }
            }
        }

        $telefono = '';
        if ($request->el_telefono){
            $telefono = $request->el_telefono;
        }

        $country = '';
        if ($request->country){
            $country = $request->country;
        }


        $comentario = '';
        if ($request->el_textarea){
            $comentario = $request->el_textarea;
        }

//        $inquire = new TInquire();
//        $inquire->hotel = $category_all;
//        $inquire->destinos = $destination_all;
//        $inquire->pasajeros = $travellers_all;
////        $inquire->duracion = $duration_all;
//        $inquire->nombre = $nombre;
//        $inquire->email = $email;
//        $inquire->fecha = $fecha;
//        $inquire->telefono = $telefono;
//        $inquire->comentario = $comentario;
//        $inquire->save();

        if ($email){
            try {
                Mail::send(['html' => 'notifications.page.client-form-design'], ['nombre' => $nombre], function ($messaje) use ($email, $nombre) {
                    $messaje->to($email, $nombre)
                        ->subject('GOTOPERU')
                        /*->attach('ruta')*/
                        ->from('paul@gotoperu.com', 'GOTOPERU');
                });
                Mail::send(['html' => 'notifications.page.admin-form-contact'], [
                    'package' => $package,
                    'category_all' => $category_all,
                    'destination_all' => $destination_all,
                    'travellers_all' => $travellers_all,
                    'duration_all' => $duration_all,

                    'nombre' => $nombre,
                    'email' => $email,
                    'fecha' => $fecha,
                    'telefono' => $telefono,
                    'comentario' => $comentario,

                    'country' => $country

                ], function ($messaje) use ($from) {
                    $messaje->to($from, 'GOTOPERU')
                        ->subject('GOTOPERU')
//                    ->cc($from2, 'GOTOPERU')
                        /*->attach('ruta')*/
                        ->from('paul@gotoperu.com', 'GOTOPERU');
                });

                return response()->json('Thank you.', 200);
            }
            catch (Exception $e){
                return $e;
            }
        }

    }

}
