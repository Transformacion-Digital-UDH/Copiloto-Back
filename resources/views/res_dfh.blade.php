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
            /* height: 100vh; Centra verticalmente */
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
            Visto, el Expediente N° {{$num_exp}}, presentado por el (la) Bach. <strong>{{$name_student}}</strong>, en la que solicita se 
            fije la fecha y hora para la Sustentación de 
            Tesis, para optar el Título Profesional de Ingeniero de Sistemas e Informática; </strong>.
            </p>
            <p><strong>CONSIDERANDO:</strong></p>
            <p>Que, de acuerdo a la Nueva Ley Universitaria 30220, Capítulo V, Art 45º inc. 45.2, y 
            con opinión favorable del Jurado Evaluador, y; </p>
            
            <p>Que, para la Sustentación de Tesis, es necesario nombrar un Jurado Evaluador, fijar 
                hora, lugar y fecha, para dicho Acto Académico y que estará integrada por tres miembros docentes 
                de la Facultad de Ingeniería, y;  </p>
            
            <p>Que, según Oficio N° {{$num_of}}-{{$year_of}}-CA-PAISI-FI-UDH, presentado por el Coordinador 
                del Programa Académico de Ingeniería de Sistemas e Informática, en el que indica que la fecha y 
                hora de sustentación será el día {{$def_fecha}} a las {{$def_hora}}, para la Sustentación 
                de Tesis del Bach. <strong>{{$name_student}}</strong>,  para optar el Título Profesional de 
                Ingeniero de Sistemas e Informática, y; </p>
                <p>
            <p>Estando a las atribuciones conferidas al Decano de la Facultad de Ingeniería y con 
            cargo a dar cuenta en el próximo Consejo de Facultad. </p>
            <p><strong>SE RESUELVE:</strong></p>
            <p><strong style="text-decoration: underline;">Artículo Primero</strong><strong>.- NOMBRAR,</strong>
             el Jurado Evaluador en la modalidad de 
                Sustentación de Tesis intitulada: <strong>"{{$tittle}}"</strong>, 
                para optar el Título Profesional de Ingeniero de Sistemas e Informática, del 
                Bach. <strong>{{$name_student}}</strong> de, el mismo que está integrado por los siguientes 
                docentes: </p>

                <ul style="margin-left: 20mm; text-indent: 1mm;">
                    <li>{{$name_presidente}} - PRESIDENTE</li>
                    <li>{{$name_secretario}} - SECRETARIO</li>
                    <li>{{$name_vocal}} - VOCAL</li>
                </ul>  
            <p><strong style="text-decoration: underline;">Artículo Segundo</strong><strong>.- DESIGNAR,</strong>
            como docente accesitario al <strong>Ing. {{$name_adviser}}</strong>, quien asumirá funciones ante cualquier contratiempo que se suscitara con los Jurados 
            Titulares. </p>
            <p><strong style="text-decoration: underline;">Artículo Tercero</strong>.- 
                El Acto de Evaluación se realizará el día {{$def_fecha}} a las {{$def_hora}}, 
                en el Auditorio de la Universidad, Aula 403-P2 Ciudad Universitaria de la 
                Esperanza. </p>
        </div>

        <p style="text-align: center"><br><strong>REGÍSTRESE, COMUNÍQUESE Y ARCHÍVESE</strong></p><br>

        <div class="firma ">
            <img src="{{ public_path('/img/sello.jpg') }}" alt="Firma 1">
            <img src="{{ public_path('/img/sello.jpg') }}" alt="Firma 2">
        </div>
        <div class="pie">
            <p style="text-decoration: underline;">Distribución:</p>
            <p>
            C PAISI– Jurados (03) - Mat. y Reg. Acad – Exp. Graduando – VRI – Of. Red de Informática – Interesado – Archivo. <br>
            BLCR/EJML/nto
            </p>


        </div>
    </div>
</body>
</html>
