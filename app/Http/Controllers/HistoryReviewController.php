<?php

namespace App\Http\Controllers;

use App\Models\HistoryReview;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HistoryReviewController extends Controller
{
    public function viewRevisionByStudent($student_id) {
        // Obtiene solo los campos rev_file, rev_count, student_id, adviser_id y updated_at, ordenados por rev_count
        $reviews = HistoryReview::where('student_id', $student_id)
                                 ->orderBy('rev_count', 'desc')
                                 ->select('rev_file', 'rev_count', 'updated_at', 'rev_status') // Selecciona los campos necesarios
                                 ->get();
    
        // Transformamos los datos
        $data = $reviews->map(function ($review) {
            return [
                'rev_file' => $review->rev_file,
                'rev_count' => $review->rev_count,
                'updated_at' => Carbon::parse($review->updated_at)->format('d/m/Y | H:i:s'), // Formato personalizado
                'rev_status' => $review->rev_status,
            ];
        });
    
        return response()->json([
            'data' => $data,
        ], 200);
    }
    
}
