<?php

namespace App\Http\Controllers\Admin;

use App\TComentario;
use App\THotel;
use App\THotelDestino;
use App\TItinerarioImagen;
use App\TPaquete;
use App\TPaqueteDestino;
use App\TPaqueteDificultad;
use App\TPaqueteVuelo;
use App\TPasajero;
use App\TVuelo;
use App\User;
use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class InquireController extends Controller
{
    public function index(Request $request, $id)
    {
        $request->user()->authorizeRoles(['user', 'admin']);

        $paquete = TPaquete::with('paquetes_destinos', 'precio_paquetes', 'imagen_paquetes', 'paquete_incluye', 'paquete_no_incluye')->where('estado', 0)->get();
        $paquete_destinos = TPaqueteDestino::with('destinos')->get();
        $paquete_iti = TPaquete::with('paquete_itinerario','paquetes_destinos', 'precio_paquetes', 'paquetes_categoria')->where('id', $id)->get();

        $hoteles = THotel::all();
        $hoteles_destinos = THotelDestino::all();

        $vuelo = TVuelo::all();
        $paquete_vuelo = TPaqueteVuelo::with('vuelos')->get();

        $dificultad = TPaqueteDificultad::all();
        $comentario = TComentario::with('itinerario')->get();

        $imagen = TItinerarioImagen::with('itinerario')->get();

        return view('admin.inquire', compact('paquete','paquete_destinos','paquete_iti','hoteles','hoteles_destinos','vuelo','paquete_vuelo','dificultad','comentario','imagen'));

    }

    public function store(Request $request)
    {
        $id_user = $_POST["id_user"];
        $id_package = $_POST["id_package"];

        $advisor = $_POST["txt_advisor"];
        $name_passenger = $_POST["txt_name_passenger"];
        $email = $_POST["txt_email"];
        $date = $_POST["txt_date"];
        $travellers = $_POST["txt_travellers"];

        if ($request->filled(['id_user', 'id_package'])){

            $inquire = new TPasajero();
            $inquire->nombre = $name_passenger;
            $inquire->email = $email;
            $inquire->fecha = $date;
            $inquire->pasajeros = $travellers;
            $inquire->id_paquete = $id_package;
            $inquire->id_usuario = $id_user;
            $inquire->save();

            return redirect(route('admin_inquire_edit_index_path', $inquire->id))->with('status', 'Code generate successfully');
//            return back()->withInput()->with('status', 'Code generate successfully');

        }else{
            return "false";
        }
    }

    public function list()
    {
        $pasajeros = TPasajero::all();
        return view('admin.inquire-list', compact('pasajeros'));
    }

    public function edit($id)
    {

        $app_url = env('APP_URL');
        $id_crypt = base64_encode($id);
        $inquire = TPasajero::find($id);
        return view('admin.inquire-edit', compact('inquire','id_crypt', 'app_url'));
    }

    public function destroy($id)
    {
        $inquire=TPasajero::find($id);
        $inquire->delete();
        return redirect(route('admin_list_index_path'))->with('delete', 'Inquire successfully removed');
    }

    public function package_pdf($id)
    {
        $pasajero=TPasajero::find($id);
        $user=User::find($pasajero->id_usuario);
        $paquete_destinos = TPaqueteDestino::with('destinos')->get();
        $paquete = TPaquete::with('paquete_itinerario','paquetes_destinos', 'precio_paquetes')->where('id', $pasajero->id_paquete)->get();

        $pdf = PDF::

        loadView('admin.pdf.package',
            compact(
                'pasajero',
                'paquete',
                'paquete_destinos',
                'user'
            )
        );

//        return $pdf->download('certificado_'.time().'.pdf');
        return $pdf->stream();
    }
}
