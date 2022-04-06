<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.0"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="{{ asset('js/chartjs-plugin.js') }}"></script>

<div class="container m-2 d-flex justify-content-center align-items-center flex-wrap">
    
    <div class="bg-white shadow py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px">
        <h6 class="mb-2 text-secondary">Porcentage des Equipements</h6>
        <canvas  id="PieChart"></canvas>     
    </div>

    <div class="bg-white shadow py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px">
        <h6 class="mb-2 text-secondary">Total des Equipements</h6>
        <canvas id="BarChart"></canvas>
    </div>
    
    <div class="bg-white shadow py-3 px-4 m-3" id="timeline" style="width:66vw;border-radius: 12px;">
        <h6 class="mb-2 text-secondary">Timeline des Equipements</h6>
        <br>
        <div id="floating" style="height: 60vh"></div>
    </div>

</div>

{{------------------------------------------ Piechart -----------------------------------------------------------}}
<script>

    let ctxPieChart = document.getElementById('PieChart').getContext('2d');
    let equipsPieChart = new Chart(ctxPieChart, {
        type: 'doughnut',
        data:{
            labels:['Ok','Warning','Critical','Unknown'],
            datasets:[{
                data: [{{ $equips_status->equips_ok }},{{ $equips_status->equips_warning }},{{ $equips_status->equips_critical }},{{ $equips_status->equips_unknown }}],
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
    let equipsBarChart = new Chart(ctxBarChart, {
        type: 'bar',
        data: {
            labels: ['Ok','Warning','Critical','Unknown'],
            datasets: [{

                data: [{{$equips_status->equips_ok}},{{$equips_status->equips_warning}},{{$equips_status->equips_critical}},{{$equips_status->equips_unknown}}],
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
      
      datasets.forEach(element => {

            for (let i = 0; i < element.Ok.length; i++) {
                
                data.push([element.equip_name,'Ok','#38c172',new Date(element.Ok[i][0]), new Date(element.Ok[i][1])]);
                
            }
            for (let i = 0; i < element.Warning.length; i++) {
                
                data.push([element.equip_name,'Warning','#ffed4a',new Date(element.Warning[i][0]), new Date(element.Warning[i][1])]);
                
            }
            for (let i = 0; i < element.Critical.length; i++) {
                
                data.push([element.equip_name,'Critical','#e3342f',new Date(element.Critical[i][0]), new Date(element.Critical[i][1])]);
                
            }
            for (let i = 0; i < element.Unknown.length; i++) {
                
                data.push([element.equip_name,'Unknown','rgb(151, 4, 230)',new Date(element.Unknown[i][0]), new Date(element.Unknown[i][1])]);
                
            }
            
      });


        dataTable.addRows(data);
    
        // let height = dataTable.getNumberOfRows() * 90;
    
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

<script>

window.addEventListener('load', function() {
    document.getElementById('statistic').style.display = 'block';
    document.getElementById('statistic-btn').classList.toggle("active-btn");
    document.getElementById('s-equips').classList.toggle("active-link");
});

</script>