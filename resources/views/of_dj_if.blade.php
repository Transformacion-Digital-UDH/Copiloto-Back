<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Old+Uyghur&display=swap" rel="stylesheet">
    <title>OFICIO N° {{$office->of_num_of}}-{{$year}}-CA-PAISI-FI-UDH</title>
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
            max-width: 100%; /* Asegúrate de que la imagen no desborde */
            height: auto; /* Mantiene la proporción de la imagen */
            margin-left: -90px;
        }
        .fecha{
            text-align: right;
            
        }
        .parrafo{
            text-indent: 20mm;
            text-align: justify;
            line-height: 5mm;
            
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

        .coordinador{
            margin-top: -90px;
            text-align: center;
        }
        .info{
            font-size: 11px;
        }


    </style>
</head>
<body>
    <div class="cabecera">
            <img src="{{ public_path('/img/portada.jpg') }}" alt="Cabecera Programa Académico Ingeniería de Sistemas">
    </div>

    <div class="fecha" >Huánuco, {{$formattedDate}}.</div>
    <div class="premisa">
        <p style="text-decoration: underline; font-weight: bold; ">OFICIO N° {{$office->of_num_of}}-{{$year}}-CA-PAISI-FI-UDH</p>
        <p style="font-weight: bold;">
            SEÑORA:
            MG. BERTHA LUCILA CAMPOS RIOS <br> 
            DECANA (E) DE LA FACULTAD DE INGENIERÍA <br>
            UNIVERSIDAD DE HUÁNUCO
        </p>
        <p>Presente. –</p>
        <p style="text-indent: 20mm;">De mi consideración:</p>

    </div>
    <div class="parrafo">
        <p>Por medio del presente me dirijo a usted para saludarla cordialmente y a la vez para hacer llegar adjunto al presente lo siguiente:
             <strong>Exp. {{$num_exp}}</strong> del Bachiller: <strong>{{$student}}</strong> quien solicita 
             <strong>DESIGNACION DE JURADOS REVISORES PARA EL INFORME FINAL DEL TRAB. INVESTIGACIÓN (TESIS)</strong>
              el mismo que ha sido aprobado para su ejecución del proyecto de Tesis; por el Consejo de Facultad, intitulada 
             <strong>{{$tittle}}</strong>
               Así mismo hago llegar la propuesta de la comisión de jurados revisores conformados por los siguientes docentes:</p>
    </div>
        <ul style="margin-left: 20mm;">
            <li>{{$presidente}}</li>
            <li>{{$secretario}}</li>
            <li>{{$vocal}}</li>
        </ul>
    Adjunto:
    <ul style="margin-left: 20mm; list-style-type: disc;">
            <li>Informe Digitalizado del Trabajo de Investigación (Tesis)</li>
            <li>Resolución Aprob. Del Proy de Tesis Nª {{$num_res}}-{{$res_year}}-D-FI-UDH ({{$res_date}}).</li>
            <li>Informe de Conformidad del Docente Asesor</li>
        </ul>
    <div class="parrafo">
        <p>Sin otro en particular, me despido recordándole las muestras de mi especial consideración y estima personal.</p>
    </div>
    <p style="text-align: center;">Atentamente,</p>  
    
    <div class="firma">
        <img src="{{ public_path('/img/firma.jpg') }}" alt="Cabecera Programa Académico Ingeniería de Sistemas">
    </div>
    <div class="coordinador">
        <p>
            ____________________________ <br>
            Ing. Paolo E. Solis Jara <br>
            Coordinador Académico
        </p>
    </div>
    <div class="info">
        c.c. <br>
        Archivo <br>
        PESJ/dnmr
    </div>
     

</body>
</html>
