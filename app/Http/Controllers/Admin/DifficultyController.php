<?php

namespace App\Http\Controllers\admin;

use App\TDificultad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DifficultyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $difficulty = TDificultad::all()->sortBy('dia');
        return view('admin.difficulty', compact('difficulty'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        return view('admin.included-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $difficulty = $_POST["txt_difficulty"];
        $descripcion = $_POST["txta_descripcion"];

        if ($request->filled(['txt_difficulty'])){

            $difficulty2 = new TDificultad();
            $difficulty2->nombre = $difficulty;
            $difficulty2->descripcion = $descripcion;
            $difficulty2->save();

            return redirect(route('admin_difficulty_index_path'))->with('status', 'Difficulty created successfully');

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
        //
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
        $difficulty = $_POST["txt_difficulty"];
        $descripcion = $_POST["txta_descripcion"];

        if ($request->filled(['txt_difficulty', 'txta_descripcion'])){

            $difficulty2 = TDificultad::FindOrFail($id);
            $difficulty2->nombre = $difficulty;
            $difficulty2->descripcion = $descripcion;
            $difficulty2->save();

            return redirect(route('admin_difficulty_index_path'))->with('status', 'Successfully updated difficulty');

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
        $difficulty2=TDificultad::find($id);
        $difficulty2->delete();
        return redirect(route('admin_difficulty_index_path'))->with('delete', 'Difficulty successfully removed');
    }
}
