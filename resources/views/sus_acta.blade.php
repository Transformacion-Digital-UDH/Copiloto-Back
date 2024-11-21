<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACTA DE SUSTENTACION</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Old+Uyghur&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 215.9mm 355.6mm;
            margin: 2mm;
        }

        body {
            font-family: "Noto Serif Old Uyghur", serif;
            margin: 20mm;

        }

        .cabecera {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 800px;
            height: 120px;
        }

        .cabecera img {
            max-width: 100%;
            /* Asegúrate de que la imagen no desborde */
            /* Mantiene la proporción de la imagen */
            margin-left: -80px;
            margin-top: -50px;
        }

        .content {
            text-align: justify;
            line-height: 5mm;
            text-indent: 20mm;
        }


        .tittle {
            text-align: center;
            font-weight: bold;
            font-size: 20px;
        }

        .signature {
            margin-top: -50px;
            text-align: center;
        }
        .spam{
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="cabecera">
        <img src="{{ public_path('/img/portada.jpg') }}" alt="Cabecera Programa Académico Ingeniería de Sistemas">
    </div>

    <div class="tittle">
        <p>ACTA DE SUSTENTACIÓN DE TESIS PARA OPTAR EL TÍTULO PROFESIONAL DE INGENIERO(A) DE SISTEMAS E INFORMÁTICA</p>
    </div>

    <div class="content">
        <p>En la ciudad de Huánuco, siendo las 17:00 horas del dia viernes 08 del mes de marzo del año 2024, se lleva a cabo la sustentación presencial en cumpliento
            de lo señalado en el Reglamento de Grados y Títulos de la Universidad de Huánuco, quienes se reunieron los <strong>Jurados Calificadores</strong> integrado por los Docente:
        </p>
    </div>

    <ul style="margin-left: 20mm;">
        <li>presi</li>
        <li>secre</li>
        <li>vocal</li>
    </ul>

    <div class="content">
        <p>Nombrados mediante la Resolución Nº 0471-2024-D-FI-UDH para evaluar la Tesis intitulada: <strong>“AUDITORIA”</strong> 
        Presentado por el (la) Bach: <strong>EVARISTO</strong>, para optar el Título Profesional de
            Ingeniero(a) de Sistemas e Informática. </p>
        <p>Dicho acto de sustentación se desarrolló en dos etapas: exposición y absolución de preguntas: procediéndose luego a la evaluación por parte de los
            miembros del Jurado.</p>

        <p>Habiendo absuelto las objeciones que le fueron formuladas por los miembros del Jurado de conformidad con las respectivas disposiciones 
            reglamentarias procedieron a deliberar y calificar,
            declarándolo(a) <strong>APROBADO</strong> por <strong>UNANIMIDAD</strong> con el calificativo cuantitativo de <strong>12</strong> 
            y cualitativo de <strong>SUFICIENTE</strong> según el (Art.47).</p>
        <p>Siendo las <strong>18:44</strong> horas del día 08 del mes de marzo del año 2024, los miembros del Jurado Calificador firman la presente Acta en señal de conformidad.</p>
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: center;">
        <tr>
            <td>
                <img src="{{ public_path('/img/firma.jpg') }}" alt="Firma 1" style="width: 200px; height: 200px; object-fit: cover; margin-top: -20px"><br>
                <span class="spam">Nombre<br>Orcid<br>DNI<br>rol</span>
            </td>
            <td>
                <img src="{{ public_path('/img/firma.jpg') }}" alt="Firma 2" style="width: 200px; height: 200px; object-fit: cover; margin-top: -20px"><br>
                <span class="spam">Nombre<br>Orcid<br>DNI<br>rol</span>
            </td>
        </tr>
        <tr>
            <td colspan="2"text-align: center;">
                <img src="{{ public_path('/img/firma.jpg') }}" alt="Firma 3" style="width: 200px; height: 200px; object-fit: cover; margin-top: -20px"><br>
                <span class="spam">Nombre<br>Orcid<br>DNI<br>rol</span>
            </td>
        </tr>
    </table>

</body>

</html>