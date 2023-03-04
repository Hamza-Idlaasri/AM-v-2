<script src="{{ asset('js/chartjs-plugin.js') }}"></script>

<div class="container w-100 bg-white shadow rounded mt-4">

    {{-- Filter --}}
    <div>
        <div class="container bg-white w-75 p-0 my-3 mx-auto d-flex justify-content-between align-items-center">
            {{-- Filter --}}
            @include('inc.filter', ['names' => $hosts_names, 'type' => 'host', 'from' => 'statistic'])
        </div>
    </div>

    <hr>

    {{-- Chart --}}
    <div class="container m-2 d-flex justify-content-center align-items-center flex-wrap">
        
        <div class="bg-white border py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px;border-color:rgb(218, 218, 218)!important">
            <h6 class="mb-2 text-secondary">Porcentage des Hosts</h6>
            <canvas id="PieChart"></canvas>
        </div>

        <div class="bg-white border py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px;border-color:rgb(218, 218, 218)!important">
            <h6 class="mb-2 text-secondary">Total des Hosts</h6>
            <canvas id="BarChart"></canvas>
        </div>

        {{-- <div class="bg-white shadow py-3 px-4 m-3" id="timeline" style="width:66vw;border-radius: 12px;">
            <h6 class="mb-2 text-secondary">Timeline des Hosts</h6>
            <br>
            <div id="floating" style="height: 60vh"></div>
        </div> --}}
        
    </div>
</div>

{{----------------------------------- Piechart ----------------------------------------------------------------------}}
<script>

    let ctxPie = document.getElementById('PieChart').getContext('2d');
    let PieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data:{
            labels:['Up','Down','Unreachable'],
            datasets:[{
                data: @json($hosts_status),
                backgroundColor: [
                        '#38c172',
                        '#e3342f',
                        'rgb(151, 4, 230)'
                    ],

            }]
        },

        options:{
            responsive: true,
            legend:{
                position:'left',
                labels:{
                    boxWidth:15,
                }
            },
            plugins: {
                labels : {
                    fontColor : ['#fff','#fff','#fff'],
                    fontSize : 13,
                }
            },
            cutoutPercentage: 60,

        },

    });

</script>

<!---------------------------------------- BarChart ------------------------------------------------------------------->
<script>

    let ctxBar = document.getElementById('BarChart').getContext('2d');
    let BarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Up','Down','Unreachable'],
            datasets: [{

                data: @json($hosts_status),
                backgroundColor: [
                    '#38c172',
                    '#e3342f',
                    'rgb(151, 4, 230)'

                ],
                // borderWidth: 1,
            }],

        },
        
        options: {
            
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize:2,
                        autoSkip: true,
                        maxTicksLimit: 10
                    },
                    gridLines: {
                        // display:false
                        color:'#f3f3f3',
                    },
                }],

                xAxes:[{
                    barPercentage:0.3,
                    gridLines: {
                        // display:false
                        color:'#f3f3f3',
                    },
                }],
            },
            legend:{
                display:false
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem) {
                            return tooltipItem.yLabel;
                    }
                }
            },
            
            // animation: {
            //     duration: 1,
            //     onComplete: function () {
            //         var chartInstance = this.chart,
            //             ctx = chartInstance.ctx;
            //         ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
            //         ctx.textAlign = 'center';
            //         ctx.textBaseline = 'bottom';

            //         this.data.datasets.forEach(function (dataset, i) {
            //             var meta = chartInstance.controller.getDatasetMeta(i);
            //             meta.data.forEach(function (bar, index) {
            //                 var data = dataset.data[index];
            //                 ctx.fillText(data, bar._model.x, bar._model.y - 5);
            //             });
            //         });
            //     }
            // }
        }
    });
</script>

