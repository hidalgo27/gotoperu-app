<?php

namespace App\Http\Controllers\Admin;

use App\TItinerario;
use App\TItinerarioImagen;
use App\TPaqueteImagen;
use Faker\Provider\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ItineraryController extends Controller
{

    public function index()
    {
        $itinerary = TItinerario::paginate(10);
        return view('admin.itinerary', compact('itinerary'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'codigo' => 'required|unique:titinerario',
        ]);

        $codigo = $_POST["codigo"];
        $title = $_POST["txt_title"];
        $short = $_POST["txta_short"];
        $extended = $_POST["txta_extended"];

        if ($request->filled(['txt_title'])){

            $itinerary = new TItinerario();
            $itinerary->codigo = $codigo;
            $itinerary->titulo = $title;
            $itinerary->resumen = $short;
            $itinerary->descripcion = $extended;
            $itinerary->save();
            $itinerary_reco=TItinerario::latest()->first();
            $imagenes=$request->input('id_blog_file2');
            if($imagenes!=null){
                $porciones = explode(",", $imagenes);
                foreach($porciones as $key) {
                    $imageUpload = new TItinerarioImagen();
                    $imageUpload->nombre = $key;
                    $imageUpload->iditinerario = $itinerary_reco->id;
                    $imageUpload->save();
                }
            }
            return redirect(route('admin_itinerary_edit_path', $itinerary->id))->with('status', 'Itinerary created successfully');

        }else{
            return "false";
        }
    }

    public function create()
    {
        return view('admin.itinerary-create');
    }

    public function edit($id)
    {
        $itinerary = TItinerario::with('itinerario_imagen')->where('id', $id)->get();

        return view('admin.itinerary-edit', ['itinerary'=>$itinerary]);
    }

    public function update(Request $request, $id)
    {
//        $validator = Validator::make($request->all(), [
//            'codigo' => 'unique:titinerario',
//        ]);
//        if ($validator->fails()) {
//            return redirect(route('admin_itinerary_edit_path', $id))
//                ->withErrors($validator)
//                ->withInput();
//        }

        $validatedData = $request->validate([
            'codigo' => 'required',
        ]);

        $codigo = $_POST["codigo"];
        $title = $_POST["txt_title"];
        $short = $_POST["txta_short"];
        $extended = $_POST["txta_extended"];

        if ($request->filled(['txt_title'])){

            $itinerary = TItinerario::FindOrFail($id);
            $itinerary->codigo = $codigo;
            $itinerary->titulo = $title;
            $itinerary->resumen = $short;
            $itinerary->descripcion = $extended;
            $itinerary->save();

            return redirect(route('admin_itinerary_edit_path', $id))->with('status', 'Successfully updated itinerary');

        }else{
            return "false";
        }
    }

    public function destroy($id)
    {
        $itinerary=TItinerario::find($id);

        $itinerario_imagen = TItinerarioImagen::where('iditinerario', $id)->get();
        $itinerario_imagen_1 = TItinerarioImagen::where('iditinerario', $id)->first();

        if ($itinerario_imagen_1){
            foreach ($itinerario_imagen as $itinerario_aws){
                $filename = explode('itinerary/', $itinerario_aws->nombre);
                $filename = $filename[1];
                Storage::disk('s3')->delete('itinerary/'.$filename);
            }
        }
        TItinerarioImagen::where('id', $itinerario_imagen_1->id)->delete();

        $itinerary->delete();
        return redirect(route('admin_itinerary_index_path'))->with('delete', 'Itinerary successfully removed');
    }

    public function image_store(Request $request)
    {

        if($request->hasFile('file')) {

            //get filename with extension
            $filenamewithextension = $request->file('file')->getClientOriginalName();

            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

            //get file extension
            $extension = $request->file('file')->getClientOriginalExtension();

            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;

            //Upload File to s3
            Storage::disk('s3')->put('itinerary/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');

            //Store $filenametostore in the database

            $imageName = Storage::disk('s3')->url('itinerary/'.$filenametostore);

        }

        $imageUpload = new TItinerarioImagen();
        $imageUpload->nombre = $imageName;
        $imageUpload->iditinerario = $request->input('id_itinerary_file');
        $imageUpload->save();
        return response()->json(['success' => $imageName]);
    }

    public function image_delete(Request $request)
    {
        $filename = $request->get('name_file');
        $id_itinerary = $request->get('id_itinerary_file');

        $filename = explode('.', $filename);
        $filename=$filename[0];

        $itinerario_imagen = TItinerarioImagen::where('iditinerario', $id_itinerary)->where('nombre', 'like', '%'.$filename.'%')->first();

        $filename = explode('itinerary/', $itinerario_imagen->nombre);
        $filename = $filename[1];
        Storage::disk('s3')->delete('itinerary/'.$filename);

        TItinerarioImagen::where('id', $itinerario_imagen->id)->delete();

        return $filename;
    }

    public function image_delete_form(Request $request)
    {
        $filename = $request->get('filename');
        $id_itinerario = $request->get('id_itinerario');
        TItinerarioImagen::where('nombre', $filename)->delete();

        $filename = explode('itinerary/', $filename);
        $filename = $filename[1];
        Storage::disk('s3')->delete('itinerary/'.$filename);

        return redirect(route('admin_itinerary_edit_path', $id_itinerario))->with('delete', 'Image successfully removed');
    }

    public function image_list(Request $request)
    {
//        $filename = $request->get('filename');
//        TItinerarioImagen::where('id', 41)->get();

//        $images = Image::get(['original_name', 'filename']);

        $images = TItinerarioImagen::where('id', 41)->get();

        $imageAnswer = [];

        foreach ($images as $image) {
            $imageAnswer[] = [
                'original' => $image->nombre,
                'server' => $image->nombre,
                'size' => \File::size(public_path('/images/itinerario/' . $image->nombre))
            ];
        }

        return response()->json([
            'images' => $imageAnswer
        ]);

    }
    //
    public function itinerary_slider_getFile(Request $request){
        $t=time();
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.$t.'.'.$extension;
        
        Storage::disk('s3')->put('itinerary/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('itinerary/'.$filenametostore);
        return $imageName." ".$t;
    }
    public function itinerary_slider_deleteFile(Request $request){
        $imagenes = $request->get('aux');
        $file_name = $request->get('name_file');
        $filename2=explode(".",$file_name);
        $name="";
        $porciones = explode(",", $imagenes);
        foreach($porciones as $key) {
            $part = explode(" ", $key);
            $part2= explode($part[2], $part[0]);
            $part3=explode($part[1],$part2[1]);
            error_log($part3[0]);
            error_log($filename2[0].'_');
            if($part3[0]==($filename2[0].'_')){
                $name=$key;
            }
        }
        $filename = explode('itinerary/', $name);
        $filename = explode(' ', $filename[1]);
        $filename = $filename[0];
        Storage::disk('s3')->delete('itinerary/'.$filename);
        return $name;
    }
}
