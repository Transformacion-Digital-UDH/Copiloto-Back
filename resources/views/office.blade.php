<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .asunto {
            text-align: left;
            margin-left: 30px;
        }
        .signature {
            margin-top: 50px;
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
            <p class="data-oficio"><strong> N° {{$solicitude->sol_num}}-ARC-PAISI-FI-UDH</strong></p>
    </div>


    <h3 class="center">INFORME N°001-2023-PESJ-DO-PAISI-FI-UDH</h3>
    
    <p><span class="bold">A:</span> Ing. Paolo E. Solís Jara<br>
    <span class="bold">Coordinador Académico</span></p>

    <p><span class="bold">DE:</span> Ing. Paolo E. Solís Jara<br>
    <span class="bold">Jurado</span></p>

    <p><span class="bold">ASUNTO:</span> Informe de Aprobación del proyecto de tesis <span class="bold">"SISTEMA DE REGISTRO CIVIL Y SU INFLUENCIA EN LA CALIDAD DE ATENCIÓN A LOS USUARIOS DEL DISTRITO DE TAHUANÍA – ATALAYA - UCAYALI 2023"</span></p>

    <p><span class="bold">FECHA:</span> Huánuco, 20 de febrero de 2023</p>

    <p>Es grato dirigirme a usted con la finalidad de saludarle cordialmente y a la vez hacer de su conocimiento la <span class="bold">APROBACIÓN</span> del proyecto de tesis titulada: <span class="bold">SISTEMA DE REGISTRO CIVIL Y SU INFLUENCIA EN LA CALIDAD DE ATENCIÓN A LOS USUARIOS DEL DISTRITO DE TAHUANÍA – ATALAYA - UCAYALI 2023</span> presentado por el Bach. <span class="bold">FRANCISCO FRANCO PALOMINO CRISANTO.</span></p>

    <p>Realizada la revisión del proyecto se sugiere al interesado seguir con los trámites que establece el Reglamento de Grados y Títulos de la UDH; por lo que informo a usted para los fines pertinentes.</p>

    <p class="signature">Atentamente,</p>
</body>
</html>