<!---------------------------------------- floating BarChart ---------------------------------------------------------->
{{-- <script>

    let labels = [];
    let getDatasets = @json($datasets);

    getDatasets.forEach(element => {
    
        // Get Labels name
        labels.push(element.host_name);

    });

    let ds = [];

    for (let i = 0; i < labels.length; i++) {
        
        getDatasets.forEach(element => {
            
            if (labels[i] == element.host_name) {
                
                if (i == 0) {

                    for (let j = 0; j < element.Up.length; j++) {
                        ds.push({
                            label: 'Up',
                            data: [[new Date(element.Up[j][0]), new Date(element.Up[j][1])]],
                            backgroundColor: '#38c172'
                        })
                    }
                    for (let j = 0; j < element.Down.length; j++) {
                        ds.push({
                            label: 'Down',
                            data: [[new Date(element.Down[j][0]), new Date(element.Down[j][1])]],
                            backgroundColor: '#e3342f'
                        })
                    }
                    for (let j = 0; j < element.Unreachable.length; j++) {
                        ds.push({
                            label: 'Unreachable',
                            data: [[new Date(element.Unreachable[j][0]), new Date(element.Unreachable[j][1])]],
                            backgroundColor: 'rgb(151, 4, 230)'
                        })
                    }
                        
                }

                if (i > 0) {

                    let left = new Array(i);
                    
                    for (let j = 0; j < element.Up.length; j++) {
                        ds.push({
                            label: 'Up',
                            data: [left ,[new Date(element.Up[j][0]), new Date(element.Up[j][1])]],
                            backgroundColor: '#38c172'
                        })
                    }
                    for (let j = 0; j < element.Down.length; j++) {
                        ds.push({
                            label: 'Down',
                            data: [left, [new Date(element.Down[j][0]), new Date(element.Down[j][1])]],
                            backgroundColor: '#e3342f'
                        })
                    }
                    for (let j = 0; j < element.Unreachable.length; j++) {
                        ds.push({
                            label: 'Unreachable',
                            data: [left, [new Date(element.Unreachable[j][0]), new Date(element.Unreachable[j][1])]],
                            backgroundColor: 'rgb(151, 4, 230)'
                        })
                    }
                }

            }

        });

    }
    
    
    let ctxFloatBar = document.getElementById('floating').getContext('2d');
    let FloatBarChart = new Chart(ctxFloatBar, {
        type: 'horizontalBar',
        data: {
            labels: labels,
            datasets: ds
        },
        
        options: {
            scales: {
                yAxes: [{
                    stacked: true,
                   
                    gridLines: {
                        // display:false
                        color:'#f3f3f3',
                    },
                    barPercentage: 0.1,
                }],

                xAxes:[{
                    
                    type:'time',
                    time:{
                        unit:'day',
                        min: @json($min),
                        max: @json($max)  
                    },
                    ticks: {
                        stepSize:1,
                        
                    },
                    stacked: false,
                    gridLines: {
                        // display:false
                        color:'#f3f3f3',
                    },
                    
                }],
            },
            legend:{
                display:false
            },
            // tooltips: {
            //     callbacks: {
            //         label: function(tooltipItem) {
            //                 return tooltipItem.xLabel;
            //         }
            //     }
            // },
            
        }
    });
</script> --}}
{{-- 
<script type="text/javascript">
    google.charts.load("current", {packages:["timeline"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
  
      var container = document.getElementById('floating');
      var chart = new google.visualization.Timeline(container);
      var dataTable = new google.visualization.DataTable();
      dataTable.addColumn({ type: 'string', id: 'Position' });
      dataTable.addColumn({ type: 'string', id: 'Name' });
      dataTable.addColumn({ type: 'string', id: 'style', role: 'style' });
      dataTable.addColumn({ type: 'date', id: 'Start' });
      dataTable.addColumn({ type: 'date', id: 'End' });

      let datasets = @json($datasets);

      let data = [];
      
      let elements = 0;

      datasets.forEach(element => {


            for (let i = 0; i < element.Up.length; i++) {
                
                data.push([element.host_name,'Up','#38c172',new Date(element.Up[i][0]), new Date(element.Up[i][1])]);
                
            }
            for (let i = 0; i < element.Down.length; i++) {
                
                data.push([element.host_name,'Down','#e3342f',new Date(element.Down[i][0]), new Date(element.Down[i][1])]);
                
            }
            for (let i = 0; i < element.Unreachable.length; i++) {
                
                data.push([element.host_name,'Unreachable','rgb(151, 4, 230)',new Date(element.Unreachable[i][0]), new Date(element.Unreachable[i][1])]);
                
            }
            
            elements++;
      });

        dataTable.addRows(data);
    
        // let height = elements * 100 + 60;
    
        // document.getElementById('timeline').style.height = height+'px';

        let options = {
                // height: height,
                timeline: { 
                    colorByRowLabel: true,
                },
                alternatingRowStyle: false,
                avoidOverlappingGridLines: false
            }
            
        chart.draw(dataTable,options);
    }
  </script>

  --}}
<script>

window.addEventListener('load', function() {
    document.getElementById('statistic').style.display = 'block';
    document.getElementById('statistic-btn').classList.toggle("active-btn");
    document.getElementById('s-hosts').classList.toggle("active-link");
});

</script>



