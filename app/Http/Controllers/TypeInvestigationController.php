<?php

namespace App\Http\Controllers;

use App\Http\Resources\TypeInvestigationResource;
use App\Models\TypeInvestigation;
use Illuminate\Http\Request;

class TypeInvestigationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $typeInvestigation = TypeInvestigation::get();
        return TypeInvestigationResource::collection($typeInvestigation);
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
    public function show(TypeInvestigation $tipeInvestigation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeInvestigation $tipeInvestigation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeInvestigation $tipeInvestigation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeInvestigation $tipeInvestigation)
    {
        //
    }
}
