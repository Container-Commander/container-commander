<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
        <title>{{ $title ?? 'Page Title' }}</title>
        <!-- Styles -->
        @vite('resources/css/app.css')
    </head>
    <body style="background: rgb(2,0,36);
    background: linear-gradient(90deg, rgb(229 229 229) 0%, rgb(101 158 108) 43%, rgba(0, 205, 246, 1) 100%)">
        <a href="{{route('home')}}">
            <img src="{{asset('storage/logo.png')}}" alt="Container Commander" width="200" class="pt-5 pl-5">
        </a>
        <div class="mt-4 container m-[auto]">
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <span class="font-medium">Success!</span> {{ session('success') }}
                </div>
            @endif
            @if (session('fail'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Danger!</span> {{ session('fail') }}
                </div>
            @endif
        </div>
        {{ $slot }}
    </body>
</html>