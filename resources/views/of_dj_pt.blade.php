<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficio N° {{$review->rev_num_of}}-ARC-PAISI-FI-UDH</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Old+Uyghur&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
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
        <p class="data-oficio"><strong>Oficio Múltiple N° 019-2024-CA-PAISI-FI-UDH</strong></p>
    </div>

    <div class="content">
        <p><strong>SEÑOR (A):</strong></p>
        Ing. Paolo Solís Jara <br>
        Ing. Paolo Solís Jara <br>
        Ing. Paolo Solís Jara </p>
        <p><strong> DEL P. A. DE INGENIERIA DE SISTEMAS E INFORMÁTICA- UNIVERSIDAD DE HUÁNUCO</strong></p>
        <p>ASUNTO:	DESIGNACIÓN DE JURADO</p>
        <p><strong>Presente.</strong></p>
        <p>De mi consideración:</p>
        <p>Por medio del presente me dirijo a usted para saludarlo cordialmente y a la vez para SOLICITAR
             su apoyo en la revisión y aprobación del PROYECTO DE TESIS, presentado con Exp. N° 458720-0000007368, 
             el mismo que hago llegar adjunto al presente, el ejemplar en digital del proyecto de tesis intitulado: 
             “TRANSFORMACION DIGITAL Y GESTION DE SERVICIO AL CLIENTE DE SUPERMIX DE LA CIUDAD DE HUANUCO EN EL 2024” 
             Presentado por el egresado. CASTAÑON ACOSTA, BRYAN ARNULFO. El tiempo que dispone es de 07 días hábiles como plazo máximo, 
             de acuerdo al Reglamento General de Grados y Títulos (Art. 24), para emitir a través de un informe sus observaciones y/o 
             conformidad para su aprobación y ejecución. En caso de no poder cumplir con el encargo sírvase devolver el proyecto al menor 
             tiempo posible con la finalidad de asignar a otro revisor. </p>
        <p>Adjunto: <br>
            Proyecto de Investigación digitalizado en formato Word. <br>
            Copia Resolución Nº 1952-2023-D-FI-UDH (05/09/2023) <br>
        </p>
        <p>Realizada la revisión del proyecto de investigación se sugiere al
            interesado siga con el trámite que establece el Reglamento General de Grados y Títulos de
            la Universidad, por lo que informo a usted para los fines pertinentes.</p>
        <p>Sin otro particular, me despido recordándole las muestras de mi especial consideración y estima personal.</p>
        <p>Atentamente,</p>
    </div>

    <div class="firma">
        <img src="{{ public_path('/img/firma.jpg') }}" alt="Cabecera Programa Académico Ingeniería de Sistemas">
    </div>
    <div class="signature">

        <p><strong>
                Ing. {{ $adviserFormatted['adv_name'] }} {{ $adviserFormatted['adv_lastname_m'] }} {{ $adviserFormatted['adv_lastname_f'] }}
            </strong></p>
    </div>
</body>

</html>