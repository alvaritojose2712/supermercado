<?php

namespace App\Http\Controllers;

use App\Models\items_devoluciones;
use App\Http\Requests\Storeitems_devolucionesRequest;
use App\Http\Requests\Updateitems_devolucionesRequest;

class ItemsDevolucionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Storeitems_devolucionesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Storeitems_devolucionesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\items_devoluciones  $items_devoluciones
     * @return \Illuminate\Http\Response
     */
    public function show(items_devoluciones $items_devoluciones)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\items_devoluciones  $items_devoluciones
     * @return \Illuminate\Http\Response
     */
    public function edit(items_devoluciones $items_devoluciones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Updateitems_devolucionesRequest  $request
     * @param  \App\Models\items_devoluciones  $items_devoluciones
     * @return \Illuminate\Http\Response
     */
    public function update(Updateitems_devolucionesRequest $request, items_devoluciones $items_devoluciones)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\items_devoluciones  $items_devoluciones
     * @return \Illuminate\Http\Response
     */
    public function destroy(items_devoluciones $items_devoluciones)
    {
        //
    }
}
