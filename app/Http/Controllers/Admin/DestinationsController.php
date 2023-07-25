<?php

namespace App\Http\Controllers\admin;

use App\TCategoria;
use App\TDestino;
use App\TDestinoImagen;
use App\TPaquete;
use App\TPaqueteImagen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\TSeo;

class DestinationsController extends Controller
{
    public function index()
    {
        $destinations = TDestino::paginate(10);
        $seo=TSeo::where('estado', 2)->get();
        return view('admin.destinations', compact('destinations','seo'));
    }

    public function create()
    {
        $host = $_SERVER["HTTP_HOST"];
        return view('admin.destinations-create', compact('host'));
    }

    public function store(Request $request)
    {
        if ($request->filled(['txt_destination', 'txt_country'])){

            $destinations = new TDestino();
            $destinations->nombre = $request->input('txt_destination');
            $destinations->region = $request->input('txt_region');
            $destinations->pais = $request->input('txt_country');
            $destinations->url = $request->input('url');
            $destinations->imagen=$request->input('id_blog_file');
            $destinations->resumen = $request->input('txta_short');
            $destinations->descripcion = $request->input('txta_extended');
            $destinations->historia = $request->input('txta_history');
            $destinations->geografia = $request->input('txta_geography');

            $destinations->donde_ir = $request->input('txta_get');
            $destinations->atracciones = $request->input('txta_attractions');
            $destinations->entretenimiento = $request->input('txta_entertainment');
            $destinations->gastronomia = $request->input('txta_gastronomy');
            $destinations->fiestas = $request->input('txta_festivals');

            $destinations->estado = '1';
            $destinations->save();

            $desti_recover=TDestino::latest()->first();
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
                
                $seo->estado=2;
                $seo->id_t=$desti_recover->id;
                $seo->save();
            }
            $imagenes=$request->input('id_blog_file2');
            if($imagenes!=null){
                $porciones = explode(",", $imagenes);
                foreach($porciones as $key) {
                    $imageUpload = new TDestinoImagen();
                    $imageUpload->nombre = $key;
                    $imageUpload->iddestinos = $desti_recover->id;
                    $imageUpload->save();
                }
            }

            return redirect(route('admin_destinations_index_path'))->with('status', 'Destination created successfully');

        }else{
            return "false";
        }
    }

    public function edit($id)
    {
        $destinations = TDestino::where('id', $id)->get();
        $host = $_SERVER["HTTP_HOST"];
        $seo=TSeo::where('estado', 2)->where('id_t',$id)->get()->first();
        return view('admin.destinations-edit', compact('destinations','host','seo'));
    }

    public function update(Request $request, $id)
    {
        if ($request->filled(['txt_destination', 'txt_country'])){

            $destinations = TDestino::FindOrFail($id);
            $destinations->nombre = $request->input('txt_destination');
            $destinations->region = $request->input('txt_region');
            $destinations->pais = $request->input('txt_country');
            $destinations->url = $request->input('url');
            $destinations->resumen = $request->input('txta_short');
            $destinations->descripcion = $request->input('txta_extended');
            $destinations->historia = $request->input('txta_history');
            $destinations->geografia = $request->input('txta_geography');

            $destinations->donde_ir = $request->input('txta_get');
            $destinations->atracciones = $request->input('txta_attractions');
            $destinations->entretenimiento = $request->input('txta_entertainment');
            $destinations->gastronomia = $request->input('txta_gastronomy');
            $destinations->fiestas = $request->input('txta_festivals');

            $destinations->estado = '1';
            $destinations->save();

            return redirect(route('admin_destinations_edit_path', $id))->with('status', 'Successfully updated destination');

        }else{
            return "false";
        }
    }

    public function destroy($id)
    {
        $destinations=TDestino::find($id);

        if ($destinations->imagen != NULL) {
            $filename = explode('destinations/', $destinations->imagen);
            $filename = $filename[1];
            Storage::disk('s3')->delete('destinations/' . $filename);
            TDestino::where('id', $id)->update(['imagen' => NULL]);
        }

        $destino_imagen = TDestinoImagen::where('iddestinos', $id)->get();
        $destino_imagen_1 = TDestinoImagen::where('iddestinos', $id)->first();

        if ($destino_imagen_1){
            foreach ($destino_imagen as $destino_aws) {
                $filename = explode('destinations/slider/', $destino_aws->nombre);
                $filename = $filename[1];
                Storage::disk('s3')->delete('destinations/slider/'.$filename);
                TDestinoImagen::where('id', $destino_imagen_1->id)->delete();
            }
        }
        

        $destinations->delete();
        $postsEO=TSeo::where('estado',2)->where('id_t', $id)->first();
        if($postsEO!=null){
            if ($postsEO->imagen != NULL) {
                $filename = explode('seo/destinations/', $postsEO->imagen);
                $filename = $filename[1];
                Storage::disk('s3')->delete('seo/destinations/' . $filename);
                TSeo::where('id', $id)->update(['imagen' => NULL]);
            }
            $postsEO->delete();
        }
        return redirect(route('admin_destinations_index_path'))->with('delete', 'Destination successfully removed');
    }


    public function image_destinations_slider_store(Request $request)
    {

        $id_destinations = $request->get('id_destinations_file');

        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;

        Storage::disk('s3')->put('destinations/slider/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('destinations/slider/'.$filenametostore);


        $imageUpload = new TDestinoImagen();
        $imageUpload->nombre = $imageName;
        $imageUpload->iddestinos = $id_destinations;
        $imageUpload->save();
        return response()->json(['success' => $imageName]);

    }

    public function image_destinations_slider_delete(Request $request)
    {

        $filename = $request->get('name_file');
        $id_destinations_file = $request->get('id_destinations_file');

        $filename = explode('.', $filename);
        $filename=$filename[0];

        $destino_imagen = TDestinoImagen::where('iddestinos', $id_destinations_file)->where('nombre', 'like', '%'.$filename.'%')->first();

        $filename = explode('destinations/slider/', $destino_imagen->nombre);
        $filename = $filename[1];
        Storage::disk('s3')->delete('destinations/slider/'.$filename);

        TDestinoImagen::where('id', $destino_imagen->id)->delete();

        return $filename;
    }

    public function image_destinations_slider_form_delete(Request $request)
    {
        $id_destinos_imagen = $request->get('id_destinos_imagen');
        $id_destinos = $request->get('id_destinos');

        $destino_imagen = TDestinoImagen::find($id_destinos_imagen);

        $filename = explode('destinations/slider/', $destino_imagen->nombre);
        $filename = $filename[1];
        Storage::disk('s3')->delete('destinations/slider/'.$filename);

        TDestinoImagen::where('id', $id_destinos_imagen)->delete();

        return redirect(route('admin_destinations_edit_path', $id_destinos))->with('delete', 'Image successfully removed');


    }

    public function image_destinations_image_store(Request $request)
    {
        $id_destino = $request->get('id_destinations_file');

        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;

        Storage::disk('s3')->put('destinations/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('destinations/'.$filenametostore);



        $imageUpload = TDestino::FindOrFail($id_destino);
        $imageUpload->imagen = $imageName;
        $imageUpload->save();

        return response()->json(['success' => $imageName]);
    }

    public function image_destinations_image_delete(Request $request)
    {
        $id_destinations_file = $request->get('id_destinations_file');
        $destino = TDestino::find($id_destinations_file);

        $filename = explode('destinations/', $destino->imagen);
        $filename = $filename[1];
        Storage::disk('s3')->delete('destinations/'.$filename);

        TDestino::where('id', $id_destinations_file)->update(['imagen' => NULL]);
        return $filename;
    }

    public function image_destinations_image_form_delete(Request $request)
    {
        $id_package_file = $request->get('id_package');
        $id_destino = $request->get('id_destino');

        $destino = TDestino::find($id_destino);

        $filename = explode('destinations/', $destino->imagen);
        $filename = $filename[1];
        Storage::disk('s3')->delete('destinations/'.$filename);

        TDestino::where('id', $id_destino)->update(['imagen' => NULL]);


        return redirect(route('admin_destinations_edit_path', $id_destino))->with('delete', 'Image successfully removed');
    }
    //
    public function destinations_imagen_getFile(Request $request){
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;
        
        Storage::disk('s3')->put('destinations/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('destinations/'.$filenametostore);
        return $imageName;
    }
    public function destinations_imagen_deleteFile(Request $request){
        $id_blog_file = $request->get('id_blog_file');

        $filename = explode('destinations/', $id_blog_file);
        $filename = $filename[1];
        Storage::disk('s3')->delete('destinations/'.$filename);

        return $filename;
    }
    public function destinations_slider_getFile(Request $request){
        $t=time();
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.$t.'.'.$extension;
        
        Storage::disk('s3')->put('destinations/slider/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('destinations/slider/'.$filenametostore);
        return $imageName." ".$t;
    }
    public function destinations_slider_deleteFile(Request $request){
        $imagenes = $request->get('aux');
        $file_name = $request->get('name_file');
        error_log($imagenes);
        $filename2=explode(".",$file_name);
        $name="";
        $porciones = explode(",", $imagenes);
        foreach($porciones as $key) {
            $part = explode(" ", $key);
            $part2= explode($part[2], $part[0]);
            $part3=explode($part[1],$part2[1]);
            if($part3[0]==($filename2[0].'_')){
                $name=$key;
            }
        }
        $filename = explode('destinations/slider/', $name);
        $filename = explode(' ', $filename[1]);
        $filename = $filename[0];
        error_log($filename[1]);
        Storage::disk('s3')->delete('destinations/slider/'.$filename);
        return $name;
    }
}
