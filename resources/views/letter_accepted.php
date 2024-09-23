<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficio N° 012-ARC-PAISI-FI-UDH</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        @page {
            size: A4;
            margin: 2mm;
        }

        body {
            font-family: Arial, sans-serif;
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
            max-width: 100%; /* Asegúrate de que la imagen no desborde */
            height: auto; /* Mantiene la proporción de la imagen */
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
            max-width: 100%; /* Asegúrate de que la imagen no desborde */
            height: auto; /* Mantiene la proporción de la imagen */
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
        <?php
        $path = '..\storage\app\public\recursos\portada.jpg';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        ?>
        
      <img src="<?php echo $base64?>" alt="Cabecera Programa academico ingenieria de sistemas">      
    </div>

    <div class="header">
        <p class="fecha-hoy">
            Huánuco, {{ $formattedDate }}
        </p>
        <p class="data-oficio"><strong> N° 012-ARC-PAISI-FI-UDH</strong></p>
    </div>

    <div class="content">
        <p><strong>SEÑOR:</strong></p>
        <p><strong>Ing. Paolo Solís Jara</strong><br>
            Coordinador del Programa Académico de Ingeniería de Sistemas e Informática<br>
            Facultad de Ingeniería<br>
            Universidad de Huánuco</p>
        <p><strong>Presente.</strong></p>
        <p>De mi consideración:</p>
        <p>Tengo el agrado de dirigirme a usted para saludarlo cordialmente y a la vez comunicarle que he aceptado asesorar el siguiente trabajo de investigación:</p>
        <strong>Título:</strong>
            <div class="titulo-tesis">
                <p>
                    {{$dato->tesis}} <!-- Accede directamente al campo 'tesis' -->
                </p>
            </div>
        <strong>Tesista:</strong>
                <div class="estudiante">
                    <p>
                       {{$dato->estudiante}} <!-- Accede directamente al campo 'estudiante' -->
                    </p>
                </div>
        <p>Sin otro particular, me despido recordándole las muestras de mi especial consideración y estima personal.</p>
        <p>Atentamente,</p>
    </div>

    <div class="firma">
        <?php
        $path = '..\storage\app\public\firmas\firma.jpg';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        ?>
        
      <img src="<?php echo $base64?>" alt="Cabecera Programa academico ingenieria de sistemas">
    </div>
    <div class="signature">

        <p><strong>
            {{$dato->grado}} {{$dato->asesor}} <!-- Accede directamente al campo 'estudiante' -->
        </strong></p>
    </div>
</body>
</html>
