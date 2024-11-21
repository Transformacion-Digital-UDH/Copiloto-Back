<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficio N° {{$office->of_num_of}}-{{$year}}-CA-PAISI-FI-UDH</title>
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
            margin-left: -80px;
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
        .pie{
            text-align: left;
            font-size: 11px;
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
        <p class="data-oficio"><strong>OFICIO N° {{$office->of_num_of}}-{{$year}}-CA-PAISI-FI-UDH</strong></p>
    </div>

    <div class="content">
        <p><strong>SEÑOR (A): <br>
            MG. BERTHA LUCILA CAMPOS RIOS <br>
            DECANA (E) DE LA FACULTAD DE INGENIERÍA <br>
            UNIVERSIDAD DE HUÁNUCO</strong></p>
            

        <p><strong>Presente.</strong></p>
        <p style="text-indent: 30px;">De mi consideración:</p>
        <p style="text-indent: 30px;">Por medio del presente me dirijo a Usted, para saludarla cordialmente y a la vez remitirle el Expediente
             <strong>N° {{$num_exp}}</strong> del Bachiller: <strong>{{$student}}</strong>, quien solicita <strong>Aprobación del informe Final del Trabajo de Investigación Tesis.</strong> </p>
        
        <p style="text-indent: 30px;">Se pone en conocimiento que, mediante <strong> RESOLUCIÓN N° {{$num_res}}-{{$res_year}}-D-FI-UDH</strong> de fecha {{$res_date}}, se aprobó el trabajo de investigación y su ejecución del Bachiller <strong>{{$student}}</strong>
         Así mismo el informe final del trabajo de investigación (Tesis) titulado: “<strong>{{$tittle}}</strong>” 
         Fue revisado por los siguientes <strong>JURADOS REVISORES:</strong></p>
         <ul style="margin-left: 20mm;">
            <li>{{$presidente}}</li>
            <li>{{$secretario}}</li>
            <li>{{$vocal}}</li>
        </ul>      
        <p style="text-indent: 30px;">Dichos jurados revisores declararon <strong>APTO</strong> para la sustentación. Por lo que se remite a su despacho para su conocimiento y fines pertinentes </p>
        <ul><strong style="text-indent: 30px;">Adjunto:</strong></ul>
        <ul style="margin-left: 20mm; list-style-type: disc;">
            <li>Informes de conformidad de los Jurados Revisores del Trabajo de Investigación de Tesis.</li>
            <li>Trabajo de Investigación de Tesis digitalizado en formato PDF</li>
            <li>Informe de Conformidad del Docente Asesor</li>
        </ul>

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
    
    <div class="pie">
            <p>
                c.c. <br>
                Archivo <br>
                PESJ/dnmr <br>
            </p>
        </div>
</body>
</html>