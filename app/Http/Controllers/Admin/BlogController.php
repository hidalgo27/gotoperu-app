<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\TBlog_post;
use App\TSeo;
use App\TBlog_categoria;
use App\TBlog_imagen;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Support\Facades\URL;

class BlogController extends Controller
{
    public function index()
    {
        $seo=TSeo::where('estado', 0)->get();
        $posts=TBlog_post::paginate(10);
        return view('admin.blog', compact('posts','seo'));
    }
    public function create()
    {
        $host = $_SERVER["HTTP_HOST"];
        $categorias=TBlog_categoria::all();
        return view('admin.blog-create', compact('categorias','host'));
    }
    public function store(Request $request)
    {  
        $cate=TBlog_categoria::where('nombre',$request->input('slc_category'))->first();
        if ($request->filled(['txt_titulo', 'url'])){
            $post = new TBlog_post();
            $post->titulo = $request->input('txt_titulo');
            $post->categoria_id =$cate->id;
            $post->url = $request->input('url');
            $post->detalle = $request->input('txta_short');
            $post->user_id= Auth::user()->id;
            $post->imagen_miniatura=$request->input('id_blog_file');
            $post->save();
            $post_recover=TBlog_post::latest()->first();
            $imagenes=$request->input('id_blog_file2');
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
                
                $seo->estado=0;
                $seo->id_t=$post_recover->id;
                $seo->save();
            }
            if($imagenes!=null){
                $porciones = explode(",", $imagenes);
                foreach($porciones as $key) {
                    $imageUpload = new TBlog_imagen();
                    $imageUpload->nombre = $key;
                    $imageUpload->post_id = $post_recover->id;
                    $imageUpload->miniatura=0;
                    $imageUpload->save();
                }
            }
            
            return redirect(route('admin_blog_index_path'))->with('status', 'Post created successfully');
        }else{
            return "false";
        }
        
    }
    public function edit($id)
    {
        $categorias=TBlog_categoria::all();
        $host = $_SERVER["HTTP_HOST"];
        $post = TBlog_post::where('id', $id)->with(['categoria'])->get()->first();
        $seo=TSeo::where('estado', 0)->where('id_t',$post->id)->get()->first();
        return view('admin.blog-edit', compact('post','categorias','seo','host'));
    }

    public function update(Request $request, $id)
    {
        $cate=TBlog_categoria::where('nombre',$request->input('slc_category'))->first();
        if ($request->filled(['txt_titulo', 'url'])){
            $post = TBlog_post::FindOrFail($id);
            $post->titulo = $request->input('txt_titulo');
            $post->categoria_id =$cate->id;
            $post->url = $request->input('url');
            $post->detalle = $request->input('txta_short');
            $post->user_id= Auth::user()->id;
            $post->save();

            return redirect(route('admin_blog_edit_path', $id))->with('status', 'Successfully updated post');

        }else{
            return "false";
        }
    }
    public function destroy($id)
    {
        $post=TBlog_post::find($id);
        $postsEO=TSeo::where('estado',0)->where('id_t', $id)->first();
        if ($post->imagen_miniatura != NULL) {
            $filename = explode('blog/', $post->imagen_miniatura);
            $filename = $filename[1];
            Storage::disk('s3')->delete('blog/' . $filename);
        }

        $post_imagen = TBlog_imagen::where('post_id', $id)->get();

        if ($post_imagen){
            foreach ($post_imagen as $imagen) {
                $filename = explode('blog/slider/', $imagen->nombre);
                $filename = $filename[1];
                Storage::disk('s3')->delete('blog/slider/'.$filename);
                $imagen->delete();
            }
        }
        if($postsEO!=null){
            if ($postsEO->imagen != NULL) {
                $filename = explode('seo/blog/', $postsEO->imagen);
                $filename = $filename[1];
                Storage::disk('s3')->delete('seo/blog/' . $filename);
                TSeo::where('id', $id)->update(['imagen' => NULL]);
            }
            $postsEO->delete();
        }

        $post->delete();
        return redirect(route('admin_blog_index_path'))->with('delete', 'Post successfully removed');
    }
    public function categoria_store(Request $request){
        $cate=TBlog_categoria::where('nombre',$request->input('slc_category'))->first();
        if ($request->filled(['txt_titulo', 'url'])){
            $post = new TBlog_post();
            $post->titulo = $request->input('txt_titulo');
            $post->categoria_id =$cate->id;
            $post->url = $request->input('url');
            $post->detalle = $request->input('txta_short');
            $post->user_id= Auth::user()->id;
            $post->imagen_miniatura=$this->$imagen;
            $post->save();
            return redirect(route('admin_blog_index_path'))->with('status', 'Post created successfully');
        }else{
            return "false";
        }
    }
    public function blog_image_store(Request $request)
    {
        $id_blog = $request->get('id_blog_file');
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;
        
        Storage::disk('s3')->put('blog/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('blog/'.$filenametostore);
        
        $imageUpload = TBlog_post::FindOrFail($id_blog);
        $imageUpload->imagen_miniatura = $imageName;
        $imageUpload->save();

        return response()->json(['success' => $imageName]);
    }
    public function blog_image_delete(Request $request)
    {
        $id_blog_file = $request->get('id_blog_file');
        $post = TBlog_post::find($id_blog_file);

        $filename = explode('blog/', $post->imagen_miniatura);
        $filename = $filename[1];
        Storage::disk('s3')->delete('blog/'.$filename);

        TBlog_post::where('id', $id_blog_file)->update(['imagen' => NULL]);
        return $filename;
    }
    public function blog_image_form_delete(Request $request)
    {
        $id_blog = $request->get('id_blog');

        $post = TBlog_post::find($id_blog);

        $filename = explode('blog/', $post->imagen_miniatura);
        $filename = $filename[1];
        Storage::disk('s3')->delete('blog/'.$filename);

        TBlog_post::where('id', $id_blog)->update(['imagen_miniatura' => NULL]);

        return redirect(route('admin_blog_edit_path', $id_blog))->with('delete', 'Image successfully removed');
    }
    public function blog_slider_store(Request $request)
    {
        $id_blog = $request->get('id_blog_file');

        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;
        
        Storage::disk('s3')->put('blog/slider/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('blog/slider/'.$filenametostore);

        $imageUpload = new TBlog_imagen();
        $imageUpload->nombre = $imageName;
        $imageUpload->post_id = $id_blog;
        $imageUpload->miniatura=0;
        $imageUpload->save();
        return response()->json(['success' => $imageName]);
    }
    public function blog_slider_delete(Request $request)
    {
        $filename = $request->get('name_file');
        $id_blog_file = $request->get('id_blog_file');

        $filename = explode('.', $filename);
        $filename=$filename[0];

        $blog_imagen = TBlog_imagen::where('post_id', $id_blog_file)->where('nombre', 'like', '%'.$filename.'%')->first();

        $filename = explode('blog/slider/', $blog_imagen->nombre);
        $filename = $filename[1];
        Storage::disk('s3')->delete('blog/slider/'.$filename);

        TBlog_imagen::where('id', $blog_imagen->id)->delete();

        return $filename;
    }

    public function blog_slider_form_delete(Request $request)
    {
        $id_blog_imagen = $request->get('id_blog_imagen');
        $id_blog = $request->get('id_blog');

        $blog_imagen = TBlog_imagen::find($id_blog_imagen);

        $filename = explode('blog/slider/', $blog_imagen->nombre);
        $filename = $filename[1];
        Storage::disk('s3')->delete('blog/slider/'.$filename);

        TBlog_imagen::where('id', $id_blog_imagen)->delete();

        return redirect(route('admin_blog_edit_path', $id_blog))->with('delete', 'Image successfully removed');


    }
    public function blog_imagen_getFile(Request $request){
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;
        
        Storage::disk('s3')->put('blog/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('blog/'.$filenametostore);
        return $imageName;
    }
    public function blog_imagen_deleteFile(Request $request){
        $id_blog_file = $request->get('id_blog_file');

        $filename = explode('blog/', $id_blog_file);
        $filename = $filename[1];
        Storage::disk('s3')->delete('blog/'.$filename);

        return $filename;
    }
    public function blog_slider_getFile(Request $request){
        $t=time();
        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.$t.'.'.$extension;
        
        Storage::disk('s3')->put('blog/slider/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('blog/slider/'.$filenametostore);
        return $imageName." ".$t;
    }
    public function blog_slider_deleteFile(Request $request){
        $imagenes = $request->get('aux');
        $file_name = $request->get('name_file');
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
        $filename = explode('blog/slider/', $name);
        $filename = explode(' ', $filename[1]);
        $filename = $filename[0];
        Storage::disk('s3')->delete('blog/slider/'.$filename);
        return $name;
    }
}
