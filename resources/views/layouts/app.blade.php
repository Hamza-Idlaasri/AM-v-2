<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chart.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user-info.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notif.css') }}">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <title>Alarm Manager</title>
    @livewireStyles
</head>
<body>

    <div class="grid-container">
        
        <div class="topbar">
            @livewire('grid.topbar')
        </div>

        <div class="sidebar">
            @livewire('grid.sidebar')
        </div>

        <div class="main">
            @yield('content')
        </div>

    </div>

    @livewireScripts

    </body>
    
</html>