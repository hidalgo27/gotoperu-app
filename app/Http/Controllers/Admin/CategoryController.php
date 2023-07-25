<?php

namespace App\Http\Controllers\admin;

use App\TCategoria;
use App\TSeo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $host = $_SERVER["HTTP_HOST"];
        $category = TCategoria::paginate(10);
        $seo=TSeo::where('estado', 3)->get();
        return view('admin.category', compact('category','host','seo'));
    }

    public function store(Request $request)
    {
        if ($request->filled(['txt_category'])){

            $category2 = new TCategoria();
            $category2->nombre = $request->input('txt_category');
            $category2->url = $request->input('url');
            $category2->descripcion = $request->input('txta_descripcion');
            $category2->imagen=$request->input('id_blog_file');
            $category2->imagen_banner=$request->input('id_blog_file2');
            $category2->save();
            $cat_recover=TCategoria::latest()->first();
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
                
                $seo->estado=3;
                $seo->id_t=$cat_recover->id;
                $seo->save();
            }
            return redirect(route('admin_category_index_path'))->with('status', 'Category created successfully');

        }else{
            return "false";
        }
    }

    public function edit($id)
    {
        $host = $_SERVER["HTTP_HOST"];
        $categoria = TCategoria::where('id', $id)->get();
        $seo=TSeo::where('estado', 3)->where('id_t',$id)->get()->first();
        return view('admin.category-edit', compact('categoria', 'host','seo'));
    }

    public function update(Request $request, $id)
    {

        if ($request->filled(['txt_category'])){

            $category2 = TCategoria::FindOrFail($id);
            $category2->nombre = $request->input('txt_category');
            $category2->url = $request->input('url');
            $category2->descripcion = $request->input('txta_descripcion');
            if ($request->has('chk_order')){
                $category2->estado = $request->input('chk_order');
            }else{
                $category2->estado = 0;
            }

            if ($request->has('chk_order_block')){
                $category2->orden_block = $request->input('chk_order_block');
            }else{
                $category2->orden_block = 0;
            }

            $category2->grupo = $request->input('slc_group');
            $category2->save();

            return redirect(route('admin_category_edit_path', $category2->id))->with('status', 'Successfully updated category');

        }else{
            return "false";
        }
    }

    public function destroy($id)
    {
        $category2=TCategoria::find($id);

        if ($category2->imagen != NULL) {
            $filename = explode('category/', $category2->imagen);
            $filename = $filename[1];
            Storage::disk('s3')->delete('category/' . $filename);
            TCategoria::where('id', $id)->update(['imagen' => NULL]);
        }
        if ($category2->imagen_banner != NULL) {
            $filename = explode('category/banner/', $category2->imagen_banner);
            $filename = $filename[1];
            Storage::disk('s3')->delete('category/banner/'.$filename);
            TCategoria::where('id', $id)->update(['imagen_banner' => NULL]);
        }

        $category2->delete();
        $postsEO=TSeo::where('estado',3)->where('id_t', $id)->first();
        if($postsEO!=null){
            if ($postsEO->imagen != NULL) {
                $filename = explode('seo/category/', $postsEO->imagen);
                $filename = $filename[1];
                Storage::disk('s3')->delete('seo/category/' . $filename);
                TSeo::where('id', $id)->update(['imagen' => NULL]);
            }
            $postsEO->delete();
        }
        return redirect(route('admin_category_index_path'))->with('delete', 'Category successfully removed');
    }


    public function image_category_slider_store(Request $request)
    {
        $image = $request->file('file');
        $id_category = $request->get('id_category_file');

        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;

        Storage::disk('s3')->put('category/banner/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('category/banner/'.$filenametostore);

        $imageUpload = TCategoria::FindOrFail($id_category);
        $imageUpload->imagen_banner = $imageName;
        $imageUpload->save();

        return response()->json(['success' => $imageName]);
    }

    public function image_category_slider_delete(Request $request)
    {
        $id_category_file = $request->get('id_category_file');
        $category = TCategoria::find($id_category_file);

        $filename = explode('category/banner/', $category->imagen_banner);
        $filename = $filename[1];
        Storage::disk('s3')->delete('category/banner/'.$filename);

        TCategoria::where('id', $id_category_file)->update(['imagen_banner' => NULL]);

        return $filename;

    }

    public function image_category_slider_form_delete(Request $request)
    {
        $id_category_file = $request->get('id_category');
        $category = TCategoria::find($id_category_file);

        $filename = explode('category/banner/', $category->imagen_banner);
        $filename = $filename[1];
        Storage::disk('s3')->delete('category/banner/'.$filename);

        TCategoria::where('id', $id_category_file)->update(['imagen_banner' => NULL]);

        return redirect(route('admin_category_edit_path', $id_category_file))->with('status', 'Successfully updated video');
    }



    public function image_category_image_store(Request $request)
    {

        $id_category = $request->get('id_category_file');

        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;

        Storage::disk('s3')->put('category/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('category/'.$filenametostore);

        $imageUpload = TCategoria::FindOrFail($id_category);
        $imageUpload->imagen = $imageName;
        $imageUpload->save();

        return response()->json(['success' => $imageName]);

    }

    public function image_category_image_delete(Request $request)
    {

        $id_category_file = $request->get('id_category_file');
        $category = TCategoria::find($id_category_file);

        $filename = explode('category/', $category->imagen);
        $filename = $filename[1];
        Storage::disk('s3')->delete('category/'.$filename);

        TCategoria::where('id', $id_category_file)->update(['imagen' => NULL]);

        return $filename;

    }

    public function image_category_image_form_delete(Request $request)
    {

        $id_category_file = $request->get('id_category');
        $category = TCategoria::find($id_category_file);

        $filename = explode('category/', $category->imagen);
        $filename = $filename[1];
        Storage::disk('s3')->delete('category/'.$filename);

        TCategoria::where('id', $id_category_file)->update(['imagen' => NULL]);


        return redirect(route('admin_category_edit_path', $id_category_file))->with('status', 'Successfully updated video');
    }
    //
    public function category_imagen_getFile(Request $request){
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        error_log($filenamewithextension);
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;
        
        Storage::disk('s3')->put('category/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('category/'.$filenametostore);
        return $imageName;
    }
    public function category_imagen_deleteFile(Request $request){
        $id_blog_file = $request->get('id_blog_file');

        $filename = explode('category/', $id_blog_file);
        $filename = $filename[1];
        Storage::disk('s3')->delete('category/'.$filename);

        return $filename;
    }
    public function category_slider_getFile(Request $request){
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;
        
        Storage::disk('s3')->put('category/banner/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('category/banner/'.$filenametostore);
        return $imageName;
    }
    public function category_slider_deleteFile(Request $request){
        $id_blog_file = $request->get('id_blog_file2');

        $filename = explode('category/banner/', $id_blog_file);
        $filename = $filename[1];
        Storage::disk('s3')->delete('category/banner/'.$filename);

        return $filename;
    }
}
