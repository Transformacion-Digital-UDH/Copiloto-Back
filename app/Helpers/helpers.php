<?php

use Carbon\Carbon;

// Formatear "11 de julio de 2024"
function fechaTexto($datetime)
{
    try {
        return Carbon::parse($datetime)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
    } catch (Exception) {
        return 'Formato de fecha inválido';
    }
}

function formatAnio($datetime)
{
    try {
        return Carbon::parse($datetime)->locale('es')->isoFormat('YYYY');
    } catch (Exception) {
        return 'Formato de fecha inválido';
    }
}
