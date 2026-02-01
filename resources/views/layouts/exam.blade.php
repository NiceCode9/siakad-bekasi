<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SIAKAD - @yield('title', 'Ujian')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('assets') }}/css/vendor/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/css/main.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        main {
            padding: 20px 0;
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
        }
        /* Custom styles for exam mode */
        .exam-container {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
    @stack('styles')
</head>
<body id="app-container" class="menu-hidden">
    <main>
        <div class="exam-container">
            @yield('content')
        </div>
    </main>

    <script src="{{ asset('assets') }}/js/vendor/jquery-3.3.1.min.js"></script>
    <script src="{{ asset('assets') }}/js/vendor/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('scripts')
</body>
</html>
