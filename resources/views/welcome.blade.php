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
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>

    <title>Document</title>

    @livewireStyles
</head>

<body>

    <div class="grid-container">

        <div class="chart" style="position: relative; height:40vh; width:80vw">
            <canvas id="hosts"></canvas>
        </div>

    </div>

    <script>

        let ctxPie = document.getElementById('hosts').getContext('2d');
        let PieChart = new Chart(ctxPie, {
            type: 'horizontalBar',
            data: {
                labels: [1,2],
                datasets: [{
                    label: 'data 1',
                    data: [[-3, 5], [20, 25], [10, 11]],
                    backgroundColor: 'green'
                },
                {
                    label: 'data 2',
                    data: [[6, 8],[0,1]],
                    backgroundColor: 'red'
                },
                {
                    label: 'data 3',
                    data: [[10, 11]],
                    backgroundColor: 'violet'
                }]
            },
            options: {
                responsive: true,
                scales: {
                xAxes: [{
                    stacked: false,
                }],
                yAxes: [{
                    stacked: true,
                }]
                },
                legend: {
                position: 'top',
                },
                title: {
                    display: true,
                    text: 'Horizontal Floating Bars'
                }
            }
        });
    
    </script>
    
    @livewireScripts
</body>
</html>