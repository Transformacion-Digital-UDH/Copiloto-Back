<?php

namespace App\Http\Controllers;

use App\Http\Resources\JuryResource;
use App\Models\Jury;
use Illuminate\Http\Request;

class JuryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jury = Jury::get()->toArray();
        return response()->json($jury);
//        return JuryResource::collection($jury);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Jury $jury)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jury $jury)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jury $jury)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jury $jury)
    {
        //
    }
}