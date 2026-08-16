<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/logo.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- VITE -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {!! RecaptchaV3::initJs() !!}
    <style>
        /* Sembunyikan icon mata & password generator bawaan browser (Edge, Chrome, Safari, Firefox) */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear,
        input[type="password"]::-webkit-contacts-auto-fill-button,
        input[type="password"]::-webkit-credentials-auto-fill-button,
        input[type="password"]::-webkit-strong-password-auto-fill-button {
            display: none !important;
            visibility: hidden !important;
            pointer-events: none !important;
            -webkit-appearance: none !important;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
        }

        .grecaptcha-badge {
            position: fixed !important;
            bottom: 14px !important;
            right: -186px !important;
            z-index: 999999 !important;
            visibility: visible !important;
            opacity: 0.85 !important;
            transition: right 0.35s ease-in-out, opacity 0.35s ease-in-out !important;
            border-radius: 8px 0 0 8px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15) !important;
        }
        .grecaptcha-badge:hover {
            right: 14px !important;
            opacity: 1 !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-[#0A1428]">

    <div class="min-h-screen w-full">
        {{ $slot }}
    </div>

</body>
</html>