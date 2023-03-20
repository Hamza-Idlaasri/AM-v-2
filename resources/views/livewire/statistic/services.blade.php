<script src="{{ asset('js/chartjs-plugin.js') }}"></script>

<div class="container w-100 bg-white shadow rounded mt-4">

    {{-- Filter --}}
    <div>
        <div class="container bg-white w-75 p-0 my-3 mx-auto d-flex justify-content-between align-items-center">
            {{-- Filter --}}
            @include('inc.filter', ['names' => $services_names,'type' => 'service','from' => 'statistic'])
        </div>
    </div>

    <hr>

    {{-- Charts --}}
    <div class="container m-2 d-flex justify-content-center align-items-center flex-wrap">
        
        <div class="bg-white border py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px;border-color:rgb(218, 218, 218)!important">
            <h6 class="mb-2 text-secondary">Porcentage des Services</h6>
            <canvas  id="PieChart" wire:ignore></canvas>     
        </div>

        <div class="bg-white border py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px;border-color:rgb(218, 218, 218)!important">
            <h6 class="mb-2 text-secondary">Total des Services</h6>
            <canvas id="BarChart" wire:ignore></canvas>
        </div>
        
        {{-- <div class="bg-white shadow py-3 px-4 m-3" id="timeline" style="width:66vw;border-radius: 12px;">
            <h6 class="mb-2 text-secondary">Timeline des Services</h6>
            <br>
            <div id="floating" style="height: 60vh"></div>
        </div> --}}

    </div>
</div>

{{---------------------------------------- Piechart ------------------------------------------------------------------}}
<script>

    let data = @json($services_status);

    let ctxServicesPie = document.getElementById('PieChart').getContext('2d');
    let servicesPieChart = new Chart(ctxServicesPie, {
        type: 'doughnut',
        data:{
            labels:['Ok','Warning','Critical','Unknown'],
            datasets:[{
                data: data,
                backgroundColor: [
                    '#38c172',
                    '#ffed4a',
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
                    fontColor : ['#fff','#212529','#fff','#fff'],
                    fontSize : 13,
                }
            },
            cutoutPercentage: 60,
            
        },   
        
    });

</script>

<!---------------------------------------- BarChart ------------------------------------------------------------------->
<script>

    let ctxBarChart = document.getElementById('BarChart').getContext('2d');
    let servicesBarChart = new Chart(ctxBarChart, {
        type: 'bar',
        data: {
            labels: ['Ok','Warning','Critical','Unknown'],
            datasets: [{

                data: data,
                backgroundColor: [
                    '#38c172',
                    '#ffed4a',
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

<script>
    
    document.addEventListener('livewire:update', function () {
        servicesPieChart.data.datasets[0].data = @this.services_status
        servicesPieChart.update()
        servicesBarChart.data.datasets[0].data = @this.services_status
        servicesBarChart.update()
    })

</script>

<!---------------------------------------- floating BarChart ---------------------------------------------------------->
{{-- <script>

    let labels = [];
    let getDatasets = @json($datasets);

    console.log(getDatasets);

    getDatasets.forEach(element => {
    
        // Get Labels name
        labels.push(element.service_name);

    });

    let ds = [];

    for (let i = 0; i < labels.length; i++) {
        
        getDatasets.forEach(element => {
            
            if (labels[i] == element.service_name) {
                
                if (i == 0) {

                    for (let j = 0; j < element.Ok.length; j++) {
                        ds.push({
                            label: 'Ok',
                            data: [[new Date(element.Ok[j][0]), new Date(element.Ok[j][1])]],
                            backgroundColor: '#38c172'
                        })
                    }
                    for (let j = 0; j < element.Warning.length; j++) {
                        ds.push({
                            label: 'Warning',
                            data: [[new Date(element.Warning[j][0]), new Date(element.Warning[j][1])]],
                            backgroundColor: '#ffed4a'
                        })
                    }
                    for (let j = 0; j < element.Critical.length; j++) {
                        ds.push({
                            label: 'Critical',
                            data: [[new Date(element.Critical[j][0]), new Date(element.Critical[j][1])]],
                            backgroundColor: '#e3342f'
                        })
                    }
                    for (let j = 0; j < element.Unknown.length; j++) {
                        ds.push({
                            label: 'Unknown',
                            data: [[new Date(element.Unknown[j][0]), new Date(element.Unknown[j][1])]],
                            backgroundColor: 'rgb(151, 4, 230)'
                        })
                    }
                        
                }

                if (i > 0) {

                    let left = new Array(i);
                    
                    for (let j = 0; j < element.Ok.length; j++) {
                        ds.push({
                            label: 'Ok',
                            data: [left ,[new Date(element.Ok[j][0]), new Date(element.Ok[j][1])]],
                            backgroundColor: '#38c172'
                        })
                    }
                    for (let j = 0; j < element.Warning.length; j++) {
                        ds.push({
                            label: 'Warning',
                            data: [left, [new Date(element.Warning[j][0]), new Date(element.Warning[j][1])]],
                            backgroundColor: '#ffed4a'
                        })
                    }
                    for (let j = 0; j < element.Critical.length; j++) {
                        ds.push({
                            label: 'Critical',
                            data: [left, [new Date(element.Critical[j][0]), new Date(element.Critical[j][1])]],
                            backgroundColor: '#e3342f'
                        })
                    }
                    for (let j = 0; j < element.Unknown.length; j++) {
                        ds.push({
                            label: 'Unknown',
                            data: [left, [new Date(element.Unknown[j][0]), new Date(element.Unknown[j][1])]],
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
                    barPercentage: 0.3,
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

{{-- <script type="text/javascript">
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
      
      datasets.forEach(element => {

            for (let i = 0; i < element.Ok.length; i++) {
                
                data.push([element.service_name,'Ok','#38c172',new Date(element.Ok[i][0]), new Date(element.Ok[i][1])]);
                
            }
            for (let i = 0; i < element.Warning.length; i++) {
                
                data.push([element.service_name,'Warning','#ffed4a',new Date(element.Warning[i][0]), new Date(element.Warning[i][1])]);
                
            }
            for (let i = 0; i < element.Critical.length; i++) {
                
                data.push([element.service_name,'Critical','#e3342f',new Date(element.Critical[i][0]), new Date(element.Critical[i][1])]);
                
            }
            for (let i = 0; i < element.Unknown.length; i++) {
                
                data.push([element.service_name,'Unknown','rgb(151, 4, 230)',new Date(element.Unknown[i][0]), new Date(element.Unknown[i][1])]);
                
            }
            
      });

    dataTable.addRows(data);
  
    // let height = elements * 100 + 80;
    
    // document.getElementById('timeline').style.height = height;

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
  
</script> --}}


{{-- Change sidebar menu choice --}}
<script>

window.addEventListener('load', function() {
    document.getElementById('statistic').style.display = 'block';
    document.getElementById('statistic-btn').classList.toggle("active-btn");
    document.getElementById('s-services').classList.toggle("active-link");
});

</script>