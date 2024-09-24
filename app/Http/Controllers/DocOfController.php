<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocOfResource;
use App\Models\DocOf;
use App\Models\Solicitude;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocOfController extends Controller
{
    public function offPDF() {
        // Asegúrate de que tu vista se llame 'off.blade.php'
        $pdf = Pdf::loadView('office');
    
        return $pdf->stream(''); // Puedes especificar un nombre para el archivo PDF
    }

    public function getOffices(){
        $docs = DocOf::where('of_status', 'tramitado')->get();

        return DocOfResource::collection($docs);
    }
    
}
