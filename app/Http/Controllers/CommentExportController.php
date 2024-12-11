<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CommentExportController extends Controller
{
    public function exportCommentsToExcel($solicitudeId)
    {
        try {
            // Validar que el comentario exista
            $commentsModel = Comment::where('solicitude_id', $solicitudeId)->first();

            if (!$commentsModel) {
                return response()->json(['error' => 'No se encontraron comentarios'], 404);
            }

            // Generar nombre de archivo único
            $fileName = 'comentarios_' . $solicitudeId . '_' . now()->format('YmdHis') . '.xlsx';

            // Retornar la descarga del archivo Excel
            return Excel::download(new CommentsExport($commentsModel), $fileName);

        } catch (\Exception $e) {
            // Registrar cualquier error
            Log::error('Error al exportar comentarios: ' . $e->getMessage());
            return response()->json(['error' => 'Error al exportar comentarios', 'details' => $e->getMessage()], 500);
        }
    }
}

// Clase de exportación personalizada
class CommentsExport implements FromCollection, WithHeadings
{
    protected $commentsModel;

    public function __construct($commentsModel)
    {
        $this->commentsModel = $commentsModel;
    }

    public function collection()
    {
        $exportData = collect();

        // Recorrer comentarios
        foreach ($this->commentsModel->getComments() as $comment) {
            // Añadir comentario principal
            $exportData->push([
                'tipo' => 'Comentario',
                'id' => $comment['id'],
                'autor' => $comment['author'],
                'contenido' => $comment['content'],
                'fecha' => Carbon::parse($comment['created_time'])->format('Y-m-d H:i:s'),
                'resuelto' => $comment['resolved'] ? 'Sí' : 'No',
                'estado_historial' => $this->formatStatusHistory($comment['status_history'] ?? [])
            ]);

            // Añadir respuestas
            foreach ($comment['replies'] as $reply) {
                $exportData->push([
                    'tipo' => 'Respuesta',
                    'id' => $reply['id'],
                    'autor' => $reply['author'],
                    'contenido' => $reply['content'],
                    'fecha' => Carbon::parse($reply['created_time'])->format('Y-m-d H:i:s'),
                    'resuelto' => 'N/A',
                    'estado_historial' => ''
                ]);
            }
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'Tipo',
            'ID',
            'Autor',
            'Contenido',
            'Fecha',
            'Resuelto',
            'Historial de Estado'
        ];
    }

    // Método para formatear el historial de estado
    protected function formatStatusHistory($statusHistory)
    {
        if (empty($statusHistory)) {
            return '';
        }

        return collect($statusHistory)->map(function($status) {
            return implode(' | ', [
                'Acción: ' . $status['action'],
                'Fecha: ' . Carbon::parse($status['timestamp'])->format('Y-m-d H:i:s'),
                'Por: ' . ($status['by'] ?? 'N/A')
            ]);
        })->implode('; ');
    }
}