<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>{{ config('app.name', 'Fajar Arena') }} — Reservasi & Sewa Lapangan Badminton Makassar Online</title>

        <!-- SEO Primary Meta Tags -->
        <meta name="title" content="Fajar Arena — Reservasi & Sewa Lapangan Badminton Makassar Online">
        <meta name="description" content="Website resmi Fajar Arena Badminton Makassar. Reservasi dan sewa lapangan badminton, padel, dan futsal di Lantai 2 Gedung Graha Pena Makassar secara online, mudah, dan real-time.">
        <meta name="keywords" content="Fajar Arena, Fajar Arena Badminton, Graha Pena Makassar, Sewa Lapangan Badminton Makassar, Booking Lapangan Badminton, Fajar Arena Makassar, fajararena.cloud">
        <meta name="author" content="Fajar Arena">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Open Graph / Facebook / WhatsApp Preview -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="Fajar Arena Badminton">
        <meta property="og:title" content="Fajar Arena — Reservasi & Sewa Lapangan Badminton Makassar Online">
        <meta property="og:description" content="Website resmi Fajar Arena Badminton Makassar. Pesan jadwal lapangan dan paket member online di Graha Pena Makassar secara real-time.">
        <meta property="og:image" content="{{ asset('images/badminton.png') }}">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="Fajar Arena — Reservasi & Sewa Lapangan Badminton Makassar Online">
        <meta name="twitter:description" content="Website resmi Fajar Arena Badminton Makassar. Pesan jadwal lapangan dan paket member online secara real-time.">
        <meta name="twitter:image" content="{{ asset('images/badminton.png') }}">

        <!-- Schema.org JSON-LD Structured Data for Google -->
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "SportsActivityLocation",
          "name": "Fajar Arena Badminton",
          "alternateName": "Fajar Arena Graha Pena Makassar",
          "image": "{{ asset('images/badminton.png') }}",
          "url": "https://fajararena.cloud",
          "address": {
            "@type": "PostalAddress",
            "streetAddress": "Lantai 2 Gedung Graha Pena, Jl. Urip Sumoharjo No. 20",
            "addressLocality": "Makassar",
            "addressRegion": "Sulawesi Selatan",
            "addressCountry": "ID"
          },
          "description": "Fasilitas olahraga bulu tangkis (badminton), padel, dan futsal indoor di Lantai 2 Gedung Graha Pena Makassar dengan sistem booking online real-time."
        }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="font-sans antialiased overflow-y-scroll">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
