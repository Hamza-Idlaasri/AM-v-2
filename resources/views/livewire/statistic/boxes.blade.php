<script src="{{ asset('js/chartjs-plugin.js') }}"></script>

<div class="container m-2 d-flex justify-content-center align-items-center flex-wrap">

    <div class="bg-white shadow py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px">
        <h6 class="mb-2 text-secondary">Porcentage des Boxes</h6>
        <canvas id="PieChart"></canvas>     
    </div>

    <div class="bg-white shadow py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px">
        <h6 class="mb-2 text-secondary">Total des Boxes</h6>
        <canvas id="BarChart"></canvas>
    </div>

    {{-- <div class="bg-white shadow py-3 px-4 m-3" id="timeline" style="width:66vw;border-radius: 12px;">
        <h6 class="mb-2 text-secondary">Timeline des Boxes</h6>
        <br>
        <div id="floating" style="height: 60vh"></div>
    </div> --}}

</div>

{{-- Boxes Pie chart --}}
<script>

    let ctxBoxPie = document.getElementById('PieChart').getContext('2d');
    let boxPieChart = new Chart(ctxBoxPie, {
        type: 'doughnut',
        data:{
            labels:['Up','Down','Unreachable'],
            datasets:[{
                data: [{{ $boxes_status->boxes_up }},{{ $boxes_status->boxes_down }},{{ $boxes_status->boxes_unreachable }}],
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

                data: [{{$boxes_status->boxes_up}},{{$boxes_status->boxes_down}},{{$boxes_status->boxes_unreachable}}],
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
                
                data.push([element.box_name,'Up','#38c172',new Date(element.Up[i][0]), new Date(element.Up[i][1])]);
                
            }
            for (let i = 0; i < element.Down.length; i++) {
                
                data.push([element.box_name,'Down','#e3342f',new Date(element.Down[i][0]), new Date(element.Down[i][1])]);
                
            }
            for (let i = 0; i < element.Unreachable.length; i++) {
                
                data.push([element.box_name,'Unreachable','rgb(151, 4, 230)',new Date(element.Unreachable[i][0]), new Date(element.Unreachable[i][1])]);
                
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

{{-- Change sidebar menu on choice  --}}
<script>

window.addEventListener('load', function() {
    document.getElementById('statistic').style.display = 'block';
    document.getElementById('statistic-btn').classList.toggle("active-btn");
    document.getElementById('s-boxes').classList.toggle("active-link");
});

</script>