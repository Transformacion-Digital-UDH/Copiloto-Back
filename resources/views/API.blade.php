<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME', 'Laravel') }} - Rutas</title>

    @vite('resources/css/app.css')
</head>

<body class="antialiased min-h-screen my-10 flex items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <a class="flex justify-center mb-8" href="/">
                <img src="{{ asset('img/logo.webp') }}" alt="Logo" class="max-h-12 object-contain">
            </a>

            <div class="w-full my-14">


                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase dark:text-gray-400">
                            <tr class="border">
                                <th scope="col" class="px-6 py-4">
                                    N°
                                </th>
                                <th scope="col" class="px-6 py-4 bg-gray-50 dark:bg-gray-800">
                                    Metodo
                                </th>
                                <th scope="col" class="px-6 py-4">
                                    Ruta / Controlador
                                </th>
                                <th scope="col" class="px-6 py-4 bg-gray-50 dark:bg-gray-800">
                                    Alias / Nombre
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($routes as $key => $route)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th  class="pl-6 py-3 font-medium text-gray-500 whitespace-nowrap dark:text-white">
                                        {{ $key + 1 }}
                                    </th>
                                    <th scope="row"
                                        class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800">
                                        {{ $route['method'] }}
                                    </th>
                                    <td class="px-6 py-3">
                                        <span class="font-medium text-gray-600 dark:text-white">{{ $route['uri'] }}</span>
                                        <br>
                                        <i>{{ $route['action'] }}</i>
                                    </td>
                                    <td class="px-6 py-3 bg-gray-50 dark:bg-gray-800">
                                        {{ $route['name'] }}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

            </div>

            <div
                class="flex flex-col sm:flex-row justify-between items-center mt-8 text-sm text-gray-500 dark:text-gray-400">
                <div class="mb-2 sm:mb-0 text-center sm:text-left">
                    &nbsp;
                </div>

                <div class="text-center sm:text-right">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                </div>
            </div>
        </div>
    </div>
</body>

</html>
