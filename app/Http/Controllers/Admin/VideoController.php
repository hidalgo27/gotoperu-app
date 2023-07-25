<?php

namespace App\Http\Controllers\Admin;

use App\TPaquete;
use App\TVideoTestimonio2;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $video = TVideoTestimonio2::paginate(10);
        return view('admin.video', compact('video'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.video-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $title = $_POST["txt_title"];
        $codigo = $_POST["txt_codigo"];

        if ($request->filled(['txt_codigo', 'txt_title'])){

            $video = new TVideoTestimonio2();
            $video->codigo = $codigo;
            $video->titulo = $title;
            $video->estado = 1;

            if ($video->save()){
                return redirect(route('admin_video_edit_path', $video->id))->with('status', 'Video created successfully');
            }


        }else{
            return "false";
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $video = TVideoTestimonio2::where('id', $id)->get();

        return view('admin.video-edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $codigo = $_POST["txt_codigo"];
        $title = $_POST["txt_title"];

        if ($request->filled(['txt_codigo', 'txt_title'])){

            $video = TVideoTestimonio2::FindOrFail($id);
            $video->codigo = $codigo;
            $video->titulo = $title;
            $video->save();

            return redirect(route('admin_video_edit_path', $id))->with('status', 'Successfully updated video');

        }else{
            return "false";
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $video = TVideoTestimonio2::find($id);

        if ($video->imagen != NULL){
            $filename = explode('video-testimonio/', $video->imagen);
            $filename = $filename[1];
            Storage::disk('s3')->delete('video-testimonio/' . $filename);
        }
        $video->delete();
        return redirect(route('admin_video_index_path'))->with('delete', 'Itinerary successfully removed');
    }
    public function image_store(Request $request)
    {
//        $image = $request->file('file');
        $id_video = $request->input('id_video_file');
//        $imageName = $image->getClientOriginalName();
//        $image->move(public_path('images/video-testimonio/'), $imageName);


        $filenamewithextension = $request->file('file')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('file')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.time().'.'.$extension;

        Storage::disk('s3')->put('video-testimonio/'.$filenametostore, fopen($request->file('file'), 'r+'), 'public');
        $imageName = Storage::disk('s3')->url('video-testimonio/'.$filenametostore);



        $imageUpload = TVideoTestimonio2::FindOrFail($id_video);
        $imageUpload->imagen = $imageName;
        $imageUpload->save();
        return response()->json(['success' => $imageName]);
    }

    public function image_delete(Request $request)
    {

//        $filename = $request->get('filename');
//        $id_video = TVideoTestimonio2::where('imagen', $filename)->first();

        $id_video = $request->get('id_video_file');

        $video = TVideoTestimonio2::FindOrFail($id_video);

        $filename = explode('video-testimonio/', $video->imagen);
        $filename = $filename[1];
        Storage::disk('s3')->delete('video-testimonio/'.$filename);

        TVideoTestimonio2::where('id', $id_video)->update(['imagen' => NULL]);
    }

    public function image_delete_form(Request $request)
    {
        $id_video = $request->get('id_video');

        $video = TVideoTestimonio2::FindOrFail($id_video);

        $filename = explode('video-testimonio/', $video->imagen);
        $filename = $filename[1];
        Storage::disk('s3')->delete('video-testimonio/'.$filename);

        TVideoTestimonio2::where('id', $id_video)->update(['imagen' => NULL]);


        return redirect(route('admin_video_edit_path', $id_video))->with('status', 'Successfully updated video');
    }



}
