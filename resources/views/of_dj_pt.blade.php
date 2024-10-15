<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficio Múltiple N° {{$office->of_num_of}}-{{$year}}-CA-PAISI-FI-UDH</title>
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
            height: auto;
            /* Mantiene la proporción de la imagen */
            margin-left: -90px;
        }

        .firma {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 200px;
            height: 200px;
        }

        .firma img {
            max-width: 100%;
            /* Asegúrate de que la imagen no desborde */
            height: auto;
            /* Mantiene la proporción de la imagen */
            margin-left: 220px;
            margin-top: -10px;
        }

        .data-oficio {
            text-align: left;
            text-decoration: underline;
        }

        .content {
            text-align: justify;
            line-height: 5mm;
        }

        .titulo-tesis {
            text-indent: 40px;
            text-align: justify;
            line-height: 5mm;
            margin-left: 7mm;
        }

        .estudiante {
            text-indent: 40px;
            text-align: justify;
            line-height: 5mm;
            margin-left: 7mm;
        }

        .header {
            text-align: center;
        }

        .signature {
            margin-top: -50px;
            text-align: center;
        }

        .fecha-hoy {
            margin-top: auto;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="cabecera">
        <img src="{{ public_path('/img/portada.jpg') }}" alt="Cabecera Programa Académico Ingeniería de Sistemas">
    </div>

    <div class="header">
        <p class="fecha-hoy">
            Huánuco, {{ $formattedDate }}
        </p>
        <p class="data-oficio"><strong>Oficio Múltiple N° {{$office->of_num_of}}-{{$year}}-CA-PAISI-FI-UDH</strong></p>
    </div>

    <div class="content">
        <p><strong>SEÑOR (A):</strong></p>
        {{ $presidente }}<br>
        {{ $secretario }}<br>
        {{ $vocal }}
        <p><strong>DOCENTE DEL P. A. DE INGENIERIA DE SISTEMAS E INFORMÁTICA- UNIVERSIDAD DE HUÁNUCO</strong></p>
        <p>ASUNTO:	DESIGNACIÓN DE JURADO</p>
        <p><strong>Presente.</strong></p>
        <p style="text-indent: 30px;">De mi consideración:</p>
        <p style="text-indent: 30px;">Por medio del presente me dirijo a usted para saludarlo cordialmente y a la vez para <strong>SOLICITAR</strong>
             su apoyo en la revisión y aprobación del <strong>PROYECTO DE TESIS</strong> , presentado con <strong>Exp. N° {{$num_exp}}</strong>, 
             el mismo que hago llegar adjunto al presente, el ejemplar en digital del proyecto de tesis intitulado: 
             <strong>"{{ $tittle }}"</strong>
             Presentado por el egresado. <strong> {{ $student }} </strong>. El tiempo que dispone es de 07 días hábiles como plazo máximo, 
             de acuerdo al Reglamento General de Grados y Títulos (Art. 24), para emitir a través de un informe sus observaciones y/o 
             conformidad para su aprobación y ejecución. En caso de no poder cumplir con el encargo sírvase devolver el proyecto al menor 
             tiempo posible con la finalidad de asignar a otro revisor. </p>
        <p style="text-indent: 30px;">
            <strong>Adjunto: </strong>
            <span style="display: block; padding-left: 10px;">- Proyecto de Investigación digitalizado en formato Word.</span>
            <span style="display: block; padding-left: 10px;">- Copia Resolución Nº {{$num_res}}-{{$res_year}}-D-FI-UDH ({{$res_date}})</span>
        </p>       
        <p style="text-indent: 30px;">Sin otro particular, me despido recordándole las muestras de mi especial consideración y estima personal.</p>
        <p style="text-align: center;">Atentamente,</p>
    </div>

    <div class="firma">
        <img src="{{ public_path('/img/firma.jpg') }}" alt="Cabecera Programa Académico Ingeniería de Sistemas">
    </div>
    <div class="signature">

        <p><strong>
                Ing. Paolo E. Solís Jara <br>
            </strong>Coordinador Académico</p>
    </div>
</body>
</html>