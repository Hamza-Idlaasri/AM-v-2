<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="{{ asset('image/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chart.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user-info.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notif.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    {{-- <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/> --}}
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <script src="{{ asset('js/chart-2.8.0.js') }}"></script>
    <script src="{{ asset('js/jquery-2.2.4.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/g-charts.js') }}"></script>
    <title>Net Monitor</title>
    @livewireStyles
</head>
<body style="overflow: hidden;">

    <div class="grid-container">
        
        <div class="topbar">
            @livewire('grid.topbar')
        </div>

        <div class="sidebar">
            @livewire('grid.sidebar')
        </div>

        <div class="main mt-3 d-flex justify-content-center align-items-start" style="overflow-x: hidden;">
            @yield('content')
                
            {{-- Popup Notifications --}}
            {{-- @livewire('popup-notifs') --}}

        </div>

    </div>

    @livewireScripts

    </body>
    
</html>