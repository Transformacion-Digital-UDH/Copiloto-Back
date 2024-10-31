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
                Visto, el Oficio N° {{$num_of}}-{{$year_of}}-CA-PAISI-FI-UDH y el Exp. N° {{$num_exp}}, presentado por el Coordinador del Programa Académico de Ingeniería de Sistemas e Informática,
                quien informa que el (la) Bach. <strong>{{$name_student}}</strong> solicita Revisión del informe
                final del Trabajo de Investigación (Tesis) intitulada: <strong>“{{$tittle}}”</strong>.
            </p>
            <p><strong>CONSIDERANDO:</strong></p>
            <p>Que, de acuerdo al Art. N° 38 y 39 del Reglamento General de Grados y Títulos de
                la Universidad de Huánuco, es necesaria la revisión del Trabajo de Investigación (Tesis) por la
                Comisión de Grados y Títulos del Programa Académico de Ingeniería de Sistemas e Informática,
                Facultad de Ingeniería, de la Universidad de Huánuco; y,</p>
            
            <p>Que, para tal efecto es necesario nombrar al jurado Revisor y/o evaluador,
                compuesta por tres miembros docentes de la Especialidad, y;</p>

            <p>Estando a las atribuciones conferidas al Decano de la Facultad de Ingeniería y con
                cargo a dar cuenta en el próximo Consejo de Facultad.</p>
            
            <p><strong>SE RESUELVE:</strong></p>
            <p><strong style="text-decoration: underline;">Artículo Primero</strong><strong>.-. NOMBRAR,</strong> al Jurado Revisor que evaluará el informe final del
            Trabajo de Investigación (Tesis) intitulada: <strong>“{{$tittle}}”</strong>, presentado por el (la) Bach. <strong>{{$name_student}}</strong> del Programa Académico de
            Ingeniería de Sistemas e Informática, Facultad de Ingeniería, conformado por los siguientes
            docentes:</p>

            <ul style="list-style-position: inside;">
                <li>{{$name_presidente}}</li>
                <li>{{$name_secretario}}</li>
                <li>{{$name_vocal}}</li>
            </ul>

            <p><strong style="text-decoration: underline;">Artículo Segundo</strong>. -Los miembros del Jurado Revisor tienen un plazo de siete (07)
                días hábiles como máximo, para emitir el informe y opinión acerca del Informe Final del Trabajo
                de Investigación (Tesis).</p>
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
            C PAISI –Mat. y Reg. Acad.- Interesado- Jurado (03)-Archivo<br>
            BCR/EJML/nto.
            </p>


        </div>
    </div>
</body>
</html>
