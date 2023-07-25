<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TVideoTestimonio;
use App\Models\TCategoria;
use App\Models\TDestino;
use App\Models\TDificultad;
use App\Models\TIncluye;
use App\Models\TItinerario;
use App\Models\TItinerarioImagen;
use App\Models\TNoIncluye;

use App\Models\TPaquete;
use App\Models\TSeo;
use App\Models\TPaqueteCategoria;
use App\Models\TPaqueteDestino;
use App\Models\TPaqueteDificultad;
use App\Models\TPaqueteImagen;
use App\Models\TPaqueteIncluye;
use App\Models\TpaqueteItinerario;
use App\Models\TPaqueteNoIncluye;
use App\Models\TPrecioPaquete;
use App\TVideoTestimonio2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }
    public function index(Request $request)
    {
        $request->user()->authorizeRoles(['user', 'admin']);

        $paquete = TPaquete::paginate(10);
        $seo= TSeo::where('estado', 1)->get();
        return view('admin.home', compact('paquete','seo'));
    }
    /**
     * Show
     */
//    public function show(Request $request, $id)
//    {
//        $request->user()->authorizeRoles(['user', 'admin']);
//
//        $paquete = TPaquete::where('id', $id)->get();
//        $itinerario = TItinerario::get()->unique('dia');
//        $itinerario_full = TItinerario::all();
//        $level = TDificultad::all();
//        $category = TCategoria::all();
//        $destinations = TDestino::all();
//        $incluye = TIncluye::all();
//        $noincluye = TNoIncluye::all();
//        return view('admin.package-show', compact('paquete'), ['itinerario'=>$itinerario, 'itinerario_full' => $itinerario_full, 'level'=>$level, 'category'=>$category, 'destinations'=>$destinations, 'incluye'=>$incluye, 'noincluye'=>$noincluye]);
//    }

    public function duration(Request $request)
    {
        $request->user()->authorizeRoles(['user', 'admin']);
        $id_itinerary = $_POST['id_itinerary'];
        $id_itinerary_i = explode('-', $id_itinerary);
        $itinerario = TItinerario::where('id', $id_itinerary_i[0])->get();
        foreach ($itinerario as $item) {
//            return [$item->resumen, $id_itinerary_i[1]];
            return response()
                ->json(['resumen' => $item->resumen, 'descripcion' => $item->descripcion, 'id' => $id_itinerary_i[1]]);
        }
    }

    public function load(Request $request, $id, $duration)
    {
        $duration = $duration;
        $request->user()->authorizeRoles(['user', 'admin']);

        $paquete = TPaquete::where('id', $id)->get();
        $itinerario = TItinerario::get()->take($duration);
        $itinerario_full = TItinerario::all();


        return view('layouts.admin.load', compact('paquete'), ['itinerario'=>$itinerario, 'itinerario_full' => $itinerario_full]);
    }

    public function create(Request $request)
    {
        $host = $_SERVER["HTTP_HOST"];
        $request->user()->authorizeRoles(['user', 'admin']);

//        $paquete = TPaquete::where('id', $id)->get();
        $itinerario = TItinerario::get()->unique('dia');
        $itinerario_full = TItinerario::all();
        $level = TDificultad::all();
        $category = TCategoria::all();
        $destinations = TDestino::all();
        $incluye = TIncluye::all();
        $noincluye = TNoIncluye::all();
        return view('admin.package-create', compact('host'),['itinerario'=>$itinerario, 'itinerario_full' => $itinerario_full, 'level'=>$level, 'category'=>$category, 'destinations'=>$destinations, 'incluye'=>$incluye, 'noincluye'=>$noincluye]);
    }
    public function store(Request $request)
    {

//        $validator = $request->validate([
//            'txt_codigo' => 'required'
//        ]);

        $validator = Validator::make($request->all(), [
            'codigo' => 'required|unique:tpaquetes',
//            'codigo_f' => 'required',
            'titulo' => 'required|unique:tpaquetes',
            'duracion' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect(route('admin_package_create_path'))
                ->withErrors($validator)
                ->withInput();
        }

        $package = new TPaquete();
        $package->codigo = $request->input('codigo');
        $package->codigo_f = $request->input('codigo_f');
        $package->titulo = $request->input('titulo');
        $package->precio_tours = $request->input('precio_tours');
        $package->duracion = $request->input('duracion');
        $package->altitud = $request->input('highest');
        $package->group_size = $request->input('group_size');
        $package->url = $request->input('url');
        $package->descripcion = $request->input('descripcion');
        $package->incluye = $request->input('txta_included');
        $package->noincluye = $request->input('txta_not_included');
        $package->opcional = $request->input('txta_optional');
        $package->imagen=$request->input('id_blog_file');
        $package->mapa=$request->input('id_file_map');
        if ($package->save()){
            $seo_atributos=$request->input('seo_atributos');
            $imagen_seo=$request->input('imagen_seo2');
            if($seo_atributos!=null){
                $porciones = explode(",", $seo_atributos);
                $seo = new TSeo();
                $seo->titulo=$porciones[0];
                $seo->descripcion = $porciones[1];
                $seo->url = $porciones[2];
                $seo->og_tipo=$porciones[3];
                $seo->keywords=$porciones[4];
                $seo->microdata=$porciones[5];
                $seo->localizacion=$porciones[6];
                $seo->nombre_sitio=$porciones[7];
                $seo->imagen=$imagen_seo;
                if($porciones[8]==null){
                    $seo->imagen_width=null;
                }else{
                    $seo->imagen_width=$porciones[8];
                }
                if($porciones[9]==null){
                    $seo->imagen_height=null;
                }else{
                    $seo->imagen_height=$porciones[9];
                }

                $seo->estado=1;
                $seo->id_t=$package->id;
                $seo->save();
            }
            $imagenes=$request->input('id_blog_file2');
            if($imagenes!=null){
                $porciones = explode(",", $imagenes);
                foreach($porciones as $key) {
                    $imageUpload = new TPaqueteImagen();
                    $imageUpload->nombre = $key;
                    $imageUpload->idpaquetes = $package->id;
                    $imageUpload->save();
                }
            }

            for($x=2; $x < 6; $x++){
                $price = new TPrecioPaquete();
                $price->estrellas = $x;
                $price->precio_s = $request->input('txt_'.$x.'_s');
                $price->precio_d = $request->input('txt_'.$x.'_d');
                $price->precio_t = $request->input('txt_'.$x.'_t');
                $price->idpaquetes = $package->id;
                $price->save();
            }

            $itinerario_val = explode('-', $request->input('itinerary')[0]);
            if ($itinerario_val[0] > 0){
                for($y=0; $y < count($request->input('itinerary')); $y++){
                    $itinerario = explode('-', $request->input('itinerary')[$y]);
                    $itinerary = new TpaqueteItinerario();
                    $itinerary->idpaquetes = $package->id;
                    $itinerary->iditinerario = $itinerario[0];
                    $itinerary->save();
                }
            }


            $package_level = TPaqueteDificultad::where('idpaquetes', $package->id)->get();
            $var = [];
            if ($request->input('level')) {
                foreach ($package_level as $package_l){
                    if (!in_array($package_l->iddificultad, $request->input('level'))){
                        $temp = TPaqueteDificultad::find($package_l->id);
                        $temp->delete();
                    }
                    $var[] = $package_l->iddificultad;
                }
                for($i=0; $i < count($request->input('level')); $i++){
                    if (!in_array($request->input('level')[$i], $var)){
                        $package_level = new TPaqueteDificultad();
                        $package_level->idpaquetes = $package->id;
                        $package_level->iddificultad = $request->input('level')[$i];
                        $package_level->save();
                    }
                }
            }else{
                TPaqueteDificultad::where('idpaquetes', $package->id)->delete();
            }

            $package_category = TPaqueteCategoria::where('idpaquetes', $package->id)->get();
            $var_c = [];
            if ($request->input('category')) {
                foreach ($package_category as $package_c){
                    if (!in_array($package_c->idcategoria, $request->input('category'))){
                        $temp = TPaqueteCategoria::find($package_c->id);
                        $temp->delete();
                    }
                    $var_c[] = $package_c->idcategoria;
                }
                for($i=0; $i < count($request->input('category')); $i++){
                    if (!in_array($request->input('category')[$i], $var_c)){
                        $package_category = new TPaqueteCategoria();
                        $package_category->idpaquetes = $package->id;
                        $package_category->idcategoria = $request->input('category')[$i];
                        $package_category->save();
                    }
                }
            }else{
                TPaqueteCategoria::where('idpaquetes', $package->id)->delete();
            }

            $package_destinations = TPaqueteDestino::where('idpaquetes', $package->id)->get();
            $var_d = [];
            if ($request->input('destino')) {
                foreach ($package_destinations as $package_d){
                    if (!in_array($package_d->iddestinos, $request->input('destino'))){
                        $temp = TPaqueteDestino::find($package_d->id);
                        $temp->delete();
                    }
                    $var_d[] = $package_d->iddestinos;
                }
                for($i=0; $i < count($request->input('destino')); $i++){
                    if (!in_array($request->input('destino')[$i], $var_d)){
                        $package_destinations = new TPaqueteDestino();
                        $package_destinations->idpaquetes = $package->id;
                        $package_destinations->iddestinos = $request->input('destino')[$i];
                        $package_destinations->save();
                    }
                }
            }else{
                TPaqueteDestino::where('idpaquetes', $package->id)->delete();
            }

//                $package_included = TPaqueteIncluye::where('idpaquetes', $package->id)->get();
//                $var_i = [];
//                if ($request->input('include')) {
//                    foreach ($package_included as $package_i){
//                        if (!in_array($package_i->idincluye, $request->input('include'))){
//                            $temp = TPaqueteIncluye::find($package_i->id);
//                            $temp->delete();
//                        }
//                        $var_i[] = $package_i->idincluye;
//                    }
//                    for($i=0; $i < count($request->input('include')); $i++){
//                        if (!in_array($request->input('include')[$i], $var_i)){
//                            $package_included = new TPaqueteIncluye();
//                            $package_included->idpaquetes = $package->id;
//                            $package_included->idincluye = $request->input('include')[$i];
//                            $package_included->save();
//                        }
//                    }
//                }else{
//                    TPaqueteIncluye::where('idpaquetes', $package->id)->delete();
//                }
//
//                $package_no_included = TPaqueteNoIncluye::where('idpaquetes', $package->id)->get();
//                $var_i = [];
//                if ($request->input('noinclude')) {
//                    foreach ($package_no_included as $package_no_i){
//                        if (!in_array($package_no_i->idnoincluye, $request->input('noinclude'))){
//                            $temp = TPaqueteNoIncluye::find($package_no_i->id);
//                            $temp->delete();
//                        }
//                        $var_i[] = $package_no_i->idnoincluye;
//                    }
//                    for($i=0; $i < count($request->input('noinclude')); $i++){
//                        if (!in_array($request->input('noinclude')[$i], $var_i)){
//                            $package_no_included = new TPaqueteNoIncluye();
//                            $package_no_included->idpaquetes = $package->id;
//                            $package_no_included->idnoincluye = $request->input('noinclude')[$i];
//                            $package_no_included->save();
//                        }
//                    }
//                }else{
//                    TPaqueteNoIncluye::where('idpaquetes', $package->id)->delete();
//                }

        }
//            return redirect('/home')->with('status', 'Package created successfully');
        return redirect(route('admin_package_edit_path', $package->id))->with('status', 'Package created successfully');

    }

    public function edit(Request $request, $id)
    {
        $host = $_SERVER["HTTP_HOST"];

        $request->user()->authorizeRoles(['user', 'admin']);

        $paquete = TPaquete::with('imagen_paquetes')->where('id', $id)->get();
        $paquete_itinerario = TpaqueteItinerario::with('itinerarios')->where('idpaquetes', $id)->get();

        $itinerario_full = TItinerario::all();

        $level = TDificultad::all();
        $paquete_dificultad = TPaqueteDificultad::where('idpaquetes', $id)->get();

        $category = TCategoria::all();
        $paquete_category = TPaqueteCategoria::where('idpaquetes', $id)->get();

        $destinations = TDestino::all();
        $paquete_destino = TPaqueteDestino::where('idpaquetes', $id)->get();

        $incluye = TIncluye::all();
        $paquete_incluye = TPaqueteIncluye::where('idpaquetes', $id)->get();

        $noincluye = TNoIncluye::all();
        $paquete_no_incluye = TPaqueteNoIncluye::where('idpaquetes', $id)->get();

        $precio_paquetes_2 = TPrecioPaquete::where('idpaquetes', $id)->where('estrellas', 2)->get();
        $precio_paquetes_3 = TPrecioPaquete::where('idpaquetes', $id)->where('estrellas', 3)->get();
        $precio_paquetes_4 = TPrecioPaquete::where('idpaquetes', $id)->where('estrellas', 4)->get();
        $precio_paquetes_5 = TPrecioPaquete::where('idpaquetes', $id)->where('estrellas', 5)->get();

        $seo=TSeo::where('estado', 1)->where('id_t',$id)->get()->first();

        return view('admin.package-edit', compact('id','seo','paquete','precio_paquetes_2', 'precio_paquetes_3','precio_paquetes_4','precio_paquetes_5','paquete_dificultad','paquete_category','paquete_destino','paquete_incluye','paquete_no_incluye','host'), ['paquete_itinerario'=>$paquete_itinerario, 'itinerario_full' => $itinerario_full, 'level'=>$level, 'category'=>$category, 'destinations'=>$destinations, 'incluye'=>$incluye, 'noincluye'=>$noincluye]);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required',
//            'codigo_f' => 'required',
            'titulo' => 'required',
            'duracion' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect(route('admin_package_create_path'))
                ->withErrors($validator)
                ->withInput();
        }

        $package = TPaquete::FindOrFail($id);
        $package->codigo = $request->input('codigo');
        $package->codigo_f = $request->input('codigo_f');
        $package->titulo = $request->input('titulo');
        $package->precio_tours = $request->input('precio_tours');
        $package->duracion = $request->input('duracion');
        $package->altitud = $request->input('highest');
        $package->group_size = $request->input('group_size');
        $package->url = $request->input('url');
        $package->descripcion = $request->input('descripcion');
        $package->incluye = $request->input('txta_included');
        $package->noincluye = $request->input('txta_not_included');
        $package->opcional = $request->input('txta_optional');

        if ($package->save()){
            TPrecioPaquete::where('idpaquetes', $id)->delete();
            for($x=2; $x < 6; $x++){
                $price = new TPrecioPaquete();
                $price->estrellas = $x;
                $price->precio_s = $request->input('txt_'.$x.'_s');
                $price->precio_d = $request->input('txt_'.$x.'_d');
                $price->precio_t = $request->input('txt_'.$x.'_t');

//                $price->codigo_s = $request->input('txt_cod_'.$x.'_s');
//                $price->codigo_d = $request->input('txt_cod_'.$x.'_d');
//                $price->codigo_t = $request->input('txt_cod_'.$x.'_t');

//                $check = $request->input('chk_estado_'.$x);
//                dd($request->input('chk_estado_'.$x));
//                if (isset($check)) {
//                    $price->estado = 1;
//                }else{
//                    $price->estado = 0;
//                }

                $price->idpaquetes = $package->id;
                $price->save();
            }

            TpaqueteItinerario::where('idpaquetes', $id)->delete();
            $itinerario_val = explode('-', $request->input('itinerary')[0]);
            if ($itinerario_val[0] > 0){
                for ($y=0; $y < count($request->input('itinerary')); $y++) {
                    $itinerario = explode('-', $request->input('itinerary')[$y]);
                    $itinerary = new TpaqueteItinerario();
                    $itinerary->idpaquetes = $package->id;
                    $itinerary->iditinerario = $itinerario[0];
                    $itinerary->save();
                }
            }

            if ($request->input('level')){
                TPaqueteDificultad::where('idpaquetes', $id)->delete();
                for($i=0; $i < count($request->input('level')); $i++){

                    $package_level = new TPaqueteDificultad();
                    $package_level->idpaquetes = $id;
                    $package_level->iddificultad = $request->input('level')[$i];
                    $package_level->save();

                }
            }else{
                TPaqueteDificultad::where('idpaquetes', $id)->delete();
            }

            if ($request->input('category')){
                TPaqueteCategoria::where('idpaquetes', $id)->delete();
                for($i=0; $i < count($request->input('category')); $i++){
                    $package_category = new TPaqueteCategoria();
                    $package_category->idpaquetes = $id;
                    $package_category->idcategoria = $request->input('category')[$i];
                    $package_category->save();

                }
            }else{
                TPaqueteCategoria::where('idpaquetes', $id)->delete();
            }

            if ($request->input('destino')){
                TPaqueteDestino::where('idpaquetes', $id)->delete();
                for($i=0; $i < count($request->input('destino')); $i++){

                    $package_destinations = new TPaqueteDestino();
                    $package_destinations->idpaquetes = $id;
                    $package_destinations->iddestinos = $request->input('destino')[$i];
                    $package_destinations->save();

                }
            }else{
                TPaqueteDestino::where('idpaquetes', $id)->delete();
            }

//            TPaqueteIncluye::where('idpaquetes', $id)->delete();
//            for($i=0; $i < count($request->input('incluye')); $i++){
//
//                $package_included = new TPaqueteIncluye();
//                $package_included->idpaquetes = $id;
//                $package_included->idincluye = $request->input('incluye')[$i];
//                $package_included->save();
//
//            }
//
//            TPaqueteNoIncluye::where('idpaquetes', $id)->delete();
//            for($i=0; $i < count($request->input('no_incluye')); $i++){
//
//                $package_no_included = new TPaqueteNoIncluye();
//                $package_no_included->idpaquetes = $id;
//                $package_no_included->idnoincluye = $request->input('no_incluye')[$i];
//                $package_no_included->save();
//
//            }

        }
        return redirect(route('admin_package_edit_path', $id))->with('status', 'Successfully updated package');

    }

    public function destroy($id)
    {
        $packages=TPaquete::find($id);

        if ($packages->imagen != NULL){
            $filename = explode('package/', $packages->imagen);
            $filename = $filename[1];
            Storage::disk('s3')->delete('package/'.$filename);
            TPaquete::where('id', $id)->update(['imagen' => NULL]);
        }

        $tpaquete_imagen = TPaqueteImagen::where('idpaquetes', $id)->get();
        $tpaquete_imagen_1 = TPaqueteImagen::where('idpaquetes', $id)->first();

        if ($tpaquete_imagen_1){
            foreach ($tpaquete_imagen as $paquete_aws){
                $filename = explode('package/slider/', $paquete_aws->nombre);
                $filename = $filename[1];
                Storage::disk('s3')->delete('package/slider/'.$filename);
            }
        }

        if ($tpaquete_imagen_1){
            TPaqueteImagen::where('id', $tpaquete_imagen_1->id)->delete();
        }

        $packages->delete();
        $postsEO=TSeo::where('estado',1)->where('id_t', $id)->first();
        if($postsEO!=null){
            if ($postsEO->imagen != NULL) {
                $filename = explode('seo/package/', $postsEO->imagen);
                $filename = $filename[1];
                Storage::disk('s3')->delete('seo/package/' . $filename);
                TSeo::where('id', $id)->update(['imagen' => NULL]);
            }
            $postsEO->delete();
        }
        return redirect('/home')->with('delete', 'Package successfully removed');
    }
    public function image_store(Request $request)
    {
        $id_package = $request->get('id_package_file');

        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;

        Storage::disk('s3')->put('package/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('package/'.$filenametostore);

        $imageUpload = TPaquete::FindOrFail($id_package);
        $imageUpload->imagen = $imageName;
        $imageUpload->save();

        return response()->json(['success' => $imageName]);
    }

    public function image_delete(Request $request)
    {
        $id_package_file = $request->get('id_package_file');
        $paquete = TPaquete::find($id_package_file);

        $filename = explode('package/', $paquete->imagen);
        $filename = $filename[1];
        Storage::disk('s3')->delete('package/'.$filename);

        TPaquete::where('id', $id_package_file)->update(['imagen' => NULL]);
        return $filename;
    }



    public function image_delete_map_package_form(Request $request)
    {
//        $filename = $request->get('filename');
//        $id_package = $request->get('id_package');


        $id_package_file = $request->get('id_package');
        $paquete = TPaquete::find($id_package_file);

        $filename = explode('package/', $paquete->imagen);
        $filename = $filename[1];
        Storage::disk('s3')->delete('package/'.$filename);

        TPaquete::where('id', $id_package_file)->update(['imagen' => NULL]);
//        return $filename;

        return redirect(route('admin_package_edit_path', $id_package_file))->with('delete', 'Image successfully removed');
    }


    public function image_store_slider(Request $request)
    {
        $id_package = $request->get('id_package_file');

        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;

        Storage::disk('s3')->put('package/slider/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('package/slider/'.$filenametostore);


        $imageUpload = new TPaqueteImagen();
        $imageUpload->nombre = $imageName;
        $imageUpload->idpaquetes = $id_package;
        $imageUpload->save();

        return response()->json(['success' => $imageName]);
    }

    public function image_delete_slider(Request $request)
    {
        $filename = $request->get('name_file');
        $id_package = $request->get('id_package_file');

        $filename = explode('.', $filename);
        $filename=$filename[0];

        $tpaquete_imagen = TPaqueteImagen::where('idpaquetes', $id_package)->where('nombre', 'like', '%'.$filename.'%')->first();

        $filename = explode('package/slider/', $tpaquete_imagen->nombre);
        $filename = $filename[1];
        Storage::disk('s3')->delete('package/slider/'.$filename);

        TPaqueteImagen::where('id', $tpaquete_imagen->id)->delete();

        return $filename;
    }

    public function image_delete_package_form(Request $request)
    {
        $filename = $request->get('filename');
        $id_imagen_package = $request->get('id_imagen_package');
        $id_package = $request->get('id_package');

        $paquete_imagen = TPaqueteImagen::find($id_imagen_package);

        $filename = explode('package/slider/', $paquete_imagen->nombre);
        $filename = $filename[1];
        Storage::disk('s3')->delete('package/slider/'.$filename);

        TPaqueteImagen::where('id', $id_imagen_package)->delete();

        return redirect(route('admin_package_edit_path', $id_package))->with('delete', 'Image successfully removed');
    }
     //PAQUETE

     public function package_imagen_getFile(Request $request){
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;

        Storage::disk('s3')->put('package/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('package/'.$filenametostore);
        return $imageName;
    }
    public function package_imagen_deleteFile(Request $request){
        $id_blog_file = $request->get('id_blog_file');

        $filename = explode('package/', $id_blog_file);
        $filename = $filename[1];
        Storage::disk('s3')->delete('package/'.$filename);

        return $filename;
    }
    public function package_map_getFile(Request $request){
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;

        Storage::disk('s3')->put('package/map/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('package/map/'.$filenametostore);
        return $imageName;
    }
    public function package_map_deleteFile(Request $request){
        $id_blog_file = $request->get('id_file_map');

        $filename = explode('package/map/', $id_blog_file);
        $filename = $filename[1];
        Storage::disk('s3')->delete('package/map/'.$filename);

        return $filename;
    }
    public function package_slider_getFile(Request $request){
        $t=time();
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.$t.'.'.$extension;

        Storage::disk('s3')->put('package/slider/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('package/slider/'.$filenametostore);
        return $imageName." ".$t;
    }
    public function package_slider_deleteFile(Request $request){
        $imagenes = $request->get('aux');
        $file_name = $request->get('name_file');
        $filename2=explode(".",$file_name);
        $name="";
        $porciones = explode(",", $imagenes);
        foreach($porciones as $key) {
            $part = explode(" ", $key);
            $part2= explode($part[1], $part[0]);
            $part3= explode("/package/slider/",$part2[0]);
            if($part3[1]==($filename2[0]."_")){
                $name=$key;
            }
        }
        $filename = explode('package/slider/', $name);
        $filename=explode(' ', $filename[1]);
        Storage::disk('s3')->delete('package/slider/'.$filename[0]);
        return $name;
    }
    public function map_store(Request $request)
    {
        $id_package = $request->get('id_package_file');

        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;

        Storage::disk('s3')->put('package/map/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('package/map/'.$filenametostore);

        $imageUpload = TPaquete::FindOrFail($id_package);
        $imageUpload->mapa = $imageName;
        $imageUpload->save();

        return response()->json(['success' => $imageName]);
    }

    public function map_delete(Request $request)
    {
        $id_package_file = $request->get('id_package_file');
        $paquete = TPaquete::find($id_package_file);

        $filename = explode('package/map/', $paquete->mapa);
        $filename = $filename[1];
        Storage::disk('s3')->delete('package/map/'.$filename);

        TPaquete::where('id', $id_package_file)->update(['mapa' => NULL]);
        return $filename;
    }
    public function delete_map_package_form(Request $request)
    {
//        $filename = $request->get('filename');
//        $id_package = $request->get('id_package');


        $id_package_file = $request->get('id_package');
        $paquete = TPaquete::find($id_package_file);

        $filename = explode('package/map/', $paquete->mapa);
        $filename = $filename[1];
        Storage::disk('s3')->delete('package/map/'.$filename);

        TPaquete::where('id', $id_package_file)->update(['mapa' => NULL]);
//        return $filename;

        return redirect(route('admin_package_edit_path', $id_package_file))->with('delete', 'Image successfully removed');
    }
}
