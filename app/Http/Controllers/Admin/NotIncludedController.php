<?php

namespace App\Http\Controllers\admin;

use App\TNoIncluye;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotIncludedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $included = TNoIncluye::all()->sortBy('dia');
        return view('admin.not-included', compact('included'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $included = $_POST["txt_included"];
        $estado = $_POST["rdo_estado"];

        if ($request->filled(['txt_included', 'rdo_estado'])){

            $included2 = new TNoIncluye();
            $included2->noincluye = $included;
            $included2->estado = $estado;
            $included2->save();

            return redirect(route('admin_not_included_index_path'))->with('status', 'not included created successfully');

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
        $included = $_POST["txt_included"];
        $estado = $_POST["rdo_estado"];

        if ($request->filled(['txt_included', 'rdo_estado'])){

            $included2 = TNoIncluye::FindOrFail($id);
            $included2->noincluye = $included;
            $included2->estado = $estado;
            $included2->save();

            return redirect(route('admin_not_included_index_path'))->with('status', 'Successfully updated not included');

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
        $included2=TNoIncluye::find($id);
        $included2->delete();
        return redirect(route('admin_not_included_index_path'))->with('delete', 'Not included successfully removed');
    }
}
