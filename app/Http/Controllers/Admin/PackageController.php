<?php

namespace App\Http\Controllers\Admin;

use App\TPaquete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PackageController extends Controller
{
    public function estado_home(Request $request){
        if ( !empty($request->input('txt_estado')) ){
            $status_homepage = TPaquete::FindOrFail($request->input('id_estado'));
            $status_homepage->estado = 1;
            $status_homepage->save();
            return 1;
        }else{
            $status_homepage = TPaquete::FindOrFail($request->input('id_estado'));
            $status_homepage->estado = 0;
            $status_homepage->save();
            return 0;
        }

    }
    public function offer_home(Request $request){
        if ( !empty($request->input('txt_offer')) ){
            $status_homepage = TPaquete::FindOrFail($request->input('id_paquete'));
            $status_homepage->offers_home = 1;
            $status_homepage->save();
            return 1;
        }else{
            $status_homepage = TPaquete::FindOrFail($request->input('id_paquete'));
            $status_homepage->offers_home = 0;
            $status_homepage->save();
            return 0;
        }

    }
    public function is_package(Request $request){
        if ( !empty($request->input('txt_is_package')) ){
            $status_homepage = TPaquete::FindOrFail($request->input('id_is_package'));
            $status_homepage->is_p_t = 1;
            $status_homepage->save();
            return 1;
        }else{
            $status_homepage = TPaquete::FindOrFail($request->input('id_is_package'));
            $status_homepage->is_p_t = 0;
            $status_homepage->save();
            return 0;
        }

    }
    public function is_tours(Request $request){
        if ( !empty($request->input('txt_is_tour')) ){
            $is_tour = TPaquete::FindOrFail($request->input('id_is_tours'));
            $is_tour->is_tours = 1;
            $is_tour->save();
            return 1;
        }else{
            $is_tour = TPaquete::FindOrFail($request->input('id_is_tours'));
            $is_tour->is_tours = 0;
            $is_tour->save();
            return 0;
        }

    }

    public function descuento(Request $request){
        $descuento = TPaquete::FindOrFail($request->input('id_paquete'));
        $descuento->descuento = $request->input('descuento_rdo');
        $descuento->save();

        return back()->withInput();
    }
}
