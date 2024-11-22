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
            size: 215.9mm 385.6mm; /*tamaño normal size: 215.9mm 365.6mm; */ 
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
                Visto, el Oficio N° {{$num_of}}-{{$year_of}}-CA-PAISI-FI-UDH, mediante el cual el Coordinador Académico de Ingeniería de Sistemas de Informática, 
                remite el dictamen de los jurados revisores, del Informe Final de Trabajo de investigación (Tesis) intitulado: <strong>“{{$tittle}}”</strong>, 
                presentado por el (la) Bach. <strong>{{$name_student}}</strong>.
            </p>
            <p><strong>CONSIDERANDO:</strong></p>
            <p>Que, según mediante Resolución N° 006-2001-R-AU-UDH, de fecha 24 de julio de 2001, se crea la Facultad de Ingeniería, y;</p>
            
            <p>Que, mediante Resolución de Consejo Directivo N° 076-2019-SUNEDU/CD, de fecha 05 de junio de 2019, otorga la Licencia a la Universidad de 
                Huánuco para ofrecer el servicio educativo superior universitario, y;</p>
            <p>Que, mediante Resolución N° {{$num_res_da}}-{{$year_res_da}}-D-FI-UDH, de fecha {{$date_res_da}}, se aprobó el Trabajo de Investigación (Tesis) 
                y su ejecución, del Bach. <strong>{{$name_student}}</strong></p>

            <p>Que, según Oficio N° {{$num_of}}-{{$year_of}}-CA-PAISI-FI-UDH, del Coordinador Académico quien informa que los 
                JURADOS REVISORES del Informe Final de Trabajo de Investigación (Tesis) intitulado: <strong>“{{$tittle}}”</strong>, presentado
                por el (la) Bach. <strong>{{$name_student}}</strong>, integrado por los siguientes docentes: Mg. {{$name_presidente}} (Presidente), Mg. {{$name_secretario}} (Secretario) e 
                Ing. {{$name_vocal}} (Vocal), quienes declaran APTO para ser ejecutado el proyecto de Tesis, y;</p>
                <p>
            <p>Estando a las atribuciones conferidas al Decano de la Facultad de Ingeniería y con cargo a dar cuenta en el próximo Consejo de Facultad.</p>
            <p><strong>SE RESUELVE:</strong></p>
            <p><strong style="text-decoration: underline;">Artículo Único</strong><strong>.-. APROBAR,</strong> el Informe Final de Trabajo de Investigación (Tesis) intitulado: 
                <strong>“{{$tittle}}”</strong>, presentado por el (la) Bach. <strong>{{$name_student}}</strong> para optar el Título Profesional de Ingeniero(a) de Sistemas e Informáticas, 
                del Programa Académico de Ingeniería de Sistemas e Informática, de la Universidad de Huánuco.</p>
            <p style="text-align: center"><br><strong>REGÍSTRESE, COMUNÍQUESE Y ARCHÍVESE</strong></p><br>
        </div>

        <div class="firma ">
            <img src="{{ public_path('/img/sello.jpg') }}" alt="Firma 1">
            <img src="{{ public_path('/img/sello.jpg') }}" alt="Firma 2">
        </div>
        <div class="pie">
            <p style="text-decoration: underline;">Distribución:</p>
            <p>
            Fac. de Ingeniería – PAISI – Exp. Graduando – Interesado - Archivo.<br>
                BCR/EJML/nto.
            </p>


        </div>
    </div>
</body>
</html>
