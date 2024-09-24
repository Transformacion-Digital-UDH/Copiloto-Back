<?php

namespace App\Http\Controllers;

use App\Models\DocOf;
use App\Models\Solicitude;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocOfController extends Controller
{
    public function offPDF($id) {

        $solicitude = Solicitude::where('_id', $id)->first();
    
        // Verifica si el registro no se encuentra
        if (!$solicitude) {
            return redirect()->back()->with('error', 'Solicitud no encontrada');
        }

        // Asegúrate de que tu vista se llame 'off.blade.php'
        $pdf = Pdf::loadView('office_adviser');
    
        return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
    }
    
}
