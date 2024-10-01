<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESOLUCIÓN Nº 1580-2024-D-FI-UDH</title>
    <!-- Fuente Noto Sans Arabic de Google Fonts -->
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
            margin-bottom: 5px; /* Espaciado entre los elementos */
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
        justify-content: center; /* Centra horizontalmente */
        width: 600px;
        height: 250px; 
        gap: 10px; /* Espacio entre las imágenes (opcional) */
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
                Visto, el Oficio N° {{$office->of_num_of}}-{{$year_res}}-CA-PAISI-FI-UDH presentado por el Coordinador
                del Programa Académico de Ingeniería de Sistemas e Informática y el Expediente N°
                {{$office->of_num_exp}}, del Bach. <strong>{{ $studentFormatted['stu_lastname_m'] }} {{ $studentFormatted['stu_lastname_f'] }}, {{ $studentFormatted['stu_name'] }}</strong>, quién solicita Asesor de
                Tesis, para desarrollar el trabajo de investigación (Tesis).
            </p>
            <p><strong>CONSIDERANDO:</strong></p>
            <p>Que, de acuerdo a la Nueva Ley Universitaria 30220, Capítulo V, Art 45º inc.45.2, es procedente su atención, y;</p>
            <p>
                Que, según el Expediente N° {{$office->of_num_exp}}, presentado por el (la) 
                Bach. <strong>{{ $studentFormatted['stu_lastname_m'] }} {{ $studentFormatted['stu_lastname_f'] }}, {{ $studentFormatted['stu_name'] }}</strong>, 
                quien solicita Asesor de Tesis, para desarrollar su trabajo de investigación, el mismo que propone al Ing. {{ $adviserFormatted['adv_name'] }} {{ $adviserFormatted['adv_lastname_m'] }} {{ $adviserFormatted['adv_lastname_f'] }}, como Asesor de Tesis, y;
            </p>
            <p>Que, según lo dispuesto en el Capítulo II, Art. 27 y 28 del Reglamento General de Grados y Títulos de la Universidad de Huánuco vigente, es procedente atender lo solicitado, y;</p>
            <p>Estando a Las atribuciones conferidas al Decano de la Facultad de Ingeniería y con cargo a dar cuenta en el próximo Consejo de Facultad.</p>
            <p><strong>SE RESUELVE:</strong></p>
            <p><strong style="text-decoration: underline;">Artículo Primero</strong><strong>.-. DESIGNAR,</strong> como Asesor de Tesis del 
            Bach. <strong>{{ $studentFormatted['stu_lastname_m'] }} {{ $studentFormatted['stu_lastname_f'] }}, {{ $studentFormatted['stu_name'] }}</strong>,
            al Ing. {{ $adviserFormatted['adv_name'] }} {{ $adviserFormatted['adv_lastname_m'] }} {{ $adviserFormatted['adv_lastname_f'] }}, Docente del Programa Académico de Ingeniería de Sistemas de Informática, Facultad de Ingeniería.</p>
            <p><strong style="text-decoration: underline;">Artículo Segundo</strong>. - El interesado tendrá un plazo máximo de 6 meses para solicitar revisión del Trabajo de Investigación (Tesis). En todo caso deberá de solicitar nuevamente el trámite con el costo económico vigente.</p>
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
                Fac. de Ingeniería – PAISI – Asesor – Mat. y Reg.Acad. – Interesado – Archivo.<br>
                <strong>BLCR/EJML/nto.</strong>
            </p>


        </div>
    </div>
</body>
</html>
