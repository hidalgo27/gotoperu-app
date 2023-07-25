<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\TDestino;
use App\THotel;
use App\THotelDestino;
use App\TPaquete;
use App\TPaqueteDestino;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    public function index()
    {
        $hotel = THotel::paginate(10);
        return view('admin.hotel', compact('hotel'));
    }
    public function create()
    {
        $destinations = TDestino::all()->sortBy('nombre');
        $host = $_SERVER["HTTP_HOST"];
        return view('admin.hotel-create', compact('host','destinations'));
    }
    public function store(Request $request)
    {
        $hotel = $request->input('txt_hotel');

        $hotel = new THotel();
        $hotel->nombre = $request->input('txt_hotel');
        $hotel->descripcion = $request->input('txta_short');
        $hotel->estrellas = $request->input('slc_category');
        $hotel->direccion = $request->input('txt_address');
        $hotel->url = $request->input('url');
        $hotel->expedia = $request->input('txt_Expedia');
        $hotel->tripadvisor = $request->input('txt_Tripadvisor');
        $hotel->imagen=$request->input('id_blog_file');
        $servicios = "";
        foreach ($request->input('slc_services') as $services){
            $servicios .=  $services.',';
        }
        $hotel->servicios = substr($servicios, 0, -1);

        if ($hotel->save()){
            $hotel_destinations = THotelDestino::where('idhotel', $hotel->id)->get();
            $var_d = [];
            if ($request->input('destino')) {
                foreach ($hotel_destinations as $hotel_d){
                    if (!in_array($hotel_d->iddestinos, $request->input('destino'))){
                        $temp = THotelDestino::find($hotel_d->id);
                        $temp->delete();
                    }
                    $var_d[] = $hotel_d->iddestinos;
                }
                for($i=0; $i < count($request->input('destino')); $i++){
                    if (!in_array($request->input('destino')[$i], $var_d)){
                        $hotel_destinations = new THotelDestino();
                        $hotel_destinations->idhotel = $hotel->id;
                        $hotel_destinations->iddestinos = $request->input('destino')[$i];
                        $hotel_destinations->save();
                    }
                }
            }else{
                THotelDestino::where('idhotel', $hotel->id)->delete();
            }
        }

        return redirect(route('admin_hotel_index_path'))->with('status', 'Hotel created successfully');

    }
    public function edit($id)
    {
        $hotel = THotel::where('id', $id)->get();
        $destinations = TDestino::all();
        $hotel_destino = THotelDestino::where('idhotel', $id)->get();
        $host = $_SERVER["HTTP_HOST"];
        return view('admin.hotel-edit', compact('hotel','host','destinations','hotel_destino'));
    }

    public function update(Request $request, $id)
    {
        $hotel = THotel::FindOrFail($id);
        $hotel->nombre = $request->input('txt_hotel');
        $hotel->descripcion = $request->input('txta_short');
        $hotel->estrellas = $request->input('slc_category');
        $hotel->direccion = $request->input('txt_address');
        $hotel->url = $request->input('url');
        $hotel->expedia = $request->input('txt_Expedia');
        $hotel->tripadvisor = $request->input('txt_Tripadvisor');
        if ($request->input('slc_services')){
            $servicios = "";
            foreach ($request->input('slc_services') as $services){
                $servicios .=  $services.',';
            }
            $hotel->servicios = substr($servicios, 0, -1);
        }else{
            $hotel->servicios = NULL;
        }

        if ($hotel->save()){
            if ($request->input('destino')){
                THotelDestino::where('idhotel', $id)->delete();
                for($i=0; $i < count($request->input('destino')); $i++){

                    $hotel_destinations = new THotelDestino();
                    $hotel_destinations->idhotel = $id;
                    $hotel_destinations->iddestinos = $request->input('destino')[$i];
                    $hotel_destinations->save();

                }
            }else{
                THotelDestino::where('idhotel', $id)->delete();
            }
        }

        return redirect(route('admin_hotel_edit_path', $id))->with('status', 'Successfully updated package');

    }
    public function destroy($id)
    {
        $hotel=THotel::find($id);
        $hotel_destino=THotelDestino::where('idhotel',$id)->first();
        if($hotel_destino){
            return redirect(route('admin_hotel_index_path'))->with('status', 'It cannot be deleted');
        }else{
            
            if ($hotel->imagen != NULL) {
                $filename = explode('hotel/', $hotel->imagen);
                $filename = $filename[1];
                Storage::disk('s3')->delete('hotel/' . $filename);
            }
            $hotel->delete();
            return redirect(route('admin_hotel_index_path'))->with('status', 'Hotel successfully removed');
        }
        
    }
    public function image_store(Request $request)
    {
        $id_hotel = $request->get('id_hotel_file');

        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;

        Storage::disk('s3')->put('hotel/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('hotel/'.$filenametostore);

        $imageUpload = THotel::FindOrFail($id_hotel);
        $imageUpload->imagen = $imageName;
        $imageUpload->save();

        return response()->json(['success' => $imageName]);
    }

    public function image_delete_form(Request $request)
    {
        $id_hotel_file = $request->input('id_hotel');
        $hotel = THotel::find($id_hotel_file);

        $filename = explode('hotel/', $hotel->imagen);
        $filename = $filename[1];
        Storage::disk('s3')->delete('hotel/'.$filename);

        THotel::where('id', $id_hotel_file)->update(['imagen' => NULL]);
//        return $filename;

        return redirect(route('admin_hotel_edit_path', $id_hotel_file))->with('delete', 'Image successfully removed');
    }

    public function image_delete_hotel(Request $request)
    {
        $id_hotel_file = $request->input('id_hotel_file');
        $hotel = THotel::find($id_hotel_file);

        $filename = explode('hotel/', $hotel->imagen);
        $filename = $filename[1];
        Storage::disk('s3')->delete('hotel/'.$filename);

        THotel::where('id', $id_hotel_file)->update(['imagen' => NULL]);
        return $filename;
    }
    public function hotel_imagen_getFile(Request $request){
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;
        
        Storage::disk('s3')->put('hotel/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('hotel/'.$filenametostore);
        return $imageName;
    }
    public function hotel_imagen_deleteFile(Request $request){
        $id_blog_file = $request->get('id_blog_file');
        error_log($id_blog_file);
        $filename = explode('hotel/', $id_blog_file);
        $filename = $filename[1];
        Storage::disk('s3')->delete('hotel/'.$filename);

        return $filename;
    }
}

