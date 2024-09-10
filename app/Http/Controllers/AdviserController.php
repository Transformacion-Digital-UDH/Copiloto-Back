<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdviserResource;
use App\Models\Adviser;
use Illuminate\Http\Request;

class AdviserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $adviser = Adviser::get();
        return AdviserResource::collection($adviser);
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
        $adviser = Adviser::create($request->all());

        return response()->json([
            'status' => true,
                'message' => "Adviser Created successfully!",
            'user' => $adviser
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Adviser $adviser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Adviser $adviser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Adviser $adviser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Adviser $adviser)
    {
        //
    }
}
