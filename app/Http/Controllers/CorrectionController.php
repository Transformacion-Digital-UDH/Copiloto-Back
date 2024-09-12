<?php

namespace App\Http\Controllers;

use App\Http\Resources\CorrectionResource;
use App\Models\Correction;
use Illuminate\Http\Request;

class CorrectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $correction = Correction::get();
        return CorrectionResource::collection($correction);
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
    public function show(Correction $correction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Correction $correction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Correction $correction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Correction $correction)
    {
        //
    }
}
