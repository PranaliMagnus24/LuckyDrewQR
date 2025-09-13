<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('Lucky Draw', 'Lucky Draw'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS (CDN) -->
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css')}}">

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- Header / Navbar --}}
    @include('frontend.partials.header')

    {{-- Main Content --}}
    <main class="flex-grow-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('frontend.partials.footer')

    <!-- Bootstrap Bundle JS -->
  <script src="{{ asset('frontend/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{ asset('frontend/js/jquery-3.7.1.min.js')}}"></script>
  <script src="{{ asset('frontend/js/qrcode.min.js')}}"></script>

    @stack('scripts')
</body>
</html>
