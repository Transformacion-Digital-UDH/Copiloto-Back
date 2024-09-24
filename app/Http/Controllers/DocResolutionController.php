<?php

namespace App\Http\Controllers;

use App\Models\DocOf;
use App\Models\DocResolution;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocResolutionController extends Controller
{
    public function resPDF($id) {
        $office = DocOf::where('_id', $id)->first();

        // Verifica si el registro no se encuentra
        if (!$office) {
            return redirect()->back()->with('error', 'Oficio no encontrado');
        }

        // Asegúrate de que tu vista se llame 'resolution_adviser.blade.php'
        // Puedes pasar el objeto $office a la vista si es necesario
        $pdf = Pdf::loadView('resolution_adviser');

        // Especifica un nombre para el archivo PDF
        return $pdf->stream(); 
}

}
