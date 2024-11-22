<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESOLUCIÓN Nº {{$resolution->docres_num_res}}-{{$year_res}}-D-FI-UDH</title>
    <!-- Fuente Noto Sans Arabic de Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Old+Uyghur&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 215.9mm 365.6mm; /*tamaño normal size: 215.9mm 365.6mm; */ 
            margin: 2mm;
        }

        body {
            font-family: "Noto Serif Old Uyghur", serif;
            margin: 20mm;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Centra verticalmente */
            color: #012160;
        }

        .container {
            text-align: center; /* Centra horizontalmente */
        }

        .container-tittle {
            font-weight: bold;
            font-size: 28px; /* Tamaño del título */
            margin-bottom: 10px;
        }

        .container-facultad{
            font-weight: bold;
            font-style: italic;
            font-size: 25px; /* Tamaño del título */
            margin-bottom: 10px;
        }

        .container div {
            margin-bottom: 5px;
        }
    
        .res-date {
            text-align: center; /* Centra horizontalmente */
        }

        .res-date div {
            margin-bottom: 15px; /* Espaciado entre los elementos */
            margin-top: 15px; /* Espaciado entre los elementos */
        }

        .parrafo{
            text-indent: 20mm;
            text-align: justify;
            line-height: 5mm;
            margin-left: 7mm;
        }

        .num-res{
            font-weight: bold;
            text-decoration: underline;
        }

        .firma {
            display: flex;
            justify-content: center;
            width: 600px;
            gap: 10px;
            margin-bottom: 0;
            /* height: 250px; Eliminar si no es necesario */
        }

        .firma img {
            max-width: 40%; /* Ajusta el tamaño de las imágenes si es necesario */
            height: auto;
        }

        .pie{
            text-align: left;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="container-tittle">UNIVERSIDAD DE HUÁNUCO</div>
        <div class="container-facultad">Facultad de Ingeniería</div>
    </div>
    <div class="res-date">
        <p class="num-res">RESOLUCIÓN Nº {{$resolution->docres_num_res}}-{{$year_res}}-D-FI-UDH</p>
        <p class="fecha">Huánuco, {{$formattedDate}}</p>
        <div class="parrafo">
            <p>
                Visto, el Expediente con Registro Virtual N° {{$num_exp}}presentado por
                el Coordinador del Programa Académico de Ingeniería de Sistemas e Informática, quien informa
                que el (la) Bach. <strong>{{$name_student}}</strong>,  del Programa Académico de Ingeniería de 
                Sistemas e Informática, quién solicita se le declare Apto para sustentar el Trabajo de Investigación 
                (Tesis) para optar el Título Profesional de Ingeniero de Sistemas e Informática. 
            </p>
            <p><strong>CONSIDERANDO:</strong></p>
            <p>Que, mediante Resolución Nº 466-2016-R-CU-UDH, de fecha 23 de mayo de 2016, 
                y el  Art. 37º del Reglamento de Grados y Títulos de la Universidad de Huánuco, se aprueba que el 
                Bachiller debe ser declarado Apto para Sustentar por Resolución para obtener el título profesional;</p>
            
            <p>Estando a lo expuesto y en uso de las atribuciones conferidas por el Art. 118º del 
                Estatuto de la Universidad de Huánuco;  </p>
            
            <p><strong>SE RESUELVE:</strong></p>
            <p><strong style="text-decoration: underline;">Artículo Único</strong><strong>.- DECLARAR,</strong>
            al Bachiller en Ingeniería de Sistemas e Informática <strong>Sr. {{$name_student}}</strong>
            apto para sustentar el Trabajo de Investigación 
            <strong>(TESIS)</strong> para obtener el Título Profesional de <strong>INGENIERO DE SISTEMAS E INFORMÁTICA.</strong> </p>

            <p style="text-align: center"><br><strong>REGÍSTRESE, COMUNÍQUESE Y ARCHÍVESE</strong></p>
            <br>
        </div>

        <div class="firma ">
            <img src="{{ public_path('/img/sello.jpg') }}" alt="Firma 1">
            <img src="{{ public_path('/img/sello.jpg') }}" alt="Firma 2">
        </div>
        <div class="pie">
            <p style="text-decoration: underline;">Distribución:</p>
            <p>
            Exp. De Título–  Interesado -  Archivo  <br>
            BLCR/EJML/nto  
            </p>


        </div>
    </div>
</body>
</html>
