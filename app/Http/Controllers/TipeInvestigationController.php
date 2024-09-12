<?php

namespace App\Http\Controllers;

use App\Http\Resources\TipeInvestigationResource;
use App\Models\TipeInvestigation;
use Illuminate\Http\Request;

class TipeInvestigationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipeInvestigation = TipeInvestigation::get();
        return TipeInvestigationResource::collection($tipeInvestigation);
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
    public function show(TipeInvestigation $tipeInvestigation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipeInvestigation $tipeInvestigation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipeInvestigation $tipeInvestigation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipeInvestigation $tipeInvestigation)
    {
        //
    }
}
