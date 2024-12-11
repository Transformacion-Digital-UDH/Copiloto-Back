<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME', 'Laravel') }}</title>

    @vite('resources/css/app.css')
</head>

<body class="antialiased min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="w-full max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-center mb-8">
                <img src="{{ asset('img/logo.webp') }}" alt="Logo" class="max-h-12 object-contain">
            </div>

            <div class="w-full my-14">
                <a href="/api"
                    class="block p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none">
                    <div class="flex items-center">
                        <div class="ml-4 flex-grow">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Tramite de Titulación</h2>

                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-base leading-relaxed">
                                Desde concebir tu idea de tesis hasta lograr el título profesional, bajo un proceso
                                simplificando y monitoreando cada etapa.
                            </p>
                        </div>

                        <div class="ml-8 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" class="stroke-red-500 w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                            </svg>
                        </div>
                    </div>
                </a>
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
