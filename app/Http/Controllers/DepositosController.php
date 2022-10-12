<?php

namespace App\Http\Controllers;

use App\Models\depositos;
use Illuminate\Http\Request;

class DepositosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDepositos()
    {
        return depositos::all();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\depositos  $depositos
     * @return \Illuminate\Http\Response
     */
    public function show(depositos $depositos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\depositos  $depositos
     * @return \Illuminate\Http\Response
     */
    public function edit(depositos $depositos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\depositos  $depositos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, depositos $depositos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\depositos  $depositos
     * @return \Illuminate\Http\Response
     */
    public function destroy(depositos $depositos)
    {
        //
    }
}
