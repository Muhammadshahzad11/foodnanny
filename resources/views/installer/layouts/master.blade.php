<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- REQUIRED META TAGS -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- FONTS -->
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/iconly/iconly.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/calistoga/calistoga.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/public/public.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/fonts/rubik/rubik.css') }}">

    <!-- CUSTOM STYLE -->
    <link rel="icon" type="image" href="{{ asset('images/default/theme/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/custom.css') }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <title>
        @if (trim($__env->yieldContent('template_title')))
            @yield('template_title')
            |
        @endif {{ trans('installer.title') }}
    </title>
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>

<body class="bg-installer bg-no-repeat bg-cover bg-center">
<div id="step-group" class="w-screen h-dvh overflow-y-auto p-3 sm:p-10">
    <div id="steps" class="block max-w-xl mx-auto overflow-hidden rounded-xl shadow-paper p-8 bg-white">
        <h3 class="text-lg font-semibold capitalize text-center mb-7">@yield('title')</h3>
        @yield('container')
    </div>
</div>

<script src="{{ asset('themes/default/js/customScript.js') }}"></script>
</body>

</html>
